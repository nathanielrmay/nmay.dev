<?php
namespace lib\api;

/**
 * Abstract base class for external API clients.
 *
 * Subclasses must set $baseUrl and $apiKey, then use apiGet() / apiPost()
 * for all outbound requests.
 */
abstract class aApiClient {

    /** @var string Base URL for the API (no trailing slash) */
    protected string $baseUrl;

    /** @var string API key */
    protected string $apiKey;

    /**
     * Perform a GET request.
     *
     * @param string $endpoint  Path appended to $baseUrl (e.g. "/nearbysearch/json")
     * @param array  $params    Query parameters (api key is added automatically)
     * @return array            Decoded JSON response
     * @throws \RuntimeException on cURL or decode failure
     */
    protected function apiGet(string $endpoint, array $params = []): array
    {
        $params['key'] = $this->apiKey;
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $url .= '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("cURL error: $error");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException("API returned HTTP $httpCode: $response");
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("JSON decode error: " . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Perform a POST request with a JSON body.
     *
     * @param string $endpoint  Path appended to $baseUrl
     * @param array  $data      Data to JSON-encode as the request body
     * @return array            Decoded JSON response
     * @throws \RuntimeException on cURL or decode failure
     */
    protected function apiPost(string $endpoint, array $data = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $url .= '?' . http_build_query(['key' => $this->apiKey]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("cURL error: $error");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException("API returned HTTP $httpCode: $response");
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("JSON decode error: " . json_last_error_msg());
        }

        return $decoded;
    }
}
