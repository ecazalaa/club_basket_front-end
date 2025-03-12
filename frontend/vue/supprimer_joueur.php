<?php


require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/SupprimerJoueur.php';
require_once '../controleur/RechercheParticipation.php';
//récupération des données

$licence = isset($_GET['licence']) ? htmlspecialchars($_GET['licence']) : '';




if (!empty($licence)) {
    // Supprimer le joueur
    $recherche = new RechercheParticipation('licence', $licence);
    $participations = $recherche->executer();
    if(count($participations) > 0){
        echo "Le joueur participe ou as deja participer à un match, impossible de le supprimer.";
    }
    else{
        $suppression = new SupprimerJoueur;
        $suppression->executer($licence);
        echo "Le joueur a été supprimé avec succès.";
    }
} else {
    echo "Informations de contact invalides.";
}


echo "<br><br><a href='index.php'>Retour</a> | ";


?>