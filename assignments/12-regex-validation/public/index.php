<?php
declare(strict_types=1);

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function post_string(string $key): string
{
    $value = $_POST[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

function normalize_phone(string $value): string
{
    return preg_replace('/\D+/u', '', $value) ?? '';
}

function validate_email_value(string $value): bool
{
    return preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/iu', $value) === 1;
}

function validate_login_value(string $value): bool
{
    return preg_match('/^[A-Za-z][A-Za-z0-9_]{2,19}$/', $value) === 1;
}

function validate_password_value(string $value): bool
{
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\W_]{8,}$/', $value) === 1;
}

function validate_phone_value(string $rawValue): bool
{
    $trimmed = trim($rawValue);
    if ($trimmed === '') {
        return false;
    }

    if ($trimmed[0] === '+' && preg_match('/^\+\d[\d\s().-]*$/', $trimmed) !== 1) {
        return false;
    }

    if ($trimmed[0] !== '+' && preg_match('/^\d[\d\s().-]*$/', $trimmed) !== 1) {
        return false;
    }

    $normalized = normalize_phone($trimmed);

    return preg_match('/^\d{10,15}$/', $normalized) === 1;
}

$values = [
    'email' => '',
    'login' => '',
    'password' => '',
    'phone' => '',
];
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $_) {
        $values[$field] = post_string($field);
    }

    if (!validate_email_value($values['email'])) {
        $errors['email'] = 'Введите корректный email в формате name@example.com.';
    }

    if (!validate_login_value($values['login'])) {
        $errors['login'] = 'Логин должен начинаться с буквы и содержать 3–20 символов: латиница, цифры и _.';
    }

    if (!validate_password_value($values['password'])) {
        $errors['password'] = 'Пароль должен содержать минимум 8 символов, хотя бы одну латинскую букву и одну цифру.';
    }

    if (!validate_phone_value($values['phone'])) {
        $errors['phone'] = 'Телефон должен содержать 10–15 цифр после нормализации и может начинаться с +.';
    }

    if ($errors === []) {
        $successMessage = 'Все поля успешно прошли проверку регулярными выражениями.';
    }
}

http_response_code(200);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>12 Regex Validation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        main { max-width: 760px; margin: 0 auto; padding: 24px; }
        form { display: grid; gap: 14px; background: #fff; border: 1px solid #cbd5e1; padding: 16px; }
        label { display: grid; gap: 6px; }
        input, button { font: inherit; padding: 8px; }
        .success { padding: 12px; background: #dcfce7; border: 1px solid #22c55e; margin-bottom: 16px; }
        .error { color: #b91c1c; font-size: 14px; }
    </style>
</head>
<body>
<main>
    <h1>Валидация формы через регулярные выражения</h1>
    <p>Проверьте email, логин, пароль и телефон по фиксированным правилам задания.</p>

    <?php if ($successMessage !== ''): ?>
        <div class="success"><?= escape_html($successMessage) ?></div>
    <?php elseif ($errors !== []): ?>
        <div class="error" style="margin-bottom: 16px;">Форма содержит ошибки. Исправьте поля ниже.</div>
    <?php endif; ?>

    <form method="post">
        <label>
            Email
            <input type="text" name="email" value="<?= escape_html($values['email']) ?>" placeholder="test@example.com">
            <?php if (isset($errors['email'])): ?><span class="error"><?= escape_html($errors['email']) ?></span><?php endif; ?>
        </label>

        <label>
            Логин
            <input type="text" name="login" value="<?= escape_html($values['login']) ?>" placeholder="ivan_123">
            <?php if (isset($errors['login'])): ?><span class="error"><?= escape_html($errors['login']) ?></span><?php endif; ?>
        </label>

        <label>
            Пароль
            <input type="password" name="password" value="<?= escape_html($values['password']) ?>" placeholder="pass1234">
            <?php if (isset($errors['password'])): ?><span class="error"><?= escape_html($errors['password']) ?></span><?php endif; ?>
        </label>

        <label>
            Телефон
            <input type="text" name="phone" value="<?= escape_html($values['phone']) ?>" placeholder="+79991234567">
            <?php if (isset($errors['phone'])): ?><span class="error"><?= escape_html($errors['phone']) ?></span><?php endif; ?>
        </label>

        <button type="submit">Проверить форму</button>
    </form>
</main>
</body>
</html>
