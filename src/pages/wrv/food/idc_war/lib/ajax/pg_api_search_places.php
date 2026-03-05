<?php
/**
 * AJAX endpoint for searching Google Places.
 * 
 * Receives POST JSON: { query: "taco bell", location: "Springfield, Missouri" }
 * Returns JSON: { results: [{ name, formatted_address, place_id }, ...] }
 */
namespace pages\wrv\food\idc_war\lib\ajax;

require_once __DIR__ . '/../../aIdcWarPage.php';

use lib\basket;
use lib\api\places\placesApiClient;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input)
        $input = $_POST;

    $query = trim($input['query'] ?? '');
    $location = trim($input['location'] ?? '');

    if (empty($query)) {
        http_response_code(400);
        echo json_encode(['error' => 'query is required']);
        exit;
    }

    $fullQuery = !empty($location) ? $query . ' in ' . $location : $query;

    $config = basket::config();
    $apiKey = $config['google']['places_api_key'] ?? '';

    if (empty($apiKey)) {
        http_response_code(500);
        echo json_encode(['error' => 'Google Places API key is missing from config.php']);
        exit;
    }

    $client = new placesApiClient($apiKey);
    $response = $client->searchByText($fullQuery);
    $results = $response['results'] ?? [];

    $db = basket::db_web();
    $modelPlace = new \lib\db\models\wrv\db_places_place($db);
    $modelCache = new \lib\db\models\wrv\db_places_cache($db);

    // Return extended fields for frontend, and cache all search results
    $clean = [];
    foreach ($results as $r) {
        $placeId = $r['place_id'] ?? '';
        $name = $r['name'] ?? '';

        if (!$placeId || !$name) {
            continue;
        }

        $item = [
            'name' => $name,
            'formatted_address' => $r['formatted_address'] ?? '',
            'place_id' => $placeId,
            'rating' => $r['rating'] ?? null,
            'user_ratings_total' => $r['user_ratings_total'] ?? 0,
            'price_level' => $r['price_level'] ?? null,
            'phone_number' => null,
            'open_now' => null
        ];

        // Check search response for open_now
        if (isset($r['open_now'])) {
            $item['open_now'] = $r['open_now'];
        }
        elseif (isset($r['opening_hours']['open_now'])) {
            $item['open_now'] = $r['opening_hours']['open_now'];
        }

        try {
            // Check if place exists
            $existing = $modelPlace->readById($placeId);
            if ($existing) {
                $placePk = (int)$existing['pk'];
                // check cache for omitted search fields (e.g. phone number from prior detail lookups)
                $cached = $modelCache->readByPlacePk($placePk);
                if ($cached) {
                    if (!empty($cached['phone_number'])) {
                        $item['phone_number'] = $cached['phone_number'];
                    }
                    if ($item['open_now'] === null && $cached['open_now'] !== null) {
                        $item['open_now'] = (bool)$cached['open_now'];
                    }
                }
            }
            else {
                $placePk = $modelPlace->write([
                    'id' => $placeId,
                    'name' => $name
                ]);
            }

            if ($placePk) {
                // Upsert cache with available search fields
                $modelCache->write((int)$placePk, $r);
            }
        }
        catch (\Exception $ex) {
            // Fail silently on cache write errors so search doesn't break
            error_log("Failed to cache search result $placeId: " . $ex->getMessage());
        }

        $clean[] = $item;
    }

    echo json_encode(['results' => $clean]);
}
catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
