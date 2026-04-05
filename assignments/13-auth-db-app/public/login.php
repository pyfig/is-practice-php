<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';
require_once dirname(__DIR__) . '/src/auth.php';

$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $user = authenticate_user($email, $password);
            store_auth_user($user);
            set_flash('success', 'Вход выполнен успешно.');
            redirect_to('/');
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        } catch (Throwable $throwable) {
            $errors[] = 'Не удалось выполнить вход. Проверьте настройки базы данных и повторите попытку.';
        }
    }
}

$content = '<h1>Вход</h1>';
if ($errors !== []) {
    $content .= '<section class="flash-error"><ul>';
    foreach ($errors as $error) {
        $content .= '<li>' . escape_html($error) . '</li>';
    }
    $content .= '</ul></section>';
}

$content .= '<form method="post">'
    . '<label>Email<input type="email" name="email" value="' . escape_html($email) . '"></label>'
    . '<label>Пароль<input type="password" name="password"></label>'
    . '<button type="submit">Войти</button>'
    . '</form>';

render_layout('13 Auth DB App — login', $content);
