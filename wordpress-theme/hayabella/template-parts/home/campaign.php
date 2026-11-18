<?php
/**
 * Full-width campaign banner.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'campaign' ) || ! hayabella_mod_url( 'hb_campaign_image' ) ) {
	return;
}
$hb_link = hayabella_mod_url( 'hb_campaign_link' );
?>
<section class="campaign">
	<img src="<?php echo esc_url( hayabella_mod_url( 'hb_campaign_image' ) ); ?>" alt="<?php echo esc_attr( hayabella_mod( 'hb_campaign_title' ) ); ?>" loading="lazy">
	<div class="campaign__content">
		<?php if ( hayabella_mod( 'hb_campaign_eyebrow' ) ) : ?>
			<p class="eyebrow"><?php echo esc_html( hayabella_mod( 'hb_campaign_eyebrow' ) ); ?></p>
		<?php endif; ?>
		<?php if ( hayabella_mod( 'hb_campaign_title' ) ) : ?>
			<h2><?php echo esc_html( hayabella_mod( 'hb_campaign_title' ) ); ?></h2>
		<?php endif; ?>
		<?php if ( hayabella_mod( 'hb_campaign_text' ) ) : ?>
			<p><?php echo esc_html( hayabella_mod( 'hb_campaign_text' ) ); ?></p>
		<?php endif; ?>
		<?php if ( $hb_link && hayabella_mod( 'hb_campaign_cta' ) ) : ?>
			<a href="<?php echo esc_url( $hb_link ); ?>" class="btn btn--light"><?php echo esc_html( hayabella_mod( 'hb_campaign_cta' ) ); ?></a>
		<?php endif; ?>
	</div>
</section>
