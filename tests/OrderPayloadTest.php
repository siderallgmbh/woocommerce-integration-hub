<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Tests;

use PHPUnit\Framework\TestCase;
use Siderall\WooIntegrationHub\Domain\OrderPayload;

final class OrderPayloadTest extends TestCase
{
    public function testSerializesExpectedApiShape(): void
    {
        $payload = new OrderPayload(
            42,
            'EUR',
            '99.90',
            'customer@example.test',
            [['sku' => 'ABC-1', 'quantity' => 2]]
        );

        self::assertSame(
            [
                'order_id' => 42,
                'currency' => 'EUR',
                'total' => '99.90',
                'customer' => ['email' => 'customer@example.test'],
                'items' => [['sku' => 'ABC-1', 'quantity' => 2]],
            ],
            $payload->toArray()
        );
    }
}
