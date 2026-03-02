<?php
namespace pages\wrv\food\idc_war;

require_once __DIR__ . '/aIdcWarPage.php';

use lib\basket;
use lib\db\models\wrv\db_idc_war;
use lib\db\models\wrv\db_idc_war_places_place;
use lib\db\models\wrv\db_idc_war_status;

class pg_idc_wars_create extends aIdcWarPage {
    public function getPageTitle() {
        return "I Don't Care Wars - Setup";
    }
}

$db = basket::db_web();
$warModel = new db_idc_war($db);
$warPlacesModel = new db_idc_war_places_place($db);
$statusModel = new db_idc_war_status($db);

$error = null;

// Load all wars and statuses for dropdowns
$allWars = $warModel->readAll();
$allStatuses = $statusModel->readAll();

// Handle Submit Changes (save/update war + entries)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_war'])) {
    $warData = [];
    
    $warName = trim($_POST['war_name'] ?? '');
    if (!empty($warName)) $warData['name'] = $warName;
    
    $warFormat = $_POST['tournament_format'] ?? '';
    if ($warFormat === 'ranked_choice') $warData['fk_idc_war_type'] = 3;
    
    $warDeadline = $_POST['deadline'] ?? '';
    if (!empty($warDeadline)) $warData['deadline'] = $warDeadline;
    
    $warStatus = $_POST['war_status'] ?? '';
    if (!empty($warStatus)) $warData['fk_status'] = (int)$warStatus;
    
    $existingWarPk = $_POST['existing_war_pk'] ?? '';
    
    if (!empty($existingWarPk)) {
        // Update existing war
        $warData['pk'] = (int)$existingWarPk;
        $warModel->write($warData);
        $warPk = (int)$existingWarPk;
    } else {
        // Create new war
        if (!isset($warData['fk_status'])) $warData['fk_status'] = 5; // creation
        if (!isset($warData['fk_idc_war_type'])) $warData['fk_idc_war_type'] = 3;
        $warPk = $warModel->write($warData);
    }
    
    if ($warPk) {
        // Delete all existing entries for this war, then recreate from JS array
        $warPlacesModel->deleteByWarPk($warPk);
        
        $restaurantJson = $_POST['restaurants_json'] ?? '[]';
        $restaurants = json_decode($restaurantJson, true) ?: [];
        
        foreach ($restaurants as $r) {
            $placePk = (int)($r['place_pk'] ?? 0);
            if ($placePk > 0) {
                $warPlacesModel->write($warPk, $placePk);
            }
        }
        
        header('Location: /wrv/food/idc_war/pg_idc_wars_create.php?war=' . $warPk . '&saved=true');
        exit;
    } else {
        $error = "Failed to save the war.";
    }
}

// Determine which war to load (default to empty/new)
$selectedWarPk = $_GET['war'] ?? null;
$selectedWar = null;
$currentEntries = [];

if ($selectedWarPk) {
    $selectedWar = $warModel->readById((int)$selectedWarPk);
    if ($selectedWar) {
        $currentEntries = $warPlacesModel->readByWarPk((int)$selectedWarPk);
    }
}

// Refresh war list after any potential writes
$allWars = $warModel->readAll();

// Build JS-friendly entries array for the partial
$jsEntries = [];
foreach ($currentEntries as $entry) {
    $jsEntries[] = [
        'place_pk' => (int)$entry['fk_places_place'],
        'place_name' => $entry['place_name'],
        'google_place_id' => $entry['google_place_id']
    ];
}
$initialRosterJson = json_encode($jsEntries);
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>I Don't Care (IDC) Wars</h2>
    <p>Create the battlefield and choose the sides.</p>
    
    <!-- War Selector -->
    <div style="margin-bottom: 20px;">
        <label style="font-weight: bold; margin-right: 10px;">Load Existing War:</label>
        <select id="war-selector" onchange="if(this.value) { window.location.href='/wrv/food/idc_war/pg_idc_wars_create.php?war=' + this.value; } else { window.location.href='/wrv/food/idc_war/pg_idc_wars_create.php'; }"
                style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; min-width: 250px;">
            <option value="">-- New War --</option>
            <?php foreach ($allWars as $war): 
                if ($war['fk_status'] == 4 && (!$selectedWar || $selectedWar['pk'] != $war['pk'])) continue; // skip complete wars, unless currently selected
            ?>
                <option value="<?= $war['pk'] ?>" <?= ($selectedWar && $selectedWar['pk'] == $war['pk']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($war['name'] ?: "War #{$war['pk']}") ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div style="background-color: #e6ffe6; color: #008000; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #b3ffb3;">
            War saved successfully!
        </div>
    <?php endif; ?>

    <?php if ($selectedWar): ?>
        <div style="margin-bottom: 20px;">
            <a href="/wrv/food/idc_war/pg_idc_wars_vote.php?war=<?= $selectedWar['pk'] ?>" target="_blank"
               style="display: inline-block; padding: 8px 15px; background-color: #6b4a8e; color: white; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 0.95rem;">
                🔗 Go to Voting Page
            </a>
        </div>
    <?php endif; ?>

    <form method="POST" action="/wrv/food/idc_war/pg_idc_wars_create.php" id="war-form">
        <input type="hidden" name="existing_war_pk" value="<?= $selectedWar ? $selectedWar['pk'] : '' ?>">
        
        <!-- Tournament Setup -->
        <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
            <h3>Tournament Setup</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Name:</label>
                    <input type="text" name="war_name" placeholder="e.g. Friday Lunch War" 
                           style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;"
                           value="<?= htmlspecialchars($selectedWar['name'] ?? '') ?>">
                </div>
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Format:</label>
                    <select name="tournament_format" style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;">
                        <option value="ranked_choice">Ranked Choice Voting (One Round)</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">
                        Deadline <span style="font-weight: normal; color: #666; font-size: 0.85rem;">(Input as UTC)</span>:
                    </label>
                    <?php $defaultDeadline = gmdate('Y-m-d\TH:i', strtotime('+1 hour')); ?>
                    <input type="datetime-local" name="deadline" id="deadline-input"
                           style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;"
                           value="<?= $selectedWar && $selectedWar['deadline'] ? gmdate('Y-m-d\TH:i', strtotime($selectedWar['deadline'])) : $defaultDeadline ?>">
                    <div id="local-time-display" style="color: #0066cc; font-size: 0.9rem; margin-top: 5px;"></div>
                    <script>
                        (function() {
                            var input = document.getElementById('deadline-input');
                            var display = document.getElementById('local-time-display');
                            
                            function updateDisplay() {
                                if (!input.value) {
                                    display.textContent = '';
                                    return;
                                }
                                // Treat the input value as UTC by appending 'Z'
                                var dateObj = new Date(input.value + 'Z');
                                if (isNaN(dateObj.getTime())) {
                                    display.textContent = '';
                                    return;
                                }
                                // Format using the browser's local timezone
                                var options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', timeZoneName: 'short' };
                                display.textContent = 'Local Time: ' + dateObj.toLocaleString(undefined, options);
                            }

                            input.addEventListener('input', updateDisplay);
                            input.addEventListener('change', updateDisplay);
                            // Initial call
                            updateDisplay();
                        })();
                    </script>
                </div>
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Status:</label>
                    <select name="war_status" style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;">
                        <?php foreach ($allStatuses as $status): 
                            // Only allow creation (5), rnd1 (1), complete (4)
                            if (!in_array($status['pk'], [1, 4, 5])) continue;
                        ?>
                            <option value="<?= $status['pk'] ?>" <?= ($selectedWar && $selectedWar['fk_status'] == $status['pk']) || (!$selectedWar && $status['pk'] == 5) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['status']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Hidden input synced by the partial's JS -->
        <input type="hidden" name="restaurants_json" id="restaurants-json" value='<?= htmlspecialchars($initialRosterJson) ?>'>

    </form>

    <!-- Restaurant Search + Roster (OUTSIDE the war form to avoid nested form issues) -->
    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
        <h3>Restaurants</h3>

        <!-- Roster managed by JS -->
        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #ddd;">
            <h3>Current Roster (<span id="roster-count">0</span>)</h3>
            <div id="roster-list">
                <p style="color: #999; font-style: italic;">No restaurants added yet.</p>
            </div>
        </div>

        <?php 
        $canAddRestaurants = (!$selectedWar || $selectedWar['fk_status'] == 5);
        $searchArgs = [
            'error'             => $error,
            'canAddRestaurants' => $canAddRestaurants
        ];
        echo basket::render('pages/wrv/food/lib/partials/pt_search_places.php', $searchArgs); 
        ?>
    </div>

    <!-- Bottom Buttons (in their own form so they can submit the war data) -->
    <form method="POST" action="/wrv/food/idc_war/pg_idc_wars_create.php" id="war-submit-form">
        <input type="hidden" name="existing_war_pk" id="submit-existing-war-pk" value="<?= $selectedWar ? $selectedWar['pk'] : '' ?>">
        <input type="hidden" name="restaurants_json" id="submit-restaurants-json" value='<?= htmlspecialchars($initialRosterJson) ?>'>
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
            <a href="/wrv/food/idc_war/pg_idc_wars_create.php" 
               style="padding: 10px 25px; background-color: #888; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block;">
                New / Clear
            </a>
            <button type="submit" name="submit_war" value="1"
                    onclick="syncSubmitForm()" 
                    style="padding: 10px 25px; background-color: #38827e; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer;">
                Submit Changes
            </button>
        </div>
    </form>

    <script>
    var roster = <?= $initialRosterJson ?? '[]' ?>;
    var canAddRestaurants = <?= json_encode($canAddRestaurants ?? true) ?>;

    // Listen for place selection from the partial
    document.addEventListener('placeSelected', function(e) {
        var data = e.detail;
        
        // Check for duplicate
        for (var i = 0; i < roster.length; i++) {
            if (roster[i].google_place_id === data.id) {
                alert(data.name + ' is already in the roster!');
                return;
            }
        }
        roster.push({
            place_pk: data.pk,
            place_name: data.name,
            google_place_id: data.id
        });
        renderRoster();
    });

    // --- Roster rendering ---
    function renderRoster() {
        var list = document.getElementById('roster-list');
        var count = document.getElementById('roster-count');
        var jsonInput = document.getElementById('restaurants-json');

        count.textContent = roster.length;
        if (jsonInput) jsonInput.value = JSON.stringify(roster);
        // Also sync to submit form if it exists
        var submitJson = document.getElementById('submit-restaurants-json');
        if (submitJson) submitJson.value = JSON.stringify(roster);

        if (roster.length === 0) {
            list.innerHTML = '<p style="color: #999; font-style: italic;">No restaurants added yet.</p>';
            return;
        }

        var html = '<div style="display: flex; flex-direction: column; gap: 10px;">';
        roster.forEach(function(r, i) {
            html += '<div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;">';
            html += '<div>';
            html += '<span style="font-weight: bold; color: #6b4a8e; margin-right: 8px;">' + (i + 1) + '.</span>';
            html += '<span style="font-size: 1.05em; color: #333;">' + escapeHtml(r.place_name) + '</span>';
            html += '</div>';
            if (canAddRestaurants) {
                html += '<button type="button" onclick="rosterRemove(' + i + ')" style="padding: 5px 12px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85em;">✕</button>';
            }
            html += '</div>';
        });
        html += '</div>';
        list.innerHTML = html;
    }

    window.rosterRemove = function(index) {
        roster.splice(index, 1);
        renderRoster();
    };

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // Initial render
    renderRoster();

    function syncSubmitForm() {
        // Copy values from the main war form into the submit form
        var warForm = document.getElementById('war-form');
        var submitForm = document.getElementById('war-submit-form');
        
        // Copy all named inputs from the war form
        var inputs = warForm.querySelectorAll('input[name], select[name]');
        inputs.forEach(function(input) {
            var existing = submitForm.querySelector('[name="' + input.name + '"]');
            if (existing) {
                existing.value = input.value;
            } else {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = input.name;
                hidden.value = input.tagName === 'SELECT' ? input.options[input.selectedIndex].value : input.value;
                submitForm.appendChild(hidden);
            }
        });
        
        // Sync the roster JSON
        var rosterJson = document.getElementById('restaurants-json');
        document.getElementById('submit-restaurants-json').value = rosterJson.value;
    }
    </script>
</div>
