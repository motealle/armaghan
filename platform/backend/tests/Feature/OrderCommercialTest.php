<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\TrackedOrder;
use App\Models\User;
use App\Support\CustomerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class OrderCommercialTest extends TestCase {
 use RefreshDatabase;
 protected function setUp(): void {parent::setUp();Storage::fake('local');}
 private function order(Customer $customer): array {$this->actingAs(User::factory()->admin()->create());return $this->postJson('/api/admin/orders',['customer_id'=>$customer->id,'request_path'=>'simple','description'=>'Clothing order'])->assertCreated()->json('order');}
 private function quote(array $row): array {return ['revision'=>$row['revision'],'currency'=>'USD','items'=>[['product_code'=>null,'description'=>'Custom clothing','quantity'=>3,'unit_price_minor'=>1299]],'shipping_minor'=>100,'tax_minor'=>0,'discount_minor'=>200,'note'=>'Reviewed customer quote'];}
 public function test_exact_totals_draft_privacy_conflicts_and_confirmed_quote_lock(): void {
  $customer=Customer::create(['active'=>true]);$old=$this->order($customer);$row=$this->putJson('/api/admin/orders/'.$old['id'].'/quote',$this->quote($old))->assertOk()->assertJsonPath('order.quote.subtotal_minor',3897)->assertJsonPath('order.quote.total_minor',3797)->json('order');
  $this->withSession([CustomerSession::KEY=>$customer->id])->getJson('/api/customer/orders')->assertJsonPath('orders.0.quote',null);
  $this->putJson('/api/admin/orders/'.$old['id'].'/quote',$this->quote($old))->assertConflict();
  $row=$this->patchJson('/api/admin/orders/'.$row['id'],['revision'=>$row['revision'],'action'=>'invoice_confirmed','note'=>'Invoice reviewed with customer','visible_to_customer'=>true])->assertOk()->json('order');
  $this->withSession([CustomerSession::KEY=>$customer->id])->getJson('/api/customer/orders')->assertJsonPath('orders.0.quote.total_minor',3797);
  $this->putJson('/api/admin/orders/'.$row['id'].'/quote',$this->quote($row))->assertUnprocessable();$this->assertDatabaseCount('order_quotes',1);
 }
 public function test_private_document_storage_visibility_ownership_and_revision(): void {
  $customer=Customer::create(['active'=>true]);$other=Customer::create(['active'=>true]);$old=$this->order($customer);
  $data=['revision'=>$old['revision'],'kind'=>'deposit_receipt','visible_to_customer'=>false,'note'=>'Receipt received for staff review','document'=>UploadedFile::fake()->createWithContent('receipt.pdf',"%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF")];
  $row=$this->post('/api/admin/orders/'.$old['id'].'/documents',$data,['Accept'=>'application/json'])->assertCreated()->json('order');$id=$row['documents'][0]['id'];$media=TrackedOrder::first()->getMedia(TrackedOrder::MEDIA_COLLECTION)->first();$this->assertSame('local',$media->disk);$this->assertStringContainsString('media/orders/'.$id.'/',$media->getPath());
  $this->get('/api/admin/orders/'.$old['id'].'/documents/'.$id)->assertOk()->assertHeader('X-Content-Type-Options','nosniff');
  $this->withSession([CustomerSession::KEY=>$customer->id])->getJson('/api/customer/orders')->assertJsonCount(0,'orders.0.documents');$this->get('/api/customer/orders/'.$old['id'].'/documents/'.$id)->assertNotFound();
  $data['revision']=$row['revision'];$data['kind']='invoice';$data['visible_to_customer']=true;$data['document']=UploadedFile::fake()->image('invoice.jpg',90,60);$row=$this->post('/api/admin/orders/'.$old['id'].'/documents',$data,['Accept'=>'application/json'])->assertCreated()->json('order');$publicId=$row['documents'][1]['id'];
  $this->get('/api/customer/orders/'.$old['id'].'/documents/'.$publicId)->assertOk();$this->withSession([CustomerSession::KEY=>$other->id])->get('/api/customer/orders/'.$old['id'].'/documents/'.$publicId)->assertNotFound();
  $data['document']=UploadedFile::fake()->create('fake.pdf',1,'text/plain');$this->post('/api/admin/orders/'.$old['id'].'/documents',$data,['Accept'=>'application/json'])->assertUnprocessable();$this->assertDatabaseCount('media',2);
 }
}
