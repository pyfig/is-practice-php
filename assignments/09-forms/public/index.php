<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/helpers.php';

$activeTask = get_post_string('task');
$responseTitle = '';
$responseMessage = '';
$appBasePath = app_base_path();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($activeTask) {
        case 'sum3':
            $numbers = [
                parse_number(get_post_string('number_1')),
                parse_number(get_post_string('number_2')),
                parse_number(get_post_string('number_3')),
            ];

            if (in_array(null, $numbers, true)) {
                $responseTitle = 'Ошибка';
                $responseMessage = 'Все три поля должны содержать числа.';
                break;
            }

            $responseTitle = 'Сумма трёх чисел';
            $responseMessage = 'Результат: ' . number_format(array_sum($numbers), 2, '.', ' ');
            break;

        case 'name_age':
            $name = get_post_string('display_name');
            $age = parse_positive_int(get_post_string('display_age'));

            if ($name === '' || $age === null) {
                $responseTitle = 'Ошибка';
                $responseMessage = 'Укажите имя и корректный возраст.';
                break;
            }

            $responseTitle = 'Имя и возраст';
            $responseMessage = sprintf('Пользователь %s, возраст %d лет.', $name, $age);
            break;

        case 'password_compare':
            $password = get_post_string('password');
            $passwordConfirm = get_post_string('password_confirm');

            if ($password === '' || $passwordConfirm === '') {
                $responseTitle = 'Ошибка';
                $responseMessage = 'Оба поля пароля должны быть заполнены.';
                break;
            }

            $responseTitle = 'Сравнение паролей';
            $responseMessage = $password === $passwordConfirm
                ? 'Пароли совпадают.'
                : 'Пароли не совпадают.';
            break;

        case 'full_name':
            $surname = get_post_string('surname');
            $name = get_post_string('first_name');
            $patronymic = get_post_string('patronymic');

            if ($surname === '' || $name === '' || $patronymic === '') {
                $responseTitle = 'Ошибка';
                $responseMessage = 'Заполните фамилию, имя и отчество.';
                break;
            }

            $responseTitle = 'Полное имя';
            $responseMessage = trim($surname . ' ' . $name . ' ' . $patronymic);
            break;

        case 'checkboxes':
            $greeting = isset($_POST['greeting']);
            $goodbye = isset($_POST['goodbye']);
            $messages = [];
            if ($greeting) {
                $messages[] = 'Привет!';
            }
            if ($goodbye) {
                $messages[] = 'До свидания!';
            }

            $responseTitle = 'Флажки';
            $responseMessage = $messages === []
                ? 'Не выбран ни один вариант.'
                : implode(' ', $messages);
            break;

        case 'gender':
            $gender = get_post_string('gender');
            $labels = [
                'male' => 'Выбран мужской пол.',
                'female' => 'Выбран женский пол.',
            ];

            $responseTitle = 'Радиокнопки';
            $responseMessage = $labels[$gender] ?? 'Выберите один из вариантов пола.';
            break;

        case 'temperature':
            $temperature = parse_number(get_post_string('temperature_value'));
            $direction = get_post_string('direction');
            if ($temperature === null) {
                $responseTitle = 'Ошибка';
                $responseMessage = 'Температура должна быть числом.';
                break;
            }

            if ($direction === 'c_to_f') {
                $converted = ($temperature * 9 / 5) + 32;
                $responseTitle = 'Конвертер температуры';
                $responseMessage = sprintf('%.2f °C = %.2f °F', $temperature, $converted);
                break;
            }

            if ($direction === 'f_to_c') {
                $converted = ($temperature - 32) * 5 / 9;
                $responseTitle = 'Конвертер температуры';
                $responseMessage = sprintf('%.2f °F = %.2f °C', $temperature, $converted);
                break;
            }

            $responseTitle = 'Ошибка';
            $responseMessage = 'Выберите направление конвертации.';
            break;

        case 'birthday':
            $birthdayRaw = get_post_string('birthday');
            $birthday = parse_birthday($birthdayRaw);
            if ($birthday === null) {
                $responseTitle = 'Ошибка';
                $responseMessage = 'Дата рождения должна быть в формате dd.mm.yyyy.';
                break;
            }

            $days = days_until_next_birthday($birthday);
            $responseTitle = 'Следующий день рождения';
            $responseMessage = sprintf('До ближайшего дня рождения осталось %d дн.', $days);
            break;

        case 'textarea_stats':
            $text = get_post_string('long_text');
            $responseTitle = 'Статистика текста';
            $responseMessage = sprintf(
                'Слов: %d. Символов: %d.',
                count_words_utf8($text),
                mb_strlen($text, 'UTF-8')
            );
            break;
    }
}

http_response_code(200);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>09 Forms</title>
    <link rel="stylesheet" href="/assets/launchpad.css">
    <style>
        main { max-width: 960px; margin: 0 auto; padding: 24px; }
        .assignment-shell { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: var(--shadow-sm); }
        section { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        form { display: grid; gap: 10px; }
        label { display: grid; gap: 4px; }
        fieldset { border: 1px solid #cbd5e1; padding: 12px; }
        input, textarea, select, button { font: inherit; padding: 8px; border-radius: var(--radius-sm); }
        input, textarea, select { border: 1px solid #cbd5e1; }
        button { width: fit-content; cursor: pointer; border: 1px solid var(--color-border-strong); background: #0f172a; color: #ffffff; }
        .inline-options { display: flex; gap: 16px; flex-wrap: wrap; }
        .assignment-description { color: var(--color-text-muted); }
        .assignment-meta { color: var(--color-text-muted); font-size: 14px; }
    </style>
</head>
<body data-app-base-path="<?= escape_html($appBasePath) ?>">

<main>
    <article class="assignment-shell">
    <h1>Практика по формам в PHP</h1>
    <p class="assignment-description">На странице собраны отдельные упражнения по GET, POST и обработке форм без сессий и базы данных.</p>
    <p class="assignment-meta">Базовый путь страницы: <code><?= escape_html($appBasePath) ?></code>.</p>

    <?php if ($responseTitle !== '' && $responseMessage !== ''): ?>
        <?= render_alert($responseTitle, $responseMessage) ?>
    <?php endif; ?>

    <section>
        <h2>1. Форма name / age / salary через GET</h2>
        <form action="<?= escape_html(app_url('result.php')) ?>" method="get">
            <label>Имя <input type="text" name="name"></label>
            <label>Возраст <input type="text" name="age"></label>
            <label>Зарплата <input type="text" name="salary"></label>
            <button type="submit">Отправить GET</button>
        </form>
    </section>

    <section>
        <h2>2. Форма name / age / salary через POST</h2>
        <form action="<?= escape_html(app_url('result.php')) ?>" method="post">
            <label>Имя <input type="text" name="name"></label>
            <label>Возраст <input type="text" name="age"></label>
            <label>Зарплата <input type="text" name="salary"></label>
            <button type="submit">Отправить POST</button>
        </form>
    </section>

    <section>
        <h2>3. Сумма трёх чисел</h2>
        <form method="post">
            <input type="hidden" name="task" value="sum3">
            <label>Первое число <input type="text" name="number_1"></label>
            <label>Второе число <input type="text" name="number_2"></label>
            <label>Третье число <input type="text" name="number_3"></label>
            <button type="submit">Посчитать сумму</button>
        </form>
    </section>

    <section>
        <h2>4. Имя и возраст</h2>
        <form method="post">
            <input type="hidden" name="task" value="name_age">
            <label>Имя <input type="text" name="display_name"></label>
            <label>Возраст <input type="text" name="display_age"></label>
            <button type="submit">Показать данные</button>
        </form>
    </section>

    <section>
        <h2>5. Сравнение паролей</h2>
        <form method="post">
            <input type="hidden" name="task" value="password_compare">
            <label>Пароль <input type="password" name="password"></label>
            <label>Повтор пароля <input type="password" name="password_confirm"></label>
            <button type="submit">Сравнить</button>
        </form>
    </section>

    <section>
        <h2>6. Фамилия, имя, отчество</h2>
        <form method="post">
            <input type="hidden" name="task" value="full_name">
            <label>Фамилия <input type="text" name="surname"></label>
            <label>Имя <input type="text" name="first_name"></label>
            <label>Отчество <input type="text" name="patronymic"></label>
            <button type="submit">Показать ФИО</button>
        </form>
    </section>

    <section>
        <h2>7. Флажки «привет» и «до свидания»</h2>
        <form method="post">
            <input type="hidden" name="task" value="checkboxes">
            <div class="inline-options">
                <label><input type="checkbox" name="greeting" value="1"> Приветствие</label>
                <label><input type="checkbox" name="goodbye" value="1"> Прощание</label>
            </div>
            <button type="submit">Проверить</button>
        </form>
    </section>

    <section>
        <h2>8. Выбор пола</h2>
        <form method="post">
            <input type="hidden" name="task" value="gender">
            <div class="inline-options">
                <label><input type="radio" name="gender" value="male"> Мужской</label>
                <label><input type="radio" name="gender" value="female"> Женский</label>
            </div>
            <button type="submit">Отправить</button>
        </form>
    </section>

    <section>
        <h2>9. Конвертер Цельсий / Фаренгейт</h2>
        <form method="post">
            <input type="hidden" name="task" value="temperature">
            <label>Температура <input type="text" name="temperature_value"></label>
            <label>Направление
                <select name="direction">
                    <option value="">Выберите вариант</option>
                    <option value="c_to_f">Цельсий → Фаренгейт</option>
                    <option value="f_to_c">Фаренгейт → Цельсий</option>
                </select>
            </label>
            <button type="submit">Конвертировать</button>
        </form>
    </section>

    <section>
        <h2>10. День рождения</h2>
        <form method="post">
            <input type="hidden" name="task" value="birthday">
            <label>Дата рождения (dd.mm.yyyy) <input type="text" name="birthday" placeholder="31.12.2000"></label>
            <button type="submit">Посчитать дни</button>
        </form>
    </section>

    <section>
        <h2>11. Подсчёт слов и символов</h2>
        <form method="post">
            <input type="hidden" name="task" value="textarea_stats">
            <label>Текст
                <textarea name="long_text" rows="6" cols="40"></textarea>
            </label>
            <button type="submit">Показать статистику</button>
        </form>
    </section>
    </article>
</main>
</body>
</html>
