<?php
/**
 * Page not found.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container page-wrap empty-state">
	<p class="eyebrow">404</p>
	<h1 class="page-head__title"><?php esc_html_e( 'This page has moved on', 'hayabella' ); ?></h1>
	<p><?php esc_html_e( 'The page you were looking for could not be found. Try a search, or continue shopping.', 'hayabella' ); ?></p>
	<?php get_search_form(); ?>
	<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
		<p><a class="btn btn--dark" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'hayabella' ); ?></a></p>
	<?php endif; ?>
</div>
<?php
get_footer();
