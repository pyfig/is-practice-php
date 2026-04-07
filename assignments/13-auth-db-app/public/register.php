<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/auth.php';

$dbStatus = auth_db_ready_status();
$dbAvailable = $dbStatus['available'];

$fullName = '';
$email = '';
$errors = [];

if ($dbAvailable && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    $errors = validate_registration_input($fullName, $email, $password);

    if ($errors === []) {
        try {
            register_user($fullName, $email, $password);
            redirect_to('/login.php?status=registered');
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        } catch (Throwable $throwable) {
            $errors[] = 'Не удалось выполнить регистрацию. Проверьте настройки базы данных и повторите попытку.';
        }
    }
}

$content = '<h1>Регистрация</h1>';

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
$content .= '<form method="post" action="' . escape_html(app_url('/register.php')) . '">'
    . '<label>Полное имя<input type="text" name="full_name" value="' . escape_html($fullName) . '"' . $disabledAttr . '></label>'
    . '<label>Email<input type="email" name="email" value="' . escape_html($email) . '"' . $disabledAttr . '></label>'
    . '<label>Пароль<input type="password" name="password"' . $disabledAttr . '></label>'
    . '<button type="submit"' . $disabledAttr . '>Зарегистрироваться</button>'
    . '</form>';

render_layout('13 Auth DB App — register', $content);
