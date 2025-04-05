<?php
require_once '../vue/session/session_timeout.php';
require_once 'check_auth.php';

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
        
        $this->apiUrl = "https://clubbasketbackend.alwaysdata.net/back-end/endpoint/JoueurEndpoint.php";
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
            
            // Vérification de la réponse avec checkApiResponse
            $result = checkApiResponse($response);
            
            // Vérification du succès
            if (isset($result['status']) && $result['status'] === 'success') {
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Erreur lors de la création du joueur : " . $e->getMessage());
            return false;
        }
    }
}


