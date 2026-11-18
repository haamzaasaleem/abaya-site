<?php
/**
 * A product in the shop grid, related products and upsells: the theme's product card.
 *
 * Based on WooCommerce's content-product.php (version 3.6.0).
 *
 * @package Hayabella
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'hb-grid__item', $product ); ?>>
	<?php hayabella_product_card( $product ); ?>
</li>
