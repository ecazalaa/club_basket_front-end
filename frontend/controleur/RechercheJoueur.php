<?php


class RechercheJoueur {
    private $critere;
    private $motcle;
    private $apiUrl;

    public function __construct($critere, $motcle)
    {
        $this->critere = $critere;
        $this->motcle = $motcle;
        $this->apiUrl = "http://localhost/club_basket_back-end/back-end/endpoint/JoueurEndpoint.php";
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