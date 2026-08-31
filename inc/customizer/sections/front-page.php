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
$portfolio_greet_help = __( 'Use a <span> with class "text-primary" to colour a word orange. Use <br> to drop to the next numbered line. Drop in an icon with [icon:backend] or [icon:wordpress].', 'portfolio' );

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
 * The CV upload and the eight floating-icon pickers used to live here.
 *
 * The CV moved to the owner's user profile (inc/user-profile-cv.php), which is
 * where the rest of the owner's own material already is; portfolio_cv_url()
 * still falls back to the old theme mod, so a CV uploaded here before the move
 * keeps working. The floating hero icons are a fixed set of technology marks —
 * they are decoration, not content, and eight image pickers for them buried
 * the three fields on this screen anyone actually edits.
 *
 * Neither setting is registered any more, so neither can be changed from the
 * Customizer. Any values already saved are still read (see the callbacks in
 * inc/customizer/callbacks/front-page.php) and are not deleted.
 * -------------------------------------------------------------------- */

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

}
