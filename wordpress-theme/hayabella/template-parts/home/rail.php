<?php
/**
 * Product slider (one of three on the homepage). Products come from WooCommerce.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

$hb_n = isset( $args['n'] ) ? (int) $args['n'] : 1;
if ( ! class_exists( 'WooCommerce' ) || ! hayabella_show( "rail_{$hb_n}" ) ) {
	return;
}
$hb_products = hayabella_rail_products(
	hayabella_mod( "hb_rail_{$hb_n}_source" ),
	hayabella_mod( "hb_rail_{$hb_n}_cat" ),
	(int) hayabella_mod( "hb_rail_{$hb_n}_count" )
);
if ( ! $hb_products ) {
	return;
}
$hb_title = hayabella_mod( "hb_rail_{$hb_n}_title" );
$hb_link  = hayabella_mod_url( "hb_rail_{$hb_n}_link" );
?>
<section class="rail" aria-label="<?php echo esc_attr( $hb_title ); ?>" data-rail>
	<header class="rail__intro container">
		<?php if ( $hb_title ) : ?>
			<h2 class="section__title"><?php echo esc_html( $hb_title ); ?></h2>
		<?php endif; ?>
		<?php if ( hayabella_mod( "hb_rail_{$hb_n}_text" ) ) : ?>
			<p><?php echo esc_html( hayabella_mod( "hb_rail_{$hb_n}_text" ) ); ?></p>
		<?php endif; ?>
	</header>
	<div class="rail__body container">
		<div class="rail__viewport">
			<button class="rail__btn rail__btn--prev" type="button" aria-label="<?php esc_attr_e( 'Previous products', 'hayabella' ); ?>" data-rail-prev><?php hayabella_icon( 'left' ); ?></button>
			<div class="rail__track">
				<?php
				foreach ( $hb_products as $hb_product ) {
					hayabella_product_card( $hb_product );
				}
				?>
			</div>
			<button class="rail__btn rail__btn--next" type="button" aria-label="<?php esc_attr_e( 'Next products', 'hayabella' ); ?>" data-rail-next><?php hayabella_icon( 'right' ); ?></button>
		</div>
		<div class="rail__dots" data-rail-dots></div>
		<?php if ( $hb_link ) : ?>
			<div class="center"><a href="<?php echo esc_url( $hb_link ); ?>" class="link-underline"><?php esc_html_e( 'View All', 'hayabella' ); ?></a></div>
		<?php endif; ?>
	</div>
</section>
