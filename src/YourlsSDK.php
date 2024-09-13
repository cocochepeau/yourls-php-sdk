<?php

namespace Cocochepeau\YourlsPhpSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use RuntimeException;

class YourlsSDK
{
    private Client $client;

    public function __construct(
        private string $apiUrl,
        private readonly string $username,
        private readonly string $password,
        float $timeout = 5.0,
    ) {
        $this->apiUrl = rtrim($this->apiUrl, '/');
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => $timeout, // Set the default timeout for requests
        ]);
    }

    /**
     * Create a short URL.
     *
     * @param string $url The URL to shorten.
     * @param string|null $keyword An optional custom keyword for the short URL.
     * @param string|null $title An optional title for the short URL.
     * @return string The generated short URL.
     */
    public function createShortUrl(string $url, ?string $keyword = null, ?string $title = null): string
    {
        $params = [
            'action' => 'shorturl',
            'url' => $url,
            'format' => 'json',
        ];

        if ($keyword) {
            $params['keyword'] = $keyword;
        }
        if ($title) {
            $params['title'] = $title;
        }

        $response = $this->sendRequest($params);

        // Return already existing ShortURL
        if (!$response->isValid() && is_array($response->getBody()['url'])) {
            return $this->getDomainFromUrl($this->apiUrl) . '/' . $response->getBody()['url']['keyword'];
        }

        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }

        return $response->getBody()['shorturl'];
    }

    private function getDomainFromUrl(string $url): string
    {
        // Parse the URL and extract the host (domain)
        $parsedUrl = parse_url($url);

        // Return the 'host' if available, otherwise fallback to an empty string
        return $parsedUrl['host'] ?? '';
    }

    /**
     * Expand a short URL.
     *
     * @param string $shortUrl The short URL to expand.
     * @return string The original long URL.
     */
    public function expandShortUrl(string $shortUrl): string
    {
        $params = [
            'action' => 'expand',
            'shorturl' => $shortUrl,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }

        return $response->getBody()['longurl'];
    }

    /**
     * Get statistics for a specific short URL.
     *
     * @param string $shortUrl The short URL to get statistics for.
     * @return array The statistics for the short URL.
     */
    public function getShortUrlStats(string $shortUrl): array
    {
        $params = [
            'action' => 'url-stats',
            'shorturl' => $shortUrl,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }

        return $response->getBody();
    }

    /**
     * Get statistics for all short URLs.
     *
     * @param string $filter The filter to apply ("top", "bottom", "rand", "last").
     * @param int $limit The maximum number of links to return.
     * @return array The statistics for the short URLs.
     */
    public function getStats(string $filter = 'top', int $limit = 10): array
    {
        $params = [
            'action' => 'stats',
            'filter' => $filter,
            'limit' => $limit,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }

        return $response->getBody();
    }

    /**
     * Get global statistics for the YOURLS installation.
     *
     * @return array The global statistics.
     */
    public function getDbStats(): array
    {
        $params = [
            'action' => 'db-stats',
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }

        return $response->getBody();
    }

    /*
     * https://github.com/claytondaley/yourls-api-delete
     */
    public function deleteByShortUrl(string $shortUrl): void
    {
        $params = [
            'action' => 'delete',
            'shorturl' => $shortUrl,
            'format' => 'json',
        ];
        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }
    }

    public function findByLongUrl(string $longUrl): FindLongUrlResponse
    {
        $params = [
            'action' => 'lookup-url-substr',
            'substr' => $longUrl,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);
        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }
        
        return new FindLongUrlResponse($response);
    }

    /*
     * For Plugin https://github.com/timcrockford/yourls-api-edit-url
     */
    public function updateShortUrlTarget(string $shortUrl, string $targetUrl): void
    {
        $params = [
            'action' => 'update',
            'shorturl' => $shortUrl,
            'url' => $targetUrl,
            'format' => 'json',
        ];
        $this->addCredentials($params);

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException('Error: ' . $response->getMessage());
        }
    }

    private function addCredentials(array $request): array
    {
        $request     ['username'] = $this->username;
        $request ['password'] = $this->password;
        return $request;
    }

    private function sendRequest(array $yourlsApiParams): YourlsResponse
    {
        $yourlsApiParams = $this->addCredentials($yourlsApiParams);
        try {
            $response = $this->client->post('', [
                'form_params' => $yourlsApiParams,
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        } finally {
            return new YourlsResponse(
                $response ?? null,
            );
        }
    }
}