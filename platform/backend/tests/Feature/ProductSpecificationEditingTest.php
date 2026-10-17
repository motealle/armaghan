<?php
namespace Tests\Feature;
use App\Models\{User,Product,Category,Subcategory,SpecDefinition};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ProductSpecificationEditingTest extends TestCase {
 use RefreshDatabase;
 private function product(): array {
  \Illuminate\Support\Facades\Cache::flush();
  $category=Category::create(['code'=>'1','name_fa'=>'نوزادی']);
  $sub=Subcategory::create(['category_id'=>$category->id,'code'=>'11','name_fa'=>'پتو']);
  $product=Product::create(['subcategory_id'=>$sub->id,'code'=>'12011','name_fa'=>'پتو','availability'=>'available','active'=>true,'sort_order'=>0]);
  $spec=SpecDefinition::create(['subcategory_id'=>$sub->id,'key'=>'fabric','label_fa'=>'جنس','label_en'=>'Fabric','locked'=>true]);
  $product->specValues()->create(['spec_definition_id'=>$spec->id,'value_text'=>'پنبه']);
  return [$product,$spec];
 }
 private function revision(Product $product): string {return $this->getJson('/api/admin/products/'.$product->id)->assertOk()->json('product.revision');}
 public function test_language_specific_edit_reload_clear_and_legacy_editor_preserve_other_languages(): void {
  [$product,$spec]=$this->product();$this->actingAs(User::factory()->admin()->create());
  $url='/api/admin/products/'.$product->id.'/specifications';$revision=$this->revision($product);
  $body=['revision'=>$revision,'locale'=>'en','specifications'=>[['definition_id'=>$spec->id,'value_text'=>' Cotton ']]];
  $saved=$this->patchJson($url,$body)->assertOk()->assertJsonPath('product.specifications.0.values.en','Cotton')->assertJsonPath('product.specifications.0.value_text','پنبه')->json('product');
  $this->patchJson($url,$body)->assertConflict();
  $this->getJson('/api/catalog/products')->assertJsonPath('data.0.specifications.0.values.en','Cotton')->assertJsonPath('data.0.specifications.0.values.fa','پنبه');
  foreach (['ar'=>'قطن','ku'=>'پەمۆ'] as $lang=>$text) {
   $saved=$this->patchJson($url,['revision'=>$saved['revision'],'locale'=>$lang,'specifications'=>[['definition_id'=>$spec->id,'value_text'=>$text]]])->assertOk()->assertJsonPath('product.specifications.0.values.'.$lang,$text)->assertJsonPath('product.specifications.0.values.en','Cotton')->json('product');
  }
  $fields=$product->only(['subcategory_id','code','name_fa','availability','active','sort_order']);
  $saved=$this->patchJson('/api/admin/products/'.$product->id,$fields+['revision'=>$saved['revision'],'specifications'=>[['definition_id'=>$spec->id,'value_text'=>'کتان']]])->assertOk()->assertJsonPath('product.specifications.0.values.en','Cotton')->json('product');
  $this->patchJson($url,['revision'=>$saved['revision'],'locale'=>'en','specifications'=>[['definition_id'=>$spec->id,'value_text'=>null]]])->assertOk()->assertJsonPath('product.specifications.0.values.en',null)->assertJsonPath('product.specifications.0.value_text','کتان');
  $this->assertDatabaseCount('products',1);$this->assertDatabaseCount('product_spec_values',1);
  $this->assertSame('12011',$product->fresh()->code);
 }
 public function test_schema_ownership_validation_is_atomic_and_invalid_language_content_fail(): void {
  [$product,$spec]=$this->product();$this->actingAs(User::factory()->admin()->create());
  $url='/api/admin/products/'.$product->id.'/specifications';$revision=$this->revision($product);
  $sub=Subcategory::create(['category_id'=>$product->subcategory->category_id,'code'=>'12','name_fa'=>'دیگر']);
  $foreign=SpecDefinition::create(['subcategory_id'=>$sub->id,'key'=>'size','label_fa'=>'اندازه']);
  foreach ([['locale'=>'de','specifications'=>[]],['locale'=>'en','specifications'=>[['definition_id'=>$spec->id,'value_text'=>'<script>x</script>']]],['locale'=>'en','specifications'=>[['definition_id'=>$spec->id,'value_text'=>'safe'],['definition_id'=>$foreign->id,'value_text'=>'invalid']]]] as $body) {
   $this->patchJson($url,['revision'=>$revision]+$body)->assertUnprocessable();
  }
  $this->assertDatabaseCount('product_spec_translations',0);
  $spec->update(['locked'=>false]);
  $this->patchJson($url,['revision'=>$revision,'locale'=>'ar','specifications'=>[]])->assertConflict();
 }
 public function test_guest_customer_and_inactive_admin_cannot_edit_specifications(): void {
  [$product]=$this->product();$url='/api/admin/products/'.$product->id.'/specifications';
  $this->patchJson($url,[])->assertUnauthorized();
  $this->actingAs(User::factory()->create());$this->patchJson($url,[])->assertForbidden();
  $this->actingAs(User::factory()->admin()->inactive()->create());$this->patchJson($url,[])->assertForbidden();
 }
}
