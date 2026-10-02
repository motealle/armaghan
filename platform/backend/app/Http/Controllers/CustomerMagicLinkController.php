<?php

namespace App\Http\Controllers;

use App\Exceptions\MagicLinkUnavailable;
use App\Models\Customer;
use App\Services\CustomerMagicLinkService;
use App\Support\CustomerSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerMagicLinkController extends Controller
{
    public function __invoke(
        Request $request,
        string $token,
        CustomerMagicLinkService $magicLinks,
    ): RedirectResponse {
        try {
            $customer = $magicLinks->consume($token);
        } catch (MagicLinkUnavailable) {
            return $this->portalRedirect(
                (string) config('armaghan.customer.portal_invalid_path', '/t/27/?auth=link-invalid#/tracking'),
            );
        }

        CustomerSession::login($request, $customer);

        return $this->portalRedirect(
            (string) config('armaghan.customer.portal_path', '/t/27/?auth=magic-login#/tracking'),
        );
    }

    private function portalRedirect(string $path): RedirectResponse
    {
        return redirect()->to($path)
            ->header('Cache-Control', 'no-store, private')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
