<?php

namespace App\Services;

use App\Exceptions\StyleProfileDraftConflict;
use App\Models\ActivityLog;
use App\Models\StyleProfile;
use App\Models\StyleProfilePublication;
use App\Models\StyleProfileVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StyleProfileService
{
    public const DEFAULT_SLUG = 'default';

    /** @var list<string> */
    public const CHANNELS = ['staging', 'production'];

    public function __construct(
        private readonly StyleProfileCompiler $compiler,
    ) {}

    public function findDefault(): ?StyleProfile
    {
        return StyleProfile::query()
            ->where('slug', self::DEFAULT_SLUG)
            ->first();
    }

    public function ensureDefault(User $actor): StyleProfile
    {
        $prepared = $this->compiler->prepare([], []);

        return StyleProfile::query()->firstOrCreate(
            ['slug' => self::DEFAULT_SLUG],
            [
                'name' => 'Armaghan Default',
                'schema_version' => StyleProfileCompiler::SCHEMA_VERSION,
                'draft_styles' => $prepared['styles'],
                'draft_texts' => $prepared['texts'],
                'draft_css' => $prepared['css'],
                'draft_checksum' => $prepared['checksum'],
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function saveDraft(User $actor, array $input): StyleProfile
    {
        $profile = $this->ensureDefault($actor);

        return DB::transaction(function () use ($profile, $actor, $input): StyleProfile {
            $locked = StyleProfile::query()
                ->whereKey($profile->id)
                ->lockForUpdate()
                ->firstOrFail();

            $expected = $input['expected_checksum'] ?? null;

            if (is_string($expected) && ! hash_equals($locked->draft_checksum, $expected)) {
                throw new StyleProfileDraftConflict('The style profile draft changed after it was loaded.');
            }

            $prepared = $this->compiler->prepare(
                $input['styles'] ?? [],
                $input['texts'] ?? [],
            );

            $locked->fill([
                'name' => $input['name'] ?? $locked->name,
                'schema_version' => StyleProfileCompiler::SCHEMA_VERSION,
                'draft_styles' => $prepared['styles'],
                'draft_texts' => $prepared['texts'],
                'draft_css' => $prepared['css'],
                'draft_checksum' => $prepared['checksum'],
                'updated_by' => $actor->id,
            ])->save();

            return $locked->fresh();
        }, 3);
    }

    public function publish(
        User $actor,
        string $channel,
        ?string $sourceTest = null,
    ): StyleProfileVersion {
        $this->assertChannel($channel);
        $profile = $this->ensureDefault($actor);

        return DB::transaction(function () use ($profile, $actor, $channel, $sourceTest): StyleProfileVersion {
            $locked = StyleProfile::query()
                ->whereKey($profile->id)
                ->lockForUpdate()
                ->firstOrFail();

            $currentPublication = StyleProfilePublication::query()
                ->where('channel', $channel)
                ->with('version')
                ->lockForUpdate()
                ->first();

            if (
                $currentPublication?->version
                && $currentPublication->version->style_profile_id === $locked->id
                && hash_equals($currentPublication->version->checksum, $locked->draft_checksum)
            ) {
                return $currentPublication->version;
            }

            return $this->createPublishedVersion(
                profile: $locked,
                actor: $actor,
                channel: $channel,
                sourceTest: $sourceTest,
                styles: $locked->draft_styles,
                texts: $locked->draft_texts,
            );
        }, 3);
    }

    public function restore(
        User $actor,
        StyleProfileVersion $historicalVersion,
        string $channel,
        ?string $sourceTest = null,
    ): StyleProfileVersion {
        $this->assertChannel($channel);
        $profile = $this->ensureDefault($actor);

        if ($historicalVersion->style_profile_id !== $profile->id) {
            throw new InvalidArgumentException('The requested version does not belong to the default style profile.');
        }

        return DB::transaction(function () use ($profile, $historicalVersion, $actor, $channel, $sourceTest): StyleProfileVersion {
            $locked = StyleProfile::query()
                ->whereKey($profile->id)
                ->lockForUpdate()
                ->firstOrFail();

            $prepared = $this->compiler->prepare(
                $historicalVersion->styles,
                $historicalVersion->texts,
            );

            $locked->fill([
                'schema_version' => StyleProfileCompiler::SCHEMA_VERSION,
                'draft_styles' => $prepared['styles'],
                'draft_texts' => $prepared['texts'],
                'draft_css' => $prepared['css'],
                'draft_checksum' => $prepared['checksum'],
                'updated_by' => $actor->id,
            ])->save();

            $version = $this->createPublishedVersion(
                profile: $locked,
                actor: $actor,
                channel: $channel,
                sourceTest: $sourceTest,
                styles: $prepared['styles'],
                texts: $prepared['texts'],
                restoredFrom: $historicalVersion->version,
            );

            return $version;
        }, 3);
    }

    public function publication(string $channel): ?StyleProfilePublication
    {
        $this->assertChannel($channel);

        return StyleProfilePublication::query()
            ->where('channel', $channel)
            ->with('version.profile')
            ->first();
    }

    /**
     * @return array<int, StyleProfileVersion>
     */
    public function history(StyleProfile $profile, int $limit = 25): array
    {
        return $profile->versions()
            ->latest('version')
            ->limit($limit)
            ->get()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $styles
     * @param  array<string, mixed>  $texts
     */
    private function createPublishedVersion(
        StyleProfile $profile,
        User $actor,
        string $channel,
        ?string $sourceTest,
        array $styles,
        array $texts,
        ?int $restoredFrom = null,
    ): StyleProfileVersion {
        $prepared = $this->compiler->prepare($styles, $texts);

        $nextVersion = ((int) $profile->versions()->max('version')) + 1;

        $version = $profile->versions()->create([
            'version' => $nextVersion,
            'schema_version' => StyleProfileCompiler::SCHEMA_VERSION,
            'source_test' => $sourceTest,
            'styles' => $prepared['styles'],
            'texts' => $prepared['texts'],
            'compiled_css' => $prepared['css'],
            'checksum' => $prepared['checksum'],
            'created_by' => $actor->id,
        ]);

        StyleProfilePublication::query()->updateOrCreate(
            ['channel' => $channel],
            [
                'style_profile_version_id' => $version->id,
                'published_by' => $actor->id,
                'published_at' => now(),
            ],
        );

        ActivityLog::query()->create([
            'actor_user_id' => $actor->id,
            'action' => $restoredFrom === null
                ? 'style_profile.published'
                : 'style_profile.restored',
            'subject_type' => StyleProfileVersion::class,
            'subject_id' => $version->id,
            'metadata' => array_filter([
                'channel' => $channel,
                'profile' => $profile->slug,
                'version' => $version->version,
                'checksum' => $version->checksum,
                'restored_from_version' => $restoredFrom,
            ], fn (mixed $value): bool => $value !== null),
        ]);

        return $version->fresh();
    }

    private function assertChannel(string $channel): void
    {
        if (! in_array($channel, self::CHANNELS, true)) {
            throw new InvalidArgumentException("Unsupported style publication channel [{$channel}].");
        }
    }
}
