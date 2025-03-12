<?php


require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/ObtenirTousLesMatchsAVenir.php';

$matchs = new ObtenirTousLesMatchsAVenir();
$matchs = $matchs->executer();



if (count($matchs) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Date</th><th>Nom adversaires</th><th>Lieu</th><th>Action</th></tr>";


    foreach ($matchs as $matchBasket) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($matchBasket['M_date']) . "</td>";
        echo "<td>" . htmlspecialchars($matchBasket['nom_adversaire']) . "</td>";
        echo "<td>" . htmlspecialchars($matchBasket['lieu']) . "</td>";
        echo "<td><a href='feuille_matchV2.php?id=" . htmlspecialchars($matchBasket['Id_Match']) . "' class='button-modif'>Feuille de Match</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Aucun matchs trouvé.";
}
?>