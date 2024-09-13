<?php

use Mehrkanal\YourlsPhpSdk\YourlsSDK;

$yourls = new YourlsSDK('http://sho.rt/yourls-api.php', 'your_username', 'your_password');

// Create a short URL
try {
    $shortUrl = $yourls->generateShortUrl('http://example.com', 'custom-keyword', 'Example Title');
    echo "Short URL: $shortUrl\n";
} catch (RuntimeException $e) {
    echo $e->getMessage();
}

// Expand a short URL
try {
    $longUrl = $yourls->expandShortUrl($shortUrl);
    echo "Long URL: $longUrl\n";
} catch (Exception $e) {
    echo $e->getMessage();
}

// Get stats for a specific short URL
try {
    $stats = $yourls->getShortUrlStats('custom-keyword');
    print_r($stats);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

// Get global stats
try {
    $dbStats = $yourls->getGlobalStats();
    print_r($dbStats);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $yourls->deleteByShortUrl($shortUrl);
    echo "Deleted" . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $longUrl = $yourls->findShortUrlsByLongUrl('http://example.com');
    foreach ($longUrl->findShortUrls() as $shortUrl) {
        echo $shortUrl . PHP_EOL;
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $yourls->updateShortUrlTarget('http://localhost:8080/hwxbp', 'http://google.com');
    echo "Updated" . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

try {
    $stats = $yourls->getStats('top', 1);
    print_r($stats);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}