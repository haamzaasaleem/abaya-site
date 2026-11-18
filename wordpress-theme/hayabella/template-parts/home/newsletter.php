<?php
/**
 * Newsletter sign-up. Uses the shortcode from Customize when one is set.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'newsletter' ) ) {
	return;
}
$hb_shortcode = trim( (string) hayabella_mod( 'hb_news_shortcode' ) );
?>
<section class="newsletter" id="newsletter">
	<div class="container newsletter__inner">
		<?php if ( hayabella_mod( 'hb_news_title' ) ) : ?>
			<h2><?php echo esc_html( hayabella_mod( 'hb_news_title' ) ); ?></h2>
		<?php endif; ?>
		<?php if ( hayabella_mod( 'hb_news_text' ) ) : ?>
			<p><?php echo esc_html( hayabella_mod( 'hb_news_text' ) ); ?></p>
		<?php endif; ?>
		<?php if ( $hb_shortcode ) : ?>
			<div class="newsletter__plugin"><?php echo do_shortcode( $hb_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php else : ?>
			<form class="newsletter__form" data-newsletter novalidate>
				<input type="email" placeholder="<?php esc_attr_e( 'Enter your email address', 'hayabella' ); ?>" aria-label="<?php esc_attr_e( 'Email address', 'hayabella' ); ?>" required>
				<button type="submit" class="btn btn--dark"><?php esc_html_e( 'Subscribe', 'hayabella' ); ?></button>
			</form>
			<p class="newsletter__msg" data-newsletter-msg role="status"></p>
		<?php endif; ?>
	</div>
</section>
