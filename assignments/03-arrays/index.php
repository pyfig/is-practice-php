<?php

declare(strict_types=1);

function arithmetic_from_array(array $numbers): int
{
    return ($numbers[0] * $numbers[1]) + ($numbers[2] * $numbers[3]);
}

function user_profile(): array
{
    return [
        'name' => 'Илья',
        'surname' => 'Шеклаков',
        'city' => 'Москва',
    ];
}

function one_to_five_array(): array
{
    return range(1, 5);
}

function min_max_values(array $values): array
{
    return [
        'min' => min($values),
        'max' => max($values),
    ];
}

function contains_value(array $values, mixed $needle): bool
{
    return in_array($needle, $values, true);
}

function sum_elements(array $values): int|float
{
    return array_sum($values);
}

function numbers_one_to_one_hundred(): array
{
    return range(1, 100);
}

function alphabet_letters(): array
{
    return range('a', 'z');
}

function sum_one_to_one_hundred_without_loop(): int
{
    return array_sum(numbers_one_to_one_hundred());
}

function shuffled_one_to_twenty_five(int $seed = 3025): array
{
    $values = range(1, 25);
    mt_srand($seed);
    shuffle($values);

    return $values;
}

function random_nonrepeating_alphabet(int $seed = 2603): string
{
    $letters = alphabet_letters();
    mt_srand($seed);
    shuffle($letters);

    return implode('', $letters);
}

function sort_demonstrations(array $source): array
{
    $demonstrations = [];

    $before = $source;
    $after = $source;
    sort($after);
    $demonstrations['sort'] = ['before' => $before, 'after' => $after];

    $before = $source;
    $after = $source;
    asort($after);
    $demonstrations['asort'] = ['before' => $before, 'after' => $after];

    $before = $source;
    $after = $source;
    ksort($after);
    $demonstrations['ksort'] = ['before' => $before, 'after' => $after];

    $before = $source;
    $after = $source;
    rsort($after);
    $demonstrations['rsort'] = ['before' => $before, 'after' => $after];

    $before = $source;
    $after = $source;
    arsort($after);
    $demonstrations['arsort'] = ['before' => $before, 'after' => $after];

    $before = $source;
    $after = $source;
    krsort($after);
    $demonstrations['krsort'] = ['before' => $before, 'after' => $after];

    return $demonstrations;
}

function format_list(array $values): string
{
    return implode(', ', array_map(static fn (int|float|string $value): string => (string) $value, $values));
}

function format_assoc(array $values): string
{
    $parts = [];
    foreach ($values as $key => $value) {
        $parts[] = (string) $key . '=>' . (string) $value;
    }

    return implode(', ', $parts);
}

function build_assignment_output(): string
{
    $arithmeticSource = [2, 5, 3, 9];
    $searchSource = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    $sortSource = ['3' => 'a', '1' => 'c', '2' => 'e', '4' => 'b'];

    $lines = [
        'Arithmetic [2,5,3,9]: ' . arithmetic_from_array($arithmeticSource),
        'User profile: ' . format_assoc(user_profile()),
        'Fill 1..5: ' . format_list(one_to_five_array()),
    ];

    $minMax = min_max_values($searchSource);
    $lines[] = 'Min/Max: ' . $minMax['min'] . '/' . $minMax['max'];
    $lines[] = 'Contains 3: ' . (contains_value($searchSource, 3) ? 'yes' : 'no');
    $lines[] = 'Sum elements: ' . sum_elements($searchSource);
    $lines[] = 'Array 1..100: ' . format_list(numbers_one_to_one_hundred());
    $lines[] = 'Array a..z: ' . format_list(alphabet_letters());
    $lines[] = 'Sum 1..100 (no loop): ' . sum_one_to_one_hundred_without_loop();
    $lines[] = 'Shuffled 1..25: ' . format_list(shuffled_one_to_twenty_five());
    $lines[] = 'Random nonrepeating alphabet: ' . random_nonrepeating_alphabet();

    foreach (sort_demonstrations($sortSource) as $sortName => $demo) {
        $lines[] = $sortName . ' | before: ' . format_assoc($demo['before']) . ' | after: ' . format_assoc($demo['after']);
    }

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    fwrite(STDOUT, build_assignment_output());
}
