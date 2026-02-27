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
    
        <?php 
        $searchArgs = [
            'searchResults'  => $searchResults,
            'searchQuery'    => $_POST['search_query'] ?? '',
            'searchLocation' => $_POST['location'] ?? 'Springfield, Missouri',
            'formAction'     => '/wrv/food/pg_create_review.php',
            'error'          => $error
        ];
        echo basket::render('pages/wrv/food/lib/partials/pt_search_places.php', $searchArgs); 
        ?>
</div>
