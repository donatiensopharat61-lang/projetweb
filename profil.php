<?php
// profile.php
require_once 'functions.php';
$user = require_login();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? $user['first_name']);
    $last = trim($_POST['last_name'] ?? $user['last_name']);
    $email = trim($_POST['email'] ?? $user['email']);
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";

    if (empty($errors)) {
        $user['first_name'] = $first;
        $user['last_name'] = $last;
        $user['email'] = $email;
        if (!empty($password)) $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        save_user($user);
        header('Location: profile.php');
        exit;
    }
}
?>
<h1>Profil</h1>
<p><a href="dashboard.php">Retour</a></p>
<?php if (!empty($errors)): foreach ($errors as $e): ?>
    <div style="color:red;"><?=htmlspecialchars($e)?></div>
<?php endforeach; endif; ?>
<form method="POST">
    <label>Prénom: <input name="first_name" value="<?=htmlspecialchars($user['first_name'])?>"></label><br>
    <label>Nom: <input name="last_name" value="<?=htmlspecialchars($user['last_name'])?>"></label><br>
    <label>Email: <input name="email" type="email" value="<?=htmlspecialchars($user['email'])?>"></label><br>
    <label>Nouveau mot de passe (laisser vide pour conserver): <input name="password" type="password"></label><br>
    <button type="submit">Enregistrer</button>
</form>
