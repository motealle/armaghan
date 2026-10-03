<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\StyleProfileDraftConflict;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveStyleProfileDraftRequest;
use App\Models\StyleProfilePublication;
use App\Models\StyleProfileVersion;
use App\Models\User;
use App\Services\StyleProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class StyleProfileController extends Controller
{
    public function show(Request $request, StyleProfileService $service): JsonResponse
    {
        $actor = $this->actor($request);
        $profile = $service->ensureDefault($actor);

        $publications = StyleProfilePublication::query()
            ->with('version')
            ->get()
            ->keyBy('channel')
            ->map(fn (StyleProfilePublication $publication): array => [
                'version' => $publication->version->version,
                'checksum' => $publication->version->checksum,
                'published_at' => $publication->published_at?->toISOString(),
            ]);

        return response()->json([
            'profile' => [
                'id' => $profile->id,
                'slug' => $profile->slug,
                'name' => $profile->name,
                'schema' => $profile->schema_version,
            ],
            'draft' => [
                'styles' => $profile->draft_styles,
                'texts' => $profile->draft_texts,
                'css' => $profile->draft_css,
                'checksum' => $profile->draft_checksum,
                'updated_at' => $profile->updated_at?->toISOString(),
            ],
            'versions' => array_map(
                fn (StyleProfileVersion $version): array => $this->serializeVersion($version),
                $service->history($profile),
            ),
            'publications' => $publications,
        ]);
    }

    public function updateDraft(
        SaveStyleProfileDraftRequest $request,
        StyleProfileService $service,
    ): JsonResponse {
        try {
            $profile = $service->saveDraft(
                $this->actor($request),
                $request->validated(),
            );
        } catch (StyleProfileDraftConflict $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        }

        return response()->json([
            'profile' => [
                'id' => $profile->id,
                'slug' => $profile->slug,
                'name' => $profile->name,
            ],
            'draft' => [
                'styles' => $profile->draft_styles,
                'texts' => $profile->draft_texts,
                'css' => $profile->draft_css,
                'checksum' => $profile->draft_checksum,
                'updated_at' => $profile->updated_at?->toISOString(),
            ],
        ]);
    }

    public function publish(
        Request $request,
        StyleProfileService $service,
        string $channel,
    ): JsonResponse {
        $data = $request->validate([
            'expected_checksum' => ['sometimes', 'nullable', 'regex:/^[a-f0-9]{64}$/'],
            'source_test' => ['sometimes', 'nullable', 'regex:/^\d{1,3}$/'],
        ]);

        try {
        $version = $service->publish(
            actor: $this->actor($request),
            channel: $channel,
            sourceTest: $data['source_test'] ?? null,
            expectedChecksum: $data['expected_checksum'] ?? null,
        );

        } catch (StyleProfileDraftConflict $exception) {
            return response()->json(['message'=>$exception->getMessage()],409);
        }
        return response()->json([
            'version' => $this->serializeVersion($version),
            'channel' => $channel,
        ], 201);
    }

    public function restore(
        Request $request,
        StyleProfileService $service,
        StyleProfileVersion $styleProfileVersion,
        string $channel,
    ): JsonResponse {
        $data = $request->validate([
            'source_test' => ['sometimes', 'nullable', 'regex:/^\d{1,3}$/'],
        ]);

        try {
            $version = $service->restore(
                actor: $this->actor($request),
                historicalVersion: $styleProfileVersion,
                channel: $channel,
                sourceTest: $data['source_test'] ?? null,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        }

        return response()->json([
            'version' => $this->serializeVersion($version),
            'channel' => $channel,
            'restored_from_version' => $styleProfileVersion->version,
        ], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeVersion(StyleProfileVersion $version): array
    {
        return [
            'id' => $version->id,
            'number' => $version->version,
            'schema' => $version->schema_version,
            'source_test' => $version->source_test,
            'checksum' => $version->checksum,
            'created_at' => $version->created_at?->toISOString(),
        ];
    }

    private function actor(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        return $user;
    }
}
