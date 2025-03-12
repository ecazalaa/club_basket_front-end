<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/RechercheMatch.php';
require_once '../controleur/RechercheParticipation.php';
require_once '../controleur/RechercheJoueur.php';

$idMatch = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';

$statusMessage = '';
$statusClass = '';

if (!empty($idMatch)) {
    $chercheMatch = new RechercheMatch('Id_Match', $idMatch);
    $m = $chercheMatch->executer();
    if ($m) {
        $m = $m[0];
        $date = htmlspecialchars($m["M_date"]);
        $lieu = htmlspecialchars($m["lieu"]);
        $nomAdversaire = htmlspecialchars($m['nom_adversaire']);

        // Compare the match date with the current date
        $currentDate = date('Y-m-d H:i:s');
        if ($date < $currentDate) {
            $statusMessage = "Le match est passé et la feuille de match pas être consulté.";
            $statusClass = "error";
        }
    } else {
        $statusMessage = "Match non trouvé.";
        $statusClass = "error";
    }
} else {
    $statusMessage = "Id de match non fourni.";
    $statusClass = "error";
}

if (!empty($statusMessage)) {
    echo "<div class='status-message.error $statusClass'>$statusMessage</div>";
    echo "<br><br><a href='index.php'>Retour</a>";
    exit;
}

$joueursActifs = new RechercheJoueur('statut', 'actif');
$joueursActifs = $joueursActifs->executer();

$participations = new RechercheParticipation('Id_Match', $idMatch);
$participations = $participations->executer();

$positions = ['meneur', 'ailier faible', 'ailier fort', 'pivot', 'arriere', 'remplacant 1', 'remplacant 2', 'remplacant 3', 'remplacant 4', 'remplacant 5'];
$joueurPositions = [];
foreach ($participations as $participation) {
    $joueurPositions[$participation['licence']] = $participation['poste'];
    $positions = array_diff($positions, [$participation['poste']]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Feuille de match</title>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --background-color: #f8fafc;
            --table-header: #f1f5f9;
            --border-color: #e2e8f0;
            --text-color: #1e293b;
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
            line-height: 1.5;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text-color);
            text-align: center;
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }

        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        th {
            background-color: var(--table-header);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-color);
            white-space: nowrap;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            background-color: white;
            color: var(--text-color);
            cursor: pointer;
            font-size: 0.875rem;
        }

        select:hover {
            border-color: var(--primary-color);
        }

        select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 0.875rem;
            display: block;
            margin: 0 auto;
        }

        .button:hover {
            background-color: var(--primary-hover);
        }

        .match-info {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: white;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .header-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .status-message {
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .status-message.success {
            color: var(--success-color);
            background-color: rgba(16, 185, 129, 0.1);
        }

        .status-message.error {
            color: var(--error-color);
            background-color: rgba(239, 68, 68, 0.1);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .card {
                padding: 1rem;
            }
        }
    </style>
    <script>
        // Liste complète des positions possibles
        const ALL_POSITIONS = ['meneur', 'ailier faible', 'ailier fort', 'pivot', 'arriere',
            'remplacant 1', 'remplacant 2', 'remplacant 3', 'remplacant 4', 'remplacant 5'];

        // Stockage des positions actuellement attribuées
        let assignedPositions = new Map();

        function initializePositions() {
            // Récupérer les positions déjà attribuées lors du chargement
            document.querySelectorAll('select.position-select').forEach(select => {
                const playerId = select.getAttribute('data-player-id');
                const selectedPosition = select.value;
                if (selectedPosition && selectedPosition !== "") {
                    assignedPositions.set(selectedPosition, playerId);
                }
            });
            updateAllSelects();
        }

        function updateAllSelects() {
            const selects = document.querySelectorAll('select.position-select');

            selects.forEach(select => {
                const playerId = select.getAttribute('data-player-id');
                const currentValue = select.value;

                // Sauvegarder la sélection actuelle
                select.innerHTML = '<option value="">Aucun poste attribué</option>';

                // Ajouter toutes les positions disponibles
                ALL_POSITIONS.forEach(position => {
                    const isAssigned = assignedPositions.has(position);
                    const isAssignedToThisPlayer = assignedPositions.get(position) === playerId;

                    // Ajouter l'option seulement si la position n'est pas assignée ou si elle est assignée à ce joueur
                    if (!isAssigned || isAssignedToThisPlayer) {
                        const option = new Option(position, position);
                        option.selected = position === currentValue;
                        select.add(option);
                    }
                });
            });
        }

        function handlePositionChange(selectElement) {
            const playerId = selectElement.getAttribute('data-player-id');
            const newPosition = selectElement.value;

            // Supprimer l'ancienne position du joueur si elle existe
            for (const [position, pid] of assignedPositions.entries()) {
                if (pid === playerId) {
                    assignedPositions.delete(position);
                }
            }

            // Assigner la nouvelle position si elle n'est pas vide
            if (newPosition !== "") {
                assignedPositions.set(newPosition, playerId);
            }

            updateAllSelects();
        }

        document.addEventListener('DOMContentLoaded', () => {
            initializePositions();
            document.querySelectorAll('select.position-select').forEach(select => {
                select.addEventListener('change', (event) => handlePositionChange(event.target));
            });
        });
    </script>
</head>
<body>
<a href="index.php" class="back-button">Retour</a>
<div class="container">
    <div class="header-container">
        <h1>Feuille de match</h1>
        <span class="match-info">
                <?php echo $date; ?> • <?php echo $lieu; ?> • vs <?php echo $nomAdversaire; ?>
            </span>
    </div>

    <form method="post" action="traiter_participation.php">
        <input type="hidden" name="match_id" value="<?php echo $idMatch; ?>">
        <div class="card">
            <h2>Joueurs actifs</h2>
            <table>
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date de naissance</th>
                    <th>Taille (cm)</th>
                    <th>Poids (kg)</th>
                    <th>N° licence</th>
                    <th>Position</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($joueursActifs as $joueur): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['date_naissance']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['taille']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['poids']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['licence']); ?></td>
                        <td>
                            <select name="positions[<?php echo htmlspecialchars($joueur['licence']); ?>]"
                                    class="position-select"
                                    data-player-id="<?php echo htmlspecialchars($joueur['licence']); ?>"
                                    onchange="handlePositionChange(this)">
                                <option value="">Aucun poste attribué</option>
                                <?php foreach ($positions as $position): ?>
                                    <option value="<?php echo $position; ?>"
                                        <?php echo (isset($joueurPositions[$joueur['licence']]) && $joueurPositions[$joueur['licence']] == $position) ? 'selected' : ''; ?>>
                                        <?php echo $position; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if (isset($joueurPositions[$joueur['licence']])): ?>
                                    <option value="<?php echo $joueurPositions[$joueur['licence']]; ?>" selected>
                                        <?php echo $joueurPositions[$joueur['licence']]; ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="button">Valider</button>
        </div>
    </form>
</div>
</body>
</html>