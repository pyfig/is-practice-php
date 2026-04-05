<?php
declare(strict_types=1);

function auth_db_config(): array
{
    $keys = ['AUTH_DB_HOST', 'AUTH_DB_PORT', 'AUTH_DB_USER', 'AUTH_DB_PASSWORD', 'AUTH_DB_NAME'];
    $config = [];
    $missing = [];

    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value === false || $value === '') {
            $missing[] = $key;
            continue;
        }

        $config[$key] = $value;
    }

    return ['config' => $config, 'missing' => $missing];
}

function auth_db_connection(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $resolved = auth_db_config();
    if ($resolved['missing'] !== []) {
        throw new RuntimeException('Отсутствуют переменные окружения: ' . implode(', ', $resolved['missing']));
    }

    $config = $resolved['config'];
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['AUTH_DB_HOST'],
        $config['AUTH_DB_PORT'],
        $config['AUTH_DB_NAME']
    );

    $pdo = new PDO(
        $dsn,
        $config['AUTH_DB_USER'],
        $config['AUTH_DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $pdo;
}
