<?php
/**
 * Admin help message.
 *
 * @package Datadev_Total_Express/Admin/Settings
 */
if (!defined('ABSPATH')) {
    exit;
}

if (apply_filters('datadev_total_express_help_message', true)) :
    ?>
    <div class="updated woocommerce-message inline">
        <p>
            <?php
            /* translators: %s: plugin name */
            echo esc_html(sprintf(esc_html__('Help us keep the %s plugin updated and improved by rating five stars &#9733;&#9733;&#9733;&#9733;&#9733; on WordPress.org. Thank you in advance!', 'datadev-total-express-for-woocommerce'), __('Datadev - Total Express for WooCommerce', 'datadev-total-express-for-woocommerce')));
            ?>
        </p>
        <p><a href="https://wordpress.org/support/plugin/datadev-total-express-for-woocommerce/reviews/?filter=5#new-post" target="_blank" class="button button-primary"><?php esc_html_e('Make a review', 'datadev-total-express-for-woocommerce'); ?></a></p>
    </div>
    <?php

endif;
