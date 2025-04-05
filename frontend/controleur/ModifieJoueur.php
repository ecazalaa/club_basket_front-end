<?php

class ModifieJoueur
{
    private $nom;
    private $prenom;
    private $date_naissance;
    private $taille;
    private $poids;
    private $licence;
    private $apiUrl;

    public function __construct($nom, $prenom, $date_naissance, $taille, $poids, $licence)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->taille = $taille;
        $this->poids = $poids;
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

        // Préparation des données pour l'API
        $data = array(
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'date_naissance' => $this->date_naissance,
            'taille' => $this->taille,
            'poids' => $this->poids
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
            
            // Si la requête échoue
            if ($response === false) {
                return false;
            }   

            // Décodage de la réponse JSON
            $result = json_decode($response, true);
                
            // Vérification si la réponse contient les données du joueur    
            if (isset($result['data'])) {
                return $result['data'];
            }

            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la modification du joueur : " . $e->getMessage());
            return false;
        }
    }
}
