<?php

declare(strict_types=1);

namespace Mehrkanal\YourlsPhpSdk;

use DateTimeImmutable;

class YourlsUrlStats
{
    private int $clicks;

    private DateTimeImmutable $timestamp;

    private string $ip;

    private string $longUrl;

    private string $shortUrl;

    public function __construct(YourlsResponse $response)
    {
        $link = $response->getBody()['link'];
        $this->clicks = $link['clicks'];
        $this->timestamp = new DateTimeImmutable($link['timestamp']);
        $this->ip = $link['ip'];
        $this->longUrl = $link['url'];
        $this->shortUrl = $link['shorturl'];
    }

    public function getClicks(): int
    {
        return $this->clicks;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getLongUrl(): string
    {
        return $this->longUrl;
    }

    public function getShortUrl(): string
    {
        return $this->shortUrl;
    }
}
