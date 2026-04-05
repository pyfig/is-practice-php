# 08 String Generation Specification

Source: Формирование строк.docx

## Assignment goal
Generate HTML from PHP variables, loops, and conditions.

## Scope floor
- Render three variables as paragraphs.
- Render three image tags from source variables.
- Render a list `1..5`.
- Render a `<select>` from an array.
- Render the current date paragraph in `year-month-day` format.
- Render a conditional `<div>` when `show=true`.
- Include a mixed PHP and HTML list variant.
- Render repeated user cards from a users array.

## Deliverable shape
- Browser-oriented assignment with `public/` entrypoint.
- HTML response, not JSON.
- Smoke-verifiable output.

## Validations and fixed defaults
- The conditional block must be present only when `show=true`.
- The mixed PHP and HTML list variant is mandatory, not optional.

## Forbidden overreach
- Do not replace HTML generation with JSON or plain text.
- Do not omit the mixed PHP and HTML variant.
- No sessions or database logic.

## Verification entrypoints
- `bash scripts/serve-assignment.sh 08-string-generation 8090`
- `curl -s http://127.0.0.1:8090`
- `php -l assignments/08-string-generation/public/index.php`
