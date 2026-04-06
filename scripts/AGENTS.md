# SCRIPTS NOTES

## Overview
- `scripts/` is the only allowed home for shared operational tooling across assignments.

## Where To Look
| Task | Location | Notes |
| --- | --- | --- |
| Workspace lint | `php-lint-all.sh` | Repo-wide `php -l` driver |
| CLI regression pass | `run-cli-assignments.sh` | Executes `01`-`07` local runners |
| Web serving | `serve-assignment.sh` | Validates known slug + port, serves `public/` |
| Web smoke | `run-web-smoke.sh` | Curl-first checks for `08`-`13` |
| DB reset | `reset-auth-db.sh` | Recreates assignment 13 schema from env |
| DB smoke | `run-db-smoke.sh` | SQL assertions after reset |

## Conventions
- Keep scripts assignment-agnostic where possible; never move assignment business logic here.
- Fail fast with actionable messages for missing PHP, MySQL, schema files, or env vars.
- Known browser slugs and fixed smoke ports are deliberate; update both paths when adding coverage.

## Verification
- Shell changes should preserve `set -euo pipefail` behavior.
- Keep output operator-friendly: short `[RUN]/[OK]/[FAIL]/[SKIP]` style messages are the established pattern.
- If a script orchestrates assignment 13, verify both the shell flow and the SQL/app contract it assumes.

## Anti-Patterns
- Embedding assignment runtime/business rules into shared shell scripts.
- Silent failure paths.
- Introducing per-developer local assumptions without validation messages.
- Expanding a one-assignment helper into a pseudo-framework for all assignments.
