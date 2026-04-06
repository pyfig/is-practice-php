<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = get_post_string('email');
    $emailFlow = session_namespace('email_flow');
    $emailFlow['email'] = $email;
    store_session_namespace('email_flow', $emailFlow);
    redirect_to('/email-step2.php');
}

$body = '<h1>Передача email между страницами</h1>'
    . '<p><a href="' . escape_html(app_url()) . '">На главную</a></p>'
    . '<section><h2>Шаг 1</h2><form method="post"><label>Email <input type="email" name="email" placeholder="student@example.com"></label><button type="submit">Сохранить email</button></form></section>';

render_page('11 Sessions — email step 1', $body);
