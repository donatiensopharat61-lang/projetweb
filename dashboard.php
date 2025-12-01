<?php
// dashboard.php
require_once 'functions.php';
$user = require_login();

$role = $user['role'];

// list quizzes created by this user (owner_id)
$all = list_quizzes();
$mine = array_filter($all, function($q) use ($user) {
    return ($q['owner_id'] ?? '') === $user['id'];
});

?>
<h1>Dashboard - <?=htmlspecialchars($user['first_name'].' '.$user['last_name'])?></h1>
<p>Rôle: <?=htmlspecialchars($role)?></p>
<p><a href="create_quiz.php">Créer un nouveau quiz</a> | <a href="profile.php">Profil</a> | <a href="logout.php">Se déconnecter</a></p>

<h2>Mes quiz</h2>
<?php if (empty($mine)): ?>
    <p>Aucun quiz créé.</p>
<?php else: ?>
    <ul>
    <?php foreach ($mine as $q): ?>
        <li>
            <?=htmlspecialchars($q['title'])?> -
            Statut: <?=htmlspecialchars($q['status'] ?? 'draft')?> -
            <a href="edit_quiz.php?id=<?=urlencode($q['id'])?>">Éditer</a> |
            <a href="take_quiz.php?id=<?=urlencode($q['id'])?>">Voir / tester</a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($role === 'admin'): ?>
    <p><a href="admin.php">Accéder au panel admin</a></p>
<?php endif; ?>
