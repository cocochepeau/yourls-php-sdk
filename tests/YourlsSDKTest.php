<?php

use PHPUnit\Framework\TestCase;
use Cocochepeau\YourlsPhpSdk\YourlsSDK; // Adjust the namespace as necessary

class YourlsSDKTest extends TestCase
{
    public function testCreateShortUrl()
    {
        $mockClient = $this->createMock(GuzzleHttp\Client::class);
        $mockClient->method('post')
                   ->willReturn(new GuzzleHttp\Psr7\Response(200, [], json_encode([
                       'status' => 'success',
                       'shorturl' => 'http://sho.rt/1f'
                   ])));

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $shortUrl = $sdk->createShortUrl('http://example.com');
        $this->assertEquals('http://sho.rt/1f', $shortUrl);
    }

    public function testExpandShortUrl()
    {
        $mockClient = $this->createMock(GuzzleHttp\Client::class);
        $mockClient->method('post')
                   ->willReturn(new GuzzleHttp\Psr7\Response(200, [], json_encode([
                       'statusCode' => 200,
                       'longurl' => 'http://example.com'
                   ])));

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $longUrl = $sdk->expandShortUrl('short-keyword');
        $this->assertEquals('http://example.com', $longUrl);
    }

    public function testGetUrlStats()
    {
        $mockClient = $this->createMock(GuzzleHttp\Client::class);
        $mockClient->method('post')
                   ->willReturn(new GuzzleHttp\Psr7\Response(200, [], json_encode([
                       'statusCode' => 200,
                       'message' => 'success'
                   ])));

        $sdk = new YourlsSDK('http://sho.rt/yourls-api.php', 'username', 'password');
        $reflection = new ReflectionProperty($sdk, 'client');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $mockClient);

        $stats = $sdk->getUrlStats('short-keyword');
        $this->assertArrayHasKey('message', $stats);
        $this->assertEquals('success', $stats['message']);
    }
}
