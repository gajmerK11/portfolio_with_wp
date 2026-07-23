<?php
/**
 * Home / hero section.
 *
 * Numbered greeting typed in thin Fira Sans over a blurred white backdrop,
 * gradient accent square peeking out top-left, and a soft animated gradient
 * glow at the bottom.
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

</section>
