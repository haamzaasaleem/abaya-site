<?php
/**
 * Lookbook / Instagram strip.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'looks' ) ) {
	return;
}
$hb_imgs = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$u = hayabella_mod_url( "hb_look_{$i}" );
	if ( $u ) {
		$hb_imgs[] = $u;
	}
}
if ( ! $hb_imgs ) {
	return;
}
$hb_link = hayabella_mod_url( 'hb_looks_link' );
$hb_tag  = $hb_link ? 'a' : 'div';
?>
<section class="section section--tight">
	<div class="container">
		<header class="section__head">
			<?php if ( hayabella_mod( 'hb_looks_eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( hayabella_mod( 'hb_looks_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<?php if ( hayabella_mod( 'hb_looks_title' ) ) : ?>
				<h2 class="section__title"><?php echo esc_html( hayabella_mod( 'hb_looks_title' ) ); ?></h2>
			<?php endif; ?>
		</header>
	</div>
	<div class="looks">
		<?php foreach ( $hb_imgs as $n => $u ) : ?>
			<<?php echo $hb_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $hb_link ? ' href="' . esc_url( $hb_link ) . '" target="_blank" rel="noopener"' : ''; ?> class="look">
				<?php /* translators: %d: photo number */ ?>
				<img src="<?php echo esc_url( $u ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Look %d', 'hayabella' ), $n + 1 ) ); ?>" loading="lazy">
				<span><?php hayabella_icon( 'instagram' ); ?></span>
			</<?php echo $hb_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php endforeach; ?>
	</div>
</section>
