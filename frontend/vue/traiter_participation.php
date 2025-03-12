<?php
// [Garder tout le PHP de traitement identique jusqu'aux messages echo]
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../modele/Participer.php';
require_once '../controleur/CreerParticipation.php';
require_once '../controleur/RechercheParticipation2.php';
require_once '../controleur/ModifierParticipation.php';
require_once '../controleur/SupprimerParticipation.php';

// Stocker les messages dans un tableau
$messages = [];

$matchId = $_POST['match_id'];
$positions = $_POST['positions'];
$modificationsApportees = false;

foreach ($positions as $licence => $position) {
    $tituRemp = (strpos($position, 'remplacant') !== false) ? 'remplacant' : 'titulaire';
    $rechercheParticipation = new RechercheParticipation2($licence, $matchId);
    $existingParticipation = $rechercheParticipation->executer();

    if ($existingParticipation) {
        if (isset($existingParticipation['poste']) && $existingParticipation['poste'] != $position && $position != null) {
            $participer = new Participer($licence, $matchId, $tituRemp, $position, null);
            $modifierParticipation = new ModifierParticipation($participer);
            $modifierParticipation->executer();
            $messages[] = "Participation mise à jour pour le joueur ID: $licence";
            $modificationsApportees = true;
        }
        if($position==null){
            $suppression= new SupprimerParticipation();
            $suppression->executer($licence, $matchId);
            $messages[] = "Le joueur d'ID: $licence a été supprimé de la participation car il n'as plus de poste";
            $modificationsApportees = true;
        }
    }

    else {
        if ($position != null) {
            $participer = new Participer($licence, $matchId, $tituRemp, $position, null);
            $creerParticipation = new CreerParticipation($participer);
            $creerParticipation->executer();
            $messages[] = "Nouvelle participation ajoutée pour le joueur ID: $licence";
            $modificationsApportees = true;
        }
    }
}

if ($modificationsApportees) {
    $statusMessage = "Les enregistrements de participation ont été mis à jour.";
    $statusClass = "success";
} else {
    $statusMessage = "Aucune modification n'a été apportée.";
    $statusClass = "info";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <style>
        :root {
            --success-color: #10b981;
            --info-color: #6366f1;
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

        .status-message.info {
            color: var(--info-color);
            background-color: rgba(99, 102, 241, 0.1);
        }

        .message-list {
            border-top: 1px solid var(--border-color);
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .message {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
            background-color: var(--background-color);
            font-size: 0.875rem;
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
            <div class="status-message <?php echo $statusClass; ?>">
                <?php echo $statusMessage; ?>
            </div>

            <?php if (!empty($messages)): ?>
                <div class="message-list">
                    <?php foreach ($messages as $message): ?>
                        <div class="message">
                            <?php echo $message; ?>
                        </div>
                    <?php endforeach; ?>
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