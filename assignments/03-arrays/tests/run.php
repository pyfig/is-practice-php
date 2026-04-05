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

assert_same(17, count($lines), 'CLI output line count mismatch.');
assert_same('Arithmetic [2,5,3,9]: 37', $lines[0], 'Arithmetic line mismatch.');
assert_same('User profile: name=>Илья, surname=>Шеклаков, city=>Москва', $lines[1], 'User profile line mismatch.');
assert_same('Fill 1..5: 1, 2, 3, 4, 5', $lines[2], 'Fill 1..5 line mismatch.');
assert_same('Min/Max: 1/9', $lines[3], 'Min/Max line mismatch.');
assert_same('Contains 3: yes', $lines[4], 'Contains 3 line mismatch.');
assert_same('Sum elements: 45', $lines[5], 'Sum elements line mismatch.');
assert_same('Array 1..100: ' . implode(', ', range(1, 100)), $lines[6], 'Array 1..100 line mismatch.');
assert_same('Array a..z: ' . implode(', ', range('a', 'z')), $lines[7], 'Array a..z line mismatch.');
assert_same('Sum 1..100 (no loop): 5050', $lines[8], 'No-loop sum line mismatch.');
assert_same('Shuffled 1..25: ' . implode(', ', shuffled_one_to_twenty_five()), $lines[9], 'Shuffled 1..25 line mismatch.');
assert_same('Random nonrepeating alphabet: ' . random_nonrepeating_alphabet(), $lines[10], 'Random alphabet line mismatch.');

$sortSource = ['3' => 'a', '1' => 'c', '2' => 'e', '4' => 'b'];
$demos = sort_demonstrations($sortSource);

assert_same(
    'sort | before: 3=>a, 1=>c, 2=>e, 4=>b | after: 0=>a, 1=>b, 2=>c, 3=>e',
    $lines[11],
    'sort demonstration line mismatch.'
);
assert_same(
    'asort | before: 3=>a, 1=>c, 2=>e, 4=>b | after: 3=>a, 4=>b, 1=>c, 2=>e',
    $lines[12],
    'asort demonstration line mismatch.'
);
assert_same(
    'ksort | before: 3=>a, 1=>c, 2=>e, 4=>b | after: 1=>c, 2=>e, 3=>a, 4=>b',
    $lines[13],
    'ksort demonstration line mismatch.'
);
assert_same(
    'rsort | before: 3=>a, 1=>c, 2=>e, 4=>b | after: 0=>e, 1=>c, 2=>b, 3=>a',
    $lines[14],
    'rsort demonstration line mismatch.'
);
assert_same(
    'arsort | before: 3=>a, 1=>c, 2=>e, 4=>b | after: 2=>e, 1=>c, 4=>b, 3=>a',
    $lines[15],
    'arsort demonstration line mismatch.'
);
assert_same(
    'krsort | before: 3=>a, 1=>c, 2=>e, 4=>b | after: 4=>b, 3=>a, 2=>e, 1=>c',
    $lines[16],
    'krsort demonstration line mismatch.'
);

assert_same(37, arithmetic_from_array([2, 5, 3, 9]), 'Arithmetic helper failed.');
assert_same(['name' => 'Илья', 'surname' => 'Шеклаков', 'city' => 'Москва'], user_profile(), 'User profile helper failed.');
assert_same([1, 2, 3, 4, 5], one_to_five_array(), '1..5 helper failed.');
assert_same(['min' => 1, 'max' => 9], min_max_values([1, 2, 3, 4, 5, 6, 7, 8, 9]), 'Min/max helper failed.');
assert_true(contains_value([1, 2, 3], 3), 'Contains helper positive case failed.');
assert_true(!contains_value([1, 2, 3], 30), 'Contains helper negative case failed.');
assert_same(45, sum_elements([1, 2, 3, 4, 5, 6, 7, 8, 9]), 'Sum helper failed.');
assert_same(range(1, 100), numbers_one_to_one_hundred(), '1..100 range helper failed.');
assert_same(range('a', 'z'), alphabet_letters(), 'Alphabet helper failed.');
assert_same(5050, sum_one_to_one_hundred_without_loop(), 'No-loop sum helper failed.');
assert_same(25, count(shuffled_one_to_twenty_five()), 'Shuffled 1..25 count mismatch.');
$shuffled = shuffled_one_to_twenty_five();
sort($shuffled);
assert_same(range(1, 25), $shuffled, 'Shuffled 1..25 must contain unique 1..25 values.');

$alphabet = random_nonrepeating_alphabet();
assert_same(26, strlen($alphabet), 'Random alphabet length mismatch.');
assert_same(26, count(array_unique(str_split($alphabet))), 'Random alphabet must contain 26 unique letters.');
$alphabetSorted = str_split($alphabet);
sort($alphabetSorted);
assert_same(range('a', 'z'), $alphabetSorted, 'Random alphabet must include every letter a..z exactly once.');

assert_same([0, 1, 2, 3], array_keys($demos['sort']['after']), 'sort() must reindex keys.');
assert_same([3, 4, 1, 2], array_keys($demos['asort']['after']), 'asort() must preserve keys by value order.');
assert_same([1, 2, 3, 4], array_keys($demos['ksort']['after']), 'ksort() must preserve key-value pairs and sort by key ascending.');
assert_same([0, 1, 2, 3], array_keys($demos['rsort']['after']), 'rsort() must reindex keys.');
assert_same([2, 1, 4, 3], array_keys($demos['arsort']['after']), 'arsort() must preserve keys by descending values.');
assert_same([4, 3, 2, 1], array_keys($demos['krsort']['after']), 'krsort() must preserve key-value pairs and sort by key descending.');

$source = file_get_contents(__DIR__ . '/../index.php');
if ($source === false) {
    fail('Unable to read index.php for no-loop validation.');
}

preg_match('/function sum_one_to_one_hundred_without_loop\(\): int\s*\{(?<body>.*?)\}/s', $source, $matches);
if (!isset($matches['body'])) {
    fail('Unable to locate sum_one_to_one_hundred_without_loop() body.');
}

assert_true((bool) preg_match('/\b(for|foreach|while|do)\b/', $matches['body']) === false, 'sum_one_to_one_hundred_without_loop() must not use loops.');

fwrite(STDOUT, '[OK] 03-arrays tests passed.' . PHP_EOL);
exit(0);
