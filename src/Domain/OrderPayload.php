<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Domain;

final class OrderPayload
{
    private int $orderId;
    private string $currency;
    private string $total;
    private string $email;
    /** @var array<int, array<string, mixed>> */
    private array $items;

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function __construct(int $orderId, string $currency, string $total, string $email, array $items)
    {
        $this->orderId = $orderId;
        $this->currency = $currency;
        $this->total = $total;
        $this->email = $email;
        $this->items = $items;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'currency' => $this->currency,
            'total' => $this->total,
            'customer' => [
                'email' => $this->email,
            ],
            'items' => $this->items,
        ];
    }
}
