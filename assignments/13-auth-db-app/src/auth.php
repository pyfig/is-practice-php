<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function validate_registration_input(string $fullName, string $email, string $password): array
{
    $errors = [];

    if ($fullName === '') {
        $errors[] = 'Укажите полное имя.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Укажите корректный email.';
    }

    if (mb_strlen($password, 'UTF-8') < 8) {
        $errors[] = 'Пароль должен содержать не меньше 8 символов.';
    }

    return $errors;
}

function find_user_by_email(string $email): ?array
{
    $statement = auth_db_connection()->prepare('SELECT id, full_name, email, password_hash, created_at FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    return is_array($user) ? $user : null;
}

function register_user(string $fullName, string $email, string $password): void
{
    if (find_user_by_email($email) !== null) {
        throw new RuntimeException('Пользователь с таким email уже существует.');
    }

    $statement = auth_db_connection()->prepare(
        'INSERT INTO users (full_name, email, password_hash) VALUES (:full_name, :email, :password_hash)'
    );

    try {
        $statement->execute([
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    } catch (PDOException $exception) {
        if ($exception->getCode() === '23000') {
            throw new RuntimeException('Пользователь с таким email уже существует.');
        }

        throw $exception;
    }
}

function authenticate_user(string $email, string $password): array
{
    $user = find_user_by_email($email);
    if ($user === null || !isset($user['password_hash']) || !is_string($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        throw new RuntimeException('Неверный email или пароль.');
    }

    return [
        'id' => $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'created_at' => $user['created_at'],
    ];
}
