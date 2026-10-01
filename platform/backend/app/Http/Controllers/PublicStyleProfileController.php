<?php

namespace App\Http\Controllers;

use App\Services\StyleProfileCompiler;
use App\Services\StyleProfileService;
use Illuminate\Http\JsonResponse;

class PublicStyleProfileController extends Controller
{
    public function show(
        StyleProfileService $service,
        string $channel = 'production',
    ): JsonResponse {
        $publication = $service->publication($channel);

        if ($publication === null) {
            return response()->json([
                'schema' => StyleProfileCompiler::SCHEMA_VERSION,
                'channel' => $channel,
                'profile' => null,
                'version' => null,
                'styles' => (object) [],
                'texts' => (object) [],
                'css' => '',
                'checksum' => null,
            ])->header('Cache-Control', 'no-store');
        }

        $version = $publication->version;
        $profile = $version->profile;

        return response()->json([
            'schema' => $version->schema_version,
            'channel' => $channel,
            'profile' => [
                'id' => $profile->id,
                'slug' => $profile->slug,
                'name' => $profile->name,
            ],
            'version' => [
                'id' => $version->id,
                'number' => $version->version,
                'source_test' => $version->source_test,
                'published_at' => $publication->published_at?->toISOString(),
            ],
            'styles' => $version->styles,
            'texts' => $version->texts,
            'css' => $version->compiled_css,
            'checksum' => $version->checksum,
        ])->header('Cache-Control', 'no-store');
    }
}
