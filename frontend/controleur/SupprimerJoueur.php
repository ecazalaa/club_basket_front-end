<?php
require_once '../vue/session/session_timeout.php';
require_once 'check_auth.php';

class SupprimerJoueur{
    private $licence;
    private $apiUrl;

    public function __construct($licence)
    {
        $this->licence = $licence;
        $this->apiUrl = "https://clubbasketbackend.alwaysdata.net/back-end/endpoint/JoueurEndpoint.php?licence=" . $licence;
    }

    public function executer()
    {
        // Vérifier si le token existe
        if (!isset($_SESSION['auth_token'])) {
            return false;
        }

        // Récupérer le token de la session
        $token = $_SESSION['auth_token'];

        // Configuration de la requête DELETE avec token d'authentification
        $options = [
            'http' => [
                'method' => 'DELETE',
                'header' => [
                    'Authorization: Bearer ' . $token   
                ],
                'ignore_errors' => true
            ]
        ];

        // Création du contexte de la requête   
        $context = stream_context_create($options);

        // Exécution de la requête
        try {
            $response = file_get_contents($this->apiUrl, false, $context);  
            
            // Vérification de la réponse avec checkApiResponse
            $result = checkApiResponse($response);
            
            // Vérification si la réponse contient les données du joueur
            if (isset($result['status']) && $result['status'] === 'success') {
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {    
            error_log("Erreur lors de la suppression du joueur : " . $e->getMessage());
            return false;
        }
    }
}
