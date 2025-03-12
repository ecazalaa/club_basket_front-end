<?php

require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/SupprimerMatch.php';
require_once '../controleur/SupprimerParticipationIdMatch.php';
require_once '../modele/MatchBasket.php';
require_once '../controleur/RechercheMatch.php';

// Récupération des données
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

        // Compare the match date with the current date
        $currentDate = date('Y-m-d H:i:s');
        if ($Date > $currentDate) {
            // Supprimer les participations associées au match
            $suppressionParticipation = new SupprimerParticipationIdMatch($IdMatch);
            $suppressionParticipation->executer();

            // Supprimer le match
            $suppression = new SupprimerMatch();
            $suppression->executer($IdMatch);

            $statusMessage = "Le match a été supprimé avec succès.";
            $statusClass = "success";
        } else {
            $statusMessage = "Le match est passé et ne peut donc pas être supprimé.";
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
    <title>Supprimer un match</title>
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
            font-family: system-ui, -apple-system, sans-serif;
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
        <?php endif; ?>
        <div class="button-container">
            <button onclick="window.location.href='index.php'" class="button">
                Retour à la page d'accueil
            </button>
        </div>
    </div>
</div>
</body>
</html>