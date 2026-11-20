<?php
/**
 * Remote theme updates: an administrator (authenticated with an Application Password) can tell
 * the site to install the latest version of this theme from the GitHub repository.
 *
 *   GET  /wp-json/hayabella/v1/status   theme version, WordPress and WooCommerce versions
 *   POST /wp-json/hayabella/v1/deploy   download the repository and update this theme's files
 *
 * Only users who can both update themes and manage options may call these. The download address
 * is fixed below (it is never taken from the request).
 *
 * @package Hayabella
 */

defined( 'ABSPATH' ) || exit;

/** Repository archive the theme is installed from (folder wordpress-theme/hayabella inside it). */
if ( ! defined( 'HAYABELLA_DEPLOY_ZIP' ) ) define( 'HAYABELLA_DEPLOY_ZIP', 'https://codeload.github.com/haamzaasaleem/abaya-site/zip/refs/heads/main' );

add_action( 'rest_api_init', 'hayabella_deploy_routes' );
/** Register the routes. */
function hayabella_deploy_routes() {
	$can = function () {
		return current_user_can( 'update_themes' ) && current_user_can( 'manage_options' );
	};
	register_rest_route(
		'hayabella/v1',
		'/status',
		array(
			'methods'             => 'GET',
			'permission_callback' => $can,
			'callback'            => 'hayabella_deploy_status',
		)
	);
	register_rest_route(
		'hayabella/v1',
		'/deploy',
		array(
			'methods'             => 'POST',
			'permission_callback' => $can,
			'callback'            => 'hayabella_deploy_run',
		)
	);
}

/** Versions and state. */
function hayabella_deploy_status() {
	$theme = wp_get_theme( 'hayabella' );
	return array(
		'theme_version' => $theme->get( 'Version' ),
		'theme_active'  => 'hayabella' === get_template(),
		'wordpress'     => get_bloginfo( 'version' ),
		'woocommerce'   => defined( 'WC_VERSION' ) ? WC_VERSION : null,
		'php'           => PHP_VERSION,
	);
}

/**
 * Download the repository, check it holds the theme, then copy the theme files over this one.
 *
 * @return array|WP_Error
 */
function hayabella_deploy_run() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
	}
	if ( ! WP_Filesystem() ) {
		return new WP_Error( 'hb_fs', __( 'WordPress cannot write files directly on this server.', 'hayabella' ), array( 'status' => 500 ) );
	}
	global $wp_filesystem;

	$zip = download_url( HAYABELLA_DEPLOY_ZIP, 300 );
	if ( is_wp_error( $zip ) ) {
		return new WP_Error( 'hb_download', $zip->get_error_message(), array( 'status' => 502 ) );
	}
	$work = trailingslashit( get_temp_dir() ) . 'hayabella-deploy-' . wp_generate_password( 8, false );
	$res  = unzip_file( $zip, $work );
	wp_delete_file( $zip );
	if ( is_wp_error( $res ) ) {
		$wp_filesystem->delete( $work, true );
		return new WP_Error( 'hb_unzip', $res->get_error_message(), array( 'status' => 500 ) );
	}

	$found = glob( $work . '/*/wordpress-theme/hayabella/style.css' );
	if ( ! $found ) {
		$wp_filesystem->delete( $work, true );
		return new WP_Error( 'hb_missing', __( 'The theme folder was not found in the repository.', 'hayabella' ), array( 'status' => 500 ) );
	}
	$src     = dirname( $found[0] );
	$headers = get_file_data( $src . '/style.css', array( 'name' => 'Theme Name', 'version' => 'Version' ) );
	if ( 'Hayabella' !== $headers['name'] || ! file_exists( $src . '/functions.php' ) ) {
		$wp_filesystem->delete( $work, true );
		return new WP_Error( 'hb_invalid', __( 'The downloaded theme is not valid.', 'hayabella' ), array( 'status' => 500 ) );
	}

	$dest   = trailingslashit( get_theme_root( 'hayabella' ) ) . 'hayabella';
	$before = wp_get_theme( 'hayabella' )->get( 'Version' );
	$copied = copy_dir( $src, $dest );
	$wp_filesystem->delete( $work, true );
	if ( is_wp_error( $copied ) ) {
		return new WP_Error( 'hb_copy', $copied->get_error_message(), array( 'status' => 500 ) );
	}
	if ( function_exists( 'opcache_reset' ) ) {
		opcache_reset();
	}
	wp_clean_themes_cache();
	return array(
		'ok'           => true,
		'from_version' => $before,
		'to_version'   => $headers['version'],
	);
}
