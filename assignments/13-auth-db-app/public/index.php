<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/auth.php';

// Check DB status first, then resolve user (which safely handles stale cookies)
$dbStatus = auth_db_status();
$user = current_auth_user();

$content = '<h1>Регистрация и авторизация с MySQL</h1>';

// Three mutually exclusive states:
// 1. DB unavailable - show error, NOT guest
// 2. Authenticated user - show user info
// 3. Healthy guest - show normal guest message

if ($dbStatus['available'] === false) {
    // DB unavailable state - never show healthy guest copy
    if ($dbStatus['reason'] === 'config_missing') {
        $content .= '<section class="flash-error"><h2>Конфигурация БД не завершена</h2><p class="muted">'
            . escape_html($dbStatus['message'])
            . '</p></section>';
    } else {
        $content .= '<section class="flash-error"><h2>База данных временно недоступна</h2><p class="muted">'
            . escape_html($dbStatus['message'])
            . '</p></section>';
    }
} elseif ($user !== null) {
    // Authenticated user
    $content .= '<section><h2>Вы вошли в систему</h2>'
        . '<p>Имя: <strong>' . escape_html((string) ($user['full_name'] ?? '')) . '</strong></p>'
        . '<p>Email: <strong>' . escape_html((string) ($user['email'] ?? '')) . '</strong></p>'
        . '<p>Дата регистрации: <strong>' . escape_html((string) ($user['created_at'] ?? '')) . '</strong></p>'
        . '</section>';
} else {
    // Healthy guest - only when DB is available
    $content .= '<section><h2>Гость</h2><p>Вы ещё не вошли в систему. Используйте регистрацию или вход.</p></section>';
}

render_layout('13 Auth DB App', $content);
