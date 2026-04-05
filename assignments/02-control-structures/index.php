<?php

declare(strict_types=1);

function season_from_month(int $month): string
{
    if ($month < 1 || $month > 12) {
        throw new InvalidArgumentException('Month must be between 1 and 12.');
    }

    return match ($month) {
        12, 1, 2 => 'winter',
        3, 4, 5 => 'spring',
        6, 7, 8 => 'summer',
        default => 'autumn',
    };
}

function first_character_is_a(string $value): bool
{
    return mb_substr($value, 0, 1, 'UTF-8') === 'a';
}

function is_lucky_six_digit_number(int $number): bool
{
    if ($number < 100000 || $number > 999999) {
        return false;
    }

    $digits = str_split((string) $number);
    $leftSum = array_sum(array_map('intval', array_slice($digits, 0, 3)));
    $rightSum = array_sum(array_map('intval', array_slice($digits, 3, 3)));

    return $leftSum === $rightSum;
}

function raise_all_salaries_by_ten_percent(array $salaries): array
{
    return array_map(static fn (int|float $salary): float => round($salary * 1.1, 2), $salaries);
}

function raise_only_salaries_up_to_400(array $salaries): array
{
    return array_map(
        static fn (int|float $salary): float => $salary <= 400 ? round($salary * 1.1, 2) : (float) $salary,
        $salaries
    );
}

function numbers_one_to_one_hundred(): array
{
    return range(1, 100);
}

function filter_positive_less_than_ten(array $numbers): array
{
    return array_values(array_filter(
        $numbers,
        static fn (int|float $number): bool => $number > 0 && $number < 10
    ));
}

function sum_and_average(array $numbers): array
{
    $sum = array_sum($numbers);
    $count = count($numbers);

    return [
        'sum' => $sum,
        'average' => $count === 0 ? 0.0 : (float) ($sum / $count),
    ];
}

function format_number(float $value, int $decimals = 2): string
{
    return number_format($value, $decimals, '.', '');
}

function format_number_list(array $numbers): string
{
    return implode(', ', array_map(static fn (int|float $number): string => (string) $number, $numbers));
}

function build_assignment_output(): string
{
    $lines = [];

    try {
        $lines[] = 'Month 3: ' . season_from_month(3);
        season_from_month(13);
    } catch (InvalidArgumentException $exception) {
        $lines[] = 'Month 13: invalid';
    }

    $lines[] = 'First character of "apple" is a: ' . (first_character_is_a('apple') ? 'yes' : 'no');
    $lines[] = 'First character of "Banana" is a: ' . (first_character_is_a('Banana') ? 'yes' : 'no');
    $lines[] = 'Lucky 385916: ' . (is_lucky_six_digit_number(385916) ? 'yes' : 'no');
    $lines[] = 'Lucky 123456: ' . (is_lucky_six_digit_number(123456) ? 'yes' : 'no');
    $lines[] = 'Salaries +10%: ' . format_number_list(raise_all_salaries_by_ten_percent([1000, 2000, 3000]));
    $lines[] = 'Salaries <= 400 +10%: ' . format_number_list(raise_only_salaries_up_to_400([150, 400, 500]));
    $lines[] = '1..100: ' . format_number_list(numbers_one_to_one_hundred());
    $lines[] = 'Filtered 0..10: ' . format_number_list(filter_positive_less_than_ten([-2, 0, 5, 9, 10, 12]));

    $aggregates = sum_and_average([1, 2, 3, 4, 5]);
    $lines[] = 'Sum: ' . (string) $aggregates['sum'] . '; Average: ' . format_number((float) $aggregates['average']);

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    fwrite(STDOUT, build_assignment_output());
}
