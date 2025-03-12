
<?php


require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../modele/Joueur.php';
require_once '../controleur/RechercheJoueur.php';

// Récupérer le numéro de licence du joueur à modifier
$licence = isset($_GET['licence']) ? htmlspecialchars($_GET['licence']) : '';



if (!empty($licence)) {
    $chercheJ = new RechercheJoueur( 'licence', $licence);
    $j=$chercheJ->executer();
    if ($j) {
        $j = $j[0];
        $nom = htmlspecialchars($j["nom"]);
        $prenom = htmlspecialchars($j["prenom"]);
        $dateNaissance = htmlspecialchars($j['date_naissance']);
        $taille = htmlspecialchars($j['taille']);
        $poids = htmlspecialchars($j['poids']);
    } else {
        echo "Joueur non trouvé.";
        exit;
    }
} else {
    echo "Numéro de licence non fourni.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Modifier un joueur</title>
    <link rel="stylesheet" type="text/css" href="style/style.css"> <!-- Assurez-vous que le chemin est correct -->
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .top-left {
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>
<div class="top-left">
    <a href="index.php" class="button">Retour</a>
</div>
<form method="post" action="modifier_joueur.php?licence=<?php echo urlencode($licence); ?>">
    <h1>Modification de Joueur</h1>
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?php echo $nom; ?>" required><br><br>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom" value="<?php echo $prenom; ?>" required><br><br>

    <label for="dateNaissance">Date de naissance</label>
    <input type="date" id="dateNaissance" name="dateNaissance" value="<?php echo $dateNaissance; ?>" required><br><br>

    <label for="taille">Taille (en cm) :</label>
    <input type="number" id="taille" name="taille" value="<?php echo $taille; ?>" required><br><br>

    <label for="poids">Poids (en kg) :</label>
    <input type="number" id="poids" name="poids" value="<?php echo $poids; ?>" required><br><br>

    <label for="numLicence">Numéro de licence</label>
    <input type="text" id="numLicence" name="numLicence" value="<?php echo $licence; ?>" readonly><br><br>

    <button type="submit">Valider les modifications</button>
</form>


<?php
require_once '../config/config.php';
require_once '../modele/Joueur.php';
require_once '../modele/JoueurDAO.php';
require_once '../controleur/ModifieJoueur.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nvnom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '';
    $nvprenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '';
    $nvdateNaissance = isset($_POST['dateNaissance']) ? htmlspecialchars($_POST['dateNaissance']) : '';
    $nvtaille = isset($_POST['taille']) ? htmlspecialchars($_POST['taille']) : '';
    $nvpoids = isset($_POST['poids']) ? htmlspecialchars($_POST['poids']) : '';
    $nvnumLicence = isset($_POST['numLicence']) ? htmlspecialchars($_POST['numLicence']) : '';

    if (!empty($nvnom) && !empty($nvprenom) && !empty($nvdateNaissance) && !empty($nvtaille) && !empty($nvpoids) && !empty($nvnumLicence)) {
        $joueurModif = new Joueur($nvnom, $nvprenom, $nvdateNaissance, $nvtaille, $nvpoids, $nvnumLicence);
        $modifJ = new ModifieJoueur($joueurModif);
        $req = $modifJ->executer();

        if ($req) {
            echo "Le joueur a été modifié avec succès.";
            header('Location: index.php');
        } else {
            echo "Aucune modification effectuée.";
        }
    }
}
?>

</body>
</html>