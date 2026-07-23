<?php
/**
 * Front Page render callbacks.
 *
 * Shared by the template (template-parts/sections/home.php) and by the
 * Customizer selective-refresh partials, so preview + front match exactly.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize greeting/subtitle copy: allow only <span class> and <br>.
 *
 * Lets the user colour words (span.text-primary) and break lines (br)
 * from the Customizer without opening up arbitrary HTML.
 *
 * @param string $value Raw value.
 * @return string
 */
function portfolio_kses_greeting( $value ) {
	return wp_kses(
		$value,
		array(
			'span' => array( 'class' => array() ),
			'br'   => array(),
		)
	);
}

/**
 * Default greeting-card copy.
 *
 * row1 = line 01. row2 = lines 02 + 03 (split on <br>).
 *
 * @return array
 */
function portfolio_fp_greeting_defaults() {
	return array(
		'row1'     => '<span class="text-neutral">&lt;</span> Hi, I\'m <span class="text-primary">Brice</span> ! <span class="text-neutral">&gt;</span>',
		'row2'     => '<span class="text-neutral">&lt;</span> I <span class="text-primary">design</span> and <span class="text-primary">develop</span> <br> <span class="text-primary">web sites</span> . <span class="text-neutral">&gt;</span>',
		'subtitle' => 'I also design your branding, logo...',
	);
}

/**
 * Default floating tech icons: url, position, size, label.
 *
 * @return array Keyed 1..8.
 */
function portfolio_fp_icon_defaults() {
	return array(
		1 => array( 'url' => 'https://cdn.simpleicons.org/php',        'pos' => 'top:13%;left:39%;', 'size' => 'w-24 h-24', 'label' => 'PHP' ),
		2 => array( 'url' => 'https://cdn.simpleicons.org/react',      'pos' => 'top:18%;left:62%;', 'size' => 'w-20 h-20', 'label' => 'React' ),
		3 => array( 'url' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-plain-wordmark.svg', 'pos' => 'top:40%;left:15%;', 'size' => 'w-28 h-28', 'label' => 'Node.js' ),
		4 => array( 'url' => 'https://cdn.simpleicons.org/javascript', 'pos' => 'top:57%;left:86%;', 'size' => 'w-20 h-20', 'label' => 'JavaScript' ),
		5 => array( 'url' => 'https://cdn.simpleicons.org/wordpress',  'pos' => 'top:73%;left:20%;', 'size' => 'w-20 h-20', 'label' => 'WordPress' ),
		6 => array( 'url' => 'https://cdn.simpleicons.org/python',     'pos' => 'top:80%;left:48%;', 'size' => 'w-20 h-20', 'label' => 'Python' ),
		7 => array( 'url' => 'https://cdn.simpleicons.org/postgresql', 'pos' => 'top:78%;left:78%;', 'size' => 'w-20 h-20', 'label' => 'PostgreSQL' ),
	);
}

/**
 * Resolve a floating icon URL: theme mod or default.
 *
 * @param int $i Icon index (1..8).
 * @return string
 */
function portfolio_fp_icon_url( $i ) {
	$defaults = portfolio_fp_icon_defaults();
	$default  = isset( $defaults[ $i ] ) ? $defaults[ $i ]['url'] : '';
	// An empty saved value means "use the built-in default icon".
	$value = get_theme_mod( 'portfolio_fp_icon_' . $i, '' );
	return ( '' !== $value ) ? $value : $default;
}

/**
 * Render the greeting card copy block.
 *
 * Line numbers (01, 02, 03…) are generated automatically: row1 is line 01,
 * and row2 adds one numbered line per <br>-separated segment.
 *
 * @return string
 */
function portfolio_render_fp_greeting() {
	$d    = portfolio_fp_greeting_defaults();
	$row1 = get_theme_mod( 'portfolio_fp_row1', $d['row1'] );
	$row2 = get_theme_mod( 'portfolio_fp_row2', $d['row2'] );

	// Build the visual lines.
	$lines = array();
	if ( trim( wp_strip_all_tags( $row1 ) ) !== '' || trim( $row1 ) !== '' ) {
		$lines[] = trim( $row1 );
	}
	foreach ( preg_split( '/<br\s*\/?>/i', $row2 ) as $part ) {
		if ( trim( $part ) !== '' ) {
			$lines[] = trim( $part );
		}
	}

	$total = count( $lines );

	ob_start();
	?>
	<div id="fp-greeting" class="font-hero font-light text-dark text-3xl sm:text-5xl lg:text-[3.8rem] leading-tight">
		<?php
		foreach ( $lines as $idx => $line ) :
			// Indent continuation lines (03 onward) so they align under the
			// text of line 02 rather than under its opening bracket.
			$indent = ( $idx >= 2 ) ? ' ml-7' : '';
			?>
			<div class="flex items-center gap-5<?php echo ( $idx < $total - 1 ) ? ' mb-8' : ''; ?>">
				<span class="text-neutral text-2xl font-normal"><?php echo esc_html( str_pad( $idx + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
				<div class="type-line whitespace-nowrap<?php echo esc_attr( $indent ); ?>"><?php echo portfolio_kses_greeting( $line ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses-sanitized. ?></div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render the subtitle under the card.
 *
 * @return string
 */
function portfolio_render_fp_subtitle() {
	$d        = portfolio_fp_greeting_defaults();
	$subtitle = get_theme_mod( 'portfolio_fp_subtitle', $d['subtitle'] );
	return '<p id="fp-subtitle" class="rise-in font-hero font-light text-xl sm:text-2xl text-dark p-5 text-center">' . portfolio_kses_greeting( $subtitle ) . '</p>';
}

/**
 * Render the "Download CV" vertical tab pinned to the right edge.
 *
 * Links to the file chosen in Customizer > Front Page. Rendered as a
 * selective-refresh partial so it gets a pencil icon in the preview.
 *
 * @return string
 */
function portfolio_render_download_cv() {
	$cv_url = get_theme_mod( 'portfolio_cv_url', '' );

	ob_start();
	?>
	<a id="download-cv" class="download-cv" <?php echo $cv_url ? 'href="' . esc_url( $cv_url ) . '" target="_blank" rel="noopener noreferrer"' : 'href="#"'; ?>>
		<span class="cv-mark" aria-hidden="true">&darr;</span>
		<span class="cv-label"><?php esc_html_e( 'Download CV', 'portfolio' ); ?></span>
	</a>
	<?php
	return ob_get_clean();
}

/**
 * Render the floating tech icons layer.
 *
 * Icons render greyscale + faded and reveal their original colour on
 * hover — see .floating-icon in src/input.css. pointer-events are enabled
 * on the icon only, so the gaps between icons stay click-through.
 *
 * @return string
 */
function portfolio_render_fp_icons() {
	$icons = portfolio_fp_icon_defaults();
	ob_start();
	?>
	<div id="fp-floating-icons" class="absolute inset-0 pointer-events-none overflow-hidden z-0" aria-hidden="true">
		<?php foreach ( $icons as $i => $icon ) : ?>
			<div class="floating-icon <?php echo esc_attr( $icon['size'] ); ?>" style="<?php echo esc_attr( $icon['pos'] ); ?>transform:translate(-50%,-50%);">
				<img alt="" src="<?php echo esc_url( portfolio_fp_icon_url( $i ) ); ?>" style="animation-delay:<?php echo esc_attr( number_format( ( $i - 1 ) * 0.8, 1 ) ); ?>s;" loading="lazy">
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
