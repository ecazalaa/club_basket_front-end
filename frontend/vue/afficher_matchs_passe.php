<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/ObtenirTousLesMatchsPasse.php';

$matchs = new ObtenirTousLesMatchsPasse();
$matchs = $matchs->executer();

if (count($matchs) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Date</th><th>Nom adversaires</th><th>Lieu</th><th>Resultat</th><th>Action</th><th>Évaluation</th></tr>";

    foreach ($matchs as $matchBasket) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($matchBasket['M_date']) . "</td>";
        echo "<td>" . htmlspecialchars($matchBasket['nom_adversaire']) . "</td>";
        echo "<td>" . htmlspecialchars($matchBasket['lieu']) . "</td>";
        echo "<td><input type='text' id='resultat_" . $matchBasket['Id_Match'] . "' value='" . htmlspecialchars($matchBasket['resultat']) . "'></td>";
        echo "<td><button class='btn-enregistrer' onclick='updateScore(" . $matchBasket['Id_Match'] . ")'>Enregistrer score</button></td>";
        echo "<td><button class='btn-evaluer' onclick='window.location.href=\"evaluer_joueurs.php?match_id=" . $matchBasket['Id_Match'] . "\"'>Évaluer les joueurs</button></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Aucun match trouvé.";
}
?>

    <style>
        .btn-enregistrer, .btn-evaluer {
            background-color: #1a8f3b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin: 2px;
        }

        .btn-evaluer {
            background-color: #2c3e50;
        }

        .btn-enregistrer:hover {
            background-color: #145a2a;
        }

        .btn-evaluer:hover {
            background-color: #1a252f;
        }
    </style>

    <script>
        function updateScore(matchId) {
            var resultat = document.getElementById('resultat_' + matchId).value;
            var regex = /^\d+\/\d+$/;

            if (!regex.test(resultat)) {
                alert("Le format du résultat est invalide. Veuillez entrer deux entiers positifs séparés par un '/'.");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "afficher_matchs_passe.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert("Le résultat a été mis à jour avec succès.");
                }
            };
            xhr.send("id=" + encodeURIComponent(matchId) + "&resultat=" + encodeURIComponent(resultat));
        }
    </script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $resultat = isset($_POST['resultat']) ? htmlspecialchars($_POST['resultat']) : '';

    if ($id > 0 && !empty($resultat) && preg_match('/^\d+\/\d+$/', $resultat)) {
        require_once '../controleur/ModifierResultatMatch.php';
        $modifierResultat = new ModifierResultatMatch($id, $resultat);
        $modifierResultat->executer();
        echo "Le résultat a été mis à jour avec succès.";
    } else {
        echo "Données invalides.";
    }
}
?>