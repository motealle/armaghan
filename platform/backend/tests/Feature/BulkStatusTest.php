<?php
namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BulkStatusTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); Cache::flush(); }
    private function items(string $resource): array
    {
        return array_map(fn ($r) => ['id'=>$r['id'],'revision'=>$r['revision']], $this->getJson('/api/admin/'.$resource)->assertOk()->json($resource));
    }
    public function test_bulk_user_status_updates_linked_customers_and_preserves_accounts(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        foreach (range(1,2) as $i) { $u=User::factory()->create();Customer::create(['user_id'=>$u->id,'active'=>true]); }
        $this->postJson('/api/admin/bulk-status/users',['items'=>$this->items('users'),'active'=>false])->assertOk()->assertJsonPath('updated',2);
        $this->assertSame(0,Customer::where('active',true)->count());
        $this->assertSame(0,User::where('role','customer')->where('active',true)->count());
        $this->assertDatabaseCount('users',3);$this->assertDatabaseCount('customers',2);$this->assertSame(2,ActivityLog::count());
    }
    public function test_stale_selection_rolls_back_every_change_and_audit(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Customer::create(['company_name'=>'First','active'=>true]);$second=Customer::create(['company_name'=>'Second','active'=>true]);
        $items=$this->items('customers');$second->update(['notes'=>'Concurrent edit']);
        // Process a valid row before the stale row to prove rollback.
        usort($items,fn($a,$b)=>$a['id']<=>$b['id']);
        $this->postJson('/api/admin/bulk-status/customers',['items'=>$items,'active'=>false])->assertConflict();
        $this->assertSame(2,Customer::where('active',true)->count());$this->assertSame(0,ActivityLog::count());
    }
    public function test_owner_self_and_other_administrators_are_protected_in_mixed_batches(): void
    {
        $owner=User::factory()->admin()->create(['email'=>'motealle@gmail.com','email_verified_at'=>now()]);$buyer=User::factory()->create();
        $this->actingAs($owner);$items=$this->items('users');usort($items,fn($a,$b)=>$b['id']<=>$a['id']);
        $this->postJson('/api/admin/bulk-status/users',['items'=>$items,'active'=>false])->assertForbidden();
        $this->assertTrue($buyer->fresh()->active);$this->assertTrue($owner->fresh()->active);$this->assertSame(0,ActivityLog::count());
        $actor=User::factory()->admin()->create();$this->actingAs($actor);
        $this->postJson('/api/admin/bulk-status/users',['items'=>$items,'active'=>false])->assertForbidden();$this->assertTrue($buyer->fresh()->active);
        $crm=Customer::create(['user_id'=>$owner->id,'active'=>true]);
        $this->postJson('/api/admin/bulk-status/customers',['items'=>[['id'=>$crm->id,'revision'=>str_repeat('a',64)]],'active'=>false])->assertForbidden();
    }
    public function test_product_bulk_status_preserves_fields_and_controls_public_visibility(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $category=Category::create(['code'=>'1','name_fa'=>'نوزادی']);$sub=Subcategory::create(['code'=>'11','category_id'=>$category->id,'name_fa'=>'لباس']);
        foreach(['11001','11002'] as $code)Product::create(['code'=>$code,'subcategory_id'=>$sub->id,'name_fa'=>'محصول','availability'=>'made_to_order','active'=>true,'sort_order'=>0]);
        $this->postJson('/api/admin/bulk-status/products',['items'=>$this->items('products'),'active'=>false])->assertOk()->assertJsonPath('updated',2);
        $this->getJson('/api/catalog/products')->assertOk()->assertJsonCount(0,'data');
        $this->assertDatabaseCount('products',2);$this->assertSame(2,Product::where('availability','made_to_order')->count());
        $this->postJson('/api/admin/bulk-status/products',['items'=>$this->items('products'),'active'=>true])->assertOk();
        $this->getJson('/api/catalog/products')->assertOk()->assertJsonCount(2,'data');
    }
    public function test_authentication_invalid_and_duplicate_selections_fail_without_writes(): void
    {
        $this->postJson('/api/admin/bulk-status/users',[])->assertUnauthorized();
        $this->actingAs(User::factory()->create())->postJson('/api/admin/bulk-status/users',[])->assertForbidden();
        $this->actingAs(User::factory()->admin()->create());$buyer=User::factory()->create();$items=$this->items('users');
        $this->postJson('/api/admin/bulk-status/users',['items'=>[$items[0],$items[0]],'active'=>false])->assertUnprocessable();
        $this->postJson('/api/admin/bulk-status/users',['items'=>[],'active'=>false])->assertUnprocessable();
        $this->postJson('/api/admin/bulk-status/users',['items'=>array_fill(0,101,$items[0]),'active'=>false])->assertUnprocessable();
        $this->assertTrue($buyer->fresh()->active);$this->assertSame(0,ActivityLog::count());
    }
}
