<?php

declare(strict_types=1);

function two_dimensional_numbers(): array
{
    return [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
    ];
}

function sum_2d_array(array $matrix): int
{
    $sum = 0;

    foreach ($matrix as $row) {
        foreach ($row as $value) {
            $sum += (int) $value;
        }
    }

    return $sum;
}

function users_with_salaries(): array
{
    return [
        ['name' => 'Илья', 'salary' => 1200],
        ['name' => 'Ольга', 'salary' => 1500],
        ['name' => 'Михаил', 'salary' => 1800],
    ];
}

function sum_first_and_third_salary(array $users): int
{
    if (!isset($users[0]['salary'], $users[2]['salary'])) {
        throw new InvalidArgumentException('Users array must contain first and third user salary.');
    }

    return (int) $users[0]['salary'] + (int) $users[2]['salary'];
}

function books_dataset(): array
{
    return [
        ['title' => 'Мастер и Маргарита', 'author' => 'Михаил Булгаков', 'year' => 1967],
        ['title' => 'Война и мир', 'author' => 'Лев Толстой', 'year' => 1869],
        ['title' => 'Преступление и наказание', 'author' => 'Фёдор Достоевский', 'year' => 1866],
    ];
}

function format_books_output(array $books): string
{
    $lines = [];

    foreach ($books as $book) {
        $lines[] = '- ' . $book['title'] . ' — ' . $book['author'] . ' (' . $book['year'] . ')';
    }

    return implode(PHP_EOL, $lines);
}

function disciplines_dataset(): array
{
    return [
        ['discipline' => 'Математика', 'hours' => 120, 'teacher' => 'Иванов И.И.'],
        ['discipline' => 'Информатика', 'hours' => 140, 'teacher' => 'Петров П.П.'],
        ['discipline' => 'Физика', 'hours' => 100, 'teacher' => 'Сидорова А.А.'],
    ];
}

function render_disciplines_table(array $disciplines): string
{
    $rows = [];

    foreach ($disciplines as $discipline) {
        $name = htmlspecialchars((string) $discipline['discipline'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hours = htmlspecialchars((string) $discipline['hours'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $teacher = htmlspecialchars((string) $discipline['teacher'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $rows[] = '<tr><td>' . $name . '</td><td>' . $hours . '</td><td>' . $teacher . '</td></tr>';
    }

    return '<table border="1"><thead><tr><th>Дисциплина</th><th>Часы</th><th>Преподаватель</th></tr></thead><tbody>'
        . implode('', $rows)
        . '</tbody></table>';
}

function groups_with_users(): array
{
    return [
        [
            'name' => 'Группа A',
            'users' => ['Анна', 'Борис'],
        ],
        [
            'name' => 'Группа B',
            'users' => ['Виктор', 'Галина'],
        ],
    ];
}

function group_user_pairs(array $groups): array
{
    $pairs = [];

    foreach ($groups as $group) {
        $groupName = (string) ($group['name'] ?? '');
        $users = $group['users'] ?? [];

        if (!is_array($users) || $users === []) {
            continue;
        }

        foreach ($users as $userName) {
            $pairs[] = $groupName . ' - ' . (string) $userName;
        }
    }

    return $pairs;
}

function build_assignment_output(): string
{
    $lines = [
        'Сумма элементов 2D массива: ' . sum_2d_array(two_dimensional_numbers()),
        'Сумма зарплат первого и третьего пользователя: ' . sum_first_and_third_salary(users_with_salaries()),
        'Книги:',
        format_books_output(books_dataset()),
        'Таблица дисциплин:',
        render_disciplines_table(disciplines_dataset()),
        'Пары группа - пользователь:',
        implode(PHP_EOL, group_user_pairs(groups_with_users())),
    ];

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    fwrite(STDOUT, build_assignment_output());
}
