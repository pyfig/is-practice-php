<?php
declare(strict_types=1);

const ASSIGNMENT13_AUTH_COOKIE = 'assignment13_auth';
const ASSIGNMENT13_DEFAULT_BASE_PATH = '/13-auth-db-app';
const ASSIGNMENT13_AUTH_MAX_AGE = 604800;

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function app_base_path(): string
{
    $basePath = $_SERVER['APP_BASE_PATH'] ?? ASSIGNMENT13_DEFAULT_BASE_PATH;

    if (!is_string($basePath) || $basePath === '') {
        return ASSIGNMENT13_DEFAULT_BASE_PATH;
    }

    $normalizedBasePath = '/' . trim($basePath, '/');

    return $normalizedBasePath === '/' ? ASSIGNMENT13_DEFAULT_BASE_PATH : $normalizedBasePath;
}

function app_request_path(): string
{
    $requestPath = $_SERVER['APP_REQUEST_PATH'] ?? null;
    if (is_string($requestPath) && $requestPath !== '') {
        return $requestPath;
    }

    $uriPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $uriPath = is_string($uriPath) && $uriPath !== '' ? $uriPath : '/';
    $basePath = app_base_path();

    if ($uriPath === $basePath) {
        return '/';
    }

    if (strpos($uriPath, $basePath . '/') === 0) {
        return substr($uriPath, strlen($basePath)) ?: '/';
    }

    return $uriPath;
}

function app_url(string $path = ''): string
{
    $basePath = app_base_path();
    if ($path === '' || $path === '/') {
        return $basePath;
    }

    if ($path[0] === '?') {
        return $basePath . $path;
    }

    if (strpos($path, '/?') === 0) {
        return $basePath . substr($path, 1);
    }

    return $basePath . '/' . ltrim($path, '/');
}

function redirect_to(string $path): void
{
    header('Location: ' . app_url($path), true, 303);
    exit;
}

function assignment13_is_https_request(): bool
{
    $https = $_SERVER['HTTPS'] ?? '';

    return is_string($https) && $https !== '' && $https !== 'off';
}

function assignment13_auth_cookie_options(int $expires): array
{
    return [
        'expires' => $expires,
        'path' => app_base_path(),
        'secure' => assignment13_is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function set_auth_cookie(string $token): void
{
    setcookie(ASSIGNMENT13_AUTH_COOKIE, $token, assignment13_auth_cookie_options(time() + ASSIGNMENT13_AUTH_MAX_AGE));
    $_COOKIE[ASSIGNMENT13_AUTH_COOKIE] = $token;
}

function clear_auth_cookie(): void
{
    setcookie(ASSIGNMENT13_AUTH_COOKIE, '', assignment13_auth_cookie_options(time() - 3600));
    unset($_COOKIE[ASSIGNMENT13_AUTH_COOKIE]);
}

function status_message(): ?array
{
    $status = $_GET['status'] ?? null;
    if (!is_string($status) || $status === '') {
        return null;
    }

    $messages = [
        'registered' => ['type' => 'success', 'message' => 'Регистрация прошла успешно. Теперь войдите в систему.'],
        'logged-in' => ['type' => 'success', 'message' => 'Вход выполнен успешно.'],
        'logged-out' => ['type' => 'success', 'message' => 'Вы вышли из системы.'],
        'login-required' => ['type' => 'error', 'message' => 'Сначала выполните вход в систему.'],
    ];

    return $messages[$status] ?? null;
}

function render_layout(string $title, string $content): void
{
    $status = status_message();
    http_response_code(200);
    ?>
    <!doctype html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= escape_html($title) ?></title>
        <link rel="stylesheet" href="/assets/launchpad.css">
        <style>
            body { font-family: Arial, sans-serif; background: var(--color-page, #f8fafc); color: var(--color-text, #0f172a); margin: 0; }
            .assignment-page { max-width: 840px; margin: 0 auto; padding: 24px; }
            .assignment-shell { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 24px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06); }
            nav { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
            nav a { color: #2563eb; }
            section, form { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
            form { display: grid; gap: 10px; }
            label { display: grid; gap: 6px; }
            input, button { font: inherit; padding: 8px; }
            .flash-success { background: #dcfce7; border-color: #22c55e; }
            .flash-error { background: #fee2e2; border-color: #ef4444; }
            .muted { color: #475569; }
        </style>
    </head>
    <body data-app-base-path="<?= escape_html(app_base_path()) ?>" data-app-request-path="<?= escape_html(app_request_path()) ?>">
    <header class="launchpad-header"><a class="home-logo" data-home-logo href="/">вернуться домой</a></header>
    <main class="assignment-page">
        <article class="assignment-shell">
            <nav>
                <a href="<?= escape_html(app_url()) ?>">Главная задания</a>
                <a href="<?= escape_html(app_url('/register.php')) ?>">Регистрация</a>
                <a href="<?= escape_html(app_url('/login.php')) ?>">Вход</a>
                <a href="<?= escape_html(app_url('/logout.php')) ?>">Выход</a>
            </nav>
            <?php if ($status !== null): ?>
                <section class="flash-<?= escape_html((string) $status['type']) ?>">
                    <?= escape_html((string) $status['message']) ?>
                </section>
            <?php endif; ?>
            <?= $content ?>
        </article>
    </main>
    </body>
    </html>
    <?php
}
