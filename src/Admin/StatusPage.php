<?php

declare(strict_types=1);

namespace Siderall\WooIntegrationHub\Admin;

final class StatusPage
{
    public function register(): void
    {
        add_submenu_page(
            'woocommerce',
            'Integration Hub',
            'Integration Hub',
            'manage_woocommerce',
            'wcih-status',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $configured = defined('WCIH_API_BASE_URL') && defined('WCIH_API_TOKEN');

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('WooCommerce Integration Hub', 'wcih') . '</h1>';
        echo '<p>' . esc_html__('External API configuration:', 'wcih') . ' <strong>';
        echo $configured ? esc_html__('configured', 'wcih') : esc_html__('missing', 'wcih');
        echo '</strong></p>';
        $log_message = esc_html__(
            'Logs are available through WooCommerce > Status > Logs using source "woocommerce-integration-hub".',
            'wcih'
        );
        echo '<p>' . $log_message . '</p>';
        echo '</div>';
    }
}
