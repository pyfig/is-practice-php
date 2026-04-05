<?php

declare(strict_types=1);

function print_own_name(): string
{
    return 'Илья Шеклаков';
}

function positive_negative_marker(int $number): string
{
    return $number >= 0 ? '+++' : '---';
}

function sum_three_numbers(int $first, int $second, int $third): int
{
    return $first + $second + $third;
}

function cube_value(int $number): int
{
    return $number ** 3;
}

function all_numbers_even(array $numbers): bool
{
    foreach ($numbers as $number) {
        if ((int) $number % 2 !== 0) {
            return false;
        }
    }

    return true;
}

function has_adjacent_duplicates(array $values): bool
{
    $count = count($values);

    for ($index = 1; $index < $count; $index++) {
        if ($values[$index] === $values[$index - 1]) {
            return true;
        }
    }

    return false;
}

function sum_digits(int $number): int
{
    $sum = 0;
    $digits = str_split((string) abs($number));

    foreach ($digits as $digit) {
        $sum += (int) $digit;
    }

    return $sum;
}

function is_prime(int $number): bool
{
    if ($number < 2) {
        return false;
    }

    if ($number === 2) {
        return true;
    }

    if ($number % 2 === 0) {
        return false;
    }

    $limit = (int) floor(sqrt($number));

    for ($divisor = 3; $divisor <= $limit; $divisor += 2) {
        if ($number % $divisor === 0) {
            return false;
        }
    }

    return true;
}

function build_assignment_output(): string
{
    $res = cube_value(3);

    $lines = [
        'Имя: ' . print_own_name(),
        positive_negative_marker(5),
        positive_negative_marker(-5),
        'Сумма трёх чисел: ' . sum_three_numbers(1, 2, 3),
        'Куб числа 3: ' . $res,
        'Все элементы чётные: ' . (all_numbers_even([2, 4, 6, 8]) ? 'да' : 'нет'),
        'Есть соседние дубликаты: ' . (has_adjacent_duplicates([1, 2, 2, 3]) ? 'да' : 'нет'),
        'Сумма цифр числа 12345: ' . sum_digits(12345),
        '13 — простое: ' . (is_prime(13) ? 'да' : 'нет'),
        '12 — простое: ' . (is_prime(12) ? 'да' : 'нет'),
    ];

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    fwrite(STDOUT, build_assignment_output());
}
