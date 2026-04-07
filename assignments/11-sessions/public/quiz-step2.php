<?php
ob_start();
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz = session_namespace('quiz');
    $quiz['answer_2'] = get_post_string('answer_2');
    store_session_namespace('quiz', $quiz);
    redirect_to('/quiz-result.php');
}

$body = '<h1>Мини-квиз</h1>'
    . '<p><a href="' . escape_html(app_url('/quiz-step1.php')) . '">Назад на шаг 1</a></p>'
    . '<section><h2>Шаг 2</h2><form method="post"><label>Где хранятся данные между страницами этого задания?<input type="text" name="answer_2" placeholder="cookie"></label><button type="submit">Завершить квиз</button></form></section>';

render_page('11 Sessions — quiz step 2', $body);
