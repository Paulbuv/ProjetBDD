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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Gestion des concours de dessins</h1></header>
    <main style="max-width: 400px; margin: 50px auto; padding: 25px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2>Connexion</h2>
        
        <?php if($error): ?>
            <p style="color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px; border: 1px solid #f5c6cb;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form action="Login.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Identifiant</label>
                <input type="text" name="Login" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Mot de passe</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #ff8a65, #ffb74d); color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 1.1em;">Se connecter</button>
        </form>
    </main>
    <footer style="text-align: center; margin-top: 20px; color: #666;">
        <p>&copy; 2026 - ESEO - Projet BD_WEB</p>
    </footer>
</body>
</html>