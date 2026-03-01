<?php
namespace pages\wrv\food\idc_war;

require_once __DIR__ . '/aIdcWarPage.php';

use lib\basket;
use lib\db\models\wrv\db_idc_war;
use lib\db\models\wrv\db_idc_war_places_place;
use lib\db\models\wrv\db_idc_war_vote;
use lib\db\models\wrv\db_idc_war_status;

class pg_idc_wars_vote extends aIdcWarPage {
    public function getPageTitle() {
        return "I Don't Care Wars - Vote";
    }
}

$db = basket::db_web();
$warModel = new db_idc_war($db);
$warPlacesModel = new db_idc_war_places_place($db);
$voteModel = new db_idc_war_vote($db);

$warPk = $_GET['war'] ?? $_POST['war_pk'] ?? null;
$error = null;
$success = false;

// If no war is specified, render the War Selection UI instead of failing
if (!$warPk) {
    $statusModel = new db_idc_war_status($db);
    $allWars = $warModel->readAll();
    $allStatuses = $statusModel->readAll();

    // Mapping for JS and rendering
    $statusMap = [];
    foreach ($allStatuses as $status) {
        $statusMap[$status['pk']] = $status['status'];
    }

    // Capture HTML for the selection UI and exit early
    ?>
    <div style="padding: 20px; max-width: 800px;">
        <h2>Select a War to Settle </h2>
        <p>Choose an active war below to cast your vote.</p>

        <!-- Filters -->
        <div style="background-color: #fafafa; padding: 15px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Filter by Name:</label>
                <input type="text" id="filter-name" placeholder="Search..." style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%;">
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Filter by Status:</label>
                <select id="filter-status" style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%;">
                    <option value="all">-- All Statuses --</option>
                    <?php foreach ($allStatuses as $status): ?>
                        <option value="<?= $status['pk'] ?>" <?= $status['pk'] == 1 ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status['status']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Render List of Wars -->
        <div id="war-selection-list">
            <?php if (empty($allWars)): ?>
                <p>No wars have been created yet.</p>
            <?php else: ?>
                <ul style="list-style: none; padding-left: 0; margin: 0;">
                    <?php foreach ($allWars as $war): 
                        $statusName = $statusMap[$war['fk_status']] ?? 'Unknown';
                        $statusColor = '#555';
                        if ($war['fk_status'] == 1) $statusColor = '#008000'; // rnd 1
                        if ($war['fk_status'] == 4) $statusColor = '#888888'; // complete
                        if ($war['fk_status'] == 5) $statusColor = '#e68a00'; // creation
                    ?>
                        <li class="war-card" data-name="<?= htmlspecialchars(strtolower($war['name'])) ?>" data-status="<?= $war['fk_status'] ?>"
                            style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background-color: #fafafa; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin-top: 0; margin-bottom: 5px;"><?= htmlspecialchars($war['name'] ?: "War #{$war['pk']}") ?></h3>
                                <p style="margin: 0; font-size: 0.9em; color: #444;">
                                    Status: <span style="font-weight: bold; color: <?= $statusColor ?>;"><?= htmlspecialchars($statusName) ?></span> <br>
                                    Deadline: <?= $war['deadline'] ? date('M j, Y, t:i a', strtotime($war['deadline'])) . ' UTC' : 'None' ?>
                                </p>
                            </div>
                            <div>
                                <?php if ($war['fk_status'] == 1): // Voting open ?>
                                    <a href="/wrv/food/idc_war/pg_idc_wars_vote.php?war=<?= $war['pk'] ?>" style="padding: 8px 16px; background-color: #6b4a8e; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">Vote</a>
                                <?php elseif ($war['fk_status'] == 4): // Complete ?>
                                    <a href="/wrv/food/idc_war/pg_idc_wars_vote.php?war=<?= $war['pk'] ?>" style="padding: 8px 16px; background-color: #38827e; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">Results</a>
                                <?php else: // Creation or other ?>
                                    <a href="/wrv/food/idc_war/pg_idc_wars_create.php?war=<?= $war['pk'] ?>" style="padding: 8px 16px; background-color: #eee; color: #333; text-decoration: none; border-radius: 4px; font-weight: bold; border: 1px solid #ccc;">Manage</a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Vanilla JS to control the filtering instantly -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('filter-name');
            const statusSelect = document.getElementById('filter-status');
            const warCards = document.querySelectorAll('.war-card');

            function filterWars() {
                const nameQuery = nameInput.value.toLowerCase();
                const statusQuery = statusSelect.value;

                warCards.forEach(card => {
                    const cardName = card.getAttribute('data-name');
                    const cardStatus = card.getAttribute('data-status');
                    
                    const matchesName = cardName.includes(nameQuery);
                    const matchesStatus = statusQuery === 'all' || statusQuery === cardStatus;

                    if (matchesName && matchesStatus) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            nameInput.addEventListener('input', filterWars);
            statusSelect.addEventListener('change', filterWars);
            
            // Run once on load to respect the default 'rnd1' selection
            filterWars();
        });
    </script>
    <?php
    return;
}

$war = $warModel->readById((int)$warPk);
if (!$war) {
    echo "<div style='padding: 20px;'><h3>Error: War not found.</h3></div>";
    return;
}

// 1 = rnd1 status
if ($war['fk_status'] != 1) {
    echo "<div style='padding: 20px;'><h3>Voting is currently closed for this war.</h3></div>";
    return;
}

// Handle Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_vote'])) {
    $rankingsJson = $_POST['rankings_json'] ?? '[]';
    $rankings = json_decode($rankingsJson, true);
    
    if (is_array($rankings) && count($rankings) > 0) {
        // Validate that user hasn't tampered with PKs by checking against known entries
        $currentEntries = $warPlacesModel->readByWarPk((int)$warPk);
        $validPks = array_column($currentEntries, 'entry_pk');
        
        $cleanRankings = [];
        foreach ($rankings as $pk) {
            $pk = (int)$pk;
            if (in_array($pk, $validPks)) {
                $cleanRankings[] = $pk;
            }
        }
        
        // Save vote
        // fk_user = null for now (anonymous)
        if ($voteModel->write((int)$warPk, null, $cleanRankings)) {
            $success = true;
        } else {
            $error = "Failed to submit your vote. Please try again.";
        }
    } else {
        $error = "Invalid rankings data.";
    }
}

// Load choices
$entries = $warPlacesModel->readByWarPk((int)$warPk);
if (empty($entries)) {
    echo "<div style='padding: 20px;'><h3>No restaurants have been added to this war yet.</h3></div>";
    return;
}
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>🍔 Vote: <?= htmlspecialchars($war['name'] ?: "War #{$war['pk']}") ?> ⚔️</h2>
    <p>Rank the following restaurants from best (top) to worst (bottom). Drag and drop or use the arrows to reorder.</p>

    <?php if ($success): ?>
        <div style="background-color: #e6ffe6; color: #008000; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3ffb3; font-weight: bold;">
            Vote submitted successfully! Thank you.
        </div>
    <?php elseif ($error): ?>
        <div style="background-color: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
        <form method="POST" action="/wrv/food/idc_war/pg_idc_wars_vote.php?war=<?= htmlspecialchars($warPk) ?>" id="vote-form">
            <input type="hidden" name="war_pk" value="<?= htmlspecialchars($warPk) ?>">
            <input type="hidden" name="rankings_json" id="rankings-json" value="">

            <div id="vote-list" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 30px;">
                <?php foreach ($entries as $index => $entry): ?>
                    <div class="vote-item" data-pk="<?= htmlspecialchars($entry['entry_pk']) ?>" draggable="true" 
                         style="display: flex; align-items: center; justify-content: space-between; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 6px; cursor: grab;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="rank-number" style="font-weight: bold; font-size: 1.2rem; color: #6b4a8e; width: 30px; text-align: center;">
                                <?= $index + 1 ?>
                            </span>
                            <span style="font-size: 1.1rem; font-weight: 500;">
                                <?= htmlspecialchars($entry['place_name']) ?>
                            </span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <button type="button" class="move-up" style="background: none; border: 1px solid #ccc; border-radius: 3px; cursor: pointer; padding: 2px 8px;">▲</button>
                            <button type="button" class="move-down" style="background: none; border: 1px solid #ccc; border-radius: 3px; cursor: pointer; padding: 2px 8px;">▼</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" name="submit_vote" value="1" 
                        style="padding: 12px 30px; background-color: #38827e; color: white; border: none; border-radius: 5px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background-color 0.2s;">
                    Submit Vote
                </button>
            </div>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('vote-list');
            const items = list.querySelectorAll('.vote-item');
            
            // Setup Drag & Drop
            let draggedItem = null;

            items.forEach(item => {
                item.addEventListener('dragstart', function(e) {
                    draggedItem = this;
                    setTimeout(() => this.style.opacity = '0.5', 0);
                });

                item.addEventListener('dragend', function() {
                    draggedItem.style.opacity = '1';
                    draggedItem = null;
                    updateRanks();
                });

                item.addEventListener('dragover', function(e) {
                    e.preventDefault();
                });

                item.addEventListener('dragenter', function(e) {
                    e.preventDefault();
                    if (this !== draggedItem) {
                        this.style.border = '2px dashed #6b4a8e';
                    }
                });

                item.addEventListener('dragleave', function() {
                    if (this !== draggedItem) {
                        this.style.border = '1px solid #ddd';
                    }
                });

                item.addEventListener('drop', function() {
                    this.style.border = '1px solid #ddd';
                    if (this !== draggedItem) {
                        // Determine if dropping before or after
                        let allNodes = Array.from(list.querySelectorAll('.vote-item'));
                        let draggedIdx = allNodes.indexOf(draggedItem);
                        let targetIdx = allNodes.indexOf(this);
                        
                        if (draggedIdx < targetIdx) {
                            this.after(draggedItem);
                        } else {
                            this.before(draggedItem);
                        }
                    }
                });

                // Setup Up/Down Buttons
                const btnUp = item.querySelector('.move-up');
                const btnDown = item.querySelector('.move-down');

                btnUp.addEventListener('click', function(e) {
                    e.preventDefault();
                    const prev = item.previousElementSibling;
                    if (prev) {
                        prev.before(item);
                        updateRanks();
                    }
                });

                btnDown.addEventListener('click', function(e) {
                    e.preventDefault();
                    const next = item.nextElementSibling;
                    if (next) {
                        next.after(item);
                        updateRanks();
                    }
                });
            });

            // Update visible numbers and prepare hidden input
            function updateRanks() {
                const currentItems = list.querySelectorAll('.vote-item');
                const rankings = [];
                currentItems.forEach((el, idx) => {
                    const numSpan = el.querySelector('.rank-number');
                    numSpan.textContent = idx + 1; // 1-based index
                    rankings.push(el.getAttribute('data-pk'));
                });
                document.getElementById('rankings-json').value = JSON.stringify(rankings);
            }

            // Bind form submit to ensure final order is captured
            document.getElementById('vote-form').addEventListener('submit', function() {
                updateRanks();
                // Avoid double submission
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
            });
            
            // Init
            updateRanks();
        });
        </script>
    <?php endif; ?>
</div>
