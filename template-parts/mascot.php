<?php
/**
 * Site mascot: a pixel creature that walks a strip of the page, captioned.
 *
 * Two variants, both loaded through $args:
 *
 *  - hero: plain creature, "I made this site." with "Scroll gently..." as a
 *    late punchline. Crosses once when the typing intro finishes.
 *  - about: headphoned creature, single line, icon trailing the text. Paces
 *    back and forth at the foot of the About section, turning around at each
 *    edge, while on screen (assets/js/main.js -> setupAboutMascot).
 *
 * $args:
 *   variant (string) 'hero'|'about' Which trigger and layout to use.
 *   icon    (string) 'plain'|'dj'   Which creature to draw.
 *   setup   (string)                The line that walks the whole way.
 *   punch   (string)                Optional late-arriving second line.
 *   trail   (bool)                  Put the creature after the text.
 *
 * The punchline is a separate span so it can fade in partway through the walk,
 * giving the gag setup-then-payoff timing instead of showing both halves at
 * once. CSS pins it out of flow just past the setup line: the walk's travel is
 * measured against the group's own width, so keeping the punchline out of that
 * width is what lets the creature start just off the left edge rather than a
 * screenful of blank space away. It fades, never toggles display — a layout
 * change mid-walk would make the creature jump.
 *
 * Pure decoration, so it is pointer-transparent and unselectable — it must
 * never sit between the visitor and the greeting or the CTA. The label is
 * split into per-character spans that bob on a staggered delay, giving the
 * text the snake-like wave that follows the creature across; the spans are
 * hidden from assistive tech and the whole strip carries a single aria-label
 * instead, so it is never announced letter by letter.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_m = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'variant' => 'hero',
		'icon'    => 'plain',
		'setup'   => '',
		'punch'   => '',
		'trail'   => false,
	)
);

// Character indices run continuously across both spans, so the wave ripples
// through the punchline as one motion rather than restarting.
$portfolio_mascot_parts = array_filter(
	array(
		'mascot-line'  => $portfolio_m['setup'],
		'mascot-punch' => $portfolio_m['punch'],
	)
);

$portfolio_mascot_label = trim( implode( ' ', $portfolio_mascot_parts ) );
if ( '' === $portfolio_mascot_label ) {
	return;
}

$portfolio_mascot_i = 0;
?>
<div
	class="mascot mascot--<?php echo esc_attr( $portfolio_m['variant'] ); ?>"
	role="img"
	aria-label="<?php echo esc_attr( $portfolio_mascot_label ); ?>"
>
	<div class="mascot-run<?php echo $portfolio_m['trail'] ? ' mascot-run--trail' : ''; ?>">

		<?php
		// .mascot-box holds the creature and the line together as one unit: it
		// takes the border in the About variant, and the impact squash. That is
		// a separate element from .mascot-run because the run carries the travel
		// — both are transforms, and two transform animations on one element
		// don't compose, the later just replaces the first.
		?>
		<span class="mascot-box">
		<span class="mascot-body">
		<?php if ( 'dj' === $portfolio_m['icon'] ) : ?>
			<?php
			// 16x15 grid: blue headphone arc over a pale band, ear cups either
			// side, salmon face and legs. Fixed fills rather than currentColor
			// — this one is multicoloured.
			?>
			<svg class="mascot-ico" viewBox="0 0 16 15" shape-rendering="crispEdges" aria-hidden="true" focusable="false">
				<!-- Pale band under the headphones -->
				<rect x="4" y="1" width="8" height="3" fill="#f4f1e8"></rect>
				<!-- Headphone arc -->
				<rect x="4" y="0" width="8" height="1" fill="#2b4a8f"></rect>
				<rect x="3" y="1" width="1" height="2" fill="#2b4a8f"></rect>
				<rect x="12" y="1" width="1" height="2" fill="#2b4a8f"></rect>
				<!-- Ear cups -->
				<rect x="2" y="3" width="2" height="4" fill="#2b4a8f"></rect>
				<rect x="12" y="3" width="2" height="4" fill="#2b4a8f"></rect>
				<!-- Body -->
				<rect x="4" y="4" width="8" height="7" fill="#d97757"></rect>
				<!-- Eyes -->
				<rect x="5.6" y="5.2" width="1.5" height="1.5" fill="#1b2a52"></rect>
				<rect x="8.9" y="5.2" width="1.5" height="1.5" fill="#1b2a52"></rect>
				<!-- Legs -->
				<rect x="4" y="11" width="1.9" height="3" fill="#d97757"></rect>
				<rect x="7.05" y="11" width="1.9" height="3" fill="#d97757"></rect>
				<rect x="10.1" y="11" width="1.9" height="3" fill="#d97757"></rect>
			</svg>
		<?php else : ?>
			<?php
			// 14x12 pixel grid, crisp edges, no anti-aliasing. Body follows
			// currentColor so the colour lives in CSS; eyes stay dark.
			?>
			<svg class="mascot-ico" viewBox="0 0 14 12" shape-rendering="crispEdges" aria-hidden="true" focusable="false">
				<!-- Arms -->
				<rect x="0.5" y="3.5" width="2.5" height="3" fill="currentColor"></rect>
				<rect x="11" y="3.5" width="2.5" height="3" fill="currentColor"></rect>
				<!-- Body -->
				<rect x="3" y="1" width="8" height="8" fill="currentColor"></rect>
				<!-- Legs -->
				<rect x="3" y="9" width="1.8" height="3" fill="currentColor"></rect>
				<rect x="6.1" y="9" width="1.8" height="3" fill="currentColor"></rect>
				<rect x="9.2" y="9" width="1.8" height="3" fill="currentColor"></rect>
				<!-- Eyes -->
				<rect x="4.6" y="2" width="1.6" height="1.6" fill="#111111"></rect>
				<rect x="7.8" y="2" width="1.6" height="1.6" fill="#111111"></rect>
			</svg>
		<?php endif; ?>
		</span>

		<?php
		// Built as one string with no whitespace between the spans. The
		// characters are inline-block, so any newline or indent between them
		// would render as a real space and pull the word apart.
		$portfolio_mascot_html = '';
		foreach ( $portfolio_mascot_parts as $portfolio_class => $portfolio_part ) {
			$portfolio_inner = '';

			// preg_split over //u keeps multibyte characters intact in
			// translations. Note the strings must contain literal characters,
			// not HTML entities — splitting per character would break an
			// entity into its pieces.
			$portfolio_chars = preg_split( '//u', $portfolio_part, -1, PREG_SPLIT_NO_EMPTY );

			foreach ( (array) $portfolio_chars as $portfolio_char ) {
				$portfolio_mascot_i++;

				if ( ' ' === $portfolio_char ) {
					$portfolio_inner .= '<span class="mascot-sp">&nbsp;</span>';
					continue;
				}

				$portfolio_inner .= '<span class="mascot-ch" style="--i:' . (int) $portfolio_mascot_i . '">' . esc_html( $portfolio_char ) . '</span>';
			}

			$portfolio_mascot_html .= '<span class="' . esc_attr( $portfolio_class ) . '">' . $portfolio_inner . '</span>';
		}
		?>
		<span class="mascot-text" aria-hidden="true"><?php echo $portfolio_mascot_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts. ?></span>
		</span><!-- .mascot-box -->

	</div>
</div>
