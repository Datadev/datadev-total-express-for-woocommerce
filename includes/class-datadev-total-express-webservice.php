<?php

/**
 * Datadev Total Express Webservice.
 *
 * @package Datadev_Total_Express/Classes/Webservice
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Datadev Total Express Webservice integration class.
 */
class Datadev_Total_Express_Webservice {

    /**
     * Webservice URL.
     *
     * @var string
     */
    private $_webservice = 'https://edi.totalexpress.com.br/webservice_calculo_frete.php?wsdl';

    /**
     * Shipping method ID.
     *
     * @var string
     */
    protected $id = '';

    /**
     * Shipping zone instance ID.
     *
     * @var int
     */
    protected $instance_id = 0;

    /**
     * ID from Total Express modality.
     *
     * @var string|array
     */
    protected $modality = '';

    /**
     * WooCommerce package containing the products.
     *
     * @var array
     */
    protected $package = null;

    /**
     * Destination postcode.
     *
     * @var string
     */
    protected $destination_postcode = '';

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
     * Package height.
     *
     * @var float
     */
    protected $height = 0;

    /**
     * Package width.
     *
     * @var float
     */
    protected $width = 0;

    /**
     * Package length.
     *
     * @var float
     */
    protected $length = 0;

    /**
     * Package weight.
     *
     * @var float
     */
    protected $weight = 0;

    /**
     * Minimum height.
     *
     * @var float
     */
    protected $minimum_height = 2;

    /**
     * Minimum width.
     *
     * @var float
     */
    protected $minimum_width = 11;

    /**
     * Minimum length.
     *
     * @var float
     */
    protected $minimum_length = 16;

    /**
     * Extra weight.
     *
     * @var float
     */
    protected $extra_weight = 0;

    /**
     * Declared value.
     *
     * @var string
     */
    protected $declared_value = '0';

    /**
     * Debug mode.
     *
     * @var string
     */
    protected $debug = 'no';

    /**
     * Logger.
     *
     * @var WC_Logger
     */
    protected $log = null;

    /**
     * Initialize webservice.
     *
     * @param string $id Method ID.
     * @param int    $instance_id Instance ID.
     */
    public function __construct($id = 'total-express', $instance_id = 0) {
        $this->id = $id;
        $this->instance_id = $instance_id;
        $this->log = new WC_Logger();
    }

    /**
     * Set the modality
     *
     * @param string|array $modality Service.
     */
    public function set_modality($modality = '') {
        if (is_array($modality)) {
            $this->modality = implode(',', $modality);
        } else {
            $this->modality = $modality;
        }
    }

    /**
     * Set shipping package.
     *
     * @param array $package Shipping package.
     */
    public function set_package($package = array()) {
        $this->package = $package;
        $total_express_package = new Datadev_Total_Express_Package($package);

        if (!is_null($total_express_package)) {
            $data = $total_express_package->get_data();

            $this->set_height($data['height']);
            $this->set_width($data['width']);
            $this->set_length($data['length']);
            $this->set_weight($data['weight']);
        }

        if ('yes' === $this->debug) {
            if (!empty($data)) {
                $data = array(
                    'weight' => $this->get_weight(),
                    'height' => $this->get_height(),
                    'width' => $this->get_width(),
                    'length' => $this->get_length(),
                );
            }

            $this->log->add($this->id, 'Weight and cubage of the order: ' . print_r($data, true));
        }
    }

    /**
     * Set destination postcode.
     *
     * @param string $postcode Destination postcode.
     */
    public function set_destination_postcode($postcode = '') {
        $this->destination_postcode = $postcode;
    }

    /**
     * Set user.
     *
     * @param string $user user.
     */
    public function set_user($user = '') {
        $this->user = $user;
    }

    /**
     * Set password.
     *
     * @param string $password password.
     */
    public function set_password($password = '') {
        $this->password = $password;
    }

    /**
     * Set shipping package height.
     *
     * @param float $height Package height.
     */
    public function set_height($height = 0) {
        $this->height = (float) $height;
    }

    /**
     * Set shipping package width.
     *
     * @param float $width Package width.
     */
    public function set_width($width = 0) {
        $this->width = (float) $width;
    }

    /**
     * Set shipping package length.
     *
     * @param float $length Package length.
     */
    public function set_length($length = 0) {
        $this->length = (float) $length;
    }

    /**
     * Set shipping package weight.
     *
     * @param float $weight Package weight.
     */
    public function set_weight($weight = 0) {
        $this->weight = (float) $weight;
    }

    /**
     * Set minimum height.
     *
     * @param float $minimum_height Package minimum height.
     */
    public function set_minimum_height($minimum_height = 0) {
        $this->minimum_height = (float) $minimum_height;
    }

    /**
     * Set minimum width.
     *
     * @param float $minimum_width Package minimum width.
     */
    public function set_minimum_width($minimum_width = 0) {
        $this->minimum_width = (float) $minimum_width;
    }

    /**
     * Set minimum length.
     *
     * @param float $minimum_length Package minimum length.
     */
    public function set_minimum_length($minimum_length = 0) {
        $this->minimum_length = (float) $minimum_length;
    }

    /**
     * Set extra weight.
     *
     * @param float $extra_weight Package extra weight.
     */
    public function set_extra_weight($extra_weight = 0) {
        $this->extra_weight = (float) wc_format_decimal($extra_weight);
    }

    /**
     * Set declared value.
     *
     * @param string $declared_value Declared value.
     */
    public function set_declared_value($declared_value = '0') {
        $this->declared_value = $declared_value;
    }

    /**
     * Set the debug mode.
     *
     * @param string $debug Yes or no.
     */
    public function set_debug($debug = 'no') {
        $this->debug = $debug;
    }

    /**
     * Get webservice URL.
     *
     * @return string
     */
    public function get_webservice_url() {
        return apply_filters('datadev_total_express_webservice_url', $this->_webservice, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get user.
     *
     * @return string
     */
    public function get_user() {
        return apply_filters('datadev_total_express_user', $this->user, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function get_password() {
        return apply_filters('datadev_password_password', $this->password, $this->id, $this->instance_id, $this->package);
    }

    /**
     * Get height.
     *
     * @return float
     */
    public function get_height() {
        return ($this->minimum_height <= $this->height ? $this->height : $this->minimum_height);
    }

    /**
     * Get width.
     *
     * @return float
     */
    public function get_width() {
        return ($this->minimum_width <= $this->width ? $this->width : $this->minimum_width);
    }

    /**
     * Get length.
     *
     * @return float
     */
    public function get_length() {
        return ($this->minimum_length <= $this->length ? $this->length : $this->minimum_length);
    }

    /**
     * Get weight.
     *
     * @return float
     */
    public function get_weight() {
        return ($this->weight + $this->extra_weight);
    }

    /**
     * Check if is available.
     *
     * @return bool
     */
    protected function is_available() {
        return !empty($this->modality) || !empty($this->destination_postcode) || 0 === $this->get_height();
    }

    /**
     * Get shipping prices.
     *
     * @return stdClass|array
     */
    public function get_shipping() {
        $shipping = null;

        // Checks if service and postcode are empty.
        if (!$this->is_available()) {
            return $shipping;
        }

        $params = apply_filters('datadev_total_express_shipping_params', array(
            'TipoServico' => $this->modality,
            'CepDestino' => wc_datadev_total_express_sanitize_postcode($this->destination_postcode),
            'Peso' => number_format($this->get_weight(), 2, ',', ''),
            'ValorDeclarado' => number_format($this->declared_value, 2, ',', ''),
            'TipoEntrega' => 0,
            'ServicoCOD' => false,
            'Altura' => number_format($this->get_height(), 2, ',', ''),
            'Largura' => number_format($this->get_width(), 2, ',', ''),
            'Profundidade' => number_format($this->get_length(), 2, ',', ''),
                ), $this->id, $this->instance_id, $this->package);

        $url = $this->get_webservice_url();

        if ('yes' === $this->debug) {
            $this->log->add($this->id, 'Requesting Total Express WebServices: ' . $url);
            $this->log->add($this->id, 'Params: ' . print_r($params, true));
        }

        try {
            $auth = 'Basic ' . base64_encode($this->get_user() . ':' . $this->get_password());

            $args = array(
                'timeout' => 5,
                'headers' => array(
                    'Authorization' => $auth,
                ),
            );
            
            $responseWSDL = wp_safe_remote_get(esc_url_raw($url), $args);
            if (is_wp_error($responseWSDL)){
                if ('yes' === $this->debug) {
                    $this->log->add($this->id, 'WP_Error: ' . $responseWSDL->get_error_message());
                }
                return $shipping;
                
            }
                           
            $body = wp_remote_retrieve_body($responseWSDL);
            if (!$this->validateWSDLResponse($body)) {
                if ('yes' === $this->debug) {
                    $this->log->add($this->id, 'Total Express server response: ' . $body);
                }
                return $shipping;
            }
            
            $wsdl = 'data://text/plain;base64,' . base64_encode($body);

            $options = array(
                'stream_context' => stream_context_create(
                        array(
                            'http' => array(
                                'header' => 'Authorization: ' . $auth,
                            )
                        )
                )
            );
            $soap = new SoapClient($wsdl, $options);

            $responseCalculo = $soap->calcularFrete($params);
            if ('yes' === $this->debug) {
                $this->log->add($this->id, 'Response: ' . print_r($responseCalculo, true));
            }
            if ($responseCalculo->CodigoProc == 1) {
                if (isset($responseCalculo->DadosFrete)) {
                    $shipping = $responseCalculo->DadosFrete;
                }
            }
        } catch (Exception $ex) {
            $this->log->add($this->id, 'Fail: ' . $ex->getMessage());
        }

        return $shipping;
    }
    
    private function validateWSDLResponse($response) {
        return strpos($response, '<') === 0;
    }

}
