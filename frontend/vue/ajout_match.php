<?php

require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../modele/MatchBasket.php';
require_once '../controleur/CreerMatch.php';
require_once '../controleur/RechercheMatch.php';

// Récupérer les données du formulaire
$date = htmlspecialchars($_POST['m_date']);
$adversaires = htmlspecialchars($_POST['adversaires']);
$lieu = htmlspecialchars($_POST['lieu']);



$recherche = new RechercheMatch( 'M_date',$date);

$matchExistant = $recherche->executer();

//Si le match existe deja

if ($matchExistant) {
    echo "Un match est déja programmé à cette date la.";
} else {
    // Créer une instance de MatchBasket
    $matchBasket = new MatchBasket($date, $adversaires, $lieu);
    $insersion = new CreerMatch($matchBasket);
    // Ajouter le match à la base de données
    $insersion->executer();

    echo "Le match a été ajouté avec succès.";
}
?>
<br><br>
<a href="index.php" class="button">retour</a>