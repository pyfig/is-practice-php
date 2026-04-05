# 09 Forms Specification

Source: Формы.docx

## Assignment goal
Practice GET and POST forms, server-side validation logic, and common input controls.

## Scope floor
- `name/age/salary` form to `result.php` for both GET and POST.
- Three-number sum form.
- Name and age display.
- Password comparison.
- Surname, name, and patronymic display.
- Checkbox-based greeting and goodbye.
- Gender radio buttons.
- Celsius and Fahrenheit converter.
- Birthday input in `dd.mm.yyyy`, with days until next birthday.
- Textarea word count and character count.

## Deliverable shape
- Browser-oriented assignment with `public/` entrypoint.
- Separate request handling where the brief requires `result.php`.
- Curl-verifiable flows.

## Validations and fixed defaults
- GET and POST variants of the first task must both work.
- Numeric and date fields need explicit invalid-input handling.
- Birthday parsing uses `dd.mm.yyyy` and computes the next upcoming birthday.

## Forbidden overreach
- Do not collapse GET and POST into one unverified path.
- Do not skip empty or invalid input handling for numeric and date fields.
- No sessions or database persistence.

## Verification entrypoints
- `bash scripts/serve-assignment.sh 09-forms 8091`
- `curl -s "http://127.0.0.1:8091"`
- `php -l assignments/09-forms/public/index.php`
