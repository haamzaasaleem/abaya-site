<?php
/**
 * Pages (including WooCommerce cart, checkout and account pages).
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

get_header();

$hb_is_shop_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );

while ( have_posts() ) :
	the_post();
	?>
	<div class="container page-wrap<?php echo $hb_is_shop_page ? ' page-wrap--shop' : ''; ?>">
		<header class="page-head">
			<h1 class="page-head__title"><?php the_title(); ?></h1>
		</header>
		<div class="entry-content">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
