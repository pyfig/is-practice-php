<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile = session_namespace('profile_prefill');
    $profile['city'] = get_post_string('city');
    $profile['age'] = get_post_string('age');
    store_session_namespace('profile_prefill', $profile);
    redirect_to('/profile-step2.php');
}

$body = '<h1>Предзаполнение формы Name / Age / City</h1>'
    . '<p><a href="' . escape_html(app_url()) . '">На главную</a></p>'
    . '<section><h2>Шаг 1</h2><form method="post"><label>Город <input type="text" name="city" placeholder="Новосибирск"></label><label>Возраст <input type="text" name="age" placeholder="25"></label><button type="submit">Сохранить и продолжить</button></form></section>';

render_page('11 Sessions — profile step 1', $body);
