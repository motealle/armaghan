<?php
namespace Tests\Feature;
use App\Models\{User,Product,Category,Subcategory};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ProductOrderTest extends TestCase {
 use RefreshDatabase;
 private function fixture(): array {
  \Illuminate\Support\Facades\Cache::flush();
  $c=Category::create(['code'=>'1','name_fa'=>'نوزادی']);$s=Subcategory::create(['category_id'=>$c->id,'code'=>'12','name_fa'=>'پتو']);
  $rows=[];foreach(['available','made_to_order','unavailable','made_to_order'] as $i=>$status)$rows[]=Product::create(['subcategory_id'=>$s->id,'code'=>'1200'.$i,'name_fa'=>'Original '.$i,'availability'=>$status,'active'=>true,'sort_order'=>10+$i]);
  return $rows;
 }
 public function test_order_persists_publicly_without_changing_other_product_fields_or_the_other_group(): void {
  [$a,$b,$c,$d]=$this->fixture();$this->actingAs(User::factory()->admin()->create());
  $state=$this->getJson('/api/admin/product-order')->assertOk()->assertHeader('Cache-Control','no-store, private')->json();
  $this->assertSame([$b->id,$c->id,$d->id],array_column($state['groups']['producible']['products'],'id'));
  $body=['group'=>'producible','revision'=>$state['groups']['producible']['revision'],'ids'=>[$d->id,$b->id,$c->id]];
  $this->putJson('/api/admin/product-order',$body)->assertOk()->assertJsonPath('groups.producible.products.0.id',$d->id);
  $this->putJson('/api/admin/product-order',$body)->assertConflict();
  $this->getJson('/api/admin/product-order')->assertJsonPath('groups.producible.products.2.id',$c->id);
  $this->assertSame(10,$a->fresh()->sort_order);$this->assertSame('Original 3',$d->fresh()->name_fa);$this->assertSame('made_to_order',$d->fresh()->availability->value);
  $public=$this->getJson('/api/catalog/products')->assertOk()->json('data');$byId=collect($public)->keyBy('id');
  $this->assertSame(0,$byId[$d->id]['sort_order']);$this->assertSame(1,$byId[$b->id]['sort_order']);$this->assertSame(2,$byId[$c->id]['sort_order']);
  $this->assertSame([$a->id,$d->id,$b->id,$c->id],array_column($this->getJson('/api/admin/products')->assertOk()->json('products'),'id'));
  $this->assertDatabaseCount('products',4);
 }
 public function test_group_membership_and_revision_prevent_partial_foreign_or_concurrently_stale_reorders(): void {
  [$a,$b,$c,$d]=$this->fixture();$this->actingAs(User::factory()->admin()->create());
  $state=$this->getJson('/api/admin/product-order')->json();$revision=$state['groups']['producible']['revision'];
  foreach([[$b->id,$c->id],[$b->id,$b->id,$d->id],[$a->id,$b->id,$c->id],[$b->id,$c->id,999999]] as $ids)$this->putJson('/api/admin/product-order',['group'=>'producible','revision'=>$revision,'ids'=>$ids])->assertUnprocessable();
  $this->assertSame(11,$b->fresh()->sort_order);
  $d->update(['active'=>false]);
  $this->putJson('/api/admin/product-order',['group'=>'producible','revision'=>$revision,'ids'=>[$b->id,$c->id,$d->id]])->assertConflict();
  $this->getJson('/api/admin/product-order')->assertJsonCount(2,'groups.producible.products');
  $this->assertSame(11,$b->fresh()->sort_order);
 }
 public function test_only_active_admins_can_read_or_save_order(): void {
  $this->fixture();$this->getJson('/api/catalog/product-order')->assertNotFound();$this->getJson('/api/admin/product-order')->assertUnauthorized();$this->putJson('/api/admin/product-order',[])->assertUnauthorized();
  foreach([User::factory()->create(),User::factory()->admin()->inactive()->create()] as $user){$this->actingAs($user);$this->getJson('/api/admin/product-order')->assertForbidden();$this->putJson('/api/admin/product-order',[])->assertForbidden();}
 }
}
