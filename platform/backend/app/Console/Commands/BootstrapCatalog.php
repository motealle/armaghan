<?php

namespace App\Console\Commands;

use App\Enums\ProductAvailability;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BootstrapCatalog extends Command
{
    protected $signature = 'armaghan:bootstrap-catalog
        {--apply : Persist the bootstrap fixture after all guards pass}';

    protected $description = 'Bootstrap the canonical MVP catalog only when all catalog tables are empty';

    public function handle(): int
    {
        $fixture = $this->loadFixture();

        $counts = [
            'categories' => Category::query()->count(),
            'subcategories' => Subcategory::query()->count(),
            'products' => Product::query()->count(),
        ];

        if (array_sum($counts) !== 0) {
            $this->error(sprintf(
                'Catalog bootstrap refused: categories=%d subcategories=%d products=%d.',
                $counts['categories'],
                $counts['subcategories'],
                $counts['products'],
            ));

            return self::FAILURE;
        }

        $expected = [
            'categories' => count($fixture['categories']),
            'subcategories' => array_sum(array_map(
                fn (array $category): int => count($category['subcategories']),
                $fixture['categories'],
            )),
            'products' => count($fixture['products']),
        ];

        if (! $this->option('apply')) {
            $this->info(sprintf(
                'Dry run PASS: fixture contains %d categories, %d subcategories and %d products. Re-run with --apply to persist.',
                $expected['categories'],
                $expected['subcategories'],
                $expected['products'],
            ));

            return self::SUCCESS;
        }

        DB::transaction(function () use ($fixture): void {
            $subcategories = [];

            foreach ($fixture['categories'] as $categoryData) {
                $category = Category::create([
                    'code' => $categoryData['code'],
                    'name_fa' => $categoryData['name_fa'],
                    'active' => true,
                    'sort_order' => $categoryData['sort_order'],
                ]);

                foreach ($categoryData['subcategories'] as $subcategoryData) {
                    $subcategory = Subcategory::create([
                        'category_id' => $category->id,
                        'code' => $subcategoryData['code'],
                        'name_fa' => $subcategoryData['name_fa'],
                        'active' => true,
                        'sort_order' => $subcategoryData['sort_order'],
                    ]);

                    $subcategories[$subcategory->code] = $subcategory;
                }
            }

            foreach ($fixture['products'] as $productData) {
                $subcategory = $subcategories[$productData['subcategory_code']] ?? null;
                if (! $subcategory instanceof Subcategory) {
                    throw new RuntimeException('Bootstrap fixture references an unknown subcategory.');
                }

                Product::create([
                    'subcategory_id' => $subcategory->id,
                    'code' => $productData['code'],
                    'name_fa' => $productData['name_fa'],
                    'availability' => ProductAvailability::from($productData['availability']),
                    'active' => true,
                    'sort_order' => $productData['sort_order'],
                ]);
            }
        }, 3);

        $actual = [
            'categories' => Category::query()->count(),
            'subcategories' => Subcategory::query()->count(),
            'products' => Product::query()->count(),
        ];

        if ($actual !== $expected) {
            throw new RuntimeException('Catalog bootstrap count verification failed.');
        }

        $this->info(sprintf(
            'Catalog bootstrap PASS: %d categories, %d subcategories and %d products.',
            $actual['categories'],
            $actual['subcategories'],
            $actual['products'],
        ));

        return self::SUCCESS;
    }

    /**
     * @return array{schema:int,categories:array<int,array<string,mixed>>,products:array<int,array<string,mixed>>}
     */
    private function loadFixture(): array
    {
        $path = database_path('fixtures/catalog-bootstrap.json');

        if (! is_file($path)) {
            throw new RuntimeException('Catalog bootstrap fixture is missing.');
        }

        $decoded = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        if (($decoded['schema'] ?? null) !== 1
            || ! is_array($decoded['categories'] ?? null)
            || ! is_array($decoded['products'] ?? null)) {
            throw new RuntimeException('Catalog bootstrap fixture schema is invalid.');
        }

        if (count($decoded['categories']) !== 3 || count($decoded['products']) !== 18) {
            throw new RuntimeException('Catalog bootstrap fixture count contract is invalid.');
        }

        $categoryCodes = [];
        $subcategoryCodes = [];
        foreach ($decoded['categories'] as $category) {
            if (! isset($category['code'], $category['name_fa'], $category['sort_order'])
                || ! is_array($category['subcategories'] ?? null)) {
                throw new RuntimeException('Catalog bootstrap category entry is invalid.');
            }

            $categoryCodes[] = (string) $category['code'];
            foreach ($category['subcategories'] as $subcategory) {
                if (! isset($subcategory['code'], $subcategory['name_fa'], $subcategory['sort_order'])) {
                    throw new RuntimeException('Catalog bootstrap subcategory entry is invalid.');
                }
                $subcategoryCodes[] = (string) $subcategory['code'];
            }
        }

        if (count($subcategoryCodes) !== 6
            || count($categoryCodes) !== count(array_unique($categoryCodes))
            || count($subcategoryCodes) !== count(array_unique($subcategoryCodes))) {
            throw new RuntimeException('Catalog bootstrap taxonomy codes are invalid.');
        }

        $productCodes = [];
        foreach ($decoded['products'] as $product) {
            if (! isset($product['code'], $product['subcategory_code'], $product['name_fa'], $product['availability'], $product['sort_order'])
                || ! in_array((string) $product['subcategory_code'], $subcategoryCodes, true)) {
                throw new RuntimeException('Catalog bootstrap product entry is invalid.');
            }

            ProductAvailability::from((string) $product['availability']);
            $productCodes[] = (string) $product['code'];
        }

        if (count($productCodes) !== count(array_unique($productCodes))) {
            throw new RuntimeException('Catalog bootstrap product codes are duplicated.');
        }

        return $decoded;
    }
}
