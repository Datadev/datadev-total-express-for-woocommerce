<?php
/**
 * Datadev Total Express Express shipping method.
 *
 * @package Datadev_Total_Express/Classes/Shipping
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Total Express Expresso shipping method class.
 */
class Datadev_Total_Express_Shipping_Express extends Datadev_Total_Express_Shipping {

	/**
	 * Total Express Express code.
	 *
	 * @var string
	 */
	protected $code = 'EXP';

	/**
	 * Initialize Total Express Express.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'total-express-express';
		$this->method_title = __( 'Total Express Express', 'datadev-total-express-for-woocommerce' );
		$this->more_link    = 'https://total.abril.com.br/total-express/servicos/';

		parent::__construct( $instance_id );
	}

	/**
	 * Get the declared value from the package.
	 *
	 * @param  array $package Cart package.
	 * @return float
	 */
	protected function get_declared_value( $package ) {
		return $package['contents_cost'];
	}
}
