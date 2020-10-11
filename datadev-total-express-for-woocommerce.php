<?php
/*
Plugin Name:          Datadev - Total Express for WooCommerce
Plugin URI:           https://github.com/datadev/datadev-total-express-for-woocommerce
Description:          Adds Total Express shipping methods to your WooCommerce store.
Author:               Datadev
Author URI:           https://www.datadev.com.br
Version:              1.1.0
License:              GPLv2 or later
Text Domain:          datadev-total-express-for-woocommerce
Domain Path:          /languages
WC requires at least: 3.8.0
WC tested up to:      4.5.2

Datadev - Total Express for WooCommerce is free software: you can
redistribute it and/or modify it under the terms of the
GNU General Public License as published by the Free Software Foundation,
either version 2 of the License, or any later version.

Datadev - Total Express for WooCommerce is distributed in the hope that it
will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Datadev - Total Express for WooCommerce. If not, see
<https://www.gnu.org/licenses/gpl-2.0.txt>.

@package Datadev_Total_Express
*/

defined( 'ABSPATH' ) || exit;

define( 'DATADEV_TOTAL_EXPRESS_VERSION', '1.0.0' );
define( 'DATADEV_TOTAL_EXPRESS_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'Datadev_Total_Express' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-datadev-total-express.php';

	add_action( 'plugins_loaded', array( 'Datadev_Total_Express', 'init' ) );
}
