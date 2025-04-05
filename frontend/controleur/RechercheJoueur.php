<?php
require_once '../vue/session/session_timeout.php';
require_once 'check_auth.php';

class RechercheJoueur {
    private $critere;
    private $motcle;
    private $apiUrl;

    public function __construct($critere, $motcle)
    {
        $this->critere = $critere;
        $this->motcle = $motcle;
        $this->apiUrl = "https://clubbasketbackend.alwaysdata.net/back-end/endpoint/JoueurEndpoint.php";
    }

    public function executer() {
        // Vérifier si le token existe
        if (!isset($_SESSION['auth_token'])) {
            return [];
        }

        // Récupérer le token de la session
        $token = $_SESSION['auth_token'];

        // Construction de l'URL avec les paramètres de recherche
        $url = $this->apiUrl . "?critere=" . urlencode($this->critere) . "&cle=" . urlencode($this->motcle);

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
            $response = file_get_contents($url, false, $context);
            
            // Vérification de la réponse avec checkApiResponse
            $result = checkApiResponse($response);
            
            // Vérification du succès et récupération des données
            if (isset($result['status']) && $result['status'] === 'success' && isset($result['data'])) {
                return $result['data'];
            }
            
            return [];
            
        } catch (Exception $e) {
            error_log("Erreur lors de la recherche de joueur : " . $e->getMessage());
            return [];
        }
    }
}