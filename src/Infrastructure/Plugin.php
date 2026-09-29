<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Infrastructure;

use Siderall\WooIntegrationHub\Admin\StatusPage;
use Siderall\WooIntegrationHub\Api\ApiClient;
use Siderall\WooIntegrationHub\Orders\OrderExportService;
use Siderall\WooIntegrationHub\Orders\OrderExportSubscriber;
use Siderall\WooIntegrationHub\Orders\OrderMapper;
use Siderall\WooIntegrationHub\Support\IntegrationLogger;
use Siderall\WooIntegrationHub\Webhook\WebhookController;
use Siderall\WooIntegrationHub\Webhook\WebhookVerifier;

final class Plugin
{
    public function boot(): void
    {
        add_action('plugins_loaded', [$this, 'register']);
    }

    public function register(): void
    {
        if (!class_exists('WooCommerce')) {
            return;
        }

        $baseUrl = defined('WCIH_API_BASE_URL') ? (string) WCIH_API_BASE_URL : 'https://example.test/api';
        $token = defined('WCIH_API_TOKEN') ? (string) WCIH_API_TOKEN : '';
        $secret = defined('WCIH_WEBHOOK_SECRET') ? (string) WCIH_WEBHOOK_SECRET : '';

        $logger = new IntegrationLogger();
        $exporter = new OrderExportService(new ApiClient($baseUrl, $token), new OrderMapper(), $logger);
        $subscriber = new OrderExportSubscriber();
        $webhook = new WebhookController(new WebhookVerifier($secret));
        $statusPage = new StatusPage();

        $subscriber->register();

        add_action(
            OrderExportSubscriber::ACTION,
            static function (int $orderId) use ($exporter): void {
                $exporter->export($orderId);
            }
        );

        add_action('rest_api_init', [$webhook, 'registerRoutes']);
        add_action('admin_menu', [$statusPage, 'register']);
    }
}
