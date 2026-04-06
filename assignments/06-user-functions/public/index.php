<?php
declare(strict_types=1);

require dirname(__DIR__) . '/index.php';

ob_start();
echo build_assignment_output();
$assignmentOutput = ob_get_clean() ?: '';

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

http_response_code(200);
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>06 User Functions</title>
    <link rel="stylesheet" href="/assets/launchpad.css">
    <style>
        .assignment-page {
            max-width: 72rem;
            margin: 0 auto;
            padding: var(--spacing-xl);
        }

        .assignment-shell {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-sm);
        }

        .assignment-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }

        .assignment-description {
            color: var(--color-text-muted);
            margin-bottom: var(--spacing-lg);
        }

        .assignment-output {
            white-space: pre-wrap;
            font-family: var(--font-mono);
            font-size: 0.95rem;
            line-height: 1.7;
            background: #f8fafc;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            overflow-x: auto;
        }
    </style>
</head>
<body>
<header class="launchpad-header">
    <a class="home-logo" data-home-logo href="/">
        <img class="home-logo-icon" src="/assets/logo.svg" alt="">
        <span>Launchpad</span>
    </a>
</header>
<main class="assignment-page">
    <article class="assignment-shell">
        <h1 class="assignment-title">06 User Functions</h1>
        <p class="assignment-description">CLI output rendered inside the shared launchpad shell.</p>
        <pre class="assignment-output"><?= escape_html($assignmentOutput) ?></pre>
    </article>
</main>
</body>
</html>
