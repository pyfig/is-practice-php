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

    // Skip home header for cookie-based session assignments to avoid header() conflicts
    $skipHeaderSlugs = ['11-sessions', '13-auth-db-app'];
    $shouldOutputHeader = !in_array($slug, $skipHeaderSlugs, true);
    
    if ($shouldOutputHeader) {
        // Start output buffering to capture assignment output
        ob_start();
    }
    
    require $targetFile;
    
    if ($shouldOutputHeader) {
        $assignmentOutput = ob_get_clean();
        // Output home navigation header before assignment content
        vercel_output_home_header($slug);
        echo $assignmentOutput;
    }
    exit;
}

function vercel_output_home_header(string $currentSlug): void
{
    $homeUrl = '/';
    $assignmentMeta = vercel_get_assignment_metadata();
    $currentTitle = $assignmentMeta[$currentSlug]['title'] ?? $currentSlug;

    echo '<style>
        .home-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .home-nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .home-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #007AFF;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .home-link:hover {
            background: #0051D5;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
        }
        .home-link:active {
            transform: scale(0.98);
        }
        .home-link svg {
            width: 18px;
            height: 18px;
        }
        .current-page {
            font-size: 0.875rem;
            color: #666;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        body {
            padding-top: 60px !important;
        }
    </style>';

    echo '<nav class="home-nav" role="navigation" aria-label="Главная навигация">';
    echo '    <div class="home-nav-content">';
    echo '        <span class="current-page">' . vercel_escape_html($currentTitle) . '</span>';
    echo '        <a href="' . $homeUrl . '" class="home-link" aria-label="Вернуться на главную">';
    echo '            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
    echo '                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>';
    echo '                <polyline points="9 22 9 12 15 12 15 22"/>';
    echo '            </svg>';
    echo '            <span>На главную</span>';
    echo '        </a>';
    echo '    </div>';
    echo '</nav>';
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
    $colors = ['blue', 'purple', 'green', 'orange', 'red', 'teal', 'indigo', 'pink', 'yellow', 'cyan', 'blue', 'purple', 'green'];
    $colorIndex = 0;

    echo '<!DOCTYPE html>' . "\n";
    echo '<html lang="ru">' . "\n";
    echo '<head>' . "\n";
    echo '    <meta charset="UTF-8">' . "\n";
    echo '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
    echo '    <title>Labs </title>' . "\n";
    echo '    <style>' . vercel_get_launchpad_styles() . '</style>' . "\n";
    echo '</head>' . "\n";
    echo '<body>' . "\n";

    echo '    <header class="launchpad-header" role="banner">' . "\n";
    echo '        <div class="header-content">' . "\n";
    echo '            <div class="header-brand">' . "\n";
    echo '                <div class="header-brand-icon">' . "\n";
    echo '                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>' . "\n";
    echo '                </div>' . "\n";
    echo '                <span>Labs</span>' . "\n";
    echo '            </div>' . "\n";
    echo '            <div class="search-container" role="search">' . "\n";
    echo '                <div class="search-wrapper">' . "\n";
    echo '                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>' . "\n";
    echo '                    <input type="text" class="search-input" id="searchInput" placeholder="Поиск работ..." aria-label="Поиск работ по названию" autocomplete="off"/>' . "\n";
    echo '                    <button class="search-clear" id="searchClear" aria-label="Очистить поиск" type="button">' . "\n";
    echo '                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>' . "\n";
    echo '                    </button>' . "\n";
    echo '                </div>' . "\n";
    echo '                <div class="search-results-info" id="searchResultsInfo" aria-live="polite"></div>' . "\n";
    echo '            </div>' . "\n";
    echo '        </div>' . "\n";
    echo '    </header>' . "\n";

    echo '    <main class="launchpad-main" role="main">' . "\n";
    echo '        <div class="launchpad-content">' . "\n";
    echo '            <div class="project-grid" id="projectGrid" role="list">' . "\n";

    foreach ($assignments as $slug => $meta) {
        $color = $colors[$colorIndex % count($colors)];
        $colorIndex++;
        $initials = vercel_get_initials($meta['title']);
        $searchData = vercel_escape_html(strtolower($meta['title'] . ' ' . $meta['description']));

        echo '                <a href="/' . vercel_escape_html($slug) . '" class="project-card" role="listitem" data-project-name="' . $searchData . '" aria-label="' . vercel_escape_html($meta['title']) . ': ' . vercel_escape_html($meta['description']) . '">' . "\n";
        echo '                    <div class="project-icon icon-' . $color . '">' . $initials . '</div>' . "\n";
        echo '                    <div class="project-info">' . "\n";
        echo '                        <span class="project-name">' . vercel_escape_html($meta['title']) . '</span>' . "\n";
        echo '                        <span class="project-subtitle">' . vercel_escape_html($meta['description']) . '</span>' . "\n";
        echo '                    </div>' . "\n";
        echo '                </a>' . "\n";
    }

    echo '            </div>' . "\n";

    echo '            <div class="empty-state" id="emptyState" role="status">' . "\n";
    echo '                <div class="empty-state-icon">' . "\n";
    echo '                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>' . "\n";
    echo '                </div>' . "\n";
    echo '                <h2 class="empty-state-title">Работы не найдены</h2>' . "\n";
    echo '                <p class="empty-state-message">По вашему запросу ничего не найдено. Попробуйте другие ключевые слова.</p>' . "\n";
    echo '                <button class="empty-state-clear" id="emptyStateClear">Очистить поиск</button>' . "\n";
    echo '            </div>' . "\n";

    echo '        </div>' . "\n";
    echo '    </main>' . "\n";

    echo '    <script>' . vercel_get_launchpad_scripts() . '</script>' . "\n";
    echo '</body>' . "\n";
    echo '</html>' . "\n";
    exit;
}

function vercel_get_initials(string $title): string
{
    $parts = explode(' ', $title);
    $initials = '';
    foreach ($parts as $part) {
        if (strlen($part) > 0 && ctype_alpha($part[0])) {
            $initials .= strtoupper($part[0]);
            if (strlen($initials) >= 2) {
                break;
            }
        }
    }
    return $initials ?: '??';
}

function vercel_get_launchpad_styles(): string
{
    return '
:root {
    --bg-gradient-start: #1a1a2e;
    --bg-gradient-mid: #16213e;
    --bg-gradient-end: #0f3460;
    --glass-bg: rgba(255, 255, 255, 0.08);
    --glass-border: rgba(255, 255, 255, 0.15);
    --glass-highlight: rgba(255, 255, 255, 0.25);
    --text-primary: #ffffff;
    --text-secondary: rgba(255, 255, 255, 0.7);
    --text-muted: rgba(255, 255, 255, 0.5);
    --accent-blue: #007AFF;
    --accent-purple: #AF52DE;
    --accent-green: #34C759;
    --accent-orange: #FF9500;
    --accent-red: #FF3B30;
    --accent-teal: #5AC8FA;
    --accent-indigo: #5856D6;
    --accent-pink: #FF2D55;
    --accent-yellow: #FFCC00;
    --accent-cyan: #00C7BE;
    --space-xs: 0.25rem;
    --space-sm: 0.5rem;
    --space-md: 1rem;
    --space-lg: 1.5rem;
    --space-xl: 2rem;
    --space-2xl: 3rem;
    --space-3xl: 4rem;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.15);
    --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.25);
    --shadow-card: 0 4px 24px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.05);
    --shadow-card-hover: 0 8px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
    --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: 250ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 400ms cubic-bezier(0.4, 0, 0.2, 1);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-full: 50%;
    --font-sans: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html {
    font-size: 16px;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body {
    font-family: var(--font-sans);
    min-height: 100vh;
    background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-mid) 50%, var(--bg-gradient-end) 100%);
    background-attachment: fixed;
    color: var(--text-primary);
    line-height: 1.5;
    overflow-x: hidden;
}

.launchpad-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    padding: var(--space-sm) var(--space-md);
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid var(--glass-border);
    box-shadow: var(--shadow-md);
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: var(--space-md);
}

.header-brand {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: -0.02em;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}

.header-brand-icon {
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-sm);
}

.header-brand-icon svg {
    width: 14px;
    height: 14px;
    fill: white;
}

.search-container {
    position: relative;
    width: 100%;
    max-width: 300px;
}

.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: var(--space-sm);
    width: 16px;
    height: 16px;
    color: var(--text-muted);
    pointer-events: none;
    transition: color var(--transition-fast);
}

.search-input {
    width: 100%;
    padding: var(--space-sm) var(--space-sm) var(--space-sm) 2rem;
    font-size: 0.875rem;
    font-family: var(--font-sans);
    color: var(--text-primary);
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    outline: none;
    transition: all var(--transition-normal);
    box-shadow: var(--shadow-sm);
}

.search-input::placeholder { color: var(--text-muted); }

.search-input:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: var(--glass-highlight);
}

.search-input:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: var(--accent-blue);
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.25), var(--shadow-md);
}

.search-input:focus + .search-icon,
.search-container:focus-within .search-icon { color: var(--accent-blue); }

.search-clear {
    position: absolute;
    right: var(--space-xs);
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-full);
    color: var(--text-secondary);
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-fast);
}

.search-clear:hover {
    background: rgba(255, 255, 255, 0.2);
    color: var(--text-primary);
}

.search-clear:focus { outline: 2px solid var(--accent-blue); outline-offset: 2px; }

.search-clear.visible { opacity: 1; visibility: visible; }

.search-clear svg { width: 12px; height: 12px; }

.search-results-info {
    display: none;
}

.launchpad-main {
    padding-top: 80px;
    padding-bottom: var(--space-3xl);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.launchpad-content {
    width: 100%;
    max-width: 1200px;
    padding: 0 var(--space-xl);
}

.launchpad-title {
    text-align: center;
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: var(--space-xl);
    letter-spacing: -0.02em;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.project-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: var(--space-xl);
    justify-items: center;
    padding: var(--space-lg) 0;
}

@media (min-width: 640px) {
    .project-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: var(--space-2xl);
    }
}

@media (min-width: 1024px) {
    .project-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }
}

.project-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    text-decoration: none;
    color: var(--text-primary);
    padding: var(--space-md);
    border-radius: var(--radius-lg);
    transition: all var(--transition-normal);
    cursor: pointer;
    position: relative;
    background: transparent;
    border: none;
    width: 100%;
    max-width: 180px;
}

.project-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: var(--glass-bg);
    border-radius: var(--radius-lg);
    border: 1px solid var(--glass-border);
    opacity: 0;
    transform: scale(0.9);
    transition: all var(--transition-normal);
    z-index: -1;
}

.project-card:hover::before { opacity: 1; transform: scale(1); }

.project-card:hover { transform: translateY(-4px); }

.project-card:hover .project-icon {
    transform: scale(1.05);
    box-shadow: var(--shadow-card-hover);
}

.project-card:active { transform: scale(0.96) translateY(0); }

.project-card:active .project-icon { transform: scale(0.95); }

.project-card:focus { outline: none; }

.project-card:focus::before {
    opacity: 1;
    transform: scale(1);
    box-shadow: 0 0 0 2px var(--accent-blue);
}

.project-card:focus-visible {
    outline: 2px solid var(--accent-blue);
    outline-offset: 4px;
}

.project-icon {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 600;
    color: white;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    box-shadow: var(--shadow-card);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}

.project-icon::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.25) 0%, transparent 50%);
    border-radius: inherit;
}

@media (min-width: 640px) {
    .project-icon { width: 96px; height: 96px; font-size: 2.25rem; }
}

@media (min-width: 1024px) {
    .project-icon { width: 104px; height: 104px; font-size: 2.5rem; }
}

.icon-blue { background: linear-gradient(135deg, #007AFF, #5856D6); }
.icon-purple { background: linear-gradient(135deg, #AF52DE, #5856D6); }
.icon-green { background: linear-gradient(135deg, #34C759, #30D158); }
.icon-orange { background: linear-gradient(135deg, #FF9500, #FF6B00); }
.icon-red { background: linear-gradient(135deg, #FF3B30, #FF6B6B); }
.icon-teal { background: linear-gradient(135deg, #5AC8FA, #64D2FF); }
.icon-indigo { background: linear-gradient(135deg, #5856D6, #6366F1); }
.icon-pink { background: linear-gradient(135deg, #FF2D55, #FF6B9D); }
.icon-yellow { background: linear-gradient(135deg, #FFCC00, #FFD700); }
.icon-cyan { background: linear-gradient(135deg, #00C7BE, #32ADE6); }

.project-info {
    margin-top: var(--space-md);
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
    width: 100%;
}

.project-name {
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.3;
    color: var(--text-primary);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    word-wrap: break-word;
    max-width: 100%;
}

@media (min-width: 640px) { .project-name { font-size: 1rem; } }

.project-subtitle {
    font-size: 0.75rem;
    color: var(--text-muted);
    line-height: 1.3;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.empty-state {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--space-3xl) var(--space-xl);
    text-align: center;
    animation: fadeIn var(--transition-slow) ease-out;
}

.empty-state.visible { display: flex; }

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin-bottom: var(--space-lg);
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-sm);
}

.empty-state-icon svg { width: 40px; height: 40px; color: var(--text-muted); }

.empty-state-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--space-sm);
}

.empty-state-message {
    font-size: 0.9375rem;
    color: var(--text-secondary);
    max-width: 400px;
    line-height: 1.5;
}

.empty-state-clear {
    margin-top: var(--space-lg);
    padding: var(--space-sm) var(--space-lg);
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--accent-blue);
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-fast);
    backdrop-filter: blur(10px);
}

.empty-state-clear:hover {
    background: rgba(0, 122, 255, 0.15);
    border-color: var(--accent-blue);
    transform: translateY(-1px);
}

.empty-state-clear:focus { outline: 2px solid var(--accent-blue); outline-offset: 2px; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}

.project-card { animation: scaleIn var(--transition-slow) ease-out backwards; }

.project-card:nth-child(1) { animation-delay: 0ms; }
.project-card:nth-child(2) { animation-delay: 30ms; }
.project-card:nth-child(3) { animation-delay: 60ms; }
.project-card:nth-child(4) { animation-delay: 90ms; }
.project-card:nth-child(5) { animation-delay: 120ms; }
.project-card:nth-child(6) { animation-delay: 150ms; }
.project-card:nth-child(7) { animation-delay: 180ms; }
.project-card:nth-child(8) { animation-delay: 210ms; }
.project-card:nth-child(9) { animation-delay: 240ms; }
.project-card:nth-child(10) { animation-delay: 270ms; }
.project-card:nth-child(11) { animation-delay: 300ms; }
.project-card:nth-child(12) { animation-delay: 330ms; }

.project-card.hidden { display: none; }

.project-card.matching { animation: scaleIn var(--transition-normal) ease-out; }

::-webkit-scrollbar { width: 8px; height: 8px; }

::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }

::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }

::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

@media (prefers-contrast: high) {
    :root {
        --glass-bg: rgba(255, 255, 255, 0.2);
        --glass-border: rgba(255, 255, 255, 0.4);
        --text-muted: rgba(255, 255, 255, 0.8);
    }
}

:focus:not(:focus-visible) { outline: none; }

:focus-visible { outline: 2px solid var(--accent-blue); outline-offset: 2px; }
';
}

function vercel_get_launchpad_scripts(): string
{
    return '
const searchInput = document.getElementById("searchInput");
const searchClear = document.getElementById("searchClear");
const searchResultsInfo = document.getElementById("searchResultsInfo");
const projectGrid = document.getElementById("projectGrid");
const emptyState = document.getElementById("emptyState");
const emptyStateClear = document.getElementById("emptyStateClear");

function filterProjects(query) {
    const normalizedQuery = query.toLowerCase().trim();
    const cards = projectGrid.querySelectorAll(".project-card");
    let visibleCount = 0;

    cards.forEach(card => {
        const projectName = card.getAttribute("data-project-name");
        const isMatch = projectName.includes(normalizedQuery);

        if (isMatch) {
            card.classList.remove("hidden");
            card.classList.add("matching");
            visibleCount++;
        } else {
            card.classList.add("hidden");
            card.classList.remove("matching");
        }
    });

    if (visibleCount === 0 && normalizedQuery !== "") {
        emptyState.classList.add("visible");
        projectGrid.style.display = "none";
    } else {
        emptyState.classList.remove("visible");
        projectGrid.style.display = "grid";
    }

    if (normalizedQuery !== "") {
        searchResultsInfo.textContent = visibleCount + " " + (visibleCount === 1 ? "задание найдено" : "заданий найдено");
        searchResultsInfo.classList.add("visible");
    } else {
        searchResultsInfo.classList.remove("visible");
    }

    return visibleCount;
}

function clearSearch() {
    searchInput.value = "";
    searchClear.classList.remove("visible");
    filterProjects("");
    searchInput.focus();
}

searchInput.addEventListener("input", (e) => {
    const query = e.target.value;
    if (query.length > 0) {
        searchClear.classList.add("visible");
    } else {
        searchClear.classList.remove("visible");
    }
    filterProjects(query);
});

searchClear.addEventListener("click", clearSearch);
emptyStateClear.addEventListener("click", clearSearch);

searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        clearSearch();
    }
});

document.addEventListener("keydown", (e) => {
    if (e.key === "/" && document.activeElement !== searchInput) {
        e.preventDefault();
        searchInput.focus();
    }
});
';
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

function vercel_get_task_manifest(): array
{
    $tasks = [];
    $taskId = 1;
    $colors = ['blue', 'purple', 'green', 'orange', 'red', 'teal', 'indigo', 'pink', 'yellow', 'cyan'];
    $colorIndex = 0;

    $assignments = [
        '01-php-basics' => [
            ['title' => 'Output Profile', 'desc' => 'Name, age, study place, hobbies'],
            ['title' => 'String Length', 'desc' => 'UTF-8 safe string length'],
            ['title' => 'Last Character', 'desc' => 'Get last char of string'],
            ['title' => 'Circle Area', 'desc' => 'Compute from radius'],
            ['title' => 'Rectangle Area', 'desc' => 'Basic geometry'],
            ['title' => 'Perimeter', 'desc' => 'Rectangle perimeter'],
        ],
        '02-control-structures' => [
            ['title' => 'Season Detector', 'desc' => 'From month 1-12'],
            ['title' => 'First Char Check', 'desc' => 'Check if equals "a"'],
            ['title' => 'Lucky Number', 'desc' => 'Six-digit lucky check'],
            ['title' => 'Salary Raise 10%', 'desc' => 'Raise all salaries'],
            ['title' => 'Conditional Raise', 'desc' => 'Only <= 400'],
            ['title' => 'Print 1-100', 'desc' => 'Loop output'],
            ['title' => 'Filter Range', 'desc' => 'Elements > 0 && < 10'],
            ['title' => 'Array Stats', 'desc' => 'Sum and mean'],
        ],
        '03-arrays' => [
            ['title' => 'Arithmetic', 'desc' => 'Using [2,5,3,9]'],
            ['title' => 'User Data', 'desc' => 'Output associative data'],
            ['title' => 'Fill Array', 'desc' => 'With 1-5'],
            ['title' => 'Min Max', 'desc' => 'Find extremes'],
            ['title' => 'Detect Value', 'desc' => 'Find value 3'],
            ['title' => 'Sum Elements', 'desc' => 'Total all values'],
            ['title' => 'Ranges', 'desc' => '1-100 and a-z'],
            ['title' => 'Sum 1-100', 'desc' => 'Without loop'],
            ['title' => 'Shuffle', 'desc' => 'range(1,25)'],
            ['title' => 'Random Alphabet', 'desc' => 'Non-repeating'],
            ['title' => 'Sorting Demo', 'desc' => 'sort, asort, ksort variants'],
        ],
        '04-associative-arrays' => [
            ['title' => 'Create Array', 'desc' => '[1 => a, 2 => b, 3 => c]'],
            ['title' => 'Months Array', 'desc' => 'January at key 1'],
            ['title' => 'Full Name', 'desc' => 'Name surname patronymic'],
            ['title' => 'Current Date', 'desc' => 'year-month-day format'],
            ['title' => 'Key Behavior', 'desc' => 'Holes and order'],
            ['title' => 'Array Count', 'desc' => 'Indexed and assoc'],
            ['title' => 'Last Elements', 'desc' => 'Last and penultimate'],
        ],
        '05-multidimensional-arrays' => [
            ['title' => 'Sum 2D Array', 'desc' => 'All elements'],
            ['title' => 'Sum Salaries', 'desc' => 'First and third user'],
            ['title' => 'Books Dataset', 'desc' => 'Output structure'],
            ['title' => 'Disciplines Table', 'desc' => 'HTML table render'],
            ['title' => 'Group Users', 'desc' => 'group name - user name'],
        ],
        '06-user-functions' => [
            ['title' => 'Print Name', 'desc' => 'Your own name'],
            ['title' => 'Sign Checker', 'desc' => '+++ for positive, --- for negative'],
            ['title' => 'Sum Three', 'desc' => 'Add three numbers'],
            ['title' => 'Cube Value', 'desc' => 'Return into $res'],
            ['title' => 'Even Check', 'desc' => 'All elements even'],
            ['title' => 'Adjacent Dups', 'desc' => 'Detect duplicates'],
            ['title' => 'Digit Sum', 'desc' => 'Sum of digits'],
            ['title' => 'Prime Check', 'desc' => 'Is number prime'],
        ],
        '07-standard-functions' => [
            ['title' => 'Array Average', 'desc' => 'Compute mean'],
            ['title' => 'Sum 1-100', 'desc' => 'Sum range'],
            ['title' => 'Print Range', 'desc' => 'Output 1-100'],
            ['title' => 'Uppercase Last', 'desc' => 'Last char to upper'],
            ['title' => 'Square Roots', 'desc' => 'For numeric array'],
            ['title' => 'Min Max', 'desc' => 'Find extremes'],
            ['title' => 'Random Number', 'desc' => 'Generate random'],
            ['title' => 'HTTP Detect', 'desc' => 'Check http prefix'],
            ['title' => 'DateTime', 'desc' => 'Year month day hour minute second'],
            ['title' => 'New Year Count', 'desc' => 'Days until New Year'],
        ],
        '08-string-generation' => [
            ['title' => 'Paragraphs', 'desc' => 'Three variables as p'],
            ['title' => 'Image Tags', 'desc' => 'From source vars'],
            ['title' => 'List 1-5', 'desc' => 'Ordered list'],
            ['title' => 'Select Dropdown', 'desc' => 'From array'],
            ['title' => 'Current Date', 'desc' => 'year-month-day'],
            ['title' => 'Conditional Div', 'desc' => 'When show=true'],
            ['title' => 'Mixed Syntax', 'desc' => 'PHP + HTML list'],
            ['title' => 'User Cards', 'desc' => 'From users array'],
        ],
        '09-forms' => [
            ['title' => 'Name Age Salary', 'desc' => 'GET and POST forms'],
            ['title' => 'Sum Three', 'desc' => 'Three number form'],
            ['title' => 'Name Age Display', 'desc' => 'Show form data'],
            ['title' => 'Password Match', 'desc' => 'Compare passwords'],
            ['title' => 'Full Name', 'desc' => 'SNP display'],
            ['title' => 'Checkbox Greeting', 'desc' => 'Hello and goodbye'],
            ['title' => 'Gender Radio', 'desc' => 'Radio buttons'],
            ['title' => 'Temp Converter', 'desc' => 'Celsius Fahrenheit'],
            ['title' => 'Birthday Form', 'desc' => 'dd.mm.yyyy format'],
            ['title' => 'Textarea Stats', 'desc' => 'Word and char count'],
        ],
        '10-http-basics' => [
            ['title' => 'Method Check', 'desc' => 'GET vs POST'],
            ['title' => 'Accept Headers', 'desc' => 'Read Accept and Language'],
            ['title' => 'All Headers', 'desc' => 'List request headers'],
            ['title' => '404 Response', 'desc' => 'Send not found'],
            ['title' => 'Status Codes', 'desc' => '200, 302, 400, 404'],
        ],
        '11-sessions' => [
            ['title' => 'Country Form', 'desc' => 'index to test.php'],
            ['title' => 'Session Timer', 'desc' => 'Seconds since entry'],
            ['title' => 'Email Carry', 'desc' => 'Form to form'],
            ['title' => 'Refresh Counter', 'desc' => 'With first-visit msg'],
            ['title' => 'Profile Prefill', 'desc' => 'Name Age City flow'],
            ['title' => 'Logout', 'desc' => 'Destroy session'],
            ['title' => 'Multi-page Quiz', 'desc' => 'Store answers'],
        ],
        '12-regex-validation' => [
            ['title' => 'Email Validate', 'desc' => 'Regex email check'],
            ['title' => 'Login Validate', 'desc' => '3-20 chars, Latin + underscore'],
            ['title' => 'Password Validate', 'desc' => 'Min 8, letter + digit'],
            ['title' => 'Phone Validate', 'desc' => 'Optional +, 10-15 digits'],
        ],
        '13-auth-db-app' => [
            ['title' => 'Registration', 'desc' => 'Sign up form'],
            ['title' => 'Login', 'desc' => 'Authenticate user'],
            ['title' => 'Save User', 'desc' => 'Store to database'],
            ['title' => 'Email Unique', 'desc' => 'Enforce uniqueness'],
            ['title' => 'User Profile', 'desc' => 'Show after login'],
            ['title' => 'Logout', 'desc' => 'End session'],
            ['title' => 'Status Messages', 'desc' => 'Success and errors'],
        ],
    ];

    foreach ($assignments as $assignmentSlug => $assignmentTasks) {
        foreach ($assignmentTasks as $index => $task) {
            $tasks[] = [
                'id' => $taskId++,
                'assignment' => $assignmentSlug,
                'assignmentTitle' => str_replace(['01-', '02-', '03-', '04-', '05-', '06-', '07-', '08-', '09-', '10-', '11-', '12-', '13-', '-'], ['', '', '', '', '', '', '', '', '', '', '', '', '', ' '], $assignmentSlug),
                'title' => $task['title'],
                'description' => $task['desc'],
                'color' => $colors[$colorIndex % count($colors)],
                'url' => '/' . $assignmentSlug,
            ];
            $colorIndex++;
        }
    }

    return $tasks;
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
