<?php


require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../modele/Joueur.php';
require_once '../controleur/CreerJoueur.php';
require_once '../controleur/RechercheJoueur.php';

// Récupérer les données du formulaire
$nom = htmlspecialchars($_POST['nom']);
$prenom = htmlspecialchars($_POST['prenom']);
$date_naissance = htmlspecialchars($_POST['dateNaissance']);
$taille = htmlspecialchars($_POST['taille']);
$poids = htmlspecialchars($_POST['poids']);
$licence = htmlspecialchars($_POST['numLicence']);


$recherche = new RechercheJoueur( 'licence',$licence);

$joueurExistant = $recherche->executer();

//Si le joueur existe déjà

if ($joueurExistant) {
    echo "Le joueur existe déjà dans la base de données.";
} else {
    // Créer une instance de Joueur
    $joueur = new Joueur($nom, $prenom, $date_naissance, $taille, $poids, $licence);
    $insersion = new CreerJoueur($joueur);
    // Ajouter le joueur à la base de données
    $insersion->executer();

    echo "Le joueur a été ajouté avec succès.";
}
?>
<br><br>
<a href="index.php" class="button">retour</a>