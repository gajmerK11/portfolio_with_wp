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
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-[#fafafa] text-dark antialiased' ); ?>>
<?php wp_body_open(); ?>

<div class="relative w-full min-h-screen md:flex">

	<?php get_template_part( 'template-parts/sidebar' ); ?>

	<?php
	// The blue vertical accent line at the sidebar/content divider.
	// Shown/animated via JS on scroll.
	?>
	<span id="divider-line" class="hidden md:block fixed top-0 left-72 h-screen w-px bg-primary origin-top scale-y-0 z-20 pointer-events-none" aria-hidden="true"></span>

	<main id="content" class="flex-1 md:ml-72 min-w-0">
