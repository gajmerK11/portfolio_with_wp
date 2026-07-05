<?php
/**
 * Customizer bootstrap.
 *
 * Callbacks load on every request (render functions must exist for the
 * template and for selective-refresh partials). Sections register only
 * inside customize_register.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

// Render callbacks — needed on the front end too.
$portfolio_cb = get_template_directory() . '/inc/customizer/callbacks/';
require_once $portfolio_cb . 'front-page.php';
require_once $portfolio_cb . 'sidebar.php';

/**
 * Register Customizer sections.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function portfolio_customize_register( $wp_customize ) {
	$portfolio_sec = get_template_directory() . '/inc/customizer/sections/';
	require $portfolio_sec . 'front-page.php';
	require $portfolio_sec . 'sidebar.php';
}
add_action( 'customize_register', 'portfolio_customize_register' );
