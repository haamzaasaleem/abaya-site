<?php
/**
 * Extra product fields (Products → Edit → "Hayabella card" box):
 * card video loop, the small line above the name, and a badge.
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', 'hayabella_product_box' );
/** Register the box. */
function hayabella_product_box() {
	add_meta_box( 'hayabella-card', __( 'Hayabella card', 'hayabella' ), 'hayabella_product_box_html', 'product', 'side', 'default' );
}

/**
 * Box markup.
 *
 * @param WP_Post $post Product.
 */
function hayabella_product_box_html( $post ) {
	wp_nonce_field( 'hayabella_card', 'hayabella_card_nonce' );
	$video = get_post_meta( $post->ID, '_hb_card_video', true );
	$tag   = get_post_meta( $post->ID, '_hb_card_tag', true );
	$badge = get_post_meta( $post->ID, '_hb_badge', true );
	?>
	<p>
		<label for="hb_card_video"><strong><?php esc_html_e( 'Card video loop', 'hayabella' ); ?></strong></label><br>
		<input type="url" id="hb_card_video" name="hb_card_video" value="<?php echo esc_attr( $video ); ?>" class="widefat" placeholder="https://…/clip.mp4">
		<button type="button" class="button hb-pick-video" style="margin-top:6px"><?php esc_html_e( 'Choose video', 'hayabella' ); ?></button>
		<span class="description" style="display:block;margin-top:6px"><?php esc_html_e( 'Optional. A short silent MP4 (about 5 s, 3:4 portrait, under 1 MB) that plays on the product card while it is on screen. Leave empty to show the product image (and the second gallery image on hover).', 'hayabella' ); ?></span>
	</p>
	<p>
		<label for="hb_card_tag"><strong><?php esc_html_e( 'Line above the name', 'hayabella' ); ?></strong></label><br>
		<input type="text" id="hb_card_tag" name="hb_card_tag" value="<?php echo esc_attr( $tag ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. Satin | Crystal Border', 'hayabella' ); ?>">
		<span class="description"><?php esc_html_e( 'Empty: the product category is shown.', 'hayabella' ); ?></span>
	</p>
	<p>
		<label for="hb_badge"><strong><?php esc_html_e( 'Badge', 'hayabella' ); ?></strong></label><br>
		<input type="text" id="hb_badge" name="hb_badge" value="<?php echo esc_attr( $badge ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'e.g. New, Limited, Bestseller', 'hayabella' ); ?>">
		<span class="description"><?php esc_html_e( 'Sale and Sold out badges are added automatically.', 'hayabella' ); ?></span>
	</p>
	<?php
}

add_action( 'save_post_product', 'hayabella_product_box_save' );
/**
 * Save the fields.
 *
 * @param int $post_id Product ID.
 */
function hayabella_product_box_save( $post_id ) {
	if ( ! isset( $_POST['hayabella_card_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['hayabella_card_nonce'] ) ), 'hayabella_card' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$fields = array(
		'hb_card_video' => array( '_hb_card_video', 'esc_url_raw' ),
		'hb_card_tag'   => array( '_hb_card_tag', 'sanitize_text_field' ),
		'hb_badge'      => array( '_hb_badge', 'sanitize_text_field' ),
	);
	foreach ( $fields as $field => $conf ) {
		$value = isset( $_POST[ $field ] ) ? call_user_func( $conf[1], wp_unslash( $_POST[ $field ] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised by the callback.
		if ( '' === $value ) {
			delete_post_meta( $post_id, $conf[0] );
		} else {
			update_post_meta( $post_id, $conf[0], $value );
		}
	}
}

add_action( 'admin_enqueue_scripts', 'hayabella_product_box_js' );
/**
 * Media picker for the video field.
 *
 * @param string $hook Admin page.
 */
function hayabella_product_box_js( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'product' !== get_post_type() ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script(
		'media-editor',
		"jQuery(function($){ $(document).on('click','.hb-pick-video',function(e){ e.preventDefault(); var f=wp.media({title:'" . esc_js( __( 'Card video loop', 'hayabella' ) ) . "',library:{type:'video'},multiple:false}); f.on('select',function(){ $('#hb_card_video').val(f.state().get('selection').first().toJSON().url); }); f.open(); }); });"
	);
}
