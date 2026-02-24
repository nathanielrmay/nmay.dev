<?php
/**
 * @var array $teams List of teams (likely from Standings model)
 * @var bool $isOpen Whether the list starts open or closed
 */

// Defaults
$isOpen = $isOpen ?? false;

// Sort alphabetically for the list view
usort($teams, function($a, $b) {
    return strcmp($a['team_city'] . ' ' . $a['team_name'], $b['team_city'] . ' ' . $b['team_name']);
});
?>

<div class="teams-foldable-container">
    <details <?= $isOpen ? 'open' : '' ?>>
        <summary class="teams-summary">NBA Teams</summary>
        <div class="teams-grid">
            <?php foreach ($teams as $t): 
                $teamName = htmlspecialchars($t['team_city'] . ' ' . $t['team_name']);
                $teamId = htmlspecialchars($t['team_id']);
                // Determine colors for button style
                $primary = isset($t['color']) ? '#' . ltrim($t['color'], '#') : '#eee';
            ?>
                <a href="?team_id=<?= $teamId ?>" class="team-link" style="border-left: 4px solid <?= $primary ?>;">
                    <span class="team-text"><?= $teamName ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </details>
</div>

<style>
    .teams-foldable-container {
        font-family: 'Roboto', sans-serif;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        background: #fff;
    }

    .teams-summary {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 6px 10px;
        background-color: #2b2b2b;
        color: #f4f1ea;
        cursor: pointer;
        text-transform: uppercase;
        list-style: none;
    }
    
    .teams-summary::-webkit-details-marker {
        display: none;
    }

    .teams-summary::after {
        content: '+'; 
        float: right;
        font-weight: bold;
    }

    details[open] .teams-summary::after {
        content: '-';
    }

    .teams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 5px;
        padding: 5px;
        max-height: 250px;
        overflow-y: auto;
    }

    .team-link {
        display: block;
        padding: 4px 6px;
        text-decoration: none;
        color: #333;
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        transition: background 0.1s;
        font-size: 0.8rem;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .team-link:hover {
        background-color: #fff;
        border-color: #ccc;
    }

    .team-text {
        font-weight: 500;
    }
</style>
