<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function app_path(string $path): string
{
    return $path;
}

function get_post_string(string $key): string
{
    $value = $_POST[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

function render_page(string $title, string $body): void
{
    http_response_code(200);
    echo '<!doctype html><html lang="ru"><head><meta charset="UTF-8"><title>'
        . escape_html($title)
        . '</title><style>body{font-family:Arial,sans-serif;background:#f8fafc;color:#0f172a;margin:0;}main{max-width:900px;margin:0 auto;padding:24px;}section{background:#fff;border:1px solid #cbd5e1;padding:16px;margin-bottom:16px;}form{display:grid;gap:10px;}label{display:grid;gap:4px;}input,button{font:inherit;padding:8px;}a{color:#2563eb;}nav ul{padding-left:20px;}</style></head><body><main>'
        . $body
        . '</main></body></html>';
}

function session_namespace(string $key): array
{
    $value = $_SESSION[$key] ?? [];

    return is_array($value) ? $value : [];
}

function store_session_namespace(string $key, array $value): void
{
    $_SESSION[$key] = $value;
}

function redirect_to(string $path): void
{
    header('Location: ' . app_path($path), true, 303);
    exit;
}
