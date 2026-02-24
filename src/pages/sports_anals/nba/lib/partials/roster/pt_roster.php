<?php
/**
 * @var array|null $roster Raw array of roster rows from DB
 * @var bool $isOpen Whether the section starts open (default true)
 * @var int|null $team_id Team ID to fetch roster for
 * @var string $style 'color' or 'newspaper'
 */

use lib\basket;
use lib\db\models\panal\nba\db_rosters;

$isOpen = $isOpen ?? true;
$team_id = $team_id ?? null;
$style = $style ?? 'newspaper';
$showControls = $showControls ?? false;
$cssId = $cssId ?? 'rost_' . uniqid();

$showNum = $showNum ?? true;
$showPos = $showPos ?? true;
$showHeight = $showHeight ?? true;
$showWeight = $showWeight ?? true;
$showAge = $showAge ?? true;
$showExp = $showExp ?? true;
$showSchool = $showSchool ?? true;

if (!isset($roster) && $team_id) {
    $db = basket::db_panal();
    $rosterModel = new db_rosters($db);
    $roster = $rosterModel->readByTeamId($team_id);
}

$title = 'TEAM ROSTER';
?>

<div class="roster-container" id="<?= $cssId ?>">

    <details <?= $isOpen ? 'open' : '' ?>>

        <summary class="section-header"><?= $title ?></summary>

        

        <?php if (empty($roster)): ?>

            <p style="font-family: 'Playfair Display', serif; font-style: italic; color: #666; font-size: 0.8rem;">

                No roster data found.

            </p>

                <?php else: ?>

                    <?php if ($showControls): ?>

                        <details class="column-controls" style="margin-bottom: 5px; border: 1px solid #ccc; padding: 5px; font-family: sans-serif;">

                            <summary style="font-size: 0.8rem; cursor: pointer; font-weight: bold; color: #333;">Options</summary>

                            

                            <div style="display: flex; gap: 10px; font-size: 0.8rem; padding-top: 5px; flex-wrap: wrap; align-items: center;">

                                <span style="font-weight: bold; margin-right: 5px;">Columns:</span>

                                <label><input type="checkbox" class="col-toggle" data-target="js-col-num" <?= $showNum ? 'checked' : '' ?>> #</label>

                                <label><input type="checkbox" class="col-toggle" data-target="js-col-pos" <?= $showPos ? 'checked' : '' ?>> Pos</label>

                                <label><input type="checkbox" class="col-toggle" data-target="js-col-height" <?= $showHeight ? 'checked' : '' ?>> HT</label>

                                <label><input type="checkbox" class="col-toggle" data-target="js-col-weight" <?= $showWeight ? 'checked' : '' ?>> WT</label>

                                <label><input type="checkbox" class="col-toggle" data-target="js-col-age" <?= $showAge ? 'checked' : '' ?>> Age</label>

                                <label><input type="checkbox" class="col-toggle" data-target="js-col-exp" <?= $showExp ? 'checked' : '' ?>> Exp</label>

                                <label><input type="checkbox" class="col-toggle" data-target="js-col-school" <?= $showSchool ? 'checked' : '' ?>> School</label>

                            </div>

                        </details>

                    <?php endif; ?>

        

                    <table class="compact-roster">

                        <thead>

                            <tr>

                                <?php if ($showNum || $showControls): ?>

                                    <th class="num-col js-col-num" <?= !$showNum ? 'style="display:none"' : '' ?>>#</th>

                                <?php endif; ?>

                                <th class="name-col">Player</th>

                                <?php if ($showPos || $showControls): ?>

                                    <th class="pos-col js-col-pos" <?= !$showPos ? 'style="display:none"' : '' ?>>Pos</th>

                                <?php endif; ?>

                                <?php if ($showHeight || $showControls): ?>

                                    <th class="height-col js-col-height" <?= !$showHeight ? 'style="display:none"' : '' ?>>HT</th>

                                <?php endif; ?>

                                <?php if ($showWeight || $showControls): ?>

                                    <th class="weight-col js-col-weight" <?= !$showWeight ? 'style="display:none"' : '' ?>>WT</th>

                                <?php endif; ?>

                                <?php if ($showAge || $showControls): ?>

                                    <th class="age-col js-col-age" <?= !$showAge ? 'style="display:none"' : '' ?>>Age</th>

                                <?php endif; ?>

                                <?php if ($showExp || $showControls): ?>

                                    <th class="exp-col js-col-exp" <?= !$showExp ? 'style="display:none"' : '' ?>>Exp</th>

                                <?php endif; ?>

                                <?php if ($showSchool || $showControls): ?>

                                    <th class="school-col js-col-school" <?= !$showSchool ? 'style="display:none"' : '' ?>>School</th>

                                <?php endif; ?>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($roster as $p): ?>

                                <tr class="rost-row">

                                    <?php if ($showNum || $showControls): ?>

                                        <td class="num-col js-col-num" <?= !$showNum ? 'style="display:none"' : '' ?>><?= htmlspecialchars($p['number'] ?? '-') ?></td>

                                    <?php endif; ?>

                            <td class="name-col" style="font-weight: 900;"><?= htmlspecialchars($p['player'] ?? 'Unknown') ?></td>

                            <?php if ($showPos || $showControls): ?>

                                <td class="pos-col js-col-pos" <?= !$showPos ? 'style="display:none"' : '' ?>><?= htmlspecialchars($p['position'] ?? '-') ?></td>

                            <?php endif; ?>

                            <?php if ($showHeight || $showControls): ?>

                                <td class="height-col js-col-height" <?= !$showHeight ? 'style="display:none"' : '' ?>><?= htmlspecialchars($p['height'] ?? '-') ?></td>

                            <?php endif; ?>

                            <?php if ($showWeight || $showControls): ?>

                                <td class="weight-col js-col-weight" <?= !$showWeight ? 'style="display:none"' : '' ?>><?= htmlspecialchars($p['weight'] ?? '-') ?></td>

                            <?php endif; ?>

                            <?php if ($showAge || $showControls): ?>

                                <td class="age-col js-col-age" <?= !$showAge ? 'style="display:none"' : '' ?>><?= htmlspecialchars($p['age'] ?? '-') ?></td>

                            <?php endif; ?>

                            <?php if ($showExp || $showControls): ?>

                                <td class="exp-col js-col-exp" <?= !$showExp ? 'style="display:none"' : '' ?>><?= htmlspecialchars($p['experiance'] ?? '-') ?></td>

                            <?php endif; ?>

                            <?php if ($showSchool || $showControls): ?>

                                <td class="school-col js-col-school" <?= !$showSchool ? 'style="display:none"' : '' ?>><?= htmlspecialchars($p['school'] ?? '-') ?></td>

                            <?php endif; ?>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </details>

</div>

<link rel="stylesheet" href="/pages/sports_anals/nba/lib/partials/roster/pt_roster.css">
<script src="/pages/sports_anals/nba/lib/partials/roster/pt_roster.js"></script>
