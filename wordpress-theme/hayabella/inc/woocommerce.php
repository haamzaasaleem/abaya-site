<?php
/**
 * WooCommerce integration: product cards, sliders, wrappers, bag count.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

/**
 * Products for a homepage slider.
 *
 * @param string $source new | best | sale | featured | category.
 * @param string $cat    Category slug (for "category").
 * @param int    $count  How many.
 * @return WC_Product[]
 */
function hayabella_rail_products( $source, $cat = '', $count = 8 ) {
	$count = max( 1, min( 24, (int) $count ) );
	$args  = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => $count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
		'tax_query'           => WC()->query->get_tax_query(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- hides hidden / out-of-stock products per store settings.
		'meta_query'          => WC()->query->get_meta_query(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'orderby'             => 'date',
		'order'               => 'DESC',
	);
	switch ( $source ) {
		case 'best':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = array(
				'meta_value_num' => 'DESC',
				'date'           => 'DESC',
			);
			break;
		case 'sale':
			$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			break;
		case 'featured':
			$args['post__in'] = array_merge( array( 0 ), wc_get_featured_product_ids() );
			break;
		case 'category':
			if ( $cat ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => array_map( 'trim', explode( ',', $cat ) ),
				);
			}
			break;
	}
	$q        = new WP_Query( $args );
	$products = array();
	foreach ( $q->posts as $id ) {
		$p = wc_get_product( $id );
		if ( $p && $p->is_visible() ) {
			$products[] = $p;
		}
	}
	// "Featured" with nothing starred yet: fall back to the newest products so the slider is not empty.
	if ( ! $products && 'featured' === $source ) {
		return hayabella_rail_products( 'new', '', $count );
	}
	return $products;
}

/**
 * Sizes for quick add: [ label => variation id ] for a variable product whose only
 * variation attribute is a size (pa_size, size, ...). Simple products return [].
 *
 * @param WC_Product $product Product.
 * @return array|null Null when quick add is not possible (needs the product page).
 */
function hayabella_quick_sizes( $product ) {
	if ( ! $product->is_type( 'variable' ) ) {
		return array();
	}
	$attrs = $product->get_variation_attributes();
	if ( 1 !== count( $attrs ) ) {
		return null;
	}
	$name = key( $attrs );
	if ( false === stripos( $name, 'size' ) ) {
		return null;
	}
	$out      = array();
	$taxonomy = taxonomy_exists( $name ) ? $name : '';
	// Keep the order the sizes are set up in (S, M, L, XL...).
	$order = array_values( (array) current( $attrs ) );
	foreach ( $product->get_children() as $vid ) {
		$v = wc_get_product( $vid );
		if ( ! $v || ! $v->is_purchasable() || ! $v->is_in_stock() || 'publish' !== $v->get_status() ) {
			continue;
		}
		$va    = $v->get_variation_attributes();
		$value = reset( $va );
		if ( '' === $value ) {
			return null; // "Any size" variation: let the product page handle it.
		}
		$label = $value;
		if ( $taxonomy ) {
			$term  = get_term_by( 'slug', $value, $taxonomy );
			$label = $term ? $term->name : $value;
		}
		$out[ $label ] = array( $vid, array_search( $value, $order, true ) );
	}
	uasort(
		$out,
		function ( $a, $b ) {
			return (int) $a[1] - (int) $b[1];
		}
	);
	return array_map(
		function ( $x ) {
			return $x[0];
		},
		$out
	);
}

/**
 * Percentage off for the sale pill.
 *
 * @param WC_Product $product Product.
 * @return int
 */
function hayabella_sale_percent( $product ) {
	$best = 0;
	$list = $product->is_type( 'variable' ) ? array_map( 'wc_get_product', $product->get_visible_children() ) : array( $product );
	foreach ( array_filter( $list ) as $p ) {
		$reg  = (float) $p->get_regular_price();
		$sale = (float) $p->get_sale_price();
		if ( $reg > 0 && $sale > 0 && $sale < $reg ) {
			$best = max( $best, (int) round( ( 1 - $sale / $reg ) * 100 ) );
		}
	}
	return $best;
}

/**
 * Output a product card (homepage sliders, shop grid, related products).
 *
 * @param WC_Product|int $product Product or ID.
 */
function hayabella_product_card( $product ) {
	$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
	if ( ! $product ) {
		return;
	}
	$id      = $product->get_id();
	$name    = $product->get_name();
	$link    = $product->get_permalink();
	$clip    = get_post_meta( $id, '_hb_card_video', true );
	$gallery = $product->get_gallery_image_ids();
	$alt_id  = $gallery ? (int) $gallery[0] : 0;
	$tag     = get_post_meta( $id, '_hb_card_tag', true );
	if ( ! $tag ) {
		$terms = get_the_terms( $id, 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$terms = array_values(
				array_filter(
					$terms,
					function ( $t ) {
						return (int) get_option( 'default_product_cat' ) !== (int) $t->term_id;
					}
				)
			);
			$tag   = $terms ? $terms[0]->name : '';
		}
	}
	$sizes   = $product->is_in_stock() ? hayabella_quick_sizes( $product ) : null;
	$badge   = get_post_meta( $id, '_hb_badge', true );
	$percent = $product->is_on_sale() ? hayabella_sale_percent( $product ) : 0;
	$sizes_attr = '(max-width: 767px) 50vw, (max-width: 1199px) 33vw, 25vw';

	$classes = 'product' . ( $clip ? ' product--clip' : '' ) . ( $product->is_in_stock() ? '' : ' product--soldout' );
	?>
	<article class="<?php echo esc_attr( $classes ); ?>">
		<div class="product__media">
			<a href="<?php echo esc_url( $link ); ?>" class="product__link" aria-label="<?php echo esc_attr( $name ); ?>">
				<?php
				if ( $product->get_image_id() ) {
					echo wp_get_attachment_image( $product->get_image_id(), 'woocommerce_single', false, array( 'alt' => $name, 'loading' => 'lazy', 'sizes' => $sizes_attr ) );
				} else {
					echo wc_placeholder_img( 'woocommerce_single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				if ( $clip ) {
					$poster = $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_single' ) : '';
					hayabella_live_video( $clip, $poster );
				} elseif ( $alt_id ) {
					echo wp_get_attachment_image( $alt_id, 'woocommerce_single', false, array( 'alt' => '', 'class' => 'img-alt', 'loading' => 'lazy', 'sizes' => $sizes_attr ) );
				}
				?>
			</a>
			<img src="<?php echo esc_url( HAYABELLA_URI . '/assets/images/brand/hb-logo-full.svg' ); ?>" alt="" class="product__mark" aria-hidden="true">
			<?php /* translators: %s: product name */ ?>
			<button type="button" class="wish" data-wish="<?php echo esc_attr( $id ); ?>" data-name="<?php echo esc_attr( $name ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Save %s to wishlist', 'hayabella' ), $name ) ); ?>" aria-pressed="false"><?php hayabella_icon( 'heart' ); ?></button>
			<?php if ( is_array( $sizes ) && ( $sizes || ( $product->is_type( 'simple' ) && $product->is_purchasable() ) ) ) : ?>
				<div class="quick-add">
					<span class="quick-add__label"><?php esc_html_e( 'Quick Add', 'hayabella' ); ?></span>
					<div class="sizes">
						<?php if ( $sizes ) : ?>
							<?php foreach ( $sizes as $label => $vid ) : ?>
								<button type="button" class="size" data-add="<?php echo esc_attr( $vid ); ?>" data-name="<?php echo esc_attr( $name . ' (' . $label . ')' ); ?>"><?php echo esc_html( $label ); ?></button>
							<?php endforeach; ?>
						<?php else : ?>
							<button type="button" class="size size--wide" data-add="<?php echo esc_attr( $id ); ?>" data-name="<?php echo esc_attr( $name ); ?>"><?php esc_html_e( 'Add to Bag', 'hayabella' ); ?></button>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $tag ) : ?>
			<p class="product__tag"><?php echo esc_html( $tag ); ?></p>
		<?php endif; ?>
		<h3 class="product__name"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a></h3>
		<div class="product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
		<?php
		if ( ! $product->is_in_stock() ) {
			echo '<span class="pill pill--soldout">' . esc_html__( 'Sold out', 'hayabella' ) . '</span>';
		} elseif ( $percent ) {
			/* translators: %d: percent */
			echo '<span class="pill pill--sale">' . esc_html( sprintf( __( '%d%% Off', 'hayabella' ), $percent ) ) . '</span>';
		} elseif ( $product->is_on_sale() ) {
			echo '<span class="pill pill--sale">' . esc_html__( 'Sale', 'hayabella' ) . '</span>';
		} elseif ( $badge ) {
			echo '<span class="pill">' . esc_html( $badge ) . '</span>';
		}
		?>
	</article>
	<?php
}

/* ---------- Page wrappers ---------- */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action( 'woocommerce_before_main_content', 'hayabella_wc_wrapper_start', 10 );
/** Open the content wrapper. */
function hayabella_wc_wrapper_start() {
	echo '<div class="container wc-page">';
}

add_action( 'woocommerce_after_main_content', 'hayabella_wc_wrapper_end', 10 );
/** Close the content wrapper. */
function hayabella_wc_wrapper_end() {
	echo '</div>';
}

add_filter( 'woocommerce_breadcrumb_defaults', 'hayabella_breadcrumb_args' );
/**
 * Breadcrumb markup.
 *
 * @param array $args Args.
 * @return array
 */
function hayabella_breadcrumb_args( $args ) {
	$args['delimiter']   = '<span class="crumbs__sep" aria-hidden="true">/</span>';
	$args['wrap_before'] = '<nav class="crumbs woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'hayabella' ) . '">';
	$args['wrap_after']  = '</nav>';
	return $args;
}

add_filter( 'loop_shop_per_page', 'hayabella_per_page', 20 );
/** Products per shop page. */
function hayabella_per_page() {
	return 16;
}

add_filter( 'loop_shop_columns', 'hayabella_loop_columns' );
/** Shop grid columns. */
function hayabella_loop_columns() {
	return 4;
}

add_filter( 'woocommerce_output_related_products_args', 'hayabella_related_args' );
/**
 * Four related products.
 *
 * @param array $args Args.
 * @return array
 */
function hayabella_related_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}

/* ---------- Bag count (updated after any add to cart) ---------- */

add_filter( 'woocommerce_add_to_cart_fragments', 'hayabella_cart_fragment' );
/**
 * Refresh the header bag count with WooCommerce's cart fragments.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function hayabella_cart_fragment( $fragments ) {
	$fragments['span.cart-count[data-cart-count]'] = '<span class="cart-count" data-cart-count>' . (int) WC()->cart->get_cart_contents_count() . '</span>';
	return $fragments;
}

/* ---------- Product page extras ---------- */

add_action( 'woocommerce_after_add_to_cart_button', 'hayabella_single_wish_button' );
/** Wishlist button next to Add to cart. */
function hayabella_single_wish_button() {
	global $product;
	if ( ! $product || ! hayabella_wishlist_url() ) {
		return;
	}
	printf(
		'<button type="button" class="btn btn--outline pdp-wish" data-wish="%1$d" data-name="%2$s" aria-pressed="false">%3$s<span>%4$s</span></button>',
		(int) $product->get_id(),
		esc_attr( $product->get_name() ),
		'<svg aria-hidden="true"><use href="#i-heart"/></svg>',
		esc_html__( 'Wishlist', 'hayabella' )
	);
}

add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );

add_action( 'woocommerce_single_product_summary', 'hayabella_single_perks', 45 );
/** Delivery / returns notes under the buy button (from the feature strip settings). */
function hayabella_single_perks() {
	echo '<ul class="pdp-perks">';
	for ( $i = 1; $i <= 3; $i++ ) {
		$t = hayabella_mod( "hb_usp_{$i}_title" );
		if ( ! $t ) {
			continue;
		}
		echo '<li>';
		hayabella_icon( hayabella_mod( "hb_usp_{$i}_icon" ) );
		echo '<span><strong>' . esc_html( $t ) . '</strong> ' . esc_html( hayabella_mod( "hb_usp_{$i}_text" ) ) . '</span></li>';
	}
	echo '</ul>';
}

add_filter( 'woocommerce_sale_flash', 'hayabella_sale_flash', 10, 3 );
/**
 * Sale badge on the product page in the theme's pill style.
 *
 * @param string     $html    Markup.
 * @param WP_Post    $post    Post.
 * @param WC_Product $product Product.
 * @return string
 */
function hayabella_sale_flash( $html, $post, $product ) {
	$p = hayabella_sale_percent( $product );
	/* translators: %d: percent */
	return '<span class="onsale pill pill--sale">' . esc_html( $p ? sprintf( __( '%d%% Off', 'hayabella' ), $p ) : __( 'Sale', 'hayabella' ) ) . '</span>';
}

/* ---------- Declare compatibility with WooCommerce features ---------- */

add_filter( 'woocommerce_enqueue_styles', 'hayabella_wc_styles' );
/**
 * Keep WooCommerce's layout styles for cart, checkout and account; drop the small-screen file
 * (the theme handles small screens).
 *
 * @param array $styles Styles.
 * @return array
 */
function hayabella_wc_styles( $styles ) {
	unset( $styles['woocommerce-smallscreen'] );
	return $styles;
}
