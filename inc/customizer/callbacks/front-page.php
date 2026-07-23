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
 * Inline icons available to the greeting copy.
 *
 * Written as [icon:name] tokens in the Customizer field and swapped for the
 * SVG below at render time. Tokens rather than <img> tags keep the stored
 * copy free of absolute URLs, so it survives a site move. Icons inherit the
 * surrounding text colour.
 *
 * @return array name => SVG body (paths only).
 */
function portfolio_fp_greeting_icons() {
	return array(
		// Stacked servers — back-end work.
		'backend'   => '<svg class="hero-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17.25 17.25h.008v.008h-.008zM17.25 11.25h.008v.008h-.008zM14.25 17.25h.008v.008h-.008zM14.25 11.25h.008v.008h-.008z" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
		// WordPress mark.
		'wordpress' => '<svg class="hero-ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zM1.211 12c0-1.564.336-3.05.935-4.39L7.25 21.87A10.79 10.79 0 0 1 1.211 12zM12 22.79c-1.06 0-2.084-.155-3.05-.44l3.24-9.42 3.32 9.1c.022.053.048.102.077.148A10.75 10.75 0 0 1 12 22.79zm1.488-15.86c.65-.034 1.235-.102 1.235-.102.582-.7.514-.897-.068-.863 0 0-1.75.137-2.879.137-1.06 0-2.845-.137-2.845-.137-.583-.034-.65.863-.069.897 0 0 .55.068 1.132.102l1.681 4.607-2.362 7.084-3.93-11.69c.65-.035 1.234-.103 1.234-.103.582-.7.513-.897-.069-.863 0 0-1.749.137-2.878.137-.203 0-.442-.005-.696-.013C4.911 3.15 8.235 1.211 12 1.211c2.804 0 5.357 1.072 7.275 2.829-.046-.003-.092-.009-.139-.009-1.06 0-1.812.923-1.812 1.914 0 .888.512 1.639 1.06 2.528.411.72.89 1.643.89 2.977 0 .923-.354 1.995-.82 3.49l-1.075 3.59-3.891-11.6zm4.972 13.783l3.297-9.532c.616-1.54.82-2.77.82-3.867 0-.398-.026-.767-.073-1.111a10.72 10.72 0 0 1 1.285 5.117c0 3.98-2.156 7.454-5.33 9.393z"></path></svg>',
	);
}

/**
 * Swap [icon:name] tokens for their inline SVG.
 *
 * Runs after wp_kses, so the SVG markup is theme-authored, never user input.
 *
 * @param string $value Sanitized copy.
 * @return string
 */
function portfolio_fp_expand_icons( $value ) {
	$icons = portfolio_fp_greeting_icons();

	return preg_replace_callback(
		'/\[icon:([a-z0-9_-]+)\]/i',
		function ( $m ) use ( $icons ) {
			$key = strtolower( $m[1] );
			return isset( $icons[ $key ] ) ? $icons[ $key ] : '';
		},
		$value
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
	<?php
	// Line metrics mirror the reference design: 3.8rem type on a flat 5rem line
	// box (its `line-height: 500%` of a 1rem parent), thin grey line numbers
	// 1.5rem with a 20px gutter. No margin between rows — the tall line box is
	// what does the spacing. Weight is 400, not the reference's declared 300:
	// its Fira Sans never loads, so it falls back to Arial, which has no Light
	// and renders at 400. Matching the number would look markedly thinner.
	?>
	<div id="fp-greeting" class="font-hero font-thin text-dark text-3xl sm:text-5xl lg:text-[3.8rem] leading-[2.8rem] sm:leading-[3.6rem] lg:leading-[5rem]">
		<?php
		foreach ( $lines as $idx => $line ) :
			// Indent continuation lines (03 onward) so they align under the
			// text of line 02 rather than under its opening bracket.
			$indent = ( $idx >= 2 ) ? ' ml-[1.8rem]' : '';
			?>
			<div class="flex items-center">
				<span class="text-neutral text-2xl font-thin mr-5"><?php echo esc_html( str_pad( $idx + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
				<div class="type-line font-normal whitespace-nowrap pr-[10px]<?php echo esc_attr( $indent ); ?>"><?php echo portfolio_fp_expand_icons( portfolio_kses_greeting( $line ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses-sanitized, then theme-owned SVG spliced in. ?></div>
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
	// Bold 1.5rem with 20px padding — the reference renders this as an <h4>,
	// which the browser's default stylesheet sets bold.
	return '<p id="fp-subtitle" class="rise-in font-hero font-bold text-xl sm:text-2xl text-dark p-5 text-center">' . portfolio_kses_greeting( $subtitle ) . '</p>';
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
		<span class="cv-mark" aria-hidden="true">CV.</span>
		<?php
		// One span per letter so they can be bounced in sequence on hover.
		$cv_label   = __( 'Download', 'portfolio' );
		$cv_letters = preg_split( '//u', $cv_label, -1, PREG_SPLIT_NO_EMPTY );
		?>
		<span class="cv-label" aria-label="<?php echo esc_attr( $cv_label ); ?>">
			<?php foreach ( $cv_letters as $pos => $cv_letter ) : ?>
				<span class="cv-letter" style="animation-delay:<?php echo esc_attr( number_format( $pos * 0.07, 2 ) ); ?>s" aria-hidden="true"><?php echo esc_html( $cv_letter ); ?></span>
			<?php endforeach; ?>
		</span>
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
