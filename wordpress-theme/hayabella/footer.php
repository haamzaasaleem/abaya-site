<?php
/**
 * Footer: brand, three menu columns, socials, copyright, payments and credit.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="footer">
	<div class="container footer__grid">
		<div class="footer__brand">
			<?php if ( hayabella_mod_url( 'hb_footer_logo' ) ) : ?>
				<img src="<?php echo esc_url( hayabella_mod_url( 'hb_footer_logo' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="footer__logo" width="116" height="100" loading="lazy">
			<?php endif; ?>
			<?php if ( hayabella_mod( 'hb_footer_about' ) ) : ?>
				<p><?php echo esc_html( hayabella_mod( 'hb_footer_about' ) ); ?></p>
			<?php endif; ?>
			<div class="socials">
				<?php
				foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'pinterest' => 'Pinterest' ) as $key => $name ) {
					$url = hayabella_mod_url( "hb_social_{$key}" );
					if ( $url ) {
						printf( '<a href="%s" aria-label="%s" target="_blank" rel="noopener">', esc_url( $url ), esc_attr( $name ) );
						hayabella_icon( $key );
						echo '</a>';
					}
				}
				?>
			</div>
		</div>
		<?php
		foreach ( array( 'footer-1', 'footer-2', 'footer-3' ) as $loc ) {
			if ( ! has_nav_menu( $loc ) ) {
				continue;
			}
			$locations = get_nav_menu_locations();
			$menu      = wp_get_nav_menu_object( $locations[ $loc ] );
			echo '<details class="footer__col" open><summary>' . esc_html( $menu ? $menu->name : '' ) . '</summary>';
			wp_nav_menu(
				array(
					'theme_location' => $loc,
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'walker'         => new Hayabella_Flat_Walker(),
				)
			);
			echo '</details>';
		}
		?>
	</div>
	<div class="footer__bottom container">
		<span><?php echo esc_html( str_replace( '{year}', gmdate( 'Y' ), (string) hayabella_mod( 'hb_copyright' ) ) ); ?></span>
		<?php if ( hayabella_mod( 'hb_region' ) ) : ?>
			<span class="footer__region"><?php echo esc_html( hayabella_mod( 'hb_region' ) ); ?></span>
		<?php endif; ?>
		<?php
		$hb_pay = array_filter( array_map( 'trim', explode( ',', (string) hayabella_mod( 'hb_payments' ) ) ) );
		if ( $hb_pay ) :
			?>
			<div class="payments" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'hayabella' ); ?>">
				<?php foreach ( $hb_pay as $p ) : ?>
					<span><?php echo esc_html( $p ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="footer__credit container">
		<?php
		printf(
			/* translators: %s: Cybersia Labs link */
			esc_html__( 'Developed by %s', 'hayabella' ),
			'<a href="https://cybersialabs.com" target="_blank" rel="noopener">Cybersia Labs</a>'
		);
		?>
	</div>
</footer>

<div class="toast" role="status" aria-live="polite" data-toast></div>

<?php wp_footer(); ?>
</body>
</html>
