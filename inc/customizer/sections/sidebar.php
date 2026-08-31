<?php
/**
 * Customizer section: Sidebar.
 *
 * @var WP_Customize_Manager $wp_customize
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

// Section.
$wp_customize->add_section( 'portfolio_sidebar', array(
	'title'       => __( 'Sidebar', 'portfolio' ),
	'description' => __( 'Left sidebar settings.', 'portfolio' ),
	'priority'    => 31,
) );

// Profile picture.
$wp_customize->add_setting( 'portfolio_sidebar_profile_image', array(
	'default'           => '',
	'sanitize_callback' => 'esc_url_raw',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'portfolio_sidebar_profile_image', array(
	'label'       => __( 'Profile picture', 'portfolio' ),
	'description' => __( 'Upload a profile photo. Falls back to the default placeholder when empty.', 'portfolio' ),
	'section'     => 'portfolio_sidebar',
) ) );

/* -----------------------------------------------------------------------
 * Social links at the foot of the sidebar. Both icons were hard-coded to
 * href="#", so the only way to point them anywhere was to edit the template.
 * An empty field hides its icon rather than rendering a dead link.
 * -------------------------------------------------------------------- */
$wp_customize->add_setting( 'portfolio_sidebar_linkedin', array(
	'default'           => '',
	'sanitize_callback' => 'esc_url_raw',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'portfolio_sidebar_linkedin', array(
	'label'       => __( 'LinkedIn URL', 'portfolio' ),
	'description' => __( 'Leave empty to hide the LinkedIn icon.', 'portfolio' ),
	'section'     => 'portfolio_sidebar',
	'type'        => 'url',
	'input_attrs' => array( 'placeholder' => 'https://www.linkedin.com/in/…' ),
) );

$wp_customize->add_setting( 'portfolio_sidebar_github', array(
	'default'           => '',
	'sanitize_callback' => 'esc_url_raw',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'portfolio_sidebar_github', array(
	'label'       => __( 'GitHub URL', 'portfolio' ),
	'description' => __( 'Leave empty to hide the GitHub icon.', 'portfolio' ),
	'section'     => 'portfolio_sidebar',
	'type'        => 'url',
	'input_attrs' => array( 'placeholder' => 'https://github.com/…' ),
) );

// Selective refresh partials (pencil edit shortcut).
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial( 'portfolio_sidebar_profile_partial', array(
		'selector'            => '#sidebar-profile',
		'container_inclusive' => true,
		'settings'            => array( 'portfolio_sidebar_profile_image' ),
		'render_callback'     => 'portfolio_render_sidebar_profile',
	) );

	$wp_customize->selective_refresh->add_partial( 'portfolio_sidebar_social_partial', array(
		'selector'            => '#sidebar-social',
		'container_inclusive' => true,
		'settings'            => array( 'portfolio_sidebar_linkedin', 'portfolio_sidebar_github' ),
		'render_callback'     => 'portfolio_render_sidebar_social',
	) );
}
