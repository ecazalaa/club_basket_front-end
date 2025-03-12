<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/RechercheMatch.php';

// Récupérer le numéro de licence du joueur à modifier
$idMatch = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';

if (!empty($idMatch)) {
    $chercheMatch = new RechercheMatch('Id_Match', $idMatch);
    $m = $chercheMatch->executer();
    if ($m) {
        $m = $m[0];
        $date = htmlspecialchars($m["M_date"]);
        $lieu = htmlspecialchars($m["lieu"]);
        $nomAdversaire = htmlspecialchars($m['nom_adversaire']);
    } else {
        echo "Match non trouvé.";
        exit;
    }
} else {
    echo "Id de match non fourni.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feuille de match</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            width: 80%;
            justify-content: space-between;
        }

        .block {
            width: 45%;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            min-height: 200px;
        }

        .component {
            background-color: #e0e0e0;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            cursor: grab;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .droppable {
            min-height: 50px;
        }

        .reset-button, .submit-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<h1>Feuille de match du <?php echo $date; ?> à <?php echo $lieu; ?> contre <?php echo $nomAdversaire; ?></h1>
<div class="container">
    <div class="block" id="block1">
        <h2>Joueurs actifs</h2>
        <?php include 'afficher_joueurs.php';
        echo getDraggablePlayers();?>
    </div>
    <div class="block" id="block2">
        <h2>Feuille de match</h2>
        <form id="matchForm" method="post" action="traiter_participation.php">
            <input type="hidden" name="matchId" value="<?php echo $idMatch; ?>">
            <table>
                <tr>
                    <th>Poste</th>
                    <th>Joueur</th>
                </tr>
                <tr>
                    <td>Meneur</td>
                    <td class="droppable" id="meneur"></td>
                </tr>
                <tr>
                    <td>Ailier fort</td>
                    <td class="droppable" id="ailier fort"></td>
                </tr>
                <tr>
                    <td>Ailier faible</td>
                    <td class="droppable" id="ailier faible"></td>
                </tr>
                <tr>
                    <td>Pivot</td>
                    <td class="droppable" id="pivot"></td>
                </tr>
                <tr>
                    <td>Arrière</td>
                    <td class="droppable" id="arriere"></td>
                </tr>
                <tr>
                    <th>Remplacant</th>
                    <th>Joueur</th>
                </tr>
                <tr>
                    <td>Remplaçant 1</td>
                    <td class="droppable" id="remplacant"></td>
                </tr>
                <tr>
                    <td>Remplaçant 2</td>
                    <td class="droppable" id="remplacant"></td>
                </tr>
                <tr>
                    <td>Remplaçant 3</td>
                    <td class="droppable" id="remplacant"></td>
                </tr>
                <tr>
                    <td>Remplaçant 4</td>
                    <td class="droppable" id="remplacant"></td>
                </tr>
                <tr>
                    <td>Remplaçant 5</td>
                    <td class="droppable" id="remplacant"></td>
                </tr>
            </table>
            <button type="button" class="reset-button" id="resetButton">Réinitialiser</button>
            <button type="submit" class="submit-button">Valider</button>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const components = document.querySelectorAll('.component');
        const droppables = document.querySelectorAll('.droppable');
        const block1 = document.getElementById('block1');
        const resetButton = document.getElementById('resetButton');
        const matchForm = document.getElementById('matchForm');

        components.forEach(component => {
            component.addEventListener('dragstart', dragStart);
        });

        droppables.forEach(droppable => {
            droppable.addEventListener('dragover', dragOver);
            droppable.addEventListener('drop', drop);
        });

        function dragStart(e) {
            e.dataTransfer.setData('text/plain', e.target.id);
        }

        function dragOver(e) {
            e.preventDefault();
        }

        function drop(e) {
            e.preventDefault();
            const id = e.dataTransfer.getData('text');
            const component = document.getElementById(id);
            if (e.target.classList.contains('droppable')) {
                e.target.innerHTML = '';
                e.target.appendChild(component);
            }
        }

        resetButton.addEventListener('click', () => {
            droppables.forEach(droppable => {
                if (droppable.firstChild) {
                    block1.appendChild(droppable.firstChild);
                }
            });
        });

        matchForm.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent the default form submission

            const participations = [];
            droppables.forEach(droppable => {
                if (droppable.firstChild) {
                    const joueurId = droppable.firstChild.id;
                    const poste = droppable.id;
                    participations.push({ joueurId, poste });
                }
            });

            const data = {
                matchId: document.querySelector('input[name="matchId"]').value,
                participations: participations
            };

            fetch('create_participation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.text())
                .then(result => {
                    console.log(result);
                    alert(result); // Display the server response in an alert
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'enregistrement des participations.');
                });
        });
    });
</script>
</body>
</html>