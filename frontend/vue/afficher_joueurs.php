<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Afficher les joueurs</title>
    <style>
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 12px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<div class="table-container">
<?php

require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/ObtenirTousLesJoueurs.php';
require_once '../controleur/ModifierStatutJoueur.php';

$joueurs = new ObtenirTousLesJoueurs();
$joueurs = $joueurs->executer();

function getDraggablePlayers()
{
    global $joueurs;
    $output = '';
    if (count($joueurs) > 0) {
        foreach ($joueurs as $joueur) {
            if($joueur['statut'] == 'actif') {
                $output .= '<div class="component" draggable="true" id="'.htmlspecialchars($joueur['licence']).'">';
                $output .= htmlspecialchars($joueur['Nom']) . ' ' . htmlspecialchars($joueur['Prenom']) . ' / ' . htmlspecialchars($joueur['taille']) . 'cm / ' . htmlspecialchars($joueur['poids'] . 'kg');
                $output .= '</div>';
            }
        }
    } else {
        $output .= 'Aucun joueur trouvé.';
    }
    return $output;
}
function getAllPlayers(){
    global $joueurs;
    if (count($joueurs) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Nom</th><th>Prénom</th><th>Date de Naissance</th><th>Taille (cm)</th><th>Poids (kg)</th><th>Numéro de licence</th><th>Statut</th></tr>";

        foreach ($joueurs as $joueur) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($joueur['Nom']) . "</td>";
            echo "<td>" . htmlspecialchars($joueur['Prenom']) . "</td>";
            echo "<td>" . htmlspecialchars($joueur['date_naissance']) . "</td>";
            echo "<td>" . htmlspecialchars($joueur['taille']) . "</td>";
            echo "<td>" . htmlspecialchars($joueur['poids']) . "</td>";
            echo "<td>" . htmlspecialchars($joueur['licence']) . "</td>";
            echo "<td>";
            echo "<select id='statut' name='statut' onchange='updateStatut(\"" . htmlspecialchars($joueur['licence']) . "\", this.value)'>";
            echo "<option value='null'" . ($joueur['statut'] == 'null' ? ' selected' : '') . ">Null</option>";
            echo "<option value='actif'" . ($joueur['statut'] == 'actif' ? ' selected' : '') . ">Actif</option>";
            echo "<option value='blesse'" . ($joueur['statut'] == 'blesse' ? ' selected' : '') . ">Blessé</option>";
            echo "<option value='suspendu'" . ($joueur['statut'] == 'suspendu' ? ' selected' : '') . ">Suspendu</option>";
            echo "<option value='absent'" . ($joueur['statut'] == 'absent' ? ' selected' : '') . ">Absent</option>";
            echo "</select>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Aucun joueur trouvé.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $licence = isset($_POST['licence']) ? htmlspecialchars($_POST['licence']) : '';
    $statut = isset($_POST['statut']) ? htmlspecialchars($_POST['statut']) : '';

    if (!empty($licence) && !empty($statut)) {
        $modifierStatut = new ModifierStatutJoueur($licence, $statut);
        $result = $modifierStatut->executer();

        if ($result) {
            echo "Le statut du joueur a été mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour du statut du joueur.";
        }
    }
}
?>

<script>
    function updateStatut(licence, statut) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "afficher_joueurs.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert("Le statut du joueur a été mis à jour avec succès.");
            }
        };
        xhr.send("licence=" + encodeURIComponent(licence) + "&statut=" + encodeURIComponent(statut));
    }
</script>
</div>
</body>
</html>