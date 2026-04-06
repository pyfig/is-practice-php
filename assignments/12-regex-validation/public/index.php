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
$appBasePath = isset($_SERVER['APP_BASE_PATH']) && is_string($_SERVER['APP_BASE_PATH'])
    ? $_SERVER['APP_BASE_PATH']
    : '/12-regex-validation';
$appRequestPath = isset($_SERVER['APP_REQUEST_PATH']) && is_string($_SERVER['APP_REQUEST_PATH'])
    ? $_SERVER['APP_REQUEST_PATH']
    : '/';

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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>12 Regex Validation</title>
    <link rel="stylesheet" href="/assets/launchpad.css">
    <style>
        .assignment-page { max-width: 760px; margin: 0 auto; padding: var(--spacing-xl); }
        .assignment-shell { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: var(--spacing-xl); box-shadow: var(--shadow-sm); }
        .assignment-title { margin: 0 0 var(--spacing-sm); font-size: 1.75rem; }
        .assignment-description { margin: 0 0 var(--spacing-lg); color: var(--color-text-muted); }
        form { display: grid; gap: 14px; }
        label { display: grid; gap: 6px; }
        input, button { font: inherit; padding: 10px 12px; border-radius: var(--radius-sm); }
        input { border: 1px solid var(--color-border); }
        button { border: 1px solid var(--color-border-strong); background: #0f172a; color: #fff; cursor: pointer; }
        .success { padding: 12px; background: #dcfce7; border: 1px solid #22c55e; margin-bottom: 16px; border-radius: var(--radius-sm); }
        .error { color: #b91c1c; font-size: 14px; }
        .form-summary-error { margin-bottom: 16px; }
        code { font-family: var(--font-mono); }
    </style>
</head>
<body data-app-base-path="<?= escape_html($appBasePath) ?>" data-app-request-path="<?= escape_html($appRequestPath) ?>">
<header class="launchpad-header">
    <a class="home-logo" data-home-logo href="/">вернуться домой</a>
</header>
<main class="assignment-page">
    <article class="assignment-shell">
    <h1 class="assignment-title">Валидация формы через регулярные выражения</h1>
    <p class="assignment-description">Проверьте email, логин, пароль и телефон по фиксированным правилам задания. Базовый путь страницы: <code><?= escape_html($appBasePath) ?></code>.</p>

    <?php if ($successMessage !== ''): ?>
        <div class="success"><?= escape_html($successMessage) ?></div>
    <?php elseif ($errors !== []): ?>
        <div class="error form-summary-error">Форма содержит ошибки. Исправьте поля ниже.</div>
    <?php endif; ?>

    <form method="post" action="<?= escape_html($appBasePath) ?>">
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
    </article>
</main>
</body>
</html>
