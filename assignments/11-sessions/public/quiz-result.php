<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

$quiz = session_namespace('quiz');
$answer1 = isset($quiz['answer_1']) && is_string($quiz['answer_1']) ? trim($quiz['answer_1']) : '';
$answer2 = isset($quiz['answer_2']) && is_string($quiz['answer_2']) ? trim($quiz['answer_2']) : '';

$score = 0;
if (mb_strtolower($answer1, 'UTF-8') === 'php') {
    $score++;
}
if (in_array(mb_strtolower($answer2, 'UTF-8'), ['cookie', 'cookies', 'куки', 'кука', 'signed cookie', 'json cookie', 'session', 'сессия', 'сессии'], true)) {
    $score++;
}

$body = '<h1>Результат квиза</h1>'
    . '<p><a href="' . escape_html(app_url('/quiz-step1.php')) . '">Пройти ещё раз</a></p>'
    . '<section><h2>Итог</h2><p>Ответ 1: <strong>' . escape_html($answer1) . '</strong></p><p>Ответ 2: <strong>' . escape_html($answer2) . '</strong></p><p>Количество правильных ответов: <strong>' . $score . ' из 2</strong></p></section>';

render_page('11 Sessions — quiz result', $body);
