<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie d'un joueur</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --background-color: #f5f6fa;
            --text-color: #2c3e50;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .back-button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        input:invalid {
            border-color: var(--error-color);
        }

        .buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 30px;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        button[type="submit"] {
            background-color: var(--secondary-color);
            color: white;
        }

        button[type="reset"] {
            background-color: #e1e1e1;
            color: var(--text-color);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: var(--box-shadow);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px auto;
            }

            .buttons {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 1.5rem;
            }

            .back-button {
                position: static;
                display: inline-block;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                margin: 10px auto;
            }

            input {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button">Retour</a>
    
    <div class="container">
        <h1>Saisie d'un Joueur</h1>
        
        <form method="post" action="ajout_joueur.php">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>

            <div class="form-group">
                <label for="dateNaissance">Date de naissance</label>
                <input type="date" id="dateNaissance" name="dateNaissance" required>
            </div>

            <div class="form-group">
                <label for="taille">Taille (en cm)</label>
                <input type="number" id="taille" name="taille" required>
            </div>

            <div class="form-group">
                <label for="poids">Poids (en kg)</label>
                <input type="number" id="poids" name="poids" required>
            </div>

            <div class="form-group">
                <label for="numLicence">Numéro de licence</label>
                <input type="text" id="numLicence" name="numLicence" 
                       pattern="\d{6}" maxlength="6" 
                       title="Le numéro de licence doit être composé de 6 chiffres" required>
            </div>

            <div class="buttons">
                <button type="reset">Vider les champs</button>
                <button type="submit">Ajouter le joueur</button>
            </div>
        </form>
    </div>
</body>
</html>