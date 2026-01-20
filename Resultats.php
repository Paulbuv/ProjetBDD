<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Résultats</title>
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
                <li><a href="Resultats.php" class="active">Résultats</a></li>
                <li><a href="Admin.php">Administration</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Résultats des concours</h2>
        <p>Ici tu pourras gérer les classements, prix et publications des résultats.</p>

        <section>
            <h3>Sélection du concours</h3>
            <p>(Sélecteur pour choisir un concours et afficher ses résultats)</p>
        </section>

        <section>
            <h3>Podium</h3>
            <p>(Zone pour afficher les gagnants et leurs dessins)</p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
</body>
</html>

