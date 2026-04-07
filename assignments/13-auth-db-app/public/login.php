<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/auth.php';

$dbStatus = auth_db_ready_status();
$dbAvailable = $dbStatus['available'];

$email = '';
$errors = [];

if ($dbAvailable && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Укажите корректный email.';
    }
    if ($password === '') {
        $errors[] = 'Введите пароль.';
    }

    if ($errors === []) {
        try {
            login_user($email, $password);
            redirect_to('/?status=logged-in');
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        } catch (Throwable $throwable) {
            $errors[] = 'Не удалось выполнить вход. Проверьте настройки базы данных и повторите попытку.';
        }
    }
}

$content = '<h1>Вход</h1>';

if ($dbAvailable === false) {
    $content .= assignment13_db_notice_html($dbStatus);
}

if ($errors !== []) {
    $content .= '<section class="flash-error"><ul>';
    foreach ($errors as $error) {
        $content .= '<li>' . escape_html($error) . '</li>';
    }
    $content .= '</ul></section>';
}

$disabledAttr = $dbAvailable ? '' : ' disabled';
$content .= '<form method="post" action="' . escape_html(app_url('/login.php')) . '">'
    . '<label>Email<input type="email" name="email" value="' . escape_html($email) . '"' . $disabledAttr . '></label>'
    . '<label>Пароль<input type="password" name="password"' . $disabledAttr . '></label>'
    . '<button type="submit"' . $disabledAttr . '>Войти</button>'
    . '</form>';

render_layout('13 Auth DB App — login', $content);
