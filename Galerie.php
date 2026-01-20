<?php
$concours = [
    1 => "Concours 1",
    2 => "Concours 2",
    3 => "Concours 3"
];
 
$selectedConcours = isset($_GET['concours']) ? intval($_GET['concours']) : 0;
 
// Fichier résultats JSON
$resultsDir = __DIR__ . "/uploads/resultats/";
$resultsFile = $selectedConcours > 0 ? $resultsDir . "concours" . $selectedConcours . ".json" : null;
 
// Lecture du JSON
$podium = [];
$error = "";
 
if ($selectedConcours > 0) {
    if (file_exists($resultsFile)) {
        $json = file_get_contents($resultsFile);
        $data = json_decode($json, true);
 
        if (json_last_error() === JSON_ERROR_NONE && isset($data["podium"]) && is_array($data["podium"])) {
            $podium = $data["podium"];
        } else {
            $error = "Fichier de résultats invalide (JSON).";
        }
    } else {
        $error = "Aucun résultat enregistré pour ce concours.";
    }
}
?>
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
 
        <?php elseif (!empty($error)): ?>
            <p><?= htmlspecialchars($error) ?></p>
 
        <?php else: ?>
            <div class="podium">
                <?php foreach ($podium as $item): ?>
                    <?php
                        $rang = intval($item["rang"] ?? 0);
                        $participant = $item["participant"] ?? "Inconnu";
                        $score = $item["score"] ?? "";
                        $image = $item["image"] ?? "";
 
                        // Emoji rang
                        $medal = ($rang === 1) ? "🥇" : (($rang === 2) ? "🥈" : (($rang === 3) ? "🥉" : "🏅"));
                        $imgUrl = "uploads/" . $image;
                    ?>
                    <div class="podium-card">
                        <div class="podium-rank"><?= $medal ?> Rang <?= $rang ?></div>
                        <div class="podium-name"><?= htmlspecialchars($participant) ?></div>
 
                        <?php if (!empty($image)): ?>
                            <img class="podium-img" src="<?= htmlspecialchars($imgUrl) ?>" alt="dessin">
                        <?php endif; ?>
 
                        <?php if ($score !== ""): ?>
                            <div class="podium-score">Score : <?= htmlspecialchars((string)$score) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
 
<footer>
    <p>&copy; <?= date('Y'); ?> - Gestion des concours de dessins</p>
</footer>
 
</body>
</html>