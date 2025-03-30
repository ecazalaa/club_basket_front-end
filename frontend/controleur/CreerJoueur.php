<?php

class CreerJoueur
{
    private $donnees;
    private $apiUrl;

    // Constructeur : Accepte directement les données du joueur à ajouter
    public function __construct($nom, $prenom, $date_naissance, $taille, $poids, $licence)
    {
        $this->donnees = [
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => $date_naissance,
            'taille' => intval($taille),
            'poids' => intval($poids),
            'licence' => $licence
        ];
        
        $this->apiUrl = "http://localhost/club_basket_back-end/back-end/endpoint/JoueurEndpoint.php";
    }

    public function executer()
    {
        // Vérifier si le token existe
        if (!isset($_SESSION['auth_token'])) {
            return false;
        }

        // Récupérer le token de la session
        $token = $_SESSION['auth_token'];

        // Configuration de la requête POST avec token d'authentification
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
                'content' => json_encode($this->donnees),
                'ignore_errors' => true
            ]
        ];

        // Création du contexte de la requête
        $context = stream_context_create($options);

        // Exécution de la requête
        try {
            $response = file_get_contents($this->apiUrl, false, $context);
            
            // Si la requête échoue
            if ($response === false) {
                return false;
            }
            
            // Extraction de la partie JSON de la réponse (au cas où il y aurait du texte avant)
            $jsonStartPos = strpos($response, '{');
            if ($jsonStartPos !== false) {
                $jsonResponse = substr($response, $jsonStartPos);
            } else {
                return false;
            }
            
            // Décodage de la partie JSON de la réponse
            $result = json_decode($jsonResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }
            
            // Vérification du succès
            if (isset($result['status']) && $result['status'] === 'success') {
                return true;
            } else {
                return false;
            }
            
        } catch (Exception $e) {
            return false;
        }
    }
}


