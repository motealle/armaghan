# Google Sign-In — Laravel Socialite Setup

The Vue prototype already exposes the intended route contract:

- `GET /auth/google/redirect`
- `GET /auth/google/callback`

Do not place a Google client secret in Vue, Vite environment variables exposed to the browser, Git, or the static `/t` build.

## 1. Google Cloud

1. Open Google Cloud Console and select/create the production project.
2. Configure the OAuth consent/brand screen with the real application name, support email, privacy policy and production domains.
3. Create **OAuth client ID → Web application**.
4. Add the production callback URI exactly:
   `https://YOUR-DOMAIN/auth/google/callback`
5. Add the local development callback if needed:
   `http://localhost:8000/auth/google/callback`
6. Copy the client ID and client secret to the server secret manager / server environment only.

Google requires the redirect URI to match exactly, including scheme, host, path and trailing-slash behavior.

## 2. Laravel 13

Install Socialite:

```bash
composer require laravel/socialite
```

Server environment:

```dotenv
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://YOUR-DOMAIN/auth/google/callback
```

`config/services.php`:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

Routes:

```php
use Laravel\Socialite\Facades\Socialite;

Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('auth.google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('auth.google.callback');
```

Controller policy:

1. Let Socialite validate the OAuth state using the normal session-based flow.
2. Fetch the Google user with `Socialite::driver('google')->user()`.
3. Require a usable email and apply an explicit account-linking rule.
4. Prefer linking to an existing customer only when the identity policy says it is safe; otherwise create a new customer/user.
5. Store Google provider ID separately from the customer's mutable email.
6. Create the Laravel authenticated session, regenerate the session ID, then redirect to the account/tracking screen.
7. Log account-linking/admin-sensitive events.

## 3. Production readiness checklist

- HTTPS enabled.
- Exact authorized redirect URI registered at Google.
- Client secret exists only on the server.
- Session/cookie domain and secure cookie settings configured.
- Login throttling and logout tested.
- Existing-account linking policy decided before launch.
- Callback failure/cancel path returns a safe localized message.
- Static Test builds continue to show backend-required behavior until the Laravel route is actually deployed.

## Current blocker

The repository currently contains the Vue prototype and backend contract, but not the Laravel production application. Therefore Google Sign-In must not report success yet. Once the Laravel backend is deployed and the two Google credentials are configured, the frontend button can navigate directly to `/auth/google/redirect`.
