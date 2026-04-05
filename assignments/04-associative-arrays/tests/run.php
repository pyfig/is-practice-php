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
$lines = explode(PHP_EOL, trim($output));

assert_same(11, count($lines), 'CLI output line count mismatch.');
assert_same('Keyed array: 1=>a, 2=>b, 3=>c', $lines[0], 'Keyed array line mismatch.');
assert_same('Months[1]: January', $lines[1], 'Months line mismatch.');
assert_same('Full name: Илья Шеклаков Ильич', $lines[2], 'Full name line mismatch.');

$expectedDate = (new DateTimeImmutable('now'))->format('Y-m-d');
assert_same('Current date: ' . $expectedDate, $lines[3], 'Current date line mismatch.');
assert_true((bool) preg_match('/^Current date: \d{4}-\d{2}-\d{2}$/', $lines[3]) === true, 'Current date must use YYYY-MM-DD format.');

assert_same('Key holes: 0=>zero, 2=>two, 3=>three', $lines[4], 'Key holes line mismatch.');
assert_same('Key order before sort: 2=>two, 5=>five, 1=>one', $lines[5], 'Key order before sort line mismatch.');
assert_same('Key order after sort: 1=>one, 2=>two, 5=>five', $lines[6], 'Key order after sort line mismatch.');
assert_same('Count indexed: 4', $lines[7], 'Indexed count line mismatch.');
assert_same('Count associative: 3', $lines[8], 'Associative count line mismatch.');
assert_same('Last element: blue', $lines[9], 'Last element line mismatch.');
assert_same('Penultimate element: green', $lines[10], 'Penultimate element line mismatch.');

assert_same([1 => 'a', 2 => 'b', 3 => 'c'], keyed_letters_array(), 'Keyed letters array mismatch.');

$months = months_array();
assert_same('January', $months[1], 'January must remain under key 1.');
assert_same(12, count($months), 'Months array count mismatch.');

assert_same([
    'name' => 'Илья',
    'surname' => 'Шеклаков',
    'patronymic' => 'Ильич',
], personal_data(), 'Personal data mismatch.');

assert_same('zero', last_element([0 => 'zero']), 'Last element must work for single-element arrays.');
assert_same(null, penultimate_element([0 => 'zero']), 'Penultimate element must be null for short arrays.');
assert_same(null, last_element([]), 'Last element must be null for empty arrays.');

fwrite(STDOUT, '[OK] 04-associative-arrays tests passed.' . PHP_EOL);
exit(0);
