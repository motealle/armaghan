<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\StyleProfile;
use App\Models\StyleProfilePublication;
use App\Models\StyleProfileVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StyleProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_style_profile_endpoint_is_safe_when_nothing_is_published(): void
    {
        $this->getJson('/api/style-profile/staging')
            ->assertOk()
            ->assertJsonPath('schema', 1)
            ->assertJsonPath('channel', 'staging')
            ->assertJsonPath('version', null)
            ->assertJsonPath('css', '')
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_only_active_admin_can_manage_style_profiles(): void
    {
        $payload = [
            'styles' => [],
            'texts' => [],
        ];

        $this->putJson('/api/admin/style-profile/draft', $payload)
            ->assertUnauthorized();

        $customer = User::factory()->create();
        $this->actingAs($customer)
            ->putJson('/api/admin/style-profile/draft', $payload)
            ->assertForbidden();

        $inactiveAdmin = User::factory()->admin()->inactive()->create();
        $this->actingAs($inactiveAdmin)
            ->putJson('/api/admin/style-profile/draft', $payload)
            ->assertForbidden();
    }

    public function test_admin_can_save_publish_and_restore_versioned_style_profiles(): void
    {
        $admin = User::factory()->admin()->create();

        $draftOne = [
            'name' => 'Customer palette review',
            'styles' => [
                'header.shell' => [
                    'backgroundColor' => 'blue',
                    'textColor' => 'white',
                ],
                'home.about.title' => [
                    'textColor' => 'green',
                ],
            ],
            'texts' => [
                'home.about.title' => [
                    'fa' => 'درباره ارمغان',
                    'en' => 'About Armaghan',
                ],
            ],
        ];

        $saveOne = $this->actingAs($admin)
            ->putJson('/api/admin/style-profile/draft', $draftOne)
            ->assertOk()
            ->assertJsonPath('profile.slug', 'default')
            ->assertJsonPath('draft.styles.header.shell.backgroundColor', 'blue');

        $checksumOne = $saveOne->json('draft.checksum');

        $this->assertIsString($checksumOne);
        $this->assertSame(64, strlen($checksumOne));
        $this->assertStringContainsString('var(--brand-blue)', $saveOne->json('draft.css'));

        $publishOne = $this->actingAs($admin)
            ->postJson('/api/admin/style-profile/publish/staging', ['source_test' => '27'])
            ->assertCreated()
            ->assertJsonPath('version.number', 1)
            ->assertJsonPath('channel', 'staging');

        $versionOneId = $publishOne->json('version.id');

        $draftTwo = [
            'expected_checksum' => $checksumOne,
            'styles' => [
                'header.shell' => [
                    'backgroundColor' => 'green',
                    'textColor' => 'white',
                ],
            ],
            'texts' => [],
        ];

        $this->actingAs($admin)
            ->putJson('/api/admin/style-profile/draft', $draftTwo)
            ->assertOk()
            ->assertJsonPath('draft.styles.header.shell.backgroundColor', 'green');

        $this->actingAs($admin)
            ->postJson('/api/admin/style-profile/publish/staging', ['source_test' => '27'])
            ->assertCreated()
            ->assertJsonPath('version.number', 2);

        $this->assertDatabaseCount('style_profile_versions', 2);

        $versionOne = StyleProfileVersion::query()->findOrFail($versionOneId);
        $this->assertSame('blue', $versionOne->styles['header.shell']['backgroundColor']);

        $restore = $this->actingAs($admin)
            ->postJson("/api/admin/style-profile/versions/{$versionOneId}/restore/staging", ['source_test' => '27'])
            ->assertCreated()
            ->assertJsonPath('version.number', 3)
            ->assertJsonPath('restored_from_version', 1);

        $this->assertDatabaseCount('style_profile_versions', 3);

        $publication = StyleProfilePublication::query()->where('channel', 'staging')->firstOrFail();
        $this->assertSame($restore->json('version.id'), $publication->style_profile_version_id);

        $this->getJson('/api/style-profile/staging')
            ->assertOk()
            ->assertJsonPath('version.number', 3)
            ->assertJsonPath('styles.header.shell.backgroundColor', 'blue')
            ->assertJsonPath('texts.home.about.title.fa', 'درباره ارمغان');

        $this->assertDatabaseHas('activity_log', [
            'action' => 'style_profile.restored',
            'subject_id' => $restore->json('version.id'),
        ]);

        $this->assertSame(
            'blue',
            StyleProfileVersion::query()->findOrFail($versionOneId)->styles['header.shell']['backgroundColor'],
        );

        $this->actingAs($admin)
            ->postJson('/api/admin/style-profile/publish/staging', ['source_test' => '27'])
            ->assertCreated()
            ->assertJsonPath('version.number', 3);

        $this->assertDatabaseCount('style_profile_versions', 3);
    }

    public function test_draft_validation_rejects_unknown_tokens_arbitrary_css_and_bad_target_ids(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->putJson('/api/admin/style-profile/draft', [
                'css' => 'body{display:none}',
                'styles' => [
                    'hero"]{display:none}/*' => [
                        'textColor' => 'magenta',
                    ],
                ],
                'texts' => [],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['css', 'styles']);

        $this->assertDatabaseCount('style_profiles', 0);
    }

    public function test_stale_draft_checksum_returns_conflict_instead_of_overwriting_newer_work(): void
    {
        $admin = User::factory()->admin()->create();

        $initial = $this->actingAs($admin)
            ->putJson('/api/admin/style-profile/draft', [
                'styles' => [],
                'texts' => [],
            ])
            ->assertOk();

        $checksum = $initial->json('draft.checksum');

        $this->actingAs($admin)
            ->putJson('/api/admin/style-profile/draft', [
                'expected_checksum' => $checksum,
                'styles' => [
                    'header.shell' => [
                        'backgroundColor' => 'blue',
                    ],
                ],
                'texts' => [],
            ])
            ->assertOk();

        $this->actingAs($admin)
            ->putJson('/api/admin/style-profile/draft', [
                'expected_checksum' => $checksum,
                'styles' => [
                    'header.shell' => [
                        'backgroundColor' => 'green',
                    ],
                ],
                'texts' => [],
            ])
            ->assertStatus(409);

        $profile = StyleProfile::query()->where('slug', 'default')->firstOrFail();
        $this->assertSame('blue', $profile->draft_styles['header.shell']['backgroundColor']);
    }

    public function test_admin_endpoint_exposes_draft_history_and_publication_pointer(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->putJson('/api/admin/style-profile/draft', [
                'styles' => [],
                'texts' => [],
            ])
            ->assertOk();

        $this->actingAs($admin)
            ->postJson('/api/admin/style-profile/publish/staging', ['source_test' => '27'])
            ->assertCreated();

        $this->actingAs($admin)
            ->getJson('/api/admin/style-profile')
            ->assertOk()
            ->assertJsonPath('profile.slug', 'default')
            ->assertJsonPath('versions.0.number', 1)
            ->assertJsonPath('publications.staging.version', 1);

        $this->assertSame(1, ActivityLog::query()->where('action', 'style_profile.published')->count());
    }
}
