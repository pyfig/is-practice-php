# 02 Control Structures Specification

Source: Управляющие конструкции.docx

## Assignment goal
Practice conditionals, loops, filtering, and aggregation.

## Scope floor
- Determine season from month `1..12`.
- Check whether the first character equals `a`.
- Check whether a six-digit number is lucky.
- Raise all salaries by 10%.
- Raise only salaries `<= 400`.
- Print numbers `1..100`.
- Filter array elements `> 0 && < 10`.
- Compute array sum and arithmetic mean.

## Deliverable shape
- One aggregated CLI script with named subtask outputs.
- `tests/run.php` assertion runner.

## Validations and fixed defaults
- Reject month values outside `1..12`.
- Lucky-number logic applies only to six-digit input.

## Forbidden overreach
- Do not silently accept invalid months.
- Do not treat non-six-digit input as lucky.
- No forms, HTTP, sessions, or database work.

## Verification entrypoints
- `php assignments/02-control-structures/tests/run.php`
- `php assignments/02-control-structures/index.php`
- `php -l assignments/02-control-structures/index.php`
