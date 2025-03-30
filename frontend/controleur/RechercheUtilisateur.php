<?php
class RechercheUtilisateur {
    private $nom;
    private $prenom;
    private $mdp;
    private $apiUrl;

    public function __construct($nom, $prenom, $mdp)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->mdp = $mdp;
        $this->apiUrl = "http://localhost/club_basket_apiAuth/authapi.php"; // Ajustez selon votre configuration
        
    }

    public function executer() {
        // Préparation des données pour l'API
        $data = array(
            'Nom' => $this->nom,
            'Prenom' => $this->prenom,
            'Mot_de_passe' => $this->mdp
        );


        // Configuration de la requête
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data),
                'ignore_errors' => true // Pour obtenir la réponse même en cas d'erreur HTTP
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
            
            // Décodage de la réponse
            $result = json_decode($response, true);
            
            // Vérification du succès et stockage du token si disponible
            if (isset($result['status']) && $result['status'] === 'success') {

                if (isset($result['data']['jwt'])) {
                    // Stockage du token dans la session
                    $_SESSION['auth_token'] = $result['data']['jwt'];

                    // Si des informations utilisateur sont retournées, les stocker également
                    $_SESSION['user_nom'] = $this->nom;
                    $_SESSION['user_prenom'] = $this->prenom;

                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}