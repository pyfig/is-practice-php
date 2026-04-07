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
    <title>05 Multidimensional Arrays</title>
    <link rel="stylesheet" href="/assets/launchpad.css">
    <link rel="stylesheet" href="/assets/assignment-common.css">
</head>
<body>

<main class="assignment-page">
    <article class="assignment-shell">
        <h1 class="assignment-title">05 Multidimensional Arrays</h1>
        <p class="assignment-description">CLI output rendered inside the shared launchpad shell.</p>
        <pre class="assignment-output"><?= escape_html($assignmentOutput) ?></pre>
    </article>
</main>
</body>
</html>
