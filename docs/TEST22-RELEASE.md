# Test 22 — Release Hardening + Immutable Snapshot Handoff

## Purpose

Test 22 creates a clean release boundary from the successful Test 21 experience without inventing new product scope. Its job is to preserve Test 21 as a released snapshot, move all mutable frontend state and build output to version 22, and prove that the CI/CD pipeline can publish the new snapshot independently.

## Release decisions

- Test 21 is added to the immutable snapshot list.
- Frontend package version is bumped to `0.22.0`.
- Vite output moves from `/t/21` to `/t/22`.
- Browser-owned mutable state moves from `armaghan:test21:*` to `armaghan:test22:*`.
- Product catalog data may migrate forward from the Test 21 product key so existing prototype catalog edits are not silently discarded.
- Other browser state remains isolated per test to keep released snapshots behaviorally independent.
- Test 20 and Test 21 contracts remain active as regression gates.
- A new Test 22 release contract validates versioning, snapshot freeze, launcher order, storage isolation and deployment targeting.
- The mutable launcher lists Test 22 before Test 21.
- FTP deployment remains scoped to `/public_html/t`; root deployment remains separately gated and remote deletion remains forbidden.

## Delivery contract

A Test 22 release is complete only when all of the following are true:

1. immutable snapshot guard passes with Test 21 frozen;
2. SQLite and Test 11–22 contracts pass;
3. TypeScript type-check and Vitest unit tests pass;
4. Vite production build creates `t/22`;
5. FTP smoke test passes;
6. `/public_html/t/22/index.html` and `/public_html/t/index.htm` are uploaded;
7. `deploy-root` is skipped for the prototype-only release;
8. deployment log confirms that no remote files were deleted.

## Production boundary

Test 22 is still a UX/product prototype snapshot. Laravel, Filament, server-side persistence, production authentication, hashed magic links and server-side media processing remain P1 productionization work and are intentionally not simulated as complete in this release.
## Live delivery

- GitHub Actions **FTP Deploy Run #74** completed successfully.
- Test 11–22 regression/release contracts passed.
- TypeScript type-check, unit tests and Vite production build passed.
- FTP smoke test passed.
- `/public_html/t/22/index.html` and the mutable launcher were uploaded.
- `deploy-root` was skipped.
- Deployment completed with **no remote file deletion**.

