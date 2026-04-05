<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/helpers.php';

$requestData = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
$name = get_request_string($requestData, 'name');
$ageRaw = get_request_string($requestData, 'age');
$salaryRaw = get_request_string($requestData, 'salary');

$errors = [];
if ($name === '') {
    $errors[] = 'Поле «Имя» обязательно для заполнения.';
}

$age = parse_positive_int($ageRaw);
if ($age === null) {
    $errors[] = 'Возраст должен быть целым неотрицательным числом.';
}

$salary = parse_number($salaryRaw);
if ($salary === null) {
    $errors[] = 'Зарплата должна быть числом.';
}

http_response_code(200);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>09 Forms — result.php</title>
</head>
<body>
<main>
    <h1>Результат формы name / age / salary</h1>
    <p>Метод запроса: <strong><?= escape_html($_SERVER['REQUEST_METHOD'] ?? 'GET') ?></strong></p>
    <p><a href="/">Вернуться на главную страницу задания</a></p>

    <?php if ($errors !== []): ?>
        <section>
            <h2>Ошибки</h2>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= escape_html($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php else: ?>
        <section>
            <h2>Принятые данные</h2>
            <p>Имя: <?= escape_html($name) ?></p>
            <p>Возраст: <?= $age ?></p>
            <p>Зарплата: <?= escape_html(number_format($salary, 2, '.', ' ')) ?></p>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
