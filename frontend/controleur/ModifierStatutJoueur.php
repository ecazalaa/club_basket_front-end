<?php
require_once '../vue/session/session_timeout.php';
require_once 'check_auth.php';

class ModifierStatutJoueur {
    private $licence;
    private $statut;
    private $apiUrl;

    public function __construct($licence, $statut)
    {
        $this->licence = $licence;
        $this->statut = $statut;
        $this->apiUrl = "https://clubbasketbackend.alwaysdata.net/back-end/endpoint/JoueurEndpoint.php?licence=" . $licence;
            }

    public function executer() {
        // Vérifier si le token existe
        if (!isset($_SESSION['auth_token'])) {
            return false;
        }

        // Récupérer le token de la session
        $token = $_SESSION['auth_token'];

        // Préparation des données pour l'API
        $data = array(
            'statut' => $this->statut
        );

        // Configuration de la requête PUT avec token d'authentification
        $options = [
            'http' => [
                'method' => 'PUT',
                'header' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ],
                'content' => json_encode($data),
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
            
            // Vérification du succès
            if (isset($result['status']) && $result['status'] === 'success') {
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Erreur lors de la modification du statut du joueur : " . $e->getMessage());
            return false;
        }
    }
}