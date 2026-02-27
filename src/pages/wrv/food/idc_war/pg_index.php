<?php
namespace pages\wrv\food\idc_war;

require_once __DIR__ . '/aIdcWarPage.php';

use lib\basket;
use lib\db\models\wrv\db_idc_war;
use lib\db\models\wrv\db_idc_war_status;

class pg_index extends aIdcWarPage {
    public function getPageTitle() {
        return "I Don't Care Wars - Home";
    }
}

$db = basket::db_web();
$warModel = new db_idc_war($db);
$statusModel = new db_idc_war_status($db);

$allWars = $warModel->readAll();
$allStatuses = $statusModel->readAll();

// Create a map of status pk to status string
$statusMap = [];
foreach ($allStatuses as $status) {
    $statusMap[$status['pk']] = $status['status'];
}

?>

<div style="padding: 20px; max-width: 800px;">
    <h2>I Don't Care Wars (IDC Wars) ⚔️🍔</h2>
    <p>Welcome to the battleground. Select a war to participate in or manage.</p>

    <div style="margin-top: 30px;">
        <?php if (empty($allWars)): ?>
            <p>No wars have been created yet. <a href="/wrv/food/idc_war/pg_idc_wars_create.php" style="color: #6b4a8e; font-weight: bold;">Create one now!</a></p>
        <?php else: ?>
            <ul style="list-style: none; padding-left: 0;">
                <?php foreach ($allWars as $war): 
                    $statusName = $statusMap[$war['fk_status']] ?? 'Unknown';
                    // Status coloring
                    $statusColor = '#555';
                    if ($war['fk_status'] == 1) $statusColor = '#008000'; // rnd 1
                    if ($war['fk_status'] == 4) $statusColor = '#888888'; // complete
                    if ($war['fk_status'] == 5) $statusColor = '#e68a00'; // creation
                ?>
                    <li style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background-color: #fafafa;">
                        <h3 style="margin-top: 0; margin-bottom: 10px;"><?= htmlspecialchars($war['name'] ?: "War #{$war['pk']}") ?></h3>
                        <p style="margin: 0 0 10px 0; font-size: 0.95em; color: #444;">
                            Status: <span style="font-weight: bold; color: <?= $statusColor ?>;"><?= htmlspecialchars($statusName) ?></span> <br>
                            Deadline: <?= $war['deadline'] ? date('M j, Y, t:i a', strtotime($war['deadline'])) . ' UTC' : 'None' ?>
                        </p>
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <?php if ($war['fk_status'] == 1): // Voting open ?>
                                <a href="/wrv/food/idc_war/pg_idc_wars_vote.php?war=<?= $war['pk'] ?>" style="padding: 6px 12px; background-color: #6b4a8e; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">Vote Now</a>
                            <?php elseif ($war['fk_status'] == 4): // Complete ?>
                                <a href="/wrv/food/idc_war/pg_idc_wars_vote.php?war=<?= $war['pk'] ?>" style="padding: 6px 12px; background-color: #38827e; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">View Results</a>
                            <?php endif; ?>
                            <a href="/wrv/food/idc_war/pg_idc_wars_create.php?war=<?= $war['pk'] ?>" style="padding: 6px 12px; background-color: #eee; color: #333; text-decoration: none; border-radius: 4px; border: 1px solid #ccc; font-weight: 500;">Manage</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
