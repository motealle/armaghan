# Armaghan repository instructions

Before making any change, read root `AGENTS.md` and `docs/PROJECT-RULES.md`.

The shared Armaghan write lock is mandatory for every mutating task. Use:
- branch `coordination/armaghan-lock`
- file `.armaghan-work-lock.json`

Follow rules 64–74 in `docs/PROJECT-RULES.md` exactly. If the lock is active, ambiguous, unavailable, or a SHA-guarded acquisition conflicts, make no repository/project mutation and report the blocker.

Also preserve all frozen numbered tests and deployment safety rules defined in the project rules.
