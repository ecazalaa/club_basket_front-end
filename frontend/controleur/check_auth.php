<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function checkAuth() {
    // Vérifier si l'utilisateur est authentifié
    if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
        header('Location: login.php');
        exit;
    }

    // Vérifier si le token existe
    if (!isset($_SESSION['auth_token'])) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

// Fonction à appeler après chaque requête API pour vérifier la réponse
function checkApiResponse($response) {
    $result = json_decode($response, true);
    
    if (isset($result['status']) && $result['status'] === "error" && 
        isset($result['status_code']) && $result['status_code'] === 401 && 
        isset($result['data']['error']) && $result['data']['error'] === "Token invalide ou expiré") {
        // Token invalide ou expiré
        session_destroy();
        header('Location:../vue/login.php');
        exit;
    }
    
    return $result;
} 