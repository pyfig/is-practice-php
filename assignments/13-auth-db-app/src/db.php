<?php
declare(strict_types=1);

function assignment13_load_env_file_once(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }

    $loaded = true;
    $rootDir = dirname(__DIR__, 3);
    $envFiles = [
        $rootDir . '/.env.supabase.local',
        $rootDir . '/.env.vercel.local',
    ];

    foreach ($envFiles as $envFile) {
        if (!is_file($envFile) || !is_readable($envFile)) {
            continue;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES);
        if (!is_array($lines)) {
            continue;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (preg_match('/^([A-Z0-9_]+)\s*=\s*(.*)$/', $line, $matches) !== 1) {
                continue;
            }

            $key = $matches[1];
            $value = trim($matches[2]);

            if ($value !== '' && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === '\'' && str_ends_with($value, '\'')))) {
                $value = substr($value, 1, -1);
            }

            if (getenv($key) !== false) {
                continue;
            }

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv($key . '=' . $value);
        }
    }
}

function auth_db_config(): array
{
    assignment13_load_env_file_once();

    $keys = [
        'SUPABASE_URL',
        'SUPABASE_SERVICE_ROLE_KEY',
    ];

    $config = [];
    $missing = [];

    foreach ($keys as $key) {
        $value = getenv($key);
        if (!is_string($value) || $value === '') {
            $missing[] = $key;
            continue;
        }

        $config[$key] = $value;
    }

    return ['config' => $config, 'missing' => $missing];
}

function auth_db_public_config(array $config): array
{
    return [
        'SUPABASE_URL' => $config['SUPABASE_URL'] ?? null,
    ];
}

function auth_db_request_url(string $path, array $query = []): string
{
    $resolved = auth_db_config();
    $config = $resolved['config'];
    $baseUrl = rtrim((string) ($config['SUPABASE_URL'] ?? ''), '/');
    $url = $baseUrl . $path;

    if ($query !== []) {
        $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    return $url;
}

function auth_db_request_headers(array $extraHeaders = []): array
{
    $resolved = auth_db_config();
    $config = $resolved['config'];
    $apiKey = (string) ($config['SUPABASE_SERVICE_ROLE_KEY'] ?? '');

    return array_merge(
        [
            'apikey: ' . $apiKey,
            'Authorization: Bearer ' . $apiKey,
            'Accept: application/json',
            'Content-Type: application/json',
        ],
        $extraHeaders
    );
}

function auth_db_http_request(string $method, string $path, array $query = [], ?array $payload = null, array $extraHeaders = []): array
{
    $url = auth_db_request_url($path, $query);
    $headers = auth_db_request_headers($extraHeaders);
    $body = $payload === null ? null : json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($body === false) {
        throw new RuntimeException('Не удалось подготовить запрос к Supabase.');
    }

    if (function_exists('curl_init')) {
        $handle = curl_init($url);
        if ($handle === false) {
            throw new RuntimeException('Не удалось инициализировать HTTP-клиент.');
        }

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER => true,
        ]);

        if ($body !== null && $body !== '') {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }

        $rawResponse = curl_exec($handle);
        if ($rawResponse === false) {
            $error = curl_error($handle);
            throw new RuntimeException('Не удалось выполнить запрос к Supabase: ' . $error);
        }

        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $responseBody = substr($rawResponse, $headerSize);
    } else {
        $contextHeaders = implode("\r\n", $headers);
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => $contextHeaders,
                'content' => $body ?? '',
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]);

        $responseBody = @file_get_contents($url, false, $context);
        if ($responseBody === false) {
            throw new RuntimeException('Не удалось выполнить запрос к Supabase.');
        }

        $status = 0;
        foreach ($http_response_header ?? [] as $headerLine) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $headerLine, $matches) === 1) {
                $status = (int) $matches[1];
                break;
            }
        }
    }

    $decoded = null;
    if ($responseBody !== '') {
        $decoded = json_decode($responseBody, true);
    }

    return [
        'status' => $status,
        'body' => $responseBody,
        'json' => is_array($decoded) ? $decoded : null,
    ];
}

function auth_db_error_text(array $response, string $fallback): string
{
    $json = $response['json'] ?? null;
    if (!is_array($json)) {
        return $fallback;
    }

    foreach (['message', 'msg', 'hint'] as $key) {
        if (isset($json[$key]) && is_string($json[$key]) && $json[$key] !== '') {
            return $json[$key];
        }
    }

    return $fallback;
}

function auth_db_status(): array
{
    static $status = null;
    if ($status !== null) {
        return $status;
    }

    $resolved = auth_db_config();
    if ($resolved['missing'] !== []) {
        $status = [
            'configured' => false,
            'missing' => $resolved['missing'],
            'available' => false,
            'config' => auth_db_public_config($resolved['config']),
            'reason' => 'config_missing',
            'message' => 'Конфигурация Supabase не завершена. Для полноценной проверки задайте переменные: ' . implode(', ', $resolved['missing']) . '.',
        ];

        return $status;
    }

    try {
        $response = auth_db_http_request('GET', '/rest/v1/users', ['select' => 'id', 'limit' => '1']);

        if ($response['status'] >= 200 && $response['status'] < 300) {
            $status = [
                'configured' => true,
                'missing' => [],
                'available' => true,
                'config' => auth_db_public_config($resolved['config']),
                'reason' => 'ok',
                'message' => null,
            ];

            return $status;
        }

        $reason = 'connection_failed';
        $message = 'Supabase временно недоступен. Пожалуйста, попробуйте позже.';
        $json = $response['json'] ?? null;
        if (is_array($json) && (($json['code'] ?? null) === '42P01' || str_contains(strtolower((string) ($json['message'] ?? '')), 'relation'))) {
            $reason = 'schema_missing';
            $message = 'Схема базы данных ещё не инициализирована. Сначала выполните reset/bootstrapping для assignment 13.';
        }

        $status = [
            'configured' => true,
            'missing' => [],
            'available' => false,
            'config' => auth_db_public_config($resolved['config']),
            'reason' => $reason,
            'message' => $message,
        ];

        return $status;
    } catch (Throwable $throwable) {
        $status = [
            'configured' => true,
            'missing' => [],
            'available' => false,
            'config' => auth_db_public_config($resolved['config']),
            'reason' => 'connection_failed',
            'message' => 'Supabase временно недоступен. Пожалуйста, попробуйте позже.',
        ];

        return $status;
    }
}

function auth_db_ready_status(): array
{
    return auth_db_status();
}

function auth_db_init_schema(): void
{
    // Runtime schema changes are not performed from the app.
}

function auth_db_fetch_user_by_email(string $email): ?array
{
    $response = auth_db_http_request('GET', '/rest/v1/users', [
        'select' => 'id,full_name,email,password_hash,created_at',
        'email' => 'eq.' . $email,
        'limit' => '1',
    ]);

    if ($response['status'] !== 200) {
        throw new RuntimeException(auth_db_error_text($response, 'Не удалось прочитать пользователя из Supabase.'));
    }

    $rows = $response['json'] ?? [];
    if (!is_array($rows) || $rows === []) {
        return null;
    }

    $user = $rows[0] ?? null;

    return is_array($user) ? $user : null;
}

function auth_db_fetch_user_by_id(int $userId): ?array
{
    $response = auth_db_http_request('GET', '/rest/v1/users', [
        'select' => 'id,full_name,email,created_at',
        'id' => 'eq.' . $userId,
        'limit' => '1',
    ]);

    if ($response['status'] !== 200) {
        throw new RuntimeException(auth_db_error_text($response, 'Не удалось прочитать пользователя из Supabase.'));
    }

    $rows = $response['json'] ?? [];
    if (!is_array($rows) || $rows === []) {
        return null;
    }

    $user = $rows[0] ?? null;

    return is_array($user) ? $user : null;
}

function auth_db_insert_user(string $fullName, string $email, string $passwordHash): void
{
    $response = auth_db_http_request(
        'POST',
        '/rest/v1/users',
        [],
        [
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $passwordHash,
        ],
        ['Prefer: return=representation']
    );

    if ($response['status'] >= 200 && $response['status'] < 300) {
        return;
    }

    $json = $response['json'] ?? [];
    if (($response['status'] === 409) || (is_array($json) && (($json['code'] ?? null) === '23505'))) {
        throw new RuntimeException('Пользователь с таким email уже существует.');
    }

    throw new RuntimeException(auth_db_error_text($response, 'Не удалось сохранить пользователя в Supabase.'));
}

function auth_db_fetch_active_session(string $tokenHash): ?array
{
    $response = auth_db_http_request('GET', '/rest/v1/user_sessions', [
        'select' => 'id,user_id,token_hash,expires_at,last_seen_at,created_at',
        'token_hash' => 'eq.' . $tokenHash,
        'expires_at' => 'gt.' . gmdate('c'),
        'limit' => '1',
    ]);

    if ($response['status'] !== 200) {
        throw new RuntimeException(auth_db_error_text($response, 'Не удалось прочитать сессию из Supabase.'));
    }

    $rows = $response['json'] ?? [];
    if (!is_array($rows) || $rows === []) {
        return null;
    }

    $session = $rows[0] ?? null;

    return is_array($session) ? $session : null;
}

function auth_db_insert_session(int $userId, string $tokenHash, string $expiresAtIso): void
{
    $response = auth_db_http_request(
        'POST',
        '/rest/v1/user_sessions',
        [],
        [
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAtIso,
            'last_seen_at' => gmdate('c'),
        ]
    );

    if ($response['status'] >= 200 && $response['status'] < 300) {
        return;
    }

    throw new RuntimeException(auth_db_error_text($response, 'Не удалось сохранить сессию в Supabase.'));
}

function auth_db_touch_session(string $tokenHash): void
{
    $response = auth_db_http_request(
        'PATCH',
        '/rest/v1/user_sessions',
        ['token_hash' => 'eq.' . $tokenHash],
        ['last_seen_at' => gmdate('c')]
    );

    if ($response['status'] >= 200 && $response['status'] < 300) {
        return;
    }

    throw new RuntimeException(auth_db_error_text($response, 'Не удалось обновить сессию в Supabase.'));
}

function auth_db_delete_session(string $tokenHash): void
{
    $response = auth_db_http_request(
        'DELETE',
        '/rest/v1/user_sessions',
        ['token_hash' => 'eq.' . $tokenHash]
    );

    if ($response['status'] >= 200 && $response['status'] < 300) {
        return;
    }

    throw new RuntimeException(auth_db_error_text($response, 'Не удалось удалить сессию в Supabase.'));
}
