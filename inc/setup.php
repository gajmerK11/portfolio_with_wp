<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and navigation menus.
 */
function portfolio_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Sidebar Nav', 'portfolio' ),
		)
	);
}
add_action( 'after_setup_theme', 'portfolio_setup' );
