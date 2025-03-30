<?php

class ObtenirTousLesJoueurs
{
    private $apiUrl;

    // Constructeur : Initialise l'URL de l'API
    public function __construct()
    {
        $this->apiUrl = "http://localhost/club_basket_back-end/back-end/endpoint/JoueurEndpoint.php";
    }

    // Retourne tous les joueurs via l'API
    public function executer()
    {
        // Vérifier si le token existe
        if (!isset($_SESSION['auth_token'])) {
            return [];
        }

        // Récupérer le token de la session
        $token = $_SESSION['auth_token'];

        // Configuration de la requête GET avec token d'authentification
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

        // Création du contexte de la requête
        $context = stream_context_create($options);

        // Exécution de la requête
        try {
            $response = file_get_contents($this->apiUrl, false, $context);
            
            // Si la requête échoue
            if ($response === false) {
                return [];
            }
            
            // Extraction de la partie JSON de la réponse (au cas où il y aurait du texte avant)
            $jsonStartPos = strpos($response, '{');
            if ($jsonStartPos !== false) {
                $jsonResponse = substr($response, $jsonStartPos);
            } else {
                return [];
            }
            
            // Décodage de la partie JSON de la réponse
            $result = json_decode($jsonResponse, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }
            
            // Vérification du succès et récupération des données
            if (isset($result['status']) && $result['status'] === 'success' && isset($result['data'])) {
                return $result['data'];
            } else {
                return [];
            }
            
        } catch (Exception $e) {
            return [];
        }
    }
}