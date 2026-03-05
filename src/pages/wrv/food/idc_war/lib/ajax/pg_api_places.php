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
    $placePk = false;
    $created = false;

    if ($existing) {
        $placePk = (int)$existing['pk'];
        $created = false;
    }
    else {
        $placePk = $model->write([
            'id' => $googlePlaceId,
            'name' => $placeName
        ]);
        $created = true;
    }

    if ($placePk) {
        $debugError = null;
        // Fetch extended details from Google to cache (always fetches when selected to get full data)
        try {
            $config = basket::config();
            $apiKey = $config['google']['places_api_key'] ?? '';

            if ($apiKey) {
                $client = new placesApiClient($apiKey);
                $response = $client->getPlaceDetails($googlePlaceId);
                $details = $response['result'] ?? null;

                if ($details) {
                    $cacheModel = new db_places_cache($db);

                    // Add the formatted_phone_number to phone_number so it matches what write() expects
                    $details['phone_number'] = $details['formatted_phone_number'] ?? null;

                    $res = $cacheModel->write((int)$placePk, $details);
                    if ($res === false) {
                        $debugError = "db_places_cache write() returned false; Check error_log for PDOException.";
                    }
                }
                else {
                    $debugError = "Google API returned no result array: " . json_encode($response);
                }
            }
        }
        catch (\Exception $ex) {
            $debugError = $ex->getMessage();
            error_log("Failed to cache place details for $googlePlaceId: " . $ex->getMessage());
        }

        if ($debugError) {
            echo json_encode(['error' => 'Cache error: ' . $debugError]);
            exit;
        }

        echo json_encode([
            'pk' => (int)$placePk,
            'id' => $googlePlaceId,
            'name' => $placeName,
            'created' => $created
        ]);
    }
    else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save place']);
    }
}
catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
