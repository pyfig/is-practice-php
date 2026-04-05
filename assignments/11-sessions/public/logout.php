<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
}
session_destroy();

$body = '<h1>Выход</h1><p>Сессия очищена. Все сохранённые данные удалены.</p><p><a href="/">Вернуться на главную</a></p>';

render_page('11 Sessions — logout', $body);
