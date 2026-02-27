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
namespace pages\wrv\food\food_war\lib\ajax;

require_once __DIR__ . '/../../aFoodWarPage.php';

use lib\basket;
use lib\db\models\wrv\db_places_place;

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
