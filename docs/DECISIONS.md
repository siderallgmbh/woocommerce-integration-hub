# Architecture decisions

## Keep WooCommerce objects outside the core payload

`OrderMapper` converts `WC_Order` into `OrderPayload`.

That keeps transport data explicit and avoids leaking WooCommerce objects into the API layer.

## Idempotent exports

Every export sends an `Idempotency-Key` derived from the order ID and modification timestamp.

A real receiving API can use that key to avoid duplicate processing when retries occur.

## Asynchronous by default

Order-status hooks should not block checkout or admin requests while waiting for a third-party API.

The subscriber prefers WooCommerce Action Scheduler and falls back to WP-Cron when unavailable.

## Signed inbound webhooks

Inbound requests are authenticated with HMAC SHA-256 and `hash_equals()`.

The raw request body is verified before JSON is trusted.

## Explicit status allow-list

Remote systems cannot set arbitrary strings as WooCommerce statuses. The webhook controller validates the requested value against statuses registered by WooCommerce.

## Logging

Integration failures need operational visibility.

The logger prefers WooCommerce's native logger and falls back to PHP error logging only when WooCommerce logging is unavailable.

## Production extensions

A production implementation would commonly add:

- retry policy with exponential backoff;
- persistent sync-attempt records;
- dead-letter queue;
- admin retry buttons;
- product/inventory synchronization;
- API rate-limit handling;
- secret storage outside source control;
- integration/contract tests against a mock HTTP service.
