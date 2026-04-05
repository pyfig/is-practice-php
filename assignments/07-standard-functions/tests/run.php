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

function assert_throws(callable $callable, string $exceptionClass, string $message): void
{
    try {
        $callable();
    } catch (Throwable $throwable) {
        if ($throwable instanceof $exceptionClass) {
            return;
        }

        fail($message . ' Wrong exception type: ' . $throwable::class);
    }

    fail($message . ' Exception was not thrown.');
}

$output = build_assignment_output();
$outputLines = explode(PHP_EOL, trim($output));

$referenceDateTime = reference_datetime();
$referenceDateParts = current_datetime_parts($referenceDateTime);

assert_same(
    [
        'Average [1,2,3,4,5]: 3',
        'Sum 1..100: 5050',
        'Print 1..100: ' . implode(', ', range(1, 100)),
        'Uppercase last character (привет): привеТ',
        'Square roots [1,4,9,16]: 1, 2, 3, 4',
        'Min/Max [3,7,1,9,5]: 1/9',
        'Random number 1..100 (seed 707): ' . seeded_random_number(1, 100),
        '"http://example.com" starts with http: yes',
        '"ftp://example.com" starts with http: no',
        'Current datetime parts: year=' . $referenceDateParts['year']
            . ' month=' . $referenceDateParts['month']
            . ' day=' . $referenceDateParts['day']
            . ' hour=' . $referenceDateParts['hour']
            . ' minute=' . $referenceDateParts['minute']
            . ' second=' . $referenceDateParts['second'],
        'Days until New Year from reference date: ' . days_until_new_year($referenceDateTime),
    ],
    $outputLines,
    'CLI output mismatch.'
);

assert_same(3.0, average([1, 2, 3, 4, 5]), 'Average helper failed for integer input.');
assert_same(2.5, average([1, 2, 3, 4]), 'Average helper failed for fractional result.');
assert_throws(static fn (): float => average([]), InvalidArgumentException::class, 'Average helper must reject empty arrays.');

assert_same(5050, sum_one_to_one_hundred(), '1..100 sum helper failed.');
assert_same(range(1, 100), numbers_one_to_one_hundred(), '1..100 print helper source array mismatch.');

assert_same('привеТ', uppercase_last_character('привет'), 'Cyrillic uppercasing of last character failed.');
assert_same('ёЖ', uppercase_last_character('ёж'), 'mb-safe handling of multibyte last character failed.');
assert_same('', uppercase_last_character(''), 'Uppercase helper should keep empty strings unchanged.');

assert_same([1.0, 2.0, 3.0, 4.0], square_roots([1, 4, 9, 16]), 'Square roots helper failed.');
assert_same(['min' => 1, 'max' => 9], min_max([3, 7, 1, 9, 5]), 'Min/max helper failed.');
assert_throws(static fn (): array => min_max([]), InvalidArgumentException::class, 'Min/max helper must reject empty arrays.');

assert_same(seeded_random_number(1, 100), seeded_random_number(1, 100), 'Seeded random helper must be deterministic for same seed.');
assert_same(20, seeded_random_number(1, 100), 'Seeded random helper baseline value changed unexpectedly.');

assert_true(has_http_prefix('http://site.test'), 'HTTP prefix detection should pass for http URLs.');
assert_true(has_http_prefix('https://site.test'), 'HTTP prefix detection should pass for https URLs.');
assert_true(has_http_prefix('httpfile'), 'HTTP prefix detection should pass for plain http-prefix strings.');
assert_true(!has_http_prefix('ftp://site.test'), 'HTTP prefix detection should fail for non-http prefixes.');

$dateParts = current_datetime_parts(new DateTimeImmutable('2032-12-31 23:58:59', new DateTimeZone('UTC')));
assert_same(
    ['year' => 2032, 'month' => 12, 'day' => 31, 'hour' => 23, 'minute' => 58, 'second' => 59],
    $dateParts,
    'Date parts helper failed.'
);

$fromYear2024 = new DateTimeImmutable('2024-12-30 15:00:00', new DateTimeZone('UTC'));
$fromYear2031 = new DateTimeImmutable('2031-12-30 08:30:00', new DateTimeZone('UTC'));
$fromLeapYear = new DateTimeImmutable('2024-02-28 10:00:00', new DateTimeZone('UTC'));

assert_same(2, days_until_new_year($fromYear2024), 'New Year countdown failed for end-of-year date.');
assert_same(2, days_until_new_year($fromYear2031), 'New Year countdown must work across years without hardcoding.');
assert_same(308, days_until_new_year($fromLeapYear), 'New Year countdown failed for leap-year date.');

$source = file_get_contents(__DIR__ . '/../index.php');
if ($source === false) {
    fail('Unable to read index.php for year-hardcode validation.');
}

preg_match('/function days_until_new_year\(DateTimeImmutable \$from\): int\s*\{(?<body>.*?)\}/s', $source, $matches);
if (!isset($matches['body'])) {
    fail('Unable to locate days_until_new_year() body.');
}

assert_true((bool) preg_match('/\b20\d{2}\b/', $matches['body']) === false, 'days_until_new_year() must not hardcode a calendar year.');

fwrite(STDOUT, '[OK] 07-standard-functions tests passed.' . PHP_EOL);
exit(0);
