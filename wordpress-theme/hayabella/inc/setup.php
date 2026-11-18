<?php
/**
 * Theme setup: supports, menus, widget areas, scripts and styles.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'hayabella_setup' );
/** Register theme features and menus. */
function hayabella_setup() {
	load_theme_textdomain( 'hayabella', HAYABELLA_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 364,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'customize-selective-refresh-widgets' );

	// WooCommerce.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary'  => __( 'Main menu (header)', 'hayabella' ),
			'mobile'   => __( 'Mobile menu (uses the main menu if empty)', 'hayabella' ),
			'footer-1' => __( 'Footer column 1', 'hayabella' ),
			'footer-2' => __( 'Footer column 2', 'hayabella' ),
			'footer-3' => __( 'Footer column 3', 'hayabella' ),
		)
	);
}

add_action( 'widgets_init', 'hayabella_widgets' );
/** Widget areas. */
function hayabella_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'Shop filters', 'hayabella' ),
			'id'            => 'shop-filters',
			'description'   => __( 'Shown beside the products on the shop and category pages. Add WooCommerce filter widgets here (by attribute, price, rating, category).', 'hayabella' ),
			'before_widget' => '<section id="%1$s" class="plp-filter widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="plp-filter__title">',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Blog sidebar', 'hayabella' ),
			'id'            => 'blog',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}

add_action( 'wp_enqueue_scripts', 'hayabella_assets' );
/** Styles and scripts. */
function hayabella_assets() {
	$v = HAYABELLA_VERSION;
	wp_enqueue_style( 'hayabella-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceVersion.MissingVersion
	wp_enqueue_style( 'hayabella', HAYABELLA_URI . '/assets/css/theme.css', array(), $v );
	wp_enqueue_style( 'hayabella-wp', HAYABELLA_URI . '/assets/css/wordpress.css', array( 'hayabella' ), $v );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'hayabella-woocommerce', HAYABELLA_URI . '/assets/css/woocommerce.css', array( 'hayabella' ), $v );
	}
	wp_add_inline_style( 'hayabella', hayabella_color_css() );

	wp_enqueue_script( 'hayabella', HAYABELLA_URI . '/assets/js/theme.js', array(), $v, true );
	wp_localize_script(
		'hayabella',
		'HAYABELLA',
		array(
			'store'       => class_exists( 'WooCommerce' ) ? esc_url_raw( rest_url( 'wc/store/v1/' ) ) : '',
			'nonce'       => class_exists( 'WooCommerce' ) ? wp_create_nonce( 'wc_store_api' ) : '',
			'cartUrl'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
			'wishlistUrl' => hayabella_wishlist_url(),
			'heroMs'      => (int) round( (float) hayabella_mod( 'hb_hero_duration' ) * 1000 ),
			'newsThanks'  => hayabella_mod( 'hb_news_thanks' ),
			'i18n'        => array(
				'added'       => __( '%s added to bag', 'hayabella' ),
				'viewBag'     => __( 'View bag', 'hayabella' ),
				'saved'       => __( '%s saved to wishlist', 'hayabella' ),
				'removed'     => __( '%s removed from wishlist', 'hayabella' ),
				'viewWish'    => __( 'View wishlist', 'hayabella' ),
				'addFailed'   => __( 'Sorry, that could not be added to your bag.', 'hayabella' ),
				'badEmail'    => __( 'Please enter a valid email address.', 'hayabella' ),
				'chooseOpts'  => __( 'Choose options', 'hayabella' ),
				'addToBag'    => __( 'Add to Bag', 'hayabella' ),
				'outOfStock'  => __( 'Out of stock', 'hayabella' ),
				'remove'      => __( 'Remove %s from wishlist', 'hayabella' ),
			),
		)
	);
	if ( is_page_template( 'page-templates/wishlist.php' ) ) {
		wp_enqueue_script( 'hayabella-wishlist', HAYABELLA_URI . '/assets/js/wishlist.js', array( 'hayabella' ), $v, true );
	}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_filter( 'wp_resource_hints', 'hayabella_resource_hints', 10, 2 );
/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls Hints.
 * @param string $rel  Relation.
 * @return array
 */
function hayabella_resource_hints( $urls, $rel ) {
	if ( 'preconnect' === $rel ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}

/** Brand colours from the Customizer as CSS variables. */
function hayabella_color_css() {
	$olive = sanitize_hex_color( hayabella_mod( 'hb_color_olive' ) );
	$cream = sanitize_hex_color( hayabella_mod( 'hb_color_cream' ) );
	$gold  = sanitize_hex_color( hayabella_mod( 'hb_color_gold' ) );
	$ink   = sanitize_hex_color( hayabella_mod( 'hb_color_ink' ) );
	$speed = max( 10, (int) hayabella_mod( 'hb_marquee_speed' ) );
	return sprintf(
		':root{--olive:%1$s;--olive-deep:%1$s;--cream:%2$s;--sand:%2$s;--gold:%3$s;--ink:%4$s;}.marquee__track{animation-duration:%5$ds;}',
		$olive ? $olive : '#45451b',
		$cream ? $cream : '#f4e9e2',
		$gold ? $gold : '#a8844f',
		$ink ? $ink : '#1e1b1a',
		$speed
	);
}

add_action( 'wp_head', 'hayabella_head_meta', 1 );
/** Theme colour and default favicon. */
function hayabella_head_meta() {
	echo '<meta name="theme-color" content="#1E1B1A">' . "\n";
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" href="' . esc_url( HAYABELLA_URI . '/assets/images/brand/hb-monogram.svg' ) . '" type="image/svg+xml">' . "\n";
	}
}

add_action( 'after_switch_theme', 'hayabella_activate' );
/** On activation, create a Wishlist page if there is none. */
function hayabella_activate() {
	if ( hayabella_wishlist_url() ) {
		return;
	}
	$id = wp_insert_post(
		array(
			'post_title'  => __( 'Wishlist', 'hayabella' ),
			'post_name'   => 'wishlist',
			'post_status' => 'publish',
			'post_type'   => 'page',
		)
	);
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_wp_page_template', 'page-templates/wishlist.php' );
		set_theme_mod( 'hb_wishlist_page', $id );
	}
}

add_filter( 'body_class', 'hayabella_body_class' );
/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function hayabella_body_class( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'hb-home';
	}
	return $classes;
}

/**
 * Fallback for the main menu before one is set up: link the top-level product categories.
 *
 * @param array $args wp_nav_menu args.
 */
function hayabella_menu_fallback( $args ) {
	$is_drawer = ! empty( $args['menu_class'] ) && false !== strpos( $args['menu_class'], 'drawer__list' );
	$class     = $is_drawer ? 'drawer__list' : 'nav__list container';
	$link_cls  = $is_drawer ? '' : 'nav__link';
	echo '<ul class="' . esc_attr( $class ) . '">';
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		printf( '<li><a class="%s" href="%s">%s</a></li>', esc_attr( $link_cls ), esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'Shop All', 'hayabella' ) );
		$cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => true,
				'number'     => 7,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);
		if ( ! is_wp_error( $cats ) ) {
			foreach ( $cats as $c ) {
				printf( '<li><a class="%s" href="%s">%s</a></li>', esc_attr( $link_cls ), esc_url( get_term_link( $c ) ), esc_html( $c->name ) );
			}
		}
	} else {
		wp_list_pages(
			array(
				'title_li' => '',
				'depth'    => 1,
			)
		);
	}
	echo '</ul>';
}

add_filter( 'nav_menu_link_attributes', 'hayabella_menu_link_class', 10, 4 );
/**
 * Give top-level header menu links the nav__link class; "Sale" items get the sale colour.
 *
 * @param array    $atts  Attributes.
 * @param WP_Post  $item  Menu item.
 * @param stdClass $args  Args.
 * @param int      $depth Depth.
 * @return array
 */
function hayabella_menu_link_class( $atts, $item, $args, $depth ) {
	if ( 'primary' === $args->theme_location && 0 === $depth ) {
		$atts['class'] = trim( ( isset( $atts['class'] ) ? $atts['class'] : '' ) . ' nav__link' );
	}
	if ( in_array( 'menu-sale', (array) $item->classes, true ) || 0 === strcasecmp( trim( $item->title ), 'sale' ) ) {
		$atts['class'] = trim( ( isset( $atts['class'] ) ? $atts['class'] : '' ) . ' nav__link--sale' );
	}
	return $atts;
}
