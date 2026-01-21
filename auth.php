<?php
// Démarrage de la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur n'est pas connecté, on le renvoie vers la page de login
if (empty($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

