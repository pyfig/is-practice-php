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

function assert_throws(callable $callback, string $expectedClass, string $message): void
{
    try {
        $callback();
    } catch (Throwable $exception) {
        if ($exception instanceof $expectedClass) {
            return;
        }

        fail($message . ' Expected ' . $expectedClass . ', got ' . get_class($exception));
    }

    fail($message . ' Expected ' . $expectedClass . ', but nothing was thrown.');
}

$output = build_assignment_output();
$lines = explode(PHP_EOL, trim($output));

assert_same(11, count($lines), 'CLI output line count mismatch.');
assert_same('Month 3: spring', $lines[0], 'Season line mismatch.');
assert_same('Month 13: invalid', $lines[1], 'Invalid month line mismatch.');
assert_same('First character of "apple" is a: yes', $lines[2], 'First-character yes line mismatch.');
assert_same('First character of "Banana" is a: no', $lines[3], 'First-character no line mismatch.');
assert_same('Lucky 385916: yes', $lines[4], 'Lucky valid line mismatch.');
assert_same('Lucky 123456: no', $lines[5], 'Lucky invalid line mismatch.');
assert_same('Salaries +10%: 1100, 2200, 3300', $lines[6], 'Salary raise all line mismatch.');
assert_same('Salaries <= 400 +10%: 165, 440, 500', $lines[7], 'Salary threshold line mismatch.');
assert_same('1..100: ' . implode(', ', range(1, 100)), $lines[8], '1..100 line mismatch.');
assert_same('Filtered 0..10: 5, 9', $lines[9], 'Filtered array line mismatch.');
assert_same('Sum: 15; Average: 3.00', $lines[10] ?? '', 'Sum/average line mismatch.');

assert_same('winter', season_from_month(12), 'Season mapping for December failed.');
assert_same('autumn', season_from_month(11), 'Season mapping for November failed.');
assert_same(true, first_character_is_a('apple'), 'First-character positive check failed.');
assert_same(false, first_character_is_a('banana'), 'First-character negative check failed.');
assert_same(true, is_lucky_six_digit_number(385916), 'Lucky-number positive case failed.');
assert_same(false, is_lucky_six_digit_number(123456), 'Lucky-number negative case failed.');
assert_same(false, is_lucky_six_digit_number(12345), 'Lucky-number non-six-digit rejection failed.');
assert_same([1100.0, 2200.0, 3300.0], raise_all_salaries_by_ten_percent([1000, 2000, 3000]), 'Raise-all salaries failed.');
assert_same([165.0, 440.0, 500.0], raise_only_salaries_up_to_400([150, 400, 500]), 'Raise-threshold salaries failed.');
assert_same(range(1, 100), numbers_one_to_one_hundred(), '1..100 range failed.');
assert_same([5, 9], filter_positive_less_than_ten([-2, 0, 5, 9, 10, 12]), 'Filter positive range failed.');

$aggregates = sum_and_average([1, 2, 3, 4, 5]);
assert_same(['sum' => 15, 'average' => 3.0], $aggregates, 'Sum/average aggregation failed.');
assert_throws(static fn () => season_from_month(0), InvalidArgumentException::class, 'Invalid month rejection failed.');
assert_throws(static fn () => season_from_month(13), InvalidArgumentException::class, 'Upper-bound month rejection failed.');

fwrite(STDOUT, '[OK] 02-control-structures tests passed.' . PHP_EOL);
exit(0);
