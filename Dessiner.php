<?php
session_start();
 
// 1. Protection de la page : l'utilisateur doit être connecté [cite: 102, 109]
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}
 
// 2. Configuration de la connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=Projet_BDD;charset=utf8mb4';
$dbUser = 'db_etu';
$dbPass = 'N3twork!';
 
$concoursEnCours = [];
$erreur = null;
 
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5,
    ]);
 
    // Récupération des concours dont l'état est 'en cours' [cite: 107, 135]
    // Seuls ces concours autorisent le dépôt de dessins.
    $sql = "SELECT numConcours, theme FROM Concours WHERE etat = 'en cours' ORDER BY dateDeb DESC";
    $stmt = $pdo->query($sql);
    $concoursEnCours = $stmt->fetchAll();
 
} catch (PDOException $e) {
    $erreur = "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>
 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atelier Dessin - Concours ESEO</title>
    <link rel="stylesheet" href="CSS.css">
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
                <li><a href="Dessiner.php" class="active">Dessiner</a></li>
                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === 'admin'): ?>
                    <li><a href="Admin.php">Administration</a></li>
                <?php endif; ?>
                <li><a href="Logout.php">Se déconnecter de <?php echo htmlspecialchars($_SESSION['login']); ?></a></li>
                </ul>
        </nav>
    </header>
 
    <main>
        <section class="hero hero-spacing">
            <div class="hero-content">
                <span class="hero-kicker">Zoom Dessin</span>
                <h2>Exprime ta <span>Créativité !</span></h2>
                <p class="hero-text">
                    Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></strong> !<br>
                    Utilise les outils pour créer ton dessin. Chaque compétiteur peut soumettre jusqu'à 3 dessins par concours.
                </p>
            </div>
        </section>
 
        <?php if ($erreur): ?>
            <p class="error-message"><?php echo $erreur; ?></p>
        <?php endif; ?>
 
        <div class="drawing-area">
            <div class="toolbar-draw">
                <div class="tool-group">
                    <button id="brushBtn" class="btn primary" title="Pinceau">🖌️</button>
                    <button id="eraserBtn" class="btn ghost" title="Gomme">🧽</button>
                    <button id="rectBtn" class="btn ghost" title="Rectangle">⬜</button>
                </div>
 
                <div class="tool-group">
                    <label>Couleur :</label>
                    <input type="color" id="color" value="#ff8a65">
                </div>
                
                <div class="tool-group">
                    <label>Taille :</label>
                    <input type="range" id="size" min="1" max="50" value="5">
                </div>
 
                <div class="tool-group">
                    <label>Opacité :</label>
                    <input type="range" id="opacity" min="0.1" max="1.0" step="0.1" value="1.0">
                </div>
 
                <div class="hero-actions">
                    <button id="undo" class="btn ghost">↩️ Annuler</button>
                    <button id="clear" class="btn ghost">🗑️ Effacer</button>
                    <button id="save" class="btn primary">💾 Télécharger</button>
                </div>
            </div>
 
            <div class="canvas-container">
                <canvas id="canvas" width="800" height="500"></canvas>
            </div>
        </div>
    </main>
 
    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Projet ESEO - Gestion des concours de dessins</p>
    </footer>
 
    <script src="logique-dessin.js"></script>
 
</body>
</html>