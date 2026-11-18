<?php
/**
 * Blog, archives and search results.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container page-wrap">
	<header class="page-head">
		<h1 class="page-head__title">
			<?php
			if ( is_search() ) {
				/* translators: %s: search terms */
				printf( esc_html__( 'Results for “%s”', 'hayabella' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				echo wp_kses_post( get_the_archive_title() );
			} elseif ( is_home() && ! is_front_page() ) {
				single_post_title();
			} else {
				esc_html_e( 'Journal', 'hayabella' );
			}
			?>
		</h1>
		<?php if ( is_archive() && get_the_archive_description() ) : ?>
			<div class="page-head__text"><?php echo wp_kses_post( get_the_archive_description() ); ?></div>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'post-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="post-card__media"><?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?></a>
					<?php endif; ?>
					<p class="post-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
					<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="post-card__excerpt"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<div class="pagination">
			<?php
			the_posts_pagination(
				array(
					'prev_text' => esc_html__( 'Previous', 'hayabella' ),
					'next_text' => esc_html__( 'Next', 'hayabella' ),
				)
			);
			?>
		</div>
	<?php else : ?>
		<div class="empty-state">
			<p><?php esc_html_e( 'Nothing found. Try another search.', 'hayabella' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
