# Customer Google sign-in / signup

Backend Laravel and Filament are live. Official Laravel Socialite is included in the guarded additive release. One Google callback handles both new customer signup and returning customer sign-in.

## One-time owner-controlled setup
1. In Google Cloud Console select/create your production project and configure Branding/Audience for Armaghan, real support email and authorized domain armaghantrading.com. Add actual privacy/terms URLs before publishing; do not invent policy compliance.
2. Create OAuth Client → Web application. Exact authorized callback:
   https://armaghantrading.com/backend/auth/google/callback
3. Set these values in the PRIVATE host-managed shared .env outside public_html:
   GOOGLE_AUTH_ENABLED=true
   GOOGLE_CLIENT_ID=<your client id>
   GOOGLE_CLIENT_SECRET=<your client secret>
   GOOGLE_REDIRECT_URI=https://armaghantrading.com/backend/auth/google/callback
   Never paste the secret into chat, Git, browser build variables, logs or public files.
4. While the Cloud app is in Testing, add intended Google test users. Publish the Cloud app when branding/domain/required policies are ready.
5. Verify GET /backend/api/auth/google/status reports only enabled:true, then perform real signup and repeat login. A false value means server setup is incomplete. Agent cannot configure an unseen Cloud account or create owner credentials without access.

## Identity and session policy
- Normal stateful Socialite validates callback state; no stateless OAuth.
- Verified Google email is required; immutable Google subject owns the identity record. No access/refresh tokens are retained.
- Existing email alone never grants or links an account. A matching customer must first authenticate using their existing secure Magic Link; retry Google to explicitly link that customer. Administrator emails and inactive users/customers cannot be used for customer Google sign-in.
- The separate CustomerSession rotates its identifier and preserves any real Filament admin authentication. No Google user becomes an admin.
- New customers appear in real Filament customer management. Callback failures are generic and do not log identity/token payloads. Return destinations are only root or a positive numbered /t folder.
- Root sign-in UI hides browser-only demo password/register forms. Numbered review lanes may retain their historical demo fallback; root uses real backend sessions only.
- Production credentials are not deployed or overwritten by ordinary releases. The switch defaults OFF. Code/deployment tests do not prove a real Google account login.

## Evidence
Dependency resolution run 37007410283 passed. Eleven automated backend cases cover disabled state, redirect allowlist, signup/repeat identity, existing-email/admin denial, authenticated linking, unverified/inactive denial, independent sessions, invalid state and cancellation. Backend deploy and actual credential readiness are pending closeout.

Sources: https://laravel.com/framework/docs/socialite and https://developers.google.com/identity/openid-connect/openid-connect .

Dependency preparation uses `composer require laravel/socialite:^5.27 --no-install --no-scripts`; deployment installs the validated lock. The client secret exists only in the private server environment.

Production endpoints: `/backend/auth/google/redirect` and `/backend/auth/google/callback`; readiness: `/backend/api/auth/google/status`.
