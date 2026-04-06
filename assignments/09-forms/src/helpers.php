<?php
declare(strict_types=1);

function app_base_path(): string
{
    $basePath = $_SERVER['APP_BASE_PATH'] ?? '/09-forms';

    if (!is_string($basePath) || $basePath === '') {
        return '/09-forms';
    }

    return rtrim($basePath, '/') ?: '/';
}

function app_url(string $path = ''): string
{
    $basePath = app_base_path();
    $normalizedPath = ltrim($path, '/');

    if ($normalizedPath === '') {
        return $basePath;
    }

    return $basePath === '/'
        ? '/' . $normalizedPath
        : $basePath . '/' . $normalizedPath;
}

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function get_post_string(string $key): string
{
    $value = $_POST[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

function get_request_string(array $source, string $key): string
{
    $value = $source[$key] ?? '';

    return is_string($value) ? trim($value) : '';
}

function normalize_numeric_input(string $value): string
{
    return str_replace(',', '.', trim($value));
}

function parse_number(string $value): ?float
{
    $normalized = normalize_numeric_input($value);
    if ($normalized === '' || !is_numeric($normalized)) {
        return null;
    }

    return (float) $normalized;
}

function parse_positive_int(string $value): ?int
{
    $trimmed = trim($value);
    if ($trimmed === '' || preg_match('/^\d+$/', $trimmed) !== 1) {
        return null;
    }

    return (int) $trimmed;
}

function render_alert(string $title, string $message): string
{
    return sprintf(
        '<div style="padding:12px;border:1px solid #d1d5db;background:#f8fafc;margin-top:12px;"><strong>%s</strong><br>%s</div>',
        escape_html($title),
        escape_html($message)
    );
}

function parse_birthday(string $value): ?DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat('!d.m.Y', trim($value));
    if ($date === false) {
        return null;
    }

    $errors = DateTimeImmutable::getLastErrors();
    if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
        return null;
    }

    return $date;
}

function days_until_next_birthday(DateTimeImmutable $birthday, ?DateTimeImmutable $today = null): int
{
    $today = $today ?? new DateTimeImmutable('today');
    $birthdayMonth = (int) $birthday->format('m');
    $birthdayDay = (int) $birthday->format('d');
    $targetYear = (int) $today->format('Y');

    $nextBirthday = next_birthday_for_year($birthdayMonth, $birthdayDay, $targetYear);

    if ($nextBirthday < $today) {
        $nextBirthday = next_birthday_for_year($birthdayMonth, $birthdayDay, $targetYear + 1);
    }

    return (int) $today->diff($nextBirthday)->days;
}

function next_birthday_for_year(int $month, int $day, int $year): DateTimeImmutable
{
    if ($month === 2 && $day === 29 && !checkdate(2, 29, $year)) {
        return new DateTimeImmutable(sprintf('%04d-03-01', $year));
    }

    return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day));
}

function count_words_utf8(string $value): int
{
    $trimmed = trim($value);
    if ($trimmed === '') {
        return 0;
    }

    $words = preg_split('/[\s,.;:!?()\[\]{}"«»]+/u', $trimmed, -1, PREG_SPLIT_NO_EMPTY);

    return $words === false ? 0 : count($words);
}
