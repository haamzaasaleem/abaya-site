<?php
/**
 * Single blog post.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'container page-wrap page-wrap--narrow' ); ?>>
		<header class="page-head">
			<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
			<h1 class="page-head__title"><?php the_title(); ?></h1>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="post-hero"><?php the_post_thumbnail( 'full' ); ?></div>
		<?php endif; ?>
		<div class="entry-content">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
		<?php
		the_post_navigation(
			array(
				'prev_text' => '<span>' . esc_html__( 'Previous', 'hayabella' ) . '</span> %title',
				'next_text' => '<span>' . esc_html__( 'Next', 'hayabella' ) . '</span> %title',
			)
		);
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</article>
	<?php
endwhile;

get_footer();
