# Armaghan — CI / Freeze Repair Analysis

Status: selected approach implemented as one repair set.

## A. Freeze enforcement

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | Immutable registry + frozen-handoff contract + build-target guard | 10.0 | Three independent layers catch committed changes and accidental rebuilds |
| 2 | Immutable registry only | 7.4 | Protects committed paths but not CI regeneration |
| 3 | Snapshot branch only | 6.8 | Good recovery point but does not block overwrite |
| 4 | Human convention only | 3.0 | Easy to violate under parallel work |
| 5 | Delete old source/build logic | 1.5 | Destructive and harms reproducibility |

Selected: option 1.

## B. Historical Test 22 contract

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | Assert historical release invariants only | 10.0 | Verifies Test 22 without constraining Test 27+ source |
| 2 | Accept a growing list of current test numbers | 5.0 | Repeats the brittle regression every release |
| 3 | Skip Test 22 in CI | 4.0 | Removes useful historical coverage |
| 4 | Pin current source to Test 26 forever | 1.5 | Blocks customer UI iteration |
| 5 | Delete historical contract | 1.0 | Loses release memory |

Selected: option 1.

## C. Main frontend build/deploy targeting

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | Safe local preview + explicit CI active target 27 + reject frozen targets | 10.0 | Prevents accidental Test 26 writes and keeps the next lane ready |
| 2 | Hard-code Vite directly to Test 27 | 7.4 | Local maintenance can create numbered output accidentally |
| 3 | Keep Test 26 target until first UI request | 3.0 | Leaves a known overwrite hazard |
| 4 | Disable frontend deployment during backend work | 2.5 | Blocks urgent customer UI corrections |
| 5 | Rebuild Test 26 on every frontend change | 0.0 | Violates the freeze |

Selected: option 1.

## D. Technical-term explanations

| Rank | Method | Score | Why |
|---:|---|---:|---|
| 1 | Canonical project rules + root AGENTS propagation | 10.0 | Applies across repository-driven chats, agents and automation |
| 2 | Project rule only | 8.2 | Canonical but easier for a new agent to miss |
| 3 | AGENTS only | 6.8 | Visible but less authoritative |
| 4 | Chat-memory convention | 4.0 | Not reliable across tools/runs |
| 5 | Manual reminder each time | 2.0 | Easy omission |

Selected: option 1.

## Common failure modes avoided

- Frozen test remains the active build destination.
- Historical regression test inspects current evolving source.
- Config-only repair accidentally publishes a new numbered customer test.
- Backend work disables urgent customer UI iteration.
- Terminology policy exists only in one chat.
- Dependent fixes land as multiple broken intermediate commits.

## External references

- https://docs.github.com/en/code-security/concepts/supply-chain-security/immutable-releases
- https://docs.github.com/en/actions/reference/workflows-and-actions/variables
- https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax
- https://laravel.com/framework/docs/deployment

## E. Historical contract sweep after first CI feedback

The first repaired CI run exposed the same anti-pattern in Test 20, 21, 23, 24 and 25 contracts: historical tests enumerated the current build destination and browser-state namespace up to Test 26. These assertions were replaced with release-invariant checks and minimum-version namespace matching, so future Test 27+ source evolution does not require editing every historical contract.
