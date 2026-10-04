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
        return $this->json(['subcategories' => Subcategory::query()->with(['category', 'specDefinitions'])->orderBy('sort_order')->get()->map(fn ($s) => [
            'id' => $s->id, 'code' => $s->code, 'name' => $s->name_fa,
            'category_code' => $s->category->code, 'category_name' => $s->category->name_fa,
            'active' => $s->active && $s->category->active,
            'schema_revision' => $this->schemaRevision($s),
            'specifications' => $s->specDefinitions->sortBy('sort_order')->values()->map(fn ($d) => $this->definition($d))->all(),
        ])->all()]);
    }

    private function schemaRevision(Subcategory $subcategory): string
    {
        return hash('sha256', json_encode($subcategory->specDefinitions()->orderBy('id')->get()->map(fn ($d) =>
            $d->only(['id', 'key', 'label_fa', 'label_ar', 'label_en', 'label_ku', 'locked', 'sort_order']))->all(), JSON_THROW_ON_ERROR));
    }

    public function saveSchema(Request $request, Subcategory $subcategory): JsonResponse
    {
        $data = $request->validate([
            'revision' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'], 'acknowledged' => ['required', 'accepted'],
            'definitions' => ['present', 'array', 'max:100'],
            'definitions.*' => ['required', 'array:id,key,labels,locked'],
            'definitions.*.id' => ['present', 'nullable', 'integer'],
            'definitions.*.key' => ['required', 'string', 'max:64', 'regex:/^[a-z][a-z0-9_]*$/', 'distinct'],
            'definitions.*.labels' => ['required', 'array:fa,ar,en,ku'],
            'definitions.*.labels.fa' => ['required', 'string', 'max:255', 'not_regex:/<[^>]*>/u'],
            'definitions.*.labels.ar' => ['nullable', 'string', 'max:255', 'not_regex:/<[^>]*>/u'],
            'definitions.*.labels.en' => ['nullable', 'string', 'max:255', 'not_regex:/<[^>]*>/u'],
            'definitions.*.labels.ku' => ['nullable', 'string', 'max:255', 'not_regex:/<[^>]*>/u'],
            'definitions.*.locked' => ['required', 'boolean'],
        ]);
        DB::transaction(function () use ($request, $data, $subcategory): void {
            $row = Subcategory::query()->lockForUpdate()->findOrFail($subcategory->id);
            abort_unless(hash_equals($this->schemaRevision($row), $data['revision']), 409);
            $existing = $row->specDefinitions()->get()->keyBy('id');
            $submitted = array_values(array_filter(array_column($data['definitions'], 'id'), fn ($id) => $id !== null));
            $owned = $existing->keys()->all(); sort($submitted); sort($owned);
            if ($submitted !== $owned) throw ValidationException::withMessages(['definitions' => 'All existing definitions must be preserved exactly once.']);
            foreach ($data['definitions'] as $index => $definition) {
                if ($definition['id'] !== null && $existing[$definition['id']]->key !== $definition['key']) {
                    throw ValidationException::withMessages(['definitions' => 'Existing specification keys cannot change.']);
                }
                $labels = $definition['labels'];
                $attributes = ['key' => $definition['key'], 'locked' => $definition['locked'], 'sort_order' => $index,
                    'label_fa' => trim($labels['fa']), 'label_ar' => $labels['ar'] ?? null,
                    'label_en' => $labels['en'] ?? null, 'label_ku' => $labels['ku'] ?? null];
                if ($definition['id'] === null) $row->specDefinitions()->create($attributes);
                else $existing[$definition['id']]->fill($attributes)->save();
            }
            ActivityLog::create(['actor_user_id' => $request->user()->id, 'action' => 'admin.product.specification-schema-updated',
                'subject_type' => Subcategory::class, 'subject_id' => $row->id, 'metadata' => ['count' => count($data['definitions'])]]);
        });
        return $this->taxonomy();
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
            $product = Product::create(array_intersect_key($data, array_flip(self::FIELDS)));
            $this->saveSpecifications($product, $data['specifications'] ?? []);
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
            $row->unsetRelation('subcategory');
            $this->saveSpecifications($row, $data['specifications'] ?? []);
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
                    abort_unless($created->hasGeneratedConversion('card') && $created->hasGeneratedConversion('thumb') && $created->hasGeneratedConversion('detail'), 500, 'Image processing incomplete.');
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
            foreach ($submitted as $i => $id) $media[$id]->forceFill(['order_column' => $i + 1])->save();
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
            'specifications' => ['sometimes', 'array', 'max:100'],
            'specifications.*' => ['required', 'array:definition_id,value_text'],
            'specifications.*.definition_id' => ['required', 'integer', 'distinct'],
            'specifications.*.value_text' => ['present', 'nullable', 'string', 'max:1000', 'not_regex:/<[^>]*>/u'],
            'id' => ['prohibited'], 'media' => ['prohibited'], 'specs' => ['prohibited']];
    }

    private function locked(Product $product, string $revision): Product
    {
        $row = Product::query()->lockForUpdate()->findOrFail($product->id);
        abort_unless(hash_equals($this->revision($row), $revision), 409, 'Product changed; reload before saving.');
        return $row;
    }

    public function revision(Product $product): string
    {
        return hash('sha256', json_encode([$product->only(array_merge(['id', 'updated_at'], self::FIELDS)), $product->adminTags(),
            // Media has appended URL attributes; serializing a partial model would require its missing disk fields.
            $product->media()->where('collection_name', Product::MEDIA_COLLECTION)->orderBy('order_column')->orderBy('id')->get(['id', 'order_column', 'updated_at'])->map(fn ($m) => $m->only(['id', 'order_column', 'updated_at']))->all(),
            $product->specValues()->orderBy('id')->get(['id', 'spec_definition_id', 'value_text', 'updated_at'])->toArray(),
            $product->subcategory->specDefinitions()->orderBy('id')->get()->map(fn ($d) => $d->only(['id', 'key', 'label_fa', 'label_ar', 'label_en', 'label_ku', 'locked', 'sort_order']))->all()], JSON_THROW_ON_ERROR));
    }

    private function snapshot(Product $product): array
    {
        return array_merge(['id' => $product->id], $product->only(self::FIELDS), ['tags' => $product->adminTags(), 'revision' => $this->revision($product),
            'specifications' => $this->specifications($product),
            'subcategory_code' => $product->subcategory->code, 'category_code' => $product->subcategory->category->code,
            'media' => $product->getMedia(Product::MEDIA_COLLECTION)->sortBy('order_column')->values()->map(fn ($m) => [
                'id' => $m->id, 'url' => $m->hasGeneratedConversion('card') ? $m->getUrl('card') : $m->getUrl(),
                'thumb_url' => $m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl(),
                'detail_url' => $m->hasGeneratedConversion('detail') ? $m->getUrl('detail') : ($m->hasGeneratedConversion('card') ? $m->getUrl('card') : $m->getUrl()),
                'detail_url' => $m->hasGeneratedConversion('detail') ? $m->getUrl('detail') : ($m->hasGeneratedConversion('card') ? $m->getUrl('card') : $m->getUrl())])->all()]);
    }

    private function definition($definition): array
    {
        return ['id' => $definition->id, 'key' => $definition->key, 'locked' => $definition->locked,
            'labels' => ['fa' => $definition->label_fa, 'ar' => $definition->label_ar,
                'en' => $definition->label_en, 'ku' => $definition->label_ku]];
    }

    private function specifications(Product $product): array
    {
        $values = $product->specValues->keyBy('spec_definition_id');
        return $product->subcategory->specDefinitions->sortBy('sort_order')->values()->map(fn ($d) =>
            array_merge($this->definition($d), ['value_text' => $values->get($d->id)?->value_text]))->all();
    }

    private function saveSpecifications(Product $product, array $specifications): void
    {
        $owned = $product->subcategory->specDefinitions()->pluck('id')->all();
        foreach ($specifications as $specification) {
            if (! in_array((int) $specification['definition_id'], $owned, true)) {
                throw ValidationException::withMessages(['specifications' => 'Specification does not belong to this subcategory.']);
            }
        }
        // Omitted values are preserved; explicit empty values clear only the selected definition.
        foreach ($specifications as $specification) {
            $value = trim($specification['value_text'] ?? '');
            $product->specValues()->updateOrCreate(['spec_definition_id' => $specification['definition_id']],
                ['value_text' => $value === '' ? null : $value]);
        }
    }

    private function audit(Request $request, Product $product, string $action, array $fields): void
    {
        ActivityLog::create(['actor_user_id' => $request->user()->id, 'action' => $action, 'subject_type' => Product::class,
            'subject_id' => $product->id, 'metadata' => ['fields' => array_values(array_intersect(array_merge(self::FIELDS, ['media', 'specifications']), $fields))]]);
    }

    private function json(array $data, int $status = 200): JsonResponse
    {
        return response()->json($data, $status)->header('Cache-Control', 'no-store, private');
    }
}
