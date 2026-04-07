<?php
declare(strict_types=1);

ob_start();

require dirname(__DIR__) . '/src/bootstrap.php';

$emailFlow = session_namespace('email_flow');
$email = isset($emailFlow['email']) && is_string($emailFlow['email']) ? trim($emailFlow['email']) : '';

$body = '<h1>Передача email между страницами</h1>'
    . '<p><a href="' . escape_html(app_url('/email-step1.php')) . '">Назад на шаг 1</a></p>'
    . '<section><h2>Шаг 2</h2><p>'
    . ($email !== '' ? 'Email из предыдущей формы: <strong>' . escape_html($email) . '</strong>' : 'Email ещё не сохранён. Сначала заполните первую форму.')
    . '</p><form method="post"><label>Подтверждение email <input type="email" name="email_confirm" value="' . escape_html($email) . '"></label><button type="submit">Показать значение</button></form></section>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmedEmail = get_post_string('email_confirm');
    $body .= '<section><h2>Результат</h2><p>Во второй форме используется email: <strong>' . escape_html($confirmedEmail) . '</strong></p></section>';
}

render_page('11 Sessions — email step 2', $body);
