# WooCommerce Integration Hub

A production-style WordPress/WooCommerce plugin that demonstrates how I structure integrations between an e-commerce store and an external business system.

The project is intentionally realistic rather than a minimal API demo. It covers order export, inbound webhooks, retries, structured logging, admin diagnostics and testable service boundaries.

## Use case

A WooCommerce store needs to keep an external ERP / CRM / fulfilment service synchronized.

Typical flow:

1. customer creates or updates an order;
2. WooCommerce schedules an asynchronous export;
3. the integration maps the order into a transport DTO;
4. the API client sends the payload to the external service;
5. failures are logged and can be retried;
6. the external system can send signed webhooks back to WordPress;
7. the plugin updates the WooCommerce order safely.

## Highlights

- clean PSR-4 architecture;
- WooCommerce hooks kept at the infrastructure edge;
- dedicated API client and order mapper;
- HMAC-signed inbound webhooks;
- asynchronous processing through Action Scheduler when available;
- fallback to WP-Cron;
- idempotency key per order export;
- structured integration log;
- REST endpoint for inbound status updates;
- admin status page;
- PHPUnit unit tests;
- PHPStan static analysis;
- PHPCS / PSR-12;
- GitHub Actions CI.

## Architecture

```text
WooCommerce
    |
    v
OrderExportSubscriber
    |
    v
OrderExportService -----> ApiClient -----> External API
    |                         |
    v                         v
OrderMapper              IntegrationLogger

External API
    |
    v
WebhookController -----> WebhookVerifier -----> WooCommerce order
```

## Development

Requirements:

- PHP 7.4+
- WordPress 6.x
- WooCommerce 8+
- Composer

```bash
composer install
composer test
composer analyse
composer phpcs
```

## Configuration

The plugin exposes these constants for local/demo configuration:

```php
define('WCIH_API_BASE_URL', 'https://example.test/api');
define('WCIH_API_TOKEN', 'demo-token');
define('WCIH_WEBHOOK_SECRET', 'change-me');
```

No real third-party credentials are committed.

## Why this repository exists

This is a portfolio project designed to show the type of PHP/WordPress work I typically do: existing business processes, external APIs, data mapping, troubleshooting, automation and maintainable production code.

The external API is intentionally generic so the same architecture can represent an ERP, CRM, warehouse, invoicing platform or custom backend.

## License

MIT. See [LICENSE](LICENSE).
