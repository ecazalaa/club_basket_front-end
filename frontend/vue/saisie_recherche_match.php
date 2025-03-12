<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recherche de match</title>
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
    <h1>Recherche de match</h1>
    <label for="cle">mot clé :</label>
    <input type="text" id="cle" name="cle" required><br><br>


    <!-- Sélection d'un critère avec des boutons radio -->
    <h3>Choisir un critère de recherche :</h3>
    <input type="radio" id="nom_adversaire" name="critere" value="nom_adversaire" required>
    <label for="nom_adversaire">Nom adversaire</label>

    <input type="radio" id="lieu" name="critere" value="lieu">
    <label for="lieu">Lieu du match</label>



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
require_once '../controleur/RechercheMatch.php';

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
    $recherche = new RechercheMatch( $critere,$motcle);
    $matchBaskets=$recherche->executer();
// Vérifier s'il y a des résultats
    if (count($matchBaskets) > 0) {
// Afficher les résultats dans un tableau HTML avec des liens de modification et suppression
        echo "<table border='1'>";
        echo "<tr><th>Date</th><th>Nom adversaire</th><th>Lieu</th><th>Resultat</th><th>Actions</th></tr>";

        // Parcourir les résultats
        foreach ($matchBaskets as $matchBasket) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($matchBasket['M_date']) . "</td>";
            echo "<td>" . htmlspecialchars($matchBasket['nom_adversaire']) . "</td>";
            echo "<td>" . htmlspecialchars($matchBasket['lieu']) . "</td>";
            echo "<td>" . htmlspecialchars($matchBasket['resultat']) . "</td>";

            // Ajouter les hyperliens pour modifier et supprimer le joueur
            echo "<td>";
            echo '<a href="modifier_match.php?Id_Match=' . urlencode($matchBasket['Id_Match']) . '">Modifier |</a>';
            echo '<a href="supprimer_match.php?Id_Match=' . urlencode($matchBasket['Id_Match']) . '">Supprimer</a>';
            echo "</td>";

            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Aucun match trouvé pour le " .$critere." sélectionné : " . htmlspecialchars($motcle);
    }
}

?>



