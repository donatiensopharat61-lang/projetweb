<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
$role = $user['role'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzeo - Accueil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
 
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
 
        .welcome-message {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
 
        .quizzeo-container {
            display: flex;
            gap: 2px;
            margin-bottom: 30px;
        }
 
        .letter {
            font-size: 120px;
            font-weight: bold;
            height: 150px;
            width: 120px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }
 
        .letter::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-repeat: repeat;
            z-index: 0;
        }
 
        .letter-Q {
            background-color: #9c27b0;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 1px, transparent 1px);
            background-size: 15px 15px;
        }
 
        .letter-U {
            background-color: #e91e63;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 2px, transparent 2px);
            background-size: 20px 20px;
        }
 
        .letter-I {
            background-color: #e91e63;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 2px, transparent 2px);
            background-size: 20px 20px;
        }
 
        .letter-Z-1, .letter-Z-2 {
            background-color: #e91e63;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 2px, transparent 2px);
            background-size: 20px 20px;
        }
 
        .letter-E {
            background-color: #ff9800;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 3px, transparent 3px);
            background-size: 25px 25px;
        }
 
        .letter-O {
            background-color: #ffc107;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 3px, transparent 3px);
            background-size: 25px 25px;
        }
 
        .content {
            text-align: center;
            margin-top: 30px;
        }
 
        .content p {
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="welcome-message">
            Bienvenue, <?php echo htmlspecialchars($user['name']); ?> (Rôle: <?php echo htmlspecialchars($role); ?>) !
        </div>
    </div>
 
    <div class="quizzeo-container">
        <div class="letter letter-Q">Q</div>
        <div class="letter letter-U">U</div>
        <div class="letter letter-I">I</div>
        <div class="letter letter-Z-1">Z</div>
        <div class="letter letter-Z-2">Z</div>
        <div class="letter letter-E">E</div>
        <div class="letter letter-O">O</div>
    </div>
 
    <div class="content">
        <p>Vous êtes connecté à votre espace Quizzeo. Utilisez le menu pour naviguer.</p>
    </div>
</body>
</html>