# AGENTS.md

## Mission
Implement all practical assignments from the root `.docx` briefs as isolated PHP apps under `assignments/`.

## Repo Overview
- Workspace model: educational multi-app PHP repo, not one cumulative application.
- Primary work areas: `assignments/`, `scripts/`, `database/`, `.sisyphus/`.
- Source briefs live at repo root as `.docx`; treat them as read-only inputs.

## Structure
```text
.
├── assignments/          # 13 isolated assignment apps
├── scripts/              # shared run/reset/verification helpers only
├── database/             # DB bootstrap assets for assignment 13
├── .sisyphus/            # plans, evidence, notes
└── *.docx                # original briefs, do not edit
```

## Where To Look
| Task | Location | Notes |
| --- | --- | --- |
| Repo-wide scope/rules | `.sisyphus/plans/all-practical-assignments.md` | Canonical assignment list, fixed defaults, evidence naming |
| CLI assignments | `assignments/01-php-basics` ... `assignments/07-standard-functions` | Each should expose `index.php` + `tests/run.php` |
| Browser assignments | `assignments/08-string-generation` ... `assignments/13-auth-db-app` | `public/` entrypoint is mandatory |
| Session-heavy flows | `assignments/11-sessions/` | Multi-page browser flow + shared bootstrap |
| Auth/DB flow | `assignments/13-auth-db-app/` + `database/13-auth-db-app/` | App code and schema/reset assets are split |
| Shared execution helpers | `scripts/` | Centralized lint, server, smoke, DB reset scripts |
| Evidence | `.sisyphus/evidence/` | Follow plan-prescribed filenames |

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

## Project Conventions
- Runtime default: native PHP CLI + PHP built-in server + local MySQL/MariaDB.
- Internal slugs and filenames stay ASCII English; visible learner-facing text may stay Russian.
- `09`, `11`, and `13` may use local `src/` helpers; simpler browser assignments stay single-entrypoint.
- `10-http-basics/public/index.php` is a single-file router; do not split routes unless the brief requires it.
- `11-sessions` and `13-auth-db-app` rely on bootstrap helpers; preserve `session_start()`-before-output guarantees.
- Assignment 13 DB assets belong under `database/13-auth-db-app/`; shared DB automation belongs under `scripts/`.

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

## Commands
```bash
bash scripts/php-lint-all.sh
bash scripts/run-cli-assignments.sh
bash scripts/run-web-smoke.sh
bash scripts/reset-auth-db.sh
bash scripts/run-db-smoke.sh
bash scripts/serve-assignment.sh 11-sessions 8093
```

## Anti-Patterns (This Repo)
- Editing root `.docx` briefs.
- Moving shared business logic outside the target assignment boundary.
- Browser-only verification for HTTP tasks that require status/header checks.
- Output before `session_start()` in session/auth assignments.
- Hardcoding DB credentials or storing plain-text passwords.
- Reusing DB/session state across assignments.

## Stop Conditions
- Stop and report if a brief contradicts the plan.
- Stop and report if local MySQL is unavailable and Docker is required.
- Stop and report if a task would require shared runtime logic across assignments.
