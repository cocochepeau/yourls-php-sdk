<?php

namespace Mehrkanal\YourlsPhpSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use RuntimeException;
use Throwable;

class YourlsSDK
{
    private const API_STATS = 'stats';
    private const API_URL_STATS = 'url-stats';
    private const API_EXPAND_SHORT_URL = 'expand';
    private const API_GENERATE_SHORT_URL = 'shorturl';
    private const API_DELETE_SHORT_URL = 'delete';
    private const API_LOOKUP_URL_SUBSTR = 'lookup-url-substr';
    private const API_UPDATE_SHORT_URL = 'update';

    private Client $client;

    public function __construct(
        private string $apiUrl,
        private readonly string $username,
        private readonly string $password,
        float $timeout = 10.0,
    ) {
        if (!str_starts_with($this->apiUrl, 'http')) {
            throw new RuntimeException('You must use http request method');
        }
        $this->apiUrl = rtrim($this->apiUrl, '/');
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => $timeout,
        ]);
    }

    /**
     * Create a short URL.
     *
     * @param string $longUrl The URL to shorten.
     * @param string|null $keyword An optional custom keyword for the short URL
     * @param string|null $title An optional title for the short URL. Else Yourls will use the page Title (slower)
     */
    public function generateShortUrl(string $longUrl, ?string $keyword = null, ?string $title = null): string
    {
        $params = [
            'action' => self::API_GENERATE_SHORT_URL,
            'url' => $longUrl,
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
        if (!$response->isValid() && is_array($response->getBodyValueByKey('url'))) {
            return $this->getYourlsDomainFromApiUrl($this->apiUrl) . '/' . $response->getBodyValueByKey(
                    'url',
                )['keyword'];
        }

        if (!$response->isValid()) {
            throw new RuntimeException(__FUNCTION__ . ' for ' . $longUrl . PHP_EOL . $response->getMessage());
        }

        return $response->getBodyValueByKey('shorturl');
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
            'action' => self::API_EXPAND_SHORT_URL,
            'shorturl' => $shortUrl,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException(__FUNCTION__ . ' for ' . $shortUrl . PHP_EOL . $response->getMessage());
        }

        return $response->getBodyValueByKey('longurl');
    }

    /**
     * Get statistics for a specific short URL.
     *
     * @param string $shortUrl The short URL to get statistics for.
     */
    public function getShortUrlStats(string $shortUrl): YourlsUrlStats
    {
        $params = [
            'action' => self::API_URL_STATS,
            'shorturl' => $shortUrl,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException(__FUNCTION__ . ' for ' . $shortUrl . PHP_EOL . $response->getMessage());
        }

        return new YourlsUrlStats($response);
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
            'action' => self::API_STATS,
            'filter' => $filter,
            'limit' => $limit,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);
        if (!$response->isValid()) {
            throw new RuntimeException(__FUNCTION__ . PHP_EOL . $response->getMessage());
        }

        return $response->getBody();
    }

    public function getGlobalStats(): YourlsGlobalStats
    {
        $params = [
            'action' => 'db-stats',
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException(__FUNCTION__ . PHP_EOL . $response->getMessage());
        }

        return new YourlsGlobalStats($response);
    }

    public function deleteByShortUrl(string $shortUrl): void
    {
        $params = [
            'action' => self::API_DELETE_SHORT_URL,
            'shorturl' => $shortUrl,
            'format' => 'json',
        ];
        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException(
                __FUNCTION__ . ' for ' . $shortUrl . PHP_EOL . 'Do you installed https://github.com/claytondaley/yourls-api-delete ?' . PHP_EOL . $response->getMessage(),
            );
        }
    }

    public function findShortUrlsByLongUrl(string $longUrl): FindLongUrlResponse
    {
        $params = [
            'action' => self::API_LOOKUP_URL_SUBSTR,
            'substr' => $longUrl,
            'format' => 'json',
        ];

        $response = $this->sendRequest($params);
        if (!$response->isValid()) {
            throw new RuntimeException(__FUNCTION__ . ' for ' . $longUrl . PHP_EOL . $response->getMessage());
        }

        return new FindLongUrlResponse($response, $this->getYourlsDomainFromApiUrl($this->apiUrl));
    }

    public function updateShortUrlTarget(string $shortUrl, string $targetUrl): void
    {
        $params = [
            'action' => self::API_UPDATE_SHORT_URL,
            'shorturl' => $shortUrl,
            'url' => $targetUrl,
            'format' => 'json',
        ];
        $this->addCredentials($params);

        $response = $this->sendRequest($params);

        if (!$response->isValid()) {
            throw new RuntimeException(
                __FUNCTION__ . ' for ' . $shortUrl . PHP_EOL . 'Do you installed https://github.com/timcrockford/yourls-api-edit-url ?' . PHP_EOL . $response->getMessage(),
            );
        }
    }

    private function getYourlsDomainFromApiUrl(string $url): string
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['scheme'])) {
            return '';
        }
        if (!isset($parsedUrl['host'])) {
            return '';
        }
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    }

    private function addCredentials(array $request): array
    {
        $request['username'] = $this->username;
        $request['password'] = $this->password;
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
        } catch (Throwable $e) {
            $response = new Response(500, [], '{"message" : "' . $e->getMessage() . '"}', '1.1');
        } finally {
            return new YourlsResponse($response ?? new Response(500, [], '{"message" : "unknown error"}', '1.1'));
        }
    }
}
