<?php
declare(strict_types=1);

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function request_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);

    return is_string($path) && $path !== '' ? $path : '/';
}

function send_html(int $statusCode, string $html, array $headers = []): void
{
    http_response_code($statusCode);
    header('Content-Type: text/html; charset=UTF-8');
    foreach ($headers as $name => $value) {
        header($name . ': ' . $value, true);
    }

    echo $html;
    exit;
}

function send_text(int $statusCode, string $body, array $headers = []): void
{
    http_response_code($statusCode);
    header('Content-Type: text/plain; charset=UTF-8');
    foreach ($headers as $name => $value) {
        header($name . ': ' . $value, true);
    }

    echo $body;
    exit;
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

    return '<!doctype html>'
        . '<html lang="ru"><head><meta charset="UTF-8"><title>10 HTTP Basics</title></head><body>'
        . '<main>'
        . '<h1>Практика по HTTP в PHP</h1>'
        . '<p>Текущий метод: <strong>' . escape_html($method) . '</strong></p>'
        . '<p>Accept: <strong>' . escape_html($accept) . '</strong></p>'
        . '<p>Accept-Language: <strong>' . escape_html($acceptLanguage) . '</strong></p>'
        . '<ul>'
        . '<li><a href="/method">/method</a> — определить GET или POST</li>'
        . '<li><a href="/headers">/headers</a> — список всех заголовков</li>'
        . '<li><a href="/status/200">/status/200</a></li>'
        . '<li><a href="/status/302">/status/302</a></li>'
        . '<li><a href="/status/400">/status/400</a></li>'
        . '<li><a href="/status/404">/status/404</a></li>'
        . '<li><a href="/redirect-target">/redirect-target</a> — цель редиректа 302</li>'
        . '</ul>'
        . '<form action="/method" method="post">'
        . '<button type="submit">Отправить POST на /method</button>'
        . '</form>'
        . '</main></body></html>';
}

$path = request_path();

if ($path === '/') {
    send_html(200, render_home_page());
}

if ($path === '/method') {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    send_text(200, 'Метод запроса: ' . $method);
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
}

if ($path === '/redirect-target') {
    send_text(200, 'Вы перешли на целевую страницу после редиректа.');
}

if (preg_match('#^/status/(200|302|400|404)$#', $path, $matches) === 1) {
    $statusCode = (int) $matches[1];

    if ($statusCode === 302) {
        send_text(302, 'Временный редирект на /redirect-target', ['Location' => '/redirect-target']);
    }

    $messages = [
        200 => 'HTTP 200 OK',
        400 => 'HTTP 400 Bad Request',
        404 => 'HTTP 404 Not Found',
    ];

    send_text($statusCode, $messages[$statusCode]);
}

send_text(404, 'Маршрут не найден. Используйте /status/404 для явной демонстрации 404.');
