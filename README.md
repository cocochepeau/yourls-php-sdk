# YourlsSDK

## Installation

```bash
composer require cocochepeau/yourls-php-sdk
```

## local development
```
docker run -it -v $PWD:/app -w /app -v $SSH_AUTH_SOCK:$SSH_AUTH_SOCK  -e SSH_AUTH_SOCK=$SSH_AUTH_SOCK -e SSH_AGENT_PID=$SSH_AGENT_PID --add-host=host.docker.internal:host-gateway composer:2.1 bash
composer up
vendor/bin/ecs
vendor/bin/rector
````

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
    $stats = $yourls->getShortUrlStats('custom-keyword');
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

try {
    $yourls->deleteByShortUrl($shortUrl);
    echo "Deleted\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

try {
    $longUrl = $yourls->findByLongUrl('http://example.com');
    echo $longUrl;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

try {
    $yourls->updateShortUrlTarget('http://localhost:8080/hwxbp', 'http://google.com');
    echo "Updated\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```
