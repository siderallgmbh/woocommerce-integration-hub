<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Orders;

use RuntimeException;
use Siderall\WooIntegrationHub\Api\ApiClient;
use Siderall\WooIntegrationHub\Support\IntegrationLogger;

final class OrderExportService
{
    private ApiClient $client;
    private OrderMapper $mapper;
    private IntegrationLogger $logger;

    public function __construct(ApiClient $client, OrderMapper $mapper, IntegrationLogger $logger)
    {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->logger = $logger;
    }

    public function export(int $orderId): void
    {
        $order = wc_get_order($orderId);

        if (!$order) {
            throw new RuntimeException(sprintf('WooCommerce order %d does not exist.', $orderId));
        }

        $payload = $this->mapper->map($order);
        $idempotencyKey = hash('sha256', 'wc-order:' . $orderId . ':' . $order->get_date_modified()->getTimestamp());

        try {
            $response = $this->client->post('/orders', $payload->toArray(), $idempotencyKey);

            if (!$response->successful()) {
                throw new RuntimeException(
                    sprintf('Remote API returned HTTP %d.', $response->statusCode())
                );
            }

            $order->update_meta_data('_wcih_last_exported_at', gmdate('c'));
            $order->save();

            $this->logger->info('Order exported successfully.', ['order_id' => $orderId]);
        } catch (RuntimeException $exception) {
            $this->logger->error(
                'Order export failed.',
                ['order_id' => $orderId, 'error' => $exception->getMessage()]
            );

            throw $exception;
        }
    }
}
