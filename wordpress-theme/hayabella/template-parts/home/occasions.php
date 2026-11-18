<?php
/**
 * Three occasion tiles with optional looping videos.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'occasions' ) ) {
	return;
}
$hb_items = array();
for ( $i = 1; $i <= 3; $i++ ) {
	if ( hayabella_mod_url( "hb_occ_{$i}_image" ) || hayabella_mod_url( "hb_occ_{$i}_video" ) ) {
		$hb_items[] = $i;
	}
}
if ( ! $hb_items ) {
	return;
}
?>
<section class="section">
	<div class="container">
		<header class="section__head">
			<?php if ( hayabella_mod( 'hb_occasions_eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( hayabella_mod( 'hb_occasions_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<?php if ( hayabella_mod( 'hb_occasions_title' ) ) : ?>
				<h2 class="section__title"><?php echo esc_html( hayabella_mod( 'hb_occasions_title' ) ); ?></h2>
			<?php endif; ?>
		</header>
		<div class="occasions">
			<?php
			foreach ( $hb_items as $i ) :
				$img   = hayabella_mod_url( "hb_occ_{$i}_image" );
				$video = hayabella_mod_url( "hb_occ_{$i}_video" );
				$link  = hayabella_mod_url( "hb_occ_{$i}_link" );
				$tag   = $link ? 'a' : 'div';
				?>
				<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $link ? ' href="' . esc_url( $link ) . '"' : ''; ?> class="occasion">
					<?php if ( $img ) : ?>
						<img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
					<?php endif; ?>
					<?php hayabella_live_video( $video, $img ); ?>
					<div class="occasion__label">
						<h3><?php echo esc_html( hayabella_mod( "hb_occ_{$i}_title" ) ); ?></h3>
						<?php if ( $link && hayabella_mod( 'hb_occ_cta' ) ) : ?>
							<span class="link-underline"><?php echo esc_html( hayabella_mod( 'hb_occ_cta' ) ); ?></span>
						<?php endif; ?>
					</div>
				</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
