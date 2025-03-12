<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/RechercheParticipation.php';
require_once '../controleur/RechercheJoueur.php';
require_once '../controleur/RechercheMatch.php';

$match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;


$m = new RechercheMatch('Id_Match', $match_id);
$m = $m->executer();
$autorisation = false;

if ($m) {
    $m = $m[0];
    $Date = htmlspecialchars($m["M_date"]);
    $nomAdversaire = htmlspecialchars($m["nom_adversaire"]);
    $lieu = htmlspecialchars($m['lieu']);

    // Compare the match date with the current date
    $currentDate = date('Y-m-d H:i:s');
    if ($Date > $currentDate) {
        $statusMessage = "Le match n'a pas encore eu lieu, vous ne pouvez pas évaluer les joueurs.";
        $statusClass = "error";
    } else {
        $autorisation = true;
    }
} else {
    $statusMessage = "Match non trouvé.";
    $statusClass = "error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Évaluation des joueurs - Match contre <?php echo isset($nomAdversaire) ? $nomAdversaire : ''; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .match-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #1a8f3b;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #1a8f3b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 20px;
        }
        .btn-submit:hover {
            background-color: #145a2a;
        }
        .btn-retour {
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="btn-retour">Retour aux matchs</a>

    <?php if (isset($messageSuccess)): ?>
        <div class="message success"><?php echo $messageSuccess; ?></div>
    <?php endif; ?>

    <?php if (isset($statusMessage)): ?>
        <div class="message <?php echo $statusClass; ?>"><?php echo $statusMessage; ?></div>
    <?php endif; ?>

    <?php if ($autorisation): ?>
        <div class="match-info">
            <h2>Match contre <?php echo $nomAdversaire; ?></h2>
            <p>Date: <?php echo $Date; ?></p>
            <p>Lieu: <?php echo $lieu; ?></p>
        </div>

        <form method="POST" action="traiter_notes.php">
            <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
            <table>
                <tr>
                    <th>Joueur</th>
                    <th>Note (0-10)</th>
                </tr>
                <?php
                $participations = new RechercheParticipation('Id_Match', $match_id);
                $participations = $participations->executer();

                foreach ($participations as $participation) {
                    $joueur = new RechercheJoueur('licence', $participation['licence']);
                    $joueur = $joueur->executer();
                    if ($joueur && count($joueur) > 0) {
                        $joueur = $joueur[0];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($joueur['nom']) . " " . htmlspecialchars($joueur['prenom']) . "</td>";
                        echo "<td><input type='number' name='note_" . $joueur['licence'] . "' 
    value='" . htmlspecialchars(isset($participation['Note']) ? $participation['Note'] : '') . "' 
    min='0' max='10' required></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>
            <button type="submit" class="btn-submit">Enregistrer les évaluations</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>