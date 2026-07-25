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
<section id="home" data-section="home" class="section relative min-h-screen flex flex-col justify-center items-center p-10 overflow-hidden">

	<!-- Soft animated gradient at the bottom of the hero -->
	<div class="hero-glow" aria-hidden="true"></div>

	<!-- Greeting card -->
	<div class="relative z-10 flex flex-col items-center">
		<div class="relative">
			<!-- Mini-square peeking out top-left, blue gradient drifting across it -->
			<div class="hero-square absolute w-20 h-20 rounded-[5px] -top-[30px] -left-[30px] z-0" aria-hidden="true"></div>

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
