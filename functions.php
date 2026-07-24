<?php
/**
 * Portfolio theme bootstrap.
 *
 * Keep this file minimal. Only require modular includes here —
 * put actual logic inside the relevant file under /inc.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_includes = array(
	'/inc/setup.php',                              // Theme supports, menus, basic registration.
	'/inc/enqueue.php',                            // Styles and scripts.
	'/inc/customizer/customizer.php',              // Customizer sections and render callbacks.
	'/inc/cpts/project-cpt.php',                   // Projects custom post type.
	'/inc/cpts/testimonial-cpt.php',               // Testimonials custom post type.
	'/inc/cpts/skill-cpt.php',                     // Skills custom post type.
	'/inc/meta-boxes/class-project-meta-box.php',  // Project details meta box.
	'/inc/meta-boxes/class-testimonial-meta-box.php', // Testimonial details meta box.
	'/inc/meta-boxes/class-skill-meta-box.php',    // Skill category details meta box.
	'/inc/user-profile-about.php',                 // About Me content (site owner's user profile).
	'/inc/experience.php',                         // Experience entries (admin screen + data access).
	'/inc/contact-form.php',                       // Contact panel submit handler (admin-ajax + wp_mail).
);

foreach ( $portfolio_includes as $portfolio_include ) {
	require_once get_template_directory() . $portfolio_include;
}
