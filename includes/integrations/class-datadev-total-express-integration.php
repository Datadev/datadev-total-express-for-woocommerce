<?php
/**
 * Datadev Total Express integration.
 *
 * @package Datadev_Total_Express/Classes/Integration
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Total Express integration class.
 */
class Datadev_Total_Express_Integration extends WC_Integration {

    /**
     * Initialize integration actions.
     */
    public function __construct() {
        $this->id = 'datadev-total-express-integration';
        $this->method_title = __('Total Express', 'datadev-total-express-for-woocommerce');

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Actions.
        add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * Total Express options page.
     */
    public function admin_options() {
        echo '<h2>' . esc_html($this->get_method_title()) . '</h2>';
        echo wp_kses_post(wpautop($this->get_method_description()));

        include Datadev_Total_Express::get_plugin_path() . 'includes/admin/views/html-admin-help-message.php';

        echo '<div><input type="hidden" name="section" value="' . esc_attr($this->id) . '" /></div>';
        echo '<table class="form-table">' . $this->generate_settings_html($this->get_form_fields(), false) . '</table>'; // WPCS: XSS ok.
    }

    /**
     * Generate Button Input HTML.
     *
     * @param string $key  Input key.
     * @param array  $data Input data.
     * @return string
     */
    public function generate_button_html($key, $data) {
        $field_key = $this->get_field_key($key);
        $defaults = array(
            'title' => '',
            'label' => '',
            'desc_tip' => false,
            'description' => '',
        );

        $data = wp_parse_args($data, $defaults);

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?></label>
                <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok.  ?>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
                    <button class="button-secondary" type="button" id="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['label']); ?></button>
                    <?php echo $this->get_description_html($data); // WPCS: XSS ok.  ?>
                </fieldset>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

}
