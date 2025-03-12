<?php

require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../modele/MatchBasket.php';
require_once '../controleur/RechercheMatch.php';

// Récupérer le numéro de licence du joueur à modifier
$IdMatch = isset($_GET['Id_Match']) ? htmlspecialchars($_GET['Id_Match']) : '';

$messages = [];
$statusMessage = '';
$statusClass = '';

if (!empty($IdMatch)) {
    $chercheM = new RechercheMatch('Id_Match', $IdMatch);
    $m = $chercheM->executer();
    if ($m) {
        $m = $m[0];
        $Date = htmlspecialchars($m["M_date"]);
        $nomAdversiare = htmlspecialchars($m["nom_adversaire"]);
        $lieu = htmlspecialchars($m['lieu']);
        $resultat = htmlspecialchars($m['resultat']);

        // Compare the match date with the current date
        $currentDate = date('Y-m-d H:i:s');
        if ($Date < $currentDate) {
            $statusMessage = "Le match est passé et ne peut pas être modifié.";
            $statusClass = "error";
        }
    } else {
        $statusMessage = "Match non trouvé.";
        $statusClass = "error";
    }
} else {
    $statusMessage = "Id du match non fourni.";
    $statusClass = "error";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un match</title>
    <style>
        :root {
            --success-color: #10b981;
            --error-color: #ef4444;
            --text-color: #374151;
            --background-color: #f9fafb;
            --card-background: #ffffff;
            --border-color: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.5;
            padding: 2rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            max-width: 800px;
            width: 100%;
        }

        .card {
            background: var(--card-background);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .status-message {
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .status-message.success {
            color: var(--success-color);
            background-color: rgba(16, 185, 129, 0.1);
        }

        .status-message.error {
            color: var(--error-color);
            background-color: rgba(239, 68, 68, 0.1);
        }

        .button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--text-color);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #1f2937;
        }

        .button-container {
            text-align: center;
            margin-top: 2rem;
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <?php if (!empty($statusMessage)): ?>
            <div class="status-message <?php echo $statusClass; ?>">
                <?php echo $statusMessage; ?>
            </div>
        <?php else: ?>
            <form method="post" action="modifier_match.php?Id_Match=<?php echo urlencode($IdMatch); ?>">
                <h1>Modification de Match</h1>

                <label for="M_date">Date du match : </label>
                <input type="datetime-local" id="M_date" name="M_date" value="<?php echo $Date; ?>" required><br><br>

                <label for="nom_adversaire"> Nom des adversaires :</label>
                <input type="text" id="nom_adversaire" name="nom_adversaire" value="<?php echo $nomAdversiare; ?>" required><br><br>

                <label for="lieu">Lieu du match :</label>
                <select id="lieu" name="lieu" required>
                    <option value="domicile" <?php echo ($lieu == 'domicile') ? 'selected' : ''; ?>>Domicile</option>
                    <option value="exterieur" <?php echo ($lieu == 'exterieur') ? 'selected' : ''; ?>>Extérieur</option>
                </select><br><br>

                <button type="submit" class="button">Valider les modifications</button>
            </form>
        <?php endif; ?>
        <div class="button-container">
            <button onclick="window.location.href='index.php'" class="button">
                Retour à la page d'accueil
            </button>
        </div>
    </div>
</div>

<?php
require_once '../controleur/ModifierMatch.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nvDate = isset($_POST['M_date']) ? htmlspecialchars($_POST['M_date']) : '';
    $nvAdversaire = isset($_POST['nom_adversaire']) ? htmlspecialchars($_POST['nom_adversaire']) : '';
    $nvLieu = isset($_POST['lieu']) ? htmlspecialchars($_POST['lieu']) : '';
    $nvResultat = isset($_POST['resultat']) ? htmlspecialchars($_POST['resultat']) : '';

    if (!empty($nvDate) && !empty($nvAdversaire) && !empty($nvLieu)) {
        $matchModif = new MatchBasket($nvDate, $nvAdversaire, $nvLieu, $nvResultat, $IdMatch);
        $modifM = new ModifierMatch($matchModif);
        $req = $modifM->executer();

        if ($req) {
            $statusMessage = "Le match a été modifié avec succès.";
            $statusClass = "success";
            header('Location: index.php');
        } else {
            $statusMessage = "Aucune modification effectuée.";
            $statusClass = "error";
        }
    }
}
?>
</body>
</html>