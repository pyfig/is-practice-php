<?php

declare(strict_types=1);

function average(array $numbers): float
{
    if ($numbers === []) {
        throw new InvalidArgumentException('Array must not be empty.');
    }

    return array_sum($numbers) / count($numbers);
}

function sum_one_to_one_hundred(): int
{
    return array_sum(range(1, 100));
}

function numbers_one_to_one_hundred(): array
{
    return range(1, 100);
}

function uppercase_last_character(string $value): string
{
    $length = mb_strlen($value, 'UTF-8');
    if ($length === 0) {
        return '';
    }

    $prefix = mb_substr($value, 0, $length - 1, 'UTF-8');
    $lastCharacter = mb_substr($value, -1, 1, 'UTF-8');

    return $prefix . mb_strtoupper($lastCharacter, 'UTF-8');
}

function square_roots(array $numbers): array
{
    return array_map(static fn (int|float $number): float => sqrt($number), $numbers);
}

function min_max(array $values): array
{
    if ($values === []) {
        throw new InvalidArgumentException('Array must not be empty.');
    }

    return [
        'min' => min($values),
        'max' => max($values),
    ];
}

function seeded_random_number(int $min, int $max, int $seed = 707): int
{
    mt_srand($seed);

    return mt_rand($min, $max);
}

function has_http_prefix(string $value): bool
{
    return mb_substr($value, 0, 4, 'UTF-8') === 'http';
}

function current_datetime_parts(DateTimeImmutable $dateTime): array
{
    return [
        'year' => (int) $dateTime->format('Y'),
        'month' => (int) $dateTime->format('n'),
        'day' => (int) $dateTime->format('j'),
        'hour' => (int) $dateTime->format('G'),
        'minute' => (int) $dateTime->format('i'),
        'second' => (int) $dateTime->format('s'),
    ];
}

function days_until_new_year(DateTimeImmutable $from): int
{
    $startOfDay = $from->setTime(0, 0, 0);
    $nextNewYear = $startOfDay
        ->setDate((int) $startOfDay->format('Y') + 1, 1, 1)
        ->setTime(0, 0, 0);

    return (int) $startOfDay->diff($nextNewYear)->format('%a');
}

function format_number(int|float $value): string
{
    if (is_float($value)) {
        if (fmod($value, 1.0) === 0.0) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(sprintf('%.10f', $value), '0'), '.');
    }

    return (string) $value;
}

function format_list(array $values): string
{
    return implode(', ', array_map(static fn (int|float $value): string => format_number($value), $values));
}

function reference_datetime(): DateTimeImmutable
{
    return (new DateTimeImmutable('@1712147696'))->setTimezone(new DateTimeZone('UTC'));
}

function build_assignment_output(): string
{
    $numbers = [1, 2, 3, 4, 5];
    $sqrtSource = [1, 4, 9, 16];
    $minMaxSource = [3, 7, 1, 9, 5];
    $exampleWord = 'привет';
    $dateTime = reference_datetime();
    $dateParts = current_datetime_parts($dateTime);
    $minMax = min_max($minMaxSource);

    $lines = [
        'Average [1,2,3,4,5]: ' . format_number(average($numbers)),
        'Sum 1..100: ' . sum_one_to_one_hundred(),
        'Print 1..100: ' . format_list(numbers_one_to_one_hundred()),
        'Uppercase last character (' . $exampleWord . '): ' . uppercase_last_character($exampleWord),
        'Square roots [1,4,9,16]: ' . format_list(square_roots($sqrtSource)),
        'Min/Max [3,7,1,9,5]: ' . format_number($minMax['min']) . '/' . format_number($minMax['max']),
        'Random number 1..100 (seed 707): ' . seeded_random_number(1, 100),
        '"http://example.com" starts with http: ' . (has_http_prefix('http://example.com') ? 'yes' : 'no'),
        '"ftp://example.com" starts with http: ' . (has_http_prefix('ftp://example.com') ? 'yes' : 'no'),
        'Current datetime parts: year=' . $dateParts['year']
            . ' month=' . $dateParts['month']
            . ' day=' . $dateParts['day']
            . ' hour=' . $dateParts['hour']
            . ' minute=' . $dateParts['minute']
            . ' second=' . $dateParts['second'],
        'Days until New Year from reference date: ' . days_until_new_year($dateTime),
    ];

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    fwrite(STDOUT, build_assignment_output());
}
