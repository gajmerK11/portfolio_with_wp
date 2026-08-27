<?php
/**
 * 404 template — the "cosmos" page.
 *
 * Deliberately does NOT call get_header() / get_footer(). Those render the
 * white sidebar column, the scroll divider, the "Work with me" pill, the
 * WhatsApp button and the contact drawer — all built for a white page with a
 * layout to sit beside. This page is a black full-bleed void with a canvas
 * under it, so it owns its whole document rather than fighting that chrome.
 * wp_head() / wp_footer() are still printed, so plugins and the admin bar
 * behave normally.
 *
 * Its assets are enqueued here rather than in inc/enqueue.php: the template
 * runs before wp_head() prints, so this is early enough, and it keeps the whole
 * page — markup, styles, behaviour, typeface — in files that belong to it
 * alone.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$cosmos_uri  = get_template_directory_uri();
$cosmos_path = get_template_directory();
$cosmos_css  = '/assets/css/cosmos.css';
$cosmos_js   = '/assets/js/cosmos.js';

// Figtree stands in for Graphik, which is a licensed retail typeface and cannot
// be served from here. Same geometric-grotesque skeleton and it holds its shape
// at the light weight this page sets its headline in. Swap the family in
// cosmos.css if a licensed face is ever installed.
wp_enqueue_style(
	'portfolio-cosmos-font',
	'https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500&display=swap',
	array(),
	null
);
wp_enqueue_style(
	'portfolio-cosmos',
	$cosmos_uri . $cosmos_css,
	array( 'portfolio-cosmos-font' ),
	file_exists( $cosmos_path . $cosmos_css ) ? filemtime( $cosmos_path . $cosmos_css ) : null
);
wp_enqueue_script(
	'portfolio-cosmos',
	$cosmos_uri . $cosmos_js,
	array(),
	file_exists( $cosmos_path . $cosmos_js ) ? filemtime( $cosmos_path . $cosmos_js ) : null,
	true
);

/*
 * The last row is split per character so each one can bob on its own delay,
 * which is what makes the wave travel through the word rather than lifting it
 * as a block.
 *
 * Characters are grouped into words, and only the gaps between words are real
 * spaces: the character spans are inline-block, so without that grouping the
 * row would be free to break in the middle of a word on a narrow screen.
 *
 * The spans are hidden from assistive tech and the row carries the phrase as a
 * single label — the same treatment template-parts/mascot.php gives its
 * per-character text, so it is never announced letter by letter.
 */
$cosmos_abyss = __( 'THE ABYSS', 'portfolio' );
$cosmos_words = preg_split( '/\s+/u', $cosmos_abyss, -1, PREG_SPLIT_NO_EMPTY );
$cosmos_total = count( (array) $cosmos_words );
$cosmos_i     = 0;
$cosmos_html  = '';

foreach ( (array) $cosmos_words as $cosmos_wi => $cosmos_word ) {
	$cosmos_chars = preg_split( '//u', $cosmos_word, -1, PREG_SPLIT_NO_EMPTY );
	$cosmos_inner = '';

	foreach ( (array) $cosmos_chars as $cosmos_char ) {
		$cosmos_i++;
		$cosmos_inner .= '<span class="cosmos-ch" style="--i:' . (int) $cosmos_i . '">' . esc_html( $cosmos_char ) . '</span>';
	}

	$cosmos_html .= '<span class="cosmos-word">' . $cosmos_inner . '</span>';
	if ( $cosmos_wi < $cosmos_total - 1 ) {
		$cosmos_html .= ' ';
	}
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'cosmos-page' ); ?>>
<?php wp_body_open(); ?>

<?php
// The fall itself. Decorative — the message below is the accessible content,
// so the canvas is hidden from assistive tech and takes no pointer events.
// With JS off the page is simply black, which is still the right page.
?>
<canvas id="cosmos-warp" class="cosmos-canvas" aria-hidden="true"></canvas>

<main class="cosmos-content" id="content">

	<?php
	// Three rows, each its own block: the break points are the writing, not a
	// consequence of how wide the box happens to be, so they must not be left
	// to wrapping.
	?>
	<h1 class="cosmos-msg">
		<span class="cosmos-line cosmos-line--lead"><?php esc_html_e( 'Ohh boy!', 'portfolio' ); ?></span>
		<span class="cosmos-line cosmos-line--mid"><?php esc_html_e( 'you have fallen into', 'portfolio' ); ?></span>
		<span class="cosmos-line cosmos-line--abyss" data-text="<?php echo esc_attr( $cosmos_abyss ); ?>" role="img" aria-label="<?php echo esc_attr( $cosmos_abyss ); ?>">
			<?php echo $cosmos_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped characters. ?>
		</span>
	</h1>
</main>

<?php wp_footer(); ?>
</body>
</html>
