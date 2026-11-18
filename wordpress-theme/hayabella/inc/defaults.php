<?php
/**
 * Default content for every Customizer setting.
 *
 * Paths starting with {theme} point into this theme's folder; links starting with {shop}
 * point to the WooCommerce shop page. Both are resolved by hayabella_url().
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

/** Number of hero slide slots in the Customizer. */
define( 'HAYABELLA_SLIDES', 10 );

/**
 * All defaults, keyed by theme_mod name.
 *
 * @return array
 */
function hayabella_defaults() {
	static $d = null;
	if ( null !== $d ) {
		return $d;
	}
	$img = '{theme}/assets/images/';
	$vid = '{theme}/assets/videos/';

	$d = array(
		// Brand colours.
		'hb_color_olive'        => '#45451b',
		'hb_color_cream'        => '#f4e9e2',
		'hb_color_gold'         => '#a8844f',
		'hb_color_ink'          => '#1e1b1a',

		// Announcement bar.
		'hb_announce_1'         => 'Free US shipping on orders over $150',
		'hb_announce_2'         => 'New: The Autumn Edit — shop now',
		'hb_announce_3'         => 'Easy 30-day returns & exchanges nationwide',
		'hb_announce_link_1'    => '',
		'hb_announce_link_2'    => '{shop}',
		'hb_announce_link_3'    => '',

		// Header.
		'hb_logo_sub'           => 'Modesty – The New Bold',
		'hb_search_popular'     => 'Black abaya, Open front, Eid collection, Linen, Jersey hijab',
		'hb_region'             => 'United States (USD $)',

		// Hero.
		'hb_hero_duration'      => 5.25,

		// Feature strip (USP).
		'hb_show_usp'           => true,
		'hb_usp_1_icon'         => 'truck',
		'hb_usp_1_title'        => 'Free US Shipping',
		'hb_usp_1_text'         => 'On orders over $150',
		'hb_usp_2_icon'         => 'return',
		'hb_usp_2_title'        => '30-Day Returns',
		'hb_usp_2_text'         => 'Easy & hassle-free',
		'hb_usp_3_icon'         => 'flag',
		'hb_usp_3_title'        => 'Ships from the USA',
		'hb_usp_3_text'         => 'Delivered in 2–5 days',
		'hb_usp_4_icon'         => 'lock',
		'hb_usp_4_title'        => 'Secure Checkout',
		'hb_usp_4_text'         => 'Pay in 4 with Klarna',

		// Product sliders.
		'hb_show_rail_1'        => true,
		'hb_rail_1_title'       => 'New Arrivals',
		'hb_rail_1_text'        => 'Meet the latest pieces, from everyday essentials to standout styles worth dressing up for.',
		'hb_rail_1_source'      => 'new',
		'hb_rail_1_cat'         => '',
		'hb_rail_1_count'       => 8,
		'hb_rail_1_link'        => '{shop}?orderby=date',
		'hb_show_rail_2'        => true,
		'hb_rail_2_title'       => 'Best Sellers',
		'hb_rail_2_text'        => 'The styles our customers keep coming back for, loved for their fit, fabric and finish.',
		'hb_rail_2_source'      => 'best',
		'hb_rail_2_cat'         => '',
		'hb_rail_2_count'       => 8,
		'hb_rail_2_link'        => '{shop}?orderby=popularity',
		'hb_show_rail_3'        => true,
		'hb_rail_3_title'       => 'The Occasion Edit',
		'hb_rail_3_text'        => 'Considered pieces for Eid, weddings and evenings, with embroidery, crystals and flowing layers.',
		'hb_rail_3_source'      => 'featured',
		'hb_rail_3_cat'         => '',
		'hb_rail_3_count'       => 8,
		'hb_rail_3_link'        => '{shop}',

		// Split banners.
		'hb_show_split'         => true,
		'hb_split_1_image'      => $img . 'photos/ivory-pearl-pose.jpg',
		'hb_split_1_eyebrow'    => 'Limited Edition',
		'hb_split_1_title'      => 'The Pearl Collection',
		'hb_split_1_cta'        => 'Discover',
		'hb_split_1_link'       => '{shop}',
		'hb_split_2_image'      => $img . 'photos/navy-open-hooded.jpg',
		'hb_split_2_eyebrow'    => 'New Arrival',
		'hb_split_2_title'      => 'Midnight Blues',
		'hb_split_2_cta'        => 'Shop Navy',
		'hb_split_2_link'       => '{shop}',

		// Marquee.
		'hb_show_marquee'       => true,
		'hb_marquee_text'       => "Modesty – The New Bold\nHayabella",
		'hb_marquee_eyebrow'    => 'Luxury abayas & modest wear',
		'hb_marquee_sub'        => 'Elegance, designed for every occasion',
		'hb_marquee_speed'      => 48,

		// Campaign banner.
		'hb_show_campaign'      => true,
		'hb_campaign_image'     => $img . 'photos/charcoal-cuff-wide.jpg',
		'hb_campaign_eyebrow'   => 'Autumn / Winter 2026',
		'hb_campaign_title'     => 'Heritage, Reimagined',
		'hb_campaign_text'      => 'Timeless craftsmanship meets modern tailoring. Designed with intention, made to be treasured.',
		'hb_campaign_cta'       => 'Shop the Collection',
		'hb_campaign_link'      => '{shop}',

		// Occasion tiles.
		'hb_show_occasions'     => true,
		'hb_occasions_eyebrow'  => 'Curated',
		'hb_occasions_title'    => 'Dressed for Every Occasion',
		'hb_occ_1_image'        => $vid . 'cards/occasion-festive-poster.jpg',
		'hb_occ_1_video'        => $vid . 'cards/occasion-festive.mp4',
		'hb_occ_1_title'        => 'Eid & Festive',
		'hb_occ_1_link'         => '{shop}',
		'hb_occ_2_image'        => $vid . 'cards/occasion-wedding-poster.jpg',
		'hb_occ_2_video'        => $vid . 'cards/occasion-wedding.mp4',
		'hb_occ_2_title'        => 'Wedding Guest',
		'hb_occ_2_link'         => '{shop}',
		'hb_occ_3_image'        => $vid . 'cards/occasion-everyday-poster.jpg',
		'hb_occ_3_video'        => $vid . 'cards/occasion-everyday.mp4',
		'hb_occ_3_title'        => 'Everyday Elegance',
		'hb_occ_3_link'         => '{shop}',
		'hb_occ_cta'            => 'Shop now',

		// Story.
		'hb_show_story'         => true,
		'hb_story_eyebrow'      => 'Our Story',
		'hb_story_title'        => 'Modesty, with a sense of occasion.',
		'hb_story_text'         => 'Every Hayabella piece begins with fabric chosen for how it moves and feels. Designed for the modern woman in America, our abayas balance heritage craftsmanship with the ease of everyday life.',
		'hb_story_cta'          => 'Read more about us',
		'hb_story_link'         => '',

		// Lookbook.
		'hb_show_looks'         => true,
		'hb_looks_eyebrow'      => '@hayabella',
		'hb_looks_title'        => 'Styled by You',
		'hb_looks_link'         => '',
		'hb_look_1'             => $img . 'photos/ivory-pearl-pose.jpg',
		'hb_look_2'             => $img . 'photos/burgundy-petal-close.jpg',
		'hb_look_3'             => $img . 'photos/navy-open-full.jpg',
		'hb_look_4'             => $img . 'photos/charcoal-cuff-wide.jpg',
		'hb_look_5'             => $img . 'photos/noir-crystal-front.jpg',
		'hb_look_6'             => $img . 'photos/mauve-ruffle.jpg',

		// Newsletter.
		'hb_show_newsletter'    => true,
		'hb_news_title'         => 'Join Hayabella',
		'hb_news_text'          => 'Sign up for 10% off your first order, early access to new collections and members-only offers.',
		'hb_news_shortcode'     => '',
		'hb_news_thanks'        => 'Welcome to Hayabella! Check your inbox for your 10% off code.',

		// Perks.
		'hb_show_perks'         => true,
		'hb_perk_1_icon'        => 'truck',
		'hb_perk_1_title'       => 'Shipping',
		'hb_perk_1_text'        => 'Free on US orders over $150',
		'hb_perk_2_icon'        => 'heart',
		'hb_perk_2_title'       => 'Crafted with Care',
		'hb_perk_2_text'        => 'Finished by hand, made to last',
		'hb_perk_3_icon'        => 'chat',
		'hb_perk_3_title'       => 'Need Help?',
		'hb_perk_3_text'        => 'Mon–Sat, 9am–6pm EST',

		// Footer.
		'hb_footer_about'       => 'Elegant abayas and modest wear, designed for the modern woman and shipped across the United States.',
		'hb_footer_logo'        => $img . 'brand/hb-logo-full-light.svg',
		'hb_social_instagram'   => '#',
		'hb_social_facebook'    => '#',
		'hb_social_tiktok'      => '#',
		'hb_social_pinterest'   => '#',
		'hb_payments'           => 'Visa, Mastercard, Amex, PayPal, Apple Pay, Klarna',
		'hb_copyright'          => '© {year} Hayabella. All rights reserved.',
	);

	// Hero slides: type is video, photo (caption over the image) or banner (text is in the image).
	$slides = array(
		array( 'video', $vid . 'hero-film-desktop-poster.jpg', $vid . 'hero-film-mobile-poster.jpg', $vid . 'hero-film-desktop.mp4', $vid . 'hero-film-mobile.mp4', 'Made to Move', 'Shop new in', '{shop}?orderby=date', 'The Hayabella campaign film' ),
		array( 'photo', $img . 'slides/greekkey-teal-desktop.jpg', $img . 'slides/greekkey-teal-mobile.jpg', '', '', 'Signature Greek Key', 'Shop now', '{shop}', 'Model in a sky-blue abaya with Greek-key trim and matching hijab' ),
		array( 'photo', $img . 'slides/satin-seated-desktop.jpg', $img . 'slides/satin-seated-mobile.jpg', '', '', 'The Satin Edit', 'Shop satin', '{shop}', 'Model seated in a dusty-blue satin abaya with embellished trim' ),
		array( 'photo', $img . 'slides/stripe-kimono-desktop.jpg', $img . 'slides/stripe-kimono-mobile.jpg', '', '', 'New In', 'Shop new in', '{shop}?orderby=date', 'Model in a blue and white striped kimono abaya with white hijab' ),
		array( 'photo', $img . 'slides/floral-noir-desktop.jpg', $img . 'slides/floral-noir-mobile.jpg', '', '', 'Occasion Wear', 'Shop occasion', '{shop}', 'Model in a black open abaya with embroidered roses' ),
		array( 'banner', $img . 'banners/noir-edit-desktop.jpg', $img . 'banners/noir-edit-mobile.jpg', '', '', '', '', '{shop}', 'The Noir Edit: fluid black chiffon, hand-set crystals. Shop now' ),
		array( 'banner', $img . 'banners/berry-luxe-desktop.jpg', $img . 'banners/berry-luxe-mobile.jpg', '', '', '', '', '{shop}', 'Berry Luxe: sculpted petal cuffs in deep burgundy. Shop Berry Luxe' ),
		array( 'banner', $img . 'banners/pearl-collection-desktop.jpg', $img . 'banners/pearl-collection-mobile.jpg', '', '', '', '', '{shop}', 'The Pearl Collection: ivory crepe with blush embroidery. Shop the collection' ),
	);
	for ( $i = 1; $i <= HAYABELLA_SLIDES; $i++ ) {
		$s = isset( $slides[ $i - 1 ] ) ? $slides[ $i - 1 ] : array( 'photo', '', '', '', '', '', 'Shop now', '{shop}', '' );
		$d[ "hb_slide_{$i}_on" ]      = isset( $slides[ $i - 1 ] );
		$d[ "hb_slide_{$i}_type" ]    = $s[0];
		$d[ "hb_slide_{$i}_desk" ]    = $s[1];
		$d[ "hb_slide_{$i}_mob" ]     = $s[2];
		$d[ "hb_slide_{$i}_vdesk" ]   = $s[3];
		$d[ "hb_slide_{$i}_vmob" ]    = $s[4];
		$d[ "hb_slide_{$i}_title" ]   = $s[5];
		$d[ "hb_slide_{$i}_cta" ]     = $s[6];
		$d[ "hb_slide_{$i}_link" ]    = $s[7];
		$d[ "hb_slide_{$i}_alt" ]     = $s[8];
	}
	return $d;
}
