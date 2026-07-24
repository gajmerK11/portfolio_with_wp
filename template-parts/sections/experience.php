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
<section id="experience" data-section="experience" class="section relative z-50 py-24 px-10 overflow-hidden">

	<h2 class="relative z-10 text-[42px] font-semibold text-dark flex items-center gap-3 mb-14">
		<?php esc_html_e( "Where I've worked", 'portfolio' ); ?>
		<span class="text-primary">&#10022;</span>
	</h2>

	<!-- Timeline -->
	<ol class="xp-rail relative z-10">
		<?php foreach ( $portfolio_experience as $portfolio_i => $portfolio_entry ) : ?>
			<?php
			$xp_bullets = portfolio_experience_bullets( $portfolio_entry['bullets'] );
			$xp_current = ( 0 === $portfolio_i );
			?>
			<li class="xp-entry<?php echo $xp_current ? ' is-current' : ''; ?>">

				<span class="xp-node" aria-hidden="true"></span>

				<div class="xp-body">
					<?php if ( '' !== trim( (string) $portfolio_entry['label'] ) ) : ?>
						<p class="xp-date"><?php echo esc_html( $portfolio_entry['label'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== trim( (string) $portfolio_entry['company'] ) ) : ?>
						<h4 class="xp-company"><?php echo esc_html( $portfolio_entry['company'] ); ?></h4>
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
