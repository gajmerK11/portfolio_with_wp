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

// Selective refresh partial (pencil edit shortcut).
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial( 'portfolio_sidebar_profile_partial', array(
		'selector'            => '#sidebar-profile',
		'container_inclusive' => true,
		'settings'            => array( 'portfolio_sidebar_profile_image' ),
		'render_callback'     => 'portfolio_render_sidebar_profile',
	) );
}
