<?php

/**
 * Datadev Total Express functions.
 *
 * @package Datadev_Total_Express/Functions
 * @since   1.0.0
 * @version 1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize postcode.
 *
 * @param  string $postcode Postcode.
 *
 * @return string
 */
function wc_datadev_total_express_sanitize_postcode($postcode) {
    return preg_replace('([^0-9])', '', sanitize_text_field($postcode));
}