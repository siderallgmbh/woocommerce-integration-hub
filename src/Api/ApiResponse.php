<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Api;

final class ApiResponse
{
    private int $statusCode;
    /** @var array<string, mixed> */
    private array $body;

    /**
     * @param array<string, mixed> $body
     */
    public function __construct(int $statusCode, array $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    public function successful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function body(): array
    {
        return $this->body;
    }
}
