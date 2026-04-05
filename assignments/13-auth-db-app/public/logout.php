<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

clear_auth_user();

$content = '<h1>Выход</h1><section><p>Сессия завершена. Вы вышли из системы.</p><p><a href="/">Вернуться на главную</a></p></section>';

render_layout('13 Auth DB App — logout', $content);
