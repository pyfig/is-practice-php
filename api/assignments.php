<?php
declare(strict_types=1);

function vercel_assignment_manifest(): array
{
    return [
        '01-php-basics' => [
            'slug' => '01-php-basics',
            'strategy' => 'root_index',
            'entry' => 'assignments/01-php-basics/public/index.php',
        ],
        '02-control-structures' => [
            'slug' => '02-control-structures',
            'strategy' => 'root_index',
            'entry' => 'assignments/02-control-structures/public/index.php',
        ],
        '03-arrays' => [
            'slug' => '03-arrays',
            'strategy' => 'root_index',
            'entry' => 'assignments/03-arrays/public/index.php',
        ],
        '04-associative-arrays' => [
            'slug' => '04-associative-arrays',
            'strategy' => 'root_index',
            'entry' => 'assignments/04-associative-arrays/public/index.php',
        ],
        '05-multidimensional-arrays' => [
            'slug' => '05-multidimensional-arrays',
            'strategy' => 'root_index',
            'entry' => 'assignments/05-multidimensional-arrays/public/index.php',
        ],
        '06-user-functions' => [
            'slug' => '06-user-functions',
            'strategy' => 'root_index',
            'entry' => 'assignments/06-user-functions/public/index.php',
        ],
        '07-standard-functions' => [
            'slug' => '07-standard-functions',
            'strategy' => 'root_index',
            'entry' => 'assignments/07-standard-functions/public/index.php',
        ],
        '08-string-generation' => [
            'slug' => '08-string-generation',
            'strategy' => 'index_only',
            'entry' => 'assignments/08-string-generation/public/index.php',
        ],
        '09-forms' => [
            'slug' => '09-forms',
            'strategy' => 'public_php_files',
            'entry' => 'assignments/09-forms/public/index.php',
            'public_dir' => 'assignments/09-forms/public',
        ],
        '10-http-basics' => [
            'slug' => '10-http-basics',
            'strategy' => 'front_controller',
            'entry' => 'assignments/10-http-basics/public/index.php',
        ],
        '11-sessions' => [
            'slug' => '11-sessions',
            'strategy' => 'public_php_files',
            'entry' => 'assignments/11-sessions/public/index.php',
            'public_dir' => 'assignments/11-sessions/public',
        ],
        '12-regex-validation' => [
            'slug' => '12-regex-validation',
            'strategy' => 'index_only',
            'entry' => 'assignments/12-regex-validation/public/index.php',
        ],
        '13-auth-db-app' => [
            'slug' => '13-auth-db-app',
            'strategy' => 'public_php_files',
            'entry' => 'assignments/13-auth-db-app/public/index.php',
            'public_dir' => 'assignments/13-auth-db-app/public',
        ],
    ];
}

function dispatch_vercel_request(): void
{
    $manifest = vercel_assignment_manifest();
    $requestPath = vercel_incoming_request_path();

    if ($requestPath === '/') {
        vercel_render_root_placeholder($manifest);
    }

    [$slug, $assignmentRequestPath] = vercel_split_assignment_route($requestPath);

    if ($slug === null || !isset($manifest[$slug])) {
        vercel_render_not_found('Неизвестный assignment slug.');
    }

    if ($assignmentRequestPath === '/') {
        vercel_redirect_to_canonical_assignment_root($slug);
    }

    $assignment = $manifest[$slug];
    $mountedRequestPath = $assignmentRequestPath === '' ? '/' : $assignmentRequestPath;
    $targetFile = vercel_resolve_assignment_target($assignment, $mountedRequestPath);

    if ($targetFile === null) {
        vercel_render_not_found('Маршрут задания не найден.');
    }

    vercel_expose_assignment_context($assignment['slug'], $mountedRequestPath, $targetFile);

    require $targetFile;
    exit;
}

function vercel_incoming_request_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);

    if (!is_string($path) || $path === '') {
        return '/';
    }

    return $path;
}

function vercel_split_assignment_route(string $requestPath): array
{
    if (preg_match('#^/([^/]+)(/.*)?$#', $requestPath, $matches) !== 1) {
        return [null, null];
    }

    $slug = $matches[1];
    $assignmentRequestPath = $matches[2] ?? '';

    return [$slug, $assignmentRequestPath];
}

function vercel_resolve_assignment_target(array $assignment, string $mountedRequestPath): ?string
{
    $rootDir = dirname(__DIR__);
    $defaultEntry = $rootDir . '/' . $assignment['entry'];

    if ($assignment['strategy'] === 'root_index' || $assignment['strategy'] === 'index_only') {
        return $mountedRequestPath === '/' && is_file($defaultEntry) ? $defaultEntry : null;
    }

    if ($assignment['strategy'] === 'front_controller') {
        return is_file($defaultEntry) ? $defaultEntry : null;
    }

    if ($assignment['strategy'] === 'public_php_files') {
        if ($mountedRequestPath === '/') {
            return is_file($defaultEntry) ? $defaultEntry : null;
        }

        return vercel_resolve_public_php_file($rootDir, $assignment['public_dir'], $mountedRequestPath);
    }

    return null;
}

function vercel_resolve_public_php_file(string $rootDir, string $publicDir, string $mountedRequestPath): ?string
{
    $relativePath = ltrim($mountedRequestPath, '/');
    if ($relativePath === '' || str_contains($relativePath, "\0") || str_contains($relativePath, '..')) {
        return null;
    }

    $publicRoot = realpath($rootDir . '/' . $publicDir);
    if ($publicRoot === false) {
        return null;
    }

    $candidate = realpath($publicRoot . '/' . $relativePath);
    if ($candidate === false || !is_file($candidate)) {
        return null;
    }

    if (strpos($candidate, $publicRoot . DIRECTORY_SEPARATOR) !== 0 || pathinfo($candidate, PATHINFO_EXTENSION) !== 'php') {
        return null;
    }

    return $candidate;
}

function vercel_expose_assignment_context(string $slug, string $mountedRequestPath, string $targetFile): void
{
    $basePath = '/' . $slug;
    $queryString = isset($_SERVER['QUERY_STRING']) && is_string($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    $appRequestUri = $mountedRequestPath . ($queryString !== '' ? '?' . $queryString : '');

    vercel_set_mount_value('APP_SLUG', $slug);
    vercel_set_mount_value('APP_BASE_PATH', $basePath);
    vercel_set_mount_value('APP_REQUEST_PATH', $mountedRequestPath);

    $_SERVER['ORIGINAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? $basePath;
    $_SERVER['REQUEST_URI'] = $appRequestUri;
    $_SERVER['SCRIPT_FILENAME'] = $targetFile;
    $_SERVER['SCRIPT_NAME'] = $mountedRequestPath === '/' ? '/index.php' : $mountedRequestPath;
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
}

function vercel_set_mount_value(string $name, string $value): void
{
    $_SERVER[$name] = $value;
    $_ENV[$name] = $value;
    putenv($name . '=' . $value);
}

function vercel_redirect_to_canonical_assignment_root(string $slug): void
{
    $location = '/' . $slug;
    $queryString = isset($_SERVER['QUERY_STRING']) && is_string($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    if ($queryString !== '') {
        $location .= '?' . $queryString;
    }

    http_response_code(308);
    header('Location: ' . $location, true, 308);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Используйте канонический маршрут без завершающего слеша.';
    exit;
}

function vercel_render_root_placeholder(array $manifest): void
{
    http_response_code(200);
    header('Content-Type: text/html; charset=UTF-8');

    $assignments = vercel_get_assignment_metadata();
    $totalCount = count($assignments);
    $cliCount = count(array_filter($assignments, fn($m) => $m['type'] === 'cli'));
    $webCount = count(array_filter($assignments, fn($m) => $m['type'] === 'web'));
    $statefulCount = count(array_filter($assignments, fn($m) => $m['type'] === 'stateful'));

    echo '<!doctype html>' . "\n";
    echo '<html lang="ru">' . "\n";
    echo '<head>' . "\n";
    echo '    <meta charset="UTF-8">' . "\n";
    echo '    <meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
    echo '    <title>labs</title>' . "\n";
    echo '    <link rel="stylesheet" href="/assets/launchpad.css">' . "\n";
    echo '</head>' . "\n";
    echo '<body>' . "\n";
    echo '    <header class="launchpad-header">' . "\n";
    echo '        <a href="/" class="home-logo" data-home-logo>labs</a>' . "\n";
    echo '    </header>' . "\n";
    echo '    <main class="launchpad-container">' . "\n";
    echo '        <section class="launchpad-hero">' . "\n";
    echo '            <h1 class="launchpad-hero-title">Практические задания по PHP</h1>' . "\n";
    echo '            <p class="launchpad-hero-subtitle">13 интерактивных заданий от базового синтаксиса до работы с базами данных и сессиями</p>' . "\n";
    echo '            <div class="launchpad-stats">' . "\n";
    echo '                <div class="stat-item"><div class="stat-value">' . $cliCount . '</div><div class="stat-label">CLI</div></div>' . "\n";
    echo '                <div class="stat-item"><div class="stat-value">' . $webCount . '</div><div class="stat-label">Web</div></div>' . "\n";
    echo '                <div class="stat-item"><div class="stat-value">' . $statefulCount . '</div><div class="stat-label">Stateful</div></div>' . "\n";
    echo '            </div>' . "\n";
    echo '        </section>' . "\n";
    echo '        <div class="launchpad-grid" data-launchpad-grid>' . "\n";

    $counter = 1;
    foreach ($assignments as $slug => $meta) {
        $typeClass = 'card-badge--' . $meta['type'];
        $typeLabel = $meta['type'] === 'cli' ? 'CLI' : ($meta['type'] === 'web' ? 'Web' : 'Stateful');

        echo '            <a href="/' . vercel_escape_html($slug) . '" class="assignment-card" data-assignment-card data-assignment-slug="' . vercel_escape_html($slug) . '" data-assignment-type="' . vercel_escape_html($meta['type']) . '" data-assignment-description="' . vercel_escape_html($meta['description']) . '">' . "\n";
        echo '                <div class="card-number">' . sprintf('%02d', $counter) . '</div>' . "\n";
        echo '                <div class="card-header">' . "\n";
        echo '                    <span class="card-title">' . vercel_escape_html($meta['title']) . '</span>' . "\n";
        echo '                    <span class="card-badge ' . $typeClass . '">' . $typeLabel . '</span>' . "\n";
        echo '                </div>' . "\n";
        echo '                <p class="card-description">' . vercel_escape_html($meta['description']) . '</p>' . "\n";
        echo '                <div class="card-arrow">→</div>' . "\n";
        echo '            </a>' . "\n";
        $counter++;
    }

    echo '        </div>' . "\n";
    echo '    </main>' . "\n";
    echo '    <footer class="launchpad-footer">' . "\n";
    echo '        <p>PHP Practice Labs • 13 Assignments</p>' . "\n";
    echo '    </footer>' . "\n";
    echo '</body>' . "\n";
    echo '</html>' . "\n";
    exit;
}

function vercel_get_assignment_metadata(): array
{
    return [
        '01-php-basics' => [
            'title' => '01 PHP Basics',
            'description' => 'Beginner PHP output, string basics, and simple geometry formulas',
            'type' => 'cli',
        ],
        '02-control-structures' => [
            'title' => '02 Control Structures',
            'description' => 'Conditionals, loops, filtering, and aggregation',
            'type' => 'cli',
        ],
        '03-arrays' => [
            'title' => '03 Arrays',
            'description' => 'Indexed arrays, search, fill, ranges, sums, shuffling, and sorting',
            'type' => 'cli',
        ],
        '04-associative-arrays' => [
            'title' => '04 Associative Arrays',
            'description' => 'Keyed array creation, personal and date data, counts',
            'type' => 'cli',
        ],
        '05-multidimensional-arrays' => [
            'title' => '05 Multidimensional Arrays',
            'description' => 'Nested arrays, nested loops, table rendering, and structured datasets',
            'type' => 'cli',
        ],
        '06-user-functions' => [
            'title' => '06 User Functions',
            'description' => 'Reusable functions, return values, boolean checks, and numeric helpers',
            'type' => 'cli',
        ],
        '07-standard-functions' => [
            'title' => '07 Standard Functions',
            'description' => 'Built-in string, array, math, and date functions',
            'type' => 'cli',
        ],
        '08-string-generation' => [
            'title' => '08 String Generation',
            'description' => 'Generate HTML from PHP variables, loops, and conditions',
            'type' => 'web',
        ],
        '09-forms' => [
            'title' => '09 Forms',
            'description' => 'GET and POST forms, server-side validation, and common input controls',
            'type' => 'web',
        ],
        '10-http-basics' => [
            'title' => '10 HTTP Basics',
            'description' => 'Request method detection, header inspection, and HTTP status responses',
            'type' => 'web',
        ],
        '11-sessions' => [
            'title' => '11 Sessions',
            'description' => 'Session persistence, counters, prefills, logout, and multi-page quiz',
            'type' => 'stateful',
        ],
        '12-regex-validation' => [
            'title' => '12 Regex Validation',
            'description' => 'Form validation with regex rules for email, login, password, and phone',
            'type' => 'web',
        ],
        '13-auth-db-app' => [
            'title' => '13 Auth DB App',
            'description' => 'Registration, login, logout, and authenticated user display with database persistence',
            'type' => 'stateful',
        ],
    ];
}

function vercel_render_not_found(string $message): void
{
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $message;
    exit;
}

function vercel_escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
