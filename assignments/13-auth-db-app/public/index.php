<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/auth.php';

$dbStatus = auth_db_ready_status();
$user = current_auth_user();

$content = '<h1>Регистрация и авторизация</h1>';

if ($dbStatus['available'] === false) {
    $content .= assignment13_db_notice_html($dbStatus);
} elseif ($user !== null) {
    $content .= '<section><h2>Вы вошли в систему</h2>'
        . '<p>Имя: <strong>' . escape_html((string) ($user['full_name'] ?? '')) . '</strong></p>'
        . '<p>Email: <strong>' . escape_html((string) ($user['email'] ?? '')) . '</strong></p>'
        . '<p>Дата регистрации: <strong>' . escape_html((string) ($user['created_at'] ?? '')) . '</strong></p>'
        . '</section>';
} else {
    $content .= '<section><h2>Гость</h2><p>Вы ещё не вошли в систему. Используйте регистрацию или вход.</p></section>';
}

render_layout('13 Auth DB App', $content);
