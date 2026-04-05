<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header('Location: ' . $path, true, 303);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['auth_app_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function pull_flash(): ?array
{
    $flash = $_SESSION['auth_app_flash'] ?? null;
    unset($_SESSION['auth_app_flash']);

    return is_array($flash) ? $flash : null;
}

function current_auth_user(): ?array
{
    $user = $_SESSION['auth_app_user'] ?? null;

    return is_array($user) ? $user : null;
}

function store_auth_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['auth_app_user'] = $user;
}

function clear_auth_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function render_layout(string $title, string $content): void
{
    $flash = pull_flash();
    http_response_code(200);
    ?>
    <!doctype html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title><?= escape_html($title) ?></title>
        <style>
            body { font-family: Arial, sans-serif; background: #f8fafc; color: #0f172a; margin: 0; }
            main { max-width: 840px; margin: 0 auto; padding: 24px; }
            nav a { margin-right: 12px; }
            section, form { background: #ffffff; border: 1px solid #cbd5e1; padding: 16px; margin-bottom: 16px; }
            form { display: grid; gap: 10px; }
            label { display: grid; gap: 6px; }
            input, button { font: inherit; padding: 8px; }
            .flash-success { background: #dcfce7; border-color: #22c55e; }
            .flash-error { background: #fee2e2; border-color: #ef4444; }
            .muted { color: #475569; }
        </style>
    </head>
    <body>
    <main>
        <nav>
            <a href="/">Главная</a>
            <a href="/register.php">Регистрация</a>
            <a href="/login.php">Вход</a>
            <a href="/logout.php">Выход</a>
        </nav>
        <?php if ($flash !== null && isset($flash['type'], $flash['message']) && is_string($flash['type']) && is_string($flash['message'])): ?>
            <section class="flash-<?= escape_html($flash['type']) ?>">
                <?= escape_html($flash['message']) ?>
            </section>
        <?php endif; ?>
        <?= $content ?>
    </main>
    </body>
    </html>
    <?php
}
