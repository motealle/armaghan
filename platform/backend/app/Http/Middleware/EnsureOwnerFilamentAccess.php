<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureOwnerFilamentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user=$request->user();
        $allowed=$user?->isPrimaryOwner()
            && (int)$request->session()->get('armaghan.advanced_admin.user_id')===(int)$user->id
            && (int)$request->session()->get('armaghan.advanced_admin.until')>now()->timestamp;
        if (! $allowed) {
            if ($request->expectsJson()) { return response()->json(['message'=>'Use the custom administration interface.'],403); }
            return redirect()->away('https://armaghantrading.com/#/admin');
        }
        return $next($request);
    }
}
