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
	<span class="divider-track hidden md:block fixed top-0 left-sidebar h-screen w-[2px] z-[55] pointer-events-none" aria-hidden="true"></span>
	<span id="divider-line" class="divider-line hidden md:block fixed top-0 left-sidebar h-screen w-[2px] origin-top scale-y-0 z-[55] pointer-events-none" aria-hidden="true"></span>

	<!-- Fixed top-right CTA — opens the slide-in contact panel -->
	<a class="work-btn group" href="#contact" data-contact-open aria-haspopup="dialog">
		<h4><?php esc_html_e( 'Work', 'portfolio' ); ?> <br> <?php esc_html_e( 'with me', 'portfolio' ); ?></h4>
		<span class="work-arrow" aria-hidden="true">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/aright.svg' ); ?>" alt="">
		</span>
	</a>

	<!-- Download CV side tab — editable via Customizer > Front Page -->
	<?php echo portfolio_render_download_cv(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>

	<!-- Slide-in contact panel (opened by "Work with me") -->
	<?php get_template_part( 'template-parts/contact' ); ?>

	<main id="content" class="flex-1 md:ml-sidebar min-w-0">
