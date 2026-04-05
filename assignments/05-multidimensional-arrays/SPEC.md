# 05 Multidimensional Arrays Specification

Source: Многомерность.docx

## Assignment goal
Practice nested arrays, nested loops, table rendering, and structured datasets.

## Scope floor
- Sum all elements of a 2D array.
- Sum salaries of the first and third user.
- Output a books dataset.
- Render a disciplines dataset as a table.
- Output `group name - user name` from nested groups.

## Deliverable shape
- One isolated assignment folder with deterministic output.
- CLI-friendly entrypoint plus HTML-capable output for the table task.
- `tests/run.php` assertion runner.

## Validations and fixed defaults
- The disciplines task must render an HTML table.
- Nested data must stay nested until rendered or iterated.

## Forbidden overreach
- Do not flatten the source data prematurely.
- Do not render the disciplines task as plain text only.
- No sessions or database work.

## Verification entrypoints
- `php assignments/05-multidimensional-arrays/tests/run.php`
- `php assignments/05-multidimensional-arrays/index.php`
- `php -l assignments/05-multidimensional-arrays/index.php`
