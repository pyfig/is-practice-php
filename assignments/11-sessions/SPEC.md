# 11 Sessions Specification

Source: Сессии в PHP.docx

## Assignment goal
Practice session persistence, counters, prefills, logout, and a multi-page quiz.

## Scope floor
- Country form on `index.php`, display on `test.php`.
- Show seconds since first site entry.
- Carry email from one form to another.
- Refresh counter with a first-visit message.
- City and age form, then prefilled `Name/Age/City` form.
- `logout.php` destroys the session.
- Multi-page quiz storing answers in session.

## Deliverable shape
- Browser-oriented assignment with `public/` entrypoint.
- Multiple pages as needed for session flows.
- Curl-verifiable session behavior.

## Validations and fixed defaults
- No output before `session_start`.
- All files must stay UTF-8 without BOM.
- Session keys should stay separated enough that one exercise does not corrupt another.

## Forbidden overreach
- Do not output content before `session_start`.
- Do not mix all exercises into one irrecoverable session namespace.
- No database-backed auth logic here.

## Verification entrypoints
- `bash scripts/serve-assignment.sh 11-sessions 8093`
- `curl -c cookies.txt -b cookies.txt http://127.0.0.1:8093`
- `php -l assignments/11-sessions/public/index.php`
