<?php
// take_quiz.php
require_once 'functions.php';
$user = require_login();
$id = $_GET['id'] ?? null;
if (!$id) { echo "Quiz non précisé."; exit; }
$quiz = get_quiz($id);
if (!$quiz) { echo "Quiz introuvable."; exit; }

// If quiz inactive, only owner can view
if (!($quiz['active'] ?? true)) {
    if ($quiz['owner_id'] !== $user['id'] && $user['role'] !== 'admin') {
        echo "Quiz désactivé.";
        exit;
    }
}

// If no questions: cannot take
if (empty($quiz['questions'])) {
    echo "Quiz sans questions.";
    exit;
}
?>
<h1><?=htmlspecialchars($quiz['title'])?></h1>
<p><?=nl2br(htmlspecialchars($quiz['description'] ?? ''))?></p>

<form method="POST" action="submit_quiz.php">
    <input type="hidden" name="quiz_id" value="<?=htmlspecialchars($quiz['id'])?>">
    <?php foreach ($quiz['questions'] as $idx => $q): ?>
        <div>
            <p><strong><?=($idx+1)?>. <?=htmlspecialchars($q['text'])?> (<?=intval($q['points'])?> pts)</strong></p>
            <?php if ($q['type'] === 'mcq'): ?>
                <?php foreach ($q['options'] as $optIndex => $opt): ?>
                    <label>
                        <input type="radio" name="answers[<?=htmlspecialchars($q['id'])?>]" value="<?=htmlspecialchars($optIndex)?>">
                        <?=htmlspecialchars($opt)?>
                    </label><br>
                <?php endforeach; ?>
            <?php else: ?>
                <textarea name="answers[<?=htmlspecialchars($q['id'])?>]" rows="3" cols="60"></textarea>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <label>Ton nom (pour enregistrement): <input name="responder_name" required></label><br>
    <button type="submit">Soumettre</button>
</form>
