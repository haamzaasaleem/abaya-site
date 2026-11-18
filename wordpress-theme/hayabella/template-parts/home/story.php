<?php
/**
 * Brand story.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'story' ) ) {
	return;
}
$hb_link = hayabella_mod_url( 'hb_story_link' );
?>
<section class="story">
	<div class="container story__inner">
		<?php if ( hayabella_mod( 'hb_story_eyebrow' ) ) : ?>
			<p class="eyebrow"><?php echo esc_html( hayabella_mod( 'hb_story_eyebrow' ) ); ?></p>
		<?php endif; ?>
		<?php if ( hayabella_mod( 'hb_story_title' ) ) : ?>
			<h2 class="story__title"><?php echo esc_html( hayabella_mod( 'hb_story_title' ) ); ?></h2>
		<?php endif; ?>
		<?php if ( hayabella_mod( 'hb_story_text' ) ) : ?>
			<p><?php echo esc_html( hayabella_mod( 'hb_story_text' ) ); ?></p>
		<?php endif; ?>
		<?php if ( $hb_link && hayabella_mod( 'hb_story_cta' ) ) : ?>
			<a href="<?php echo esc_url( $hb_link ); ?>" class="link-underline"><?php echo esc_html( hayabella_mod( 'hb_story_cta' ) ); ?></a>
		<?php endif; ?>
	</div>
</section>
