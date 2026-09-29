<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Support;

final class IntegrationLogger
{
    /**
     * @param array<string, mixed> $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->write('info', $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->write('error', $message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function write(string $level, string $message, array $context): void
    {
        if (function_exists('wc_get_logger')) {
            wc_get_logger()->log(
                $level,
                $message . ' ' . wp_json_encode($context),
                ['source' => 'woocommerce-integration-hub']
            );
            return;
        }

        error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            wp_json_encode([
                'level' => $level,
                'message' => $message,
                'context' => $context,
            ])
        );
    }
}
