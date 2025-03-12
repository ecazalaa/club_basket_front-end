<?php
session_start();

require_once '../modele/Utilisateur.php';
require_once '../controleur/RechercheUtilisateur.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mdp = sha1($_POST['mdp']);


    $utilisateur = new RechercheUtilisateur( $nom, $prenom, $mdp);
    $utilisateur= $utilisateur->executer();


    if(!$utilisateur){
        $error = 'Nom, prenom ou mot de passe incorrect';
    }
    else{
        $_SESSION['authenticated'] = true;
        header('Location: index.php');
        exit;
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Page de connexion</title>
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-color: #1f2937;
            --background-color: #f3f4f6;
            --card-background: #ffffff;
            --border-color: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: var(--card-background);
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
        }

        .button {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 0.5rem;
            text-align: center;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 1rem;
        }

        .button:hover {
            background-color: #1d4ed8;
        }

        .create-account {
            display: block;
            text-align: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .create-account:hover {
            text-decoration: underline;
        }

        .error {
            color: var(--danger-color);
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById('mdp');
            var toggleButton = document.getElementById('togglePassword');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleButton.textContent = 'Cacher le mot de passe';
            } else {
                passwordField.type = 'password';
                toggleButton.textContent = 'Afficher le mot de passe';
            }
        }
    </script>
</head>
<body>
<form method="post" action="login.php">
    <h1>Connexion</h1>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required>
    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom" required>
    <label for="mdp">Mot de passe:</label>
    <input type="password" id="mdp" name="mdp" required>
    <button type="button" class="button" id="togglePassword" onclick="togglePasswordVisibility()">Afficher le mot de passe</button>
    <button type="submit" class="button">Se connecter</button>
    <a href="creer_compte_utilisateur.html" class="create-account">Créer mon compte utilisateur</a>
</form>

</body>
</html>
