<?php
/**
 * @var array|null $schedule Raw array of schedule rows from DB
 * @var bool $isOpen Whether the section starts open (default true)
 * @var int|null $team_id Optional team ID to filter/fetch
 * @var string $scope 'future', 'past', or 'all' (default 'future')
 * @var int $limit Max games to show (default 10)
 * @var bool $showDate Whether to show the Date column (default false)
 * @var string $sort 'ASC' or 'DESC'
 */

use lib\basket;
use lib\db\models\panal\nba\db_schedule;

$isOpen = $isOpen ?? true;
$team_id = $team_id ?? null;
$scope = $scope ?? 'future';
$limit = $limit ?? 10;
$showDate = $showDate ?? false;
$showLocation = $showLocation ?? true;
$showTime = $showTime ?? true;
$showGameType = $showGameType ?? true;
$showControls = $showControls ?? false;
$showLimitControl = $showLimitControl ?? false;
$sort = $sort ?? ($scope === 'past' ? 'DESC' : 'ASC');
$cssId = $cssId ?? 'sched_' . uniqid();

// Handle date selection
$selectedDate = $_GET['schedule_date'] ?? date('Y-m-d');

if (!isset($schedule)) {
    $db = basket::db_panal();
    $schedModel = new db_schedule($db);
    if ($team_id) {
        $schedule = $schedModel->readByTeamId($team_id, $limit, $scope, $sort);
    } else {
        $schedule = $schedModel->readByDate($selectedDate);
    }
}

// Determine Title
$title = 'SCHEDULE';
if ($team_id) {
    if ($scope === 'all') $title = 'SEASON SCHEDULE';
    elseif ($scope === 'past') $title = 'RECENT RESULTS';
    else $title = 'UPCOMING GAMES';
}

$showScores = ($scope !== 'future') || (!empty($selectedDate) && $selectedDate < date('Y-m-d'));
?>

<div class="schedule-container" id="<?= $cssId ?>">
    <details <?= $isOpen ? 'open' : '' ?> >
        <summary>
            <?= $title ?>
            <?php if (!$team_id): ?>
            <form method="GET" style="display: inline-block; margin-left: 20px;" onsubmit="return false;">
                <input type="date" name="schedule_date" value="<?= htmlspecialchars($selectedDate) ?>" 
                       style="font-family: inherit; font-size: 0.8rem; padding: 2px;"
                       onchange="window.location.search = 'schedule_date=' + this.value">
            </form>
            <?php endif; ?>
        </summary>
        
        <?php if (empty($schedule)): ?>
            <p><?= $team_id ? 'No games found.' : 'No games scheduled for this date.' ?></p>
        <?php else: ?>
            <?php if ($showControls || $showLimitControl): ?>
                <details class="column-controls">
                    <summary>Options</summary>
                    
                    <?php if ($showControls): ?>
                    <div class="column-toggles">
                        <span>Columns:</span>
                        <label><input type="checkbox" class="col-toggle" data-target="js-col-location" <?= $showLocation ? 'checked' : '' ?>> Location</label>
                        <label><input type="checkbox" class="col-toggle" data-target="js-col-date" <?= $showDate ? 'checked' : '' ?>> Date</label>
                        <label><input type="checkbox" class="col-toggle" data-target="js-col-time" <?= $showTime ? 'checked' : '' ?>> Time</label>
                        <label><input type="checkbox" class="col-toggle" data-target="js-col-gametype" <?= $showGameType ? 'checked' : '' ?>> Game Type</label>
                    </div>
                    <?php endif; ?>

                    <?php if ($showLimitControl): ?>
                    <div class="limit-control">
                        <label>
                            <span>Show:</span> 
                            <select class="limit-select">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">All</option>
                            </select> 
                            Games
                        </label>
                    </div>
                    <?php endif; ?>
                </details>
            <?php endif; ?>
            <div class="schedule">
            <table style="border: none;">
                <thead>
                    <tr class="schedule">
                        <?php if ($showGameType || $showControls): ?>
                            <th class="schedule js-col-gametype" <?= (!$showGameType) ? 'style="display:none"' : '' ?>>Game Type</th>
                        <?php endif; ?>
                        <?php if ($showDate || $showControls): ?>
                            <th class="schedule js-col-date" <?= (!$showDate) ? 'style="display:none"' : '' ?>>Date</th>
                        <?php endif; ?>
                        <?php if ($showTime || $showControls): ?>
                            <th class="schedule js-col-time" <?= (!$showTime) ? 'style="display:none"' : '' ?>>Time</th>
                        <?php endif; ?>
                        <th class="schedule">Home Team</th>
                        <th class="schedule">Home Rec</th>
                        <?php if ($showScores): ?>
                            <th class="schedule">Score</th>
                            <th class="schedule">Score</th>
                        <?php endif; ?>
                        <th class="schedule">Away Rec</th>
                        <th class="schedule">Away Team</th>
                        <?php if ($showLocation || $showControls): ?>
                            <th class="schedule js-col-location" <?= (!$showLocation) ? 'style="display:none"' : '' ?>>Location</th>
                        <?php endif; ?>

                    </tr>
                </thead>
                <tbody class="schedule">
                    <?php 
                    $schedIdx = 0;
                    foreach ($schedule as $g): 
                        $schedIdx++;
                        $gameType = !empty($g['game_label']) ? $g['game_label'] : 'Regular Season';
                        $gameTimeUtc = $g['game_time_utc'] ?? '';
                        $gameDate = $g['game_date'] ?? '';
                        if ($gameDate) $gameDate = date('M j', strtotime($gameDate));
                        
                        $homeCity = $g['home_team_city'] ?? '';
                        $homeName = $g['home_team_name'] ?? 'Home';
                        $homeRec = isset($g['home_team_wins']) ? $g['home_team_wins'] . '-' . $g['home_team_losses'] : '-';
                        $homeScore = $g['home_team_score'] ?? '';

                        $awayCity = $g['away_team_city'] ?? '';
                        $awayName = $g['away_team_name'] ?? 'Away';
                        $awayRec = isset($g['away_team_wins']) ? $g['away_team_wins'] . '-' . $g['away_team_losses'] : '-';
                        $awayScore = $g['away_team_score'] ?? '';
//                        basket::pretty_print_array($g);
                        $location = ($g['arena_name'] ?? '') . ($g['arena_city'] ? ', ' . $g['arena_city'] : '' ) . ( $g['arena_state'] ? ' ' . $g['arena_state'] : '' );
                    ?>
                        <tr class="sched-row" data-idx="<?= $schedIdx ?>">
                            <?php if ($showGameType || $showControls): ?>
                                <td class="js-col-gametype" <?= (!$showGameType) ? 'style="display:none"' : '' ?> >
                                    <?= htmlspecialchars($gameType) ?>
                                </td>
                            <?php endif; ?>

                            <?php if ($showDate || $showControls): ?>
                                <td class="js-col-date" <?= (!$showDate) ? 'style="display:none"' : '' ?> >
                                    <?= htmlspecialchars($gameDate) ?>
                                </td>
                            <?php endif; ?>

                            <?php if ($showTime || $showControls): ?>
                                <td class="js-col-time" <?= (!$showTime) ? 'style="display:none"' : '' ?> >
                                    <span class="local-time" data-utc="<?= htmlspecialchars($gameTimeUtc) ?>">--:--</span>
                                </td>
                            <?php endif; ?>

                            <td>
                                <a href="/sports_anals/nba/pg_teams.php?team_id=<?= $g['away_team_id'] ?>">
                                    <span><?= htmlspecialchars($awayCity) ?></span>
                                    <span><?= htmlspecialchars($awayName) ?></span>
                                </a>
                            </td>

                            <td><?= htmlspecialchars($awayRec) ?></td>

                            <?php if ($showScores): ?>
                                <td><?= htmlspecialchars($homeScore) ?></td>
                                <td><?= htmlspecialchars($awayScore) ?></td>
                            <?php endif; ?>

                            <td><?= htmlspecialchars($homeRec) ?></td>

                            <td>
                                <a href="/sports_anals/nba/pg_teams.php?team_id=<?= $g['home_team_id'] ?>">
                                    <span><?= htmlspecialchars($homeCity) ?></span>
                                    <span><?= htmlspecialchars($homeName) ?></span>
                                </a>
                            </td>

                            <?php if ($showLocation || $showControls): ?>
                                <td class="js-col-location" <?= (!$showLocation) ? 'style="display:none"' : '' ?>> 
                                    <?= htmlspecialchars($location) ?> 
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </details>
</div>

<link rel="stylesheet" href="/pages/sports_anals/nba/lib/partials/schedule/pt_schedule.css">
<script src="/pages/sports_anals/nba/lib/partials/schedule/pt_schedule.js"></script>
