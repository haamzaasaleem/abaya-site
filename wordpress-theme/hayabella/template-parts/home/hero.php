<?php
/**
 * Hero slider (Customize → Hayabella: Homepage → Hero slider).
 *
 * On each change the current slide drops to black, black holds briefly, then the next slide
 * fades up while settling from 120% to 100% size and stays still; its caption follows.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

$hb_slides = array();
for ( $i = 1; $i <= HAYABELLA_SLIDES; $i++ ) {
	if ( ! hayabella_mod( "hb_slide_{$i}_on" ) ) {
		continue;
	}
	$s = array(
		'type'  => hayabella_mod( "hb_slide_{$i}_type" ),
		'desk'  => hayabella_mod_url( "hb_slide_{$i}_desk" ),
		'mob'   => hayabella_mod_url( "hb_slide_{$i}_mob" ),
		'vdesk' => hayabella_mod_url( "hb_slide_{$i}_vdesk" ),
		'vmob'  => hayabella_mod_url( "hb_slide_{$i}_vmob" ),
		'title' => hayabella_mod( "hb_slide_{$i}_title" ),
		'cta'   => hayabella_mod( "hb_slide_{$i}_cta" ),
		'link'  => hayabella_mod_url( "hb_slide_{$i}_link" ),
		'alt'   => hayabella_mod( "hb_slide_{$i}_alt" ),
	);
	if ( 'video' === $s['type'] && ! $s['vdesk'] && ! $s['vmob'] ) {
		$s['type'] = 'photo';
	}
	if ( 'video' !== $s['type'] && ! $s['desk'] && ! $s['mob'] ) {
		continue;
	}
	$hb_slides[] = $s;
}
if ( ! $hb_slides ) {
	return;
}
$hb_total = count( $hb_slides );
?>
<section class="hero" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured collections', 'hayabella' ); ?>" data-slider>
	<h1 class="sr-only"><?php echo esc_html( get_bloginfo( 'name' ) . ( get_bloginfo( 'description' ) ? ', ' . get_bloginfo( 'description' ) : '' ) ); ?></h1>
	<div class="hero__slides">
		<?php
		foreach ( $hb_slides as $n => $s ) :
			$label  = $s['title'] ? $s['title'] : $s['alt'];
			$active = 0 === $n ? ' is-active' : '';
			$lazy   = 0 === $n ? 'eager' : 'lazy';

			if ( 'video' === $s['type'] ) :
				$vdesk  = $s['vdesk'] ? $s['vdesk'] : $s['vmob'];
				$vmob   = $s['vmob'] ? $s['vmob'] : $s['vdesk'];
				$pdesk  = $s['desk'] ? $s['desk'] : $s['mob'];
				$pmob   = $s['mob'] ? $s['mob'] : $s['desk'];
				?>
				<div class="hero__slide hero__slide--video<?php echo esc_attr( $active ); ?>" aria-roledescription="slide" aria-label="<?php echo esc_attr( $label ); ?>" data-video-slide>
					<div class="hero__videos">
						<video class="hero__video hero__video--desktop"<?php echo $pdesk ? ' poster="' . esc_url( $pdesk ) . '"' : ''; ?> muted loop playsinline preload="none" aria-hidden="true" tabindex="-1">
							<source src="<?php echo esc_url( $vdesk ); ?>" type="<?php echo esc_attr( hayabella_video_type( $vdesk ) ); ?>">
						</video>
						<video class="hero__video hero__video--mobile"<?php echo $pmob ? ' poster="' . esc_url( $pmob ) . '"' : ''; ?> muted loop playsinline preload="none" aria-hidden="true" tabindex="-1">
							<source src="<?php echo esc_url( $vmob ); ?>" type="<?php echo esc_attr( hayabella_video_type( $vmob ) ); ?>">
						</video>
					</div>
					<?php if ( $s['title'] || $s['cta'] ) : ?>
						<div class="hero__caption">
							<?php if ( $s['title'] ) : ?>
								<p class="hero__ctitle"><?php echo esc_html( $s['title'] ); ?></p>
							<?php endif; ?>
							<?php if ( $s['cta'] && $s['link'] ) : ?>
								<a href="<?php echo esc_url( $s['link'] ); ?>" class="hero__cta"><?php echo esc_html( $s['cta'] ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<button type="button" class="hero__vtoggle" aria-label="<?php esc_attr_e( 'Pause video', 'hayabella' ); ?>" data-hero-vtoggle><span></span></button>
				</div>
				<?php
			else :
				$tag  = $s['link'] ? 'a' : 'div';
				$href = $s['link'] ? ' href="' . esc_url( $s['link'] ) . '"' : '';
				$desk = $s['desk'] ? $s['desk'] : $s['mob'];
				?>
				<<?php echo $tag . $href; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag is a/div, href escaped above. ?> class="hero__slide hero__slide--<?php echo esc_attr( $s['type'] ); ?><?php echo esc_attr( $active ); ?>" aria-roledescription="slide" aria-label="<?php echo esc_attr( trim( $label . ( $s['cta'] ? '. ' . $s['cta'] : '' ) ) ); ?>">
					<picture class="hero__media">
						<?php if ( $s['mob'] && $s['mob'] !== $desk ) : ?>
							<source media="(max-width: 767px)" srcset="<?php echo esc_url( $s['mob'] ); ?>">
						<?php endif; ?>
						<img src="<?php echo esc_url( $desk ); ?>" alt="<?php echo esc_attr( $s['alt'] ); ?>" loading="<?php echo esc_attr( $lazy ); ?>" decoding="async">
					</picture>
					<?php if ( 'photo' === $s['type'] && ( $s['title'] || $s['cta'] ) ) : ?>
						<span class="hero__caption">
							<?php if ( $s['title'] ) : ?>
								<span class="hero__ctitle"><?php echo esc_html( $s['title'] ); ?></span>
							<?php endif; ?>
							<?php if ( $s['cta'] ) : ?>
								<span class="hero__cta"><?php echo esc_html( $s['cta'] ); ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php
			endif;
		endforeach;
		?>
	</div>
	<?php if ( $hb_total > 1 ) : ?>
		<button class="hero__arrow hero__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'hayabella' ); ?>" data-prev><?php hayabella_icon( 'left' ); ?></button>
		<button class="hero__arrow hero__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next slide', 'hayabella' ); ?>" data-next><?php hayabella_icon( 'right' ); ?></button>
		<div class="hero__dots" role="tablist" aria-label="<?php esc_attr_e( 'Choose slide', 'hayabella' ); ?>" data-dots></div>
	<?php endif; ?>
</section>
