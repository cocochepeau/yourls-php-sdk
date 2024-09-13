# YourlsSDK

Fork of https://github.com/cocochepeau/yourls-php-sdk

## Installation

```bash
composer require mehrkanal/yourls-php-sdk
```

## local development

```bash
docker run -it -v $PWD:/app -w /app -v $SSH_AUTH_SOCK:$SSH_AUTH_SOCK  -e SSH_AUTH_SOCK=$SSH_AUTH_SOCK -e SSH_AGENT_PID=$SSH_AGENT_PID --add-host=host.docker.internal:host-gateway composer:2.1 bash
composer up
vendor/bin/ecs
vendor/bin/rector
vendor/bin/phpstan
````

## Example Usage

[See Example](example.php)