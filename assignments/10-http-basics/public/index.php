<?php
declare(strict_types=1);

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function request_path(): string
{
    $appRequestPath = $_SERVER['APP_REQUEST_PATH'] ?? null;
    if (is_string($appRequestPath) && $appRequestPath !== '') {
        return $appRequestPath;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);

    if (!is_string($path) || $path === '') {
        return '/';
    }

    $basePath = app_base_path();
    $basePathPrefix = $basePath === '/' ? '/' : $basePath . '/';
    if ($path === $basePath) {
        $path = '/';
    } elseif ($basePath !== '/' && strncmp($path, $basePathPrefix, strlen($basePathPrefix)) === 0) {
        $path = substr($path, strlen($basePath));
    }

    return $path !== '' ? $path : '/';
}

function app_base_path(): string
{
    $basePath = $_SERVER['APP_BASE_PATH'] ?? '';

    if (!is_string($basePath) || $basePath === '') {
        return '/';
    }

    return rtrim($basePath, '/') ?: '/';
}

function app_url(string $path = ''): string
{
    $basePath = app_base_path();
    $normalizedPath = ltrim($path, '/');

    if ($normalizedPath === '') {
        return $basePath;
    }

    return $basePath === '/'
        ? '/' . $normalizedPath
        : $basePath . '/' . $normalizedPath;
}

function send_html(int $statusCode, string $html, array $headers = []): void
{
    http_response_code($statusCode);
    header('Content-Type: text/html; charset=UTF-8');
    foreach ($headers as $name => $value) {
        header($name . ': ' . $value, true);
    }

    echo $html;
}

function send_text(int $statusCode, string $body, array $headers = []): void
{
    http_response_code($statusCode);
    header('Content-Type: text/plain; charset=UTF-8');
    foreach ($headers as $name => $value) {
        header($name . ': ' . $value, true);
    }

    echo $body;
}

function all_request_headers(): array
{
    if (function_exists('getallheaders')) {
        $headers = getallheaders();

        return is_array($headers) ? $headers : [];
    }

    $result = [];
    foreach ($_SERVER as $key => $value) {
        if (!is_string($value)) {
            continue;
        }

        if (strncmp($key, 'HTTP_', 5) === 0) {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
            $result[$name] = $value;
            continue;
        }

        if ($key === 'CONTENT_TYPE') {
            $result['Content-Type'] = $value;
            continue;
        }

        if ($key === 'CONTENT_LENGTH') {
            $result['Content-Length'] = $value;
        }
    }

    return $result;
}

function render_home_page(): string
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? 'не передан';
    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'не передан';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $appBasePath = app_base_path();

    return '<!doctype html>'
        . '<html lang="ru"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>10 HTTP Basics</title><link rel="stylesheet" href="/assets/launchpad.css"><link rel="stylesheet" href="/assets/assignments/10-http-basics/styles.css"></head><body data-app-base-path="' . escape_html($appBasePath) . '">' 

        . '<main>'
        . '<article class="assignment-shell">'
        . '<h1>Практика по HTTP в PHP</h1>'
        . '<p>Текущий метод: <strong>' . escape_html($method) . '</strong></p>'
        . '<p>Accept: <strong>' . escape_html($accept) . '</strong></p>'
        . '<p>Accept-Language: <strong>' . escape_html($acceptLanguage) . '</strong></p>'
        . '<p class="assignment-meta">Базовый путь страницы: <code>' . escape_html($appBasePath) . '</code>.</p>'
        . '<ul>'
        . '<li><a href="' . escape_html(app_url('method')) . '">/method</a> — определить GET или POST</li>'
        . '<li><a href="' . escape_html(app_url('headers')) . '">/headers</a> — список всех заголовков</li>'
        . '<li><a href="' . escape_html(app_url('status/200')) . '">/status/200</a></li>'
        . '<li><a href="' . escape_html(app_url('status/302')) . '">/status/302</a></li>'
        . '<li><a href="' . escape_html(app_url('status/400')) . '">/status/400</a></li>'
        . '<li><a href="' . escape_html(app_url('status/404')) . '">/status/404</a></li>'
        . '<li><a href="' . escape_html(app_url('redirect-target')) . '">/redirect-target</a> — цель редиректа 302</li>'
        . '</ul>'
        . '<form action="' . escape_html(app_url('method')) . '" method="post">'
        . '<button type="submit">Отправить POST на /method</button>'
        . '</form>'
        . '</article></main></body></html>';
}

$path = request_path();

if ($path === '/') {
    send_html(200, render_home_page());
    return;
}

if ($path === '/method') {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    send_text(200, 'Метод запроса: ' . $method);
    return;
}

if ($path === '/headers') {
    $headers = all_request_headers();
    $lines = [
        'Accept: ' . ($_SERVER['HTTP_ACCEPT'] ?? 'не передан'),
        'Accept-Language: ' . ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'не передан'),
        '',
        'Все заголовки:',
    ];

    foreach ($headers as $name => $value) {
        $lines[] = $name . ': ' . $value;
    }

    send_text(200, implode("\n", $lines));
    return;
}

if ($path === '/redirect-target') {
    send_text(200, 'Вы перешли на целевую страницу после редиректа.');
    return;
}

if (preg_match('#^/status/(200|302|400|404)$#', $path, $matches) === 1) {
    $statusCode = (int) $matches[1];

    if ($statusCode === 302) {
        send_text(302, 'Временный редирект на /redirect-target', ['Location' => app_url('redirect-target')]);
        return;
    }

    $messages = [
        200 => 'HTTP 200 OK',
        400 => 'HTTP 400 Bad Request',
        404 => 'HTTP 404 Not Found',
    ];

    send_text($statusCode, $messages[$statusCode]);
    return;
}

send_text(404, 'Маршрут не найден. Используйте /status/404 для явной демонстрации 404.');
