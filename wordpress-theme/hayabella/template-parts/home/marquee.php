<?php
/**
 * Endless scrolling brand line.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'marquee' ) ) {
	return;
}
$hb_phrases = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) hayabella_mod( 'hb_marquee_text' ) ) ) ) );
if ( ! $hb_phrases ) {
	return;
}
// Repeat the phrases so one group is wider than the screen; the second group is a copy for a seamless loop.
$hb_group = '';
$hb_reps  = max( 2, (int) ceil( 6 / count( $hb_phrases ) ) );
for ( $r = 0; $r < $hb_reps; $r++ ) {
	foreach ( $hb_phrases as $p ) {
		$hb_group .= '<span>' . esc_html( $p ) . '</span><span class="marquee__sep">|</span>';
	}
}
?>
<section class="marquee" aria-label="<?php echo esc_attr( implode( '. ', $hb_phrases ) ); ?>">
	<div class="marquee__track" aria-hidden="true">
		<div class="marquee__group"><?php echo $hb_group; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></div>
		<div class="marquee__group"><?php echo $hb_group; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></div>
	</div>
	<?php if ( hayabella_mod( 'hb_marquee_eyebrow' ) ) : ?>
		<p class="marquee__eyebrow"><?php echo esc_html( hayabella_mod( 'hb_marquee_eyebrow' ) ); ?></p>
	<?php endif; ?>
	<?php if ( hayabella_mod( 'hb_marquee_sub' ) ) : ?>
		<p class="marquee__sub"><?php echo esc_html( hayabella_mod( 'hb_marquee_sub' ) ); ?></p>
	<?php endif; ?>
</section>
