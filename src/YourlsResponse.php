<?php

declare(strict_types=1);

namespace Mehrkanal\YourlsPhpSdk;

use JetBrains\PhpStorm\ArrayShape;
use Psr\Http\Message\ResponseInterface;

class YourlsResponse
{
    private array $body;

    private int $statusCode;

    public function __construct(ResponseInterface $response)
    {
        $this->body = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->statusCode = $response->getStatusCode();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    #[ArrayShape([
        'message' => 'string',
        'errorCode' => 'int',
    ])]
    public function getBody(): array
    {
        return $this->body;
    }

    public function getBodyValueByKey(string $key): mixed
    {
        return $this->body[$key] ?? null;
    }

    public function getMessage(): ?string
    {
        return $this->getBodyValueByKey('message');
    }

    public function isValid(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
