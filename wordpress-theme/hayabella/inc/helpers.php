<?php
/**
 * Small helpers used by the templates.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

/**
 * A theme setting, falling back to its default.
 *
 * @param string $key Setting name.
 * @return mixed
 */
function hayabella_mod( $key ) {
	$d = hayabella_defaults();
	return get_theme_mod( $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

/**
 * Turn {theme} / {shop} placeholders (and attachment IDs) into real URLs.
 *
 * @param string|int $value URL, placeholder path or attachment ID.
 * @return string
 */
function hayabella_url( $value ) {
	if ( is_numeric( $value ) && (int) $value > 0 ) {
		$u = wp_get_attachment_url( (int) $value );
		return $u ? $u : '';
	}
	$value = (string) $value;
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, '{theme}' ) ) {
		return HAYABELLA_URI . substr( $value, 7 );
	}
	if ( 0 === strpos( $value, '{shop}' ) ) {
		$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
		$rest = substr( $value, 6 );
		if ( '' !== $rest && '?' === $rest[0] ) {
			return add_query_arg( wp_parse_args( substr( $rest, 1 ) ), $shop );
		}
		return $shop . ltrim( $rest, '/' );
	}
	return $value;
}

/**
 * A setting resolved as a URL.
 *
 * @param string $key Setting name.
 * @return string
 */
function hayabella_mod_url( $key ) {
	return hayabella_url( hayabella_mod( $key ) );
}

/**
 * Echo an SVG icon from the sprite in footer.php.
 *
 * @param string $name  Icon name (search, user, heart, bag, menu, close, left, right, truck, return, lock, flag, instagram, facebook, tiktok, chat, pinterest).
 * @param string $class Optional class.
 */
function hayabella_icon( $name, $class = '' ) {
	printf( '<svg%s aria-hidden="true"><use href="#i-%s"/></svg>', $class ? ' class="' . esc_attr( $class ) . '"' : '', esc_attr( $name ) );
}

/** Icons offered in the Customizer for the feature strip and perks. */
function hayabella_icon_choices() {
	return array(
		'truck'  => __( 'Delivery truck', 'hayabella' ),
		'return' => __( 'Returns arrow', 'hayabella' ),
		'flag'   => __( 'Flag', 'hayabella' ),
		'lock'   => __( 'Padlock', 'hayabella' ),
		'heart'  => __( 'Heart', 'hayabella' ),
		'chat'   => __( 'Chat bubble', 'hayabella' ),
		'bag'    => __( 'Shopping bag', 'hayabella' ),
		'user'   => __( 'Person', 'hayabella' ),
	);
}

/**
 * URL of the wishlist page (a page using the Wishlist template).
 *
 * @return string
 */
function hayabella_wishlist_url() {
	$id = (int) get_theme_mod( 'hb_wishlist_page', 0 );
	if ( ! $id ) {
		$pages = get_pages(
			array(
				'meta_key'   => '_wp_page_template',
				'meta_value' => 'page-templates/wishlist.php',
				'number'     => 1,
			)
		);
		$id = $pages ? $pages[0]->ID : 0;
	}
	return $id ? get_permalink( $id ) : '';
}

/**
 * Whether a homepage section is switched on.
 *
 * @param string $name Section name.
 * @return bool
 */
function hayabella_show( $name ) {
	return (bool) hayabella_mod( 'hb_show_' . $name );
}

/**
 * Output a muted looping video used by cards and tiles (plays only while on screen, see theme.js).
 *
 * @param string $src    Video URL.
 * @param string $poster Poster image URL.
 */
function hayabella_live_video( $src, $poster = '' ) {
	if ( ! $src ) {
		return;
	}
	printf(
		'<video class="live-video" muted loop playsinline preload="none"%s aria-hidden="true" tabindex="-1" data-live><source src="%s" type="%s"></video>',
		$poster ? ' poster="' . esc_url( $poster ) . '"' : '',
		esc_url( $src ),
		esc_attr( hayabella_video_type( $src ) )
	);
}

/**
 * MIME type for a video URL.
 *
 * @param string $src URL.
 * @return string
 */
function hayabella_video_type( $src ) {
	$ext = strtolower( pathinfo( wp_parse_url( $src, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$map = array(
		'webm' => 'video/webm',
		'mov'  => 'video/quicktime',
		'ogv'  => 'video/ogg',
	);
	return isset( $map[ $ext ] ) ? $map[ $ext ] : 'video/mp4';
}

/**
 * Menu walker that outputs plain links (footer columns).
 */
class Hayabella_Flat_Walker extends Walker_Nav_Menu {
	/**
	 * Start element.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$target  = $item->target ? ' target="' . esc_attr( $item->target ) . '" rel="noopener"' : '';
		$output .= '<a href="' . esc_url( $item->url ) . '"' . $target . '>' . esc_html( $item->title ) . '</a>';
	}

	/**
	 * End element (nothing to close).
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}
