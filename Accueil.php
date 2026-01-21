<?php
// Connexion à la base de données (identique à Concours.php)
$dsn = 'mysql:host=localhost;dbname=Projet_BDD;charset=utf8mb4';
$dbUser = 'db_etu';
$dbPass = 'N3twork!';
$erreurConnexion = null;
$concoursEnCours = [];
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 3,
    ]);

    // Requête adaptée à la table Concours pour les concours en cours (même logique que Concours.php)
    $sql = "SELECT numConcours, numPresident, theme, dateDeb, dateFin, Etat, description FROM Concours WHERE Etat = 'en cours' ORDER BY dateDeb DESC LIMIT 4";
    $stmt = $pdo->query($sql);
    $concoursEnCours = $stmt->fetchAll();
} catch (PDOException $e) {
    $erreurConnexion = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Accueil</title>
    <link rel="stylesheet" href="./CSS.css">
</head>
<body>
    <header>
        <h1>Gestion des concours de dessins</h1>
        <img src="images/logo/logo_dessin.png" alt="Logo" class="header-logo">
        <nav>
            <ul>
                <li><a href="Accueil.php" class="active">Accueil</a></li>
                <li><a href="Concours.php">Concours</a></li>
                <li><a href="Participants.php">Participants</a></li>
                <li><a href="Galerie.php">Galerie</a></li>
                <li><a href="Admin.php">Administration</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section style="text-align:center; padding: 40px 10px 30px 10px; background: linear-gradient(135deg, #ffb74d 60%, #fff7ed 100%); border-radius: 12px; margin-bottom: 30px;">
            <h2 style="font-size:2.3em; margin-bottom: 0.2em; color:#ff8a65;">Bienvenue sur <span style="font-weight:bold; color:#333;">Zoom Dessin</span></h2>
            <p style="font-size:1.2em; max-width:700px; margin: 0 auto 1.2em auto; color:#444;">La plateforme moderne pour organiser, participer et célébrer la créativité à travers des concours de dessins ouverts à tous. Découvrez les concours en cours, explorez les œuvres, et rejoignez la communauté !</p>
        </section>
        <section>
            <h3 style="font-size:1.4em; color:#ff8a65; margin-bottom:0.7em;">Concours en cours</h3>
            <?php if ($erreurConnexion): ?>
                <p style="color: red;">Erreur de connexion à la base de données : <?php echo htmlspecialchars($erreurConnexion); ?></p>
            <?php elseif (empty($concoursEnCours)): ?>
                <p>Aucun concours en cours pour le moment.</p>
            <?php else: ?>
                <div style="display: flex; flex-wrap: wrap; gap: 24px; justify-content: center;">
                    <?php foreach ($concoursEnCours as $concours): ?>
                        <div style="flex:1 1 220px; min-width:220px; max-width:320px; background: #fff7ed; border:1px solid #ffe0b2; border-radius:10px; box-shadow:0 2px 8px rgba(255,138,101,0.07); padding:18px 16px 14px 16px; margin-bottom:10px;">
                            <h4 style="margin:0 0 0.5em 0; color:#ff8a65; font-size:1.15em;">Thème : <?php echo htmlspecialchars($concours['theme']); ?></h4>
                            <div style="font-size:0.98em; color:#555; margin-bottom:0.5em;">
                                <span><b>Du</b> <?php echo date('d/m/Y', strtotime($concours['dateDeb'])); ?></span> <b>au</b> <span><?php echo date('d/m/Y', strtotime($concours['dateFin'])); ?></span>
                            </div>
                            <div style="font-size:0.97em; color:#333; min-height:48px; margin-bottom:0.5em;">
                                <?php echo nl2br(htmlspecialchars($concours['description'])); ?>
                            </div>
                            <div style="text-align:right;">
                                <a href="Concours.php" style="color:#ff8a65; text-decoration:underline; font-weight:600;">Voir le concours</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <section>
            <h3>Ce que tu peux faire ici</h3>
            <ul>
                <li>Créer et planifier de nouveaux concours de dessins.</li>
                <li>Enregistrer et suivre les participants.</li>
                <li>Afficher les dessins soumis dans une galerie.</li>
                <li>Publier les résultats et les gagnants.</li>
            </ul>
        </section>
        <section>
            <h3>Navigation rapide</h3>
            <p>Utilise les onglets du haut pour accéder directement aux différentes parties de l’application.</p>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
</body>
</html>

