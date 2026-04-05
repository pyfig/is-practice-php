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

function assert_float_equals(float $expected, float $actual, float $epsilon, string $message): void
{
    if (abs($expected - $actual) > $epsilon) {
        fail($message . ' Expected ' . $expected . ', got ' . $actual);
    }
}

$output = build_assignment_output();
$lines = explode(PHP_EOL, trim($output));

assert_same(9, count($lines), 'CLI output line count mismatch.');
assert_same('Name: Ada Lovelace', $lines[0], 'Name line mismatch.');
assert_same('Age: 20', $lines[1], 'Age line mismatch.');
assert_same('Study place: OpenAI Academy', $lines[2], 'Study place line mismatch.');
assert_same('Hobbies: reading, coding, chess', $lines[3], 'Hobbies line mismatch.');
assert_same('String length (Привет): 6', $lines[4], 'UTF-8 string length line mismatch.');
assert_same('Last character (Привет): т', $lines[5], 'Last character line mismatch.');
assert_same('Circle area: 28.27', $lines[6], 'Circle area output mismatch.');
assert_same('Rectangle area: 20', $lines[7], 'Rectangle area output mismatch.');
assert_same('Rectangle perimeter: 18', $lines[8], 'Rectangle perimeter output mismatch.');

assert_same(6, utf8_string_length('Привет'), 'UTF-8 string length assertion failed.');
assert_same('т', last_character('Привет'), 'UTF-8 last-character assertion failed.');
assert_float_equals(28.274333882308138, circle_area(3.0), 0.0000000001, 'Circle area formula assertion failed.');
assert_float_equals(20.0, rectangle_area(4.0, 5.0), 0.0000000001, 'Rectangle area formula assertion failed.');
assert_float_equals(18.0, rectangle_perimeter(4.0, 5.0), 0.0000000001, 'Rectangle perimeter formula assertion failed.');

fwrite(STDOUT, '[OK] 01-php-basics tests passed.' . PHP_EOL);
exit(0);
