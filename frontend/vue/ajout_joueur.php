<?php


require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/CreerJoueur.php';
require_once '../controleur/RechercheJoueur.php';

// Récupérer les données du formulaire
$nom = htmlspecialchars($_POST['nom']);
$prenom = htmlspecialchars($_POST['prenom']);
$date_naissance = htmlspecialchars($_POST['dateNaissance']);
$taille = htmlspecialchars($_POST['taille']);
$poids = htmlspecialchars($_POST['poids']);
$licence = htmlspecialchars($_POST['numLicence']);

// Vérifier si le token d'authentification existe
if (!isset($_SESSION['auth_token'])) {
    echo "Erreur d'authentification. Veuillez vous reconnecter.";
    exit;
}

// Vérifier si le joueur existe déjà via l'API
$apiUrl = "http://localhost/club_basket_back-end/back-end/endpoint/JoueurEndpoint.php?critere=licence&cle=" . urlencode($licence);
$token = $_SESSION['auth_token'];

// Configuration de la requête avec token d'authentification
$options = [
    'http' => [
        'method' => 'GET',
        'header' => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ],
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($apiUrl, false, $context);

// Extraire et décoder la réponse JSON
$joueurExistant = false;
if ($response !== false) {
    $jsonStartPos = strpos($response, '{');
    if ($jsonStartPos !== false) {
        $jsonResponse = substr($response, $jsonStartPos);
        $result = json_decode($jsonResponse, true);
        
        if ($result && isset($result['status']) && $result['status'] === 'success' && !empty($result['data'])) {
            $joueurExistant = true;
        }
    }
}

if ($joueurExistant) {
    echo "Le joueur existe déjà dans la base de données.";
} else {
    // Utiliser le nouveau contrôleur avec les données directement
    $insertion = new CreerJoueur($nom, $prenom, $date_naissance, $taille, $poids, $licence);
    
    // Ajouter le joueur via l'API
    if ($insertion->executer()) {
        echo "Le joueur a été ajouté avec succès.";
    } else {
        echo "Une erreur est survenue lors de l'ajout du joueur.";
    }
}
?>
<br><br>
<a href="index.php" class="button">retour</a>