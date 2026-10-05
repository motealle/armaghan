<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AccountArchiveService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccountArchiveController extends Controller
{
    public function index(Request $request, string $resource, AccountArchiveService $service): JsonResponse
    {
        return $this->json(['archives' => $service->listing($request->user(), $resource)]);
    }

    public function prepare(Request $request, string $resource, int $id, AccountArchiveService $service): JsonResponse
    {
        $data = $request->validate(['revision' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/']]);
        return $this->json($service->prepare($request->user(), $resource, $id, $data['revision']));
    }

    public function destroy(Request $request, string $archive, AccountArchiveService $service): JsonResponse
    {
        $data = $request->validate(['receipt' => ['required', 'string', 'size:32'],
            'backup_sha256' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/'], 'backup_downloaded' => ['required', 'accepted']]);
        $service->remove($request->user(), $archive, $data['receipt'], $data['backup_sha256']);
        return $this->json(['ok' => true]);
    }

    public function restore(Request $request, string $archive, AccountArchiveService $service): JsonResponse
    {
        $service->restore($request->user(), $archive);
        return $this->json(['ok' => true]);
    }

    public function import(Request $request, AccountArchiveService $service): JsonResponse
    {
        $data = $request->validate(['backup' => ['required', 'string', 'max:100000']]);
        $backup = json_decode($data['backup'], true);
        abort_unless(is_array($backup) && ($backup['format'] ?? '') === 'armaghan.account-backup.v1'
            && is_string($backup['archive_id'] ?? null) && \Illuminate\Support\Str::isUuid($backup['archive_id']), 422);
        $service->restore($request->user(), $backup['archive_id'], $data['backup']);
        return $this->json(['ok' => true]);
    }

    private function json(array $data): JsonResponse
    {
        return response()->json($data)->header('Cache-Control', 'no-store, private');
    }
}
