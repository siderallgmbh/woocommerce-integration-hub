<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Webhook;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

final class WebhookController
{
    private WebhookVerifier $verifier;

    public function __construct(WebhookVerifier $verifier)
    {
        $this->verifier = $verifier;
    }

    public function registerRoutes(): void
    {
        register_rest_route(
            'wcih/v1',
            '/webhooks/order-status',
            [
                'methods' => 'POST',
                'callback' => [$this, 'handle'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    /**
     * @return WP_REST_Response|WP_Error
     */
    public function handle(WP_REST_Request $request)
    {
        $raw = $request->get_body();
        $signature = (string) $request->get_header('x-wcih-signature');

        if (!$this->verifier->valid($raw, $signature)) {
            return new WP_Error('invalid_signature', 'Invalid webhook signature.', ['status' => 401]);
        }

        $payload = json_decode($raw, true);

        if (!is_array($payload) || empty($payload['order_id']) || empty($payload['status'])) {
            return new WP_Error('invalid_payload', 'Webhook payload is incomplete.', ['status' => 422]);
        }

        $order = wc_get_order((int) $payload['order_id']);

        if (!$order) {
            return new WP_Error('order_not_found', 'Order not found.', ['status' => 404]);
        }

        $status = sanitize_key((string) $payload['status']);
        $allowedStatuses = array_keys(wc_get_order_statuses());
        $normalizedAllowed = array_map(
            static fn(string $value): string => str_replace('wc-', '', $value),
            $allowedStatuses
        );

        if (!in_array($status, $normalizedAllowed, true)) {
            return new WP_Error('invalid_status', 'Unsupported WooCommerce order status.', ['status' => 422]);
        }

        $order->update_status($status, 'Status updated by WooCommerce Integration Hub webhook.');

        return new WP_REST_Response(['updated' => true, 'order_id' => $order->get_id()], 200);
    }
}
