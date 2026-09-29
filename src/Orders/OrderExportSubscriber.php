<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Orders;

final class OrderExportSubscriber
{
    public const ACTION = 'wcih_export_order';

    public function register(): void
    {
        add_action('woocommerce_order_status_processing', [$this, 'schedule'], 10, 1);
    }

    public function schedule(int $orderId): void
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action(self::ACTION, ['order_id' => $orderId], 'wcih');
            return;
        }

        if (!wp_next_scheduled(self::ACTION, [$orderId])) {
            wp_schedule_single_event(time() + 5, self::ACTION, [$orderId]);
        }
    }
}
