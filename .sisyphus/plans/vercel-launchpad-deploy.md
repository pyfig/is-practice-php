# Vercel Launchpad Deployment for 13 PHP Assignments

## TL;DR
> **Summary**: Deploy the repo as one Vercel-hosted PHP project with a minimalist launchpad at `/`, short assignment routes like `/01-php-basics`, a persistent home logo in the header, and production-safe routing/state handling for all 13 assignments.
> **Deliverables**:
> - `vercel.json` + thin `api/` dispatcher for Vercel PHP runtime
> - root launchpad grid with 13 assignment cards and shared minimalist visual contract
> - web wrappers for CLI assignments `01-07`
> - base-path-safe refactors for `09`, `10`, `11`, and `13`
> - Vercel smoke automation + deploy evidence
> **Effort**: XL
> **Parallel**: YES - 2 waves
> **Critical Path**: 1 → 2 → 8/9/10 → F1-F4

## Context
### Original Request
- Задеплоить на Vercel.
- На главной показать 13 заданий сеткой как launchpad.
- В хедере сделать лого, чтобы возвращаться на главную.
- Стиль должен быть минималистичным.

### Interview Summary
- Use one Vercel deployment, not 13 separate projects.
- Production routes must be short top-level slugs such as `/01-php-basics`.
- Header uses icon + text logo that always returns to `/`.
- Visual direction follows the spirit of a launchpad grid, without pixel-copying any existing product.
- Full production coverage is required for all 13 assignments.
- CLI assignments `01-07` must open from the launchpad as real web pages.
- Assignment `13` must keep a MySQL-compatible external database.
- Stateful behavior should avoid Redis; `11` and `13` must use cookie/session-token style state appropriate for Vercel.

### Metis Review (gaps addressed)
- Gap fixed: state model is now explicit instead of vague “cookie sessions”.
- Guardrail added: do not rely on native PHP file sessions on Vercel.
- Guardrail added: freeze one base-path contract before touching assignment routes.
- Gap fixed: launchpad cards will include title, one-line description, and type badge (`CLI`, `Web`, `Stateful`) to stay minimal while still informative.
- Guardrail added: verification must cover both `vercel dev` and the deployed production URL.

## Work Objectives
### Core Objective
Turn the current educational multi-assignment PHP repo into one Vercel-deployable project with a root launchpad, preserved assignment isolation, stable mounted routes, and production-safe state/DB behavior.

### Deliverables
- root Vercel runtime contract under `api/` plus `vercel.json`
- launchpad homepage at `/` with 13 cards in a responsive minimalist grid
- consistent assignment header/logo contract across all 13 assignment pages
- new web entrypoints for `01-07`
- mounted-route-compatible behavior for `08-13`
- signed cookie state for `11-sessions`
- DB-backed auth session tokens for `13-auth-db-app`
- smoke automation for local Vercel emulation and deployed production URL

### Definition of Done (verifiable conditions with commands)
- `bash scripts/php-lint-all.sh` exits `0`.
- `bash scripts/run-cli-assignments.sh` exits `0`.
- `bash scripts/run-web-smoke.sh` exits `0`.
- `set -a; source .env.vercel.local; set +a; bash scripts/run-db-smoke.sh` exits `0`.
- `vercel dev --listen 127.0.0.1:4010` starts successfully and `bash scripts/run-vercel-smoke.sh http://127.0.0.1:4010` exits `0`.
- `vercel deploy --prod --yes | tee .sisyphus/evidence/vercel-deploy.txt` succeeds.
- `DEPLOY_URL="$(python3 scripts/extract_vercel_url.py .sisyphus/evidence/vercel-deploy.txt)" && bash scripts/run-vercel-smoke.sh "$DEPLOY_URL"` exits `0`.
- `curl -fsSL "$DEPLOY_URL" | grep -q 'data-launchpad-grid'` succeeds after production deploy.

### Must Have
- one thin Vercel ingress layer only; assignment business logic remains inside each assignment directory
- launchpad root route with 13 stable cards and stable test selectors
- shared header/logo behavior on every assignment page
- canonical short routes for every assignment
- no root-anchored links/forms/redirects inside mounted assignments
- explicit cookie naming, path scoping, and logout invalidation rules
- assignment `13` auth remains backed by MySQL-compatible persistence

### Must NOT Have
- no framework migration
- no Redis / Valkey / external session store
- no shared business logic layer across assignments
- no reliance on `$_SESSION` file storage on Vercel paths
- no route scheme under `/assignments/...`
- no manual “open browser and inspect” acceptance criteria

## Verification Strategy
> ZERO HUMAN INTERVENTION - all verification is agent-executed.
- Test decision: **TDD** for ingress/routing/state slices, with existing repo smoke scripts kept as regressions.
- QA policy: Every task has agent-executed scenarios.
- Evidence: `.sisyphus/evidence/task-{N}-{slug}.{ext}`
- Browser checks use stable selectors introduced by Task 2: `data-home-logo`, `data-launchpad-grid`, `data-assignment-card`, `data-assignment-slug`, `data-assignment-type`, `data-assignment-description`.

## Execution Strategy
### Parallel Execution Waves
> Target: 5-8 tasks per wave. <3 per wave (except final) = under-splitting.
> Extract shared dependencies as Wave-1 tasks for max parallelism.

Wave 1: contract + shell + low-risk route exposure (`1,2,3,4,5`)
Wave 2: mounted-route/stateful refactors + deployment automation (`6,7,8,9,10`)

### Dependency Matrix (full, all tasks)
- 1 blocks 2,3,4,5,6,7,8,9,10
- 2 blocks 3,4,5,6,7,8,9,10
- 3 and 4 are independent once 1 and 2 are complete
- 5 depends on 1 and 2 only
- 6 depends on 1 and 2 only
- 7 depends on 1 and 2 only
- 8 depends on 1 and 2 only
- 9 depends on 1,2 and external DB env contract
- 10 depends on 1-9

### Agent Dispatch Summary (wave → task count → categories)
- Wave 1 → 5 tasks → unspecified-high / visual-engineering / quick
- Wave 2 → 5 tasks → unspecified-high / deep / writing

## TODOs
> Implementation + Test = ONE task. Never separate.
> EVERY task MUST have: Agent Profile + Parallelization + QA Scenarios.

- [x] 1. Create the Vercel ingress contract and mounted-route manifest

  **What to do**: Add `vercel.json` plus a thin root dispatcher under `api/` that owns only two responsibilities: render `/` and dispatch `/{slug}` + `/{slug}/...` to assignment-local entry files. Freeze the canonical route contract now: no trailing slash for assignment root routes, preserve nested child paths without forced slash stripping, return 404 from the dispatcher for unknown slugs, and expose `APP_SLUG`, `APP_BASE_PATH`, and `APP_REQUEST_PATH` to mounted assignments before include/dispatch. In the same foundation task, add `.env.vercel.local.example` documenting `AUTH_DB_*`, `ASSIGNMENT11_STATE_SECRET`, and `ASSIGNMENT13_AUTH_SECRET`.
  **Must NOT do**: Do not move assignment business logic into `api/`; do not introduce a framework router; do not leave route canonicalization implicit.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: root runtime architecture and routing contract are the foundation for every downstream task.
  - Skills: `[]` - no special skill required.
  - Omitted: `["playwright"]` - primary work is routing and contract wiring, not browser behavior.

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: [2,3,4,5,6,7,8,9,10] | Blocked By: []

  **References** (executor has NO interview context - be exhaustive):
  - Official constraint: Vercel PHP uses community runtime `vercel-php` via `api/` + `vercel.json` rewrites.
  - Existing local serving pattern: `scripts/serve-assignment.sh` - current repo serves one assignment at a time and has no unified router.
  - Existing web smoke baseline: `scripts/run-web-smoke.sh`
  - Existing route hotspots to support later: `assignments/09-forms/public/index.php`, `assignments/10-http-basics/public/index.php`, `assignments/11-sessions/src/bootstrap.php`, `assignments/13-auth-db-app/src/bootstrap.php`

  **Acceptance Criteria** (agent-executable only):
  - [ ] `test -f vercel.json && grep -q 'vercel-php' vercel.json`
  - [ ] `test -f api/index.php`
  - [ ] `test -f api/assignments.php`
  - [ ] `test -f .env.vercel.local.example && grep -q 'ASSIGNMENT11_STATE_SECRET' .env.vercel.local.example && grep -q 'ASSIGNMENT13_AUTH_SECRET' .env.vercel.local.example`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > .sisyphus/evidence/task-1-vercel-dev.log 2>&1 & VPID=$!; sleep 5; curl -fsSI http://127.0.0.1:4010/ > .sisyphus/evidence/task-1-root-head.txt; kill $VPID` exits `0`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task1.log 2>&1 & VPID=$!; sleep 5; curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:4010/not-a-real-assignment | grep -q '^404$'; kill $VPID`

  **QA Scenarios** (MANDATORY - task incomplete without these):
  ```
  Scenario: Root dispatcher serves launchpad placeholder and unknown slug 404
    Tool: Bash
    Steps: start `vercel dev --listen 127.0.0.1:4010`; request `/` and `/unknown-slug`; store both responses; stop server
    Expected: `/` returns HTTP 200, `/unknown-slug` returns HTTP 404, and neither response is served from a raw assignment file path
    Evidence: .sisyphus/evidence/task-1-ingress.txt

  Scenario: Mounted request metadata reaches an assignment route
    Tool: Bash
    Steps: start `vercel dev --listen 127.0.0.1:4010`; request `/08-string-generation?show=0`; stop server
    Expected: dispatcher preserves the mounted slug path and query string so downstream assignment logic can react correctly
    Evidence: .sisyphus/evidence/task-1-metadata.txt
  ```

  **Commit**: YES | Message: `feat(deploy): add vercel ingress contract and route manifest` | Files: [`vercel.json`, `api/`]

- [x] 2. Build the launchpad shell, card contract, and shared minimalist assets

  **What to do**: Implement the root homepage for `/` with a minimalist responsive grid, icon+text home logo, and 13 assignment cards. Store only presentation-level shared assets at root (`public/assets/launchpad.css`, `public/assets/logo.svg` or equivalent); define stable card markup contract with `data-launchpad-grid`, `data-assignment-card`, `data-assignment-slug`, `data-assignment-type`, and `data-assignment-description`; the card model must include title, one-line description, and badge only.
  **Must NOT do**: Do not add a component framework; do not hide assignments behind tabs/filters; do not use visual clutter like gradients, shadows, and multi-color badges beyond a restrained minimalist palette.

  **Recommended Agent Profile**:
  - Category: `visual-engineering` - Reason: this task defines the UX contract for the homepage and header.
  - Skills: `[]` - no special skill required.
  - Omitted: `["playwright"]` - browser validation happens in QA, not implementation itself.

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: [3,4,5,6,7,8,9,10] | Blocked By: [1]

  **References**:
  - User direction: launchpad-style grid, icon+text logo, minimalist style
  - Safe lightweight HTML pattern: `assignments/08-string-generation/public/index.php`
  - Existing repo root context: `README.md`
  - Route source for all 13 assignments: `assignments/*/SPEC.md`

  **Acceptance Criteria**:
  - [ ] `test -f public/assets/launchpad.css`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task2.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/ | grep -q 'data-launchpad-grid'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task2.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/ | grep -c 'data-assignment-card' | grep -q '^13$'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task2.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/ | grep -q 'data-home-logo'; kill $VPID`

  **QA Scenarios**:
  ```
  Scenario: Launchpad exposes exactly 13 cards and a working home logo
    Tool: Playwright
    Steps: open `http://127.0.0.1:4010/`; assert `[data-launchpad-grid]` exists; assert `[data-assignment-card]` count is 13; click `[data-home-logo]`
    Expected: clicking the logo keeps or returns the browser to `/` and the 13-card grid remains visible
    Evidence: .sisyphus/evidence/task-2-launchpad.png

  Scenario: Minimalist card density stays constrained
    Tool: Playwright
    Steps: inspect the first card `[data-assignment-slug="01-php-basics"]`
    Expected: the card contains only one title element, one description element, one type badge, and one primary link/button
    Evidence: .sisyphus/evidence/task-2-card-structure.txt
  ```

  **Commit**: YES | Message: `feat(ui): add minimalist launchpad shell and assets` | Files: [`public/assets/`, `api/index.php`, `api/assignments.php`]

- [x] 3. Web-enable CLI assignments `01-03` with isolated public wrappers

  **What to do**: Add `public/index.php` to `assignments/01-php-basics`, `02-control-structures`, and `03-arrays`. Each wrapper must capture the current deterministic CLI output from the existing assignment-local `index.php`, preserve UTF-8 content, render it inside a minimalist assignment page with the shared header/logo contract, and expose the page through `/01-php-basics`, `/02-control-structures`, and `/03-arrays`.
  **Must NOT do**: Do not rewrite the underlying assignment logic into a new shared service; do not remove the existing CLI entrypoints or tests.

  **Recommended Agent Profile**:
  - Category: `quick` - Reason: the pattern is straightforward once the wrapper contract is fixed.
  - Skills: `[]` - no special skill required.
  - Omitted: `["playwright"]` - curl + existing CLI assertions are sufficient here.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [10] | Blocked By: [1,2]

  **References**:
  - Existing CLI sources: `assignments/01-php-basics/index.php`, `assignments/02-control-structures/index.php`, `assignments/03-arrays/index.php`
  - Existing regression tests: `assignments/01-php-basics/tests/run.php`, `assignments/02-control-structures/tests/run.php`, `assignments/03-arrays/tests/run.php`
  - Safe page shell example: `assignments/08-string-generation/public/index.php`

  **Acceptance Criteria**:
  - [ ] `php assignments/01-php-basics/tests/run.php && php assignments/02-control-structures/tests/run.php && php assignments/03-arrays/tests/run.php` exits `0`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task3.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/01-php-basics | grep -q 'Rectangle perimeter'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task3.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/02-control-structures | grep -q 'season'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task3.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/03-arrays | grep -q 'sort'; kill $VPID`

  **QA Scenarios**:
  ```
  Scenario: CLI wrappers preserve deterministic content
    Tool: Bash
    Steps: save `php assignments/01-php-basics/index.php`, `php assignments/02-control-structures/index.php`, and `php assignments/03-arrays/index.php` outputs; request the three mounted web pages; compare expected key phrases
    Expected: each wrapper includes the same core assignment result text as its CLI source, plus the shared home logo header
    Evidence: .sisyphus/evidence/task-3-cli-web.txt

  Scenario: UTF-8 text survives web wrapping
    Tool: Bash
    Steps: request `/01-php-basics` and inspect response headers/body encoding-sensitive content
    Expected: response is UTF-8 and Cyrillic text does not mojibake
    Evidence: .sisyphus/evidence/task-3-cli-utf8.txt
  ```

  **Commit**: YES | Message: `feat(web): expose assignments 01-03 through vercel wrappers` | Files: [`assignments/01-php-basics/public/`, `assignments/02-control-structures/public/`, `assignments/03-arrays/public/`, `api/assignments.php`]

- [x] 4. Web-enable CLI assignments `04-07` with the same wrapper contract

  **What to do**: Apply the same assignment-local public wrapper pattern to `04-associative-arrays`, `05-multidimensional-arrays`, `06-user-functions`, and `07-standard-functions`. Ensure each page shows the assignment output inside the shared minimalist shell and is reachable from the launchpad with its short slug route.
  **Must NOT do**: Do not regress the existing CLI tests; do not create one generic “CLI assignment page” that bypasses assignment-local entrypoints.

  **Recommended Agent Profile**:
  - Category: `quick` - Reason: same pattern as Task 3 across four more isolated assignments.
  - Skills: `[]` - no special skill required.
  - Omitted: `["playwright"]` - curl + CLI tests are enough.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [10] | Blocked By: [1,2]

  **References**:
  - Existing CLI sources: `assignments/04-associative-arrays/index.php`, `assignments/05-multidimensional-arrays/index.php`, `assignments/06-user-functions/index.php`, `assignments/07-standard-functions/index.php`
  - Existing regression tests: `assignments/04-associative-arrays/tests/run.php`, `assignments/05-multidimensional-arrays/tests/run.php`, `assignments/06-user-functions/tests/run.php`, `assignments/07-standard-functions/tests/run.php`
  - Wrapper pattern to follow: Task 3 outputs and assignment-local `public/index.php` structure

  **Acceptance Criteria**:
  - [ ] `php assignments/04-associative-arrays/tests/run.php && php assignments/05-multidimensional-arrays/tests/run.php && php assignments/06-user-functions/tests/run.php && php assignments/07-standard-functions/tests/run.php` exits `0`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task4.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/04-associative-arrays | grep -q 'January'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task4.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/05-multidimensional-arrays | grep -q '<table'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task4.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/07-standard-functions | grep -q 'http'; kill $VPID`

  **QA Scenarios**:
  ```
  Scenario: Four additional CLI assignments open from launchpad routes
    Tool: Playwright
    Steps: open `/`; click cards for `04-associative-arrays`, `05-multidimensional-arrays`, `06-user-functions`, and `07-standard-functions`
    Expected: each route opens a page with the shared header/logo and assignment-specific content, and the browser URL matches the clicked slug
    Evidence: .sisyphus/evidence/task-4-cli-navigation.png

  Scenario: Existing CLI assertions remain authoritative
    Tool: Bash
    Steps: run the four existing `tests/run.php` files after wrapper creation
    Expected: all tests still pass unchanged
    Evidence: .sisyphus/evidence/task-4-cli-regression.txt
  ```

  **Commit**: YES | Message: `feat(web): expose assignments 04-07 through vercel wrappers` | Files: [`assignments/04-associative-arrays/public/`, `assignments/05-multidimensional-arrays/public/`, `assignments/06-user-functions/public/`, `assignments/07-standard-functions/public/`, `api/assignments.php`]

- [x] 5. Adapt already-safe assignments `08` and `12` to the shared header and mounted-route contract

  **What to do**: Keep `08-string-generation` and `12-regex-validation` functionally intact, but add the shared header/logo markup and make their pages explicitly aware of the mounted route contract. They must work unchanged under `/08-string-generation` and `/12-regex-validation`, preserve current logic, and expose stable selectors for the home logo.
  **Must NOT do**: Do not redesign their assignment behavior; do not add unnecessary local routers.

  **Recommended Agent Profile**:
  - Category: `unspecified-low` - Reason: small targeted UI/mount integration changes.
  - Skills: `[]` - no special skill required.
  - Omitted: `[]` - browser smoke is relevant here.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [10] | Blocked By: [1,2]

  **References**:
  - `assignments/08-string-generation/public/index.php`
  - `assignments/12-regex-validation/public/index.php`
  - Launchpad shell contract from Task 2

  **Acceptance Criteria**:
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task5.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/08-string-generation | grep -q 'data-home-logo'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task5.log 2>&1 & VPID=$!; sleep 5; curl -fsSL 'http://127.0.0.1:4010/08-string-generation?show=0' | grep -vq 'id="conditional-block"'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task5.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/12-regex-validation | grep -q 'data-home-logo'; kill $VPID`

  **QA Scenarios**:
  ```
  Scenario: Safe mounted assignments keep their original behavior
    Tool: Playwright
    Steps: open `/08-string-generation`; confirm images, list, and select exist; click home logo; open `/12-regex-validation`; submit valid form data
    Expected: both pages render inside the shared navigation pattern and keep their original happy-path functionality
    Evidence: .sisyphus/evidence/task-5-safe-pages.png

  Scenario: Header contract is identical on both pages
    Tool: Bash
    Steps: request `/08-string-generation` and `/12-regex-validation`; grep for `data-home-logo` and the shared stylesheet path
    Expected: both pages expose the same header/logo contract and shared CSS asset reference
    Evidence: .sisyphus/evidence/task-5-header-contract.txt
  ```

  **Commit**: YES | Message: `feat(ui): align assignments 08 and 12 with launchpad shell` | Files: [`assignments/08-string-generation/public/index.php`, `assignments/12-regex-validation/public/index.php`, `public/assets/launchpad.css`]

- [x] 6. Refactor `09-forms` for mounted routes and local URL helpers

  **What to do**: Add assignment-local helpers to `09-forms` so every link/form action is generated relative to `APP_BASE_PATH`. Replace hardcoded root usages (`action="/result.php"`, `href="/"`) with `app_url()` or equivalent local helper calls, and ensure both GET and POST flows still work when mounted at `/09-forms`.
  **Must NOT do**: Do not centralize form helper logic outside the assignment; do not break direct GET vs POST distinction.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: multiple form flows and path generation must all change consistently.
  - Skills: `[]` - no special skill required.
  - Omitted: `["playwright"]` - curl-based verification is primary.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [10] | Blocked By: [1,2]

  **References**:
  - Route hotspots: `assignments/09-forms/public/index.php:185`, `assignments/09-forms/public/index.php:195`
  - Back-link hotspot: `assignments/09-forms/public/result.php:38`
  - Existing parsing helpers: `assignments/09-forms/src/helpers.php`
  - Existing local smoke expectation: `scripts/run-web-smoke.sh`

  **Acceptance Criteria**:
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task6.log 2>&1 & VPID=$!; sleep 5; curl -fsSL 'http://127.0.0.1:4010/09-forms/result.php?name=Ivan&age=20&salary=500' | grep -q 'Метод запроса: <strong>GET</strong>'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task6.log 2>&1 & VPID=$!; sleep 5; curl -fsSL -X POST -d 'name=Ivan&age=20&salary=500' http://127.0.0.1:4010/09-forms/result.php | grep -q 'Метод запроса: <strong>POST</strong>'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task6.log 2>&1 & VPID=$!; sleep 5; curl -fsSL http://127.0.0.1:4010/09-forms | grep -vq 'action="/result.php"'; kill $VPID`

  **QA Scenarios**:
  ```
  Scenario: Mounted GET and POST result flows both work
    Tool: Bash
    Steps: start `vercel dev --listen 127.0.0.1:4010`; submit the first form by GET and by POST against `/09-forms/result.php`; stop server
    Expected: both responses reflect the submitted values and never redirect to bare `/result.php`
    Evidence: .sisyphus/evidence/task-6-forms-mounted.txt

  Scenario: Invalid birthday stays mounted and reports validation cleanly
    Tool: Bash
    Steps: POST `task=birthday&birthday=31.02.1990` to `/09-forms`
    Expected: response contains the birthday validation error and no PHP warning output; all links/forms still point under `/09-forms`
    Evidence: .sisyphus/evidence/task-6-forms-birthday.txt
  ```

  **Commit**: YES | Message: `fix(forms): make assignment 09 mounted-route safe` | Files: [`assignments/09-forms/public/`, `assignments/09-forms/src/helpers.php`]

- [x] 7. Refactor `10-http-basics` into a mounted router under `/10-http-basics`

  **What to do**: Keep `10-http-basics` as a single-file router, but make route matching relative to `APP_REQUEST_PATH` instead of the global root path. All links, form actions, redirects, and Location headers must resolve under `/10-http-basics`, while still exposing the original logical subroutes: `/method`, `/headers`, `/status/200`, `/status/302`, `/status/400`, `/status/404`, and `/redirect-target`.
  **Must NOT do**: Do not split the assignment into multiple controllers; do not lose exact status-code behavior.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: route normalization and header correctness are easy to break.
  - Skills: `[]` - curl-based verification is the primary tool.
  - Omitted: `["playwright"]` - browser adds little value over header-aware curl here.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [10] | Blocked By: [1,2]

  **References**:
  - Link hotspot region: `assignments/10-http-basics/public/index.php:88-99`
  - Status/router region: `assignments/10-http-basics/public/index.php:133-149`
  - Existing single-file router behavior: `assignments/10-http-basics/public/index.php`

  **Acceptance Criteria**:
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task7.log 2>&1 & VPID=$!; sleep 5; curl -i -fsSL http://127.0.0.1:4010/10-http-basics/status/200 | grep -q 'HTTP/1.1 200'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task7.log 2>&1 & VPID=$!; sleep 5; curl -i -s http://127.0.0.1:4010/10-http-basics/status/302 | grep -q 'Location: /10-http-basics/redirect-target'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task7.log 2>&1 & VPID=$!; sleep 5; curl -s -H 'Accept-Language: ru' http://127.0.0.1:4010/10-http-basics/headers | grep -q 'Accept-Language'; kill $VPID`

  **QA Scenarios**:
  ```
  Scenario: Mounted HTTP router returns exact status codes
    Tool: Bash
    Steps: start `vercel dev --listen 127.0.0.1:4010`; call `/10-http-basics/status/200`, `/302`, `/400`, `/404` with `curl -i`; stop server
    Expected: each response returns the exact intended HTTP status and stays under the `/10-http-basics` route family
    Evidence: .sisyphus/evidence/task-7-http-status.txt

  Scenario: Method endpoint distinguishes GET and POST under mounted path
    Tool: Bash
    Steps: call `/10-http-basics/method` once with GET and once with `curl -X POST`
    Expected: response text clearly identifies the actual method each time
    Evidence: .sisyphus/evidence/task-7-http-method.txt
  ```

  **Commit**: YES | Message: `fix(http): mount assignment 10 router under slug path` | Files: [`assignments/10-http-basics/public/index.php`]

- [x] 8. Replace `11-sessions` native sessions with a signed JSON cookie scoped to `/11-sessions`

  **What to do**: Remove Vercel-path dependence on `$_SESSION` from assignment `11`. Implement a local state layer that serializes the needed per-flow data into one signed JSON cookie named `assignment11_state`, path-scoped to `/11-sessions`, using HMAC signing with a deployment secret. Preserve current flows: country persistence, first-entry timer, email carry-over, refresh counter, city/age profile prefill, logout reset, and the multi-page quiz. Introduce local helpers for `app_url()`, `app_request_path()`, and cookie read/write/clear in `assignments/11-sessions/src/bootstrap.php`.
  **Must NOT do**: Do not keep `session_start()` on Vercel paths; do not use one cookie path of `/`; do not let one flow overwrite another flow’s namespace.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: this is the highest-risk state migration short of auth.
  - Skills: `["playwright"]` - multi-page cookie flow verification benefits from browser automation.
  - Omitted: `[]` - all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [10] | Blocked By: [1,2]

  **References**:
  - Current state bootstrap: `assignments/11-sessions/src/bootstrap.php`
  - Current index route hotspots: `assignments/11-sessions/public/index.php:21`, `assignments/11-sessions/public/index.php:30`, `assignments/11-sessions/public/index.php:36-39`
  - Current public flow files: `assignments/11-sessions/public/test.php`, `email-step1.php`, `email-step2.php`, `profile-step1.php`, `profile-step2.php`, `quiz-step1.php`, `quiz-step2.php`, `quiz-result.php`, `logout.php`

  **Acceptance Criteria**:
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task8.log 2>&1 & VPID=$!; sleep 5; curl -c /tmp/a11.cookies -b /tmp/a11.cookies -fsSL -X POST -d 'country=Россия' http://127.0.0.1:4010/11-sessions > /dev/null; curl -c /tmp/a11.cookies -b /tmp/a11.cookies -fsSL http://127.0.0.1:4010/11-sessions/test.php | grep -q 'Россия'; kill $VPID`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task8.log 2>&1 & VPID=$!; sleep 5; curl -c /tmp/a11.cookies -b /tmp/a11.cookies -fsSL http://127.0.0.1:4010/11-sessions > /tmp/first11.html; curl -c /tmp/a11.cookies -b /tmp/a11.cookies -fsSL http://127.0.0.1:4010/11-sessions > /tmp/second11.html; grep -q 'первый визит' /tmp/first11.html; grep -q 'Текущее количество открытий' /tmp/second11.html; kill $VPID`
  - [ ] `grep -q 'assignment11_state' assignments/11-sessions/src/bootstrap.php`

  **QA Scenarios**:
  ```
  Scenario: Signed cookie state preserves multi-step flows and logout clears it
    Tool: Playwright
    Steps: open `/11-sessions`; submit country form; verify `/11-sessions/test.php`; complete email flow and quiz flow; click logout; revisit `/11-sessions/test.php`
    Expected: pre-logout pages retain the expected state, logout clears the cookie-backed state, and post-logout revisit shows cleared session state
    Evidence: .sisyphus/evidence/task-8-sessions.png

  Scenario: Cookie scope prevents leakage outside assignment 11
    Tool: Bash
    Steps: run a `/11-sessions` flow with a cookie jar, then request `/13-auth-db-app` with the same jar
    Expected: assignment 13 does not treat the assignment 11 cookie as auth/session state
    Evidence: .sisyphus/evidence/task-8-cookie-scope.txt
  ```

  **Commit**: YES | Message: `refactor(sessions): replace assignment 11 native sessions with signed cookie state` | Files: [`assignments/11-sessions/src/bootstrap.php`, `assignments/11-sessions/public/`]

- [ ] 9. Refactor `13-auth-db-app` to use a DB-backed opaque auth cookie and mounted routes

  **What to do**: Remove dependency on native PHP session storage from assignment `13`. Keep the `users` table and add a deployment support table `user_sessions(id, user_id, token_hash, created_at, expires_at, last_seen_at)` in `database/13-auth-db-app/schema.sql`. Implement auth as an opaque random token stored hashed in MySQL, delivered via a cookie named `assignment13_auth`, path-scoped to `/13-auth-db-app`, with `HttpOnly`, `SameSite=Lax`, and `Secure` in production. Replace session flash usage with query-status redirects or a short-lived dedicated flash cookie; mounted navigation and redirects must resolve under `/13-auth-db-app`. Use `.env.vercel.local` as the local source of `AUTH_DB_*` and `ASSIGNMENT13_AUTH_SECRET` during verification.
  **Must NOT do**: Do not keep `session_start()` on Vercel paths; do not store plain-text tokens in the DB; do not leave logout as cookie deletion only - it must also invalidate the DB row.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: auth, DB persistence, and mounted-route correctness all change together.
  - Skills: `["playwright"]` - end-to-end auth flow validation benefits from browser automation.
  - Omitted: `[]` - all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [10] | Blocked By: [1,2]

  **References**:
  - Current landing page: `assignments/13-auth-db-app/public/index.php`
  - Current auth pages: `assignments/13-auth-db-app/public/register.php`, `login.php`, `logout.php`
  - Current bootstrap/nav hotspot: `assignments/13-auth-db-app/src/bootstrap.php`
  - Current DB contract: `assignments/13-auth-db-app/src/db.php`
  - Current auth logic: `assignments/13-auth-db-app/src/auth.php`
  - Schema baseline: `database/13-auth-db-app/schema.sql`
  - DB scripts baseline: `scripts/reset-auth-db.sh`, `scripts/run-db-smoke.sh`

  **Acceptance Criteria**:
  - [ ] `grep -q 'user_sessions' database/13-auth-db-app/schema.sql`
  - [ ] `set -a; source .env.vercel.local; set +a; bash scripts/reset-auth-db.sh` exits `0`
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task9.log 2>&1 & VPID=$!; sleep 5; curl -c /tmp/a13.cookies -b /tmp/a13.cookies -fsSL -X POST -d 'full_name=Ilya Sheklakov&email=ilya@example.com&password=pass1234' http://127.0.0.1:4010/13-auth-db-app/register.php > /tmp/a13-register.html; curl -c /tmp/a13.cookies -b /tmp/a13.cookies -fsSL -X POST -d 'email=ilya@example.com&password=pass1234' http://127.0.0.1:4010/13-auth-db-app/login.php > /tmp/a13-login.html; grep -q 'Вы вошли в систему' /tmp/a13-login.html; kill $VPID`
  - [ ] `set -a; source .env.vercel.local; set +a; bash scripts/run-db-smoke.sh` exits `0`

  **QA Scenarios**:
  ```
  Scenario: Registration, duplicate rejection, login, and logout all work under mounted path
    Tool: Playwright
    Steps: open `/13-auth-db-app/register.php`; register `ilya@example.com`; attempt duplicate registration with the same email; log in; verify `/13-auth-db-app`; log out
    Expected: first registration succeeds, duplicate email is rejected with a clear message, login succeeds, logout removes auth state and returns the app to guest mode
    Evidence: .sisyphus/evidence/task-9-auth.png

  Scenario: Logout invalidates DB token as well as cookie
    Tool: Bash
    Steps: capture the auth cookie after login, call logout, then query the DB for the corresponding hashed token row
    Expected: the cookie is cleared and the matching session row is gone or expired immediately
    Evidence: .sisyphus/evidence/task-9-auth-logout.txt
  ```

  **Commit**: YES | Message: `refactor(auth): make assignment 13 vercel-safe with db-backed auth cookie` | Files: [`assignments/13-auth-db-app/`, `database/13-auth-db-app/schema.sql`, `scripts/reset-auth-db.sh`, `scripts/run-db-smoke.sh`]

- [ ] 10. Add Vercel smoke automation, deploy extraction tooling, and deployment runbook updates

  **What to do**: Add a root deployment smoke script `scripts/run-vercel-smoke.sh` that validates the launchpad, 13 card count, header/logo behavior, representative wrapper routes (`/01-php-basics`, `/07-standard-functions`), safe mounted pages (`/08-string-generation`, `/12-regex-validation`), mounted refactors (`/09-forms`, `/10-http-basics/status/404`, `/11-sessions`, `/13-auth-db-app`), and auth/state failure paths where environment is available. Add `scripts/extract_vercel_url.py` to parse the production URL from `vercel deploy --prod --yes` output, and update `README.md` plus `.env.vercel.local.example` with the exact local+production deploy workflow and required env vars/secrets.
  **Must NOT do**: Do not leave production verification dependent on manual URL copy/paste; do not add instructions that assume localhost DB on Vercel.

  **Recommended Agent Profile**:
  - Category: `writing` - Reason: this is automation plus precise deployment/runbook documentation.
  - Skills: `[]` - no special skill required.
  - Omitted: `[]` - both bash and docs matter.

  **Parallelization**: Can Parallel: NO | Wave 2 | Blocks: [] | Blocked By: [3,4,5,6,7,8,9]

  **References**:
  - Existing smoke scripts: `scripts/run-web-smoke.sh`, `scripts/run-db-smoke.sh`, `scripts/php-lint-all.sh`
  - Existing repo runbook: `README.md`
  - Launchpad selector contract from Task 2
  - Auth env baseline: `assignments/13-auth-db-app/src/db.php`, `scripts/reset-auth-db.sh`

  **Acceptance Criteria**:
  - [ ] `test -f scripts/run-vercel-smoke.sh && test -f scripts/extract_vercel_url.py`
  - [ ] `python3 scripts/extract_vercel_url.py .sisyphus/evidence/vercel-deploy.txt` prints a `.vercel.app` URL after a successful deploy log is present
  - [ ] `vercel dev --listen 127.0.0.1:4010 > /tmp/task10.log 2>&1 & VPID=$!; sleep 5; bash scripts/run-vercel-smoke.sh http://127.0.0.1:4010; STATUS=$?; kill $VPID; test $STATUS -eq 0`
  - [ ] `grep -q 'vercel deploy --prod --yes' README.md`

  **QA Scenarios**:
  ```
  Scenario: Local Vercel emulation smoke covers representative routes
    Tool: Bash
    Steps: start `vercel dev --listen 127.0.0.1:4010`; run `bash scripts/run-vercel-smoke.sh http://127.0.0.1:4010`; stop server
    Expected: smoke script validates `/`, 13 cards, mounted routes, status endpoints, and state/auth checks that are possible with available env vars
    Evidence: .sisyphus/evidence/task-10-vercel-dev-smoke.txt

  Scenario: Production URL is parsed and reused automatically
    Tool: Bash
    Steps: deploy with `vercel deploy --prod --yes | tee .sisyphus/evidence/vercel-deploy.txt`; run `DEPLOY_URL="$(python3 scripts/extract_vercel_url.py .sisyphus/evidence/vercel-deploy.txt)"`; invoke `bash scripts/run-vercel-smoke.sh "$DEPLOY_URL"`
    Expected: the deploy URL is extracted without manual editing and the production smoke script uses it directly
    Evidence: .sisyphus/evidence/task-10-vercel-prod-smoke.txt
  ```

  **Commit**: YES | Message: `test(deploy): add vercel smoke automation and deployment runbook` | Files: [`scripts/run-vercel-smoke.sh`, `scripts/extract_vercel_url.py`, `README.md`]

## Final Verification Wave (MANDATORY — after ALL implementation tasks)
> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.
> **Do NOT auto-proceed after verification. Wait for user's explicit approval before marking work complete.**
> **Never mark F1-F4 as checked before getting user's okay.** Rejection or user feedback -> fix -> re-run -> present again -> wait for okay.
- [ ] F1. Plan Compliance Audit — oracle
- [ ] F2. Code Quality Review — unspecified-high
- [ ] F3. Real Manual QA — unspecified-high (+ playwright if UI)
- [ ] F4. Scope Fidelity Check — deep

## Commit Strategy
- Commit after each numbered task; do not batch unrelated assignment families into one commit.
- Keep root ingress/UI commits separate from assignment-local refactors.
- Preserve the order: `1 → 2 → 3/4/5/6/7/8/9 → 10`.
- For task 9, commit schema, app code, and DB smoke updates atomically so auth state cannot drift from DB automation.

## Success Criteria
- Vercel serves `/` as a 13-card launchpad with a minimalist grid and working home logo.
- Every assignment opens under a short route and no assignment depends on bare-root links such as `/result.php`, `/test.php`, `/login.php`, or `/status/404`.
- `01-07` remain CLI-testable and are also readable as mounted web pages.
- `11-sessions` no longer depends on native PHP file sessions; `13-auth-db-app` no longer depends on native PHP sessions for auth state.
- Production deployment is smoke-tested automatically from the deployed `.vercel.app` URL.
