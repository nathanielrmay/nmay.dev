<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;
use lib\api\places\placesApiClient;
use lib\db\models\wrv\db_idc_war;
use lib\db\models\wrv\db_idc_war_entry;
use lib\db\models\wrv\db_places_place;

class pg_idc_wars extends aFoodPage {
    public function getPageTitle() {
        return "I Don't Care Wars - Setup";
    }
}

$searchResults = [];
$error = null;

// Temporary hardcoded default until we build out the full tournament lifecycle UI
$currentWarPk = 1; 

$db = basket::db_web();
$entryModel = new db_idc_war_entry($db);

// Handle Remove Entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_entry_pk'])) {
    $entryModel->deleteEntry((int)$_POST['remove_entry_pk']);
    header('Location: /wrv/food/pg_idc_wars.php?removed=true');
    exit;
}

// Handle Search Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])) {
    $query = trim($_POST['search_query']);
    $location = trim($_POST['location'] ?? 'Springfield, Missouri');
    
    if (!empty($query)) {
        try {
            $fullQuery = !empty($location) ? $query . ' in ' . $location : $query;
            
            $config = basket::config();
            $apiKey = $config['google']['places_api_key'] ?? '';
            
            if (empty($apiKey)) {
                $error = "Google Places API key is missing from config.php.";
            } else {
                $client = new placesApiClient($apiKey);
                $response = $client->searchByText($fullQuery);
                $searchResults = $response['results'] ?? [];
            }
        } catch (\Exception $e) {
            $error = "Error searching API: " . $e->getMessage();
        }
    }
}

// Handle Place Selection & Save to Tournament
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_id']) && isset($_POST['place_name'])) {
    $placeId = $_POST['place_id'];
    $placeName = $_POST['place_name'];
    
    $placesModel = new db_places_place($db);
    
    // Check if the place already exists in the master places table
    $existingPlace = $placesModel->readById($placeId);
    $placePk = null;
    
    if ($existingPlace) {
        $placePk = $existingPlace['pk'];
    } else {
        // Save new place
        $placePk = $placesModel->write([
            'id' => $placeId,
            'name' => $placeName
        ]);
    }
    
    if ($placePk) {
        $newEntryPk = $entryModel->write([
            'fk_idc_war' => $currentWarPk,
            'fk_places_place' => $placePk
        ]);
        
        if (!$newEntryPk) {
            $error = "Failed to add restaurant to the tournament.";
        } else {
            header('Location: /wrv/food/pg_idc_wars.php?added=true');
            exit;
        }
    } else {
        $error = "Failed to save restaurant to the global database.";
    }
}

// Load current entries for this tournament
$currentEntries = $entryModel->readEntriesForWar($currentWarPk);
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>I Don't Care (IDC) Wars ⚔️🍔</h2>
    <p>For when nobody can make up their mind on where to eat. Set up a tournament, build a list of restaurants, and let the games decide!</p>
    
    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
        <h3>1. Tournament Setup</h3>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Format:</label>
                <select name="tournament_format" style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;">
                    <option value="round_robin">Round Robin</option>
                    <option value="ranked_choice">Ranked Choice Voting (One Round)</option>
                </select>
            </div>
            <div>
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Deadline:</label>
                <input type="datetime-local" name="deadline" 
                       style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;">
            </div>
        </div>
    </div>

    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
        <h3>2. Add Restaurants</h3>
        <?php if (isset($_GET['added'])): ?>
            <div style="background-color: #e6ffe6; color: #008000; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #b3ffb3;">
                Restaurant added successfully!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['removed'])): ?>
            <div style="background-color: #fff3e0; color: #e65100; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #ffe0b2;">
                Restaurant removed.
            </div>
        <?php endif; ?>
        
        <?php 
        $searchArgs = [
            'searchResults'  => $searchResults,
            'searchQuery'    => $_POST['search_query'] ?? '',
            'searchLocation' => $_POST['location'] ?? 'Springfield, Missouri',
            'formAction'     => '/wrv/food/pg_idc_wars.php',
            'error'          => $error
        ];
        echo basket::render('pages/wrv/food/lib/partials/pt_search_places.php', $searchArgs); 
        ?>
    </div>

    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
        <h3>3. Current Roster (<?= count($currentEntries) ?>)</h3>
        <?php if (empty($currentEntries)): ?>
            <p style="color: #999; font-style: italic;">No restaurants added yet. Use the search above to add some!</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($currentEntries as $i => $entry): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;">
                        <div>
                            <span style="font-weight: bold; color: #6b4a8e; margin-right: 8px;"><?= $i + 1 ?>.</span>
                            <span style="font-size: 1.05em; color: #333;"><?= htmlspecialchars($entry['place_name']) ?></span>
                        </div>
                        <form method="POST" action="/wrv/food/pg_idc_wars.php" style="margin: 0;">
                            <input type="hidden" name="remove_entry_pk" value="<?= $entry['entry_pk'] ?>">
                            <button type="submit" style="padding: 5px 12px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85em;" onclick="return confirm('Remove this restaurant?');">
                                ✕
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
