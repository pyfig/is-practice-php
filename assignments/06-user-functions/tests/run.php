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
$outputLines = explode(PHP_EOL, trim($output));

assert_same(
    [
        'Имя: Илья Шеклаков',
        '+++',
        '---',
        'Сумма трёх чисел: 6',
        'Куб числа 3: 27',
        'Все элементы чётные: да',
        'Есть соседние дубликаты: да',
        'Сумма цифр числа 12345: 15',
        '13 — простое: да',
        '12 — простое: нет',
    ],
    $outputLines,
    'CLI output mismatch.'
);

assert_same('Илья Шеклаков', print_own_name(), 'Name output mismatch.');
assert_same('+++', positive_negative_marker(0), 'Positive marker must be exact.');
assert_same('+++', positive_negative_marker(7), 'Positive marker must stay exact for positive values.');
assert_same('---', positive_negative_marker(-1), 'Negative marker must be exact.');
assert_same(6, sum_three_numbers(1, 2, 3), 'Sum of three numbers failed.');

$res = cube_value(4);
assert_same(64, $res, 'Cube helper failed.');

assert_same(true, all_numbers_even([2, 4, 6, 8]), 'Even-array check should pass for all-even values.');
assert_same(false, all_numbers_even([2, 3, 6]), 'Even-array check should fail when odd values are present.');

assert_same(true, has_adjacent_duplicates([1, 2, 2, 3]), 'Adjacent duplicate detection should pass.');
assert_same(false, has_adjacent_duplicates([1, 2, 3, 2]), 'Adjacent duplicate detection should fail for non-adjacent matches.');
assert_same(false, has_adjacent_duplicates([1, 2, 3, 4]), 'Adjacent duplicate detection should fail for unique arrays.');

assert_same(15, sum_digits(12345), 'Digit sum failed for positive number.');
assert_same(15, sum_digits(-2463), 'Digit sum should ignore the sign.');

assert_same(true, is_prime(13), 'Prime check should pass for prime numbers.');
assert_same(false, is_prime(12), 'Prime check should fail for composite numbers.');
assert_same(false, is_prime(1), 'Prime check should fail for numbers below 2.');

assert_true(str_contains($output, '+++'), 'CLI output must contain the positive marker.');
assert_true(str_contains($output, '---'), 'CLI output must contain the negative marker.');

fwrite(STDOUT, '[OK] 06-user-functions tests passed.' . PHP_EOL);
exit(0);
