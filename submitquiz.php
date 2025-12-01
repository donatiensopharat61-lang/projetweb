<?php
// submit_quiz.php
require_once 'functions.php';
$user = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }
$quiz_id = $_POST['quiz_id'] ?? null;
$quiz = get_quiz($quiz_id);
if (!$quiz) { echo "Quiz introuvable."; exit; }

$answers = $_POST['answers'] ?? [];
$responder_name = trim($_POST['responder_name'] ?? ($user['first_name'].' '.$user['last_name']));

$score = 0;
$max = 0;
$detailed = [];

foreach ($quiz['questions'] as $q) {
    $max += intval($q['points'] ?? 1);
    $qid = $q['id'];
    $given = $answers[$qid] ?? null;
    $correct = null;
    $obtained = 0;

    if ($q['type'] === 'mcq') {
        // store correct as index or value; when author saved answer it may be index/text
        $correct_raw = $q['answer'];
        // try to interpret as index numeric:
        if (is_numeric($correct_raw)) {
            $correctIndex = intval($correct_raw);
            $isCorrect = (string)$given === (string)$correctIndex;
        } else {
            // compare by option text
            $opt = isset($q['options'][intval($given)]) ? $q['options'][intval($given)] : null;
            $isCorrect = $opt !== null && trim(strtolower($opt)) === trim(strtolower($correct_raw));
        }
        if ($isCorrect) {
            $obtained = intval($q['points'] ?? 1);
            $score += $obtained;
        }
        $correct = $correct_raw;
    } else {
        // open: we do not auto-grade beyond exact match (could be improved)
        $correct_raw = $q['answer'] ?? null;
        if ($correct_raw !== null && strtolower(trim($given)) === strtolower(trim($correct_raw))) {
            $obtained = intval($q['points'] ?? 1);
            $score += $obtained;
        }
        $correct = $correct_raw;
    }

    $detailed[] = [
        'question_id' => $qid,
        'given' => $given,
        'correct' => $correct,
        'points' => intval($q['points'] ?? 1),
        'obtained' => $obtained,
    ];
}

// save response
$response = [
    'id' => uniqid('resp_'),
    'quiz_id' => $quiz_id,
    'responder_name' => $responder_name,
    'user_id' => $user['id'],
    'score' => $score,
    'max_score' => $max,
    'detailed' => $detailed,
    'created_at' => date('c')
];
save_response($response);

// redirect to results page or show summary
?>
<h1>Résultat</h1>
<p>Merci <?=htmlspecialchars($responder_name)?> — Score: <?=$score?> / <?=$max?></p>
<p><a href="dashboard.php">Retour au dashboard</a></p>
