<?php
/**
 * AJAX endpoint for resolving/creating places.
 * 
 * Receives POST with place_id (Google) and place_name.
 * Returns JSON with the place record (pk, id, name).
 *
 * This file lives in the pages directory so the front controller
 * routes to it, but we output JSON and exit before the layout renders.
 */
namespace pages\wrv\food\idc_war\lib\ajax;

require_once __DIR__ . '/../../aIdcWarPage.php';

use lib\basket;
use lib\db\models\wrv\db_places_place;
use lib\db\models\wrv\db_places_cache;
use lib\api\places\placesApiClient;

// Only handle POST AJAX requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

header('Content-Type: application/json');

try {
    // Read raw JSON body or form data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        // Fallback to form POST data
        $input = $_POST;
    }

    $googlePlaceId = trim($input['place_id'] ?? '');
    $placeName = trim($input['place_name'] ?? '');

    if (empty($googlePlaceId) || empty($placeName)) {
        http_response_code(400);
        echo json_encode(['error' => 'place_id and place_name are required']);
        exit;
    }

    $db = basket::db_web();
    $model = new db_places_place($db);

    // Check if place already exists
    $existing = $model->readById($googlePlaceId);

    if ($existing) {
        echo json_encode([
            'pk' => (int)$existing['pk'],
            'id' => $existing['id'],
            'name' => $existing['name'],
            'created' => false
        ]);
        exit;
    }

    // Create new
    $newPk = $model->write([
        'id' => $googlePlaceId,
        'name' => $placeName
    ]);

    if ($newPk) {
        // Try to fetch extended details from Google to cache
        try {
            $config = basket::config();
            $apiKey = $config['google']['places_api_key'] ?? '';
            
            if ($apiKey) {
                $client = new placesApiClient($apiKey);
                $response = $client->getPlaceDetails($googlePlaceId);
                $details = $response['result'] ?? null;
                
                if ($details) {
                    $cacheModel = new db_places_cache($db);
                    $cacheModel->write((int)$newPk, [
                        'formatted_address' => $details['formatted_address'] ?? null,
                        'phone_number' => $details['formatted_phone_number'] ?? null,
                        'website' => $details['website'] ?? null,
                        'price_level' => isset($details['price_level']) ? (int)$details['price_level'] : null,
                        'rating' => isset($details['rating']) ? (float)$details['rating'] : null,
                        'user_ratings_total' => isset($details['user_ratings_total']) ? (int)$details['user_ratings_total'] : null,
                    ]);
                }
            }
        } catch (\Exception $ex) {
            error_log("Failed to cache place details for $googlePlaceId: " . $ex->getMessage());
        }

        echo json_encode([
            'pk' => (int)$newPk,
            'id' => $googlePlaceId,
            'name' => $placeName,
            'created' => true
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save place']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
