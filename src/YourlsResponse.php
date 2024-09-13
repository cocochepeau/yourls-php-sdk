<?php

declare(strict_types=1);

namespace Cocochepeau\YourlsPhpSdk;

use GuzzleHttp\Psr7\Response;
use JetBrains\PhpStorm\ArrayShape;

class YourlsResponse
{
    #[ArrayShape(['message' => 'string', 'errorCode' => 'int'])]
    private array $body;
    private int $statusCode;

    public function __construct(?\Psr\Http\Message\ResponseInterface $response)
    {
        if ($response === null) {
            $this->statusCode = 500;
            return;
        }
        $this->body = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->statusCode = $response->getStatusCode();
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getMessage(): string
    {
        return $this->body['message'];
    }

    public function isValid(): bool
    {
        return
            $this->statusCode >= 200 && $this->statusCode < 300 &&
            $this->body['status'] === 'success';
    }

    public function getStatus(): string
    {
        return $this->body['status'];
    }
}