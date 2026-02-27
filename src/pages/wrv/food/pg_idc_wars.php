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
$createdWar = null;

// Temporary hardcoded default until we build out the full tournament lifecycle UI
$currentWarPk = 1; 

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
    
    $db = basket::db_web();
    $placesModel = new db_places_place($db);
    $entryModel = new db_idc_war_entry($db);
    
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
            // Force a reload to clear POST data and show success state (if we had one)
            header('Location: /wrv/food/pg_idc_wars.php?added=true');
            exit;
        }
    } else {
        $error = "Failed to save restaurant to the global database.";
    }
}
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>I Don't Care (IDC) Wars ⚔️🍔</h2>
    <p>For when nobody can make up their mind on where to eat. Set up a tournament, build a list of restaurants, and let the games decide!</p>
    
    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
        <h3>1. Tournament Format</h3>
        <form method="POST" action="" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Select Format:</label>
                <select name="tournament_format" style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;">
                    <option value="bracket">Tournament Bracket</option>
                    <option value="round_robin">Round Robin</option>
                    <option value="ranked_choice">Ranked Choice Voting (One Round)</option>
                </select>
            </div>
            
            <!-- In the future, this button will save the tournament to the database and redirect to a management page -->
            <button type="submit" name="start_setup" style="padding: 10px 20px; background-color: #38827e; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer; width: fit-content;">
                Start Setup
            </button>
        </form>
    </div>

    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
        <h3>2. Add Restaurants to Tournament #<?= htmlspecialchars($currentWarPk) ?></h3>
        <?php if (isset($_GET['added'])): ?>
            <div style="background-color: #e6ffe6; color: #008000; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #b3ffb3;">
                Restaurant added successfully!
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
</div>
