# DATABASE NOTES

## Overview
- `database/` exists only for assignment-owned bootstrap assets that should not be mixed into unrelated apps.

## Where To Look
| Task | Location | Notes |
| --- | --- | --- |
| Auth schema | `13-auth-db-app/schema.sql` | Recreated by `scripts/reset-auth-db.sh` |

## Conventions
- Keep DB assets scoped to their owning assignment.
- Use deterministic resettable SQL, not manually mutated state.
- Charset/collation must stay UTF-8-safe (`utf8mb4` in current schema).

## Verification
- Validate schema changes through `bash scripts/reset-auth-db.sh` and `bash scripts/run-db-smoke.sh`.
- Preserve the empty-after-reset expectation for `users`.
- Preserve the unique index on `users.email` because app logic depends on it.

## Anti-Patterns
- Sharing one database schema across multiple assignments.
- Treating this folder as a dump for ad hoc SQL experiments.
- Drifting from the app-side contract in `assignments/13-auth-db-app/src/db.php` and `src/auth.php`.
- Depending on manually pre-seeded data to make the auth app appear correct.
