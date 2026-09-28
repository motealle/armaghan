# Armaghan Agent Instructions

These instructions apply to every AI chat, coding agent, automation and human-assisted tool working in this repository.

## Mandatory startup
Before planning a repository mutation, read:
1. `docs/PROJECT-RULES.md`
2. `docs/HANDOFF.md`
3. `docs/BACKLOG.md`
4. the current test audit and relevant asset/media documents

Repository rules are authoritative; chat memory is secondary.

## Mandatory shared write lock
Before the first mutating action, follow the canonical concurrency protocol in `docs/PROJECT-RULES.md` rules 64–74.

Canonical coordination state:
- branch: `coordination/armaghan-lock`
- file: `.armaghan-work-lock.json`
- default lease: 90 minutes
- renew before 60 minutes if work is still active

If another unexpired holder exists, or lock state cannot be checked safely, do not write, commit, create a numbered test, edit `/t/index.htm`, or deploy. Stop and report the collision/blocker.

Acquisition and release must use the exact current blob SHA of the lock file. A conflict means another worker won the race; fail closed.

Never release a lock owned by a different `run_id`.

## Existing project protections
Respect all frozen-test, deployment-scope, no-remote-delete, media, UX, QA and repository-memory rules in `docs/PROJECT-RULES.md`. In particular, older numbered tests are immutable snapshots and prototype deployment is scoped to `/public_html/t`.
