<?php
/**
 * @var array $standings Raw array of standings rows from DB
 * @var bool $isOpen Whether the section starts open (default true)
 * @var int|null $team_id Support for single team ID to highlight
 * @var array|null $highlight_ids Array of team IDs to highlight
 * @var string|null $filter_conference Optional conference filter ('East' or 'West')
 */

$isOpen = $isOpen ?? true;
$team_id = $team_id ?? null;
$highlight_ids = $highlight_ids ?? [];
$filter_conference = $filter_conference ?? null;

// Normalize highlight IDs
if ($team_id && !in_array($team_id, $highlight_ids)) {
    $highlight_ids[] = $team_id;
}

// 1. Organize data into Conference > Division > Teams
$data = ['East' => [], 'West' => []];

foreach ($standings as $row) {
    $conf = $row['conference'] ?? 'Unknown';
    $div = $row['division'] ?? 'Unknown';
    
    if (!isset($data[$conf][$div])) {
        $data[$conf][$div] = [];
    }
    $data[$conf][$div][] = $row;
}

// Helper to render a division table
if (!function_exists('renderDivisionTable')) {
    function renderDivisionTable($divisionName, $teams, $highlightIds = []) {
        ?>
        <div class="division-block">
            <table class="standings" style="border: none;">
                <thead>
                    <tr>
                        <th class="standings"><?= htmlspecialchars($divisionName) ?></th>
                        <th class="standings">W</th>
                        <th class="standings">L</th>
                        <th class="standings">Pct</th>
                        <th class="standings">Rnk</th>
                    </tr>
                </thead>
                <tbody class="standings">
                    <?php foreach ($teams as $t): 
                        $isHighlight = (in_array($t['team_id'], $highlightIds));
                        $highlightClass = $isHighlight ? 'active-team-highlight' : '';
                    ?>
                        <tr class="<?= $highlightClass ?>">
                            <td class="standings">
                                <a class="standings" href="/sports_anals/nba/pg_teams.php?team_id=<?= $t['team_id'] ?>">
                                    <span><?= htmlspecialchars($t['team_city']) ?></span>
                                    <span><?= htmlspecialchars($t['team_name']) ?></span>
                                </a>
                            </td>
                            <td class="standings"><?= $t['wins'] ?></td>
                            <td class="standings"><?= $t['losses'] ?></td>
                            <td class="standings"><?= number_format((float)$t['win_pct'], 3) ?></td>
                            <td class="standings"><?= htmlspecialchars($t['playoff_rank'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
?>

<div>
    <details <?= $isOpen ? 'open' : '' ?>>
        <summary>NBA STANDINGS</summary>
        <div class="standings-grid">
            
            <?php if (!$filter_conference || $filter_conference === 'East'): ?>
            <div class="conference-column">
                <h3 style="margin: 5px 0px;">Eastern Conference</h3><?php
                if (isset($data['East'])) {
                    if ($filter_conference) {
                        $allTeams = [];
                        foreach($data['East'] as $divTeams) { $allTeams = array_merge($allTeams, $divTeams); }
                        usort($allTeams, function($a, $b) { return $b['win_pct'] <=> $a['win_pct']; });
                        renderDivisionTable('Conference Standings', $allTeams, $highlight_ids);
                    } else {
                        foreach ($data['East'] as $divName => $teams) {
                            renderDivisionTable($divName, $teams, $highlight_ids);
                        }
                    }
                }
                ?></div>
            <?php endif; ?>

            <?php if (!$filter_conference || $filter_conference === 'West'): ?>
            <div class="conference-column">
                <h3 style="margin: 5px 0px;">Western Conference</h3><?php
                if (isset($data['West'])) {
                    if ($filter_conference) {
                        $allTeams = [];
                        foreach($data['West'] as $divTeams) { $allTeams = array_merge($allTeams, $divTeams); }
                        usort($allTeams, function($a, $b) { return $b['win_pct'] <=> $a['win_pct']; });
                        renderDivisionTable('Conference Standings', $allTeams, $highlight_ids);
                    } else {
                        foreach ($data['West'] as $divName => $teams) {
                            renderDivisionTable($divName, $teams, $highlight_ids);
                        }
                    }
                }
                ?></div>
            <?php endif; ?>
        </div>
    </details>
</div>

<link rel="stylesheet" href="/pages/sports_anals/nba/lib/partials/standings/pt_standings.css">
