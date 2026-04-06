# Vercel Launchpad Deployment for Assignments 01-13

## TL;DR
> **Summary**: Deploy the workspace as one Vercel project with a launchpad-style root page at `/` and canonical assignment routes `/assignments/01` ... `/assignments/13`. Keep assignment business logic isolated, normalize every assignment to be base-path aware, and use a single catch-all PHP entrypoint instead of exposing many PHP files directly.
> **Deliverables**:
> - one Vercel deployment scaffold (`vercel.json` + one catch-all PHP entrypoint)
> - launchpad home page with Apple-inspired 13-card grid
> - persistent assignment-local header on every HTML assignment page with return-to-home navigation
> - web wrappers for current CLI assignments `01`-`07`
> - prefix-safe routing for existing web assignments `08`-`13`
> - serverless-safe cookie session strategy for `11` and `13`
> - external MySQL/TLS-ready deployment contract for `13`
> **Effort**: XL
> **Parallel**: YES - 3 waves
> **Critical Path**: 1 → 2 → 6/7/8/9/10 → 11/12/13 → 14 → 15 → F1-F4

## Context
### Original Request
- Развернуть каждый проект на Vercel.
- На главной показать папочки 1..13 по сетке в стиле Launchpad / Apple.
- На каждой странице держать header.
- Из header должна быть возможность вернуться на главную.

### Interview Summary
- Deployment model is fixed to **one Vercel project**, not many separate projects.
- Current CLI assignments `01`-`07` must become web-accessible through thin wrappers, not be left out.
- Assignment `13` must stay on **external MySQL**, not be migrated to another database type.
- Persistent header means **all HTML assignment pages**; raw HTTP/text/status demonstration endpoints in assignment `10` remain unwrapped.

### Metis Review (gaps addressed)
- Guardrail fixed: many-file PHP exposure on Vercel is rejected; the plan is locked to one catch-all PHP entrypoint.
- Guardrail fixed: root-absolute links/forms/redirects in `09`, `10`, `11`, and `13` must be normalized; config-only rewrites are insufficient.
- Guardrail fixed: default file-based PHP sessions are not acceptable for Vercel in `11` and `13`; assignment-scoped signed cookie sessions are mandatory.
- Guardrail fixed: assignment `10` keeps raw non-HTML endpoints under its mounted prefix.
- Risk fixed: `13` DB config must support provider/TLS options, not only bare host/port/user/password/dbname.

## Work Objectives
### Core Objective
Ship a single-domain Vercel deployment where `/` is an Apple-inspired launchpad grid for all 13 assignments and every assignment is reachable at a stable prefixed route without breaking its original exercise semantics.

### Deliverables
- `vercel.json` with one-project, one-entrypoint routing
- root launchpad page and shared route dispatcher
- browser wrappers for `01`-`07`
- base-path aware navigation for `08`-`13`
- HTML header on all HTML assignment pages
- raw-status/raw-text preservation for assignment `10`
- assignment-scoped signed cookie session layer for `11` and `13`
- external-MySQL-ready env contract and Vercel deployment notes for `13`

### Definition of Done (verifiable conditions with commands)
- `php -l` passes on all changed PHP files.
- `bash scripts/run-cli-assignments.sh` still exits `0`.
- local parity smoke passes for mounted routes and nested pages.
- `vercel.json` exists and routes `/` plus `/assignments/*` through one PHP entrypoint.
- direct deep-link access works for `/assignments/09/result.php`, `/assignments/10/status/404`, `/assignments/11/test.php`, `/assignments/13/login.php`.
- assignment `10` returns correct raw status/headers under its mounted prefix.
- assignment `11` and `13` use different cookie names and cookie paths.
- assignment `13` can connect to the chosen external MySQL env contract, including TLS options when required.

### Must Have
- canonical public routes: `/assignments/01` ... `/assignments/13`
- root launchpad at `/`
- Apple-inspired card grid layout on the launchpad
- header on every HTML assignment page with home link
- assignment isolation preserved
- `01`-`07` web wrappers must reuse existing deterministic CLI logic, not replace it
- `11` and `13` must not rely on default file-backed PHP sessions in production

### Must NOT Have
- no 14 separate Vercel projects
- no many-file Vercel PHP deployment model
- no shared assignment business logic layer across folders
- no HTML wrapping for assignment `10` text/status endpoints
- no literal root-absolute assignment URLs left in deployed HTML flows
- no migration of assignment `13` away from MySQL
- no richer feature expansion of `01`-`07` beyond thin read-only wrappers

## Verification Strategy
> ZERO HUMAN INTERVENTION - all verification is agent-executed.
- Test decision: tests-after with PHP lint + curl route checks + browser-state checks where session flow matters
- QA policy: every task includes happy-path and failure/edge verification
- Evidence: `.sisyphus/evidence/task-{N}-{slug}.{ext}`

## Execution Strategy
### Parallel Execution Waves
> Target: 5-8 tasks per wave. <3 per wave (except final) = under-splitting.
> Shared routing/session decisions are extracted into Wave 1 to maximize safe parallelism.

Wave 1: deployment scaffold, route contract, launchpad shell, CLI web wrappers

Wave 2: normalize stateless/mixed assignments `08`, `09`, `10`, `12`, and HTML chrome in `11`

Wave 3: signed-cookie sessions, auth/db deployment hardening, nested-route/deep-link verification, Vercel deployment docs

### Dependency Matrix (full, all tasks)
- 1 blocks 2-15
- 2 blocks 6-15
- 3 blocks 6-15
- 4 and 5 depend on 1-3 and can run in parallel
- 6, 7, 8, 10 depend on 1-3 and can run in parallel
- 9 depends on 1-3 and partially on 2 because header contract must already exist
- 11 depends on 2, 3, and 9
- 12 depends on 2, 3, and 13
- 13 depends on 1-3
- 14 depends on 6-13
- 15 depends on 1-14

### Agent Dispatch Summary (wave → task count → categories)
- Wave 1 → 5 tasks → deep / unspecified-high / visual-engineering / writing
- Wave 2 → 5 tasks → unspecified-high / visual-engineering
- Wave 3 → 5 tasks → deep / unspecified-high / writing

## TODOs
> Implementation + Test = ONE task. Never separate.
> EVERY task MUST have: Agent Profile + Parallelization + QA Scenarios.

- [ ] 1. Create single-entry Vercel deployment scaffold

  **What to do**: Add `vercel.json` and one catch-all PHP entrypoint (recommended file: `api/index.php`) that receives `/` and all `/assignments/**` requests. The catch-all must parse the request path, normalize trailing slashes, preserve query strings, and dispatch into isolated assignment handlers without exposing one Vercel function per PHP file.
  **Must NOT do**: Do not deploy each public PHP file as a separate Vercel function. Do not mount assignments at root-level paths like `/login.php` or `/test.php` in production.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: deployment/runtime contract and routing architecture are the highest-risk decisions.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['playwright']` - deployment scaffold is routing/config work, not browser automation.

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: [2,3,4,5,6,7,8,9,10,11,12,13,14,15] | Blocked By: []

  **References**:
  - Constraint: `README.md:12-15` - current browser apps are assignment-local `public/` entrypoints.
  - Constraint: `scripts/serve-assignment.sh:7-70` - repo is currently served one assignment at a time, so deployment needs a new central entrypoint.
  - Risk: Vercel-fit research summary in draft - many-file PHP exposure is a poor fit and should be avoided.

  **Acceptance Criteria**:
  - [ ] `vercel.json` exists and rewrites `/` plus `/assignments/(.*)` to one PHP entrypoint.
  - [ ] catch-all entrypoint preserves request path and query string for downstream dispatch.
  - [ ] direct refresh of a nested path does not 404 at the Vercel router layer.

  **QA Scenarios**:
  ```
  Scenario: Root and prefixed routes reach one dispatcher
    Tool: Bash
    Steps: inspect `vercel.json`; run local dispatcher parity checks against `/` and `/assignments/10/status/404`.
    Expected: both requests are handled by the same entrypoint contract; no many-file routing dependency remains.
    Evidence: .sisyphus/evidence/task-1-vercel-dispatch.txt

  Scenario: Trailing slash and query-string normalization
    Tool: Bash
    Steps: request `/assignments/09/` and `/assignments/09/result.php?name=Ilya&age=25&salary=1234.5` through the mounted router.
    Expected: trailing slash normalizes predictably and query parameters reach the downstream assignment intact.
    Evidence: .sisyphus/evidence/task-1-vercel-dispatch-edge.txt
  ```

  **Commit**: YES | Message: `feat(deploy): add single-entry vercel routing scaffold` | Files: [`vercel.json`, `api/index.php`, any root deployment helper files]

- [ ] 2. Define canonical route map and base-path helper contract

  **What to do**: Lock the public URL scheme to `/assignments/01` ... `/assignments/13`, with nested pages beneath each numeric prefix (examples: `/assignments/09/result.php`, `/assignments/11/test.php`, `/assignments/13/login.php`). Implement a deployment-wide route contract and assignment-local base-path helper contract so assignments generate links/forms/redirects relative to their mounted prefix instead of root `/`.
  **Must NOT do**: Do not leave literal assignment URLs hardcoded as site-root absolute paths. Do not use mixed slug styles like `/assignments/01-php-basics` for some assignments and `/assignments/01` for others.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: this decision controls every downstream assignment refactor.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['frontend-ui-ux']` - URL contract is not a visual task.

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: [4,5,6,7,8,9,10,11,12,13,14,15] | Blocked By: [1]

  **References**:
  - Root-absolute form/link example: `assignments/09-forms/public/index.php` - current form actions target `/result.php`.
  - Front-controller path dependency: `assignments/10-http-basics/public/index.php:9-15,102-149` - current router reads `REQUEST_URI` directly.
  - Session links/redirects: `assignments/11-sessions/src/bootstrap.php:13-16,47-50` - current helper returns raw root paths.
  - Auth nav/redirects: `assignments/13-auth-db-app/src/bootstrap.php:13-16,85-90` - current links point to root-level auth pages.

  **Acceptance Criteria**:
  - [ ] one documentable canonical path exists for every assignment and every nested page.
  - [ ] all HTML route generators become base-path aware.
  - [ ] assignment `10` route stripping preserves raw endpoint behavior under `/assignments/10/*`.

  **QA Scenarios**:
  ```
  Scenario: Canonical routes resolve correctly
    Tool: Bash
    Steps: request `/assignments/01`, `/assignments/08`, `/assignments/09/result.php`, `/assignments/11/test.php`, `/assignments/13/login.php` through the mounted app.
    Expected: each path resolves without escaping to root `/` URLs.
    Evidence: .sisyphus/evidence/task-2-route-map.txt

  Scenario: No root-absolute assignment links remain in HTML flows
    Tool: Bash
    Steps: crawl rendered HTML for assignments 08, 09, 11, 12, 13 and search for `href="/` or `action="/` that incorrectly bypass the prefix.
    Expected: only intentional launchpad-home links or assignment-10 raw route examples remain; no broken root-absolute assignment flow links exist.
    Evidence: .sisyphus/evidence/task-2-route-map-edge.txt
  ```

  **Commit**: YES | Message: `feat(routes): lock canonical assignment base paths` | Files: [`api/index.php`, assignment-local path helpers, route docs if added]

- [ ] 3. Build the launchpad home page and persistent header design contract

  **What to do**: Implement the root `/` launchpad page with 13 Apple-inspired folder cards arranged in a grid, one per assignment. Fix the visual contract: glassy/light Apple-like surfaces, generous spacing, rounded cards, consistent typography, and a persistent header pattern for HTML assignment pages with a home/back-to-launchpad action. Define the exact header markup contract each HTML assignment must follow.
  **Must NOT do**: Do not build a shared business-logic framework. Do not apply the visual shell to assignment `10` raw endpoints.

  **Recommended Agent Profile**:
  - Category: `visual-engineering` - Reason: launchpad and header design need intentional UI execution.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['playwright']` - browser testing comes later.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [6,7,8,9,10,11,12,14,15] | Blocked By: [1]

  **References**:
  - Existing root gap: draft research - no current root `index.php` or `public/index.php` exists.
  - Existing local shell pattern: `assignments/11-sessions/src/bootstrap.php:25-33`.
  - Existing local shell pattern: `assignments/13-auth-db-app/src/bootstrap.php:60-101`.

  **Acceptance Criteria**:
  - [ ] `/` renders 13 cards labeled `01` ... `13` and each links to its canonical assignment path.
  - [ ] every HTML assignment page shows the agreed persistent header with home navigation.
  - [ ] assignment `10` keeps shell only on its HTML landing page, not on raw endpoints.

  **QA Scenarios**:
  ```
  Scenario: Launchpad grid renders all assignments
    Tool: Playwright
    Steps: open `/`; count launchpad cards; click cards `01`, `08`, and `13`.
    Expected: 13 visible cards in a grid; each selected card opens the correct assignment route.
    Evidence: .sisyphus/evidence/task-3-launchpad.png

  Scenario: Header remains on HTML pages only
    Tool: Bash
    Steps: request `/assignments/08`, `/assignments/11/test.php`, and `/assignments/10/status/404`.
    Expected: 08 and 11 HTML include the header contract; 10 status endpoint returns raw non-HTML output without shell markup.
    Evidence: .sisyphus/evidence/task-3-launchpad-edge.txt
  ```

  **Commit**: YES | Message: `feat(ui): add launchpad home and shared header contract` | Files: [root launchpad files, assignment HTML pages touched for header contract]

- [ ] 4. Add thin web wrappers for CLI assignments 01-03

  **What to do**: Add `public/index.php` wrappers for `01`, `02`, and `03` that call the existing deterministic CLI output builders and render that output as read-only HTML inside the persistent header/shell. Preserve the original root `index.php` and `tests/run.php` behavior untouched.
  **Must NOT do**: Do not duplicate business calculations into wrapper files. Do not convert these assignments into interactive forms/apps.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: wrappers must bridge CLI logic into HTML without altering the tested core.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['deep']` - architectural decisions are already fixed.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [14,15] | Blocked By: [1,2,3]

  **References**:
  - CLI output pattern: `assignments/01-php-basics/index.php:35-63` - `build_assignment_output()` + direct CLI execution guard.
  - Repo convention: `README.md:12-15` - browser entrypoints use `public/`.

  **Acceptance Criteria**:
  - [ ] `/assignments/01`, `/assignments/02`, `/assignments/03` render HTML wrappers over existing deterministic output.
  - [ ] existing CLI tests for `01`-`03` remain unchanged and passing.
  - [ ] wrapper output visually sits inside the persistent header/shell.

  **QA Scenarios**:
  ```
  Scenario: Wrapper preserves CLI content
    Tool: Bash
    Steps: compare rendered HTML text fragments from `/assignments/01`, `/assignments/02`, `/assignments/03` against the output of their existing CLI builders/tests.
    Expected: key deterministic lines appear unchanged inside HTML wrappers.
    Evidence: .sisyphus/evidence/task-4-cli-web-wrappers.txt

  Scenario: CLI regression stays green
    Tool: Bash
    Steps: run `bash scripts/run-cli-assignments.sh`.
    Expected: all CLI assignments still pass after adding wrappers.
    Evidence: .sisyphus/evidence/task-4-cli-web-wrappers-regression.txt
  ```

  **Commit**: YES | Message: `feat(web): wrap assignments 01-03 for launchpad access` | Files: [`assignments/01-php-basics/public/`, `assignments/02-control-structures/public/`, `assignments/03-arrays/public/`]

- [ ] 5. Add thin web wrappers for CLI assignments 04-07

  **What to do**: Repeat the wrapper pattern for `04`, `05`, `06`, and `07` using `public/index.php` per assignment, preserving existing root CLI entrypoints and tests. Keep the wrappers read-only and deterministic.
  **Must NOT do**: Do not refactor shared assignment logic across siblings. Do not add interactivity beyond viewing deterministic output.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: same migration pattern as task 4 across four more assignments.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['frontend-ui-ux']` - shell contract is already fixed in task 3.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [14,15] | Blocked By: [1,2,3]

  **References**:
  - Assignment boundary rule: `assignments/AGENTS.md` - never import business logic from sibling assignments.
  - CLI pattern reference: analogous to `assignments/01-php-basics/index.php:35-63` and matching CLI siblings.

  **Acceptance Criteria**:
  - [ ] `/assignments/04` ... `/assignments/07` render read-only wrappers over existing deterministic outputs.
  - [ ] existing CLI tests for `04`-`07` remain green.
  - [ ] all wrappers include the persistent header and home navigation.

  **QA Scenarios**:
  ```
  Scenario: Wrapped CLI assignments are reachable from launchpad
    Tool: Playwright
    Steps: open `/`; click cards `04`, `05`, `06`, `07` one by one.
    Expected: each card opens an HTML page with wrapped deterministic content and header.
    Evidence: .sisyphus/evidence/task-5-cli-web-wrappers-ui.png

  Scenario: CLI regression remains intact
    Tool: Bash
    Steps: run `bash scripts/run-cli-assignments.sh`.
    Expected: existing tests still pass with no output drift caused by wrapper work.
    Evidence: .sisyphus/evidence/task-5-cli-web-wrappers-regression.txt
  ```

  **Commit**: YES | Message: `feat(web): wrap assignments 04-07 for launchpad access` | Files: [`assignments/04-associative-arrays/public/`, `assignments/05-multidimensional-arrays/public/`, `assignments/06-user-functions/public/`, `assignments/07-standard-functions/public/`]

- [ ] 6. Normalize assignment 08 for mounted deployment and shared header

  **What to do**: Update `08-string-generation` to render through the persistent header contract and make every internal URL/query example base-path aware under `/assignments/08`. Preserve current content sections and `?show=0` conditional-block behavior.
  **Must NOT do**: Do not alter the assignment’s required sections or convert it into a multi-page app.

  **Recommended Agent Profile**:
  - Category: `visual-engineering` - Reason: mostly HTML/shell integration without deep server logic.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['deep']` - routing contract already fixed.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [14,15] | Blocked By: [1,2,3]

  **References**:
  - Current standalone page: `assignments/08-string-generation/public/index.php`.
  - Existing conditional behavior: verified current `?show=0` branch from prior repo checks.

  **Acceptance Criteria**:
  - [ ] `/assignments/08` renders with persistent header.
  - [ ] `?show=0` still hides the conditional block under the mounted prefix.
  - [ ] no root-absolute assignment URLs remain in the page.

  **QA Scenarios**:
  ```
  Scenario: Happy path render under prefix
    Tool: Bash
    Steps: request `/assignments/08`.
    Expected: page contains launchpad-home header and required assignment sections.
    Evidence: .sisyphus/evidence/task-6-assignment-08.txt

  Scenario: Conditional block hides correctly
    Tool: Bash
    Steps: request `/assignments/08?show=0`.
    Expected: `id="conditional-block"` is absent while the rest of the page still renders.
    Evidence: .sisyphus/evidence/task-6-assignment-08-edge.txt
  ```

  **Commit**: YES | Message: `feat(08): mount string-generation under launchpad shell` | Files: [`assignments/08-string-generation/public/index.php`]

- [ ] 7. Normalize assignment 09 for prefixed form actions and result routing

  **What to do**: Refactor `09-forms/public/index.php` and `result.php` so all forms, links, and post-redirect targets work under `/assignments/09` instead of `/`. Integrate the persistent header into both HTML pages.
  **Must NOT do**: Do not break the separate `result.php` requirement. Do not merge GET and POST verification paths.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: multiple forms and action targets must be normalized carefully.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['playwright']` - curl checks are sufficient.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [14,15] | Blocked By: [1,2,3]

  **References**:
  - Current standalone actions: `assignments/09-forms/public/index.php`.
  - Current result page: `assignments/09-forms/public/result.php`.
  - Helper layer: `assignments/09-forms/src/helpers.php`.

  **Acceptance Criteria**:
  - [ ] GET and POST first-task submissions work at `/assignments/09/result.php`.
  - [ ] return-to-main navigation from result page points to `/assignments/09`.
  - [ ] all HTML pages show the persistent header.

  **QA Scenarios**:
  ```
  Scenario: GET and POST result flows under mounted path
    Tool: Bash
    Steps: request `/assignments/09/result.php?name=Ilya&age=25&salary=1234.5`; POST the same fields to `/assignments/09/result.php`.
    Expected: both responses render correctly and identify the right request method.
    Evidence: .sisyphus/evidence/task-7-assignment-09.txt

  Scenario: Invalid input remains handled after path normalization
    Tool: Bash
    Steps: POST invalid birthday and invalid temperature data to the mounted assignment routes.
    Expected: error messages still render correctly; no broken form actions or 404s occur.
    Evidence: .sisyphus/evidence/task-7-assignment-09-edge.txt
  ```

  **Commit**: YES | Message: `feat(09): make forms base-path aware for vercel` | Files: [`assignments/09-forms/public/index.php`, `assignments/09-forms/public/result.php`, optional local helper changes]

- [ ] 8. Normalize assignment 10 for prefix-aware raw HTTP routing

  **What to do**: Refactor `10-http-basics/public/index.php` so the router strips the `/assignments/10` prefix before evaluating routes, while keeping `/method`, `/headers`, `/status/*`, and `/redirect-target` semantics intact. Apply the visual header only to the HTML landing page at `/assignments/10`; all other endpoints must remain raw text/status responses.
  **Must NOT do**: Do not wrap `/method`, `/headers`, or `/status/*` in HTML. Do not break `302 Location` targets after prefix mounting.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: prefix-aware front-controller behavior and raw HTTP correctness are fragile.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['frontend-ui-ux']` - most work is routing/HTTP semantics.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [14,15] | Blocked By: [1,2,3]

  **References**:
  - Current route parsing: `assignments/10-http-basics/public/index.php:9-15,102-149`.
  - Current root-absolute links/forms: `assignments/10-http-basics/public/index.php:74-99`.

  **Acceptance Criteria**:
  - [ ] `/assignments/10` renders HTML landing page with header.
  - [ ] `/assignments/10/status/200|302|400|404` return correct HTTP status codes.
  - [ ] `302 Location` points to `/assignments/10/redirect-target`.
  - [ ] `/assignments/10/method` and `/assignments/10/headers` stay plain text.

  **QA Scenarios**:
  ```
  Scenario: Prefixed raw HTTP endpoints behave correctly
    Tool: Bash
    Steps: run `curl -i` against `/assignments/10/status/200`, `/assignments/10/status/302`, `/assignments/10/status/400`, `/assignments/10/status/404`.
    Expected: each response returns the expected status code; 302 includes the prefixed `Location` header.
    Evidence: .sisyphus/evidence/task-8-assignment-10.txt

  Scenario: Raw endpoints are not HTML-wrapped
    Tool: Bash
    Steps: request `/assignments/10/method` and `/assignments/10/headers`.
    Expected: responses are plain text and contain no launchpad/header HTML markup.
    Evidence: .sisyphus/evidence/task-8-assignment-10-edge.txt
  ```

  **Commit**: YES | Message: `feat(10): preserve raw http demos under mounted routes` | Files: [`assignments/10-http-basics/public/index.php`]

- [ ] 9. Normalize assignment 11 HTML shell and route helpers for prefixed multi-page flow

  **What to do**: Update `11-sessions/src/bootstrap.php` and all public pages so links, redirects, and header navigation resolve under `/assignments/11`. Reuse the existing `render_page()` shell point to keep changes centralized within the assignment.
  **Must NOT do**: Do not flatten all pages into one route. Do not remove the per-flow page structure.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: many pages depend on one bootstrap and must stay coherent.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['artistry']` - this is controlled normalization, not creative problem-solving.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [11,14,15] | Blocked By: [1,2,3]

  **References**:
  - Shared shell and path helper: `assignments/11-sessions/src/bootstrap.php:13-16,25-33,47-50`.
  - Multi-page flow inventory: `assignments/11-sessions/AGENTS.md`.

  **Acceptance Criteria**:
  - [ ] `/assignments/11`, `/assignments/11/test.php`, `/assignments/11/email-step1.php`, `/assignments/11/quiz-step1.php` all resolve directly.
  - [ ] all HTML pages show the persistent header and launchpad-home link.
  - [ ] no page still links or redirects to site-root `/...`.

  **QA Scenarios**:
  ```
  Scenario: Deep-link entry works on nested session pages
    Tool: Bash
    Steps: request `/assignments/11/test.php`, `/assignments/11/email-step2.php`, `/assignments/11/quiz-step2.php` directly.
    Expected: pages render under the mounted route without router 404s.
    Evidence: .sisyphus/evidence/task-9-assignment-11.txt

  Scenario: Redirects remain in-prefix
    Tool: Bash
    Steps: submit the country form and email step 1 through the mounted routes and inspect redirect targets.
    Expected: redirects stay inside `/assignments/11/*` and never escape to root.
    Evidence: .sisyphus/evidence/task-9-assignment-11-edge.txt
  ```

  **Commit**: YES | Message: `feat(11): mount multi-page session flows under canonical prefix` | Files: [`assignments/11-sessions/src/bootstrap.php`, `assignments/11-sessions/public/*.php`]

- [ ] 10. Normalize assignment 12 for mounted deployment and persistent header

  **What to do**: Make `12-regex-validation/public/index.php` base-path safe under `/assignments/12` and integrate the persistent header/shell while preserving the single-form validation experience.
  **Must NOT do**: Do not weaken regex rules. Do not expand the assignment into registration/auth logic.

  **Recommended Agent Profile**:
  - Category: `visual-engineering` - Reason: mostly HTML integration with light server-side path adjustments.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['deep']` - no special architecture beyond the fixed route contract.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [14,15] | Blocked By: [1,2,3]

  **References**:
  - Current standalone page: `assignments/12-regex-validation/public/index.php`.

  **Acceptance Criteria**:
  - [ ] `/assignments/12` renders with header.
  - [ ] valid and invalid submissions both work under the mounted path.
  - [ ] no root-absolute assignment URLs remain.

  **QA Scenarios**:
  ```
  Scenario: Valid submission under prefix
    Tool: Bash
    Steps: POST valid email/login/password/phone data to `/assignments/12`.
    Expected: success state renders under the mounted route.
    Evidence: .sisyphus/evidence/task-10-assignment-12.txt

  Scenario: Invalid submission under prefix
    Tool: Bash
    Steps: POST invalid email/login/password/phone data to `/assignments/12`.
    Expected: field-level error output renders and no broken form path occurs.
    Evidence: .sisyphus/evidence/task-10-assignment-12-edge.txt
  ```

  **Commit**: YES | Message: `feat(12): mount regex validation under launchpad shell` | Files: [`assignments/12-regex-validation/public/index.php`]

- [ ] 11. Replace assignment 11 file sessions with signed cookie session storage

  **What to do**: Remove reliance on default `session_start()` file-backed sessions in `11-sessions`. Implement assignment-local signed cookie session helpers (recommended location: `assignments/11-sessions/src/session.php` or equivalent) with cookie name `assignment11_session`, cookie path `/assignments/11`, `Secure`, `HttpOnly`, `SameSite=Lax`, and secret-driven HMAC signing. Keep existing exercise namespaces (`country_form`, `visit_timer`, `email_flow`, `refresh_counter`, `profile_prefill`, `quiz`) in the signed payload.
  **Must NOT do**: Do not share the session helper with assignment `13`. Do not use default `PHPSESSID` at site-root scope.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: serverless-safe state handling and security-sensitive cookie design.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['playwright']` - browser automation comes in verification.

  **Parallelization**: Can Parallel: NO | Wave 3 | Blocks: [14,15] | Blocked By: [2,3,9]

  **References**:
  - Current file-session bootstrap: `assignments/11-sessions/src/bootstrap.php:4-6,35-45`.
  - Flow inventory: `assignments/11-sessions/AGENTS.md`.

  **Acceptance Criteria**:
  - [ ] assignment `11` no longer depends on `session_start()` file persistence in production flow.
  - [ ] cookie name/path are assignment-specific and do not collide with assignment `13`.
  - [ ] all existing session flows still work from browser requests.

  **QA Scenarios**:
  ```
  Scenario: Signed cookie session preserves multi-page flow
    Tool: Playwright
    Steps: open `/assignments/11`; submit country, email, profile, and quiz flows in one browser session.
    Expected: state persists across pages; final outputs match submitted data.
    Evidence: .sisyphus/evidence/task-11-assignment-11-session.png

  Scenario: Assignment 11 logout does not affect assignment 13 cookie scope
    Tool: Bash
    Steps: inspect response cookies for assignment 11 before and after `/assignments/11/logout.php`; verify cookie name/path are assignment-specific.
    Expected: only the assignment 11 cookie is cleared; no cross-assignment cookie namespace is targeted.
    Evidence: .sisyphus/evidence/task-11-assignment-11-session-edge.txt
  ```

  **Commit**: YES | Message: `feat(11): replace file sessions with signed assignment cookie` | Files: [`assignments/11-sessions/src/`, `assignments/11-sessions/public/*.php`]

- [ ] 12. Replace assignment 13 file sessions and root nav with assignment-scoped auth cookies

  **What to do**: Refactor `13-auth-db-app/src/bootstrap.php` and auth pages to stop using default file-backed PHP sessions. Implement assignment-local signed cookie auth state with cookie name `assignment13_session`, cookie path `/assignments/13`, and the same secure attributes as task 11. Make nav links, redirects, and flash handling base-path aware under `/assignments/13`.
  **Must NOT do**: Do not reuse assignment `11` session code. Do not fall back to root-level `/login.php` or `/logout.php` links.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: auth cookies and redirect behavior are security-sensitive and path-sensitive.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['frontend-ui-ux']` - the main risk is auth/session correctness.

  **Parallelization**: Can Parallel: NO | Wave 3 | Blocks: [14,15] | Blocked By: [2,3]

  **References**:
  - Current file-session bootstrap and nav: `assignments/13-auth-db-app/src/bootstrap.php:4-6,13-16,19-58,60-101`.
  - Current DB/auth split: `assignments/13-auth-db-app/AGENTS.md`.

  **Acceptance Criteria**:
  - [ ] assignment `13` no longer depends on default `PHPSESSID` file sessions.
  - [ ] nav links and redirects are correct under `/assignments/13`.
  - [ ] auth state survives the intended request flow on the deployed domain.
  - [ ] logging out of `13` does not target assignment `11` cookies.

  **QA Scenarios**:
  ```
  Scenario: Auth flow works under prefixed path
    Tool: Playwright
    Steps: open `/assignments/13/register.php`; register; log in; verify the home page shows the logged-in user; log out.
    Expected: full auth flow succeeds entirely within `/assignments/13/*`.
    Evidence: .sisyphus/evidence/task-12-assignment-13-auth.png

  Scenario: Auth cookie is scoped to assignment 13 only
    Tool: Bash
    Steps: inspect `Set-Cookie` from login/logout responses.
    Expected: cookie name/path are `assignment13_session` and `/assignments/13`; logout clears only that cookie.
    Evidence: .sisyphus/evidence/task-12-assignment-13-auth-edge.txt
  ```

  **Commit**: YES | Message: `feat(13): scope auth session and nav for mounted deployment` | Files: [`assignments/13-auth-db-app/src/bootstrap.php`, `assignments/13-auth-db-app/public/*.php`, optional assignment-local session helper]

- [ ] 13. Extend assignment 13 DB configuration for external MySQL on Vercel

  **What to do**: Update `13-auth-db-app/src/db.php` and deployment docs so external MySQL on Vercel is first-class: keep existing `AUTH_DB_HOST`, `AUTH_DB_PORT`, `AUTH_DB_USER`, `AUTH_DB_PASSWORD`, `AUTH_DB_NAME`, and add explicit TLS/provider knobs (recommended: `AUTH_DB_SSL_MODE`, optional CA/certificate env handling if the provider requires it). Keep graceful user-facing failures when DB env vars are missing or the connection is down.
  **Must NOT do**: Do not swap MySQL for another database type. Do not hardcode credentials or provider details into source.

  **Recommended Agent Profile**:
  - Category: `deep` - Reason: external DB compatibility and failure handling are deployment-critical.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['writing']` - docs are secondary to getting the env contract correct.

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: [14,15] | Blocked By: [1,2]

  **References**:
  - Current env contract: `assignments/13-auth-db-app/src/db.php`.
  - Current schema ownership: `database/13-auth-db-app/schema.sql`.
  - Current automation: `scripts/reset-auth-db.sh`, `scripts/run-db-smoke.sh`.

  **Acceptance Criteria**:
  - [ ] DB config supports external MySQL from Vercel preview/production.
  - [ ] missing env vars produce a user-readable failure state, not a fatal.
  - [ ] TLS/SSL provider requirements are representable by env config, not hidden assumptions.

  **QA Scenarios**:
  ```
  Scenario: External MySQL env contract succeeds
    Tool: Bash
    Steps: run the auth app and DB smoke using the final env matrix against the external/staging-compatible MySQL target.
    Expected: reset, schema checks, register/login/logout, duplicate-email rejection, and password-hash storage all pass.
    Evidence: .sisyphus/evidence/task-13-assignment-13-db.txt

  Scenario: Missing env vars fail gracefully
    Tool: Bash
    Steps: unset one required DB env var and request `/assignments/13`.
    Expected: the page shows a human-readable DB configuration error state and does not fatally crash.
    Evidence: .sisyphus/evidence/task-13-assignment-13-db-edge.txt
  ```

  **Commit**: YES | Message: `feat(13): harden external mysql config for vercel` | Files: [`assignments/13-auth-db-app/src/db.php`, related docs/scripts if required]

- [ ] 14. Verify nested routing, direct deep links, and query preservation across the mounted project

  **What to do**: Add or update automated smoke checks so the mounted deployment verifies `/`, all `/assignments/NN` roots, and the fragile nested paths for `09`, `10`, `11`, and `13`. Extend existing smoke coverage to reflect the final Vercel route map rather than the current per-assignment local server model.
  **Must NOT do**: Do not leave verification at “page reachable” depth only. Do not skip nested direct-entry checks.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` - Reason: this is verification-system work spanning multiple assignments.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['frontend-ui-ux']` - focus is behavior validation, not appearance.

  **Parallelization**: Can Parallel: NO | Wave 3 | Blocks: [15] | Blocked By: [4,5,6,7,8,9,10,11,12,13]

  **References**:
  - Current smoke coverage: `scripts/run-web-smoke.sh`.
  - Current CLI regression script: `scripts/run-cli-assignments.sh`.

  **Acceptance Criteria**:
  - [ ] mounted-route smoke covers launchpad root plus all 13 canonical assignment roots.
  - [ ] smoke verifies at least `/assignments/09/result.php`, `/assignments/10/status/404`, `/assignments/11/test.php`, `/assignments/13/login.php`.
  - [ ] query strings and redirect targets are exercised where relevant.

  **QA Scenarios**:
  ```
  Scenario: Deep-link refresh matrix
    Tool: Bash
    Steps: request root, all assignment roots, and the four fragile nested routes directly.
    Expected: no route 404s; nested routes render or return raw HTTP as designed.
    Evidence: .sisyphus/evidence/task-14-mounted-routes.txt

  Scenario: Redirect and query preservation matrix
    Tool: Bash
    Steps: test `/assignments/08?show=0`, `/assignments/09/result.php?...`, `/assignments/10/status/302`, and mounted 11/13 redirects.
    Expected: query params survive; redirect targets remain in-prefix.
    Evidence: .sisyphus/evidence/task-14-mounted-routes-edge.txt
  ```

  **Commit**: YES | Message: `test(deploy): cover mounted vercel route matrix` | Files: [`scripts/`, any route-specific smoke helpers]

- [ ] 15. Add deployment documentation and Vercel env/runbook

  **What to do**: Document the exact deployment workflow: required Vercel config files, required environment variables (including session secrets and DB/TLS settings), canonical route map, how to connect the external MySQL instance, and preview/production verification steps. Include the implementation note that assignment `10` raw endpoints are intentionally unwrapped.
  **Must NOT do**: Do not leave environment naming implicit. Do not omit session-cookie/path requirements from the deployment runbook.

  **Recommended Agent Profile**:
  - Category: `writing` - Reason: this is deployment contract capture and operator guidance.
  - Skills: `[]` - no extra skill required.
  - Omitted: `['playwright']` - documentation task.

  **Parallelization**: Can Parallel: NO | Wave 3 | Blocks: [F1,F2,F3,F4] | Blocked By: [1,2,3,11,12,13,14]

  **References**:
  - Current repo overview: `README.md:1-36`.
  - Current DB env contract: `assignments/13-auth-db-app/src/db.php`.
  - Existing assignment-local shell points: `assignments/11-sessions/src/bootstrap.php:25-33`, `assignments/13-auth-db-app/src/bootstrap.php:60-101`.

  **Acceptance Criteria**:
  - [ ] deployment runbook lists all required Vercel env vars and their meaning.
  - [ ] route map and assignment 10 exception are explicitly documented.
  - [ ] preview/prod verification steps are executable from the repo.

  **QA Scenarios**:
  ```
  Scenario: Runbook completeness review
    Tool: Bash
    Steps: compare final docs against actual env vars, route map, and scripts present in the repo.
    Expected: no undocumented required variable or route behavior remains.
    Evidence: .sisyphus/evidence/task-15-deploy-docs.txt

  Scenario: New-operator dry run
    Tool: interactive_bash
    Steps: follow the runbook from a clean shell context up to the point of local validation/deployment preparation.
    Expected: the instructions are sufficient without hidden tribal knowledge.
    Evidence: .sisyphus/evidence/task-15-deploy-docs-edge.txt
  ```

  **Commit**: YES | Message: `docs(deploy): add vercel launchpad runbook` | Files: [`README.md`, deployment docs, env examples]

## Final Verification Wave (MANDATORY — after ALL implementation tasks)
> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.
> **Do NOT auto-proceed after verification. Wait for user's explicit approval before marking work complete.**
> **Never mark F1-F4 as checked before getting user's okay.** Rejection or user feedback -> fix -> re-run -> present again -> wait for okay.
- [ ] F1. Plan Compliance Audit — oracle
- [ ] F2. Code Quality Review — unspecified-high
- [ ] F3. Real Manual QA — unspecified-high (+ playwright if UI)
- [ ] F4. Scope Fidelity Check — deep

## Commit Strategy
- Commit after Wave 1 foundation: Vercel scaffold + launchpad + wrapper contract.
- Commit after Wave 2 route normalization: mounted HTML/raw route correctness for `08`-`12`.
- Commit after Wave 3 stateful/deployment hardening: signed-cookie sessions, DB env contract, mounted-route smoke, deployment runbook.
- Do not squash Wave 2 and Wave 3 into one commit; stateful/session work must remain auditable.

## Success Criteria
- One Vercel project serves `/` and all 13 assignments.
- `01`-`07` remain CLI-correct and become web-accessible via thin wrappers.
- `08`-`13` work under `/assignments/NN` without broken root-absolute links.
- Assignment `10` preserves raw HTTP/text endpoint behavior under its prefix.
- Assignments `11` and `13` use assignment-scoped signed cookies rather than default file-backed PHP sessions.
- Assignment `13` works with external MySQL using the final env contract.
- The launchpad home and persistent HTML headers match the agreed Apple-inspired shell direction.
