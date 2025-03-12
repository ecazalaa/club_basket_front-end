<?php


require_once 'session/session_timeout.php';
require_once '../modele/Utilisateur.php';
require_once '../controleur/CreerUtilisateur.php';
require_once '../controleur/RechercheUtilisateur.php';

// Récupérer les données du formulaire
$nom = htmlspecialchars($_POST['nom']);
$prenom = htmlspecialchars($_POST['prenom']);
$mdp = sha1($_POST['mdp']);

//connexion à la base de données

$recherche = new RechercheUtilisateur($nom,$prenom,$mdp);

$UtilisateurExistant = $recherche->executer();

//Si l'utilisateur existe déjà

if ($UtilisateurExistant) {
    echo "L'utilisateur existe déjà dans la base de données.";
} else {
    // Créer une instance de Utilisateur
    $utilisateur = new Utilisateur($nom, $prenom, $mdp);
    $insersion = new CreerUtilisateur($utilisateur);
    // Ajouter l'utilisateur à la base de données
    $insersion->executer();

    echo "L'utilisateur a été ajouté avec succès.";
}
?>
<br><br>
<a href="login.php" class="button">retour</a>
