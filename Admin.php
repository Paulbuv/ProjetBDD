<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Administration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Gestion des concours de dessins</h1>
        <img src="images/logo/logo_dessin.png" alt="Logo" class="header-logo">
        <nav>
            <ul>
                <li><a href="Accueil.php">Accueil</a></li>
                <li><a href="Concours.php">Concours</a></li>
                <li><a href="Participants.php">Participants</a></li>
                <li><a href="Galerie.php">Galerie</a></li>
                <li><a href="Resultats.php">Résultats</a></li>
                <li><a href="Admin.php" class="active">Administration</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Administration</h2>
        <p>Zone réservée à la configuration générale des concours.</p>

        <section>
            <h3>Paramètres généraux</h3>
            <p>(Configuration des dates, thèmes, limites de participation, etc.)</p>
        </section>

        <section>
            <h3>Gestion des comptes</h3>
            <p>(Gestion des comptes organisateurs / jurés, droits d’accès, etc.)</p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
</body>
</html>

