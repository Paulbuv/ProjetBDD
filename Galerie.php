<?php
// ------------------------------------------------------------
// Récupération dynamique de la liste des concours depuis la BDD
// ------------------------------------------------------------
$dsn = 'mysql:host=localhost;dbname=Projet_BDD;charset=utf8mb4';
$dbUser = 'db_etu';
$dbPass = 'N3twork!';

$concours = [];
$erreurConnexion = null;
$topDessins = [];
$allDessins = [];
$showAll = isset($_GET['showAll']) && $_GET['showAll'] === '1';

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 3,
    ]);

    // On récupère les numéros et les thèmes des concours
    $sql = "SELECT numConcours, theme 
            FROM Concours
            ORDER BY dateDeb DESC";
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch()) {
        $id = (int)$row['numConcours'];
        $nom = $row['theme'];
        $concours[$id] = $nom;
    }

    // Si un concours est sélectionné, récupérer les dessins en BDD
    $selectedConcours = isset($_GET['concours']) ? intval($_GET['concours']) : 0;
    if ($selectedConcours > 0) {
        // 3 premiers pour le podium
        $sqlTop = "
            SELECT 
                d.numDessin,
                d.classement,
                u.nom,
                u.prenom
            FROM Dessin d
            JOIN Competiteur c ON c.numCompetiteur = d.numCompetiteur
            JOIN Utilisateur u ON u.numUtilisateur = c.numCompetiteur
            WHERE d.numConcours = :numConcours
              AND d.classement IS NOT NULL
            ORDER BY d.classement ASC
            LIMIT 3
        ";
        $stmtTop = $pdo->prepare($sqlTop);
        $stmtTop->execute([':numConcours' => $selectedConcours]);
        $topDessins = $stmtTop->fetchAll();

        // Tous les dessins du concours (pour "Voir plus")
        if ($showAll) {
            $sqlAll = "
                SELECT 
                    d.numDessin,
                    d.classement,
                    u.nom,
                    u.prenom
                FROM Dessin d
                JOIN Competiteur c ON c.numCompetiteur = d.numCompetiteur
                JOIN Utilisateur u ON u.numUtilisateur = c.numCompetiteur
                WHERE d.numConcours = :numConcours
                ORDER BY 
                    CASE WHEN d.classement IS NULL THEN 1 ELSE 0 END,
                    d.classement,
                    d.numDessin
            ";
            $stmtAll = $pdo->prepare($sqlAll);
            $stmtAll->execute([':numConcours' => $selectedConcours]);
            $allDessins = $stmtAll->fetchAll();
        }
    }
} catch (PDOException $e) {
    // En cas d'erreur, on garde un tableau vide et on stocke le message
    $concours = [];
    $erreurConnexion = $e->getMessage();
}
 
// Si la connexion a échoué, on récupère quand même le concours sélectionné pour le formulaire
if (!isset($selectedConcours)) {
    $selectedConcours = isset($_GET['concours']) ? intval($_GET['concours']) : 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Résultats</title>
    <?php
    // Construction du chemin vers CSS.css (chemin relatif depuis le répertoire du script)
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/') {
        $cssPath = '/CSS.css';
    } else {
        $cssPath = $basePath . '/CSS.css';
    }
    ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($cssPath) ?>">
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
            <li><a href="Admin.php">Administration</a></li>
        </ul>
    </nav>
</header>
 
<main>
    <h2>Résultats des concours</h2>
    <p>Ici tu peux gérer les classements, prix et publications des résultats.</p>
 
    <section>
        <h3>Sélection du concours</h3>
        <form method="get">
            <select name="concours" onchange="this.form.submit()">
                <option value="0">-- Choisir un concours --</option>
                <?php foreach ($concours as $id => $nom): ?>
                    <option value="<?= $id ?>" <?= ($id === $selectedConcours) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nom) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </section>
 
    <section>
        <h3>Podium</h3>

        <?php if ($selectedConcours === 0): ?>
            <p>Veuillez sélectionner un concours.</p>
        <?php elseif (!empty($erreurConnexion)): ?>
            <p style="color:red;">
                Erreur de connexion à la base de données :
                <?= htmlspecialchars($erreurConnexion, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php else: ?>
            <?php if (empty($topDessins)): ?>
                <p>Aucun classement trouvé pour ce concours.</p>
            <?php else: ?>
                <div class="podium">
                    <?php foreach ($topDessins as $dessin): ?>
                        <?php
                            $rang = isset($dessin['classement']) ? (int)$dessin['classement'] : 0;
                            $nom = $dessin['nom'] ?? '';
                            $prenom = $dessin['prenom'] ?? '';
                            $titre = trim($prenom . ' ' . $nom);
                            if ($titre === '') {
                                $titre = 'Participant inconnu';
                            }
                            $medal = ($rang === 1) ? "🥇" : (($rang === 2) ? "🥈" : (($rang === 3) ? "🥉" : "🏅"));

                            // Construction du chemin de l'image : concoursX_dessinY.jpg
                            // Exemple : concours1_dessin3.jpg
                            $numDessin = isset($dessin['numDessin']) ? (int)$dessin['numDessin'] : 0;
                            $imagePath = '';
                            if ($selectedConcours > 0 && $numDessin > 0) {
                                $imagePath = "dessins/concours" . $selectedConcours . "_dessin" . $numDessin . ".jpg";
                            }
                        ?>
                        <div class="podium-card" data-zoom>
                            <div class="podium-image-wrapper">
                                <?php if (!empty($imagePath)): ?>
                                    <img src="<?= htmlspecialchars($imagePath) ?>"
                                         alt="Dessin du participant <?= htmlspecialchars($titre) ?>"
                                         class="podium-img"
                                         loading="lazy"
                                         data-full="<?= htmlspecialchars($imagePath) ?>"
                                    >
                                <?php endif; ?>
                                <div class="podium-caption">
                                    <span class="podium-rank-text"><?= $medal ?> Rang <?= htmlspecialchars((string)$rang) ?></span>
                                    <span class="podium-name-text"><?= htmlspecialchars($titre) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; <?= date('Y'); ?> - Gestion des concours de dessins</p>
</footer>

<script>
// Galerie : zoom plein écran sur les dessins du podium
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.podium-card[data-zoom] img.podium-img');
    if (!cards.length) return;

    const overlay = document.createElement('div');
    overlay.className = 'zoom-overlay';
    overlay.innerHTML = `
        <div class="zoom-overlay__backdrop"></div>
        <div class="zoom-overlay__content" role="dialog" aria-modal="true">
            <button type="button" class="zoom-overlay__close" aria-label="Fermer l’aperçu">×</button>
            <img src="" alt="Dessin en grand format" class="zoom-overlay__image">
        </div>
    `;
    document.body.appendChild(overlay);

    const imgTarget = overlay.querySelector('.zoom-overlay__image');
    const closeBtn = overlay.querySelector('.zoom-overlay__close');
    const backdrop = overlay.querySelector('.zoom-overlay__backdrop');

    const open = (src) => {
        imgTarget.src = src;
        overlay.classList.add('is-open');
    };
    const close = () => {
        overlay.classList.remove('is-open');
        imgTarget.src = '';
    };

    cards.forEach(img => {
        img.addEventListener('click', () => {
            const src = img.dataset.full || img.src;
            open(src);
        });
    });

    closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
});
</script>

</body>
</html>