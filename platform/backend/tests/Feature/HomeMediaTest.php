<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\StyleProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class HomeMediaTest extends TestCase {
 use RefreshDatabase;
 protected function setUp(): void {parent::setUp();Storage::fake('public');}
 private function upload(string $target,array $state): array {return $this->post('/api/admin/home-media',['target'=>$target,'revision'=>$state['revision'],'image'=>UploadedFile::fake()->image('photo.jpg',80,60)],['Accept'=>'application/json'])->assertCreated()->json();}
 public function test_guest_and_non_admin_cannot_upload_or_publish(): void {
  $this->getJson('/api/admin/home-media')->assertUnauthorized();$this->actingAs(User::factory()->create())->postJson('/api/admin/home-media/publish/production',['selection'=>[]])->assertForbidden();
  $this->getJson('/api/home-media/production')->assertOk()->assertJsonMissingPath('images.hero');
 }
 public function test_uploaded_home_media_is_not_public_until_explicit_channel_publication(): void {
  $this->actingAs(User::factory()->admin()->create());$state=$this->getJson('/api/admin/home-media')->assertOk()->json();$state=$this->upload('hero',$state);$id=$state['images'][0]['id'];
  $this->get(parse_url($state['images'][0]['url'],PHP_URL_PATH))->assertOk()->assertHeader('X-Content-Type-Options','nosniff');
  $this->get('/api/home-media/file/'.$id)->assertNotFound();
  $this->getJson('/api/home-media/production')->assertJsonMissingPath('images.hero');
  $state=$this->postJson('/api/admin/home-media/publish/staging',['revision'=>$state['revision'],'selection'=>[$id]])->assertOk()->json();
  $public=$this->getJson('/api/home-media/staging')->assertJsonStructure(['images'=>['hero']])->json('images.hero');
  $this->get(parse_url($public,PHP_URL_PATH))->assertOk()->assertHeader('X-Content-Type-Options','nosn');
  $this->getJson('/api/home-media/production')->assertJsonMissingPath('images.hero');
  $state=$this->postJson('/api/admin/home-media/publish/production',['revision'=>$state['revision'],'selection'=>[$id]])->assertOk()->json();
  $this->getJson('/api/home-media/production')->assertJsonStructure(['images'=>['hero']]);$media=StyleProfile::first()->getMedia(StyleProfile::MEDIA_COLLECTION)->first();$this->assertStringContainsString('media/home/'.$id.'/',$media->getPath());$this->assertNotFalse(getimagesize($media->getPath()));
  $this->postJson('/api/admin/home-media/publish/production',['revision'=>$state['revision'],'selection'=>[]])->assertOk();$this->getJson('/api/home-media/production')->assertJsonMissingPath('images.hero');$this->get('/api/home-media/file/'.$id)->assertNotFound();$this->assertDatabaseCount('media',1);
 }
 public function test_stale_foreign_duplicate_target_and_invalid_upload_are_rejected_atomically(): void {
  $this->actingAs(User::factory()->admin()->create());$old=$this->getJson('/api/admin/home-media')->json();$state=$this->upload('hero',$old);$state=$this->upload('hero',$state);
  $this->postJson('/api/admin/home-media/publish/production',['revision'=>$old['revision'],'selection'=>[]])->assertConflict();
  $this->postJson('/api/admin/home-media/publish/production',['revision'=>$state['revision'],'selection'=>[99999]])->assertUnprocessable();
  $this->postJson('/api/admin/home-media/publish/production',['revision'=>$state['revision'],'selection'=>array_column($state['images'],'id')])->assertUnprocessable();
  $this->post('/api/admin/home-media',['target'=>'../bad','revision'=>$state['revision'],'image'=>UploadedFile::fake()->image('bad.jpg')],['Accept'=>'application/json'])->assertUnprocessable();
  $this->post('/api/admin/home-media',['target'=>'about','revision'=>$state['revision'],'image'=>UploadedFile::fake()->create('bad.jpg',1,'text/plain')],['Accept'=>'application/json'])->assertUnprocessable();
  $this->assertDatabaseCount('media',2);$this->getJson('/api/home-media/production')->assertJsonMissingPath('images.hero');
 }
}
