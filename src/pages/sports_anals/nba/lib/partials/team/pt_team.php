<?php
/**
 * @var int $team_id The ID of the team to display
 */

use lib\basket;
use pages\sports_anals\nba\lib\basket as nba_basket;
use lib\db\models\panal\nba\db_teams;
use lib\db\models\panal\nba\db_standings;

if (!isset($team_id)) return;

$db = basket::db_panal();
$teamModel = new db_teams($db);
$standingsModel = new db_standings($db);

$team = $teamModel->read($team_id);
$standing = $standingsModel->readByTeamId($team_id);
//basket::pretty_print_array($team);
//basket::pretty_print_array($standing);

if (!$team) {
    echo "<p>Team not found.</p>";
    return;
}

// Colors
$primary = isset($team['color']) ? '#' . ltrim($team['color'], '#') : '#333';
$alt = isset($team['alternate_color']) ? '#' . ltrim($team['alternate_color'], '#') : '#fff';
// Primary logo key is 'logo' as confirmed by schema
$logoUrl = $team['logo'] ?? $team['nba_logo_svg'] ?? '';

$textColor = nba_basket::getContrastColorTeam($primary);
?>

<div class="team-card-compact">
    <!-- Line 1: Header -->
    <div class="team-header-compact" style="background-color: <?= $primary ?>; color: <?= $textColor ?>;">
        <div class="header-left">
            <?php if ($logoUrl): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($team['team_name']) ?>" class="team-logo-img">
            <?php else: ?>
                <div class="team-logo-placeholder" style="background-color: <?= $alt ?>; color: <?= nba_basket::getContrastColorTeam($alt) ?>;">
                    <?= substr($team['team_name'], 0, 1) ?>
                </div>
            <?php endif; ?>
            
            <div class="team-identity">
                <span class="team-city"><?= htmlspecialchars($team['team_city']) ?></span>
                <span class="team-name" style="color: <?= $alt ?>; text-shadow: 1px 1px 0 #000;"><?= htmlspecialchars($team['team_name']) ?></span>
            </div>
        </div>
        
        <div class="header-right">
            <?php if ($standing): ?>
                <span class="record"><?= $standing['wins'] ?>-<?= $standing['losses'] ?></span>
                <span class="rank-badge">#<?= $standing['playoff_rank'] ?? '-' ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* ... existing styles ... */
    
    /* Ensure inner details match height? Optional. */
    
    .team-card-compact {
        background-color: transparent;
        font-family: 'Roboto', sans-serif;
        max-width: 100%;
        overflow: hidden;
    }

    .team-header-compact {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 15px; /* Significantly reduced vertical padding */
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .team-logo-placeholder {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Playfair Display', serif;
        font-weight: 900;
        font-size: 1.2rem;
        border: 2px solid #fff;
    }

    .team-identity {
        display: flex;
        flex-direction: row; /* Inline as requested */
        align-items: baseline;
        gap: 6px;
        line-height: 1;
    }

    .team-city {
        font-size: 1.0rem;
        font-weight: 400;
        text-transform: uppercase;
        opacity: 0.9;
    }

    .team-name {
        font-family: 'Playfair Display', serif;
        font-weight: 900;
        font-size: 1.6rem;
        text-transform: uppercase;
    }

    .team-logo-img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        background-color: #ffffff;
        padding: 5px;
        border: 2px solid #ffffff;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .header-right {
        text-align: right;
        font-family: 'Roboto Mono', monospace;
    }

    .record {
        font-size: 1.3rem;
        font-weight: 700;
        display: block;
    }

    .rank-badge {
        font-size: 0.8rem;
        opacity: 0.8;
    }
</style>
