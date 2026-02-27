<?php
namespace pages\wrv\food\idc_war;

require_once __DIR__ . '/aIdcWarPage.php';

use lib\basket;
use lib\db\models\wrv\db_idc_war;
use lib\db\models\wrv\db_idc_war_places_place;
use lib\db\models\wrv\db_idc_war_vote;

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

if (!$warPk) {
    echo "<div style='padding: 20px;'><h3>Error: No war specified.</h3></div>";
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
        $validPks = array_column($currentEntries, 'pk');
        
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
                    <div class="vote-item" data-pk="<?= htmlspecialchars($entry['pk']) ?>" draggable="true" 
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
