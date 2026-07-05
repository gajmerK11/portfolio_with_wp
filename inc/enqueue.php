<?php
/**
 * Enqueue styles and scripts.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end assets: Google Fonts, compiled Tailwind CSS, GSAP, theme JS.
 */
function portfolio_enqueue_assets() {
	$theme      = wp_get_theme();
	$version    = $theme->get( 'Version' );
	$theme_uri  = get_template_directory_uri();
	$theme_path = get_template_directory();

	// Google Fonts: Inter (sans) + Fira Code (mono).
	wp_enqueue_style(
		'portfolio-fonts',
		'https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	// Compiled Tailwind stylesheet. Version by file mtime for cache-busting during dev.
	$css_rel  = '/assets/css/main.css';
	$css_ver  = file_exists( $theme_path . $css_rel ) ? filemtime( $theme_path . $css_rel ) : $version;
	wp_enqueue_style(
		'portfolio-main',
		$theme_uri . $css_rel,
		array( 'portfolio-fonts' ),
		$css_ver
	);

	// GSAP + ScrollTrigger (CDN). Loaded in footer.
	wp_enqueue_script(
		'gsap',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
		array(),
		'3.12.5',
		true
	);
	wp_enqueue_script(
		'gsap-scrolltrigger',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
		array( 'gsap' ),
		'3.12.5',
		true
	);

	// Theme JS (depends on GSAP).
	$js_rel = '/assets/js/main.js';
	$js_ver = file_exists( $theme_path . $js_rel ) ? filemtime( $theme_path . $js_rel ) : $version;
	wp_enqueue_script(
		'portfolio-main',
		$theme_uri . $js_rel,
		array( 'gsap', 'gsap-scrolltrigger' ),
		$js_ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'portfolio_enqueue_assets' );
