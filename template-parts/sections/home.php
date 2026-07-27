<?php
/**
 * Home / hero section.
 *
 * Numbered greeting typed in thin Fira Sans over a blurred white backdrop,
 * gradient accent square peeking out top-left, a soft animated gradient glow
 * at the bottom, and the pixel mascot walking across underneath.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<?php
// The right padding below the nav breakpoint is the floating nav bar's width
// plus its offset: the bar is fixed over this section, and without the gap the
// greeting card would sit underneath it. The card is centred inside what is
// left, so it shifts left rather than being covered.
?>
<section id="home" data-section="home" class="section relative min-h-screen flex flex-col justify-center items-center pl-4 pr-[70px] py-16 sm:pl-8 sm:pr-[80px] nav:p-10 overflow-hidden">

	<!-- Soft animated gradient at the bottom of the hero -->
	<div class="hero-glow" aria-hidden="true"></div>

	<?php
	// Profile as a full-bleed backdrop across the top of the hero, shown only
	// while the sidebar is a floating icon pill with no room for it. A direct
	// child of the section, not of .hero-stack: it has to span the viewport,
	// and inside the stack it would only span the greeting card. No id — the
	// sidebar copy owns #sidebar-profile, the Customizer partial's selector.
	?>
	<div class="hero-profile" aria-hidden="true">
		<?php echo portfolio_render_sidebar_profile( '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
	</div>

	<?php
	// .hero-stack is the fluid root the whole block scales from below sm — see
	// src/input.css. Everything inside is sized in em from it, so the mobile
	// layout is the desktop layout at a smaller scale rather than a set of
	// separately sized parts.
	?>
	<!-- Greeting card -->
	<div class="hero-stack relative z-10 flex flex-col items-center">

		<div class="relative">
			<!-- Mini-square peeking out top-left, blue gradient drifting across it -->
			<div class="hero-square absolute w-10 h-10 -top-3 -left-3 sm:w-16 sm:h-16 sm:-top-5 sm:-left-5 nav:w-20 nav:h-20 nav:-top-[30px] nav:-left-[30px] rounded-[5px] z-0" aria-hidden="true"></div>

			<div class="hero-card z-10">
				<?php echo portfolio_render_fp_greeting(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
			</div>
		</div>

		<!-- Subtitle under the card -->
		<?php echo portfolio_render_fp_subtitle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
	</div>

	<?php
	// Absolutely positioned, not part of the flex flow: a strip in the flow
	// would add its height to the centred column and push the greeting card
	// upward. Out of flow, the card keeps the exact position it has without
	// the mascot, and the creature sits low in the hero on its own.
	?>
	<div class="absolute left-0 right-0 bottom-[12%] z-10">
		<?php
		get_template_part(
			'template-parts/mascot',
			null,
			array(
				'variant' => 'hero',
				'icon'    => 'plain',
				'setup'   => __( 'I made this site.', 'portfolio' ),
				'punch'   => __( 'Scroll gently...', 'portfolio' ),
			)
		);
		?>
	</div>

</section>
