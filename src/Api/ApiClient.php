<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Api;

use RuntimeException;

final class ApiClient
{
    private string $baseUrl;
    private string $token;

    public function __construct(string $baseUrl, string $token)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function post(string $path, array $payload, string $idempotencyKey): ApiResponse
    {
        $response = wp_remote_post(
            $this->baseUrl . '/' . ltrim($path, '/'),
            [
                'timeout' => 15,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                    'Idempotency-Key' => $idempotencyKey,
                ],
                'body' => wp_json_encode($payload),
            ]
        );

        if (is_wp_error($response)) {
            throw new RuntimeException($response->get_error_message());
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        $rawBody = (string) wp_remote_retrieve_body($response);
        $decoded = json_decode($rawBody, true);

        return new ApiResponse($status, is_array($decoded) ? $decoded : []);
    }
}
