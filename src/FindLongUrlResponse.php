<?php

declare(strict_types=1);

namespace Mehrkanal\YourlsPhpSdk;

class FindLongUrlResponse
{
    private array $shortUrls;

    public function __construct($response)
    {
        $this->shortUrls = $response->getBody()['keywords'];
    }

    public function getShortUrls(): array
    {
        return $this->shortUrls;
    }
}