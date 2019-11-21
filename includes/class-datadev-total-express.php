<?php

/**
 * Datadev Total Express
 *
 * @package Datadev_Total_Express/Classes
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugins main class.
 */
class Datadev_Total_Express {

    /**
     * Initialize the plugin public actions.
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'load_plugin_textdomain'), -1);

        // Checks with WooCommerce is installed.
        if (class_exists('WC_Integration')) {
            self::includes();

            add_filter('woocommerce_integrations', array(__CLASS__, 'include_integrations'));
            add_filter('woocommerce_shipping_methods', array(__CLASS__, 'include_methods'));
        } else {
            add_action('admin_notices', array(__CLASS__, 'woocommerce_missing_notice'));
        }
    }

    /**
     * Load the plugin text domain for translation.
     */
    public static function load_plugin_textdomain() {
        load_plugin_textdomain('datadev-total-express-for-woocommerce', false, dirname(plugin_basename(DATADEV_TOTAL_EXPRESS_PLUGIN_FILE)) . '/languages/');
    }

    /**
     * Includes.
     */
    private static function includes() {
        include_once dirname(__FILE__) . '/datadev-total-express-functions.php';
        include_once dirname(__FILE__) . '/class-datadev-total-express-package.php';
        include_once dirname(__FILE__) . '/class-datadev-total-express-webservice.php';
        include_once dirname(__FILE__) . '/class-datadev-total-express-orders.php';
        include_once dirname(__FILE__) . '/integrations/class-datadev-total-express-integration.php';
        include_once dirname(__FILE__) . '/abstracts/class-datadev-total-express-shipping.php';

        foreach (glob(plugin_dir_path(__FILE__) . '/shipping/class-datadev-total-express-*.php') as $filename) {
            include_once $filename;
        }
    }

    /**
     * Include Total Express integration to WooCommerce.
     *
     * @param  array $integrations Default integrations.
     *
     * @return array
     */
    public static function include_integrations($integrations) {
        $integrations[] = 'Datadev_Total_Express_Integration';

        return $integrations;
    }

    /**
     * Include Total Express shipping methods to WooCommerce.
     *
     * @param  array $methods Default shipping methods.
     *
     * @return array
     */
    public static function include_methods($methods) {
        $methods['total-express-standard'] = 'Datadev_Total_Express_Shipping_Standard';
        $methods['total-express-express'] = 'Datadev_Total_Express_Shipping_Express';

        return $methods;
    }

    /**
     * WooCommerce fallback notice.
     */
    public static function woocommerce_missing_notice() {
        include_once dirname(__FILE__) . '/admin/views/html-admin-missing-dependencies.php';
    }

    /**
     * Get main file.
     *
     * @return string
     */
    public static function get_main_file() {
        return DATADEV_TOTAL_EXPRESS_PLUGIN_FILE;
    }

    /**
     * Get plugin path.
     *
     * @return string
     */
    public static function get_plugin_path() {
        return plugin_dir_path(DATADEV_TOTAL_EXPRESS_PLUGIN_FILE);
    }

    /**
     * Get templates path.
     *
     * @return string
     */
    public static function get_templates_path() {
        return self::get_plugin_path() . 'templates/';
    }

}
