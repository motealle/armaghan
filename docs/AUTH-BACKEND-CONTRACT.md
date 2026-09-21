# Authentication backend contract

Test 19 exposes the final intended authentication surfaces without pretending that browser-only code can provide production authentication.

## Supported surface

| Method | Test 19 behavior | Production backend |
|---|---|---|
| Username/password | Functional local prototype account + existing owner test credentials | Laravel auth with hashed passwords, throttling, email verification/reset |
| Registration | Functional local prototype persistence | Laravel validation + database + verification |
| Direct / magic link | Functional local generated token with Permanent or Expiring mode and revoke state | Store only a hash of the token; signed HTTPS route; scope; expiry; one-time/permanent policy; revoke; audit log |
| Google | UI is wired to the route contract `/auth/google/redirect`; no fake success | Laravel Socialite + Google OAuth client ID/secret + allowed callback URL |

## Google OAuth

Step-by-step production activation: `docs/GOOGLE-OAUTH-SETUP.md`.

Live Google sign-in cannot be completed safely without server-side OAuth credentials and callback handling. The frontend button must never simulate a successful Google login.

Expected Laravel routes:

- `GET /auth/google/redirect`
- `GET /auth/google/callback`

Expected callback behavior:

1. Validate OAuth state.
2. Resolve verified Google identity.
3. Link to an existing account only under an explicit safe policy.
4. Create/update the application session.
5. Redirect to the account/tracking view.

Secrets must stay in server environment variables and must never be committed to the repository or embedded in the Vue bundle.

## Direct-link model

Recommended database fields:

- `user_id`
- `token_hash`
- `mode`: `permanent | expiring`
- `expires_at` nullable
- `revoked_at` nullable
- `last_used_at` nullable
- `created_by_admin_id` nullable
- `scope`
- timestamps

Raw tokens are shown once when generated and are not stored in plaintext on the production server.

## Security boundaries

- Direct links are capabilities; possession grants only the scope encoded for that customer account.
- Admin impersonation is separate from customer direct access and must be audited.
- Password reset is not equivalent to an admin reading a customer's password; production passwords remain one-way hashed.
- Anonymous favorites use a random visitor token. IP address may be optional short-lived security/risk metadata but is **not** used as account identity.
