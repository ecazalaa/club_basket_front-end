<?php
require_once 'session/session.php';
require_once 'session/session_timeout.php';
require_once '../controleur/RechercheMatch.php';
require_once '../controleur/ObtenirTousLesMatchsPasse.php';
require_once '../controleur/RechercheJoueur.php';
require_once '../controleur/ObtenirTousLesJoueurs.php';
require_once '../controleur/RechercheParticipation.php';

// Fonction pour calculer les statistiques générales des matchs
function getMatchStats() {
    $rechercheMatch = new ObtenirTousLesMatchsPasse();
    $matchs = $rechercheMatch->executer();

    $stats = [
        'total' => 0,
        'victoires' => 0,
        'nuls' => 0,
        'defaites' => 0
    ];

    foreach ($matchs as $match) {
        $stats['total']++;
        $resultat = explode('/', $match['resultat']);
        if (count($resultat) == 2) {
            list($scoreEquipe, $scoreAdversaire) = $resultat;
            if ($scoreEquipe > $scoreAdversaire) {
                $stats['victoires']++;
            } elseif ($scoreEquipe == $scoreAdversaire) {
                $stats['nuls']++;
            } else {
                $stats['defaites']++;
            }
        } else {
            // Handle the case where the resultat format is incorrect
            // For example, you can log an error or set default values
            $scoreEquipe = 0;
            $scoreAdversaire = 0;
        }
    }

    // Calculate percentages
    $stats['pct_victoires'] = $stats['total'] ? round(($stats['victoires'] / $stats['total']) * 100, 1) : 0;
    $stats['pct_nuls'] = $stats['total'] ? round(($stats['nuls'] / $stats['total']) * 100, 1) : 0;
    $stats['pct_defaites'] = $stats['total'] ? round(($stats['defaites'] / $stats['total']) * 100, 1) : 0;

    return $stats;
}

// Fonction pour obtenir les statistiques par joueur
function getPlayerStats() {
    $rechercheJoueur = new ObtenirTousLesJoueurs();
    $joueurs = $rechercheJoueur->executer();
    $statsJoueurs = [];

    foreach ($joueurs as $joueur) {
        $licence = $joueur['licence'];
        $rechercheParticipation = new RechercheParticipation('licence', $licence);
        $participations = $rechercheParticipation->executer();

        $statsJoueur = [
            'nom' => $joueur['Nom'],
            'prenom' => $joueur['Prenom'],
            'statut' => $joueur['statut'],
            'titularisations' => 0,
            'remplacements' => 0,
            'evaluations' => [],
            'matchs_gagnes' => 0,
            'total_matchs' => 0,
            'selections_consecutives' => getSelectionsConsecutives($licence)
        ];

        $postes = [];
        foreach ($participations as $participation) {
            // Count starts and substitutions
            if ($participation['Titu_Remp'] == 'titulaire') {
                $statsJoueur['titularisations']++;
            } else {
                $statsJoueur['remplacements']++;
            }

            // Collect positions to determine the preferred position
            if ($participation['poste']) {
                $postes[] = $participation['poste'];
            }

            // Collect ratings
            if (is_numeric($participation['Note'])) {
                $statsJoueur['evaluations'][] = (float)$participation['Note'];
            }

            // Check if the match was won
            $match = (new RechercheMatch('Id_Match', $participation['Id_Match']))->executer()[0];
            $resultat = explode('/', $match['resultat']);
            if (count($resultat) == 2) {
                list($scoreEquipe, $scoreAdversaire) = $resultat;
                if ($scoreEquipe > $scoreAdversaire) {
                    $statsJoueur['matchs_gagnes']++;
                }
                $statsJoueur['total_matchs']++;
            }
        }

        // Calculate the preferred position (most played)
        if (!empty($postes)) {
            $posteCounts = array_count_values($postes);
            arsort($posteCounts);
            $statsJoueur['poste_prefere'] = key($posteCounts);
        } else {
            $statsJoueur['poste_prefere'] = 'Non défini';
        }

        // Calculate the average rating
        $statsJoueur['moyenne_evaluations'] = !empty($statsJoueur['evaluations'])
            ? round(array_sum($statsJoueur['evaluations']) / count($statsJoueur['evaluations']), 1)
            : 'N/A';

        // Calculate the percentage of matches won
        $statsJoueur['pct_victoires'] = $statsJoueur['total_matchs']
            ? round(($statsJoueur['matchs_gagnes'] / $statsJoueur['total_matchs']) * 100, 1)
            : 0;

        $statsJoueurs[] = $statsJoueur;
    }

    return $statsJoueurs;
}

// Fonction pour calculer les sélections consécutives
function getSelectionsConsecutives($licence) {
    
    $rechercheParticipation = new RechercheParticipation('licence', $licence);
    $participations = $rechercheParticipation->executer();
    
    // Trier les participations par date de match
    usort($participations, function($a, $b) {
        $matchA = (new RechercheMatch('Id_Match', $a['Id_Match']))->executer()[0];
        $matchB = (new RechercheMatch('Id_Match', $b['Id_Match']))->executer()[0];
        return strtotime($matchB['M_date']) - strtotime($matchA['M_date']);
    });
    
    $consecutives = 0;
    foreach ($participations as $participation) {
        if ($participation['Titu_Remp']) {
            $consecutives++;
        } else {
            break;
        }
    }
    
    return $consecutives;
    
}

$matchStats = getMatchStats();
$playerStats = getPlayerStats();
?>

<!DOCTYPE html>
<html lang="fr"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de l'équipe</title>
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
            line-height: 1.5;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1, h2 {
            color: var(--text-color);
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-background);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .stat-subtitle {
            font-size: 0.875rem;
            color: #9ca3af;
        }

        .players-table {
            width: 100%;
            background: var(--card-background);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .players-table th,
        .players-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .players-table th {
            background-color: #f8fafc;
            font-weight: 600;
        }

        .players-table tr:last-child td {
            border-bottom: none;
        }

        .players-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-actif {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-inactif {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .players-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Statistiques de l'équipe</h1>
        <a href="index.php" class="button">Retour à l'accueil</a>

        <!-- Statistiques générales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Matchs gagnés</div>
                <div class="stat-value" style="color: var(--success-color)">
                    <?php echo $matchStats['victoires']; ?>
                </div>
                <div class="stat-subtitle"><?php echo $matchStats['pct_victoires']; ?>% des matchs</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Matchs nuls</div>
                <div class="stat-value" style="color: var(--warning-color)">
                    <?php echo $matchStats['nuls']; ?>
                </div>
                <div class="stat-subtitle"><?php echo $matchStats['pct_nuls']; ?>% des matchs</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Matchs perdus</div>
                <div class="stat-value" style="color: var(--danger-color)">
                    <?php echo $matchStats['defaites']; ?>
                </div>
                <div class="stat-subtitle"><?php echo $matchStats['pct_defaites']; ?>% des matchs</div>
            </div>
        </div>

        <!-- Statistiques des joueurs -->
        <h2>Statistiques par joueur</h2>
        <div class="players-table-container">
            <table class="players-table">
                <thead>
                    <tr>
                        <th>Joueur</th>
                        <th>Statut</th>
                        <th>Poste préféré</th>
                        <th>Titularisations</th>
                        <th>Remplacements</th>
                        <th>Moyenne éval.</th>
                        <th>Sél. consécutives</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerStats as $player): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($player['prenom']) . ' ' . htmlspecialchars($player['nom']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $player['statut']; ?>">
                                    <?php echo ucfirst($player['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($player['poste_prefere']); ?></td>
                            <td><?php echo $player['titularisations']; ?></td>
                            <td><?php echo $player['remplacements']; ?></td>
                            <td><?php echo $player['moyenne_evaluations']; ?></td>
                            <td><?php echo $player['selections_consecutives']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>