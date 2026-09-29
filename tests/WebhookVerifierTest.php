<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Tests;

use PHPUnit\Framework\TestCase;
use Siderall\WooIntegrationHub\Webhook\WebhookVerifier;

final class WebhookVerifierTest extends TestCase
{
    public function testAcceptsValidSignature(): void
    {
        $payload = '{"order_id":42,"status":"completed"}';
        $secret = 'test-secret';
        $signature = hash_hmac('sha256', $payload, $secret);

        $verifier = new WebhookVerifier($secret);

        self::assertTrue($verifier->valid($payload, $signature));
    }

    public function testRejectsTamperedPayload(): void
    {
        $secret = 'test-secret';
        $signature = hash_hmac('sha256', '{"order_id":42}', $secret);

        $verifier = new WebhookVerifier($secret);

        self::assertFalse($verifier->valid('{"order_id":99}', $signature));
    }

    public function testRejectsEmptySecret(): void
    {
        $verifier = new WebhookVerifier('');

        self::assertFalse($verifier->valid('{}', 'signature'));
    }
}
