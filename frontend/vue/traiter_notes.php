<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/ModifierParticipationNote.php';

$match_id = $_POST['match_id'];
$success = true;
$message = "";

// Debugging: Print the contents of $_POST


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erreurs = [];

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'note_') === 0) {
            $licence = substr($key, 5);
            $note = intval($value);

            if ($note >= 0 && $note <= 10) {
                try {
                    $modifier = new ModifierParticipationNote($licence,$match_id, $note);
                    if (!$modifier->executer()) {
                        $erreurs[] = "Erreur lors de l'enregistrement de la note pour le joueur avec la licence " . htmlspecialchars($licence);
                    }
                } catch (Exception $e) {
                    $erreurs[] = "Une erreur est survenue lors de l'enregistrement des notes: " . $e->getMessage();
                    $success = false;
                }
            } else {
                $erreurs[] = "La note doit être comprise entre 0 et 10 pour le joueur avec la licence " . htmlspecialchars($licence);
                $success = false;
            }
        }
    }

    if (empty($erreurs)) {
        $message = "Les notes ont été enregistrées avec succès !";
    } else {
        $message = "Des erreurs sont survenues :<br>" . implode("<br>", $erreurs);
        $success = false;
    }
} else {
    $message = "Méthode non autorisée";
    $success = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Traitement des évaluations</title>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-retour {
            display: inline-block;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .btn-retour:hover {
            background-color: #1a252f;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="message <?php echo $success ? 'success' : 'error'; ?>">
        <?php echo $message; ?>
    </div>
    <a href="index.php" class="btn-retour">Retour à l'accueil</a>
</div>
</body>
</html>