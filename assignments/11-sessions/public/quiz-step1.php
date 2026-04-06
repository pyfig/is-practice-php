<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz = session_namespace('quiz');
    $quiz['answer_1'] = get_post_string('answer_1');
    store_session_namespace('quiz', $quiz);
    redirect_to('/quiz-step2.php');
}

$body = '<h1>Мини-квиз</h1>'
    . '<p><a href="' . escape_html(app_url()) . '">На главную</a></p>'
    . '<section><h2>Шаг 1</h2><form method="post"><label>Какой язык выполняется на сервере в этом задании?<input type="text" name="answer_1" placeholder="PHP"></label><button type="submit">Дальше</button></form></section>';

render_page('11 Sessions — quiz step 1', $body);
