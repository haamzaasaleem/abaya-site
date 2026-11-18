<?php
/**
 * Header: announcement bar, logo, icons, main menu, mobile drawer and search.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

$hb_wishlist = hayabella_wishlist_url();
$hb_account  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$hb_cart     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '';
$hb_count    = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$hb_logo     = HAYABELLA_URI . '/assets/images/brand/hayabella-wordmark.svg';
$hb_notes    = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$t = trim( (string) hayabella_mod( "hb_announce_{$i}" ) );
	if ( '' !== $t ) {
		$hb_notes[] = array( $t, hayabella_mod_url( "hb_announce_link_{$i}" ) );
	}
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/icons' ); ?>
<a class="skip-link sr-only" href="#main"><?php esc_html_e( 'Skip to content', 'hayabella' ); ?></a>

<?php if ( $hb_notes ) : ?>
<div class="announcement" role="region" aria-label="<?php esc_attr_e( 'Announcements', 'hayabella' ); ?>">
	<div class="announcement__track" data-announcement>
		<?php foreach ( $hb_notes as $n => $note ) : ?>
			<p<?php echo 0 === $n ? ' class="is-active"' : ''; ?>>
				<?php if ( $note[1] ) : ?>
					<a href="<?php echo esc_url( $note[1] ); ?>"><?php echo esc_html( $note[0] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $note[0] ); ?>
				<?php endif; ?>
			</p>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<header class="header" data-header>
	<div class="header__inner container">
		<div class="header__left">
			<button class="icon-btn header__burger" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'hayabella' ); ?>" data-open-drawer><?php hayabella_icon( 'menu' ); ?></button>
			<button class="icon-btn" type="button" aria-label="<?php esc_attr_e( 'Search', 'hayabella' ); ?>" data-open-search><?php hayabella_icon( 'search' ); ?></button>
		</div>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				$hb_logo_id = get_theme_mod( 'custom_logo' );
				echo wp_get_attachment_image( $hb_logo_id, 'full', false, array( 'class' => 'logo__img', 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				printf( '<img src="%s" alt="%s" class="logo__img" width="182" height="40">', esc_url( $hb_logo ), esc_attr( get_bloginfo( 'name' ) ) );
			}
			?>
			<?php if ( hayabella_mod( 'hb_logo_sub' ) ) : ?>
				<span class="logo__sub"><?php echo esc_html( hayabella_mod( 'hb_logo_sub' ) ); ?></span>
			<?php endif; ?>
		</a>

		<div class="header__right">
			<a href="<?php echo esc_url( $hb_account ); ?>" class="icon-btn hide-sm" aria-label="<?php esc_attr_e( 'Account', 'hayabella' ); ?>"><?php hayabella_icon( 'user' ); ?></a>
			<?php if ( $hb_wishlist ) : ?>
				<a href="<?php echo esc_url( $hb_wishlist ); ?>" class="icon-btn hide-sm" aria-label="<?php esc_attr_e( 'Wishlist', 'hayabella' ); ?>"><?php hayabella_icon( 'heart' ); ?><span class="cart-count wish-count" data-wish-count hidden>0</span></a>
			<?php endif; ?>
			<?php if ( $hb_cart ) : ?>
				<a href="<?php echo esc_url( $hb_cart ); ?>" class="icon-btn cart-btn" aria-label="<?php esc_attr_e( 'Bag', 'hayabella' ); ?>"><?php hayabella_icon( 'bag' ); ?><span class="cart-count" data-cart-count><?php echo (int) $hb_count; ?></span></a>
			<?php endif; ?>
		</div>
	</div>

	<nav class="nav" aria-label="<?php esc_attr_e( 'Primary', 'hayabella' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__list container',
				'depth'          => 3,
				'fallback_cb'    => 'hayabella_menu_fallback',
			)
		);
		?>
	</nav>
</header>

<div class="drawer" data-drawer aria-hidden="true">
	<div class="drawer__overlay" data-close-drawer></div>
	<aside class="drawer__panel" aria-label="<?php esc_attr_e( 'Menu', 'hayabella' ); ?>">
		<div class="drawer__head">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'logo__img logo__img--sm', 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				printf( '<img src="%s" alt="%s" class="logo__img logo__img--sm" width="137" height="30">', esc_url( $hb_logo ), esc_attr( get_bloginfo( 'name' ) ) );
			}
			?>
			<button class="icon-btn" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'hayabella' ); ?>" data-close-drawer><?php hayabella_icon( 'close' ); ?></button>
		</div>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => has_nav_menu( 'mobile' ) ? 'mobile' : 'primary',
				'container'      => false,
				'menu_class'     => 'drawer__list',
				'depth'          => 2,
				'fallback_cb'    => 'hayabella_menu_fallback',
			)
		);
		?>
		<div class="drawer__foot">
			<a href="<?php echo esc_url( $hb_account ); ?>"><?php hayabella_icon( 'user' ); ?> <?php echo is_user_logged_in() ? esc_html__( 'My account', 'hayabella' ) : esc_html__( 'Sign in / Register', 'hayabella' ); ?></a>
			<?php if ( $hb_wishlist ) : ?>
				<a href="<?php echo esc_url( $hb_wishlist ); ?>"><?php hayabella_icon( 'heart' ); ?> <?php esc_html_e( 'Wishlist', 'hayabella' ); ?></a>
			<?php endif; ?>
			<?php if ( hayabella_mod( 'hb_region' ) ) : ?>
				<span class="drawer__region"><?php echo esc_html( hayabella_mod( 'hb_region' ) ); ?></span>
			<?php endif; ?>
		</div>
	</aside>
</div>

<div class="search" data-search aria-hidden="true">
	<div class="search__inner container">
		<form class="search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php hayabella_icon( 'search' ); ?>
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search abayas, kaftans, hijabs…', 'hayabella' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'hayabella' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" data-search-input>
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
			<button type="button" class="icon-btn" aria-label="<?php esc_attr_e( 'Close search', 'hayabella' ); ?>" data-close-search><?php hayabella_icon( 'close' ); ?></button>
		</form>
		<?php
		$hb_popular = array_filter( array_map( 'trim', explode( ',', (string) hayabella_mod( 'hb_search_popular' ) ) ) );
		if ( $hb_popular ) :
			?>
			<div class="search__popular">
				<span><?php esc_html_e( 'Popular:', 'hayabella' ); ?></span>
				<?php
				foreach ( $hb_popular as $term ) {
					$args = array( 's' => $term );
					if ( class_exists( 'WooCommerce' ) ) {
						$args['post_type'] = 'product';
					}
					printf( '<a href="%s">%s</a>', esc_url( add_query_arg( $args, home_url( '/' ) ) ), esc_html( $term ) );
				}
				?>
			</div>
		<?php endif; ?>
	</div>
</div>

<main id="main">
