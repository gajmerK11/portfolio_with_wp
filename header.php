<?php
/**
 * Header: opens document, renders fixed sidebar, opens content wrapper.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script>
	/* Gate the greeting typing intro before paint: play on every load,
	   never when reduced motion is requested. */
	(function () {
		try {
			if ( ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
				document.documentElement.classList.add( 'pre-typing' );
			}
		} catch ( e ) {}
	})();
	</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-white text-dark antialiased' ); ?>>
<?php wp_body_open(); ?>

<div class="relative w-full min-h-screen md:flex">

	<?php get_template_part( 'template-parts/sidebar' ); ?>

	<?php
	// Scroll progress line at the sidebar/content divider: a light grey
	// track with a navy bar that grows with scroll (via JS).
	?>
	<span class="divider-track hidden md:block fixed top-0 left-sidebar h-screen w-[2px] z-20 pointer-events-none" aria-hidden="true"></span>
	<span id="divider-line" class="divider-line hidden md:block fixed top-0 left-sidebar h-screen w-[2px] origin-top scale-y-0 z-20 pointer-events-none" aria-hidden="true"></span>

	<!-- Fixed top-right CTA -->
	<a class="work-btn group" href="#about">
		<h4><?php esc_html_e( 'Work', 'portfolio' ); ?> <br> <?php esc_html_e( 'with me', 'portfolio' ); ?></h4>
		<span class="work-arrow ml-3" aria-hidden="true">
			<svg viewBox="0 0 300 24" fill="none" stroke="currentColor" class="text-white h-6">
				<path d="M2 12h288m0 0l-8-8m8 8l-8 8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
			</svg>
		</span>
	</a>

	<!-- Download CV side tab — editable via Customizer > Front Page -->
	<?php echo portfolio_render_download_cv(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>

	<main id="content" class="flex-1 md:ml-sidebar min-w-0">
