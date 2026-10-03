<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Subcategories\SubcategoryResource;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentTaxonomyResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_admin_can_open_taxonomy_resource_pages(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'active' => true,
            'email' => 'motealle@gmail.com',
        ]);

        $category = Category::create([
            'code' => '1',
            'name_fa' => 'نوزادی',
            'sort_order' => 10,
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'code' => '11',
            'name_fa' => 'لباس نوزادی',
            'sort_order' => 10,
        ]);

        $this->actingAs($admin)->withSession(['armaghan.advanced_admin.user_id'=>$admin->id,'armaghan.advanced_admin.until'=>now()->addMinutes(15)->timestamp]);

        $this->get(CategoryResource::getUrl('index'))->assertOk();
        $this->get(CategoryResource::getUrl('create'))->assertOk();
        $this->get(CategoryResource::getUrl('edit', ['record' => $category]))->assertOk();

        $this->get(SubcategoryResource::getUrl('index'))->assertOk();
        $this->get(SubcategoryResource::getUrl('create'))->assertOk();
        $this->get(SubcategoryResource::getUrl('edit', ['record' => $subcategory]))->assertOk();
    }

    public function test_inactive_or_non_admin_users_cannot_enter_admin_panel(): void
    {
        foreach ([
            ['role' => UserRole::Customer, 'active' => true],
            ['role' => UserRole::Admin, 'active' => false],
        ] as $attributes) {
            $user = User::factory()->create($attributes);
            $this->actingAs($user)
                ->get(CategoryResource::getUrl('index'))
                ->assertRedirect('https://armaghantrading.com/#/admin');
            auth()->logout();
        }
    }

    public function test_taxonomy_resources_do_not_expose_destructive_delete_actions(): void
    {
        $categoryTable = file_get_contents(app_path('Filament/Resources/Categories/Tables/CategoriesTable.php'));
        $subcategoryTable = file_get_contents(app_path('Filament/Resources/Subcategories/Tables/SubcategoriesTable.php'));

        $this->assertStringNotContainsString('DeleteAction', $categoryTable);
        $this->assertStringNotContainsString('DeleteBulkAction', $categoryTable);
        $this->assertStringNotContainsString('DeleteAction', $subcategoryTable);
        $this->assertStringNotContainsString('DeleteBulkAction', $subcategoryTable);
    }
}
