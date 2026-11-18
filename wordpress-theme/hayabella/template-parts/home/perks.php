<?php
/**
 * Perks bar above the footer.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

if ( ! hayabella_show( 'perks' ) ) {
	return;
}
$hb_items = array();
for ( $i = 1; $i <= 3; $i++ ) {
	if ( hayabella_mod( "hb_perk_{$i}_title" ) || hayabella_mod( "hb_perk_{$i}_text" ) ) {
		$hb_items[] = $i;
	}
}
if ( ! $hb_items ) {
	return;
}
?>
<section class="perks" aria-label="<?php esc_attr_e( 'Shopping with us', 'hayabella' ); ?>">
	<div class="perks__inner container">
		<?php foreach ( $hb_items as $i ) : ?>
			<div class="perk">
				<span class="perk__icon"><?php hayabella_icon( hayabella_mod( "hb_perk_{$i}_icon" ) ); ?></span>
				<div><strong><?php echo esc_html( hayabella_mod( "hb_perk_{$i}_title" ) ); ?></strong><span><?php echo esc_html( hayabella_mod( "hb_perk_{$i}_text" ) ); ?></span></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
