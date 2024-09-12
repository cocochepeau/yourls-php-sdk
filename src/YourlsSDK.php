<?php

namespace Cocochepeau\YourlsPhpSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

        // Return already existing ShortURL
        if ($response->getStatus() === 'fail' && is_array($response->getBody()['url'])) {
            return $this->apiUrl . $response->getBody()['url']['keyword'];
        }

        if ($response->hasInvalidStatus()) {
            throw new RuntimeException('Error: ' . $response['message']);
        }

        return $response['shorturl'];
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
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Error: ' . $response['message']);
        }

        return $response['longurl'];
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
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Error: ' . $response['message']);
        }

        return $response;
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
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Error: ' . $response['message']);
        }

        return $response;
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
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Error: ' . $response['message']);
        }

        return $response;
    }

    /*
     * https://github.com/claytondaley/yourls-api-delete
     */
    public function deleteByShortUrl(string $shortUrl)
    {
        $params = [
            'action' => 'delete',
            'shorturl' => $shortUrl,
            'format' => 'json',
            'username' => $this->username,
            'password' => $this->password,
        ];
        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Error: ' . $response['message']);
        }
    }

    public function findByLongUrl(string $longUrl)
    {
        $params = [
            'action' => 'lookup-url-substr',
            'substr' => $longUrl,
            'format' => 'json',
            'username' => $this->username,
            'password' => $this->password,
        ];
        $response = $this->sendRequest($params);
        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Error: ' . $response['message']);
        }
        return $response['keywords'];
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
            'username' => $this->username,
            'password' => $this->password,
        ];

        $response = $this->sendRequest($params);

        if ($response['statusCode'] !== 200) {
            throw new RuntimeException('Error: ' . $response['message']);
        }
    }

    /**
     * @param array $params The parameters to send with the request.
     * @return array The decoded JSON response.
     */
    private function sendRequest(array $params): YourlsResponse
    {
        try {
            $response = $this->client->post('', [
                'form_params' => $params,
            ]);

            $body = $response->getBody();
            return new YourlsResponse($response->getStatusCode(), json_decode($body, true));
        } catch (GuzzleException $e) {
            return new YourlsResponse(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody(), true),
            );
        }
    }
}

class YourlsResponse
{
    private array $body;

    public function __construct(private int $statusCode, string $body)
    {
        $this->body = json_decode($body, true);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function hasInvalidStatus(): bool
    {
        return $this->body['status'] !== 'success';
    }

    public function getStatus(): string
    {
        return $this->body['status'];
    }
}