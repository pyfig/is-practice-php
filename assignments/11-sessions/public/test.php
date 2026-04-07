<?php
declare(strict_types=1);

ob_start();

require dirname(__DIR__) . '/src/bootstrap.php';

$countryForm = session_namespace('country_form');
$country = isset($countryForm['country']) && is_string($countryForm['country']) ? trim($countryForm['country']) : '';

$body = '<h1>Страница test.php</h1>'
    . '<p><a href="' . escape_html(app_url()) . '">На главную</a></p>'
    . '<section><h2>Сохранённая страна</h2><p>'
    . ($country !== '' ? 'Вы выбрали страну: <strong>' . escape_html($country) . '</strong>' : 'Страна ещё не выбрана. Вернитесь на главную страницу и отправьте форму.')
    . '</p></section>';

render_page('11 Sessions — test.php', $body);
