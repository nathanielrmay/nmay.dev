<?php
namespace lib\api\places;

require_once __DIR__ . '/../aApiClient.php';
use lib\api\aApiClient;

/**
 * Google Places API client.
 *
 * Usage:
 *   $client = new placesApiClient('YOUR_API_KEY');
 *   $results = $client->searchNearby(30.2672, -97.7431, 5000);
 *   $details = $client->getPlaceDetails('ChIJ...');
 */
class placesApiClient extends aApiClient {

    public function __construct(string $apiKey)
    {
        $this->baseUrl = 'https://maps.googleapis.com/maps/api/place';
        $this->apiKey  = $apiKey;
    }

    /**
     * Search for nearby places.
     *
     * @param float  $lat    Latitude
     * @param float  $lng    Longitude
     * @param int    $radius Radius in meters
     * @param string $type   Place type filter (default: 'restaurant')
     * @return array         API response with 'results' key
     */
    public function searchNearby(float $lat, float $lng, int $radius, string $type = 'restaurant'): array
    {
        return $this->apiGet('/nearbysearch/json', [
            'location' => "$lat,$lng",
            'radius'   => $radius,
            'type'     => $type,
        ]);
    }

    /**
     * Get full details for a specific place.
     *
     * @param string $placeId Google place_id
     * @return array          API response with 'result' key
     */
    public function getPlaceDetails(string $placeId): array
    {
        return $this->apiGet('/details/json', [
            'place_id' => $placeId,
        ]);
    }
    /**
     * Search for places using a text query.
     *
     * @param string $query  Search string (e.g. "Taco Bell Austin")
     * @param string $type   Place type filter (default: 'restaurant')
     * @return array         API response with 'results' key
     */
    public function searchByText(string $query, string $type = 'restaurant'): array
    {
        return $this->apiGet('/textsearch/json', [
            'query' => $query,
            'type'  => $type,
        ]);
    }
}
