# Customer Google sign-in / signup

Backend Laravel and Filament are live. Official Laravel Socialite is included in the guarded additive release. One Google callback handles both new customer signup and returning customer sign-in.

## One-time owner-controlled setup
1. In Google Cloud Console select/create your production project and configure Branding/Audience for Armaghan, real support email and authorized domain armaghantrading.com. Add actual privacy/terms URLs before publishing; do not invent policy compliance.
2. Create OAuth Client → Web application. Exact authorized callback:
   https://armaghantrading.com/backend/auth/google/callback
3. Edit the existing PRIVATE `armaghan-data/.env` and active `armaghan-backend/.env`, both siblings of public_html. Preserve all existing settings. Set these values in both:
   GOOGLE_AUTH_ENABLED=true
   GOOGLE_CLIENT_ID=<your client id>
   GOOGLE_CLIENT_SECRET=<your client secret>
   GOOGLE_REDIRECT_URI=https://armaghantrading.com/backend/auth/google/callback
   Never paste the secret into chat, Git, browser build variables, logs or public files.
4. Basic identity scopes only (`openid`, `email`, `profile`) are exempt from the Testing allowlist requirement. Other scopes can require test users and verification. Publish the Cloud app when branding/domain/required policies are ready. See https://support.google.com/cloud/answer/15549945 .
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
Dependency resolution run 37007410283 passed. Eleven automated backend cases cover disabled state, redirect allowlist, signup/repeat identity, existing-email/admin denial, authenticated linking, unverified/inactive denial, independent sessions, invalid state and cancellation. Backend CI 37008505779 and Additive Deploy 37008505784 PASS (49 tests, 348 assertions). Live readiness check in FTP 37009477221 reports enabled:false; actual credentials and real Google login remain OPEN.

Sources: https://laravel.com/framework/docs/socialite and https://developers.google.com/identity/openid-connect/openid-connect .

Dependency preparation uses `composer require laravel/socialite:^5.27 --no-install --no-scripts`; deployment installs the validated lock. The client secret exists only in the private server environment.

Production endpoints: `/backend/auth/google/redirect` and `/backend/auth/google/callback`; readiness: `/backend/api/auth/google/status`.

## Google activation and frontend return repair — 2026-10-02

- Owner created the Google web client and installed private credentials in both `armaghan-data/.env` and `armaghan-backend/.env`; neither file nor secret is in Git. Live status now returns enabled:true.
- Read-only redirect check: HTTP 302 to accounts.google.com; expected client ID/callback, openid/profile/email and state present.
- Owner reported the Laravel welcome page after Google login. A fresh cancellation probe reproduced HTTP 302 to https://armaghantrading.com/backend/#/tracking?auth_error=google. Laravel prefixes frontend-relative redirects with the production /backend root.
- Repair uses the fixed production origin plus the existing strict root/numbered-test path allowlist for success, cancellation and invalid-state returns. No authentication bypass or email-only linking.
- Two regression cases force a /backend URL root and cover successful root login and cancellation to root/numbered paths, including hostile path fallback. Existing assertions now require the fixed production origin.
- Deployment/tests and fresh live cancellation probe pending. Real Google signup/repeat login still OPEN; the screenshot is not proof of an authenticated customer session.
- Tests 01–28, Test29 assets, root selector, host credentials and database schema remain unchanged.

| Rank | Method | Score | Reason |
|---:|---|---:|---|
| 1 | Fixed frontend origin + existing strict path allowlist | 9.5 | Repairs actual subdirectory behavior without extra configuration |
| 2 | Configurable frontend origin | 8 | Flexible but adds a host setting |
| 3 | Change all backend base URL settings | 5 | Affects unrelated backend links |
| 4 | Redirect backend welcome route | 4 | Masks the wrong callback destination |
| 5 | Browser-side forwarding | 2 | Depends on loading another page |

Selected: option 1.
