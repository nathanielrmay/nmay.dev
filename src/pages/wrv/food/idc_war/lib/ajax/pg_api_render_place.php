<?php
/**
 * AJAX endpoint for rendering the pt_place layout from a Place PK.
 * 
 * Receives POST JSON: { pk: 123 }
 * Returns JSON: { html: "<div>...</div>" }
 */
namespace pages\wrv\food\idc_war\lib\ajax;

require_once __DIR__ . '/../../aIdcWarPage.php';

use lib\basket;
use lib\db\models\wrv\db_places_place;
use lib\db\models\wrv\db_places_cache;

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

    $pk = isset($input['pk']) ? (int)$input['pk'] : null;

    if (!$pk) {
        http_response_code(400);
        echo json_encode(['error' => 'pk is required']);
        exit;
    }

    $db = basket::db_web();
    $modelPlace = new db_places_place($db);
    $modelCache = new db_places_cache($db);

    $place = $modelPlace->read($pk);
    if (!$place) {
        http_response_code(404);
        echo json_encode(['error' => 'Place not found']);
        exit;
    }

    $cache = $modelCache->readByPlacePk($pk);
    if (!$cache) {
        $cache = [];
    }

    // Render the partial
    $args = [
        'place' => $place,
        'cache' => $cache
    ];

    // We capture output buffering inside basket::render, but we need to ensure the autoloader can find the path relative to the app root
    $html = basket::render('pages/wrv/food/lib/partials/pt_place.php', $args);

    if (empty($html)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to render partial']);
        exit;
    }

    echo json_encode(['html' => $html]);

}
catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
