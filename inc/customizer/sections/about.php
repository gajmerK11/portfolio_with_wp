<?php
/**
 * Customizer section: About.
 *
 * Picks which user's profile feeds the About Me section. The content itself is
 * edited under Users > Profile ("About Me Section"). Defaults to the first
 * administrator when left as "Auto".
 *
 * @var WP_Customize_Manager $wp_customize
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$wp_customize->add_section(
	'portfolio_about',
	array(
		'title'       => __( 'About Me', 'portfolio' ),
		'description' => __( 'Choose whose profile feeds the About Me section. Edit the content under Users > Profile.', 'portfolio' ),
		'priority'    => 35,
	)
);

// Build a user list (authors and up). "0" = auto (first administrator).
$portfolio_about_users = array( 0 => __( 'Auto (first administrator)', 'portfolio' ) );
foreach ( get_users( array( 'capability' => array( 'edit_posts' ) ) ) as $portfolio_user ) {
	$portfolio_about_users[ $portfolio_user->ID ] = $portfolio_user->display_name;
}

$wp_customize->add_setting(
	'portfolio_about_user',
	array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	)
);
$wp_customize->add_control(
	'portfolio_about_user',
	array(
		'label'   => __( 'About User', 'portfolio' ),
		'section' => 'portfolio_about',
		'type'    => 'select',
		'choices' => $portfolio_about_users,
	)
);
