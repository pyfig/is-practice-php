<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function assignment13_auth_secret(): string
{
    $secret = getenv('ASSIGNMENT13_AUTH_SECRET');
    if (is_string($secret) && $secret !== '') {
        return $secret;
    }

    // In production (Vercel), require explicit secret
    $isProduction = getenv('VERCEL_ENV') === 'production' || getenv('VERCEL') === '1';
    if ($isProduction) {
        throw new RuntimeException('ASSIGNMENT13_AUTH_SECRET не настроен в production окружении.');
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
    return auth_db_fetch_user_by_email($email);
}

function register_user(string $fullName, string $email, string $password): void
{
    auth_db_insert_user($fullName, $email, password_hash($password, PASSWORD_DEFAULT));
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
    static $isResolved = false;
    static $resolvedUser = null;

    if ($isResolved) {
        return $resolvedUser;
    }

    $token = $_COOKIE[ASSIGNMENT13_AUTH_COOKIE] ?? null;
    if (!is_string($token) || $token === '') {
        $isResolved = true;
        $resolvedUser = null;
        return null;
    }

    $dbStatus = auth_db_status();
    if ($dbStatus['available'] === false) {
        clear_auth_cookie();
        $isResolved = true;
        $resolvedUser = null;
        return null;
    }

    try {
        $tokenHash = assignment13_hash_token($token);
        $session = auth_db_fetch_active_session($tokenHash);
        if ($session === null || !isset($session['user_id'])) {
            clear_auth_cookie();
            $isResolved = true;
            $resolvedUser = null;
            return null;
        }

        $user = auth_db_fetch_user_by_id((int) $session['user_id']);
        if ($user === null) {
            clear_auth_cookie();
            $isResolved = true;
            $resolvedUser = null;
            return null;
        }

        auth_db_touch_session($tokenHash);

        $isResolved = true;
        $resolvedUser = $user;
        return $resolvedUser;
    } catch (Throwable $throwable) {
        clear_auth_cookie();
        $isResolved = true;
        $resolvedUser = null;
        return null;
    }
}

function create_user_session(array $user): void
{
    $token = assignment13_generate_token();
    auth_db_insert_session(
        (int) ($user['id'] ?? 0),
        assignment13_hash_token($token),
        gmdate('c', time() + ASSIGNMENT13_AUTH_MAX_AGE)
    );
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

    clear_auth_cookie();

    if (is_string($token) && $token !== '') {
        $dbStatus = auth_db_status();
        if ($dbStatus['available'] === true) {
            try {
                auth_db_delete_session(assignment13_hash_token($token));
            } catch (Throwable $throwable) {
            }
        }
    }
}
