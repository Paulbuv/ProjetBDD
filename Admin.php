<?php
session_start();

// ------------------------------------------------------------
// Vérification : seul l'utilisateur "admin" peut accéder à cette page
// ------------------------------------------------------------
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'admin') {
    header('Location: Login.php');
    exit();
}

// Connexion BDD
$dsn = 'mysql:host=localhost;dbname=Projet_BDD;charset=utf8mb4';
$dbUser = 'db_etu';
$dbPass = 'N3twork!';

$erreurConnexion = null;
$messageSucces = '';
$messageErreur = '';
$utilisateurs = [];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Récupération de la liste des utilisateurs pour le select
    $stmtUsers = $pdo->query("SELECT numUtilisateur, login FROM Utilisateur ORDER BY login ASC");
    $utilisateurs = $stmtUsers->fetchAll();

    // Traitement des formulaires
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'create_user') {
            $loginNew = trim($_POST['login_new'] ?? '');
            $passwordNew = $_POST['password_new'] ?? '';
            $nomNew = trim($_POST['nom_new'] ?? '');
            $prenomNew = trim($_POST['prenom_new'] ?? '');

            if ($loginNew === '' || $passwordNew === '') {
                $messageErreur = "Le login et le mot de passe sont obligatoires pour créer un compte.";
            } else {
                // Vérifier si le login existe déjà
                $stmtCheck = $pdo->prepare("SELECT 1 FROM Utilisateur WHERE login = ?");
                $stmtCheck->execute([$loginNew]);
                if ($stmtCheck->fetch()) {
                    $messageErreur = "Ce login existe déjà.";
                } else {
                    $stmtInsert = $pdo->prepare("
                        INSERT INTO Utilisateur (login, motDePasse, nom, prenom)
                        VALUES (:login, :mdp, :nom, :prenom)
                    ");
                    $stmtInsert->execute([
                        ':login' => $loginNew,
                        ':mdp' => $passwordNew, // texte clair pour rester cohérent avec Login.php
                        ':nom' => $nomNew,
                        ':prenom' => $prenomNew,
                    ]);
                    $messageSucces = "Compte créé avec succès pour l'utilisateur « " . htmlspecialchars($loginNew, ENT_QUOTES, 'UTF-8') . " ».";
                }
            }
        } elseif ($action === 'change_password') {
            $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
            $newPassword = $_POST['new_password'] ?? '';

            if ($userId <= 0 || $newPassword === '') {
                $messageErreur = "Veuillez sélectionner un utilisateur et saisir un nouveau mot de passe.";
            } else {
                $stmtUpdate = $pdo->prepare("UPDATE Utilisateur SET motDePasse = :mdp WHERE numUtilisateur = :id");
                $stmtUpdate->execute([
                    ':mdp' => $newPassword, // texte clair comme dans Login.php
                    ':id' => $userId,
                ]);
                $messageSucces = "Mot de passe mis à jour avec succès.";
            }
        }
    }
} catch (PDOException $e) {
    $erreurConnexion = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Administration</title>
    <link rel="stylesheet" href="./CSS.css">
</head>
<body>
    <header>
        <h1>Gestion des concours de dessins</h1>
        <img src="images/logo/logo_dessin.png"
             alt="Logo"
             class="header-logo"
        >
        <nav>
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="Concours.php">Concours</a></li>
                <li><a href="Participants.php">Participants</a></li>
                <li><a href="Galerie.php">Galerie</a></li>
                <li><a href="Dessiner.php">Dessiner</a></li>
                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === 'admin'): ?>
                    <li><a href="Admin.php" class="active">Administration</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['login'])): ?>
                    <li><a href="Logout.php">Se déconnecter de <?php echo htmlspecialchars($_SESSION['login']); ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Administration</h2>
        <p>Zone réservée à la configuration générale des concours.</p>

        <?php if (!empty($erreurConnexion)): ?>
            <p class="error">Erreur de connexion à la base de données : <?= htmlspecialchars($erreurConnexion, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if (!empty($messageSucces)): ?>
            <p class="success-message">
                <?= $messageSucces ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($messageErreur)): ?>
            <p class="error-message">
                <?= $messageErreur ?>
            </p>
        <?php endif; ?>

        <section>
            <h3>Paramètres généraux</h3>
            <p>(Configuration des dates, thèmes, limites de participation, etc.)</p>
        </section>

        <section>
            <h3>Créer un nouveau compte utilisateur</h3>
            <form method="post">
                <input type="hidden" name="action" value="create_user">

                <label for="login_new">Login *</label>
                <input type="text" id="login_new" name="login_new" required>

                <label for="password_new">Mot de passe *</label>
                <input type="password" id="password_new" name="password_new" required>

                <label for="nom_new">Nom</label>
                <input type="text" id="nom_new" name="nom_new">

                <label for="prenom_new">Prénom</label>
                <input type="text" id="prenom_new" name="prenom_new">

                <button type="submit">Créer le compte</button>
            </form>
        </section>

        <section>
            <h3>Modifier le mot de passe d'un compte</h3>
            <form method="post">
                <input type="hidden" name="action" value="change_password">

                <label for="user_id">Utilisateur</label>
                <select id="user_id" name="user_id" required>
                    <option value="">-- Choisir un utilisateur --</option>
                    <?php foreach ($utilisateurs as $u): ?>
                        <option value="<?= (int)$u['numUtilisateur'] ?>">
                            <?= htmlspecialchars($u['login'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" required>

                <button type="submit">Mettre à jour le mot de passe</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
</body>
</html>

