<?php

declare(strict_types=1);

require __DIR__ . '/../index.php';

function fail(string $message): void
{
    fwrite(STDERR, '[FAIL] ' . $message . PHP_EOL);
    exit(1);
}

function assert_same(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fail($message . ' Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

function assert_true(bool $condition, string $message): void
{
    if ($condition !== true) {
        fail($message);
    }
}

$output = build_assignment_output();

assert_true(str_contains($output, 'Сумма элементов 2D массива: 45'), '2D sum output mismatch.');
assert_true(str_contains($output, 'Сумма зарплат первого и третьего пользователя: 3000'), 'Salary extraction output mismatch.');
assert_true(str_contains($output, 'Книги:'), 'Books section header is missing.');
assert_true(str_contains($output, '- Мастер и Маргарита — Михаил Булгаков (1967)'), 'Books dataset line mismatch.');
assert_true(str_contains($output, '<table'), 'Disciplines table must be rendered as HTML table.');
assert_true(str_contains($output, '<th>Дисциплина</th>'), 'Disciplines table header mismatch.');
assert_true(str_contains($output, 'Группа A - Анна'), 'Nested group/user output mismatch for first user.');
assert_true(str_contains($output, 'Группа B - Галина'), 'Nested group/user output mismatch for last user.');

assert_same(45, sum_2d_array(two_dimensional_numbers()), '2D array summation failed.');
assert_same(0, sum_2d_array([]), 'Empty 2D array must sum to zero.');

$customMatrix = [
    [10, 20],
    [30],
    [40, 50, 60],
];
assert_same(210, sum_2d_array($customMatrix), '2D summation must support uneven nested rows.');

assert_same(3000, sum_first_and_third_salary(users_with_salaries()), 'First and third salary sum mismatch.');

$books = books_dataset();
assert_same(3, count($books), 'Books dataset size mismatch.');
assert_same('Война и мир', $books[1]['title'], 'Books dataset order mismatch.');

$disciplinesTable = render_disciplines_table(disciplines_dataset());
assert_true(str_starts_with($disciplinesTable, '<table'), 'Disciplines table must start with <table>.');
assert_true(str_contains($disciplinesTable, '<td>Информатика</td>'), 'Disciplines table body mismatch.');

$pairs = group_user_pairs(groups_with_users());
assert_same(
    [
        'Группа A - Анна',
        'Группа A - Борис',
        'Группа B - Виктор',
        'Группа B - Галина',
    ],
    $pairs,
    'Group/user nested-loop output mismatch.'
);

assert_same([], group_user_pairs([]), 'Empty nested group list must return empty result safely.');
assert_same(
    [],
    group_user_pairs([
        ['name' => 'Группа C', 'users' => []],
    ]),
    'Group with empty users list must be handled safely.'
);

fwrite(STDOUT, '[OK] 05-multidimensional-arrays tests passed.' . PHP_EOL);
exit(0);
