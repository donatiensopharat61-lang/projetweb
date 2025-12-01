<?php
// admin.php
require_once 'functions.php';
require_role('admin');

$users = read_users();
$quizzes = list_quizzes();
$responses = read_responses();

// handle toggle user active or toggle quiz active via GET params
if (isset($_GET['toggle_user'])) {
    $uid = $_GET['toggle_user'];
    foreach ($users as $i => $u) {
        if ($u['id'] === $uid) {
            $users[$i]['active'] = !($u['active'] ?? true);
            write_users($users);
            header('Location: admin.php'); exit;
        }
    }
}

if (isset($_GET['toggle_quiz'])) {
    $qid = $_GET['toggle_quiz'];
    $q = get_quiz($qid);
    if ($q) {
        $q['active'] = !($q['active'] ?? true);
        save_quiz($q);
    }
    header('Location: admin.php'); exit;
}

?>
<h1>Admin Panel</h1>
<p><a href="dashboard.php">Retour</a></p>

<h2>Utilisateurs</h2>
<table border="1" cellpadding="4">
<tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Actif</th><th>Action</th></tr>
<?php foreach ($users as $u): ?>
<tr>
<td><?=htmlspecialchars($u['first_name'].' '.$u['last_name'])?></td>
<td><?=htmlspecialchars($u['email'])?></td>
<td><?=htmlspecialchars($u['role'])?></td>
<td><?=($u['active'] ?? true) ? 'Oui' : 'Non'?></td>
<td><a href="admin.php?toggle_user=<?=urlencode($u['id'])?>">Activer/Désactiver</a></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Quiz</h2>
<table border="1" cellpadding="4">
<tr><th>Titre</th><th>Propriétaire</th><th>Statut</th><th>Actif</th><th>Action</th></tr>
<?php foreach ($quizzes as $q): 
    $owner = find_user_by_id($q['owner_id'] ?? '');
?>
<tr>
<td><?=htmlspecialchars($q['title'])?></td>
<td><?= $owner ? htmlspecialchars($owner['first_name'].' '.$owner['last_name']) : '—' ?></td>
<td><?=htmlspecialchars($q['status'] ?? 'draft')?></td>
<td><?=($q['active'] ?? true) ? 'Oui' : 'Non'?></td>
<td><a href="admin.php?toggle_quiz=<?=urlencode($q['id'])?>">Activer/Désactiver</a></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Statistiques rapides</h2>
<p>Nombre d'utilisateurs: <?=count($users)?></p>
<p>Nombre de quiz: <?=count($quizzes)?></p>
<p>Nombre de réponses: <?=count($responses)?></p>
<?php
$sessions = read_sessions();
?>
<h3>Sessions actives</h3>
<table border="1" cellpadding="4">
<tr>
    <th>ID Session</th>
    <th>Utilisateur</th>
    <th>IP</th>
    <th>Agent</th>
    <th>Date connexion</th>
</tr>
<?php foreach ($sessions as $s):
    $u = find_user_by_id($s['user_id']);
?>
<tr>
    <td><?= htmlspecialchars($s['session_id']) ?></td>
    <td><?= $u ? htmlspecialchars($u['first_name'].' '.$u['last_name']) : "Inconnu" ?></td>
    <td><?= htmlspecialchars($s['ip']) ?></td>
    <td><?= htmlspecialchars($s['user_agent']) ?></td>
    <td><?= htmlspecialchars($s['connected_at']) ?></td>
</tr>
<?php endforeach; ?>
</table>


