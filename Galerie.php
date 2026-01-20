<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Galerie</title>
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
                <li><a href="Galerie.php" class="active">Galerie</a></li>
                <li><a href="Resultats.php">Résultats</a></li>
                <li><a href="Admin.php">Administration</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Galerie des dessins</h2>
        <p>Ici tu pourras afficher les dessins soumis pour chaque concours.</p>

        <section>
            <h3>Filtre par concours</h3>
            <p>(Menu déroulant pour choisir un concours et filtrer les dessins)</p>
        </section>

        <section>
            <h3>Vignettes des dessins</h3>
            <p>(Grille de vignettes avec aperçu des dessins)</p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
</body>
</html>

