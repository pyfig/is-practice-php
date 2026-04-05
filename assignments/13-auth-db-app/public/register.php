<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/auth.php';

$fullName = '';
$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));
    $errors = validate_registration_input($fullName, $email, $password);

    if ($errors === []) {
        try {
            register_user($fullName, $email, $password);
            set_flash('success', 'Регистрация прошла успешно. Теперь войдите в систему.');
            redirect_to('/login.php');
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        } catch (Throwable $throwable) {
            $errors[] = 'Не удалось выполнить регистрацию. Проверьте настройки базы данных и повторите попытку.';
        }
    }
}

$content = '<h1>Регистрация</h1>';
if ($errors !== []) {
    $content .= '<section class="flash-error"><ul>';
    foreach ($errors as $error) {
        $content .= '<li>' . escape_html($error) . '</li>';
    }
    $content .= '</ul></section>';
}

$content .= '<form method="post">'
    . '<label>Полное имя<input type="text" name="full_name" value="' . escape_html($fullName) . '"></label>'
    . '<label>Email<input type="email" name="email" value="' . escape_html($email) . '"></label>'
    . '<label>Пароль<input type="password" name="password"></label>'
    . '<button type="submit">Зарегистрироваться</button>'
    . '</form>';

render_layout('13 Auth DB App — register', $content);
