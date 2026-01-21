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

    // On neutralise la casse/les variantes de l'état pour éviter de masquer les concours en cours
    $sql = "
        SELECT numConcours, numPresident, theme, dateDeb, dateFin, Etat, description
        FROM Concours
        WHERE LOWER(Etat) = 'en cours'
        ORDER BY dateDeb DESC
        LIMIT 4
    ";
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
        <section class="hero" data-anim>
            <div class="hero-content">
                <p class="hero-kicker">Plateforme des concours de dessin</p>
                <h2>Bienvenue sur <span>Zoom Dessin</span></h2>
                <p class="hero-text">
                    Organise, participe et fais rayonner la créativité. Découvre les concours en cours,
                    inspire-toi des œuvres de la communauté et partage tes talents.
                </p>
                <div class="hero-actions">
                    <a class="btn primary" href="Concours.php">Voir les concours</a>
                    <a class="btn ghost" href="Galerie.php">Explorer la galerie</a>
                </div>
            </div>
            <div class="hero-badge">
                <p class="badge-title">Concours en cours</p>
                <p class="badge-number"><?php echo count($concoursEnCours); ?></p>
                <p class="badge-subtitle">sélectionnés pour toi</p>
            </div>
            <div class="floating-shapes" aria-hidden="true">
                <span></span><span></span><span></span><span></span>
            </div>
        </section>

        <section class="info-grid" data-anim>
            <div class="info-card">
                <h3>Créer</h3>
                <p>Lance un nouveau concours avec tes règles, tes dates et ton jury.</p>
            </div>
            <div class="info-card">
                <h3>Participer</h3>
                <p>Inscris-toi en quelques clics et suis tes soumissions en temps réel.</p>
            </div>
            <div class="info-card">
                <h3>Célébrer</h3>
                <p>Mets en avant les gagnants, partage les galeries et inspire la communauté.</p>
            </div>
        </section>

        <section data-anim>
            <div class="section-head">
                <div>
                    <p class="kicker">À la une</p>
                    <h3>Concours en cours</h3>
                </div>
                <a class="link" href="Concours.php">Tous les concours →</a>
            </div>

            <?php if ($erreurConnexion): ?>
                <p class="error">Erreur de connexion à la base de données : <?php echo htmlspecialchars($erreurConnexion); ?></p>
            <?php elseif (empty($concoursEnCours)): ?>
                <p class="muted">Aucun concours en cours pour le moment.</p>
            <?php else: ?>
                <div class="concours-grid">
                    <?php foreach ($concoursEnCours as $concours): ?>
                        <article class="concours-card" data-tilt data-anim>
                            <div class="concours-card__header">
                                <span class="pill"><?php echo htmlspecialchars($concours['Etat']); ?></span>
                                <span class="pill pill-light">n°<?php echo htmlspecialchars($concours['numConcours']); ?></span>
                            </div>
                            <h4><?php echo htmlspecialchars($concours['theme']); ?></h4>
                            <p class="dates">
                                Du <?php echo date('d/m/Y', strtotime($concours['dateDeb'])); ?>
                                au <?php echo date('d/m/Y', strtotime($concours['dateFin'])); ?>
                            </p>
                            <p class="description">
                                <?php echo nl2br(htmlspecialchars($concours['description'])); ?>
                            </p>
                            <div class="card-actions">
                                <a class="link" href="Concours.php">Voir le concours</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="about" data-anim>
            <div class="about-text">
                <p class="kicker">Qui sommes-nous ?</p>
                <h3>Une équipe de 4 étudiants ingénieurs passionnés d’art</h3>
                <p>Nous sommes quatre étudiants de l’ESEO réunis par l’envie de mettre la technologie au service de la créativité. Nous imaginons, concevons et animons cette plateforme pour faciliter l’organisation de concours, valoriser les talents et rapprocher les artistes de toutes les générations.</p>
                <p>Notre ambition : offrir un espace simple, moderne et chaleureux pour que chacun puisse lancer un concours, partager ses œuvres et célébrer l’art sous toutes ses formes.</p>
            </div>
            <div class="about-media">
                <img src="https://www.eseo.fr/sites/default/files/styles/1920x900/public/2023-09/eseo-angers-campus.jpg" alt="Campus de l'ESEO" class="about-photo" loading="lazy">
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
    <script>
        // Accueil: animations d’apparition + tilt léger (sans librairies)
        document.addEventListener('DOMContentLoaded', () => {
            const animated = document.querySelectorAll('[data-anim]');

            // Apparition au scroll
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) entry.target.classList.add('is-visible');
                });
            }, { threshold: 0.18 });
            animated.forEach(el => io.observe(el));

            // Tilt doux sur cartes
            const prefersReduced =
                window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReduced) return;

            document.querySelectorAll('[data-tilt]').forEach(card => {
                const reset = () => { card.style.transform = ''; };
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    const rotateX = (y / rect.height) * -4;
                    const rotateY = (x / rect.width) * 4;
                    card.style.transform =
                        `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
                });
                card.addEventListener('mouseleave', reset);
                card.addEventListener('blur', reset);
            });
        });
    </script>
</body>
</html>

