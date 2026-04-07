<?php
declare(strict_types=1);

ob_start();

require dirname(__DIR__) . '/src/bootstrap.php';

clear_state_cookie();

$body = '<h1>Выход</h1><p>Состояние очищено. Все сохранённые данные удалены.</p><p><a href="' . escape_html(app_url()) . '">Вернуться на главную</a></p>';

render_page('11 Sessions — logout', $body);
