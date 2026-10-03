<?php
namespace Tests\Feature;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
class CustomOnlyAdminTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void {parent::setUp();Cache::flush();}
    private function owner(): User {return User::factory()->admin()->create(['email'=>'motealle@gmail.com']);}
    public function test_guest_and_normal_admin_never_see_filament_even_with_forged_permit(): void
    {
        $this->get('/admin/login')->assertRedirect('https://armaghantrading.com/#/admin');
        $this->postJson('/api/admin/advanced-access',['reason'=>'diagnosis'])->assertUnauthorized();
        $user=User::factory()->admin()->create(['email'=>'amirmashti1378@gmail.com']);
        $this->actingAs($user)->withSession(['armaghan.advanced_admin.user_id'=>$user->id,'armaghan.advanced_admin.until'=>now()->addHour()->timestamp]);
        $this->get('/admin/products')->assertRedirect('https://armaghantrading.com/#/admin');
        $this->getJson('/admin/products')->assertForbidden();
        $this->postJson('/api/admin/advanced-access',['reason'=>'diagnosis'])->assertForbidden();
        $this->getJson('/api/admin/products')->assertOk();
    }
    public function test_owner_must_explicitly_select_valid_reason_and_permit_is_scoped_expiring_and_revocable(): void
    {
        $owner=$this->owner();$this->actingAs($owner);
        $this->get('/admin')->assertRedirect('https://armaghantrading.com/#/admin');
        $this->postJson('/api/admin/advanced-access',['reason'=>''])->assertUnprocessable();
        $this->postJson('/api/admin/advanced-access',['reason'=>'arbitrary'])->assertUnprocessable();
        $this->postJson('/api/admin/advanced-access',['reason'=>'integration-gap'])->assertOk()->assertJsonPath('expires_in',900);
        $this->get('/admin/products')->assertOk();
        $this->assertDatabaseHas('activity_log',['actor_user_id'=>$owner->id,'action'=>'admin.advanced.opened']);
        $this->travel(16)->minutes();$this->get('/admin/products')->assertRedirect('https://armaghantrading.com/#/admin');
        $this->postJson('/api/admin/advanced-access',['reason'=>'recovery'])->assertOk();
        $this->deleteJson('/api/admin/advanced-access')->assertOk();$this->get('/admin/products')->assertRedirect('https://armaghantrading.com/#/admin');
    }
    public function test_owner_permit_does_not_survive_logout_or_disabled_owner(): void
    {
        $owner=$this->owner();$this->actingAs($owner)->postJson('/api/admin/advanced-access',['reason'=>'diagnosis'])->assertOk();
        $this->postJson('/api/admin/logout')->assertOk()->assertSessionMissing('armaghan.advanced_admin.until');
        $this->actingAs($owner)->get('/admin/products')->assertRedirect('https://armaghantrading.com/#/admin');
        $disabled=User::factory()->admin()->inactive()->create();$this->actingAs($disabled)->postJson('/api/admin/advanced-access',['reason'=>'diagnosis'])->assertForbidden();
    }
    public function test_publication_checksum_rejects_stale_draft_without_publishing_another_editors_copy(): void
    {
        $this->actingAs($this->owner());
        $checksum=$this->getJson('/api/admin/style-profile')->json('draft.checksum');
        $new=$this->putJson('/api/admin/style-profile/draft',['expected_checksum'=>$checksum,'styles'=>[],'texts'=>['hero.title'=>['fa'=>'Actual title']]])->assertOk()->json('draft.checksum');
        $this->postJson('/api/admin/style-profile/publish/production',['expected_checksum'=>$checksum])->assertConflict();
        $this->assertDatabaseCount('style_profile_versions',0);
        $this->postJson('/api/admin/style-profile/publish/production',['expected_checksum'=>$new])->assertCreated();
        $published=$this->getJson('/api/style-profile/production')->assertOk()->json('texts');
        $this->assertSame('Actual title',$published['hero.title']['fa']);
        $this->getJson('/api/style-profile/staging')->assertOk()->assertJsonPath('version',null);
    }
}
