# All Practical PHP/MySQL Assignments

## TL;DR
> **Summary**: Build the workspace from scratch as 13 isolated PHP assignment folders derived from the 13 root `.docx` briefs, with lightweight shared tooling only, executable verification, and a later executor-created `AGENTS.md` based on the draft in this plan.
> **Deliverables**:
> - 13 assignment implementations under `assignments/`
> - shared run/verification/bootstrap scripts under `scripts/`
> - DB bootstrap/reset assets for the database practical
> - repo-root `AGENTS.md` materialized from the draft below
> **Effort**: XL
> **Parallel**: YES - 4 waves
> **Critical Path**: 1 → 2/4/5 → 14/15/16/17 → 18 → F1-F4

## Context
### Original Request
- Выполнить все задания, которые находятся в данной директории.
- Составить план.
- Создать spec'и и `agents.md` для решения практических.
- После этого начать работу.

### Interview Summary
- Scope is all 13 root-level `.docx` briefs discovered in the workspace.
- Implementation must use separate folders/apps per topic, not one cumulative app.
- Verification may include any tooling actually needed by the assignment; default to lightweight tests-after with executable QA.
- `AGENTS.md` must be prepared now as planning content and materialized by the executor during implementation.
- Prometheus remains in planning mode only; execution handoff is via `/start-work`.

### Metis Review (gaps addressed)
- Gap addressed: filenames/themes alone were insufficient, so the source briefs were normalized into the spec pack below.
- Guardrail added: shared tooling is allowed, shared assignment business logic is not.
- Guardrail added: every assignment must have executable setup/reset, happy-path, and failure-path verification.
- Guardrail added: no framework/ORM/platform-building unless a brief explicitly requires it.
- Risk captured: HTTP, regex, and DB briefs are underspecified, so defaults are fixed explicitly in this plan.

## Repo Decisions (fixed)
- Top-level work tree: `assignments/`, `scripts/`, `database/`, `.sisyphus/`
- Assignment folders use numbered ASCII slugs:
  - `assignments/01-php-basics`
  - `assignments/02-control-structures`
  - `assignments/03-arrays`
  - `assignments/04-associative-arrays`
  - `assignments/05-multidimensional-arrays`
  - `assignments/06-user-functions`
  - `assignments/07-standard-functions`
  - `assignments/08-string-generation`
  - `assignments/09-forms`
  - `assignments/10-http-basics`
  - `assignments/11-sessions`
  - `assignments/12-regex-validation`
  - `assignments/13-auth-db-app`
- Encoding: UTF-8 everywhere; avoid BOM; use `mb_*` string functions whenever Cyrillic correctness matters.
- Runtime default: native PHP CLI + PHP built-in server + local MySQL/MariaDB. Docker is NOT introduced unless execution proves native DB unavailable.
- Verification mode: tests-after with lightweight per-assignment assertions; no mandatory PHPUnit unless an executor proves it materially reduces complexity for a specific assignment family.
- Shared code policy: shared scripts/test harnesses are allowed; shared assignment runtime/business logic is forbidden.
- Output language policy: preserve Russian visible strings when the brief implies learner-facing Russian text; internal filenames/slugs remain ASCII English.

## Embedded Specification Pack

### S1. 01-php-basics
- **Source**: `Копия Основы PHP.docx`
- **Goal**: cover beginner PHP output, string basics, and simple formulas.
- **Mandatory exercises**:
  - output name, age, study place, hobbies
  - output string length
  - output last character of a string
  - compute circle area from radius
  - compute rectangle area
  - compute rectangle perimeter
- **Deliverable shape**: one assignment folder with either one aggregated script or several tiny scripts, plus a deterministic test runner.
- **Mandatory validations**: string length must be UTF-8-safe when Cyrillic text is used.
- **Ambiguity resolved**: “write to file” is implemented as deterministic script output saved/checked by the test harness, not manual file authoring.

### S2. 02-control-structures
- **Source**: `Управляющие конструкции.docx`
- **Goal**: conditionals, loops, filtering, aggregation.
- **Mandatory exercises**:
  - season from month 1..12
  - first char equals `a`
  - lucky six-digit number check
  - raise all salaries by 10%
  - raise only salaries `<= 400`
  - print `1..100`
  - filter array elements `>0 && <10`
  - sum and average array values
- **Deliverable shape**: one aggregated script with named subtask outputs and assertion coverage.
- **Mandatory validations**: reject month values outside `1..12`; lucky-number logic is only for six-digit input.

### S3. 03-arrays
- **Source**: `Массивы.docx`
- **Goal**: indexed arrays, search, fill, sums, ranges, shuffle, sorting.
- **Mandatory exercises**:
  - arithmetic using `[2,5,3,9]`
  - output user associative data
  - fill array `1..5`
  - find min/max
  - detect value `3`
  - sum all elements
  - create arrays `1..100` and `a..z`
  - sum `1..100` without a loop
  - `range(1,25)` + `shuffle`
  - random nonrepeating alphabet
  - multiple sorts on `['3'=>'a','1'=>'c','2'=>'e','4'=>'b']`
- **Ambiguity resolved**: “different sorts” means at minimum demonstrate `sort`, `asort`, `ksort`, `rsort`, `arsort`, and `krsort` where applicable with before/after output.

### S4. 04-associative-arrays
- **Source**: `Ассоциативные массивы.docx`
- **Goal**: keyed array creation, date/user data, counts, last-element retrieval.
- **Mandatory exercises**:
  - `[1=>'a',2=>'b',3=>'c']`
  - months array with January key `1`
  - `name surname patronymic`
  - current date as `year-month-day`
  - key holes/order demo
  - count indexed vs associative arrays
  - last and penultimate elements

### S5. 05-multidimensional-arrays
- **Source**: `Многомерность.docx`
- **Goal**: nested arrays, nested loops, tables, structured datasets.
- **Mandatory exercises**:
  - sum all elements of a 2D array
  - sum salaries of first and third user
  - books dataset output
  - disciplines dataset as a table
  - output `group name - user name` from nested groups
- **Mandatory validations**: disciplines view must be HTML table output.

### S6. 06-user-functions
- **Source**: `Пользовательские функции.docx`
- **Goal**: reusable functions, return values, boolean checks, numeric helpers.
- **Mandatory exercises**:
  - print own name
  - positive/negative emits `+++` or `---`
  - sum three numbers
  - cube function returning into `$res`
  - all elements even
  - adjacent duplicates detection
  - sum of digits
  - prime check

### S7. 07-standard-functions
- **Source**: `Стандартные функции PHP.docx`
- **Goal**: built-in string/array/math/date functions.
- **Mandatory exercises**:
  - average of numeric array
  - sum `1..100`
  - print `1..100`
  - uppercase last char of a string
  - square roots for numeric array
  - min/max of array
  - random number
  - detect `http` prefix
  - output current year/month/day/hour/minute/second
  - days until New Year for any year
- **Mandatory validations**: string tasks must be mb-safe if Cyrillic examples are used; New Year task must not hardcode a specific year.

### S8. 08-string-generation
- **Source**: `Формирование строк.docx`
- **Goal**: generate HTML from PHP variables, loops, and conditions.
- **Mandatory exercises**:
  - 3 variables as paragraphs
  - 3 image tags from source vars
  - list `1..5`
  - `<select>` from array
  - current date paragraph in `year-month-day`
  - conditional `<div>` block when `show=true`
  - mixed PHP/HTML list variant
  - repeated user cards from users array

### S9. 09-forms
- **Source**: `Формы.docx`
- **Goal**: GET/POST forms, validation-by-logic, checkbox/radio/date/textarea tasks.
- **Mandatory exercises**:
  - `name/age/salary` form to `result.php` for both GET and POST
  - 3-number sum form
  - name + age display
  - password comparison
  - surname/name/patronymic display
  - checkbox-based greeting/goodbye
  - gender radio buttons
  - Celsius/Fahrenheit converter
  - birthday input in `dd.mm.yyyy`; days until next birthday
  - textarea word count and character count

### S10. 10-http-basics
- **Source**: `Работа с HTTP.docx`
- **Goal**: detect request method, inspect headers, send statuses.
- **Mandatory exercises**:
  - distinguish GET vs POST
  - read `Accept` and `Accept-Language`
  - list all request headers
  - send 404
  - send corresponding status codes
- **Ambiguity resolved**: because starter code is absent, executor must implement explicit demo endpoints for 200/302/400/404 and verify them with `curl`.

### S11. 11-sessions
- **Source**: `Сессии в PHP.docx`
- **Goal**: session persistence, counters, prefills, logout, multi-page quiz.
- **Mandatory exercises**:
  - country form on `index.php`, display on `test.php`
  - show seconds since first site entry
  - carry email from one form to another
  - refresh counter + first-visit message
  - city+age form then prefilled `Name/Age/City` form
  - `logout.php` destroys session
  - multi-page quiz storing answers in session
- **Mandatory validations**: no output before `session_start`; UTF-8 without BOM.

### S12. 12-regex-validation
- **Source**: `Тема_11_Практическая_работа_Реглярные_выражения.docx`
- **Goal**: one PHP form validating email/login/password/phone by regex.
- **Mandatory exercises**:
  - build the form
  - validate all four fields by regex
  - show success if all valid, otherwise error
- **Ambiguity resolved**: fixed regex policy for execution:
  - email: conventional local-part + `@` + domain + TLD
  - login: 3-20 chars, Latin letters/digits/underscore, must start with a letter
  - password: min 8 chars, at least one Latin letter and one digit
  - phone: optional leading `+`, digits only after normalization, final length 10-15 digits

### S13. 13-auth-db-app
- **Source**: `Базы данных.docx`
- **Goal**: registration/login/logout with DB persistence and user-facing status handling.
- **Mandatory exercises**:
  - registration form
  - login form
  - save registered user to DB
  - enforce unique email
  - show user info after login
  - logout
  - show clear success/error state messages
- **Ambiguity resolved**: fixed schema for execution:
  - `users(id, full_name, email, password_hash, created_at)`
  - password storage uses `password_hash`/`password_verify`
  - one MySQL database dedicated to this assignment only

## AGENTS.md Draft (to be materialized by executor at repo root)
```md
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
```

## Work Objectives
### Core Objective
Implement every assignment defined by the 13 source briefs as a reproducible, verifiable, isolated PHP workspace with exact folder naming, fixed runtime assumptions, and explicit agent execution guidance.

### Deliverables
- 13 completed assignment folders under `assignments/`
- shared scripts for linting, serving, CLI test runs, and DB reset/bootstrap
- executor-created repo-root `AGENTS.md` from the draft above
- executable proof artifacts under `.sisyphus/evidence/`

### Definition of Done (verifiable conditions with commands)
- `php -v` succeeds and shows a compatible installed PHP runtime.
- `bash scripts/php-lint-all.sh` exits `0`.
- `bash scripts/run-cli-assignments.sh` exits `0` and reports passing assertion totals for CLI assignments.
- `bash scripts/run-web-smoke.sh` exits `0` and confirms forms/http/session/regex flows.
- `bash scripts/run-db-smoke.sh` exits `0` and confirms schema bootstrap, registration, login, duplicate-email rejection, and logout.
- `test -f AGENTS.md` succeeds and the file content matches the draft intent above.

### Must Have
- one isolated assignment folder per brief
- deterministic test or smoke entrypoint per assignment
- no hidden manual steps
- explicit state reset for sessions and DB-backed work
- concrete user-facing success/error messages for regex and DB practicals

### Must NOT Have
- no monolithic “course app”
- no framework/ORM introduction without brief evidence
- no shared `src/` business layer across assignments
- no vague “open in browser and inspect” acceptance criteria
- no reliance on pre-existing DB/session state

## Verification Strategy
> ZERO HUMAN INTERVENTION — all verification is agent-executed.
- Test decision: tests-after with lightweight native PHP assertions and smoke scripts
- QA policy: Every task has agent-executed scenarios
- Evidence: `.sisyphus/evidence/task-{N}-{slug}.{ext}`

## Execution Strategy
### Parallel Execution Waves
> Target: 5-8 tasks per wave. <3 per wave (except final) = under-splitting.
> Extract shared dependencies as Wave-1 tasks for max parallelism.

Wave 1: bootstrap, repo rules, shared scripts, DB harness foundations
Wave 2: standalone CLI/topic assignments (01, 02, 03, 04, 06, 07)
Wave 3: mixed rendering/web assignments (05, 08, 09, 10, 12)
Wave 4: stateful/database assignments (11, 13)

### Dependency Matrix (full, all tasks)
- 1 blocks all downstream tasks
- 2 blocks 6,7,8,9,10,11,12,13,14,15,16,17,18
- 3 blocks CLI-oriented tasks 6,7,8,9,10,11,12
- 4 blocks web/stateful tasks 13,14,15,16,17,18
- 5 blocks 18
- 6,7,8,9,11,12 are execution-independent once 1,2,3 are done
- 10 is execution-independent once 1,2,3 are done
- 13,14,15,17 are execution-independent once 1,2,4 are done
- 16 is execution-independent once 1,2,4 are done
- 18 requires 1,2,4,5 and should start only after its dedicated smoke path is stable

### Agent Dispatch Summary (wave → task count → categories)
- Wave 1 → 5 tasks → quick / unspecified-low / writing
- Wave 2 → 6 tasks → quick / unspecified-low
- Wave 3 → 5 tasks → unspecified-low / unspecified-high / visual-engineering
- Wave 4 → 2 tasks → unspecified-high / deep

## TODOs
> Implementation + Test = ONE task. Never separate.
> EVERY task MUST have: Agent Profile + Parallelization + QA Scenarios.

- [x] 1. Bootstrap workspace layout and materialize AGENTS.md

  **What to do**: Create `assignments/`, `scripts/`, `database/`, and `.sisyphus/evidence/` layout; create all 13 numbered assignment directories; materialize repo-root `AGENTS.md` from the draft in this plan; add a minimal root `README.md` that explains local run entrypoints and the folder map.
  **Must NOT do**: Do not add frameworks, Composer packages, Docker, or shared assignment logic.

  **Recommended Agent Profile**:
  - Category: `writing` — Reason: mostly structure, docs, and rules materialization.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — no browser interaction required.

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18] | Blocked By: []

  **References**:
  - Source brief set: root `*.docx` files listed in `## Embedded Specification Pack`
  - Plan section: `## Repo Decisions (fixed)` — canonical folder names and runtime choices
  - Plan section: `## AGENTS.md Draft (to be materialized by executor at repo root)`

  **Acceptance Criteria**:
  - [ ] `test -d assignments/01-php-basics && test -d assignments/13-auth-db-app`
  - [ ] `test -d scripts && test -d database && test -d .sisyphus/evidence`
  - [ ] `test -f AGENTS.md && grep -q "Do not merge assignments into one app" AGENTS.md`
  - [ ] `test -f README.md`

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Workspace scaffold exists
    Tool: Bash
    Steps: run `test -d assignments/09-forms && test -d assignments/11-sessions && test -f AGENTS.md && test -f README.md`
    Expected: command exits 0
    Evidence: .sisyphus/evidence/task-1-bootstrap.txt

  Scenario: AGENTS draft was not truncated
    Tool: Bash
    Steps: run `grep -q "Shared scripts are allowed only under \`scripts/\`" AGENTS.md && grep -q "Stop and report if local MySQL is unavailable" AGENTS.md`
    Expected: command exits 0
    Evidence: .sisyphus/evidence/task-1-bootstrap-agents.txt
  ```

  **Commit**: YES | Message: `chore(repo): bootstrap assignment workspace and agent rules` | Files: [`AGENTS.md`, `README.md`, `assignments/`, `scripts/`, `database/`]

- [x] 2. Materialize per-assignment specs from the plan

  **What to do**: Create `SPEC.md` inside each of the 13 assignment folders, derived directly from the corresponding `S1..S13` entries in this plan. Each spec must list scope floor, fixed deliverable shape, forbidden overreach, verification commands, and source brief filename.
  **Must NOT do**: Do not paraphrase away explicit requirements; do not invent extra business goals beyond the brief.

  **Recommended Agent Profile**:
  - Category: `writing` — Reason: precise spec transcription from the normalized brief matrix.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — browser automation not required.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [6,7,8,9,10,11,12,13,14,15,16,17,18] | Blocked By: [1]

  **References**:
  - Plan section: `## Embedded Specification Pack`
  - Source briefs: corresponding root `.docx` file per assignment section
  - Plan section: `## Must NOT Have`

  **Acceptance Criteria**:
  - [ ] `test -f assignments/01-php-basics/SPEC.md && test -f assignments/13-auth-db-app/SPEC.md`
  - [ ] `grep -q "Source: Копия Основы PHP.docx" assignments/01-php-basics/SPEC.md`
  - [ ] `grep -q "email uniqueness" assignments/13-auth-db-app/SPEC.md`

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Spec files exist for all assignments
    Tool: Bash
    Steps: run `test $(functions(){ count=0; for f in assignments/*/SPEC.md; do count=$((count+1)); done; printf %s "$count"; }; functions) -eq 13`
    Expected: command exits 0 and count is 13
    Evidence: .sisyphus/evidence/task-2-specs.txt

  Scenario: Underspecified briefs have explicit defaults
    Tool: Bash
    Steps: run `grep -q "login: 3-20 chars" assignments/12-regex-validation/SPEC.md && grep -q "password_hash" assignments/13-auth-db-app/SPEC.md && grep -q "200/302/400/404" assignments/10-http-basics/SPEC.md`
    Expected: command exits 0
    Evidence: .sisyphus/evidence/task-2-spec-defaults.txt
  ```

  **Commit**: YES | Message: `docs(specs): materialize assignment spec pack` | Files: [`assignments/*/SPEC.md`]

- [x] 3. Create shared CLI verification harness

  **What to do**: Add shared scripts for linting all PHP files and running CLI-oriented assignment tests. Standardize `tests/run.php` for assignments `01,02,03,04,05,06,07` so the harness can execute them uniformly and capture assertion counts.
  **Must NOT do**: Do not introduce PHPUnit unless absolutely necessary; do not couple one assignment’s test runner to another assignment’s business logic.

  **Recommended Agent Profile**:
  - Category: `unspecified-low` — Reason: lightweight scripting plus reusable verification conventions.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — CLI only.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [6,7,8,9,10,11,12] | Blocked By: [1]

  **References**:
  - Plan section: `## Repo Decisions (fixed)` — shared tooling only, no shared business logic
  - Plan section: `## Definition of Done`
  - Specs: `assignments/01-php-basics/SPEC.md` through `assignments/07-standard-functions/SPEC.md`

  **Acceptance Criteria**:
  - [ ] `test -f scripts/php-lint-all.sh && test -f scripts/run-cli-assignments.sh`
  - [ ] `bash scripts/php-lint-all.sh` exits `0`
  - [ ] `bash scripts/run-cli-assignments.sh` exits `0` after tasks 6,7,8,9,10,11,12 are complete

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: CLI harness validates syntax
    Tool: Bash
    Steps: run `bash scripts/php-lint-all.sh`
    Expected: exit code 0 and no syntax errors
    Evidence: .sisyphus/evidence/task-3-cli-lint.txt

  Scenario: CLI harness fails on broken PHP
    Tool: Bash
    Steps: temporarily point the harness at a fixture file containing invalid PHP under a test fixture path, run `bash scripts/php-lint-all.sh`, then restore fixture
    Expected: harness exits non-zero and names the invalid file
    Evidence: .sisyphus/evidence/task-3-cli-lint-failure.txt
  ```

  **Commit**: YES | Message: `test(repo): add cli lint and assertion harness` | Files: [`scripts/php-lint-all.sh`, `scripts/run-cli-assignments.sh`, `assignments/*/tests/`]

- [x] 4. Create shared web smoke harness

  **What to do**: Add `scripts/serve-assignment.sh` and `scripts/run-web-smoke.sh` so assignments `08,09,10,11,12,13` can be served and smoke-tested consistently with exact ports and no manual browser dependency by default.
  **Must NOT do**: Do not create a central router or shared application kernel.

  **Recommended Agent Profile**:
  - Category: `unspecified-low` — Reason: scripting and local runtime orchestration.
  - Skills: `["playwright"]` — available for the browser-dependent smoke flows.
  - Omitted: `[]` — all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [13,14,15,16,17,18] | Blocked By: [1]

  **References**:
  - Plan section: `## Definition of Done`
  - Specs: `assignments/08-string-generation/SPEC.md` through `assignments/13-auth-db-app/SPEC.md`
  - Plan section: `## Verification Strategy`

  **Acceptance Criteria**:
  - [ ] `test -f scripts/serve-assignment.sh && test -f scripts/run-web-smoke.sh`
  - [ ] `bash scripts/serve-assignment.sh 09-forms 8091` serves the correct assignment root
  - [ ] `bash scripts/run-web-smoke.sh` exits `0` after tasks 13,14,15,16,17,18 are complete

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Serve a web assignment on a fixed port
    Tool: Bash
    Steps: start `bash scripts/serve-assignment.sh 08-string-generation 8090`, request `http://127.0.0.1:8090`, then stop the server
    Expected: HTTP 200 from the correct assignment folder
    Evidence: .sisyphus/evidence/task-4-serve.txt

  Scenario: Unknown assignment slug is rejected
    Tool: Bash
    Steps: run `bash scripts/serve-assignment.sh does-not-exist 8099`
    Expected: non-zero exit with a clear unknown-assignment message
    Evidence: .sisyphus/evidence/task-4-serve-error.txt
  ```

  **Commit**: YES | Message: `test(repo): add web serving and smoke harness` | Files: [`scripts/serve-assignment.sh`, `scripts/run-web-smoke.sh`]

- [x] 5. Create database bootstrap and reset harness

  **What to do**: Add DB bootstrap assets for assignment `13-auth-db-app`: `database/13-auth-db-app/schema.sql`, `database/13-auth-db-app/reset.sql` or equivalent reset flow, plus `scripts/reset-auth-db.sh` and `scripts/run-db-smoke.sh`. The harness must create a clean database, recreate the `users` table, and allow repeatable smoke runs.
  **Must NOT do**: Do not share this database with other assignments; do not hardcode credentials directly into committed source files.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: environment-sensitive DB setup with repeatable reset guarantees.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — DB harness first, browser not required yet.

  **Parallelization**: Can Parallel: YES | Wave 1 | Blocks: [18] | Blocked By: [1]

  **References**:
  - Spec: `assignments/13-auth-db-app/SPEC.md`
  - Plan section: `### S13. 13-auth-db-app`
  - Plan section: `## Repo Decisions (fixed)` — dedicated DB per assignment

  **Acceptance Criteria**:
  - [ ] `test -f database/13-auth-db-app/schema.sql && test -f scripts/reset-auth-db.sh && test -f scripts/run-db-smoke.sh`
  - [ ] `bash scripts/reset-auth-db.sh` exits `0`
  - [ ] `bash scripts/run-db-smoke.sh` exits `0` after task 18 is complete

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Database reset is repeatable
    Tool: Bash
    Steps: run `bash scripts/reset-auth-db.sh && bash scripts/reset-auth-db.sh`
    Expected: both runs exit 0 and leave an empty users table
    Evidence: .sisyphus/evidence/task-5-db-reset.txt

  Scenario: Missing DB connection fails clearly
    Tool: Bash
    Steps: run the reset script with intentionally invalid DB env vars in a subshell
    Expected: non-zero exit and explicit connection/setup error message
    Evidence: .sisyphus/evidence/task-5-db-reset-error.txt
  ```

  **Commit**: YES | Message: `chore(db): add auth database bootstrap and smoke scripts` | Files: [`database/13-auth-db-app/`, `scripts/reset-auth-db.sh`, `scripts/run-db-smoke.sh`]

- [x] 6. Implement assignment 01-php-basics

  **What to do**: Build `assignments/01-php-basics/` with deterministic scripts covering all six basics exercises from `S1`, plus `tests/run.php` assertions that verify each expected computation/output block.
  **Must NOT do**: Do not add forms, HTTP handling, or database code.

  **Recommended Agent Profile**:
  - Category: `quick` — Reason: small standalone beginner tasks with deterministic outputs.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — CLI-only assignment.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [] | Blocked By: [1,2,3]

  **References**:
  - Spec: `assignments/01-php-basics/SPEC.md`
  - Source: `Копия Основы PHP.docx`
  - Harness: `scripts/run-cli-assignments.sh`

  **Acceptance Criteria**:
  - [ ] `php assignments/01-php-basics/tests/run.php` exits `0`
  - [ ] `php assignments/01-php-basics/index.php | grep -q "Rectangle perimeter"`
  - [ ] circle and rectangle calculations match expected values in the test runner

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Happy-path basics assertions pass
    Tool: Bash
    Steps: run `php assignments/01-php-basics/tests/run.php`
    Expected: exit code 0 and output starts with `OK:`
    Evidence: .sisyphus/evidence/task-6-php-basics.txt

  Scenario: UTF-8 string length remains correct
    Tool: Bash
    Steps: run a test case in `tests/run.php` using a Cyrillic sample string
    Expected: reported length matches visible character count, not byte count
    Evidence: .sisyphus/evidence/task-6-php-basics-utf8.txt
  ```

  **Commit**: YES | Message: `feat(assignment-01): implement php basics exercises` | Files: [`assignments/01-php-basics/`]

- [x] 7. Implement assignment 02-control-structures

  **What to do**: Build `assignments/02-control-structures/` with one deterministic output entrypoint and tests for month-to-season, first-character check, lucky-number logic, salary transformations, `1..100`, filtering, sum, and arithmetic mean.
  **Must NOT do**: Do not accept non-six-digit input as lucky-number success; do not silently accept invalid months.

  **Recommended Agent Profile**:
  - Category: `quick` — Reason: straightforward branching/loop exercises.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — CLI-only assignment.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [] | Blocked By: [1,2,3]

  **References**:
  - Spec: `assignments/02-control-structures/SPEC.md`
  - Source: `Управляющие конструкции.docx`
  - Plan section: `### S2. 02-control-structures`

  **Acceptance Criteria**:
  - [ ] `php assignments/02-control-structures/tests/run.php` exits `0`
  - [ ] invalid month input yields an explicit error/invalid-range result
  - [ ] lucky-number tests cover one valid and one invalid six-digit example

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Control structure assertions pass
    Tool: Bash
    Steps: run `php assignments/02-control-structures/tests/run.php`
    Expected: exit code 0 and assertion summary printed
    Evidence: .sisyphus/evidence/task-7-control.txt

  Scenario: Invalid month is rejected
    Tool: Bash
    Steps: include month `13` in the test runner and execute it
    Expected: the case is marked invalid and does not map to a season
    Evidence: .sisyphus/evidence/task-7-control-invalid-month.txt
  ```

  **Commit**: YES | Message: `feat(assignment-02): implement control structure exercises` | Files: [`assignments/02-control-structures/`]

- [x] 8. Implement assignment 03-arrays

  **What to do**: Build `assignments/03-arrays/` to cover all listed array tasks, including no-loop sum for `1..100`, shuffled ranges, nonrepeating alphabet generation, and multiple sort demonstrations with assertions.
  **Must NOT do**: Do not use loops for the explicit no-loop sum task; do not allow duplicate letters in the random alphabet output.

  **Recommended Agent Profile**:
  - Category: `unspecified-low` — Reason: moderate breadth of array operations with deterministic assertions.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — CLI-only assignment.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [] | Blocked By: [1,2,3]

  **References**:
  - Spec: `assignments/03-arrays/SPEC.md`
  - Source: `Массивы.docx`
  - Plan section: `### S3. 03-arrays`

  **Acceptance Criteria**:
  - [ ] `php assignments/03-arrays/tests/run.php` exits `0`
  - [ ] the test runner proves the alphabet output contains 26 unique letters
  - [ ] sort demonstrations include key-preserving and reindexing variants

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Array assignment assertions pass
    Tool: Bash
    Steps: run `php assignments/03-arrays/tests/run.php`
    Expected: exit code 0 and assertion summary printed
    Evidence: .sisyphus/evidence/task-8-arrays.txt

  Scenario: Duplicate-letter guard catches bad alphabet generation
    Tool: Bash
    Steps: execute a negative test in `tests/run.php` against a fixture alphabet containing duplicates
    Expected: duplicate detection fails that case explicitly
    Evidence: .sisyphus/evidence/task-8-arrays-duplicate.txt
  ```

  **Commit**: YES | Message: `feat(assignment-03): implement array exercises` | Files: [`assignments/03-arrays/`]

- [x] 9. Implement assignment 04-associative-arrays

  **What to do**: Build `assignments/04-associative-arrays/` with deterministic demonstrations for keyed arrays, months, personal data, current date formatting, key-hole behavior, counts, and last/penultimate extraction.
  **Must NOT do**: Do not hardcode January to any key other than `1`; do not ignore associative-array count cases.

  **Recommended Agent Profile**:
  - Category: `quick` — Reason: compact keyed-array exercise set.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — CLI-only assignment.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [] | Blocked By: [1,2,3]

  **References**:
  - Spec: `assignments/04-associative-arrays/SPEC.md`
  - Source: `Ассоциативные массивы.docx`
  - Plan section: `### S4. 04-associative-arrays`

  **Acceptance Criteria**:
  - [ ] `php assignments/04-associative-arrays/tests/run.php` exits `0`
  - [ ] the generated months structure uses key `1` for January
  - [ ] the date output matches `YYYY-MM-DD`

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Associative-array assertions pass
    Tool: Bash
    Steps: run `php assignments/04-associative-arrays/tests/run.php`
    Expected: exit code 0 and assertion summary printed
    Evidence: .sisyphus/evidence/task-9-assoc.txt

  Scenario: Missing penultimate element is handled safely
    Tool: Bash
    Steps: include a negative test using a one-element array
    Expected: the code returns a safe failure/empty-state result rather than a notice-driven success
    Evidence: .sisyphus/evidence/task-9-assoc-edge.txt
  ```

  **Commit**: YES | Message: `feat(assignment-04): implement associative array exercises` | Files: [`assignments/04-associative-arrays/`]

- [x] 10. Implement assignment 05-multidimensional-arrays

  **What to do**: Build `assignments/05-multidimensional-arrays/` covering 2D summation, nested salary extraction, books dataset rendering, disciplines HTML table rendering, and `group - user` nested-loop output.
  **Must NOT do**: Do not flatten nested data prematurely; do not render the disciplines task as plain text instead of a table.

  **Recommended Agent Profile**:
  - Category: `unspecified-low` — Reason: mixed CLI/data traversal plus one HTML output requirement.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — browser not required for validation.

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: [] | Blocked By: [1,2,3]

  **References**:
  - Spec: `assignments/05-multidimensional-arrays/SPEC.md`
  - Source: `Многомерность.docx`
  - Plan section: `### S5. 05-multidimensional-arrays`

  **Acceptance Criteria**:
  - [ ] `php assignments/05-multidimensional-arrays/tests/run.php` exits `0`
  - [ ] `php assignments/05-multidimensional-arrays/index.php | grep -q "<table"`
  - [ ] the group output contains `group name - user name` formatting

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Multidimensional-array assertions pass
    Tool: Bash
    Steps: run `php assignments/05-multidimensional-arrays/tests/run.php`
    Expected: exit code 0 and assertion summary printed
    Evidence: .sisyphus/evidence/task-10-multi.txt

  Scenario: Empty nested list does not break rendering
    Tool: Bash
    Steps: execute a fixture case with an empty subgroup/books list
    Expected: output remains valid and the test runner reports a handled empty state
    Evidence: .sisyphus/evidence/task-10-multi-empty.txt
  ```

  **Commit**: YES | Message: `feat(assignment-05): implement multidimensional array exercises` | Files: [`assignments/05-multidimensional-arrays/`]

- [x] 11. Implement assignment 06-user-functions

  **What to do**: Build `assignments/06-user-functions/` with named functions for all brief requirements and a deterministic test runner covering positive/negative markers, math helpers, adjacency detection, digit sums, and prime checks.
  **Must NOT do**: Do not collapse everything into anonymous inline logic; do not omit return-value tests.

  **Recommended Agent Profile**:
  - Category: `quick` — Reason: isolated logic with clear function contracts.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — CLI-only assignment.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [] | Blocked By: [1,2,3]

  **References**:
  - Spec: `assignments/06-user-functions/SPEC.md`
  - Source: `Пользовательские функции.docx`
  - Plan section: `### S6. 06-user-functions`

  **Acceptance Criteria**:
  - [ ] `php assignments/06-user-functions/tests/run.php` exits `0`
  - [ ] the positive/negative function emits `+++` and `---` exactly
  - [ ] prime-check assertions include both prime and composite numbers

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: User-function assertions pass
    Tool: Bash
    Steps: run `php assignments/06-user-functions/tests/run.php`
    Expected: exit code 0 and assertion summary printed
    Evidence: .sisyphus/evidence/task-11-functions.txt

  Scenario: Adjacent-duplicate detection catches repeated neighbors
    Tool: Bash
    Steps: include `[1,2,2,3]` and `[1,2,3,2]` in the negative/positive test cases
    Expected: first case reports adjacent duplicates, second does not
    Evidence: .sisyphus/evidence/task-11-functions-adjacent.txt
  ```

  **Commit**: YES | Message: `feat(assignment-06): implement user function exercises` | Files: [`assignments/06-user-functions/`]

- [x] 12. Implement assignment 07-standard-functions

  **What to do**: Build `assignments/07-standard-functions/` with mb-safe string handling where relevant, numeric/date utilities, prefix detection, square-root mapping, and a year-agnostic New Year countdown.
  **Must NOT do**: Do not hardcode a calendar year into the countdown; do not use byte-oriented string logic for Cyrillic-sensitive cases.

  **Recommended Agent Profile**:
  - Category: `unspecified-low` — Reason: moderate built-in function coverage with date/string edge cases.
  - Skills: `[]` — no special skill required.
  - Omitted: `["playwright"]` — CLI-only assignment.

  **Parallelization**: Can Parallel: YES | Wave 2 | Blocks: [] | Blocked By: [1,2,3]

  **References**:
  - Spec: `assignments/07-standard-functions/SPEC.md`
  - Source: `Стандартные функции PHP.docx`
  - Plan section: `### S7. 07-standard-functions`

  **Acceptance Criteria**:
  - [ ] `php assignments/07-standard-functions/tests/run.php` exits `0`
  - [ ] the countdown test proves the logic works when the current year changes
  - [ ] the `http` prefix check distinguishes matching and non-matching strings

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Standard-function assertions pass
    Tool: Bash
    Steps: run `php assignments/07-standard-functions/tests/run.php`
    Expected: exit code 0 and assertion summary printed
    Evidence: .sisyphus/evidence/task-12-standard.txt

  Scenario: Cyrillic last-character uppercase stays mb-safe
    Tool: Bash
    Steps: include a Cyrillic sample string in `tests/run.php`
    Expected: the final character uppercases correctly without mojibake
    Evidence: .sisyphus/evidence/task-12-standard-utf8.txt
  ```

  **Commit**: YES | Message: `feat(assignment-07): implement standard function exercises` | Files: [`assignments/07-standard-functions/`]

- [x] 13. Implement assignment 08-string-generation

  **What to do**: Build `assignments/08-string-generation/public/index.php` to render every required HTML-generation exercise: paragraphs, image tags, list items, select/options, current-date paragraph, conditional div, mixed PHP/HTML list, and repeated user cards.
  **Must NOT do**: Do not replace HTML generation with raw JSON/text output; do not omit the mixed PHP/HTML variant.

  **Recommended Agent Profile**:
  - Category: `visual-engineering` — Reason: HTML output correctness matters even for a simple training assignment.
  - Skills: `["playwright"]` — useful for smoke verification of rendered HTML.
  - Omitted: `[]` — all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: [] | Blocked By: [1,2,4]

  **References**:
  - Spec: `assignments/08-string-generation/SPEC.md`
  - Source: `Формирование строк.docx`
  - Harness: `scripts/serve-assignment.sh`

  **Acceptance Criteria**:
  - [ ] `bash scripts/serve-assignment.sh 08-string-generation 8090` serves the page successfully
  - [ ] `curl -s http://127.0.0.1:8090 | grep -q "<select"`
  - [ ] `curl -s http://127.0.0.1:8090 | grep -q "<img"`

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Rendered HTML contains required structures
    Tool: Bash
    Steps: start `bash scripts/serve-assignment.sh 08-string-generation 8090`, run `curl -s http://127.0.0.1:8090`, inspect for `<p>`, `<img`, `<ul>`, `<select>`, then stop the server
    Expected: all required tags are present in the response
    Evidence: .sisyphus/evidence/task-13-strings.html

  Scenario: Conditional block disappears when disabled
    Tool: Bash
    Steps: invoke the page in its disabled fixture/test mode defined by the assignment test runner
    Expected: the target `<div>` is absent while the page still renders valid HTML
    Evidence: .sisyphus/evidence/task-13-strings-conditional.html
  ```

  **Commit**: YES | Message: `feat(assignment-08): implement string generation exercises` | Files: [`assignments/08-string-generation/`]

- [ ] 14. Implement assignment 09-forms

  **What to do**: Build `assignments/09-forms/public/` with the full form set from `S9`, including GET and POST variants, a `result.php` flow where required, numeric sum handling, password comparison, checkbox/radio cases, temperature conversion, birthday countdown, and textarea metrics.
  **Must NOT do**: Do not collapse GET/POST into one unverified path; do not skip empty/invalid input handling for numeric/date fields.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: multiple small form flows with distinct validation and request-method behavior.
  - Skills: `["playwright"]` — helpful for form submission QA.
  - Omitted: `[]` — all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: [] | Blocked By: [1,2,4]

  **References**:
  - Spec: `assignments/09-forms/SPEC.md`
  - Source: `Формы.docx`
  - Plan section: `### S9. 09-forms`

  **Acceptance Criteria**:
  - [ ] `bash scripts/serve-assignment.sh 09-forms 8091` serves the form set
  - [ ] GET and POST variants of the first task both complete successfully
  - [ ] birthday parsing accepts `dd.mm.yyyy` and returns days until next birthday

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: GET and POST form flows both work
    Tool: Bash
    Steps: start `bash scripts/serve-assignment.sh 09-forms 8091`; submit the first task with `curl` once by GET and once by POST using `name=Ivan&age=20&salary=500`; stop the server
    Expected: both responses echo the submitted values and are not method-confused
    Evidence: .sisyphus/evidence/task-14-forms-get-post.txt

  Scenario: Invalid birthday input is rejected cleanly
    Tool: Bash
    Steps: submit `birthday=31.02.1990` to the birthday handler
    Expected: explicit validation error, no PHP warnings/notices in the response
    Evidence: .sisyphus/evidence/task-14-forms-birthday-error.txt
  ```

  **Commit**: YES | Message: `feat(assignment-09): implement form handling exercises` | Files: [`assignments/09-forms/`]

- [ ] 15. Implement assignment 10-http-basics

  **What to do**: Build `assignments/10-http-basics/public/` with explicit endpoints for request-method detection, request-header display, all-header listing, and status responses for at least `200`, `302`, `400`, and `404`, verified by `curl`.
  **Must NOT do**: Do not emit body content before headers are set; do not rely on browser-only verification.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: header/status behavior is easy to get subtly wrong.
  - Skills: `[]` — curl-based verification is primary.
  - Omitted: `["playwright"]` — browser is optional, not required.

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: [] | Blocked By: [1,2,4]

  **References**:
  - Spec: `assignments/10-http-basics/SPEC.md`
  - Source: `Работа с HTTP.docx`
  - Plan section: `### S10. 10-http-basics`

  **Acceptance Criteria**:
  - [ ] `bash scripts/serve-assignment.sh 10-http-basics 8092` serves the endpoints
  - [ ] `curl -i http://127.0.0.1:8092/status/404` returns `HTTP/1.1 404`
  - [ ] `curl -s -H 'Accept-Language: ru' http://127.0.0.1:8092/headers | grep -q 'Accept-Language'`

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Status endpoints return exact codes
    Tool: Bash
    Steps: start `bash scripts/serve-assignment.sh 10-http-basics 8092`; request `/status/200`, `/status/302`, `/status/400`, `/status/404` with `curl -i`; stop the server
    Expected: each response returns its exact intended HTTP status
    Evidence: .sisyphus/evidence/task-15-http-status.txt

  Scenario: Method detection distinguishes POST from GET
    Tool: Bash
    Steps: call the method endpoint once with GET and once with `curl -X POST`
    Expected: responses explicitly identify the correct method each time
    Evidence: .sisyphus/evidence/task-15-http-method.txt
  ```

  **Commit**: YES | Message: `feat(assignment-10): implement http exercises` | Files: [`assignments/10-http-basics/`]

- [ ] 16. Implement assignment 11-sessions

  **What to do**: Build `assignments/11-sessions/public/` with all session-based flows from `S11`: country persistence, seconds-since-entry counter, email carry-over form flow, refresh counter, city/age prefill flow, `logout.php`, and a multi-page quiz that stores answers in session.
  **Must NOT do**: Do not output content before `session_start`; do not mix multiple exercises into one irrecoverable session namespace without per-flow keys.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: stateful multi-page behavior and resettable session handling.
  - Skills: `["playwright"]` — useful for session/cookie flow verification.
  - Omitted: `[]` — all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 4 | Blocks: [] | Blocked By: [1,2,4]

  **References**:
  - Spec: `assignments/11-sessions/SPEC.md`
  - Source: `Сессии в PHP.docx`
  - Plan section: `### S11. 11-sessions`

  **Acceptance Criteria**:
  - [ ] `bash scripts/serve-assignment.sh 11-sessions 8093` serves the session app
  - [ ] the logout flow destroys the session and clears protected state
  - [ ] the quiz stores answers across multiple requests using one session

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Session persists and logout resets it
    Tool: Bash
    Steps: start `bash scripts/serve-assignment.sh 11-sessions 8093`; use `curl -c cookies.txt -b cookies.txt` to submit country data, read it back on `test.php`, then hit `logout.php` and request `test.php` again; stop the server
    Expected: first read shows stored country, post-logout read shows cleared session state
    Evidence: .sisyphus/evidence/task-16-sessions.txt

  Scenario: First-visit counter message changes on refresh
    Tool: Bash
    Steps: use one cookie jar to request the counter page twice
    Expected: first response shows first-visit message, second response shows incremented count instead
    Evidence: .sisyphus/evidence/task-16-sessions-counter.txt
  ```

  **Commit**: YES | Message: `feat(assignment-11): implement session exercises` | Files: [`assignments/11-sessions/`]

- [ ] 17. Implement assignment 12-regex-validation

  **What to do**: Build `assignments/12-regex-validation/public/` as one form with fields `email`, `login`, `password`, `phone`, applying the fixed regex policy from `S12`, normalizing phone input before digit-length validation, and showing a binary success/error result.
  **Must NOT do**: Do not weaken the regex rules below the fixed defaults; do not accept a login that starts with a digit.

  **Recommended Agent Profile**:
  - Category: `unspecified-high` — Reason: validation details are underspecified in the source brief and now fixed by plan.
  - Skills: `["playwright"]` — useful for realistic form validation checks.
  - Omitted: `[]` — all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 3 | Blocks: [] | Blocked By: [1,2,4]

  **References**:
  - Spec: `assignments/12-regex-validation/SPEC.md`
  - Source: `Тема_11_Практическая_работа_Реглярные_выражения.docx`
  - Plan section: `### S12. 12-regex-validation`

  **Acceptance Criteria**:
  - [ ] `bash scripts/serve-assignment.sh 12-regex-validation 8094` serves the regex form
  - [ ] valid payload `email=test@example.com&login=ivan_123&password=pass1234&phone=+79991234567` succeeds
  - [ ] invalid payloads for each field produce explicit error messages

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Valid regex form submission succeeds
    Tool: Bash
    Steps: start `bash scripts/serve-assignment.sh 12-regex-validation 8094`; submit `email=test@example.com&login=ivan_123&password=pass1234&phone=+79991234567` by POST with `curl`; stop the server
    Expected: response contains a success message and no field errors
    Evidence: .sisyphus/evidence/task-17-regex-success.txt

  Scenario: Invalid login is rejected
    Tool: Bash
    Steps: submit `login=1bad` with otherwise valid data
    Expected: response contains a login-specific validation error and does not show global success
    Evidence: .sisyphus/evidence/task-17-regex-login-error.txt
  ```

  **Commit**: YES | Message: `feat(assignment-12): implement regex validation practical` | Files: [`assignments/12-regex-validation/`]

- [ ] 18. Implement assignment 13-auth-db-app

  **What to do**: Build `assignments/13-auth-db-app/public/` with registration, login, authenticated user-info display, logout, clear user-facing state messages, and DB persistence using the fixed `users(id, full_name, email, password_hash, created_at)` schema plus the DB harness from task 5.
  **Must NOT do**: Do not store plain-text passwords; do not allow duplicate email registration; do not share session or DB state with other assignments.

  **Recommended Agent Profile**:
  - Category: `deep` — Reason: stateful DB-backed auth flow with correctness and data-integrity requirements.
  - Skills: `["playwright"]` — helpful for end-to-end auth flow verification.
  - Omitted: `[]` — all listed skills are relevant.

  **Parallelization**: Can Parallel: YES | Wave 4 | Blocks: [] | Blocked By: [1,2,4,5]

  **References**:
  - Spec: `assignments/13-auth-db-app/SPEC.md`
  - Source: `Базы данных.docx`
  - Plan section: `### S13. 13-auth-db-app`
  - DB harness: `database/13-auth-db-app/schema.sql`, `scripts/reset-auth-db.sh`, `scripts/run-db-smoke.sh`

  **Acceptance Criteria**:
  - [ ] `bash scripts/reset-auth-db.sh` exits `0`
  - [ ] registration creates one DB row with hashed password
  - [ ] duplicate email registration is rejected with a clear message
  - [ ] login shows persisted user information and logout clears access

  **QA Scenarios** (MANDATORY — task incomplete without these):
  ```
  Scenario: Registration → login → logout works end to end
    Tool: Bash
    Steps: run `bash scripts/reset-auth-db.sh`; start `bash scripts/serve-assignment.sh 13-auth-db-app 8095`; register `full_name=Ivan Petrov&email=test@example.com&password=pass1234`; log in with the same credentials; request the logout route; stop the server
    Expected: registration succeeds, login shows stored user info, logout clears the authenticated state
    Evidence: .sisyphus/evidence/task-18-auth-happy.txt

  Scenario: Duplicate email is blocked
    Tool: Bash
    Steps: after one successful registration, submit the same `email=test@example.com` again with any name/password
    Expected: response contains a duplicate-email error, and an SQL assertion confirms only one row exists for that email
    Evidence: .sisyphus/evidence/task-18-auth-duplicate.txt
  ```

  **Commit**: YES | Message: `feat(assignment-13): implement database auth practical` | Files: [`assignments/13-auth-db-app/`, `database/13-auth-db-app/`]

## Final Verification Wave (MANDATORY — after ALL implementation tasks)
> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.
> **Do NOT auto-proceed after verification. Wait for user's explicit approval before marking work complete.**
> **Never mark F1-F4 as checked before getting user's okay.** Rejection or user feedback -> fix -> re-run -> present again -> wait for okay.
- [ ] F1. Plan Compliance Audit — oracle
- [ ] F2. Code Quality Review — unspecified-high
- [ ] F3. Real Manual QA — unspecified-high (+ playwright if UI)
- [ ] F4. Scope Fidelity Check — deep

## Commit Strategy
- Create one repo bootstrap commit after foundational folders/scripts/AGENTS materialization are in place.
- Use one commit per assignment after its implementation + verification passes.
- Keep DB bootstrap/schema changes separate from auth feature changes unless inseparable.
- Use conventional commit format: `feat(assignment-xx): ...`, `chore(repo): ...`, `test(assignment-xx): ...`, `docs(repo): ...`.

## Success Criteria
- All 13 assignment folders exist and map 1:1 to the source briefs.
- Each assignment has deterministic verification and evidence artifacts.
- `AGENTS.md` exists at repo root and matches the plan intent.
- Regex and DB practicals handle both success and failure paths.
- No shared runtime/business logic leaks across assignment folders.
