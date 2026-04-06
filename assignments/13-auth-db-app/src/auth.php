<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function assignment13_auth_secret(): string
{
    $secret = getenv('ASSIGNMENT13_AUTH_SECRET');
    if (is_string($secret) && $secret !== '') {
        return $secret;
    }

    return 'assignment13-local-dev-secret';
}

function assignment13_hash_token(string $token): string
{
    return hash_hmac('sha256', $token, assignment13_auth_secret());
}

function assignment13_generate_token(): string
{
    return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
}

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

function current_auth_user(): ?array
{
    static $resolvedUser = false;
    if (is_array($resolvedUser)) {
        return $resolvedUser;
    }

    $token = $_COOKIE[ASSIGNMENT13_AUTH_COOKIE] ?? null;
    if (!is_string($token) || $token === '') {
        $resolvedUser = null;

        return null;
    }

    $statement = auth_db_connection()->prepare(
        'SELECT users.id, users.full_name, users.email, users.created_at
         FROM user_sessions
         INNER JOIN users ON users.id = user_sessions.user_id
         WHERE user_sessions.token_hash = :token_hash
           AND user_sessions.expires_at > UTC_TIMESTAMP()
         LIMIT 1'
    );
    $statement->execute(['token_hash' => assignment13_hash_token($token)]);
    $user = $statement->fetch();

    if (!is_array($user)) {
        clear_auth_cookie();
        $resolvedUser = null;

        return null;
    }

    $touch = auth_db_connection()->prepare('UPDATE user_sessions SET last_seen_at = UTC_TIMESTAMP() WHERE token_hash = :token_hash');
    $touch->execute(['token_hash' => assignment13_hash_token($token)]);

    $resolvedUser = $user;

    return $resolvedUser;
}

function create_user_session(array $user): void
{
    $token = assignment13_generate_token();
    $statement = auth_db_connection()->prepare(
        'INSERT INTO user_sessions (user_id, token_hash, expires_at, last_seen_at)
         VALUES (:user_id, :token_hash, UTC_TIMESTAMP() + INTERVAL 7 DAY, UTC_TIMESTAMP())'
    );
    $statement->execute([
        'user_id' => $user['id'],
        'token_hash' => assignment13_hash_token($token),
    ]);

    set_auth_cookie($token);
}

function login_user(string $email, string $password): array
{
    $user = authenticate_user($email, $password);
    create_user_session($user);

    return $user;
}

function logout_current_user(): void
{
    $token = $_COOKIE[ASSIGNMENT13_AUTH_COOKIE] ?? null;
    if (is_string($token) && $token !== '') {
        $statement = auth_db_connection()->prepare('DELETE FROM user_sessions WHERE token_hash = :token_hash');
        $statement->execute(['token_hash' => assignment13_hash_token($token)]);
    }

    clear_auth_cookie();
}
