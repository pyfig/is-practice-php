<?php

declare(strict_types=1);

function utf8_string_length(string $value): int
{
    return mb_strlen($value, 'UTF-8');
}

function last_character(string $value): string
{
    return mb_substr($value, -1, 1, 'UTF-8');
}

function circle_area(float $radius): float
{
    return pi() * $radius * $radius;
}

function rectangle_area(float $width, float $height): float
{
    return $width * $height;
}

function rectangle_perimeter(float $width, float $height): float
{
    return 2 * ($width + $height);
}

function format_number(float $value, int $decimals = 2): string
{
    return number_format($value, $decimals, '.', '');
}

function build_assignment_output(): string
{
    $name = 'Ada Lovelace';
    $age = 20;
    $studyPlace = 'OpenAI Academy';
    $hobbies = 'reading, coding, chess';
    $sourceString = 'Привет';
    $radius = 3.0;
    $width = 4.0;
    $height = 5.0;

    $lines = [
        'Name: ' . $name,
        'Age: ' . $age,
        'Study place: ' . $studyPlace,
        'Hobbies: ' . $hobbies,
        'String length (Привет): ' . utf8_string_length($sourceString),
        'Last character (Привет): ' . last_character($sourceString),
        'Circle area: ' . format_number(circle_area($radius)),
        'Rectangle area: ' . format_number(rectangle_area($width, $height), 0),
        'Rectangle perimeter: ' . format_number(rectangle_perimeter($width, $height), 0),
    ];

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    fwrite(STDOUT, build_assignment_output());
}
