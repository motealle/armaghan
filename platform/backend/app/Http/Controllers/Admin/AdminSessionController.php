<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSessionController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['admin' => [
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'is_owner' => $request->user()->isPrimaryOwner(),
        ]])->header('Cache-Control', 'no-store, private');
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->forget(['armaghan.password_setup_user_id', 'armaghan.password_setup_until', 'password_hash_web']);
        // Rotate authentication state without deleting the independent customer session.
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return response()->json(['ok' => true])->header('Cache-Control', 'no-store, private');
    }
}
