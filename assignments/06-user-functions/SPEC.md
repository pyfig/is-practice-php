# 06 User Functions Specification

Source: Пользовательские функции.docx

## Assignment goal
Practice reusable functions, return values, boolean checks, and numeric helpers.

## Scope floor
- Print your own name.
- Emit `+++` for positive and `---` for negative values.
- Sum three numbers.
- Return a cube value into `$res`.
- Check whether all elements are even.
- Detect adjacent duplicates.
- Compute the sum of digits.
- Check whether a number is prime.

## Deliverable shape
- Named PHP functions for each task.
- Deterministic CLI entrypoint.
- `tests/run.php` assertion runner.

## Validations and fixed defaults
- Marker output must be exactly `+++` and `---`.
- Prime checks must cover both prime and composite cases.
- Return-value tasks must be asserted, not only printed.

## Forbidden overreach
- Do not collapse all logic into anonymous inline code.
- Do not omit return-value coverage.
- No forms, HTTP, sessions, or database features.

## Verification entrypoints
- `php assignments/06-user-functions/tests/run.php`
- `php assignments/06-user-functions/index.php`
- `php -l assignments/06-user-functions/index.php`
