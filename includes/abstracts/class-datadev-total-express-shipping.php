<?php

/**
 * Abstract Total Express shipping method.
 *
 * @package Datadev_Total_Express/Abstracts
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Default Total Express shipping method abstract class.
 *
 * This is a abstract method with default options for all methods.
 */
abstract class Datadev_Total_Express_Shipping extends WC_Shipping_Method {

    /**
     * Service modality code.
     *
     * @var string
     */
    protected $code = '';

    /**
     * User.
     *
     * @var string
     */
    protected $user = '';

    /**
     * Password.
     *
     * @var string
     */
    protected $password = '';

    /**
     * Initialize the Datadev Total Express shipping method.
     *
     * @param int $instance_id Shipping zone instance ID.
     */
    public function __construct($instance_id = 0) {       
        $this->instance_id = absint($instance_id);
        /* translators: %s: method title */
        $this->method_description = sprintf(__('%s is a shipping method from Total Express.', 'datadev-total-expresss-for-woocommerce'), $this->method_title);
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
        );

        // Load the form fields.
        $this->init_form_fields();

        // Define user set variables.
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->shipping_class_id = (int) $this->get_option('shipping_class_id', '-1');
        $this->show_delivery_time = $this->get_option('show_delivery_time');
        $this->additional_time = $this->get_option('additional_time');
        $this->fee = $this->get_option('fee');
        $this->declare_value = $this->get_option('declare_value');
        $this->custom_code = $this->get_option('custom_code');
        $this->user = $this->get_option('user');
        $this->password = $this->get_option('password');
        $this->minimum_height = $this->get_option('minimum_height');
        $this->minimum_width = $this->get_option('minimum_width');
        $this->minimum_length = $this->get_option('minimum_length');
        $this->extra_weight = $this->get_option('extra_weight', '0');
        $this->debug = $this->get_option('debug');

        // Save admin options.
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * Get log.
     *
     * @return string
     */
    protected function get_log_link() {
        return ' <a href="' . esc_url(admin_url('admin.php?page=wc-status&tab=logs&log_file=' . esc_attr($this->id) . '-' . sanitize_file_name(wp_hash($this->id)) . '.log')) . '">' . __('View logs.', 'datadev-total-express-for-woocommerce') . '</a>';
    }

    /**
     * Get shipping classes options.
     *
     * @return array
     */
    protected function get_shipping_classes_options() {
        $shipping_classes = WC()->shipping->get_shipping_classes();
        $options = array(
            '-1' => __('Any Shipping Class', 'datadev-total-express-for-woocommerce'),
            '0' => __('No Shipping Class', 'datadev-total-express-for-woocommerce'),
        );

        if (!empty($shipping_classes)) {
            $options += wp_list_pluck($shipping_classes, 'name', 'term_id');
        }

        return $options;
    }

    /**
     * Admin options fields.
     */
    public function init_form_fields() {
        $this->instance_form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'datadev-total-express-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable this shipping method', 'datadev-total-express-for-woocommerce'),
                'default' => 'yes',
            ),
            'title' => array(
                'title' => __('Title', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => $this->method_title,
            ),
            'behavior_options' => array(
                'title' => __('Behavior Options', 'datadev-total-express-for-woocommerce'),
                'type' => 'title',
                'default' => '',
            ),
            'shipping_class_id' => array(
                'title' => __('Shipping Class', 'datadev-total-express-for-woocommerce'),
                'type' => 'select',
                'description' => __('If necessary, select a shipping class to apply this method.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => '',
                'class' => 'wc-enhanced-select',
                'options' => $this->get_shipping_classes_options(),
            ),
            'show_delivery_time' => array(
                'title' => __('Delivery Time', 'datadev-total-express-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Show estimated delivery time', 'datadev-total-express-for-woocommerce'),
                'description' => __('Display the estimated delivery time in working days.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => 'no',
            ),
            'additional_time' => array(
                'title' => __('Additional Days', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('Additional working days to the estimated delivery.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
                'placeholder' => '0',
            ),
            'fee' => array(
                'title' => __('Handling Fee', 'datadev-total-express-for-woocommerce'),
                'type' => 'price',
                'description' => __('Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => '0.00',
                'default' => '',
            ),
            'optional_services' => array(
                'title' => __('Optional Services', 'datadev-total-express-for-woocommerce'),
                'type' => 'title',
                'description' => __('Use these options to add the value of each service provided by Total Express.', 'datadev-total-express-for-woocommerce'),
                'default' => '',
            ),
            'declare_value' => array(
                'title' => __('Declare Value for Insurance', 'datadev-total-express-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable declared value', 'datadev-total-express-for-woocommerce'),
                'description' => __('This controls if the price of the package must be declared for insurance purposes.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => 'yes',
            ),
            'service_options' => array(
                'title' => __('Service Options', 'datadev-total-express-for-woocommerce'),
                'type' => 'title',
                'default' => '',
            ),
            'custom_code' => array(
                'title' => __('Service Code', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('Service code, use this for custom codes.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->code,
                'default' => '',
            ),
            'user' => array(
                'title' => __('User', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('User', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->user,
                'default' => '',
            ),
            'password' => array(
                'title' => __('Password', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('Password.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'placeholder' => $this->password,
                'default' => '',
            ),
            'package_standard' => array(
                'title' => __('Package Standard', 'datadev-total-express-for-woocommerce'),
                'type' => 'title',
                'description' => __('Minimum measure for your shipping packages.', 'datadev-total-express-for-woocommerce'),
                'default' => '',
            ),
            'minimum_height' => array(
                'title' => __('Minimum Height (cm)', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('Minimum height of your shipping packages.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'minimum_width' => array(
                'title' => __('Minimum Width (cm)', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('Minimum width of your shipping packages.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'minimum_length' => array(
                'title' => __('Minimum Length (cm)', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('Minimum length of your shipping packages.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'extra_weight' => array(
                'title' => __('Extra Weight (kg)', 'datadev-total-express-for-woocommerce'),
                'type' => 'text',
                'description' => __('Extra weight in kilograms to add to the package total when quoting shipping costs.', 'datadev-total-express-for-woocommerce'),
                'desc_tip' => true,
                'default' => '0',
            ),
            'testing' => array(
                'title' => __('Testing', 'datadev-total-express-for-woocommerce'),
                'type' => 'title',
                'default' => '',
            ),
            'debug' => array(
                'title' => __('Debug Log', 'datadev-total-express-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable logging', 'datadev-total-express-for-woocommerce'),
                'default' => 'no',
                /* translators: %s: method title */
                'description' => sprintf(__('Log %s events, such as WebServices requests.', 'datadev-total-express-for-woocommerce'), $this->method_title) . $this->get_log_link(),
            ),
        );
    }

    /**
     * Datadev Total Express options page.
     */
    public function admin_options() {
        include Datadev_Total_Express::get_plugin_path() . 'includes/admin/views/html-admin-shipping-method-settings.php';
    }

    /**
     * Validate price field.
     *
     * Make sure the data is escaped correctly, etc.
     * Includes "%" back.
     *
     * @param  string $key   Field key.
     * @param  string $value Posted value.
     * @return string
     */
    public function validate_price_field($key, $value) {
        $value = is_null($value) ? '' : $value;
        $new_value = '' === $value ? '' : wc_format_decimal(trim(stripslashes($value)));

        if ('%' === substr($value, -1)) {
            $new_value .= '%';
        }

        return $new_value;
    }

    /**
     * Get Total Express modality code.
     *
     * @return string
     */
    public function get_code() {
        if (!empty($this->custom_code)) {
            $code = $this->custom_code;
        } else {
            $code = $this->code;
        }

        return apply_filters('datadev_total_express_shipping_method_code', $code, $this->id, $this->instance_id);
    }

    /**
     * Get user.
     *
     * @return string
     */
    public function get_user() {
        if (!empty($this->user)) {
            $user = $this->user;
        } else {
            $user = $this->user;
        }

        return apply_filters('datadev_total_express_shipping_user', $user, $this->id, $this->instance_id);
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function get_password() {
        if (!empty($this->password)) {
            $password = $this->password;
        } else {
            $password = $this->password;
        }

        return apply_filters('datadev_total_express_shipping_password', $password, $this->id, $this->instance_id);
    }

    /**
     * Get the declared value from the package.
     *
     * @param  array $package Cart package.
     *
     * @return float
     */
    protected function get_declared_value($package) {
        return $package['contents_cost'];
    }

    /**
     * Get shipping rate.
     *
     * @param  array $package Cart package.
     *
     * @return SimpleXMLElement|null
     */
    protected function get_rate($package) {
        $api = new Datadev_Total_Express_Webservice($this->id, $this->instance_id);
        $api->set_debug($this->debug);
        $api->set_modality($this->get_code());
        $api->set_package($package);
        $api->set_destination_postcode($package['destination']['postcode']);

        if ('yes' === $this->declare_value) {
            $api->set_declared_value($this->get_declared_value($package));
        }

        $api->set_user($this->get_user());
        $api->set_password($this->get_password());

        $api->set_minimum_height($this->minimum_height);
        $api->set_minimum_width($this->minimum_width);
        $api->set_minimum_length($this->minimum_length);
        $api->set_extra_weight($this->extra_weight);

        $shipping = $api->get_shipping();

        return $shipping;
    }

    /**
     * Get additional time.
     *
     * @param  array $package Package data.
     *
     * @return array
     */
    protected function get_additional_time($package = array()) {
        return apply_filters('datadev_total_express_shipping_additional_time', $this->additional_time, $package);
    }

    /**
     * Check if package uses only the selected shipping class.
     *
     * @param  array $package Cart package.
     * @return bool
     */
    protected function has_only_selected_shipping_class($package) {
        $only_selected = true;

        if (-1 === $this->shipping_class_id) {
            return $only_selected;
        }

        foreach ($package['contents'] as $item_id => $values) {
            $product = $values['data'];
            $qty = $values['quantity'];

            if ($qty > 0 && $product->needs_shipping()) {
                if ($this->shipping_class_id !== $product->get_shipping_class_id()) {
                    $only_selected = false;
                    break;
                }
            }
        }

        return $only_selected;
    }

    /**
     * Calculates the shipping rate.
     *
     * @param array $package Order package.
     */
    public function calculate_shipping($package = array()) {
        // Check if valid to be calculeted.
        if ('' === $package['destination']['postcode'] || 'BR' !== $package['destination']['country']) {
            return;
        }

        // Check for shipping classes.
        if (!$this->has_only_selected_shipping_class($package)) {
            return;
        }

        $shipping = $this->get_rate($package);

        if (!isset($shipping->ValorServico)) {
            return;
        }

        // Set the shipping rates.
        $label = $this->title;
        $cost = $this->string_to_float($shipping->ValorServico); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
        //$cost = (float) str_replace($shipping->ValorServico, ',', '.'); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
        // Exit if don't have price.
        if (0 === intval($cost)) {
            return;
        }

        // Apply fees.
        $fee = $this->get_fee($this->fee, $cost);

        // Display delivery.
        $meta_delivery = array();
        if ('yes' === $this->show_delivery_time) {
            $meta_delivery = array(
                '_delivery_forecast' => intval($shipping->Prazo) + intval($this->get_additional_time($package)), // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
            );
        }

        // Create the rate and apply filters.
        $rate = apply_filters(
                'woocommerce_datadev_total_express_' . $this->id . '_rate', array(
            'id' => $this->id . $this->instance_id,
            'label' => $label,
            'cost' => (float) $cost + (float) $fee,
            'meta_data' => $meta_delivery,
                ), $this->instance_id, $package
        );

        $rates = apply_filters('datadev_total_express_shipping_methods', array($rate), $package);
                
        // Add rate to WooCommerce.
        $this->add_rate($rates[0]);
    }

    public function string_to_float($string_number) {
        return floatval(str_replace(',', '.', str_replace('.', '', $string_number)));
    }

}
