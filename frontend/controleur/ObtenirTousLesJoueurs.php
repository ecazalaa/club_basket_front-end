<?php
require_once 'check_auth.php';

class ObtenirTousLesJoueurs
{
    private $apiUrl;

    // Constructeur : Initialise l'URL de l'API
    public function __construct()
    {
        $this->apiUrl = "https://clubbasketbackend.alwaysdata.net/back-end/endpoint/JoueurEndpoint.php";
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
            $result = checkApiResponse($response);
            
            // Vérifier si la réponse contient les données des joueurs
            if (isset($result['data']) && is_array($result['data'])) {
                return $result['data'];
            }
            
            // Si pas de données ou format incorrect, retourner un tableau vide
            return [];
            
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des joueurs : " . $e->getMessage());
            return [];
        }
    }
}