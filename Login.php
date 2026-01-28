<?php
session_start();

// 1. Configuration de la connexion à la VM
$dsn = 'mysql:host=localhost;dbname=Projet_BDD;charset=utf8mb4';
$user_db = 'db_etu';
$pass_db = 'N3twork!';
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Le champ du formulaire s'appelle "Login" (majuscule),
    // on doit donc lire $_POST['Login'] et pas $_POST['login']
    $login = isset($_POST['Login']) ? trim($_POST['Login']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    try {
        $pdo = new PDO($dsn, $user_db, $pass_db, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // 2. Recherche de l'utilisateur selon le schéma UML [cite: 115, 119]
        $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Vérification du mot de passe en texte clair 
        if ($user && $password === $user['motDePasse']) {
            // Stockage des informations en session [cite: 36, 116, 117]
            $_SESSION['user_id'] = $user['numUtilisateur'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom']; 

            // REDIRECTION vers la page d'accueil
            header("Location: ./Accueil.php");
            exit(); 
        } else {
            $error = "Identifiant ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error = "Erreur de base de données : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Concours de Dessin</title>
    <link rel="stylesheet" href="./CSS.css">
</head>
<body>
    <header>
        <h1>Gestion des concours de dessins</h1>
        <img src="images/logo/logo_dessin.png"
             alt="Logo"
             class="header-logo"
        >
        </header>
    <main class="login-main">
        <h2>Connexion</h2>
        
        <?php if($error): ?>
            <p class="login-error">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form action="Login.php" method="POST">
            <div class="login-form-group">
                <label class="login-label">Identifiant</label>
                <input type="text" name="Login" required class="login-input">
            </div>
            <div class="login-form-group last">
                <label class="login-label">Mot de passe</label>
                <input type="password" name="password" required class="login-input">
            </div>
            <button type="submit" class="login-submit">Se connecter</button>
        </form>
    </main>
    <footer class="login-footer">
        <p>&copy; 2026 - ESEO - Projet BD_WEB</p>
    </footer>
</body>
</html>