<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Concours de dessins - Participants</title>
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
                <li><a href="Concours.php">Concours</a></li>
                <li><a href="Participants.php" class="active">Participants</a></li>
                <li><a href="Galerie.php">Galerie</a></li>
                <li><a href="Admin.php">Administration</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Participants</h2>
        <p>Par défaut, tous les participants sont affichés. Tu peux filtrer par concours avec le sélecteur ci‑dessous.</p>

        <?php
        // Connexion PDO (même paramètres que dans Concours.php)
        $dsn = 'mysql:host=localhost;dbname=Projet_BDD;charset=utf8mb4';
        $dbUser = 'db_etu';
        $dbPass = 'N3twork!';

        $erreurConnexion = null;
        $concoursList = [];
        $participants = [];
        $numConcoursChoisi = isset($_GET['numConcours']) ? (int)$_GET['numConcours'] : null;

        try {
            $pdo = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Récupération de tous les concours pour le sélecteur
            $stmtConcours = $pdo->query("SELECT numConcours, theme, dateDeb, dateFin FROM Concours ORDER BY dateDeb DESC");
            $concoursList = $stmtConcours->fetchAll();

            // Récupération des participants
            if ($numConcoursChoisi) {
                // Participants d'un concours spécifique
                $sql = "
                    SELECT 
                        u.numUtilisateur,
                        u.nom,
                        u.prenom,
                        u.age,
                        u.email,
                        c.nomClub,
                        c.ville
                    FROM ParticipationCompetiteur pc
                    JOIN Competiteur comp ON comp.numCompetiteur = pc.numCompetiteur
                    JOIN Utilisateur u ON u.numUtilisateur = comp.numCompetiteur
                    LEFT JOIN Club c ON c.numClub = u.numClub
                    WHERE pc.numConcours = :numConcours
                    ORDER BY numUtilisateur ASC
                ";
                $stmtPart = $pdo->prepare($sql);
                $stmtPart->execute([':numConcours' => $numConcoursChoisi]);
                $participants = $stmtPart->fetchAll();
            } else {
                // Tous les participants (tous concours confondus)
                $sql = "
                    SELECT DISTINCT
                        u.numUtilisateur,
                        u.nom,
                        u.prenom,
                        u.age,
                        u.email,
                        c.nomClub,
                        c.ville
                    FROM ParticipationCompetiteur pc
                    JOIN Competiteur comp ON comp.numCompetiteur = pc.numCompetiteur
                    JOIN Utilisateur u ON u.numUtilisateur = comp.numCompetiteur
                    LEFT JOIN Club c ON c.numClub = u.numClub
                    ORDER BY numUtilisateur ASC
                ";
                $stmtPart = $pdo->query($sql);
                $participants = $stmtPart->fetchAll();
            }
        } catch (PDOException $e) {
            $erreurConnexion = $e->getMessage();
        }
        ?>

        <section>
            <h3>Choisir un concours</h3>

            <?php if (!empty($erreurConnexion)): ?>
                <p style="color:red;">
                    Erreur de connexion à la base de données :
                    <?php echo htmlspecialchars($erreurConnexion, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>

            <form method="get" action="Participants.php">
                <label for="numConcours">Concours :</label>
                <select name="numConcours" id="numConcours">
                    <option value="">-- Sélectionne un concours --</option>
                    <?php foreach ($concoursList as $concours): ?>
                        <?php
                            $selected = ($numConcoursChoisi === (int)$concours['numConcours']) ? 'selected' : '';
                            $label = 'Concours n°' . $concours['numConcours'] . ' - ' . $concours['theme'];
                            if (!empty($concours['dateDeb']) && !empty($concours['dateFin'])) {
                                $label .= ' (' . $concours['dateDeb'] . ' → ' . $concours['dateFin'] . ')';
                            }
                        ?>
                        <option value="<?php echo htmlspecialchars($concours['numConcours']); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Afficher les participants</button>
            </form>
        </section>

        <?php if ($numConcoursChoisi): ?>
            <p><strong><?php echo count($participants); ?></strong> participant(s) pour ce concours.</p>
        <?php endif; ?>

        <section>
            <h3>
                <?php if ($numConcoursChoisi): ?>
                    Participants du concours n°<?php echo htmlspecialchars($numConcoursChoisi); ?>
                <?php else: ?>
                    Tous les participants (tous concours confondus)
                <?php endif; ?>
            </h3>

            <?php if (!empty($participants)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>N° utilisateur</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Âge</th>
                            <th>Email</th>
                            <th>Club</th>
                            <th>Ville</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['numUtilisateur']); ?></td>
                                <td><?php echo htmlspecialchars($p['nom']); ?></td>
                                <td><?php echo htmlspecialchars($p['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($p['age']); ?></td>
                                <td><?php echo htmlspecialchars($p['email']); ?></td>
                                <td><?php echo htmlspecialchars($p['nomClub'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($p['ville'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>
                    <?php if ($numConcoursChoisi): ?>
                        Aucun participant trouvé pour ce concours.
                    <?php else: ?>
                        Aucun participant trouvé dans la base de données.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Gestion des concours de dessins</p>
    </footer>
</body>
</html>

