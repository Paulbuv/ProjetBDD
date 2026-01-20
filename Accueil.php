<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Gestion des concours de dessins</h1>
        <nav>
            <ul>
                <li><a href="Accueil.php" class="active">Accueil</a></li>
                <li><a href="Concours.php">Concours</a></li>
                <li><a href="Participants.php">Participants</a></li>
                <li><a href="Galerie.php">Galerie</a></li>
                <li><a href="Resultats.php">Résultats</a></li>
                <li><a href="Admin.php">Administration</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Bienvenue sur la plateforme de concours de dessins</h2>
        <p>
            Cette interface te permet de gérer facilement des concours de dessins :
            création de concours, gestion des participants, galerie des œuvres et résultats.
        </p>

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

