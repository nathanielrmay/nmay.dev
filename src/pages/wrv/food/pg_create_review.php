<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;
use lib\api\places\placesApiClient;
use lib\db\models\wrv\db_places_place;

class pg_create_review extends aFoodPage {
    public function getPageTitle() {
        return "Find Restaurant - Wilma's Reviews";
    }
}

$searchResults = [];
$error = null;
$savedPlace = null;

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

// Handle Place Selection & Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_id']) && isset($_POST['place_name'])) {
    $placeId = $_POST['place_id'];
    $placeName = $_POST['place_name'];
    
    $db = basket::db_web();
    $model = new db_places_place($db);
    
    // Check if it already exists
    $existing = $model->readById($placeId);
    
    if ($existing) {
        $savedPlace = $existing;
    } else {
        // Save new place
        $newPk = $model->write([
            'id' => $placeId,
            'name' => $placeName
        ]);
        
        if ($newPk) {
            $savedPlace = ['pk' => $newPk, 'id' => $placeId, 'name' => $placeName];
        } else {
            $error = "Failed to save restaurant to the database.";
        }
    }
}
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>Find a Restaurant</h2>
    
    <?php if ($error): ?>
        <div style="background-color: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($savedPlace): ?>
        <div style="background-color: #e6ffe6; color: #008000; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3ffb3;">
            <strong>Success!</strong> Selected <em><?= htmlspecialchars($savedPlace['name']) ?></em>. 
            <br>
            <span style="font-size: 0.9em; color: #666;">(Database ID: <?= $savedPlace['pk'] ?>, Google ID: <?= htmlspecialchars($savedPlace['id']) ?>)</span>
            <br><br>
            <p><em>(The actual review form fields will go here in the next iteration.)</em></p>
            <a href="/wrv/food/pg_create_review.php" style="color: #38827e; font-weight: bold;">Start over</a>
        </div>
    <?php else: ?>

        <!-- Search Form -->
        <form method="POST" action="/wrv/food/pg_create_review.php" style="margin-bottom: 30px; display: flex; gap: 10px;">
            <input type="text" name="search_query" placeholder="Enter restaurant name (e.g. Taco Bell)" required 
                   style="flex: 2; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px;"
                   value="<?= htmlspecialchars($_POST['search_query'] ?? '') ?>">
            <input type="text" name="location" placeholder="Location" required 
                   style="flex: 1; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px;"
                   value="<?= htmlspecialchars($_POST['location'] ?? 'Springfield, Missouri') ?>">
            <button type="submit" style="padding: 10px 20px; background-color: #6b4a8e; color: white; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer;">
                Search Maps
            </button>
        </form>

        <!-- Search Results -->
        <?php if (!empty($searchResults)): ?>
            <h3>Results</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($searchResults as $result): ?>
                    <div style="border: 1px solid #eee; padding: 15px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; background-color: #fafafa;">
                        <div>
                            <strong style="font-size: 1.1em; color: #333;"><?= htmlspecialchars($result['name']) ?></strong>
                            <div style="color: #666; font-size: 0.9em; margin-top: 5px;">
                                <?= htmlspecialchars($result['formatted_address']) ?>
                            </div>
                        </div>
                        <form method="POST" action="/wrv/food/pg_create_review.php" style="margin: 0;">
                            <input type="hidden" name="place_id" value="<?= htmlspecialchars($result['place_id']) ?>">
                            <input type="hidden" name="place_name" value="<?= htmlspecialchars($result['name']) ?>">
                            <button type="submit" style="padding: 8px 15px; background-color: #38827e; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                Select
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])): ?>
            <p style="color: #666;">No results found for that search.</p>
        <?php endif; ?>

    <?php endif; ?>
</div>
