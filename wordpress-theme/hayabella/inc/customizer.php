<?php
/**
 * Customizer: every homepage section, the header, footer and brand colours are edited here
 * (Appearance → Customize).
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

/**
 * Keep links that use the {shop} / {theme} placeholders, otherwise sanitise as a URL.
 *
 * @param string $value Value.
 * @return string
 */
function hayabella_sanitize_link( $value ) {
	$value = trim( (string) $value );
	if ( preg_match( '/^\{(shop|theme)\}/', $value ) ) {
		return sanitize_text_field( $value );
	}
	if ( '' === $value || '#' === $value ) {
		return $value;
	}
	return esc_url_raw( $value );
}

/** Checkbox sanitiser. */
function hayabella_sanitize_bool( $value ) {
	return (bool) $value;
}

/**
 * Choice sanitiser factory.
 *
 * @param array $choices Allowed keys.
 * @return callable
 */
function hayabella_sanitize_choice( $choices ) {
	return function ( $value, $setting ) use ( $choices ) {
		return array_key_exists( $value, $choices ) ? $value : $setting->default;
	};
}

/**
 * Add one setting + control.
 *
 * @param WP_Customize_Manager $wp      Manager.
 * @param string               $section Section id.
 * @param string               $key     Setting name.
 * @param string               $label   Label.
 * @param string               $type    text | textarea | link | image | video | checkbox | select | number | color | page.
 * @param array                $extra   Extra control args (choices, description, input_attrs).
 */
function hayabella_control( $wp, $section, $key, $label, $type = 'text', $extra = array() ) {
	$defaults = hayabella_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$sanitize = 'sanitize_text_field';

	switch ( $type ) {
		case 'textarea':
			$sanitize = 'sanitize_textarea_field';
			break;
		case 'link':
			$sanitize = 'hayabella_sanitize_link';
			break;
		case 'image':
		case 'video':
			$sanitize = 'esc_url_raw';
			$default  = hayabella_url( $default );
			break;
		case 'checkbox':
			$sanitize = 'hayabella_sanitize_bool';
			break;
		case 'select':
			$sanitize = hayabella_sanitize_choice( $extra['choices'] );
			break;
		case 'number':
			$sanitize = function ( $v ) {
				return is_numeric( $v ) ? $v + 0 : 0;
			};
			break;
		case 'color':
			$sanitize = 'sanitize_hex_color';
			break;
		case 'page':
			$sanitize = 'absint';
			break;
	}

	$wp->add_setting(
		$key,
		array(
			'default'           => $default,
			'sanitize_callback' => $sanitize,
			'transport'         => 'refresh',
		)
	);

	$args = array_merge(
		array(
			'label'   => $label,
			'section' => $section,
		),
		$extra
	);

	if ( 'image' === $type ) {
		$wp->add_control( new WP_Customize_Image_Control( $wp, $key, $args ) );
	} elseif ( 'video' === $type ) {
		$args['mime_type'] = 'video';
		$wp->add_control( new WP_Customize_Upload_Control( $wp, $key, $args ) );
	} elseif ( 'color' === $type ) {
		$wp->add_control( new WP_Customize_Color_Control( $wp, $key, $args ) );
	} else {
		$map          = array(
			'link'   => 'text',
			'page'   => 'dropdown-pages',
		);
		$args['type'] = isset( $map[ $type ] ) ? $map[ $type ] : $type;
		$wp->add_control( $key, $args );
	}
}

add_action( 'customize_register', 'hayabella_customize_register' );
/**
 * Register panels, sections and settings.
 *
 * @param WP_Customize_Manager $wp Manager.
 */
function hayabella_customize_register( $wp ) {
	$link_help = __( 'A full URL, or {shop} for the shop page (e.g. {shop}?orderby=date for newest first).', 'hayabella' );
	$icons     = hayabella_icon_choices();

	/* ---------- Brand colours ---------- */
	$wp->add_section( 'hb_colors', array( 'title' => __( 'Hayabella: Brand colours', 'hayabella' ), 'priority' => 25 ) );
	hayabella_control( $wp, 'hb_colors', 'hb_color_olive', __( 'Olive (logo, buttons, marquee text)', 'hayabella' ), 'color' );
	hayabella_control( $wp, 'hb_colors', 'hb_color_cream', __( 'Blush cream (bands and backgrounds)', 'hayabella' ), 'color' );
	hayabella_control( $wp, 'hb_colors', 'hb_color_gold', __( 'Gold accent', 'hayabella' ), 'color' );
	hayabella_control( $wp, 'hb_colors', 'hb_color_ink', __( 'Text', 'hayabella' ), 'color' );

	/* ---------- Header ---------- */
	$wp->add_section( 'hb_header', array( 'title' => __( 'Hayabella: Header & announcement bar', 'hayabella' ), 'priority' => 26, 'description' => __( 'Upload your logo in Site Identity. Menus are edited in Menus.', 'hayabella' ) ) );
	for ( $i = 1; $i <= 3; $i++ ) {
		/* translators: %d: message number */
		hayabella_control( $wp, 'hb_header', "hb_announce_{$i}", sprintf( __( 'Announcement %d (leave empty to hide)', 'hayabella' ), $i ) );
		/* translators: %d: message number */
		hayabella_control( $wp, 'hb_header', "hb_announce_link_{$i}", sprintf( __( 'Announcement %d link (optional)', 'hayabella' ), $i ), 'link' );
	}
	hayabella_control( $wp, 'hb_header', 'hb_logo_sub', __( 'Line under the logo', 'hayabella' ) );
	hayabella_control( $wp, 'hb_header', 'hb_search_popular', __( 'Popular searches (comma separated)', 'hayabella' ) );
	hayabella_control( $wp, 'hb_header', 'hb_region', __( 'Region label (menu and footer)', 'hayabella' ) );
	hayabella_control( $wp, 'hb_header', 'hb_wishlist_page', __( 'Wishlist page', 'hayabella' ), 'page', array( 'description' => __( 'A page using the "Wishlist" template. One is created when the theme is activated.', 'hayabella' ) ) );

	/* ---------- Homepage panel ---------- */
	$wp->add_panel( 'hb_home', array( 'title' => __( 'Hayabella: Homepage', 'hayabella' ), 'priority' => 27, 'description' => __( 'Each homepage section in order, top to bottom. Every section can be switched off.', 'hayabella' ) ) );

	// Hero slides.
	$wp->add_section(
		'hb_hero',
		array(
			'title'       => __( 'Hero slider', 'hayabella' ),
			'panel'       => 'hb_home',
			'description' => __( 'Up to 10 slides. Type: Video (a background film), Photo (a full-bleed photo with a caption bottom-left) or Banner (a designed image with the text already in it). Desktop images: 1920×800 or larger. Mobile images: 4:5 portrait, e.g. 1080×1350. Videos: MP4, muted, under ~8 MB.', 'hayabella' ),
		)
	);
	hayabella_control( $wp, 'hb_hero', 'hb_hero_duration', __( 'Seconds per photo/banner slide (video slides play their full length)', 'hayabella' ), 'number', array( 'input_attrs' => array( 'min' => 2, 'max' => 20, 'step' => 0.25 ) ) );
	$types = array(
		'video'  => __( 'Video', 'hayabella' ),
		'photo'  => __( 'Photo with caption', 'hayabella' ),
		'banner' => __( 'Banner (text in the image)', 'hayabella' ),
	);
	for ( $i = 1; $i <= HAYABELLA_SLIDES; $i++ ) {
		/* translators: %d: slide number */
		$n = sprintf( __( 'Slide %d', 'hayabella' ), $i );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_on", $n . ': ' . __( 'show this slide', 'hayabella' ), 'checkbox' );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_type", $n . ': ' . __( 'type', 'hayabella' ), 'select', array( 'choices' => $types ) );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_desk", $n . ': ' . __( 'desktop image (poster for videos)', 'hayabella' ), 'image' );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_mob", $n . ': ' . __( 'mobile image (optional)', 'hayabella' ), 'image' );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_vdesk", $n . ': ' . __( 'desktop video (Video type)', 'hayabella' ), 'video' );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_vmob", $n . ': ' . __( 'mobile video (optional)', 'hayabella' ), 'video' );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_title", $n . ': ' . __( 'caption title', 'hayabella' ) );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_cta", $n . ': ' . __( 'link text', 'hayabella' ) );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_link", $n . ': ' . __( 'link', 'hayabella' ), 'link', array( 'description' => $link_help ) );
		hayabella_control( $wp, 'hb_hero', "hb_slide_{$i}_alt", $n . ': ' . __( 'image description (for screen readers)', 'hayabella' ) );
	}

	// Feature strip.
	$wp->add_section( 'hb_usp', array( 'title' => __( 'Feature strip (under the slider)', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_usp', 'hb_show_usp', __( 'Show this section', 'hayabella' ), 'checkbox' );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: item number */
		$n = sprintf( __( 'Item %d', 'hayabella' ), $i );
		hayabella_control( $wp, 'hb_usp', "hb_usp_{$i}_icon", $n . ': ' . __( 'icon', 'hayabella' ), 'select', array( 'choices' => $icons ) );
		hayabella_control( $wp, 'hb_usp', "hb_usp_{$i}_title", $n . ': ' . __( 'title', 'hayabella' ) );
		hayabella_control( $wp, 'hb_usp', "hb_usp_{$i}_text", $n . ': ' . __( 'text', 'hayabella' ) );
	}

	// Product sliders.
	$sources = array(
		'new'      => __( 'Newest products', 'hayabella' ),
		'best'     => __( 'Best sellers', 'hayabella' ),
		'sale'     => __( 'On sale', 'hayabella' ),
		'featured' => __( 'Featured (starred) products', 'hayabella' ),
		'category' => __( 'A product category', 'hayabella' ),
	);
	$labels  = array( 1 => __( 'Product slider 1 (after the feature strip)', 'hayabella' ), 2 => __( 'Product slider 2 (after the marquee)', 'hayabella' ), 3 => __( 'Product slider 3 (after the occasion tiles)', 'hayabella' ) );
	foreach ( $labels as $i => $title ) {
		$sec = "hb_rail_{$i}";
		$wp->add_section( $sec, array( 'title' => $title, 'panel' => 'hb_home', 'description' => __( 'Products come from WooCommerce. A slider with no matching products is hidden.', 'hayabella' ) ) );
		hayabella_control( $wp, $sec, "hb_show_rail_{$i}", __( 'Show this section', 'hayabella' ), 'checkbox' );
		hayabella_control( $wp, $sec, "hb_rail_{$i}_title", __( 'Title', 'hayabella' ) );
		hayabella_control( $wp, $sec, "hb_rail_{$i}_text", __( 'Text', 'hayabella' ), 'textarea' );
		hayabella_control( $wp, $sec, "hb_rail_{$i}_source", __( 'Products to show', 'hayabella' ), 'select', array( 'choices' => $sources ) );
		hayabella_control( $wp, $sec, "hb_rail_{$i}_cat", __( 'Category slug (when "A product category" is chosen)', 'hayabella' ) );
		hayabella_control( $wp, $sec, "hb_rail_{$i}_count", __( 'How many products', 'hayabella' ), 'number', array( 'input_attrs' => array( 'min' => 1, 'max' => 24 ) ) );
		hayabella_control( $wp, $sec, "hb_rail_{$i}_link", __( '"View All" link (leave empty to hide)', 'hayabella' ), 'link', array( 'description' => $link_help ) );
	}

	// Split banners.
	$wp->add_section( 'hb_split', array( 'title' => __( 'Split banners (two images side by side)', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_split', 'hb_show_split', __( 'Show this section', 'hayabella' ), 'checkbox' );
	for ( $i = 1; $i <= 2; $i++ ) {
		/* translators: %d: banner number */
		$n = sprintf( __( 'Banner %d', 'hayabella' ), $i );
		hayabella_control( $wp, 'hb_split', "hb_split_{$i}_image", $n . ': ' . __( 'image (portrait 4:5)', 'hayabella' ), 'image' );
		hayabella_control( $wp, 'hb_split', "hb_split_{$i}_eyebrow", $n . ': ' . __( 'small line above the title', 'hayabella' ) );
		hayabella_control( $wp, 'hb_split', "hb_split_{$i}_title", $n . ': ' . __( 'title', 'hayabella' ) );
		hayabella_control( $wp, 'hb_split', "hb_split_{$i}_cta", $n . ': ' . __( 'link text', 'hayabella' ) );
		hayabella_control( $wp, 'hb_split', "hb_split_{$i}_link", $n . ': ' . __( 'link', 'hayabella' ), 'link', array( 'description' => $link_help ) );
	}

	// Marquee.
	$wp->add_section( 'hb_marquee', array( 'title' => __( 'Scrolling marquee', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_marquee', 'hb_show_marquee', __( 'Show this section', 'hayabella' ), 'checkbox' );
	hayabella_control( $wp, 'hb_marquee', 'hb_marquee_text', __( 'Scrolling phrases (one per line; they repeat with a | between them)', 'hayabella' ), 'textarea' );
	hayabella_control( $wp, 'hb_marquee', 'hb_marquee_eyebrow', __( 'Small line under the marquee', 'hayabella' ) );
	hayabella_control( $wp, 'hb_marquee', 'hb_marquee_sub', __( 'Second line under the marquee', 'hayabella' ) );
	hayabella_control( $wp, 'hb_marquee', 'hb_marquee_speed', __( 'Seconds per loop (higher is slower)', 'hayabella' ), 'number', array( 'input_attrs' => array( 'min' => 10, 'max' => 200 ) ) );

	// Campaign.
	$wp->add_section( 'hb_campaign', array( 'title' => __( 'Campaign banner (full width)', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_campaign', 'hb_show_campaign', __( 'Show this section', 'hayabella' ), 'checkbox' );
	hayabella_control( $wp, 'hb_campaign', 'hb_campaign_image', __( 'Background image (wide)', 'hayabella' ), 'image' );
	hayabella_control( $wp, 'hb_campaign', 'hb_campaign_eyebrow', __( 'Small line above the title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_campaign', 'hb_campaign_title', __( 'Title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_campaign', 'hb_campaign_text', __( 'Text', 'hayabella' ), 'textarea' );
	hayabella_control( $wp, 'hb_campaign', 'hb_campaign_cta', __( 'Button text', 'hayabella' ) );
	hayabella_control( $wp, 'hb_campaign', 'hb_campaign_link', __( 'Button link', 'hayabella' ), 'link', array( 'description' => $link_help ) );

	// Occasion tiles.
	$wp->add_section( 'hb_occasions', array( 'title' => __( 'Occasion tiles', 'hayabella' ), 'panel' => 'hb_home', 'description' => __( 'Three tiles. Each shows its video on a loop while on screen, or its image if no video is set. Portrait 7:9 works best.', 'hayabella' ) ) );
	hayabella_control( $wp, 'hb_occasions', 'hb_show_occasions', __( 'Show this section', 'hayabella' ), 'checkbox' );
	hayabella_control( $wp, 'hb_occasions', 'hb_occasions_eyebrow', __( 'Small line above the title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_occasions', 'hb_occasions_title', __( 'Title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_occasions', 'hb_occ_cta', __( 'Link text on each tile', 'hayabella' ) );
	for ( $i = 1; $i <= 3; $i++ ) {
		/* translators: %d: tile number */
		$n = sprintf( __( 'Tile %d', 'hayabella' ), $i );
		hayabella_control( $wp, 'hb_occasions', "hb_occ_{$i}_title", $n . ': ' . __( 'title', 'hayabella' ) );
		hayabella_control( $wp, 'hb_occasions', "hb_occ_{$i}_image", $n . ': ' . __( 'image', 'hayabella' ), 'image' );
		hayabella_control( $wp, 'hb_occasions', "hb_occ_{$i}_video", $n . ': ' . __( 'video loop (optional)', 'hayabella' ), 'video' );
		hayabella_control( $wp, 'hb_occasions', "hb_occ_{$i}_link", $n . ': ' . __( 'link', 'hayabella' ), 'link', array( 'description' => $link_help ) );
	}

	// Story.
	$wp->add_section( 'hb_story', array( 'title' => __( 'Brand story', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_story', 'hb_show_story', __( 'Show this section', 'hayabella' ), 'checkbox' );
	hayabella_control( $wp, 'hb_story', 'hb_story_eyebrow', __( 'Small line above the title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_story', 'hb_story_title', __( 'Title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_story', 'hb_story_text', __( 'Text', 'hayabella' ), 'textarea' );
	hayabella_control( $wp, 'hb_story', 'hb_story_cta', __( 'Link text', 'hayabella' ) );
	hayabella_control( $wp, 'hb_story', 'hb_story_link', __( 'Link (leave empty to hide)', 'hayabella' ), 'link' );

	// Lookbook.
	$wp->add_section( 'hb_looks', array( 'title' => __( 'Lookbook / Instagram strip', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_looks', 'hb_show_looks', __( 'Show this section', 'hayabella' ), 'checkbox' );
	hayabella_control( $wp, 'hb_looks', 'hb_looks_eyebrow', __( 'Small line above the title (e.g. your @handle)', 'hayabella' ) );
	hayabella_control( $wp, 'hb_looks', 'hb_looks_title', __( 'Title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_looks', 'hb_looks_link', __( 'Where the photos link to (e.g. your Instagram)', 'hayabella' ), 'link' );
	for ( $i = 1; $i <= 6; $i++ ) {
		/* translators: %d: photo number */
		hayabella_control( $wp, 'hb_looks', "hb_look_{$i}", sprintf( __( 'Photo %d (square works best)', 'hayabella' ), $i ), 'image' );
	}

	// Newsletter.
	$wp->add_section( 'hb_news', array( 'title' => __( 'Newsletter', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_news', 'hb_show_newsletter', __( 'Show this section', 'hayabella' ), 'checkbox' );
	hayabella_control( $wp, 'hb_news', 'hb_news_title', __( 'Title', 'hayabella' ) );
	hayabella_control( $wp, 'hb_news', 'hb_news_text', __( 'Text', 'hayabella' ), 'textarea' );
	hayabella_control( $wp, 'hb_news', 'hb_news_shortcode', __( 'Sign-up form shortcode', 'hayabella' ), 'text', array( 'description' => __( 'Paste the shortcode from your email plugin (e.g. Mailchimp for WordPress, MailPoet, Klaviyo). Until you do, the built-in form only shows the thank-you message and does not save emails.', 'hayabella' ) ) );
	hayabella_control( $wp, 'hb_news', 'hb_news_thanks', __( 'Thank-you message (built-in form)', 'hayabella' ) );

	// Perks.
	$wp->add_section( 'hb_perks', array( 'title' => __( 'Perks bar (above the footer)', 'hayabella' ), 'panel' => 'hb_home' ) );
	hayabella_control( $wp, 'hb_perks', 'hb_show_perks', __( 'Show this section', 'hayabella' ), 'checkbox' );
	for ( $i = 1; $i <= 3; $i++ ) {
		/* translators: %d: item number */
		$n = sprintf( __( 'Item %d', 'hayabella' ), $i );
		hayabella_control( $wp, 'hb_perks', "hb_perk_{$i}_icon", $n . ': ' . __( 'icon', 'hayabella' ), 'select', array( 'choices' => $icons ) );
		hayabella_control( $wp, 'hb_perks', "hb_perk_{$i}_title", $n . ': ' . __( 'title', 'hayabella' ) );
		hayabella_control( $wp, 'hb_perks', "hb_perk_{$i}_text", $n . ': ' . __( 'text', 'hayabella' ) );
	}

	/* ---------- Footer ---------- */
	$wp->add_section( 'hb_footer', array( 'title' => __( 'Hayabella: Footer', 'hayabella' ), 'priority' => 28, 'description' => __( 'The three link columns are menus: set them in Menus (Footer column 1–3). The menu name is used as the column title.', 'hayabella' ) ) );
	hayabella_control( $wp, 'hb_footer', 'hb_footer_logo', __( 'Footer logo (light, for the dark background)', 'hayabella' ), 'image' );
	hayabella_control( $wp, 'hb_footer', 'hb_footer_about', __( 'About text', 'hayabella' ), 'textarea' );
	foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'pinterest' => 'Pinterest' ) as $k => $name ) {
		/* translators: %s: network name */
		hayabella_control( $wp, 'hb_footer', "hb_social_{$k}", sprintf( __( '%s link (leave empty to hide)', 'hayabella' ), $name ), 'link' );
	}
	hayabella_control( $wp, 'hb_footer', 'hb_payments', __( 'Payment methods (comma separated)', 'hayabella' ) );
	hayabella_control( $wp, 'hb_footer', 'hb_copyright', __( 'Copyright line ({year} becomes the current year)', 'hayabella' ) );
}
