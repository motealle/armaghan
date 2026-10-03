<?php

namespace App\Services;

use App\Exceptions\FavoriteShareUnavailable;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\FavoriteShare;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class FavoriteShareService
{
    /**
     * @param  array<int, string>  $productCodes
     * @return array{share: FavoriteShare, url: string, owned: bool}
     */
    public function issue(array $productCodes, ?Customer $customer): array
    {
        $codes = array_values(array_unique(array_map(
            static fn (mixed $code): string => trim((string) $code),
            $productCodes,
        )));

        $maxProducts = (int) config('armaghan.favorite_share.max_products', 30);
        if ($codes === [] || count($codes) > $maxProducts) {
            throw ValidationException::withMessages([
                'product_codes' => 'Favorite share product selection is invalid.',
            ]);
        }

        /** @var Collection<int, Product> $products */
        $products = Product::query()
            ->whereIn('code', $codes)
            ->where('active', true)
            ->whereHas('subcategory', fn ($query) => $query
                ->where('active', true)
                ->whereHas('category', fn ($category) => $category->where('active', true)))
            ->get()
            ->keyBy('code');

        if ($products->count() !== count($codes)) {
            throw ValidationException::withMessages([
                'product_codes' => 'One or more selected products are unavailable.',
            ]);
        }

        $ttlDays = $customer instanceof Customer
            ? (int) config('armaghan.favorite_share.customer_ttl_days', 30)
            : (int) config('armaghan.favorite_share.guest_ttl_days', 7);
        $ttlDays = max(1, min(90, $ttlDays));

        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);

        return DB::transaction(function () use ($codes, $products, $customer, $ttlDays, $token, $tokenHash): array {
            $share = FavoriteShare::query()->create([
                'customer_id' => $customer?->getKey(),
                'token_hash' => $tokenHash,
                'expires_at' => now()->addDays($ttlDays),
            ]);

            $pivot = [];
            foreach ($codes as $index => $code) {
                $pivot[$products->get($code)->getKey()] = ['sort_order' => $index];
            }
            $share->products()->attach($pivot);

            ActivityLog::query()->create([
                'customer_id' => $customer?->getKey(),
                'action' => 'favorite_share.issued',
                'subject_type' => FavoriteShare::class,
                'subject_id' => $share->getKey(),
                'metadata' => [
                    'product_count' => count($codes),
                    'owner' => $customer instanceof Customer ? 'customer' : 'guest',
                    'expires_in_days' => $ttlDays,
                ],
            ]);

            return [
                'share' => $share,
                'url' => $this->publicUrl($token),
                'owned' => $customer instanceof Customer,
            ];
        });
    }

    public function resolve(string $token): FavoriteShare
    {
        if (strlen($token) !== 64 || ! ctype_alnum($token)) {
            throw new FavoriteShareUnavailable('Favorite share is unavailable.');
        }

        $share = FavoriteShare::query()
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $share instanceof FavoriteShare) {
            throw new FavoriteShareUnavailable('Favorite share is unavailable.');
        }

        $share->load(['products' => fn ($query) => $query
            ->where('products.active', true)
            ->whereHas('subcategory', fn ($subcategory) => $subcategory
                ->where('active', true)
                ->whereHas('category', fn ($category) => $category->where('active', true)))
            ->orderBy('favorite_share_product.sort_order')]);

        if ($share->products->isEmpty()) {
            throw new FavoriteShareUnavailable('Favorite share is unavailable.');
        }

        return $share;
    }

    public function revoke(FavoriteShare $share, Customer $customer): void
    {
        if ((int) $share->customer_id !== (int) $customer->getKey()) {
            throw new FavoriteShareUnavailable('Favorite share is unavailable.');
        }

        DB::transaction(function () use ($share, $customer): void {
            /** @var FavoriteShare|null $locked */
            $locked = FavoriteShare::query()->whereKey($share->getKey())->lockForUpdate()->first();

            if (! $locked instanceof FavoriteShare || (int) $locked->customer_id !== (int) $customer->getKey()) {
                throw new FavoriteShareUnavailable('Favorite share is unavailable.');
            }

            if ($locked->revoked_at === null) {
                $locked->forceFill(['revoked_at' => now()])->save();
            }

            ActivityLog::query()->create([
                'customer_id' => $customer->getKey(),
                'action' => 'favorite_share.revoked',
                'subject_type' => FavoriteShare::class,
                'subject_id' => $locked->getKey(),
            ]);
        });
    }

    private function publicUrl(string $token): string
    {
        $origin = request()->getSchemeAndHttpHost();
        $fragmentPath = (string) config('armaghan.favorite_share.fragment_path', '/#/favorites/share/');

        // Historical host configuration must not send live shares into frozen token-unaware UI.
        if (preg_match('~^/t/(?:0?[1-9]|1[0-9]|2[0-8])/#/favorites/share/$~', $fragmentPath)) $fragmentPath = '/#/favorites/share/';

        return rtrim($origin, '/').'/'.ltrim($fragmentPath, '/').$token;
    }
}
