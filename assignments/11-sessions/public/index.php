<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$visitTimer = session_namespace('visit_timer');
if (!isset($visitTimer['first_entered_at'])) {
    $visitTimer['first_entered_at'] = time();
}
store_session_namespace('visit_timer', $visitTimer);

$refreshCounter = session_namespace('refresh_counter');
$refreshCounter['count'] = isset($refreshCounter['count']) ? (int) $refreshCounter['count'] + 1 : 1;
store_session_namespace('refresh_counter', $refreshCounter);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = get_post_string('country');
    $countryForm = session_namespace('country_form');
    $countryForm['country'] = $country;
    store_session_namespace('country_form', $countryForm);
    redirect_to('/test.php');
}

$secondsOnSite = time() - (int) ($visitTimer['first_entered_at'] ?? time());
$isFirstVisit = $refreshCounter['count'] === 1;

$body = '<h1>Практика по сессиям в PHP</h1>'
    . '<section><h2>1. Страна пользователя</h2>'
    . '<form method="post"><label>Страна <input type="text" name="country" placeholder="Россия"></label><button type="submit">Сохранить страну</button></form>'
    . '<p><a href="/test.php">Перейти на test.php</a></p></section>'
    . '<section><h2>2. Время с первого входа</h2><p>С момента первого посещения прошло <strong>' . $secondsOnSite . '</strong> сек.</p></section>'
    . '<section><h2>3. Счётчик обновлений</h2><p>'
    . ($isFirstVisit ? 'Добро пожаловать! Это ваш первый визит.' : 'Вы уже обновляли страницу ранее.')
    . '</p><p>Текущее количество открытий index.php: <strong>' . (int) $refreshCounter['count'] . '</strong></p></section>'
    . '<section><h2>Другие упражнения</h2><nav><ul>'
    . '<li><a href="/email-step1.php">Передача email между формами</a></li>'
    . '<li><a href="/profile-step1.php">Форма с предзаполнением Name / Age / City</a></li>'
    . '<li><a href="/quiz-step1.php">Многостраничный мини-квиз</a></li>'
    . '<li><a href="/logout.php">Выход и очистка сессии</a></li>'
    . '</ul></nav></section>';

render_page('11 Sessions', $body);
