<?php
/**
 * "Where I've worked" experience section.
 *
 * Single column timeline: a thin navy rail with a node per entry, the newest
 * entry's node filled pink. Entries come from the Experience admin screen
 * (inc/experience.php) — one option, dragged into order, no CPT.
 *
 * Motifs are carried over from the rest of the page: numbered lines like the
 * hero greeting, the leading dot of project titles, the drifting gradient
 * chip, and the soft glow along the bottom edge.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_experience = portfolio_get_experience();

if ( empty( $portfolio_experience ) ) {
	return;
}
?>
<?php
// Reduced vertical padding below the column layout. The 6rem the desktop uses
// is a gap between two neighbouring sections of *twice* that, both paddings
// meeting — 12rem of blank page, which is a whole screen of nothing on a
// phone and most of one on a tablet. The bottom is smaller than the top
// because the last .xp-entry brings its own py-4 with it, so the gap into the
// next heading already starts 1rem ahead.
//
// The 24s are restored at `nav` rather than left as the sm value: sm reaches
// all the way up, so without that the column layout would inherit the tablet
// spacing too.
//
// Sides spelled out rather than `py-*`: the shorthand and the single-side
// utilities are separate groups in Tailwind's output order, so a `nav:py-24`
// would not reliably outrank a plain `pb-6`.
?>
<section id="experience" data-section="experience" class="section relative z-50 pt-12 pb-6 sm:pb-10 nav:pt-24 nav:pb-24 px-4 sm:px-10 overflow-hidden">

	<?php
	// Same heading treatment as the projects section: nowrap plus a fluid size,
	// because at a flat 42px the phrase broke onto a second line on every phone
	// and a heading that reflows mid-phrase reads as two headings. This is the
	// longest of the four headings, so it is the one that sets what 8vw can be:
	// at 320px it wants about 239px, which is why the section's mobile side
	// padding above was cut to 1rem to match the projects section.
	?>
	<h2 class="relative z-10 text-[clamp(1.6rem,8vw,42px)] font-semibold text-dark flex items-center gap-2 sm:gap-3 whitespace-nowrap mb-6 sm:mb-14">
		<?php esc_html_e( "Where I've worked", 'portfolio' ); ?>
		<span class="text-primary">&#10022;</span>
	</h2>

	<!-- Timeline -->
	<ol class="xp-rail relative z-10">
		<?php foreach ( $portfolio_experience as $portfolio_i => $portfolio_entry ) : ?>
			<?php
			$xp_bullets = portfolio_experience_bullets( $portfolio_entry['bullets'] );
			$xp_current = ( 0 === $portfolio_i );
			$xp_logo    = isset( $portfolio_entry['logo'] ) ? (int) $portfolio_entry['logo'] : 0;
			?>
			<li class="xp-entry<?php echo $xp_current ? ' is-current' : ''; ?>">

				<span class="xp-node" aria-hidden="true"></span>

				<div class="xp-body">
					<?php if ( '' !== trim( (string) $portfolio_entry['label'] ) ) : ?>
						<p class="xp-date"><?php echo esc_html( $portfolio_entry['label'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== trim( (string) $portfolio_entry['company'] ) || $xp_logo ) : ?>
						<h4 class="xp-company">
							<span class="xp-company-name"><?php echo esc_html( $portfolio_entry['company'] ); ?></span>
							<?php if ( $xp_logo ) : ?>
								<span class="xp-logo">
									<?php
									echo wp_get_attachment_image(
										$xp_logo,
										'medium',
										false,
										array(
											'alt'     => '',
											'loading' => 'lazy',
										)
									);
									?>
								</span>
							<?php endif; ?>
						</h4>
					<?php endif; ?>

					<?php if ( '' !== trim( (string) $portfolio_entry['role'] ) ) : ?>
						<p class="xp-role"><?php echo esc_html( $portfolio_entry['role'] ); ?></p>
					<?php endif; ?>

					<?php if ( $xp_bullets ) : ?>
						<ul class="xp-bullets">
							<?php foreach ( $xp_bullets as $xp_bullet ) : ?>
								<li><?php echo esc_html( $xp_bullet ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
</section>
