<?php
/**
 * About Me section.
 *
 * Content comes from the Page chosen in Customizer > About (or the page with
 * slug "about"), edited through the "About Me Content" meta box. Layout follows
 * the reference: name + location, two lead/description blocks, a journey link,
 * and two skill-icon groups.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_about_id = function_exists( 'portfolio_about_user_id' ) ? portfolio_about_user_id() : 0;
if ( ! $portfolio_about_id ) {
	return; // No About user available.
}

$a = array(
	'name'     => get_user_meta( $portfolio_about_id, 'portfolio_about_name', true ),
	'location' => get_user_meta( $portfolio_about_id, 'portfolio_about_location', true ),
	'lead1'    => get_user_meta( $portfolio_about_id, 'portfolio_about_lead1', true ),
	'desc1'    => get_user_meta( $portfolio_about_id, 'portfolio_about_desc1', true ),
	'lead2'    => get_user_meta( $portfolio_about_id, 'portfolio_about_lead2', true ),
	'desc2'    => get_user_meta( $portfolio_about_id, 'portfolio_about_desc2', true ),
);
// Fall back to the user's display name when no explicit name is set.
if ( '' === $a['name'] ) {
	$portfolio_about_user = get_userdata( $portfolio_about_id );
	$a['name']            = $portfolio_about_user ? $portfolio_about_user->display_name : '';
}
?>
<?php
// Flow from the top (py rhythm shared with skills/experience) rather than
// force a full-screen, vertically centred block — with little content the
// centring left a large empty gap after the Skills section.
?>
<?php
// The width cap sits on an inner wrapper rather than the section, so the
// mascot strip below can span the full content column — out to the divider
// line on the left — while the copy stays at a readable measure.
?>
<?php
// The phone bottom padding is deeper than the tablet one, which is otherwise
// backwards. This is the last section on the page, so the mascot strip at its
// foot ends up level with the two fixed buttons — WhatsApp bottom-left, "Work
// with me" bottom-right. Above sm the strip's track is inset horizontally so
// the creature turns at their inner edges (assets/js/main.js -> fitTrack); a
// phone has no width to give up for that, so the strip is lifted clear of them
// instead.
//
// 8rem, with the strip's own mt-10 above it, is what centres the strip in the
// space it actually has. The buttons stand about 85px off the bottom of the
// page when it is scrolled to the end, so this padding is not free space — it
// is 85px of button and ~43px of gap. Reading it as free space is what had the
// strip looking like it was sitting on top of them.
?>
<section id="about" data-section="about" class="section relative flex flex-col pt-12 pb-32 sm:pb-12 nav:pt-24 px-4 sm:px-10">

	<?php
	// Download CV, tablet widths only (sm up to the nav breakpoint). It is a
	// child of this section, so it exists on About and nowhere else — that is
	// structural, no scroll logic needed.
	//
	// The rail is why it holds still while the copy scrolls past. The tab is
	// sticky, and sticky travels inside its parent's box, so it needs a parent
	// that spans the section: the rail is that, absolutely positioned down the
	// left gutter and therefore out of the section's flex column. Putting the
	// tab in that column directly would instead reserve its full height and
	// push the name and the copy down the page.
	?>
	<div class="cv-rail" aria-hidden="false">
		<?php echo portfolio_render_download_cv( 'about' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
	</div>

	<div class="flex flex-col max-w-4xl">

	<!-- Name + location -->
	<?php if ( $a['name'] ) : ?>
		<h2 class="text-[48px] font-bold text-dark uppercase tracking-tight"><?php echo esc_html( $a['name'] ); ?></h2>
	<?php endif; ?>
	<?php if ( $a['location'] ) : ?>
		<p class="text-dark mt-1 text-xl"><?php echo esc_html( $a['location'] ); ?></p>
	<?php endif; ?>

	<!-- Block 1 -->
	<?php if ( $a['lead1'] ) : ?>
		<h3 class="text-[32px] font-normal text-dark mt-10 leading-snug"><?php echo esc_html( $a['lead1'] ); ?></h3>
	<?php endif; ?>
	<?php if ( $a['desc1'] ) : ?>
		<p class="text-dark text-[22px] mt-4 leading-relaxed max-w-2xl"><?php echo nl2br( esc_html( $a['desc1'] ) ); ?></p>
	<?php endif; ?>

	<!-- Block 2 -->
	<?php if ( $a['lead2'] ) : ?>
		<h3 class="text-[32px] font-normal text-dark mt-10 leading-snug"><?php echo esc_html( $a['lead2'] ); ?></h3>
	<?php endif; ?>
	<?php if ( $a['desc2'] ) : ?>
		<p class="text-dark text-[22px] mt-4 leading-relaxed max-w-2xl"><?php echo nl2br( esc_html( $a['desc2'] ) ); ?></p>
	<?php endif; ?>

	</div>

	<?php
	// Mascot paces the foot of the section. The negative margin cancels the
	// section padding so its left edge is the divider line and its right edge
	// the content edge — those are the two walls it turns around at. It has to
	// track the section's padding at both breakpoints, or the walls move.
	?>
	<div class="mascot-track -mx-4 sm:-mx-10 mt-10 sm:mt-24">
		<?php
		get_template_part(
			'template-parts/mascot',
			null,
			array(
				'variant' => 'about',
				'icon'    => 'dj',
				'trail'   => true,
				'setup'   => __( "If you find anything broken, trust me, it's his fault", 'portfolio' ),
			)
		);
		?>
	</div>

</section>
