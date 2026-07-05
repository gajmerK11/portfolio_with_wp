<?php
/**
 * Customizer section: Front Page.
 *
 * @var WP_Customize_Manager $wp_customize
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$d = portfolio_fp_greeting_defaults();

// Section.
$wp_customize->add_section( 'portfolio_front_page', array(
	'title'       => __( 'Front Page', 'portfolio' ),
	'description' => __( 'Home hero content: greeting card, subtitle, and floating tech icons.', 'portfolio' ),
	'priority'    => 30,
) );

/* -----------------------------------------------------------------------
 * Greeting card copy. Each field accepts <span> and <br>:
 *   - Wrap a word in a span with class "text-primary" to colour it orange.
 *   - Use <br> to drop text onto the next numbered line.
 * -------------------------------------------------------------------- */
$portfolio_greet_help = __( 'Use a <span> with class "text-primary" to colour a word orange. Use <br> to drop to the next numbered line.', 'portfolio' );

$wp_customize->add_setting( 'portfolio_fp_row1', array(
	'default'           => $d['row1'],
	'sanitize_callback' => 'portfolio_kses_greeting',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'portfolio_fp_row1', array(
	'label'       => __( '01 text', 'portfolio' ),
	'description' => __( 'Row 01 content. ', 'portfolio' ) . $portfolio_greet_help,
	'section'     => 'portfolio_front_page',
	'type'        => 'textarea',
) );

$wp_customize->add_setting( 'portfolio_fp_row2', array(
	'default'           => $d['row2'],
	'sanitize_callback' => 'portfolio_kses_greeting',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'portfolio_fp_row2', array(
	'label'       => __( '02 text', 'portfolio' ),
	'description' => __( 'Rows 02 and 03 content — each <br> becomes the next numbered line. ', 'portfolio' ) . $portfolio_greet_help,
	'section'     => 'portfolio_front_page',
	'type'        => 'textarea',
) );

/* -----------------------------------------------------------------------
 * Subtitle under the card ("I also..."). Also accepts <span> and <br>.
 * -------------------------------------------------------------------- */
$wp_customize->add_setting( 'portfolio_fp_subtitle', array(
	'default'           => $d['subtitle'],
	'sanitize_callback' => 'portfolio_kses_greeting',
	'transport'         => 'postMessage',
) );
$wp_customize->add_control( 'portfolio_fp_subtitle', array(
	'label'       => __( 'Subtitle text', 'portfolio' ),
	'description' => $portfolio_greet_help,
	'section'     => 'portfolio_front_page',
	'type'        => 'textarea',
) );

/* -----------------------------------------------------------------------
 * Floating tech icons (8 image controls). Empty = built-in default.
 * -------------------------------------------------------------------- */
$portfolio_icon_defaults = portfolio_fp_icon_defaults();
foreach ( $portfolio_icon_defaults as $i => $icon ) {
	$id = 'portfolio_fp_icon_' . $i;
	$wp_customize->add_setting( $id, array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
		/* translators: %d: icon number. */
		'label'       => sprintf( __( 'Icon %d', 'portfolio' ), $i ),
		'description' => __( 'Leave empty to use the default icon.', 'portfolio' ),
		'section'     => 'portfolio_front_page',
	) ) );
}

/* -----------------------------------------------------------------------
 * Selective refresh partials (give each element the pencil edit shortcut).
 * -------------------------------------------------------------------- */
if ( isset( $wp_customize->selective_refresh ) ) {
	$wp_customize->selective_refresh->add_partial( 'portfolio_fp_greeting_partial', array(
		'selector'            => '#fp-greeting',
		'container_inclusive' => true,
		'settings'            => array( 'portfolio_fp_row1', 'portfolio_fp_row2' ),
		'render_callback'     => 'portfolio_render_fp_greeting',
	) );

	$wp_customize->selective_refresh->add_partial( 'portfolio_fp_subtitle_partial', array(
		'selector'            => '#fp-subtitle',
		'container_inclusive' => true,
		'settings'            => array( 'portfolio_fp_subtitle' ),
		'render_callback'     => 'portfolio_render_fp_subtitle',
	) );

	$wp_customize->selective_refresh->add_partial( 'portfolio_fp_icons_partial', array(
		'selector'            => '#fp-floating-icons',
		'container_inclusive' => true,
		'settings'            => array_map(
			function ( $i ) {
				return 'portfolio_fp_icon_' . $i;
			},
			array_keys( $portfolio_icon_defaults )
		),
		'render_callback'     => 'portfolio_render_fp_icons',
	) );
}
