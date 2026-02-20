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