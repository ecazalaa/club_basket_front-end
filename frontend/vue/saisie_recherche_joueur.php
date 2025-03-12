<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recherche de joueur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }

        form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 300px;
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            margin-bottom: 5px;
            color: #555;
            display: block;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f7f7f7;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

</head>
<body>


<form method="post" action="">
    <h1>Recherche de joueur</h1>
    <label for="cle">mot clé :</label>
    <input type="text" id="cle" name="cle" required><br><br>


    <!-- Sélection d'un critère avec des boutons radio -->
    <h3>Choisir un critère de recherche :</h3>
    <input type="radio" id="nom" name="critere" value="nom" required>
    <label for="nom">Nom</label>

    <input type="radio" id="prenom" name="critere" value="prenom">
    <label for="prenom">Prénom</label>

    <input type="radio" id="licence" name="critere" value="licence">
    <label for="licence">Numéro de licence</label>


    <!-- Bouton-->
    <button type="submit">Chercher</button><br><br><br>


</form>

<!-- Add a button to return to the homepage -->
<a href="index.php" class="button">Retour à la page d'accueil</a>

<br>
<br>
</body>
</html>

<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/RechercheJoueur.php';

// Récupérer les données du formulaire
$critere = null;
$motcle=null;

if (isset($_POST['cle'])) {
$motcle = $_POST['cle'];
}

if (isset($_POST['critere'])) {
$critere = $_POST['critere'];
}




if (!empty($motcle) && !empty($critere)) {
// Requête dynamique basée sur le critère choisi
    $recherche = new RechercheJoueur( $critere,$motcle);
    $joueurs=$recherche->executer();
// Vérifier s'il y a des résultats
if (count($joueurs) > 0) {
// Afficher les résultats dans un tableau HTML avec des liens de modification et suppression
echo "<table border='1'>";
    echo "<tr><th>Nom</th><th>Prénom</th><th>Date de naissance</th><th>Taille</th><th>Poids</th><th>Numéro de licence</th><th>Actions</th></tr>";

    // Parcourir les résultats
    foreach ($joueurs as $joueur) {
    echo "<tr>";
        echo "<td>" . htmlspecialchars($joueur['nom']) . "</td>";
        echo "<td>" . htmlspecialchars($joueur['prenom']) . "</td>";
        echo "<td>" . htmlspecialchars($joueur['date_naissance']) . "</td>";
        echo "<td>" . htmlspecialchars($joueur['taille']) . "</td>";
        echo "<td>" . htmlspecialchars($joueur['poids']) . "</td>";
        echo "<td>" . htmlspecialchars($joueur['licence']) . "</td>";

        // Ajouter les hyperliens pour modifier et supprimer le joueur
        echo "<td>";
            echo '<a href="modifier_joueur.php?licence=' . urlencode($joueur['licence']) . '">Modifier |</a>';
            echo '<a href="supprimer_joueur.php?licence=' . urlencode($joueur['licence']) . '">Supprimer</a>';
            echo "</td>";

        echo "</tr>";
    }

    echo "</table>";
} else {
echo "Aucun joueur trouvé pour le " .$critere." sélectionné : " . htmlspecialchars($motcle);
}
}

?>



