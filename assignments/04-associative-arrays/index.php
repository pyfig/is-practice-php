<?php

declare(strict_types=1);

function keyed_letters_array(): array
{
    return [1 => 'a', 2 => 'b', 3 => 'c'];
}

function months_array(): array
{
    return [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];
}

function personal_data(): array
{
    return [
        'name' => 'Илья',
        'surname' => 'Шеклаков',
        'patronymic' => 'Ильич',
    ];
}

function current_date_formatted(): string
{
    return (new DateTimeImmutable('now'))->format('Y-m-d');
}

function key_holes_demo(): array
{
    $values = [0 => 'zero', 1 => 'one', 2 => 'two'];
    unset($values[1]);
    $values[] = 'three';

    return $values;
}

function key_order_demo(): array
{
    $values = [2 => 'two', 5 => 'five', 1 => 'one'];

    return $values;
}

function count_indexed_array(): int
{
    return count([10, 20, 30, 40]);
}

function count_associative_array(): int
{
    return count([
        'name' => 'Илья',
        'surname' => 'Шеклаков',
        'city' => 'Москва',
    ]);
}

function last_element(array $values): mixed
{
    if ($values === []) {
        return null;
    }

    $keys = array_keys($values);

    return $values[$keys[array_key_last($keys)]];
}

function penultimate_element(array $values): mixed
{
    if (count($values) < 2) {
        return null;
    }

    $keys = array_keys($values);
    $penultimateKeyIndex = array_key_last($keys) - 1;

    return $values[$keys[$penultimateKeyIndex]];
}

function format_key_value_pairs(array $values): string
{
    $parts = [];

    foreach ($values as $key => $value) {
        $parts[] = (string) $key . '=>' . (string) $value;
    }

    return implode(', ', $parts);
}

function format_nullable_value(mixed $value): string
{
    if ($value === null) {
        return 'n/a';
    }

    return (string) $value;
}

function build_assignment_output(): string
{
    $keyHoles = key_holes_demo();
    $keyOrder = key_order_demo();

    $lines = [
        'Keyed array: ' . format_key_value_pairs(keyed_letters_array()),
        'Months[1]: ' . months_array()[1],
        'Full name: ' . implode(' ', personal_data()),
        'Current date: ' . current_date_formatted(),
        'Key holes: ' . format_key_value_pairs($keyHoles),
        'Key order before sort: ' . format_key_value_pairs($keyOrder),
    ];

    $sortedKeyOrder = $keyOrder;
    ksort($sortedKeyOrder);
    $lines[] = 'Key order after sort: ' . format_key_value_pairs($sortedKeyOrder);
    $lines[] = 'Count indexed: ' . count_indexed_array();
    $lines[] = 'Count associative: ' . count_associative_array();
    $lines[] = 'Last element: ' . format_nullable_value(last_element(['red', 'green', 'blue']));
    $lines[] = 'Penultimate element: ' . format_nullable_value(penultimate_element(['red', 'green', 'blue']));

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    fwrite(STDOUT, build_assignment_output());
}
