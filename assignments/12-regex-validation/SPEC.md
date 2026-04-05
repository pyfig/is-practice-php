# 12 Regex Validation Specification

Source: Тема_11_Практическая_работа_Реглярные_выражения.docx

## Assignment goal
Build one PHP form that validates email, login, password, and phone with regex rules.

## Scope floor
- Build the form.
- Validate all four fields by regex.
- Show success if all fields are valid.
- Show error state when any field is invalid.

## Deliverable shape
- Browser-oriented assignment with `public/` entrypoint.
- One form covering all four fields.
- Clear success and error output.

## Validations and fixed defaults
- email: conventional local-part + `@` + domain + TLD.
- login: 3-20 chars, Latin letters/digits/underscore, must start with a letter.
- password: min 8 chars, at least one Latin letter and one digit.
- phone: optional leading `+`, digits only after normalization, final length 10-15 digits.

## Forbidden overreach
- Do not weaken the fixed regex rules.
- Do not accept a login that starts with a digit.
- Do not expand the assignment into registration or database persistence.

## Verification entrypoints
- `bash scripts/serve-assignment.sh 12-regex-validation 8094`
- `curl -X POST -d "email=test@example.com&login=ivan_123&password=pass1234&phone=+79991234567" http://127.0.0.1:8094`
- `php -l assignments/12-regex-validation/public/index.php`
