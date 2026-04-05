# 07 Standard Functions Specification

Source: Стандартные функции PHP.docx

## Assignment goal
Practice built-in string, array, math, and date functions.

## Scope floor
- Compute the average of a numeric array.
- Sum `1..100`.
- Print `1..100`.
- Uppercase the last character of a string.
- Compute square roots for a numeric array.
- Find array min and max.
- Generate a random number.
- Detect the `http` prefix.
- Output current year, month, day, hour, minute, and second.
- Compute days until New Year for any year.

## Deliverable shape
- Deterministic CLI entrypoint.
- `tests/run.php` assertion runner.

## Validations and fixed defaults
- String tasks must be `mb_*` safe when Cyrillic examples are used.
- The New Year countdown must not hardcode a specific year.

## Forbidden overreach
- Do not use byte-oriented string logic for Cyrillic-sensitive cases.
- Do not hardcode a calendar year into the countdown.
- No web, session, or database behavior.

## Verification entrypoints
- `php assignments/07-standard-functions/tests/run.php`
- `php assignments/07-standard-functions/index.php`
- `php -l assignments/07-standard-functions/index.php`
