# AGENTS.md

## Mission
Implement all practical assignments from the root `.docx` briefs as isolated PHP apps under `assignments/`.

## Non-Negotiables
- Do not merge assignments into one app.
- Do not share assignment business logic across folders.
- Shared scripts are allowed only under `scripts/`.
- Use UTF-8 without BOM.
- Prefer `mb_*` functions for Cyrillic-sensitive string operations.
- Preserve Russian learner-facing text when examples/messages are visible to the user.
- No frameworks or ORMs unless the assignment brief explicitly requires them.

## Folder Rules
- Use only the numbered assignment folders defined in `.sisyphus/plans/all-practical-assignments.md`.
- CLI-oriented assignments must expose deterministic output and a `tests/run.php` assertion runner.
- Browser-oriented assignments must use a `public/` entrypoint and have curl/Playwright-verifiable flows.
- DB assignment must include reset/bootstrap assets and never reuse state from other assignments.

## Execution Rules
- Work assignment-by-assignment unless the plan explicitly allows a parallel wave.
- Before implementation, create or update tests/verification for the target assignment.
- Collect evidence under `.sisyphus/evidence/` exactly as the plan specifies.
- Use atomic commits matching the plan’s commit strategy.
- Never skip failure-path checks.

## Verification Rules
- Every task requires setup/reset, happy-path, and failure-path verification.
- Use `php -l` before task completion.
- Use `curl` for HTTP/status/header checks.
- Use Playwright only for flows where browser state materially matters.
- Use SQL assertions for database state.

## Stop Conditions
- Stop and report if a brief contradicts the plan.
- Stop and report if local MySQL is unavailable and Docker is required.
- Stop and report if a task would require shared runtime logic across assignments.
