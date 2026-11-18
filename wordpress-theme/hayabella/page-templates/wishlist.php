<?php
/**
 * Template Name: Wishlist
 *
 * Saved products are kept in the visitor's browser and loaded from WooCommerce here.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container page-wrap">
	<header class="page-head">
		<h1 class="page-head__title"><?php the_title(); ?> <span class="page-head__count" data-wl-count></span></h1>
	</header>
	<div data-wl hidden>
		<ul class="wl__grid" data-wl-grid aria-live="polite"></ul>
	</div>
	<div class="empty-state" data-wl-empty hidden>
		<p><?php esc_html_e( 'Your wishlist is empty. Tap the heart on any piece to save it here.', 'hayabella' ); ?></p>
		<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
			<a class="btn btn--dark" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Start shopping', 'hayabella' ); ?></a>
		<?php endif; ?>
	</div>
	<p class="wl-loading" data-wl-loading><?php esc_html_e( 'Loading your wishlist…', 'hayabella' ); ?></p>
	<?php
	while ( have_posts() ) {
		the_post();
		if ( '' !== trim( get_the_content() ) ) {
			echo '<div class="entry-content">';
			the_content();
			echo '</div>';
		}
	}
	?>
</div>
<?php
get_footer();
