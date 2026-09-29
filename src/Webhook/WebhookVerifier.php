<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Webhook;

final class WebhookVerifier
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function valid(string $payload, string $providedSignature): bool
    {
        if ($this->secret === '' || $providedSignature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $this->secret);

        return hash_equals($expected, $providedSignature);
    }
}
