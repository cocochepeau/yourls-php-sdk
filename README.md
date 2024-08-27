# YourlsSDK

## Installation

```bash
composer require cocochepeau/yourls-php-sdk
```

## Example Usage

```php

// Initialize the SDK
$yourls = new \Cocochepeau\YourlsPhpSdk\YourlsSDK('http://sho.rt/yourls-api.php', 'your_username', 'your_password');

// Create a short URL
try {
    $shortUrl = $yourls->createShortUrl('http://example.com', 'custom-keyword', 'Example Title');
    echo "Short URL: $shortUrl\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Expand a short URL
try {
    $longUrl = $yourls->expandShortUrl('custom-keyword');
    echo "Long URL: $longUrl\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Get stats for a specific short URL
try {
    $stats = $yourls->getUrlStats('custom-keyword');
    print_r($stats);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Get global stats
try {
    $dbStats = $yourls->getDbStats();
    print_r($dbStats);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

```
