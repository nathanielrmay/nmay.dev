<?php
/**
 * AJAX endpoint for searching Google Places.
 * 
 * Receives POST JSON: { query: "taco bell", location: "Springfield, Missouri" }
 * Returns JSON: { results: [{ name, formatted_address, place_id }, ...] }
 */
namespace pages\wrv\food\food_war\lib\ajax;

require_once __DIR__ . '/../../aFoodWarPage.php';

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
    if (!$input) $input = $_POST;

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

    // Return only the fields the frontend needs
    $clean = [];
    foreach ($results as $r) {
        $clean[] = [
            'name' => $r['name'] ?? '',
            'formatted_address' => $r['formatted_address'] ?? '',
            'place_id' => $r['place_id'] ?? ''
        ];
    }

    echo json_encode(['results' => $clean]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
