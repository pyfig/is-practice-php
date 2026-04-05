# PHP Practical Assignments Workspace

This repository keeps each practical assignment in its own isolated folder under `assignments/`.

## Folder map
- `assignments/` contains the 13 numbered assignment workspaces.
- `scripts/` is the only place for shared helper scripts and verification helpers.
- `database/` is reserved for database bootstrap and reset assets used by the database assignment.
- `.sisyphus/evidence/` stores task evidence collected during execution.
- Root `.docx` files remain the source briefs and should not be edited.

## Local entrypoints
- CLI assignments should expose deterministic output and a `tests/run.php` runner inside the target assignment folder.
- Browser assignments should expose a `public/` entrypoint and run with PHP's built-in server from that assignment folder.
- The database assignment should keep its own reset and bootstrap assets and must not reuse state from other assignments.

## Working rules
- Keep all files in UTF-8 without BOM.
- Preserve visible Russian learner-facing text from the briefs.
- Don't merge assignments into one app.
- Don't share assignment business logic across folders.

## Numbered assignments
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
