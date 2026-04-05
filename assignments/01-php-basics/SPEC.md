# 01 PHP Basics Specification

Source: Копия Основы PHP.docx

## Assignment goal
Cover beginner PHP output, string basics, and simple geometry formulas.

## Scope floor
- Output name, age, study place, and hobbies.
- Output string length.
- Output the last character of a string.
- Compute circle area from radius.
- Compute rectangle area.
- Compute rectangle perimeter.

## Deliverable shape
- One isolated assignment folder.
- Either one aggregated script or several tiny scripts.
- Deterministic CLI output.
- `tests/run.php` assertion runner.

## Validations and fixed defaults
- String length must be UTF-8 safe when Cyrillic text is used.
- The brief's "write to file" wording is treated as deterministic script output checked by the test harness, not manual file authoring.

## Forbidden overreach
- No forms, HTTP handling, sessions, or database code.
- Do not add business behavior beyond the listed beginner exercises.

## Verification entrypoints
- `php assignments/01-php-basics/tests/run.php`
- `php assignments/01-php-basics/index.php`
- `php -l assignments/01-php-basics/index.php`
