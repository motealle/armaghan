<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\TrackedOrder;
use App\Models\User;
use App\Support\CustomerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
class OrderTrackingTest extends TestCase {
 use RefreshDatabase;
 protected function setUp(): void {parent::setUp();Cache::flush();}
 private function createOrder(Customer $customer): array {return $this->postJson('/api/admin/orders',['customer_id'=>$customer->id,'request_path'=>'custom','description'=>'Requested clothing production'])->assertCreated()->json('order');}
 private function change(array $order,string $action,array $extra=[]): array {return $this->patchJson('/api/admin/orders/'.$order['id'],array_merge(['revision'=>$order['revision'],'action'=>$action,'note'=>'Reviewed manually','visible_to_customer'=>false],$extra))->assertOk()->json('order');}
 public function test_guest_customer_and_inactive_admin_cannot_manage_orders(): void {
  $this->getJson('/api/admin/orders')->assertUnauthorized();$this->getJson('/api/customer/orders')->assertUnauthorized();
  foreach([User::factory()->create(),User::factory()->admin()->inactive()->create()] as $user){$this->actingAs($user)->getJson('/api/admin/orders')->assertForbidden();$this->postJson('/api/admin/orders',[])->assertForbidden();}
 }
 public function test_manual_full_lifecycle_requires_confirmations_and_preserves_history(): void {
  $this->actingAs(User::factory()->admin()->create());$customer=Customer::create(['active'=>true]);$row=$this->createOrder($customer);
  foreach(['review','invoice','awaiting_deposit'] as $stage)$row=$this->change($row,'stage',['stage'=>$stage]);
  $this->patchJson('/api/admin/orders/'.$row['id'],['revision'=>$row['revision'],'action'=>'stage','stage'=>'production','note'=>'Proceed to production','visible_to_customer'=>true])->assertUnprocessable();
  $row=$this->change($row,'invoice_confirmed');$row=$this->change($row,'deposit_confirmed');
  foreach(['production','quality','ready','shipped','delivered'] as $stage)$row=$this->change($row,'stage',['stage'=>$stage]);
  $this->assertCount(11,$row['events']);$this->assertNotNull($row['deposit_confirmed_at']);
  $this->patchJson('/api/admin/orders/'.$row['id'],['revision'=>$row['revision'],'action'=>'stage','stage'=>'shipped','note'=>'Undo delivery','visible_to_customer'=>false])->assertUnprocessable();
  $this->assertDatabaseCount('tracked_orders',1);$this->assertDatabaseCount('tracked_order_events',11);
 }
 public function test_stale_edit_rejected_without_event_or_order_mutation(): void {
  $this->actingAs(User::factory()->admin()->create());$row=$this->createOrder(Customer::create(['active'=>true]));$new=$this->change($row,'note');
  $this->patchJson('/api/admin/orders/'.$row['id'],['revision'=>$row['revision'],'action'=>'stage','stage'=>'review','note'=>'Old edit','visible_to_customer'=>false])->assertConflict();
  $this->assertDatabaseCount('tracked_order_events',2);$this->assertSame('inquiry',TrackedOrder::first()->stage);
 }
 public function test_customer_sees_only_own_orders_and_public_events_and_cannot_forge_owner(): void {
  $a=Customer::create(['active'=>true]);$b=Customer::create(['active'=>true]);$admin=User::factory()->admin()->create();$this->actingAs($admin);
  $row=$this->createOrder($a);$this->createOrder($b);$row=$this->change($row,'note',['note'=>'Private staff note']);$this->change($row,'note',['note'=>'Public update','visible_to_customer'=>true]);
  $this->withSession([CustomerSession::KEY=>$a->id])->getJson('/api/customer/orders')->assertOk()->assertJsonCount(1,'orders')->assertJsonCount(2,'orders.0.events')->assertJsonMissing(['note'=>'Private staff note'])->assertJsonMissingPath('orders.0.revision')->assertJsonMissingPath('orders.0.customer_name');
  $this->postJson('/api/customer/orders',['customer_id'=>$b->id,'request_path'=>'simple','description'=>'Customer request'])->assertUnprocessable();
  $this->postJson('/api/customer/orders',['request_path'=>'simple','description'=>'Customer request'])->assertCreated();$this->assertSame($a->id,TrackedOrder::latest('id')->first()->customer_id);
 }
 public function test_retried_request_returns_same_order_and_changed_payload_conflicts(): void {
  $customer=Customer::create(['active'=>true]);$this->actingAs(User::factory()->admin()->create());$payload=['customer_id'=>$customer->id,'request_path'=>'custom','description'=>'Stable clothing request','request_key'=>(string)\Illuminate\Support\Str::uuid()];
  $a=$this->postJson('/api/admin/orders',$payload)->assertCreated()->json('order');$b=$this->postJson('/api/admin/orders',$payload)->assertCreated()->json('order');$this->assertSame($a['id'],$b['id']);
  $this->postJson('/api/admin/orders',array_merge($payload,['description'=>'Changed request']))->assertConflict();$this->assertDatabaseCount('tracked_orders',1);$this->assertDatabaseCount('tracked_order_events',1);
  $this->withSession([CustomerSession::KEY=>$customer->id])->postJson('/api/customer/orders',['request_path'=>'custom','description'=>'Customer request','request_key'=>$payload['request_key']])->assertCreated();$this->assertDatabaseCount('tracked_orders',2);
 }
 public function test_primary_owner_related_orders_are_protected_from_other_admins(): void {
  $owner=User::factory()->admin()->create(['email'=>'motealle@gmail.com','email_verified_at'=>now()]);$customer=Customer::create(['user_id'=>$owner->id,'active'=>true]);$this->actingAs($owner);$row=$this->createOrder($customer);
  $this->actingAs(User::factory()->admin()->create())->getJson('/api/admin/orders')->assertJsonCount(0,'orders');
  $this->postJson('/api/admin/orders',['customer_id'=>$customer->id,'request_path'=>'simple','description'=>'Forged request'])->assertForbidden();
  $this->patchJson('/api/admin/orders/'.$row['id'],['revision'=>$row['revision'],'action'=>'note','note'=>'Unauthorized edit','visible_to_customer'=>false])->assertForbidden();
 }
}
