<?php

declare(strict_types=1);

namespace Mehrkanal\YourlsPhpSdk;

class YourlsGlobalStats
{
    private int $totalLinks;

    private int $totalClicks;

    public function __construct(YourlsResponse $response)
    {
        $dbstats = $response->getBody()['db-stats'];
        $this->totalLinks = (int) $dbstats['total_links'];
        $this->totalClicks = (int) $dbstats['total_clicks'];
    }

    public function getTotalLinks(): int
    {
        return $this->totalLinks;
    }

    public function getTotalClicks(): int
    {
        return $this->totalClicks;
    }
}
