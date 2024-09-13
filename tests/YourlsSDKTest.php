<?php

namespace Mehrkanal\YourlsPhpSdkTest;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mehrkanal\YourlsPhpSdk\YourlsSDK;
use Mehrkanal\YourlsPhpSdk\YourlsUrlStats;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class YourlsSDKTest extends TestCase
{
    public function testCreateShortUrl(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'status' => 'success',
                    'shorturl' => 'http://sho.rt/1f',
                ])),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $shortUrl = $sdk->generateShortUrl('http://example.com');
        $this->assertSame('http://sho.rt/1f', $shortUrl);
    }

    public function testExpandShortUrl(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'longurl' => 'http://example.com',
                ])),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $longUrl = $sdk->expandShortUrl('short-keyword');
        $this->assertSame('http://example.com', $longUrl);
    }

    public function testGetUrlStats(): void
    {
        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->method('post')
            ->willReturn(
                new Response(200, [], json_encode([
                    'statusCode' => 200,
                    'message' => 'success',
                    'link' => [
                        'clicks' => 2,
                        'timestamp' => '1970-01-01 00:00:00',
                        'ip' => '1.1.1.1',
                        'url' => 'http://example.com',
                        'shorturl' => '1',
                    ],
                ], JSON_THROW_ON_ERROR)),
            );

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $stats = $sdk->getShortUrlStats('short-keyword');
        $this->assertInstanceOf(YourlsUrlStats::class, $stats);
        $this->assertSame(2, $stats->getClicks());
    }
}
