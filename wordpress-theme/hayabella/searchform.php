<?php
/**
 * Search form.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="hb-s-<?php echo esc_attr( wp_unique_id() ); ?>"><?php esc_html_e( 'Search', 'hayabella' ); ?></label>
	<input type="search" class="search-form__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search the store…', 'hayabella' ); ?>">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit" class="btn btn--dark"><?php esc_html_e( 'Search', 'hayabella' ); ?></button>
</form>
