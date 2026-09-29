<?php
/**
 * Plugin Name: WooCommerce Integration Hub
 * Description: Production-style demo for WooCommerce API integrations, webhooks and asynchronous order synchronization.
 * Version: 0.1.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * License: MIT
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$autoload = __DIR__ . '/vendor/autoload.php';

if (is_readable($autoload)) {
    require_once $autoload;
}

if (class_exists(\Siderall\WooIntegrationHub\Infrastructure\Plugin::class)) {
    (new \Siderall\WooIntegrationHub\Infrastructure\Plugin())->boot();
}
