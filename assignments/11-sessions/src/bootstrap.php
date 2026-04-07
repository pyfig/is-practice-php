<?php
declare(strict_types=1);

const ASSIGNMENT11_STATE_COOKIE = 'assignment11_state';
const ASSIGNMENT11_DEFAULT_BASE_PATH = '/11-sessions';
const ASSIGNMENT11_STATE_MAX_AGE = 2592000;

$GLOBALS['assignment11_state'] = assignment11_load_state();
$GLOBALS['assignment11_state_dirty'] = false;

register_shutdown_function(static function (): void {
    if (($GLOBALS['assignment11_state_dirty'] ?? false) !== true) {
        return;
    }

    assignment11_write_state_cookie($GLOBALS['assignment11_state'] ?? []);
    $GLOBALS['assignment11_state_dirty'] = false;
});

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function app_path(string $path): string
{
    return app_url($path);
}

function app_base_path(): string
{
    $basePath = $_SERVER['APP_BASE_PATH'] ?? ASSIGNMENT11_DEFAULT_BASE_PATH;

    if (!is_string($basePath) || $basePath === '') {
        return ASSIGNMENT11_DEFAULT_BASE_PATH;
    }

    $normalizedBasePath = '/' . trim($basePath, '/');

    return $normalizedBasePath === '/' ? ASSIGNMENT11_DEFAULT_BASE_PATH : $normalizedBasePath;
}

function app_request_path(): string
{
    $requestPath = $_SERVER['APP_REQUEST_PATH'] ?? null;
    if (is_string($requestPath) && $requestPath !== '') {
        return $requestPath;
    }

    $uriPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $uriPath = is_string($uriPath) && $uriPath !== '' ? $uriPath : '/';
    $basePath = app_base_path();

    if ($uriPath === $basePath) {
        return '/';
    }

    if (strpos($uriPath, $basePath . '/') === 0) {
        return substr($uriPath, strlen($basePath)) ?: '/';
    }

    return $uriPath;
}

function app_url(string $path = ''): string
{
    $basePath = app_base_path();
    if ($path === '') {
        return $basePath;
    }

    if ($path === '/') {
        return $basePath;
    }

    return $basePath . '/' . ltrim($path, '/');
}

function get_post_string(string $key): string
{
    $value = $_POST[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

function render_page(string $title, string $body): void
{
    http_response_code(200);
    echo '<!doctype html><html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'
        . escape_html($title)
        . '</title><link rel="stylesheet" href="/assets/launchpad.css"><link rel="stylesheet" href="/assets/assignments/11-sessions/styles.css"></head><body data-app-base-path="'
        . escape_html(app_base_path())
        . '" data-app-request-path="'
        . escape_html(app_request_path())
        . '"><main class="assignment-page"><article class="assignment-shell">'
        . $body
        . '</article></main></body></html>';
}

function session_namespace(string $key): array
{
    $state = assignment11_state();
    $value = $state[$key] ?? [];

    return is_array($value) ? $value : [];
}

function store_session_namespace(string $key, array $value): void
{
    $state = assignment11_state();
    $state[$key] = $value;
    $GLOBALS['assignment11_state'] = $state;
    $GLOBALS['assignment11_state_dirty'] = true;
}

function assignment11_state(): array
{
    $state = $GLOBALS['assignment11_state'] ?? [];

    return is_array($state) ? $state : [];
}

function read_state_cookie(): array
{
    return assignment11_state();
}

function write_state_cookie(array $state): void
{
    $GLOBALS['assignment11_state'] = $state;
    $GLOBALS['assignment11_state_dirty'] = false;
    assignment11_write_state_cookie($state);
}

function clear_state_cookie(): void
{
    $GLOBALS['assignment11_state'] = [];
    $GLOBALS['assignment11_state_dirty'] = false;

    setcookie(ASSIGNMENT11_STATE_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => app_base_path(),
        'secure' => assignment11_is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    unset($_COOKIE[ASSIGNMENT11_STATE_COOKIE]);
}

function redirect_to(string $path): void
{
    header('Location: ' . app_url($path), true, 303);
    exit;
}

function assignment11_load_state(): array
{
    $cookieValue = $_COOKIE[ASSIGNMENT11_STATE_COOKIE] ?? null;
    if (!is_string($cookieValue) || $cookieValue === '') {
        return [];
    }

    $cookiePayload = json_decode($cookieValue, true);
    if (!is_array($cookiePayload)) {
        return [];
    }

    $payload = $cookiePayload['payload'] ?? null;
    $signature = $cookiePayload['signature'] ?? null;
    if (!is_string($payload) || $payload === '' || !is_string($signature) || $signature === '') {
        return [];
    }

    $expectedSignature = hash_hmac('sha256', $payload, assignment11_state_secret());
    if (!hash_equals($expectedSignature, $signature)) {
        return [];
    }

    $state = json_decode($payload, true);

    return is_array($state) ? $state : [];
}

function assignment11_write_state_cookie(array $state): void
{
    $payload = json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($payload === false) {
        return;
    }

    $cookieValue = json_encode([
        'payload' => $payload,
        'signature' => hash_hmac('sha256', $payload, assignment11_state_secret()),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($cookieValue === false) {
        return;
    }

    setcookie(ASSIGNMENT11_STATE_COOKIE, $cookieValue, [
        'expires' => time() + ASSIGNMENT11_STATE_MAX_AGE,
        'path' => app_base_path(),
        'secure' => assignment11_is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    $_COOKIE[ASSIGNMENT11_STATE_COOKIE] = $cookieValue;
}

function assignment11_state_secret(): string
{
    $secret = getenv('ASSIGNMENT11_STATE_SECRET');
    if (is_string($secret) && $secret !== '') {
        return $secret;
    }

    return 'assignment11-local-dev-secret';
}

function assignment11_is_https_request(): bool
{
    $https = $_SERVER['HTTPS'] ?? '';

    return is_string($https) && $https !== '' && $https !== 'off';
}
