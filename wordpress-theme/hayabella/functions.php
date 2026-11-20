<?php
/**
 * Hayabella theme functions.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

define( 'HAYABELLA_VERSION', '1.0.1' );
define( 'HAYABELLA_DIR', get_template_directory() );
define( 'HAYABELLA_URI', get_template_directory_uri() );

require HAYABELLA_DIR . '/inc/defaults.php';
require HAYABELLA_DIR . '/inc/helpers.php';
require HAYABELLA_DIR . '/inc/setup.php';
require HAYABELLA_DIR . '/inc/customizer.php';
require HAYABELLA_DIR . '/inc/deploy.php';

if ( class_exists( 'WooCommerce' ) ) {
	require HAYABELLA_DIR . '/inc/woocommerce.php';
	require HAYABELLA_DIR . '/inc/product-fields.php';
}
