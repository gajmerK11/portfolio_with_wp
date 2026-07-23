<?php
/**
 * Home / hero section.
 *
 * Numbered greeting typed in thin Fira Sans over a blurred white backdrop,
 * navy accent square peeking out top-left, soft animated gradient glow at
 * the bottom, and a "Learn more" scroll cue.
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
			<!-- Navy mini-square peeking out top-left -->
			<div class="absolute bg-primary w-20 h-20 rounded-[5px] -top-[30px] -left-[30px] z-0" aria-hidden="true"></div>

			<div class="hero-card z-10">
				<?php echo portfolio_render_fp_greeting(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
			</div>
		</div>

		<!-- Subtitle under the card -->
		<?php echo portfolio_render_fp_subtitle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
	</div>

	<!-- Learn more scroll cue -->
	<a class="learn-more rise-in absolute bottom-[8%] left-1/2 -translate-x-1/2 z-20" href="#projects" data-scroll-to="projects">
		<span class="text-[17px] text-dark"><?php esc_html_e( 'Learn more', 'portfolio' ); ?></span>
		<span class="lm-arrow" aria-hidden="true">
			<svg class="read-more-arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
		</span>
	</a>
</section>
