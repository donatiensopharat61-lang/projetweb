<?php
// edit_quiz.php
require_once 'functions.php';
$user = require_login();

$id = $_GET['id'] ?? null;
if (!$id) { echo "Quiz non spécifié."; exit; }
$quiz = get_quiz($id);
if (!$quiz) { echo "Quiz introuvable."; exit; }
if ($quiz['owner_id'] !== $user['id'] && $user['role'] !== 'admin') {
    echo "Accès refusé.";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz['title'] = trim($_POST['title'] ?? $quiz['title']);
    $quiz['description'] = trim($_POST['description'] ?? $quiz['description']);
    $quiz['status'] = $_POST['status'] ?? $quiz['status'];
    $quiz['active'] = isset($_POST['active']) ? true : false;

    // For simplicity, we replace questions entirely if provided
    $q_texts = $_POST['q_text'] ?? [];
    $q_type = $_POST['q_type'] ?? [];
    $q_points = $_POST['q_points'] ?? [];
    $q_answer = $_POST['q_answer'] ?? [];
    $q_options = $_POST['q_options'] ?? [];

    $questions = [];
    for ($i=0;$i<count($q_texts);$i++){
        $qt = trim($q_texts[$i]);
        if ($qt === '') continue;
        $q = [
            'id' => uniqid('q_'),
            'text' => $qt,
            'type' => $q_type[$i] ?? 'mcq',
            'points' => intval($q_points[$i] ?? 1),
            'options' => [],
            'answer' => $q_answer[$i] ?? null
        ];
        if ($q['type'] === 'mcq') {
            $opts_raw = $q_options[$i] ?? '';
            if (is_array($opts_raw)) $opts = $opts_raw;
            else $opts = array_filter(array_map('trim', explode("\n", $opts_raw)));
            $q['options'] = array_values($opts);
        }
        $questions[] = $q;
    }
    if (!empty($questions)) $quiz['questions'] = $questions;
    save_quiz($quiz);
    header('Location: dashboard.php');
    exit;
}
?>
<h1>Éditer : <?=htmlspecialchars($quiz['title'])?></h1>
<p><a href="dashboard.php">Retour</a></p>
<form method="POST">
    <label>Titre: <input name="title" value="<?=htmlspecialchars($quiz['title'])?>"></label><br>
    <label>Description:<br><textarea name="description"><?=htmlspecialchars($quiz['description'] ?? '')?></textarea></label><br>
    <label>Statut:
        <select name="status">
            <option value="draft" <?=($quiz['status'] ?? '')==='draft'?'selected':''?>>En écriture</option>
            <option value="launched" <?=($quiz['status'] ?? '')==='launched'?'selected':''?>>Lancé</option>
            <option value="finished" <?=($quiz['status'] ?? '')==='finished'?'selected':''?>>Terminé</option>
        </select>
    </label><br>
    <label>Actif: <input type="checkbox" name="active" <?=($quiz['active'] ?? true)?'checked':''?>></label><br>

    <h3>Questions (remplace celles existantes si modifiées)</h3>
    <?php if (!empty($quiz['questions'])): foreach ($quiz['questions'] as $i=>$q): ?>
        <div>
            <h4>Question <?=($i+1)?></h4>
            <label>Texte: <input name="q_text[]" value="<?=htmlspecialchars($q['text'])?>"></label><br>
            <label>Type:
                <select name="q_type[]">
                    <option value="mcq" <?= $q['type']==='mcq'?'selected':''?>>QCM</option>
                    <option value="open" <?= $q['type']==='open'?'selected':''?>>Libre</option>
                </select>
            </label><br>
            <label>Points: <input name="q_points[]" type="number" value="<?=intval($q['points'] ?? 1)?>"></label><br>
            <label>Options (QCM) une par ligne:<br><textarea name="q_options[]"><?= $q['type']==='mcq' ? htmlspecialchars(implode("\n", $q['options'])) : '' ?></textarea></label><br>
            <label>Bonne réponse: <input name="q_answer[]" value="<?=htmlspecialchars($q['answer'] ?? '')?>"></label><br>
        </div>
    <?php endforeach; else: ?>
        <p>Aucune question encore.</p>
    <?php endif; ?>
    <button type="submit">Enregistrer</button>
</form>
