<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/db.php';

$user = current_auth_user();
$resolved = auth_db_config();

$content = '<h1>Регистрация и авторизация с MySQL</h1>';

if ($resolved['missing'] !== []) {
    $content .= '<section><h2>Конфигурация БД не завершена</h2><p class="muted">Для полноценной проверки задайте переменные: '
        . escape_html(implode(', ', $resolved['missing']))
        . '.</p></section>';
}

if ($user !== null) {
    $content .= '<section><h2>Вы вошли в систему</h2>'
        . '<p>Имя: <strong>' . escape_html((string) ($user['full_name'] ?? '')) . '</strong></p>'
        . '<p>Email: <strong>' . escape_html((string) ($user['email'] ?? '')) . '</strong></p>'
        . '<p>Дата регистрации: <strong>' . escape_html((string) ($user['created_at'] ?? '')) . '</strong></p>'
        . '</section>';
} else {
    $content .= '<section><h2>Гость</h2><p>Вы ещё не вошли в систему. Используйте регистрацию или вход.</p></section>';
}

render_layout('13 Auth DB App', $content);
