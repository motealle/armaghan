<?php
namespace Tests\Feature;
use App\Models\{User,Product,Category,Subcategory};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class ImageFormatPipelineTest extends TestCase {
 use RefreshDatabase;
 private function prepared(string $source,string $name,string $mime): UploadedFile {
  $path=tempnam(sys_get_temp_dir(),'format_');copy($source,$path);
  return new UploadedFile($path,$name,$mime,null,true);
 }
 public function test_actual_browser_outputs_upload_persist_and_display_in_product_and_home(): void {
  $dir=getenv('ARMAGHAN_FORMAT_FIXTURE_DIR');
  if(!$dir)$this->markTestSkipped('Run through the dedicated image-format acceptance workflow.');
  $manifest=json_decode(file_get_contents($dir.'/manifest.json'),true,512,JSON_THROW_ON_ERROR);
  $this->assertCount(8,$manifest);Storage::fake('public');$this->actingAs(User::factory()->admin()->create());
  $category=Category::create(['code'=>'1','name_fa'=>'نوزادی']);
  $sub=Subcategory::create(['category_id'=>$category->id,'code'=>'11','name_fa'=>'لباس']);
  foreach($manifest as $index=>$sample){
   $source=$dir.'/'.$sample['path'];$this->assertFileExists($source);
   $product=Product::create(['code'=>'110'.str_pad((string)$index,2,'0',STR_PAD_LEFT),'subcategory_id'=>$sub->id,'name_fa'=>'Format QA','availability'=>'available','active'=>true,'sort_order'=>0]);
   $url='/api/admin/products/'.$product->id;
   $revision=$this->getJson($url)->assertOk()->json('product.revision');
   $saved=$this->post($url.'/images',['revision'=>$revision,'image'=>$this->prepared($source,$sample['path'],$sample['type'])],['Accept'=>'application/json'])->assertCreated()->json('product');
   $this->assertCount(1,$saved['media']);$this->assertNotSame($revision,$saved['revision']);
   $this->get(parse_url($saved['media'][0]['url'],PHP_URL_PATH))->assertOk()->assertHeader('Content-Type','image/webp');
   $this->assertNotFalse(getimagesize($product->fresh()->getFirstMedia(Product::MEDIA_COLLECTION)->getPath('card')));
   $this->getJson($url)->assertJsonCount(1,'product.media');
   $state=$this->getJson('/api/admin/home-media')->assertOk()->json();
   $state=$this->post('/api/admin/home-media',['target'=>'hero','revision'=>$state['revision'],'image'=>$this->prepared($source,$sample['path'],$sample['type'])],['Accept'=>'application/json'])->assertCreated()->json();
   $id=$state['images'][array_key_last($state['images'])]['id'];
   $this->postJson('/api/admin/home-media/publish/production',['revision'=>$state['revision'],'selection'=>[$id]])->assertOk();
   $public=$this->getJson('/api/home-media/production')->assertOk()->json('images.hero');
   $this->get(parse_url($public,PHP_URL_PATH))->assertOk()->assertHeader('Content-Type','image/webp');
  }
  $this->assertDatabaseCount('products',8);$this->assertDatabaseCount('media',16);
 }
}
