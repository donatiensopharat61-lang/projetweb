<?php
// login.php
require_once 'functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = find_user_by_email($email);
    if (!$user || !password_verify($password, $user['password'])) {
        $errors[] = "Identifiants invalides.";
    } elseif (!($user['active'] ?? true)) {
        $errors[] = "Compte désactivé.";
    } else {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    }
}
require_once 'session_manager.php';
register_session($user['id']);
?>
<form method="POST">
    <h2>Connexion</h2>
    <?php if (!empty($errors)): foreach ($errors as $e): ?>
        <div style="color:red;"><?=htmlspecialchars($e)?></div>
    <?php endforeach; endif; ?>
    <label>Email: <input name="email" type="email" required></label><br>
    <label>Mot de passe: <input name="password" type="password" required></label><br>
    <button type="submit">Se connecter</button>
</form>
<p><a href="register.php">Créer un compte</a></p>


