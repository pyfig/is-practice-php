<?php
ob_start();
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$profile = session_namespace('profile_prefill');
$city = isset($profile['city']) && is_string($profile['city']) ? trim($profile['city']) : '';
$age = isset($profile['age']) && is_string($profile['age']) ? trim($profile['age']) : '';

$submittedResult = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedResult = '<section><h2>Результат</h2><p>Имя: <strong>' . escape_html(get_post_string('name')) . '</strong></p><p>Возраст: <strong>' . escape_html(get_post_string('age')) . '</strong></p><p>Город: <strong>' . escape_html(get_post_string('city')) . '</strong></p></section>';
}

$body = '<h1>Предзаполненная форма</h1>'
    . '<p><a href="' . escape_html(app_url('/profile-step1.php')) . '">Назад на шаг 1</a></p>'
    . '<section><h2>Шаг 2</h2><form method="post"><label>Имя <input type="text" name="name" placeholder="Иван"></label><label>Возраст <input type="text" name="age" value="' . escape_html($age) . '"></label><label>Город <input type="text" name="city" value="' . escape_html($city) . '"></label><button type="submit">Отправить форму</button></form></section>'
    . $submittedResult;

render_page('11 Sessions — profile step 2', $body);
