# 10 HTTP Basics Specification

Source: Работа с HTTP.docx

## Assignment goal
Practice request method detection, header inspection, and HTTP status responses.

## Scope floor
- Distinguish GET from POST.
- Read `Accept` and `Accept-Language`.
- List all request headers.
- Send `404`.
- Send corresponding status codes.

## Deliverable shape
- Browser-oriented assignment with `public/` entrypoint.
- Explicit demo endpoints for method checks, header inspection, and status responses.
- Curl-verifiable behavior.

## Validations and fixed defaults
- Because starter code is absent, the assignment must expose explicit demo endpoints for `200/302/400/404`.
- Headers must be sent before any body content.
- Verification is done with `curl`, not browser-only inspection.

## Forbidden overreach
- Do not emit body content before headers are set.
- Do not rely on browser-only verification.
- Do not add unrelated application behavior.

## Verification entrypoints
- `bash scripts/serve-assignment.sh 10-http-basics 8092`
- `curl -i http://127.0.0.1:8092/status/404`
- `php -l assignments/10-http-basics/public/index.php`
