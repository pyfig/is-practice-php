# 11-SESSIONS NOTES

## Overview
- Session assignment with multiple public pages sharing one bootstrap and isolated session namespaces.

## Where To Look
| Task | Location | Notes |
| --- | --- | --- |
| Common helpers | `src/bootstrap.php` | Starts session, escaping, redirects, shared rendering |
| Country flow | `public/index.php`, `public/test.php` | Basic session persistence |
| Email flow | `public/email-step1.php`, `public/email-step2.php` | Two-step carry-over |
| Profile prefill flow | `public/profile-step1.php`, `public/profile-step2.php` | Multi-field persistence |
| Quiz flow | `public/quiz-step1.php`, `public/quiz-step2.php`, `public/quiz-result.php` | Multi-page answer storage |
| Session reset | `public/logout.php` | Destroy session safely |

## Conventions
- Require `src/bootstrap.php` before any output.
- Keep exercise data partitioned by namespace keys so one flow cannot corrupt another.
- Preserve UTF-8 output and Russian visible labels.
- Redirects use 303 semantics via the shared helper; keep that behavior consistent.

## Verification
- Serve via `bash scripts/serve-assignment.sh 11-sessions <port>`.
- Prefer cookie-aware `curl -c/-b` checks for stateful flows.
- Verify both first-visit and repeat-visit/session-present behavior.

## Anti-Patterns
- Output before `session_start()`.
- Mixing all exercises into one flat `$_SESSION` structure.
- Inlining duplicate HTML helpers into each public page.
