<?php
declare(strict_types=1);

function auth_db_config(): array
{
    $keys = ['AUTH_DB_HOST', 'AUTH_DB_PORT', 'AUTH_DB_USER', 'AUTH_DB_PASSWORD', 'AUTH_DB_NAME'];
    $config = [];
    $missing = [];

    foreach ($keys as $key) {
        $value = getenv($key);
        $requiresNonEmptyValue = $key !== 'AUTH_DB_PASSWORD';

        if ($value === false || ($requiresNonEmptyValue && $value === '')) {
            $missing[] = $key;
            continue;
        }

        $config[$key] = $value;
    }

    return ['config' => $config, 'missing' => $missing];
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
            'reason' => 'config_missing',
            'message' => 'Конфигурация БД не завершена. Для полноценной проверки задайте переменные: ' . implode(', ', $resolved['missing']) . '.',
        ];
        return $status;
    }

    $config = $resolved['config'];
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['AUTH_DB_HOST'],
        $config['AUTH_DB_PORT'],
        $config['AUTH_DB_NAME']
    );

    try {
        $pdo = new PDO(
            $dsn,
            $config['AUTH_DB_USER'],
            $config['AUTH_DB_PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        
        // Test the connection
        $pdo->query('SELECT 1');
        
        $status = [
            'configured' => true,
            'missing' => [],
            'available' => true,
            'reason' => 'ok',
            'message' => null,
        ];
        return $status;
    } catch (PDOException $e) {
        $status = [
            'configured' => true,
            'missing' => [],
            'available' => false,
            'reason' => 'connection_failed',
            'message' => 'База данных временно недоступна. Пожалуйста, попробуйте позже.',
        ];
        return $status;
    }
}

function auth_db_connection(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $status = auth_db_status();
    if ($status['available'] === false) {
        throw new RuntimeException($status['message'] ?? 'База данных недоступна');
    }

    $resolved = auth_db_config();
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
