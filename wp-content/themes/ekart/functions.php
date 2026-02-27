<?php
/**
 * Theme functions and definitions
 *
 * @package eKart
 */

/**
 * After setup theme hook
 */
function ekart_theme_setup(){
    /*
     * Make child theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
    load_child_theme_textdomain( 'ekart' );	
}
add_action( 'after_setup_theme', 'ekart_theme_setup' );

/**
 * Load assets.
 */

function ekart_theme_css() {
	wp_enqueue_style( 'ekart-parent-theme-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'ekart_theme_css', 99);

require get_stylesheet_directory() . '/theme-functions/controls/class-customize.php';

/**
 * Import Options From Parent Theme
 *
 */
function ekart_parent_theme_options() {
	$ekart_mods = get_option( 'theme_mods_shopire' );
	if ( ! empty( $ekart_mods ) ) {
		foreach ( $ekart_mods as $ekart_mod_k => $ekart_mod_v ) {
			set_theme_mod( $ekart_mod_k, $ekart_mod_v );
		}
	}
}
add_action( 'after_switch_theme', 'ekart_parent_theme_options' );

/**
 * Fix Arabic my-account logout endpoint 404 by redirecting it
 * to WooCommerce valid logout endpoint while preserving nonce.
 */
function ekart_fix_arabic_logout_endpoint_404() {
	if ( is_admin() ) {
		return;
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '/';
	$request_path = parse_url( $request_uri, PHP_URL_PATH );
	$request_path = is_string( $request_path ) ? $request_path : '/';
	$normalized   = rawurldecode( strtolower( rtrim( $request_path, '/' ) ) );

	if ( $normalized !== '/ar/حسابي/customer-logout' ) {
		return;
	}

	$target = home_url( '/my-account/customer-logout/' );

	if ( isset( $_GET['_wpnonce'] ) ) {
		$target = add_query_arg(
			'_wpnonce',
			sanitize_text_field( wp_unslash( (string) $_GET['_wpnonce'] ) ),
			$target
		);
	}

	wp_safe_redirect( $target, 302 );
	exit;
}
add_action( 'template_redirect', 'ekart_fix_arabic_logout_endpoint_404', 1 );