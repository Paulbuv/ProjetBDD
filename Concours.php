<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['login'])) {
    header('Location: Login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Concours</title>
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
                <li><a href="Concours.php" class="active">Concours</a></li>
                <li><a href="Participants.php">Participants</a></li>
                <li><a href="Galerie.php">Galerie</a></li>
                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === 'admin'): ?>
                    <li><a href="Admin.php">Administration</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['login'])): ?>
                    <li><a href="Logout.php">Se déconnecter de <?php echo htmlspecialchars($_SESSION['login']); ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Liste des concours</h2>
        <p>Ici tu pourras afficher, créer, modifier ou supprimer des concours de dessins.</p>

        <?php
        // ------------------------------------------------------------
        // Connexion PDO (même machine que MySQL => on utilise localhost)
        // ------------------------------------------------------------
        $dsn = 'mysql:host=localhost;dbname=Projet_BDD;charset=utf8mb4';
        $dbUser = 'db_etu';
        $dbPass = 'N3twork!';

        $erreurConnexion = null;

        try {
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 3,
            ]);

            // Requête adaptée à ta table Concours
            $sql = "SELECT numConcours, numPresident, theme, dateDeb, dateFin, Etat, description
                    FROM Concours
                    ORDER BY dateDeb DESC";
            $stmt = $pdo->query($sql);
            $concours = $stmt->fetchAll();
        } catch (PDOException $e) {
            $concours = [];
            $erreurConnexion = $e->getMessage();
        }
        ?>

        <section>
            <h3>Tous les concours</h3>

            <?php if (!empty($erreurConnexion)): ?>
                <p style="color: red;">Erreur de connexion à la base de données : 
                    <?php echo htmlspecialchars($erreurConnexion, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($concours)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Thème</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>État</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($concours as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['numConcours']); ?></td>
                                <td><?php echo htmlspecialchars($c['theme']); ?></td>
                                <td><?php echo htmlspecialchars($c['dateDeb']); ?></td>
                                <td><?php echo htmlspecialchars($c['dateFin']); ?></td>
                                <td><?php echo htmlspecialchars($c['Etat']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($c['description'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun concours trouvé dans la base de données.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
</body>
</html>

