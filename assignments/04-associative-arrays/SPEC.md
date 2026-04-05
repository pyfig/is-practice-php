# 04 Associative Arrays Specification

Source: Ассоциативные массивы.docx

## Assignment goal
Practice keyed array creation, personal and date data, counts, and last-element retrieval.

## Scope floor
- Create `[1 => 'a', 2 => 'b', 3 => 'c']`.
- Build a months array with January at key `1`.
- Output `name surname patronymic`.
- Output the current date as `year-month-day`.
- Demonstrate key holes and key order behavior.
- Count indexed and associative arrays.
- Return the last and penultimate elements.

## Deliverable shape
- Deterministic CLI entrypoint.
- `tests/run.php` assertion runner.

## Validations and fixed defaults
- January must remain under key `1`.
- Date output must follow `YYYY-MM-DD` formatting.
- Last and penultimate element handling must be safe for short arrays.

## Forbidden overreach
- Do not hardcode January to another key.
- Do not ignore associative-array counting cases.
- No forms, HTTP, sessions, or database behavior.

## Verification entrypoints
- `php assignments/04-associative-arrays/tests/run.php`
- `php assignments/04-associative-arrays/index.php`
- `php -l assignments/04-associative-arrays/index.php`
