<?php
// register.php
require_once 'functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $role = $_POST['role'] ?? 'user'; // 'school' or 'company' or 'user'
    $captcha = $_POST['captcha'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (strlen($password) < 6) $errors[] = "Mot de passe trop court (>=6).";
    if (empty($first) || empty($last)) $errors[] = "Nom et prénom requis.";
    if (!verify_captcha($captcha)) $errors[] = "Captcha incorrect.";

    if (find_user_by_email($email)) $errors[] = "Email déjà utilisé.";

    if (empty($errors)) {
        $user = [
            'id' => uniqid('user_'),
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'first_name' => $first,
            'last_name' => $last,
            'role' => $role === 'school' ? 'school' : ($role === 'company' ? 'company' : 'user'),
            'active' => true,
            'created_at' => date('c')
        ];
        save_user($user);
        // auto-login after register
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    }
}

$captcha_question = generate_captcha();
?>
<!-- minimal HTML form -->
<form method="POST">
    <h2>Créer un compte</h2>
    <?php if (!empty($errors)): foreach ($errors as $e): ?>
        <div style="color:red;"><?=htmlspecialchars($e)?></div>
    <?php endforeach; endif; ?>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Prénom: <input name="first_name" required></label><br>
    <label>Nom: <input name="last_name" required></label><br>
    <label>Rôle:
        <select name="role">
            <option value="user">Utilisateur</option>
            <option value="school">École</option>
            <option value="company">Entreprise</option>
        </select>
    </label><br>
    <label>Mot de passe: <input type="password" name="password" required></label><br>
    <label>Captcha: <?= $captcha_question ?> <input name="captcha" required></label><br>
    <button type="submit">S'inscrire</button>
</form>
