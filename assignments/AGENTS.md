# ASSIGNMENTS KNOWLEDGE BASE

## Overview
- `assignments/` contains the 13 isolated deliverables; treat each subfolder as its own app boundary.

## Structure
```text
assignments/
├── 01-php-basics ... 07-standard-functions   # CLI exercises
├── 08-string-generation/                      # public/
├── 09-forms/                                 # public/ + src/
├── 10-http-basics/                           # public/ (single-file router)
├── 11-sessions/                              # public/ + src/
├── 12-regex-validation/                      # public/
└── 13-auth-db-app/                           # public/ + src/
```

## Where To Look
| Task | Location | Notes |
| --- | --- | --- |
| CLI output exercises | `01-*` ... `07-*` | Expect root `index.php` plus `tests/run.php` |
| HTML generation | `08-string-generation/` | Single `public/index.php` app |
| Form parsing/helpers | `09-forms/` | `public/` + small `src/helpers.php` |
| HTTP status/header work | `10-http-basics/` | Single-file router under `public/index.php` |
| Session flows | `11-sessions/` | Multi-page `public/` plus `src/bootstrap.php` |
| Regex validation | `12-regex-validation/` | Single form endpoint |
| Auth + DB app | `13-auth-db-app/` | App code here, schema lives under `database/13-auth-db-app/` |

## Conventions
- Never import assignment business logic from a sibling assignment.
- Keep numbering and ASCII slugs exactly as defined by the plan.
- CLI assignments favor deterministic text output and local assertion runners.
- Browser assignments must expose real HTTP-verifiable behavior, not static mock pages.
- `src/` appears only where flow complexity justifies helpers; do not add it gratuitously to simple assignments.

## Verification Pattern
- `01`-`07`: `php assignments/<slug>/tests/run.php`
- `08`-`13`: serve from `public/`, verify with `curl` or shared smoke scripts
- `13`: pair app verification with DB reset/smoke scripts and SQL assertions

## Anti-Patterns
- Converting this folder into one monolithic app.
- Sharing runtime state, helpers, or persistence logic across assignment folders.
- Replacing required browser flows with CLI-only output.
- Skipping failure-path verification because a happy-path page renders.
