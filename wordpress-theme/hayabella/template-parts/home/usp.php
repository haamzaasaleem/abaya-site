<?php
/**
 * Feature strip under the hero.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'usp' ) ) {
	return;
}
$hb_items = array();
for ( $i = 1; $i <= 4; $i++ ) {
	if ( hayabella_mod( "hb_usp_{$i}_title" ) || hayabella_mod( "hb_usp_{$i}_text" ) ) {
		$hb_items[] = $i;
	}
}
if ( ! $hb_items ) {
	return;
}
?>
<section class="usp" aria-label="<?php esc_attr_e( 'Why shop with us', 'hayabella' ); ?>">
	<div class="usp__inner container">
		<?php foreach ( $hb_items as $i ) : ?>
			<div class="usp__item">
				<?php hayabella_icon( hayabella_mod( "hb_usp_{$i}_icon" ) ); ?>
				<div><strong><?php echo esc_html( hayabella_mod( "hb_usp_{$i}_title" ) ); ?></strong><span><?php echo esc_html( hayabella_mod( "hb_usp_{$i}_text" ) ); ?></span></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
