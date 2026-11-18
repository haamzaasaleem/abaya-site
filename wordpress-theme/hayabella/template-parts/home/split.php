<?php
/**
 * Two banners side by side.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'split' ) ) {
	return;
}
$hb_items = array();
for ( $i = 1; $i <= 2; $i++ ) {
	if ( hayabella_mod_url( "hb_split_{$i}_image" ) ) {
		$hb_items[] = $i;
	}
}
if ( ! $hb_items ) {
	return;
}
?>
<section class="split">
	<?php
	foreach ( $hb_items as $i ) :
		$link = hayabella_mod_url( "hb_split_{$i}_link" );
		$tag  = $link ? 'a' : 'div';
		?>
		<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $link ? ' href="' . esc_url( $link ) . '"' : ''; ?> class="split__item">
			<img src="<?php echo esc_url( hayabella_mod_url( "hb_split_{$i}_image" ) ); ?>" alt="<?php echo esc_attr( hayabella_mod( "hb_split_{$i}_title" ) ); ?>" loading="lazy">
			<div class="split__content split__content--light">
				<?php if ( hayabella_mod( "hb_split_{$i}_eyebrow" ) ) : ?>
					<p class="eyebrow"><?php echo esc_html( hayabella_mod( "hb_split_{$i}_eyebrow" ) ); ?></p>
				<?php endif; ?>
				<?php if ( hayabella_mod( "hb_split_{$i}_title" ) ) : ?>
					<h3><?php echo esc_html( hayabella_mod( "hb_split_{$i}_title" ) ); ?></h3>
				<?php endif; ?>
				<?php if ( hayabella_mod( "hb_split_{$i}_cta" ) ) : ?>
					<span class="link-underline"><?php echo esc_html( hayabella_mod( "hb_split_{$i}_cta" ) ); ?></span>
				<?php endif; ?>
			</div>
		</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php endforeach; ?>
</section>
