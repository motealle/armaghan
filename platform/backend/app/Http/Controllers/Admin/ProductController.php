<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductAvailability;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private const FIELDS = ['subcategory_id', 'code', 'name_fa', 'name_ar', 'name_en', 'name_ku', 'availability', 'active', 'sort_order'];

    public function taxonomy(): JsonResponse
    {
        return $this->json(['subcategories' => Subcategory::query()->with('category')->orderBy('sort_order')->get()->map(fn ($s) => [
            'id' => $s->id, 'code' => $s->code, 'name' => $s->name_fa,
            'category_code' => $s->category->code, 'category_name' => $s->category->name_fa,
            'active' => $s->active && $s->category->active,
        ])->all()]);
    }

    public function index(Request $request): JsonResponse
    {
        $input = $request->validate(['page' => ['sometimes', 'integer', 'min:1'], 'search' => ['nullable', 'string', 'max:100'],
            'subcategory_id' => ['nullable', 'integer', 'exists:subcategories,id'], 'per_page' => ['sometimes', 'integer', Rule::in([25, 50, 100])]]);
        $query = Product::query()->with(['subcategory.category', 'media']);
        if ($search = trim($input['search'] ?? '')) {
            $query->where(function ($q) use ($search): void {
                foreach (['code', 'name_fa', 'name_ar', 'name_en', 'name_ku'] as $field) {
                    $q->orWhere($field, 'like', '%'.$search.'%');
                }
            });
        }
        if (! empty($input['subcategory_id'])) $query->where('subcategory_id', $input['subcategory_id']);
        $page = $query->orderBy('sort_order')->orderBy('id')->paginate($input['per_page'] ?? 25);

        return $this->json(['products' => $page->getCollection()->map(fn ($p) => $this->snapshot($p))->all(),
            'page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $product = DB::transaction(function () use ($data, $request) {
            $product = Product::create($data);
            $this->audit($request, $product, 'admin.product.created', array_keys($data));
            return $product;
        });
        return $this->json(['product' => $this->snapshot($product->refresh()->load(['subcategory.category', 'media']))], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate($this->rules($product));
        $row = DB::transaction(function () use ($request, $data, $product) {
            $row = $this->locked($product, $data['revision']);
            // Existing specification values belong to their taxonomy; moving them would silently mislabel them.
            if ($row->subcategory_id !== (int) $data['subcategory_id'] && $row->specValues()->exists()) {
                throw ValidationException::withMessages(['subcategory_id' => 'Product specifications must be migrated before changing subcategory.']);
            }
            $row->fill(array_intersect_key($data, array_flip(self::FIELDS)))->save();
            $this->audit($request, $row, 'admin.product.updated', array_keys($data));
            return $row;
        });
        return $this->json(['product' => $this->snapshot($row->refresh()->load(['subcategory.category', 'media']))]);
    }

    public function upload(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate(['revision' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'],
            'image' => ['required', 'file', 'image', 'mimetypes:image/jpeg,image/png,image/webp', 'max:8192', 'dimensions:max_width=5000,max_height=5000']]);
        $file = $request->file('image');
        // Decode before storage, and derive a safe extension from MIME, never from an uploaded name.
        $size = @getimagesize($file->getRealPath());
        $mime = $file->getMimeType();
        $extension = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
        $decoded = $extension ? @imagecreatefromstring(file_get_contents($file->getRealPath())) : false;
        if (! $decoded || ! is_array($size)) throw ValidationException::withMessages(['image' => 'Invalid image content.']);
        imagedestroy($decoded);
        $created = null;
        try {
            $row = DB::transaction(function () use ($product, $data, $file, $extension, $request, &$created) {
                $row = $this->locked($product, $data['revision']);
                if ($row->getMedia(Product::MEDIA_COLLECTION)->count() >= 6) {
                    throw ValidationException::withMessages(['image' => 'Maximum six images per product.']);
                }
                $existing = $row->media()->pluck('id')->all();
                try {
                    $created = $row->addMedia($file)->usingFileName(Str::uuid().'.'.$extension)->toMediaCollection(Product::MEDIA_COLLECTION);
                    $created->refresh();
                    abort_unless($created->hasGeneratedConversion('card') && $created->hasGeneratedConversion('thumb'), 500, 'Image processing incomplete.');
                    $this->audit($request, $row, 'admin.product.image-added', ['media']);
                } catch (\Throwable $e) {
                    // An event/conversion may throw before addMedia returns. Clean while its rows still exist.
                    $row->media()->where('collection_name', Product::MEDIA_COLLECTION)->whereNotIn('id', $existing)->get()->each(fn ($m) => $m->delete());
                    $created = null;
                    throw $e;
                }
                return $row;
            });
        } catch (\Throwable $e) {
            // Database rollback cannot remove files. Only clean the media created by this failed request.
            if ($created) $created->delete();
            throw $e;
        }
        return $this->json(['product' => $this->snapshot($row->refresh()->load(['subcategory.category', 'media']))], 201);
    }

    public function order(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate(['revision' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'],
            'media_ids' => ['required', 'array', 'min:1', 'max:6'], 'media_ids.*' => ['required', 'integer', 'distinct']]);
        $row = DB::transaction(function () use ($request, $product, $data) {
            $row = $this->locked($product, $data['revision']);
            $media = $row->getMedia(Product::MEDIA_COLLECTION)->keyBy('id');
            $submitted = array_map('intval', $data['media_ids']);
            $owned = $media->keys()->map(fn ($id) => (int) $id)->all();
            $sorted = $submitted; sort($sorted); sort($owned);
            if ($sorted !== $owned) throw ValidationException::withMessages(['media_ids' => 'Order must contain exactly this product gallery.']);
            foreach ($submitted as $i => $id) $media[$id]->forceFill(['sort_order' => $i + 1])->save();
            $this->audit($request, $row, 'admin.product.images-ordered', ['media']);
            return $row;
        });
        return $this->json(['product' => $this->snapshot($row->refresh()->load(['subcategory.category', 'media']))]);
    }

    private function rules(?Product $product = null): array
    {
        return ['subcategory_id' => ['required', 'integer', 'exists:subcategories,id'],
            'code' => ['required', 'string', 'max:64', Rule::unique('products', 'code')->ignore($product?->id)],
            'name_fa' => ['required', 'string', 'max:255'], 'name_ar' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'], 'name_ku' => ['nullable', 'string', 'max:255'],
            'availability' => ['required', Rule::enum(ProductAvailability::class)], 'active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'revision' => $product ? ['required', 'string', 'regex:/^[a-f0-9]{64}$/'] : ['prohibited'],
            'id' => ['prohibited'], 'media' => ['prohibited'], 'specs' => ['prohibited']];
    }

    private function locked(Product $product, string $revision): Product
    {
        $row = Product::query()->lockForUpdate()->findOrFail($product->id);
        abort_unless(hash_equals($this->revision($row), $revision), 409, 'Product changed; reload before saving.');
        return $row;
    }

    private function revision(Product $product): string
    {
        return hash('sha256', json_encode([$product->only(array_merge(['id', 'updated_at'], self::FIELDS)),
            // Media has appended URL attributes; serializing a partial model would require its missing disk fields.
            $product->media()->where('collection_name', Product::MEDIA_COLLECTION)->orderBy('sort_order')->orderBy('id')->get(['id', 'sort_order', 'updated_at'])->map(fn ($m) => $m->only(['id', 'sort_order', 'updated_at']))->all(),
            $product->specValues()->orderBy('id')->get(['id', 'spec_definition_id', 'value_text', 'updated_at'])->toArray()], JSON_THROW_ON_ERROR));
    }

    private function snapshot(Product $product): array
    {
        return array_merge(['id' => $product->id], $product->only(self::FIELDS), ['revision' => $this->revision($product),
            'subcategory_code' => $product->subcategory->code, 'category_code' => $product->subcategory->category->code,
            'media' => $product->getMedia(Product::MEDIA_COLLECTION)->sortBy('sort_order')->values()->map(fn ($m) => [
                'id' => $m->id, 'url' => $m->hasGeneratedConversion('card') ? $m->getUrl('card') : $m->getUrl(),
                'thumb_url' => $m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl()])->all()]);
    }

    private function audit(Request $request, Product $product, string $action, array $fields): void
    {
        ActivityLog::create(['actor_user_id' => $request->user()->id, 'action' => $action, 'subject_type' => Product::class,
            'subject_id' => $product->id, 'metadata' => ['fields' => array_values(array_intersect(array_merge(self::FIELDS, ['media']), $fields))]]);
    }

    private function json(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status)->header('Cache-Control', 'no-store, private');
    }
}
