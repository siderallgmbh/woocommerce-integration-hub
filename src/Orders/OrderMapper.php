<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Orders;

use Siderall\WooIntegrationHub\Domain\OrderPayload;
use WC_Order;

final class OrderMapper
{
    public function map(WC_Order $order): OrderPayload
    {
        $items = [];

        foreach ($order->get_items() as $item) {
            $product = $item->get_product();

            $items[] = [
                'product_id' => $product ? $product->get_id() : null,
                'sku' => $product ? $product->get_sku() : '',
                'name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'subtotal' => (string) $item->get_subtotal(),
                'total' => (string) $item->get_total(),
            ];
        }

        return new OrderPayload(
            (int) $order->get_id(),
            (string) $order->get_currency(),
            (string) $order->get_total(),
            (string) $order->get_billing_email(),
            $items
        );
    }
}
