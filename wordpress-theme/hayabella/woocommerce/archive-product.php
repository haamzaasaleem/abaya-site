<?php
/**
 * Shop, category and tag pages: title, sub-category chips, filters sidebar and the product grid.
 *
 * Based on WooCommerce's archive-product.php (version 8.6.0).
 *
 * @package Hayabella
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

$hb_has_filters = is_active_sidebar( 'shop-filters' );
?>
<header class="plp-hero">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="plp-hero__title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>
	<div class="plp-hero__text"><?php do_action( 'woocommerce_archive_description' ); ?></div>
	<?php
	// Sub-categories (or top-level categories on the shop page) as chips.
	$hb_parent = is_product_category() ? get_queried_object_id() : 0;
	$hb_cats   = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $hb_parent,
			'hide_empty' => true,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	if ( $hb_cats && ! is_wp_error( $hb_cats ) && ( is_shop() || is_product_category() ) ) :
		?>
		<nav class="plp-cats" aria-label="<?php esc_attr_e( 'Categories', 'hayabella' ); ?>">
			<?php foreach ( $hb_cats as $hb_cat ) : ?>
				<a class="chip" href="<?php echo esc_url( get_term_link( $hb_cat ) ); ?>"><?php echo esc_html( $hb_cat->name ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>
</header>

<?php if ( woocommerce_product_loop() ) : ?>
	<div class="plp-toolbar">
		<?php if ( $hb_has_filters ) : ?>
			<button type="button" class="btn btn--outline plp-toolbar__filters" data-open-filters><?php esc_html_e( 'Filters', 'hayabella' ); ?></button>
		<?php endif; ?>
		<?php do_action( 'woocommerce_before_shop_loop' ); ?>
	</div>

	<div class="plp<?php echo $hb_has_filters ? ' plp--with-filters' : ''; ?>">
		<?php if ( $hb_has_filters ) : ?>
			<aside class="plp-filters" aria-label="<?php esc_attr_e( 'Filters', 'hayabella' ); ?>" data-filters>
				<div class="plp-filters__head">
					<strong><?php esc_html_e( 'Filters', 'hayabella' ); ?></strong>
					<button type="button" class="icon-btn" aria-label="<?php esc_attr_e( 'Close filters', 'hayabella' ); ?>" data-close-filters><?php hayabella_icon( 'close' ); ?></button>
				</div>
				<?php dynamic_sidebar( 'shop-filters' ); ?>
			</aside>
		<?php endif; ?>
		<div class="plp-main">
			<?php
			woocommerce_product_loop_start();
			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}
			woocommerce_product_loop_end();
			do_action( 'woocommerce_after_shop_loop' );
			?>
		</div>
	</div>
<?php else : ?>
	<?php do_action( 'woocommerce_no_products_found' ); ?>
<?php endif; ?>

<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
