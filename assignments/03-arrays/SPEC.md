# 03 Arrays Specification

Source: Массивы.docx

## Assignment goal
Practice indexed arrays, search, fill, ranges, sums, shuffling, and sorting.

## Scope floor
- Arithmetic using `[2,5,3,9]`.
- Output user associative data.
- Fill an array with `1..5`.
- Find min and max.
- Detect value `3`.
- Sum all elements.
- Create arrays `1..100` and `a..z`.
- Sum `1..100` without a loop.
- Build `range(1,25)` and shuffle it.
- Generate a random nonrepeating alphabet.
- Demonstrate multiple sorts on `['3' => 'a', '1' => 'c', '2' => 'e', '4' => 'b']`.

## Deliverable shape
- Deterministic CLI entrypoint with named task output.
- `tests/run.php` assertion runner.

## Validations and fixed defaults
- The no-loop sum task for `1..100` must stay loop-free.
- The random alphabet output must not repeat letters.
- The sorting section must demonstrate at least `sort`, `asort`, `ksort`, `rsort`, `arsort`, and `krsort`, with before and after output where applicable.

## Forbidden overreach
- Do not skip any listed sort variant.
- Do not allow duplicate letters in the alphabet task.
- No web, session, or database features.

## Verification entrypoints
- `php assignments/03-arrays/tests/run.php`
- `php assignments/03-arrays/index.php`
- `php -l assignments/03-arrays/index.php`
