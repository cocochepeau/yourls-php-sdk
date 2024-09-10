<?php

namespace Cocochepeau\YourlsPhpSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class YourlsSDK
{
    private Client $client;
    private string $apiUrl;

    public function __construct(string $apiUrl, private string $username, private string $password, float $timeout = 5.0)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout'  => $timeout, // Set the default timeout for requests
        ]);
    }

    /**
     * Send an API request.
     *
     * @param array $params The parameters to send with the request.
     * @return array The decoded JSON response.
     * @throws Exception If the request fails.
     */
    private function sendRequest(array $params): array
    {
        try {
            $response = $this->client->post('', [
                'form_params' => $params
            ]);

            $body = $response->getBody();
            return json_decode($body, true);
        } catch (GuzzleException $e) {
            throw new Exception('Request failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create a short URL.
     *
     * @param string $url The URL to shorten.
     * @param string|null $keyword An optional custom keyword for the short URL.
     * @param string|null $title An optional title for the short URL.
     * @return string The generated short URL.
     * @throws Exception If the request fails.
     */
    public function createShortUrl(string $url, ?string $keyword = null, ?string $title = null): string
    {
        $params = [
            'action' => 'shorturl',
            'url' => $url,
            'format' => 'json',
            'username' => $this->username,
            'password' => $this->password,
        ];

        if ($keyword) {
            $params['keyword'] = $keyword;
        }
        if ($title) {
            $params['title'] = $title;
        }

        $response = $this->sendRequest($params);

        if ($response['status'] !== 'success') {
            throw new Exception('Error: ' . $response['message']);
        }

        return $response['shorturl'];
    }

    /**
     * Expand a short URL.
     *
     * @param string $shortUrl The short URL to expand.
     * @return string The original long URL.
     * @throws Exception If the request fails.
     */
    public function expandShortUrl(string $shortUrl): string
    {
        $params = [
            'action' => 'expand',
            'shorturl' => $shortUrl,
            'format' => 'json',
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new Exception('Error: ' . $response['message']);
        }

        return $response['longurl'];
    }

    /**
     * Get statistics for a specific short URL.
     *
     * @param string $shortUrl The short URL to get statistics for.
     * @return array The statistics for the short URL.
     * @throws Exception If the request fails.
     */
    public function getUrlStats(string $shortUrl): array
    {
        $params = [
            'action' => 'url-stats',
            'shorturl' => $shortUrl,
            'format' => 'json',
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new Exception('Error: ' . $response['message']);
        }

        return $response;
    }

    /**
     * Get statistics for all short URLs.
     *
     * @param string $filter The filter to apply ("top", "bottom", "rand", "last").
     * @param int $limit The maximum number of links to return.
     * @return array The statistics for the short URLs.
     * @throws Exception If the request fails.
     */
    public function getStats(string $filter = 'top', int $limit = 10): array
    {
        $params = [
            'action' => 'stats',
            'filter' => $filter,
            'limit' => $limit,
            'format' => 'json',
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new Exception('Error: ' . $response['message']);
        }

        return $response;
    }

    /**
     * Get global statistics for the YOURLS installation.
     *
     * @return array The global statistics.
     * @throws Exception If the request fails.
     */
    public function getDbStats(): array
    {
        $params = [
            'action' => 'db-stats',
            'format' => 'json',
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new Exception('Error: ' . $response['message']);
        }

        return $response;
    }
}
