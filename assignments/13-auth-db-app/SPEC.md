# 13 Auth DB App Specification

Source: Базы данных.docx

## Assignment goal
Build registration, login, logout, and authenticated user display with database persistence and clear status messages.

## Scope floor
- Registration form.
- Login form.
- Save a registered user to the database.
- Enforce email uniqueness.
- Show user info after login.
- Logout.
- Show clear success and error state messages.

## Deliverable shape
- Browser-oriented assignment with `public/` entrypoint.
- Dedicated Supabase PostgreSQL database for this assignment only.
- Auto-initialization of database schema on first connection.
- Reset and bootstrap assets for repeatable verification.

## Validations and fixed defaults
- Fixed schema: `users(id, full_name, email, password_hash, created_at)`.
- Password storage uses `password_hash` and `password_verify`.
- The implementation must enforce email uniqueness at the application and database level.
- The spec explicitly requires email uniqueness handling and duplicate-email rejection.

## Forbidden overreach
- Do not store plain-text passwords.
- Do not allow duplicate email registration.
- Do not share database or session state with other assignments.
- Do not introduce frameworks or ORMs unless the brief explicitly requires them.

## Verification entrypoints
- `bash scripts/serve-assignment.sh 13-auth-db-app 8095`
- `bash scripts/reset-auth-db.sh`
- `php -l assignments/13-auth-db-app/public/index.php`
