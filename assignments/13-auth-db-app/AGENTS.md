# 13-AUTH-DB-APP NOTES

## Overview
- Auth assignment: browser app in this folder, schema/reset assets outside it under `database/13-auth-db-app/` and `scripts/`.

## Where To Look
| Task | Location | Notes |
| --- | --- | --- |
| Shared app bootstrap | `src/bootstrap.php` | Session, flashes, redirects, layout helpers |
| DB configuration | `src/db.php` | Reads `AUTH_DB_*`, creates PDO |
| Auth logic | `src/auth.php` | Validation, uniqueness, registration, login |
| Landing page | `public/index.php` | Logged-in state surface |
| Registration | `public/register.php` | Form + error/success handling |
| Login | `public/login.php` | Auth flow |
| Logout | `public/logout.php` | Session teardown |
| Schema/reset | `../../database/13-auth-db-app/schema.sql`, `../../scripts/reset-auth-db.sh` | External but assignment-owned assets |

## Conventions
- Keep email uniqueness enforced in both application logic and SQL schema.
- Use `password_hash()` / `password_verify()` only; never store plain-text passwords.
- All runtime DB config must come from `AUTH_DB_HOST`, `AUTH_DB_PORT`, `AUTH_DB_USER`, `AUTH_DB_PASSWORD`, `AUTH_DB_NAME`.
- Preserve the split ownership model: PHP app here, schema in `database/`, shared automation in `scripts/`.

## Verification
- Lint changed PHP files with `php -l`.
- Reset DB before auth verification: `bash scripts/reset-auth-db.sh`.
- Run smoke SQL assertions: `bash scripts/run-db-smoke.sh`.
- Serve app and verify login/register/logout pages plus duplicate-email failure path.

## Anti-Patterns
- Hardcoding credentials into source.
- Bypassing schema-level uniqueness.
- Reusing another assignment's DB or session state.
- Catching duplicate-user errors without surfacing a user-facing message.
