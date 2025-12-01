<?php
// create_quiz.php
require_once 'functions.php';
$user = require_login();
$role = $user['role'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'draft'; // draft, launched, finished
    $active = isset($_POST['active']) ? true : false;

    // Questions come as arrays
    $questions = [];
    // expected posted fields: q_text[], q_type[], q_points[], q_options[][], q_answer[]
    $q_texts = $_POST['q_text'] ?? [];
    $q_type = $_POST['q_type'] ?? [];
    $q_points = $_POST['q_points'] ?? [];
    $q_answer = $_POST['q_answer'] ?? [];
    $q_options = $_POST['q_options'] ?? []; // q_options is array of arrays (string separated or JSON)

    for ($i=0;$i<count($q_texts);$i++) {
        $qt = trim($q_texts[$i]);
        if ($qt === '') continue;
        $q = [
            'id' => uniqid('q_'),
            'text' => $qt,
            'type' => $q_type[$i] ?? 'mcq', // mcq or open
            'points' => intval($q_points[$i] ?? 1),
            'options' => [],
            'answer' => $q_answer[$i] ?? null
        ];
        if ($q['type'] === 'mcq') {
            // options as newline separated or array
            $opts_raw = $q_options[$i] ?? '';
            if (is_array($opts_raw)) $opts = $opts_raw;
            else $opts = array_filter(array_map('trim', explode("\n", $opts_raw)));
            $q['options'] = array_values($opts);
        }
        $questions[] = $q;
    }

    if (!$title) $errors[] = "Titre requis.";

    if (empty($errors)) {
        $quiz = [
            'id' => uniqid('quiz_'),
            'title' => $title,
            'description' => $desc,
            'owner_id' => $user['id'],
            'status' => $status,
            'active' => $active,
            'created_at' => date('c'),
            'questions' => $questions
        ];
        save_quiz($quiz);
        header('Location: dashboard.php');
        exit;
    }
}
?>
<h1>Créer un quiz</h1>
<p><a href="dashboard.php">Retour</a></p>

<form method="POST">
    <label>Titre: <input name="title" required></label><br>
    <label>Description:<br><textarea name="description"></textarea></label><br>
    <label>Statut:
        <select name="status">
            <option value="draft">En écriture</option>
            <option value="launched">Lancé</option>
            <option value="finished">Terminé</option>
        </select>
    </label><br>
    <label>Actif: <input type="checkbox" name="active" checked></label><br>

    <h3>Questions (exemple simple)</h3>
    <p>Pour ajouter plusieurs questions, dupliquer les champs côté UI. Ici on propose 2 emplacements par défaut.</p>
    <div>
        <h4>Question 1</h4>
        <label>Texte: <input name="q_text[]"></label><br>
        <label>Type:
            <select name="q_type[]">
                <option value="mcq">QCM</option>
                <option value="open">Libre</option>
            </select>
        </label><br>
        <label>Points: <input name="q_points[]" type="number" value="1"></label><br>
        <label>Options (pour QCM, une par ligne):<br><textarea name="q_options[]"></textarea></label><br>
        <label>Bonne réponse (index ou texte): <input name="q_answer[]"></label><br>
    </div>

    <div>
        <h4>Question 2</h4>
        <label>Texte: <input name="q_text[]"></label><br>
        <label>Type:
            <select name="q_type[]">
                <option value="mcq">QCM</option>
                <option value="open">Libre</option>
            </select>
        </label><br>
        <label>Points: <input name="q_points[]" type="number" value="1"></label><br>
        <label>Options (pour QCM, une par ligne):<br><textarea name="q_options[]"></textarea></label><br>
        <label>Bonne réponse (index ou texte): <input name="q_answer[]"></label><br>
    </div>

    <button type="submit">Enregistrer</button>
</form>
