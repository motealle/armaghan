# Armaghan Agent Instructions

These instructions apply to every AI chat, coding agent, automation and human-assisted tool working in this repository.

## Mandatory startup
Before planning a repository mutation, read:
1. `docs/PROJECT-RULES.md`
2. `docs/CURRENT-STATUS.md`
3. `docs/HANDOFF.md`
4. `docs/BACKLOG.md`
5. the current test audit and relevant asset/media documents

Repository rules are authoritative; `docs/CURRENT-STATUS.md` is the canonical current-state summary; chat memory is secondary.

## Owner authorization and single-thread work
On 2026-10-05 the owner permanently authorized work, commits, pushes, merges and releases for `motealle/armaghan` on GitHub. Do not request publication permission again for ordinary project work within this repository's scope.

The owner manages one active work thread. Shared repository write-lock rules 64–74 are superseded: do not acquire, renew, inspect or enforce the historical coordination lock as a work prerequisite. Keep non-force Git updates, frozen-version protections, tests, deployment boundaries and secret protection. This does not disable GitHub branch protection or deployment serialization.

## Existing project protections
Respect all frozen-test, deployment-scope, no-remote-delete, media, UX, QA and repository-memory rules in `docs/PROJECT-RULES.md`. In particular, older numbered tests are immutable snapshots and prototype deployment is scoped to `/public_html/t`.

## Mandatory terminology

Every user-facing Armaghan surface must follow `docs/PROJECT-RULES.md` rules 75–77: on first use, each specialist or technical term needs a short plain-Persian explanation in parentheses. This applies to chat replies, reports, handoffs, automation summaries and review notes across all agents and tools working from this repository.

## Frozen UI release lane

Tests 01–28 are frozen. Test29 is the current mutable lane. Read rules 167–176 before build/deploy; visual checks are deferred by the owner. Never rebuild or redeploy Test 26 from current main.

Owner now authorizes guarded root index promotion of selected Test29; read PROJECT-RULES 177–180 and docs/ROOT-RELEASE.md. Never root-wide sync. Root selection is independent of the active test lane.
