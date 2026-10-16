# Customer access follow-up: release decision

| Rank | Method | Reason |
|---:|---|---|
| 1 | Optional six-digit customer PIN, block obvious codes, account-wide failed-attempt budget | Meets requested simple access while retaining stronger passwords and isolating admin/email policies |
| 2 | PIN plus SMS recovery | Requires provider, cost and verified phone flow outside current no-SMS scope |
| 3 | Keep eight-character letter/number minimum | Smallest change, fails explicit requested six-digit access |
| 4 | Magic links only | Depends on delivery channel and changes accepted login UX |
| 5 | Shared/default password or phone-only login | Rejected: account takeover and privacy risk |

Selected: option 1, reusing Laravel validation rules and rate limiter. References: https://laravel.com/framework/docs/validation and https://laravel.com/framework/docs/rate-limiting ; official Laravel routing documentation for named middleware limiters.

## Risks and required checks

- Never normalize administrator/email passwords. For legacy phone identities check exact existing bytes before normalized PIN fallback.
- Mobile ownership remains unverified; never attach existing customer records or recover accounts based solely on mobile.
- Aliases share a user-ID failure budget; generic errors remain. Test wrong-password exhaustion, alias switch, expiry and successful-login reset.
- Preserve existing 86 FAQ entries and current search/sheet; no duplicate help center. Explain registration accurately in the existing FAQ.
- No database migration. Existing phone-identity migration from PR31 must be present on production before authenticated live acceptance. Code-only deployment guard must abort on schema drift.
- Rollback code with a normal revert and prior root revision; do not delete additive identity tables or customer accounts. Existing six-digit accounts still need password-login compatibility if registration policy is reverted.
- Local PHP unavailable. Require remote PHP unit/feature tests and existing CI before merge. Type-check/build and focused Vue checks already passed; device and actual hosting acceptance remain separate.
