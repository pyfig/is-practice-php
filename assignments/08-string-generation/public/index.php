<?php
declare(strict_types=1);

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function build_svg_data_uri(string $label, string $color): string
{
    $svg = sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="120" viewBox="0 0 220 120"><rect width="220" height="120" rx="16" fill="%s"/><text x="110" y="68" font-size="24" text-anchor="middle" fill="#ffffff" font-family="Arial, sans-serif">%s</text></svg>',
        $color,
        escape_html($label)
    );

    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
}

$paragraphs = [
    'PHP позволяет формировать HTML прямо из переменных.',
    'Циклы помогают повторять элементы страницы без копирования кода.',
    'Условия показывают только нужные блоки интерфейса.',
];

$imageSources = [
    [
        'src' => build_svg_data_uri('PHP', '#2563eb'),
        'alt' => 'Синяя карточка PHP',
    ],
    [
        'src' => build_svg_data_uri('HTML', '#ea580c'),
        'alt' => 'Оранжевая карточка HTML',
    ],
    [
        'src' => build_svg_data_uri('Loop', '#16a34a'),
        'alt' => 'Зелёная карточка Loop',
    ],
];

$selectOptions = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май'];

$mixedListItems = ['Первый элемент', 'Второй элемент', 'Третий элемент'];

$users = [
    [
        'name' => 'Анна Смирнова',
        'role' => 'Контент-менеджер',
        'email' => 'anna@example.test',
    ],
    [
        'name' => 'Илья Петров',
        'role' => 'PHP-разработчик',
        'email' => 'ilya@example.test',
    ],
    [
        'name' => 'Мария Волкова',
        'role' => 'Тестировщик',
        'email' => 'maria@example.test',
    ],
];

$showParam = isset($_GET['show']) ? mb_strtolower((string) $_GET['show'], 'UTF-8') : 'true';
$showConditionalBlock = !in_array($showParam, ['0', 'false', 'off', 'no'], true);
$currentDate = date('Y-m-d');
$appBasePath = isset($_SERVER['APP_BASE_PATH']) && is_string($_SERVER['APP_BASE_PATH'])
    ? $_SERVER['APP_BASE_PATH']
    : '/08-string-generation';
$appRequestPath = isset($_SERVER['APP_REQUEST_PATH']) && is_string($_SERVER['APP_REQUEST_PATH'])
    ? $_SERVER['APP_REQUEST_PATH']
    : '/';

http_response_code(200);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>08 String Generation</title>
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
            margin: 0 0 var(--spacing-sm);
            font-size: 1.75rem;
        }

        .assignment-description {
            margin: 0 0 var(--spacing-lg);
            color: var(--color-text-muted);
        }

        .assignment-sections {
            display: grid;
            gap: var(--spacing-lg);
        }

        .assignment-section {
            display: grid;
            gap: var(--spacing-sm);
        }

        .assignment-section h2 {
            margin: 0;
            font-size: 1.1rem;
        }

        .image-grid,
        .user-grid {
            display: grid;
            gap: var(--spacing-md);
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .user-card {
            display: grid;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: var(--spacing-md);
            background: #f8fafc;
            gap: 0.35rem;
        }

        .user-card h3,
        .user-card p {
            margin: 0;
        }

        select {
            max-width: 16rem;
            padding: 0.65rem 0.75rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            font: inherit;
            background: #fff;
        }

        code {
            font-family: var(--font-mono);
        }
    </style>
</head>
<body data-app-base-path="<?= escape_html($appBasePath) ?>" data-app-request-path="<?= escape_html($appRequestPath) ?>">
<header class="launchpad-header">
    <a class="home-logo" data-home-logo href="/">вернуться домой</a>
</header>
<main class="assignment-page">
    <article class="assignment-shell">
        <h1 class="assignment-title">Формирование HTML-строк с помощью PHP</h1>
        <p class="assignment-description">Задание работает по mounted-route контракту: базовый путь <code><?= escape_html($appBasePath) ?></code>.</p>
        <div class="assignment-sections">

    <section class="assignment-section">
        <h2>Абзацы из переменных</h2>
        <?php foreach ($paragraphs as $paragraph): ?>
            <p><?= escape_html($paragraph) ?></p>
        <?php endforeach; ?>
    </section>

    <section class="assignment-section">
        <h2>Изображения из переменных</h2>
        <div class="image-grid">
        <?php foreach ($imageSources as $image): ?>
            <img src="<?= escape_html($image['src']) ?>" alt="<?= escape_html($image['alt']) ?>" width="220" height="120">
        <?php endforeach; ?>
        </div>
    </section>

    <section class="assignment-section">
        <h2>Список чисел от 1 до 5</h2>
        <ul>
            <?php for ($number = 1; $number <= 5; $number++): ?>
                <li><?= $number ?></li>
            <?php endfor; ?>
        </ul>
    </section>

    <section class="assignment-section">
        <h2>Выпадающий список</h2>
        <select name="month">
            <?php foreach ($selectOptions as $option): ?>
                <option value="<?= escape_html($option) ?>"><?= escape_html($option) ?></option>
            <?php endforeach; ?>
        </select>
    </section>

    <section class="assignment-section">
        <h2>Текущая дата</h2>
        <p><?= escape_html($currentDate) ?></p>
    </section>

    <section class="assignment-section">
        <h2>Условный блок</h2>
        <p>По умолчанию блок виден. Для проверки отсутствия используйте адрес с параметром <code>?show=0</code>.</p>
        <?php if ($showConditionalBlock): ?>
            <div id="conditional-block">Этот блок отображается только когда show=true.</div>
        <?php endif; ?>
    </section>

    <section class="assignment-section">
        <h2>Смешанный PHP и HTML</h2>
        <ul>
            <?php foreach ($mixedListItems as $item): ?>
                <li><?= escape_html($item) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="assignment-section">
        <h2>Карточки пользователей</h2>
        <div class="user-grid">
        <?php foreach ($users as $user): ?>
            <article class="user-card">
                <h3><?= escape_html($user['name']) ?></h3>
                <p>Должность: <?= escape_html($user['role']) ?></p>
                <p>Email: <?= escape_html($user['email']) ?></p>
            </article>
        <?php endforeach; ?>
        </div>
    </section>
        </div>
    </article>
</main>
</body>
</html>
