# Fix Assignment 13 Auth DB App on Vercel Production

## TL;DR
> **Summary**: Restore assignment 13 so the Vercel production deployment can register, log in, persist auth state, and log out against MySQL when `AUTH_DB_*` is configured, while failing clearly and safely when DB config/connectivity is broken.
> **Deliverables**:
> - Vercel production env/secret contract for assignment 13
> - Hardened assignment 13 DB/auth state handling for missing config, DB outage, and stale cookies
> - Expanded local + Vercel smoke coverage for auth happy/failure/edge paths
> **Effort**: Medium
> **Parallel**: YES - 2 waves
> **Critical Path**: 1 → 2 → 3/4 → 5 → 6

## Context
### Original Request
- Fix cloud behavior for assignment `13-auth-db-app`, where the deployed page currently shows:
  - "Конфигурация БД не завершена"
  - guest-state content instead of a working auth flow

### Interview Summary
- Cloud target fixed to **Vercel production**.
- Scope fixed to **env wiring + app hardening**.
- Verification style fixed to **tests-after**.
- Default applied: if Vercel/DB credentials or permissions are unavailable, execution must stop and report rather than invent fallback behavior.

### Metis Review (gaps addressed)
- Explicitly forbid silent degradation into normal guest mode when DB config/connectivity is broken.
- Cover stale-cookie, deleted-user, rotated-secret, duplicate-email, and bad-password flows.
- Treat Vercel production verification as concrete smoke against the deployed URL, not just local parity.
- Avoid scope creep into assignment 11, auth redesign, framework adoption, or generic UI restyling.

## Work Objectives
### Core Objective
Make `assignments/13-auth-db-app` behave correctly on Vercel production with MySQL-backed auth, and make failure states explicit, non-fatal, and non-misleading.

### Deliverables
- Verified Vercel production env contract for `AUTH_DB_HOST`, `AUTH_DB_PORT`, `AUTH_DB_USER`, `AUTH_DB_PASSWORD`, `AUTH_DB_NAME`, `ASSIGNMENT13_AUTH_SECRET`
- Assignment 13 auth flow that works end-to-end on Vercel prod when env/DB is valid
- Deterministic error-state contract for missing DB config / DB connection failure / stale cookie paths
- Updated smoke coverage for local and deployed auth flows, including failure-path checks
- Corrected deployment documentation so the repo documents the same runtime env contract that PHP actually reads in production
- Evidence artifacts under `.sisyphus/evidence/`

### Definition of Done (verifiable conditions with commands)
- `php -l assignments/13-auth-db-app/src/bootstrap.php`
- `php -l assignments/13-auth-db-app/src/db.php`
- `php -l assignments/13-auth-db-app/src/auth.php`
- `php -l assignments/13-auth-db-app/public/index.php`
- `php -l assignments/13-auth-db-app/public/register.php`
- `php -l assignments/13-auth-db-app/public/login.php`
- `php -l assignments/13-auth-db-app/public/logout.php`
- `bash scripts/reset-auth-db.sh`
- `bash scripts/run-db-smoke.sh`
- `bash scripts/run-web-smoke.sh`
- `bash scripts/run-vercel-smoke.sh "$DEPLOY_URL" --with-db`

### Must Have
- No hardcoded DB credentials or auth secrets in source
- `ASSIGNMENT13_AUTH_SECRET` must be present in Vercel Production; production must not rely on `assignment13-local-dev-secret`
- Deployment docs must describe the actual runtime env names consumed by PHP (`AUTH_DB_*`, `ASSIGNMENT13_AUTH_SECRET`), not only legacy/lowercase secret examples
- Clear distinction between: fully configured + working, config missing, DB unavailable, invalid credentials, duplicate email
- DB-unavailable and stale/bogus-cookie paths must never leak a PHP stack trace or return HTTP 500 to the user
- `assignment13_auth` cookie remains assignment-scoped and clears safely on logout / stale-session cleanup
- Duplicate-email rejection remains enforced in both application logic and SQL schema
- Production deployment no longer shows the current misleading state when env is actually configured correctly

### Must NOT Have (guardrails, AI slop patterns, scope boundaries)
- Must NOT silently present normal guest mode as if the app were healthy when DB init fails
- Must NOT use the local fallback auth secret in production
- Must NOT modify assignment 11 unless a concrete collision is proven
- Must NOT replace MySQL with another store or introduce frameworks / ORMs
- Must NOT redesign the whole page layout; limit UI changes to status/error-state clarity
- Must NOT move assignment 13 business logic outside `assignments/13-auth-db-app/`
- Must NOT weaken SQL uniqueness / FK guarantees in `database/13-auth-db-app/schema.sql`
- Must NOT leave Vercel wrapper markup in an invalid state where injected header/style output precedes the assignment document structure

## Verification Strategy
> ZERO HUMAN INTERVENTION - all verification is agent-executed.
- Test decision: **tests-after** using existing PHP lint, DB reset/smoke, local web smoke, and Vercel smoke scripts
- QA policy: Every task includes agent-executed happy + failure/edge scenarios
- Evidence: `.sisyphus/evidence/task-{N}-{slug}.{ext}`

## Execution Strategy
### Parallel Execution Waves
> Target: 5-8 tasks per wave. <3 per wave (except final) = under-splitting.
> Extract shared dependencies as Wave-1 tasks for max parallelism.

Wave 1: 1) Vercel env contract and deployment stop-conditions, 2) DB availability/error contract, 3) auth session/stale-cookie hardening

Wave 2: 4) home/status rendering contract, 5) register/login failure-state contract, 6) smoke/evidence coverage expansion

### Dependency Matrix (full, all tasks)
- 1 blocks 6 and informs 2-5
- 2 blocks 3-5
- 3 blocks 4-6
- 4 blocks 6
- 5 blocks 6
- 6 depends on 1-5

### Agent Dispatch Summary (wave → task count → categories)
- Wave 1 → 3 tasks → `unspecified-high`, `deep`, `deep`
- Wave 2 → 3 tasks → `deep`, `deep`, `unspecified-high`
- Final Verification → 4 tasks → `oracle`, `unspecified-high`, `unspecified-high`, `deep`

## TODOs
> Implementation + Test = ONE task. Never separate.
> EVERY task MUST have: Agent Profile + Parallelization + QA Scenarios.

- [ ] 1. Verify and provision the Vercel production env contract for assignment 13

  **What to do**: Use the Vercel CLI to inspect the **production** environment for runtime variables named exactly `AUTH_DB_HOST`, `AUTH_DB_PORT`, `AUTH_DB_USER`, `AUTH_DB_PASSWORD`, `AUTH_DB_NAME`, and `ASSIGNMENT13_AUTH_SECRET`. If any are missing, add the missing **runtime env vars** in Production using secure values from the authorized secret source available to the executor; do not rely on legacy secret names alone. Treat missing `ASSIGNMENT13_AUTH_SECRET` as a production blocker even if the app appears to run via the local fallback secret. Update the deployment docs (`README.md` and any related examples) so they describe the actual uppercase runtime env contract and explicitly distinguish between Vercel secrets as storage and Vercel env vars as runtime exposure. Trigger a fresh production deployment after env correction; capture a **sanitized** env inventory (names/scope only, never values), deployment URL, and first-pass smoke output. If Vercel access, deployment permissions, or DB access are unavailable, stop and report instead of inventing fallback behavior.
  **Must NOT do**: Do not commit secrets, do not save secret values under `.sisyphus/evidence/`, do not change Preview/Development env unless production parity work is explicitly required by the command you run, and do not change routing away from the existing `/13-auth-db-app` mount.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: external-system operational work across Vercel env inventory, deployment, and evidence capture.
  - Skills: `[]` - Reason: no additional skill is required beyond shell/Vercel workflow execution.
  - Omitted: `[/playwright]` - Reason: browser automation is unnecessary for the env inventory/provisioning step.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [6] | Blocked By: []

  **References** (executor has NO interview context - be exhaustive):
  - Runtime route contract: `vercel.json:1-14` - all requests route through `api/index.php` on Vercel.
  - Assignment mount: `api/assignments.php:69-74` - assignment 13 is mounted as `13-auth-db-app` using `public_php_files`.
  - Assignment context/header: `api/assignments.php:105-116` - dispatcher exposes assignment context and wraps mounted output.
  - Home-header behavior reported by user: `api/assignments.php:119-194` - current Vercel wrapper renders the "На главную" navigation the user pasted.
  - Required env names: `assignments/13-auth-db-app/src/db.php:4-55` - runtime reads exactly `AUTH_DB_HOST`, `AUTH_DB_PORT`, `AUTH_DB_USER`, `AUTH_DB_PASSWORD`, `AUTH_DB_NAME`.
  - Local/prod env examples: `.env.vercel.local.example:3-14` - assignment 13 also needs `ASSIGNMENT13_AUTH_SECRET`.
  - Production secret runbook: `README.md:69-77` - historical Vercel secret examples; normalize these into uppercase runtime env vars consumed by PHP.
  - Production deploy/smoke runbook: `README.md:98-123` - deploy URL extraction and smoke entrypoint.

  **Acceptance Criteria** (agent-executable only):
  - [ ] `vercel env ls | tee .sisyphus/evidence/task-1-vercel-env.txt` lists all six required variable names with **Production** scope and without storing values.
  - [ ] `vercel deploy --prod --yes | tee .sisyphus/evidence/task-1-vercel-deploy.txt` completes, and `DEPLOY_URL=$(python3 scripts/extract_vercel_url.py .sisyphus/evidence/task-1-vercel-deploy.txt)` resolves a non-empty production URL.
  - [ ] `curl -fsSL "$DEPLOY_URL/13-auth-db-app" | tee .sisyphus/evidence/task-1-vercel-auth-root.html >/dev/null` no longer contains `Конфигурация БД не завершена`.
  - [ ] Production verification notes explicitly confirm `ASSIGNMENT13_AUTH_SECRET` exists as a runtime env var and the app is not relying on `assignment13-local-dev-secret`.
  - [ ] `grep -q 'AUTH_DB_HOST' README.md && grep -q 'ASSIGNMENT13_AUTH_SECRET' README.md` succeeds after the docs are corrected to the runtime env contract.
  - [ ] `bash scripts/run-vercel-smoke.sh "$DEPLOY_URL" | tee .sisyphus/evidence/task-1-vercel-smoke.txt` exits `0`.

  **QA Scenarios** (MANDATORY - task incomplete without these):
  ```
  Scenario: Production env inventory and deploy smoke succeed
    Tool: Bash
    Steps: authenticate Vercel CLI; run `vercel env ls | tee .sisyphus/evidence/task-1-vercel-env.txt`; verify the six required names have Production scope; add any missing runtime env vars in Production from the authorized secret source; update `README.md` so it documents runtime env exposure correctly; run `vercel deploy --prod --yes | tee .sisyphus/evidence/task-1-vercel-deploy.txt`; extract `DEPLOY_URL` with `python3 scripts/extract_vercel_url.py`; request `curl -fsSL "$DEPLOY_URL/13-auth-db-app" | tee .sisyphus/evidence/task-1-vercel-auth-root.html >/dev/null`; run `bash scripts/run-vercel-smoke.sh "$DEPLOY_URL" | tee .sisyphus/evidence/task-1-vercel-smoke.txt`.
    Expected: required runtime env names exist with Production scope, the docs match the runtime contract, the auth root no longer shows the missing-config banner, a production URL is emitted, and first-pass Vercel smoke succeeds.
    Evidence: .sisyphus/evidence/task-1-vercel-smoke.txt

  Scenario: Required env or Vercel access is missing
    Tool: Bash
    Steps: run `vercel env ls` before any code change; if one of the six required runtime variable names is absent from Production scope or the CLI returns an auth/permission error, write the raw CLI output to `.sisyphus/evidence/task-1-vercel-env-error.txt` and stop execution immediately.
    Expected: execution halts with a concrete missing-var or permission report; no secret values are written; no code workaround is attempted.
    Evidence: .sisyphus/evidence/task-1-vercel-env-error.txt
  ```

  **Commit**: NO | Message: `n/a` | Files: []

- [ ] 2. Introduce an explicit DB availability contract in `src/db.php`

  **What to do**: Keep `auth_db_config()` in place, preserve the existing empty-password exception for `AUTH_DB_PASSWORD`, and add a new helper named **`auth_db_status()`** in `assignments/13-auth-db-app/src/db.php`. Its return shape must be exactly `['configured' => bool, 'missing' => string[], 'available' => bool, 'reason' => 'ok'|'config_missing'|'connection_failed', 'message' => ?string]`. `auth_db_status()` must attempt a PDO connection only when configuration is complete, cache the result for the current request, and return a **user-safe Russian message** instead of leaking DSN/credential details. `auth_db_connection()` must delegate to `auth_db_status()` and throw `RuntimeException($status['message'])` whenever `available === false`. In the same task, harden `assignment13_auth_secret()` so Production/Vercel runtime treats missing `ASSIGNMENT13_AUTH_SECRET` as a blocking configuration error instead of silently using `assignment13-local-dev-secret`; preserve the fallback secret only for explicit local non-production execution if the implementer can prove that behavior is still needed.
  **Must NOT do**: Do not hardcode credentials, do not return a partially initialized `PDO`, do not expose raw driver errors to users, do not call `exit`/`die`, and do not remove the current `AUTH_DB_PASSWORD` empty-string allowance.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: this sets the core runtime contract that all auth pages and failure paths will consume.
  - Skills: `[]` - Reason: the work is localized PHP runtime design, not tool-specific workflow.
  - Omitted: `[/playwright]` - Reason: browser automation is not needed to define the DB helper contract.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [3, 4, 5] | Blocked By: []

  **References** (executor has NO interview context - be exhaustive):
  - Current env parsing and PDO creation: `assignments/13-auth-db-app/src/db.php:4-55` - existing config/missing detection and exception throw point.
  - Current landing-page usage: `assignments/13-auth-db-app/public/index.php:7-16` - page currently reads config directly and surfaces missing vars.
  - Current register failure handling: `assignments/13-auth-db-app/public/register.php:11-26` - page distinguishes `RuntimeException` from generic throwable.
  - Current login failure handling: `assignments/13-auth-db-app/public/login.php:10-30` - same pattern as registration.
  - Shell env contract: `scripts/reset-auth-db.sh:8-29` - all five `AUTH_DB_*` variables are required by reset automation.
  - Shell DB connectivity diagnostics: `scripts/reset-auth-db.sh:44-78` - current repo convention is actionable DB error output without credential leakage.
  - Assignment conventions: `assignments/13-auth-db-app/AGENTS.md:18-34` - runtime config must stay env-driven, and duplicate-user messaging must remain user-facing.

  **Acceptance Criteria** (agent-executable only):
  - [ ] `php -l assignments/13-auth-db-app/src/db.php`
  - [ ] `env -u AUTH_DB_HOST -u AUTH_DB_PORT -u AUTH_DB_USER -u AUTH_DB_PASSWORD -u AUTH_DB_NAME php -r 'require "assignments/13-auth-db-app/src/db.php"; $s = auth_db_status(); echo $s["reason"], "\n", (int) $s["configured"], "\n", (int) $s["available"], "\n";' | tee .sisyphus/evidence/task-2-db-status-missing.txt` outputs `config_missing`, `0`, `0` in that order.
  - [ ] `AUTH_DB_HOST=127.0.0.1 AUTH_DB_PORT=1 AUTH_DB_USER=test AUTH_DB_PASSWORD=test AUTH_DB_NAME=test php -r 'require "assignments/13-auth-db-app/src/db.php"; $s = auth_db_status(); echo $s["reason"], "\n", (int) $s["configured"], "\n", (int) $s["available"], "\n";' | tee .sisyphus/evidence/task-2-db-status-connection.txt` outputs `connection_failed`, `1`, `0` in that order.

  **QA Scenarios** (MANDATORY - task incomplete without these):
  ```
  Scenario: DB status reports healthy state when env and DB are valid
    Tool: Bash
    Steps: export valid local `AUTH_DB_*`; run `bash scripts/reset-auth-db.sh`; then run `php -r 'require "assignments/13-auth-db-app/src/db.php"; $s = auth_db_status(); echo $s["reason"], "\n", (int) $s["configured"], "\n", (int) $s["available"], "\n";' | tee .sisyphus/evidence/task-2-db-status-ok.txt`.
    Expected: output is `ok`, `1`, `1`; no raw PDO/driver error text is emitted.
    Evidence: .sisyphus/evidence/task-2-db-status-ok.txt

  Scenario: Connection failure is explicit and non-fatal
    Tool: Bash
    Steps: run `AUTH_DB_HOST=127.0.0.1 AUTH_DB_PORT=1 AUTH_DB_USER=test AUTH_DB_PASSWORD=test AUTH_DB_NAME=test php -r 'require "assignments/13-auth-db-app/src/db.php"; $s = auth_db_status(); echo $s["reason"], "\n", $s["message"], "\n";' | tee .sisyphus/evidence/task-2-db-status-connection.txt`.
    Expected: `reason` is `connection_failed`; the message is user-safe Russian text; the process exits normally without a PHP fatal.
    Evidence: .sisyphus/evidence/task-2-db-status-connection.txt
  ```

  **Commit**: NO | Message: `n/a` | Files: []

- [ ] 3. Harden auth session resolution and logout for stale-cookie and DB-unavailable paths

  **What to do**: Update `current_auth_user()` and `logout_current_user()` in `assignments/13-auth-db-app/src/auth.php` to use `auth_db_status()` before touching the database. The contract is fixed: (1) no cookie => return `null` without a DB call; (2) cookie + `available === false` => `clear_auth_cookie()` and return `null`; (3) cookie + lookup miss (expired session, deleted user row, bogus token, rotated secret hash mismatch) => `clear_auth_cookie()` and return `null`; (4) logout must **always** clear the cookie, and must delete the DB session row only when `available === true`. Keep token generation, HMAC hashing, cookie name/path/max-age, and 7-day session lifetime unchanged. On both `/13-auth-db-app` and `/13-auth-db-app/logout.php`, stale/bogus cookies during DB misconfiguration/outage must return a non-fatal response with no stack trace and no HTTP 500.
  **Must NOT do**: Do not rename `ASSIGNMENT13_AUTH_COOKIE`, do not change cookie path/scoping rules, do not change password hashing behavior, do not introduce `$_SESSION`, and do not throw when logout is called during DB outage or missing config.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: correctness depends on careful interaction between cookie state, DB availability, and existing auth/session helpers.
  - Skills: `[]` - Reason: no extra skill is required beyond disciplined PHP changes.
  - Omitted: `[/playwright]` - Reason: command-line HTTP checks are sufficient for this hardening task.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [4, 6] | Blocked By: [2]

  **References** (executor has NO interview context - be exhaustive):
  - Cookie contract: `assignments/13-auth-db-app/src/bootstrap.php:4-6` - cookie name and lifetime are fixed.
  - Cookie set/clear implementation: `assignments/13-auth-db-app/src/bootstrap.php:79-99` - preserve current path, secure, httponly, and samesite behavior.
  - Auth secret + token hashing: `assignments/13-auth-db-app/src/auth.php:6-24` - keep current HMAC token model.
  - Current session resolution/logout logic: `assignments/13-auth-db-app/src/auth.php:94-166` - these are the exact functions to harden.
  - Landing page call site: `assignments/13-auth-db-app/public/index.php:7-26` - page currently calls `current_auth_user()` at top-level.
  - DB session schema: `database/13-auth-db-app/schema.sql:16-27` - `user_sessions` rows cascade with `users` deletion and use unique `token_hash`.

  **Acceptance Criteria** (agent-executable only):
  - [ ] `php -l assignments/13-auth-db-app/src/auth.php`
  - [ ] With `AUTH_DB_*` unset and a stale cookie header, requesting `/logout.php` returns `303` and does not fatal: `bash scripts/serve-assignment.sh 13-auth-db-app 8095 >/tmp/task-3-server.log 2>&1 & SERVER_PID=$!; sleep 1; curl -i -sS -H 'Cookie: assignment13_auth=stale-token' http://127.0.0.1:8095/logout.php | tee .sisyphus/evidence/task-3-logout-missing-db.txt; kill $SERVER_PID; wait $SERVER_PID 2>/dev/null || true`.
  - [ ] With a stale cookie header on `/`, the response stays non-fatal and the page no longer crashes when DB is unavailable: `bash scripts/serve-assignment.sh 13-auth-db-app 8095 >/tmp/task-3-home.log 2>&1 & SERVER_PID=$!; sleep 1; curl -i -sS -H 'Cookie: assignment13_auth=stale-token' http://127.0.0.1:8095/ | tee .sisyphus/evidence/task-3-stale-cookie-home.txt; kill $SERVER_PID; wait $SERVER_PID 2>/dev/null || true`.
  - [ ] On the deployed route, `curl -i -sS -H 'Cookie: assignment13_auth=stale-token' "$DEPLOY_URL/13-auth-db-app" | tee .sisyphus/evidence/task-3-vercel-stale-cookie.txt` returns HTTP `200` and does not contain `Fatal error`, `Stack trace`, or `Uncaught RuntimeException`.
  - [ ] On the deployed route, `curl -i -sS -H 'Cookie: assignment13_auth=stale-token' "$DEPLOY_URL/13-auth-db-app/logout.php" | tee .sisyphus/evidence/task-3-vercel-logout-cookie.txt` returns a non-500 response, clears the cookie path safely, and does not contain `Fatal error`, `Stack trace`, or `Uncaught RuntimeException`.

  **QA Scenarios** (MANDATORY - task incomplete without these):
  ```
  Scenario: Logout clears auth state during healthy DB-backed session flow
    Tool: Bash
    Steps: export valid local `AUTH_DB_*`; run `bash scripts/reset-auth-db.sh`; start `bash scripts/serve-assignment.sh 13-auth-db-app 8095`; use a cookie jar to register and log in a fresh user; request `/logout.php`; then request `/` again with the same cookie jar.
    Expected: logout returns a redirect, the auth cookie is cleared, and the follow-up `/` request is unauthenticated without any PHP fatal.
    Evidence: .sisyphus/evidence/task-3-logout-healthy.txt

  Scenario: Stale cookie during missing DB config is handled safely
    Tool: Bash
    Steps: leave `AUTH_DB_*` unset; start `bash scripts/serve-assignment.sh 13-auth-db-app 8095`; request `/` and `/logout.php` with `Cookie: assignment13_auth=stale-token`; capture both responses.
    Expected: both endpoints remain non-fatal; stale cookie is cleared on the server response path; no database exception is dumped to the page.
    Evidence: .sisyphus/evidence/task-3-stale-cookie-home.txt

  Scenario: Deployed stale-cookie paths do not leak stack traces
    Tool: Bash
    Steps: after production deploy, run `curl -i -sS -H 'Cookie: assignment13_auth=stale-token' "$DEPLOY_URL/13-auth-db-app"` and `curl -i -sS -H 'Cookie: assignment13_auth=stale-token' "$DEPLOY_URL/13-auth-db-app/logout.php"`; capture both responses.
    Expected: neither response returns HTTP 500 or contains `Fatal error`, `Stack trace`, or `Uncaught RuntimeException`; the app degrades safely instead of crashing.
    Evidence: .sisyphus/evidence/task-3-vercel-stale-cookie.txt
  ```

  **Commit**: NO | Message: `n/a` | Files: []

- [ ] 4. Make the assignment 13 home page render explicit healthy vs unavailable states

  **What to do**: Add a shared helper named **`assignment13_db_notice_html(array $dbStatus): string`** in `assignments/13-auth-db-app/src/bootstrap.php` and reuse it on the landing page. In `public/index.php`, resolve `$dbStatus = auth_db_status()` before page assembly, then resolve `$user = current_auth_user()` so stale-cookie cleanup still happens safely. Render three mutually exclusive states only: (1) authenticated user section when `$dbStatus['available'] === true && $user !== null`; (2) healthy guest section when `$dbStatus['available'] === true && $user === null`; (3) unavailable state when `$dbStatus['available'] === false`. For `config_missing`, preserve the existing heading `Конфигурация БД не завершена` and the missing-variable list; for `connection_failed`, render the heading `База данных временно недоступна` plus the user-safe message from `auth_db_status()`. In **both** unavailable cases, do **not** render the normal guest copy `Вы ещё не вошли в систему. Используйте регистрацию или вход.`
  **Must NOT do**: Do not remove the existing nav links, do not change the Vercel wrapper header, do not change the authenticated user fields shown on success, and do not present an unavailable app as a healthy guest page.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: page-state correctness depends on coordinating the new DB contract with auth/session behavior and existing layout helpers.
  - Skills: `[]` - Reason: this is localized PHP/HTML state rendering work.
  - Omitted: `[/frontend-ui-ux, /playwright]` - Reason: layout redesign and browser automation are unnecessary for this bounded rendering contract.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [6] | Blocked By: [2, 3]

  **References** (executor has NO interview context - be exhaustive):
  - Current layout/nav helpers: `assignments/13-auth-db-app/src/bootstrap.php:102-167` - add the shared DB notice helper here and preserve the existing navigation/status rendering.
  - Cookie/path/layout contract: `assignments/13-auth-db-app/src/bootstrap.php:13-99` - maintain base-path-aware URLs and cookie scoping.
  - Current landing-page logic: `assignments/13-auth-db-app/public/index.php:7-26` - this is the exact branch structure to replace.
  - DB status source: `assignments/13-auth-db-app/src/db.php:4-55` - new `auth_db_status()` contract from task 2 belongs here.
  - Auth session resolution: `assignments/13-auth-db-app/src/auth.php:94-166` - stale-cookie cleanup must remain compatible with home-page rendering.
  - User-visible target behavior: `assignments/13-auth-db-app/SPEC.md:5-16` - assignment still must show clear success and error state messages.

  **Acceptance Criteria** (agent-executable only):
  - [ ] `php -l assignments/13-auth-db-app/src/bootstrap.php && php -l assignments/13-auth-db-app/public/index.php`
  - [ ] With `AUTH_DB_*` unset, `bash scripts/serve-assignment.sh 13-auth-db-app 8095 >/tmp/task-4-missing.log 2>&1 & SERVER_PID=$!; sleep 1; curl -sS http://127.0.0.1:8095/ | tee .sisyphus/evidence/task-4-home-missing.html; kill $SERVER_PID; wait $SERVER_PID 2>/dev/null || true` produces HTML containing `Конфигурация БД не завершена` and **not** containing `Вы ещё не вошли в систему`.
  - [ ] With valid local `AUTH_DB_*` and a reset DB, `bash scripts/reset-auth-db.sh && bash scripts/serve-assignment.sh 13-auth-db-app 8095 >/tmp/task-4-healthy.log 2>&1 & SERVER_PID=$!; sleep 1; curl -sS http://127.0.0.1:8095/ | tee .sisyphus/evidence/task-4-home-healthy.html; kill $SERVER_PID; wait $SERVER_PID 2>/dev/null || true` produces HTML containing `Гость` and not containing the DB-unavailable headings.

  **QA Scenarios** (MANDATORY - task incomplete without these):
  ```
  Scenario: Healthy no-cookie landing page renders the guest state only when DB is available
    Tool: Bash
    Steps: export valid local `AUTH_DB_*`; run `bash scripts/reset-auth-db.sh`; start `bash scripts/serve-assignment.sh 13-auth-db-app 8095`; request `/`; stop the server.
    Expected: response contains `Гость` and `Вы ещё не вошли в систему`, and contains neither `Конфигурация БД не завершена` nor `База данных временно недоступна`.
    Evidence: .sisyphus/evidence/task-4-home-healthy.html

  Scenario: Missing config or broken DB never renders a healthy guest page
    Tool: Bash
    Steps: first, leave `AUTH_DB_*` unset and request `/`; second, run the same server with unreachable DB env such as `AUTH_DB_PORT=1` and request `/` again; capture both outputs.
    Expected: the missing-config response contains the existing config heading; the unreachable-DB response contains `База данных временно недоступна`; neither response contains the healthy guest copy.
    Evidence: .sisyphus/evidence/task-4-home-missing.html
  ```

  **Commit**: NO | Message: `n/a` | Files: []

- [ ] 5. Make register/login pages fail clearly and deterministically when DB is unavailable

  **What to do**: Update `public/register.php` and `public/login.php` to resolve `$dbStatus = auth_db_status()` before POST handling and to reuse `assignment13_db_notice_html($dbStatus)`. When `$dbStatus['available'] === false`, the pages must render the DB notice, keep the page reachable with HTTP 200, **skip all DB calls**, and render the form controls in a disabled state (`disabled` on each input and submit button). When the DB is available, preserve the current validation rules, duplicate-email behavior, bad-password behavior, and success redirects. Duplicate email must still surface `Пользователь с таким email уже существует.`; invalid login must still surface `Неверный email или пароль.`
  **Must NOT do**: Do not remove form fields from the page, do not change the redirect targets, do not swallow duplicate-email or bad-password messages into a generic DB message, and do not allow a POST to hit the database when `available === false`.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: this task must preserve existing auth semantics while introducing a strict unavailability contract across two public endpoints.
  - Skills: `[]` - Reason: no extra skill is necessary.
  - Omitted: `[/playwright]` - Reason: curl-based form checks are sufficient for this endpoint-level work.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [6] | Blocked By: [2]

  **References** (executor has NO interview context - be exhaustive):
  - Register endpoint: `assignments/13-auth-db-app/public/register.php:11-45` - existing POST/redirect/error handling to preserve when DB is healthy.
  - Login endpoint: `assignments/13-auth-db-app/public/login.php:10-48` - existing validation and redirect contract.
  - Shared layout/status rendering: `assignments/13-auth-db-app/src/bootstrap.php:102-167` - page-level notices/flash sections already live here.
  - DB availability contract: `assignments/13-auth-db-app/src/db.php:4-55` - pages must consult `auth_db_status()` before attempting DB work.
  - Auth behavior/messages: `assignments/13-auth-db-app/src/auth.php:26-92` - keep the current duplicate-email and invalid-credential semantics intact.
  - Assignment requirements: `assignments/13-auth-db-app/SPEC.md:8-31` - clear success/error messages and duplicate-email rejection are mandatory.

  **Acceptance Criteria** (agent-executable only):
  - [ ] `php -l assignments/13-auth-db-app/public/register.php && php -l assignments/13-auth-db-app/public/login.php`
  - [ ] With `AUTH_DB_*` unset, posting to register does not redirect and shows the unavailable/config message with disabled controls: `bash scripts/serve-assignment.sh 13-auth-db-app 8095 >/tmp/task-5-register.log 2>&1 & SERVER_PID=$!; sleep 1; curl -sS -X POST -d 'full_name=Ivan Petrov&email=ivan@example.com&password=password123' http://127.0.0.1:8095/register.php | tee .sisyphus/evidence/task-5-register-unavailable.html; kill $SERVER_PID; wait $SERVER_PID 2>/dev/null || true`.
  - [ ] With valid local `AUTH_DB_*` and a reset DB, duplicate email and bad password stay deterministic: first register once, then re-register the same email and submit a bad-password login; capture both responses to `.sisyphus/evidence/task-5-duplicate.html` and `.sisyphus/evidence/task-5-bad-password.html`.

  **QA Scenarios** (MANDATORY - task incomplete without these):
  ```
  Scenario: Healthy DB-backed register → login flow still works
    Tool: Bash
    Steps: export valid local `AUTH_DB_*`; run `bash scripts/reset-auth-db.sh`; start `bash scripts/serve-assignment.sh 13-auth-db-app 8095`; submit a fresh registration; submit a matching login; capture the responses and stop the server.
    Expected: registration redirects to `login.php?status=registered`; login redirects to `/?status=logged-in`; the follow-up home page shows the persisted user info.
    Evidence: .sisyphus/evidence/task-5-register-login-happy.txt

  Scenario: Unavailable DB blocks POST logic without hiding the form
    Tool: Bash
    Steps: leave `AUTH_DB_*` unset; start `bash scripts/serve-assignment.sh 13-auth-db-app 8095`; POST to `/register.php` and `/login.php`; capture both responses.
    Expected: both responses are HTTP 200 pages with the DB notice visible, the form controls disabled, and no redirect or PHP fatal.
    Evidence: .sisyphus/evidence/task-5-register-unavailable.html

  Scenario: Duplicate email and bad password remain explicit failures
    Tool: Bash
    Steps: with valid local `AUTH_DB_*`, register `test@example.com` once; attempt the same email again; then attempt login with `test@example.com` and a wrong password.
    Expected: the second registration shows `Пользователь с таким email уже существует.`; the bad-password login shows `Неверный email или пароль.`; neither failure mutates the healthy auth/session contract.
    Evidence: .sisyphus/evidence/task-5-duplicate.html
  ```

  **Commit**: YES | Message: `fix(assignment-13): harden auth db availability and session flows` | Files: [`assignments/13-auth-db-app/src/bootstrap.php`, `assignments/13-auth-db-app/src/db.php`, `assignments/13-auth-db-app/src/auth.php`, `assignments/13-auth-db-app/public/index.php`, `assignments/13-auth-db-app/public/register.php`, `assignments/13-auth-db-app/public/login.php`, `assignments/13-auth-db-app/public/logout.php`]

- [ ] 6. Expand local and Vercel smoke coverage for assignment 13 happy/failure/edge flows

  **What to do**: Update `scripts/run-web-smoke.sh` so assignment 13's local non-DB coverage asserts the new **unavailable-state contract**: `/` must show the DB notice when env is missing and must not show the healthy guest copy; `/register.php` and `/login.php` must stay reachable and expose disabled controls while unavailable. Update `scripts/run-vercel-smoke.sh` so `--with-db` performs the full deployed auth flow on `/13-auth-db-app`: register a timestamped user, log in, confirm the authenticated landing page, log out, reject duplicate email, reject bad password, and verify both bogus/stale cookie requests to `/13-auth-db-app` and `/13-auth-db-app/logout.php` do not fatal, do not return HTTP 500, and do not leak stack traces. Also add a deployed assertion that assignment pages do not emit invalid wrapper markup before the document structure; the Vercel home header must be integrated without prepending raw `<style>`/`<nav>` before the assignment HTML document. Keep non-DB Vercel smoke as route/header coverage only.
  **Must NOT do**: Do not make `bash scripts/run-web-smoke.sh` depend on a live DB, do not remove the `--with-db` opt-in from Vercel smoke, do not commit `.sisyphus/evidence/`, and do not embed assignment business logic into generic helpers beyond bounded smoke assertions.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: this is cross-script orchestration work spanning local smoke, Vercel smoke, and evidence plumbing.
  - Skills: `[]` - Reason: shell/curl coverage is sufficient.
  - Omitted: `[/playwright]` - Reason: the required checks are deterministic curl/header/cookie flows.

  **Parallelization**: Can Parallel: NO | Wave 2 | Blocks: [] | Blocked By: [1, 2, 3, 4, 5]

  **References** (executor has NO interview context - be exhaustive):
  - Current local assignment-13 smoke gap: `scripts/run-web-smoke.sh:136-149` - only root/register/login reachability is covered today.
  - Local smoke orchestration: `scripts/run-web-smoke.sh:156-230` - preserve the existing serve/wait/run pattern.
  - Current Vercel mounted assignment-13 checks: `scripts/run-vercel-smoke.sh:168-227` - `--with-db` currently covers only a minimal registration/login pass.
  - Vercel local/full smoke runbook: `README.md:87-95` - repo already documents DB-backed Vercel smoke.
  - Production smoke runbook: `README.md:111-123` - production deploy verification goes through `run-vercel-smoke.sh`.
  - Assignment verification notes: `assignments/13-auth-db-app/AGENTS.md:24-29` - duplicate-email failure path is explicitly required.
  - Canonical auth QA baseline: `.sisyphus/plans/all-practical-assignments.md:1014-1053` - end-to-end register/login/logout and duplicate-email rejection are already part of the assignment contract.
  - Evidence naming convention: `.sisyphus/plans/all-practical-assignments.md:316` - evidence files must be named `task-{N}-{slug}.{ext}`.

  **Acceptance Criteria** (agent-executable only):
  - [ ] `bash scripts/run-web-smoke.sh | tee .sisyphus/evidence/task-6-web-smoke.txt` exits `0` and assignment 13 coverage now asserts the unavailable-state contract.
  - [ ] `bash scripts/run-db-smoke.sh | tee .sisyphus/evidence/task-6-db-smoke.txt` exits `0` after the auth hardening changes.
  - [ ] `bash scripts/run-vercel-smoke.sh "$DEPLOY_URL" --with-db | tee .sisyphus/evidence/task-6-vercel-auth-smoke.txt` exits `0` and covers register/login/logout, duplicate-email rejection, bad-password rejection, and bogus-cookie safety.

  **QA Scenarios** (MANDATORY - task incomplete without these):
  ```
  Scenario: Local smoke verifies the unavailable-state contract without requiring a DB
    Tool: Bash
    Steps: ensure `AUTH_DB_*` is unset; run `bash scripts/run-web-smoke.sh | tee .sisyphus/evidence/task-6-web-smoke.txt`.
    Expected: assignment 13 smoke passes while explicitly asserting the config-warning/unavailable contract instead of only route reachability.
    Evidence: .sisyphus/evidence/task-6-web-smoke.txt

  Scenario: Deployed Vercel auth flow passes end to end
    Tool: Bash
    Steps: export or derive `DEPLOY_URL`; run `bash scripts/run-vercel-smoke.sh "$DEPLOY_URL" --with-db | tee .sisyphus/evidence/task-6-vercel-auth-smoke.txt`.
    Expected: smoke proves registration, login, authenticated reload, logout, duplicate-email rejection, and bad-password rejection on the deployed route.
    Evidence: .sisyphus/evidence/task-6-vercel-auth-smoke.txt

  Scenario: Bogus cookie does not break the deployed landing page
    Tool: Bash
    Steps: run `curl -i -sS -H 'Cookie: assignment13_auth=bogus-token' "$DEPLOY_URL/13-auth-db-app" | tee .sisyphus/evidence/task-6-vercel-bogus-cookie.txt` after the DB-backed smoke completes.
    Expected: response stays non-fatal, returns HTTP 200, and does not leave the app in a fake authenticated state.
    Evidence: .sisyphus/evidence/task-6-vercel-bogus-cookie.txt
  ```

  **Commit**: YES | Message: `test(assignment-13): expand auth smoke coverage for cloud and failures` | Files: [`scripts/run-web-smoke.sh`, `scripts/run-vercel-smoke.sh`]

## Final Verification Wave (MANDATORY — after ALL implementation tasks)
> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.
> **Do NOT auto-proceed after verification. Wait for user's explicit approval before marking work complete.**
> **Never mark F1-F4 as checked before getting user's okay.** Rejection or user feedback -> fix -> re-run -> present again -> wait for okay.
- [ ] F1. Plan Compliance Audit — oracle
- [ ] F2. Code Quality Review — unspecified-high
- [ ] F3. Real Manual QA — unspecified-high (+ playwright if UI)
- [ ] F4. Scope Fidelity Check — deep

## Commit Strategy
- Operational step (no repo commit): verify/provision Vercel production env and capture deployment evidence.
- Commit 1: `fix(assignment-13): harden auth db availability and session flows`
- Commit 2: `test(assignment-13): expand auth smoke coverage for cloud and failures`

## Success Criteria
- Vercel production assignment 13 can complete register → login → reload → logout against MySQL.
- Missing env / DB outage paths render explicit error-state messaging and do not masquerade as a healthy guest app.
- Stale cookie / deleted-user / rotated-secret flows clear auth state safely without fatal errors.
- Vercel Production has an explicit `ASSIGNMENT13_AUTH_SECRET` runtime env var, and production does not rely on the local fallback secret.
- Duplicate email and bad password remain deterministic, user-facing failures.
- Assignment pages mounted through Vercel do not prepend invalid wrapper markup ahead of the document structure.
- `README.md` documents the same runtime env names that the PHP app consumes in production.
- Stale/bogus-cookie requests to both the mounted home route and mounted logout route complete without HTTP 500 or stack-trace leakage.
- Local and Vercel smoke coverage both include assignment 13 happy path plus key failure paths, with evidence saved under `.sisyphus/evidence/`.
