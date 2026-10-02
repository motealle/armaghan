<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\GoogleCustomerIdentityService;
use App\Support\CustomerSession;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleCustomerAuthController extends Controller
{
    private const RETURN_KEY = 'armaghan.google.return_path';

    public function status(): JsonResponse
    {
        return response()->json(['enabled' => $this->configured()])
            ->header('Cache-Control', 'no-store');
    }

    public function redirect(Request $request): RedirectResponse
    {
        abort_unless($this->configured(), 503, 'Google sign-in is not configured yet.');
        $path = $request->query('return_path', '/');
        abort_unless(is_string($path) && preg_match('#^/(?:t/(?:0[1-9]|[1-9][0-9]*)/)?$#D', $path), 422);
        $request->session()->put(self::RETURN_KEY, $path);
        return Socialite::driver('google')->scopes(['openid', 'email', 'profile'])->redirect();
    }

    public function callback(Request $request, GoogleCustomerIdentityService $identities): RedirectResponse
    {
        $path = $request->session()->pull(self::RETURN_KEY, '/');
        if (! is_string($path) || ! preg_match('#^/(?:t/(?:0[1-9]|[1-9][0-9]*)/)?$#D', $path)) {
            $path = '/';
        }
        if (! $this->configured() || $request->has('error')) {
            $request->session()->forget('state');
            return $this->frontendReturn($path, true);
        }
        try {
            // Stateful Socialite validates the one-use session state. Never use stateless().
            $identity = Socialite::driver('google')->user();
            $customer = $identities->resolve($identity, CustomerSession::current($request));
            ActivityLog::create([
                'customer_id' => $customer->id,
                'action' => 'customer.google.signed_in',
                'subject_type' => get_class($customer),
                'subject_id' => $customer->id,
            ]);
            CustomerSession::login($request, $customer);
        } catch (Throwable) {
            // Do not log provider tokens, authorization codes or identity payloads.
            $request->session()->forget('state');
            return $this->frontendReturn($path, true);
        }
        return $this->frontendReturn($path);
    }

    private function frontendReturn(string $path, bool $failed = false): RedirectResponse
    {
        // Laravel's URL root includes /backend in production. Frontend paths
        // must resolve at the fixed site origin, after the return-path allowlist.
        return redirect()->away('https://armaghantrading.com'.$path.'#/tracking'
            .($failed ? '?auth_error=google' : ''));
    }

    private function configured(): bool
    {
        return (bool) config('services.google.enabled')
            && is_string(config('services.google.client_id')) && config('services.google.client_id') !== ''
            && is_string(config('services.google.client_secret')) && config('services.google.client_secret') !== ''
            && config('services.google.redirect') === 'https://armaghantrading.com/backend/auth/google/callback';
    }
}
