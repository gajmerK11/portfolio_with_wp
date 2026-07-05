<?php
/**
 * Home / hero section.
 *
 * Mirrors the Stitch design: numbered code-style greeting card, orange
 * accent square, floating background tech icons, "Work with me" CTA,
 * and a "Read more" scroll cue.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<section id="home" data-section="home" class="section relative min-h-screen flex flex-col justify-center items-center p-10 overflow-hidden">

	<!-- Floating tech icons (background) — editable via Customizer > Front Page -->
	<?php echo portfolio_render_fp_icons(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>

	<!-- Top-right CTA -->
	<div class="absolute top-8 right-10 z-20">
		<a class="group bg-primary text-white rounded-[1.75rem] pl-8 pr-7 py-4 font-bold flex items-center gap-4 hover:bg-orange-600 transition-colors shadow-btn-glow" href="#">
			<span class="flex flex-col leading-tight text-left text-xl">
				<span><?php esc_html_e( 'Work', 'portfolio' ); ?></span>
				<span><?php esc_html_e( 'with me', 'portfolio' ); ?></span>
			</span>
			<span class="flex items-center shrink-0" aria-hidden="true">
				<span class="block h-[2px] w-6 rounded bg-current transition-all duration-300 group-hover:w-11"></span>
				<svg class="w-4 h-4 -ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path></svg>
			</span>
		</a>
	</div>

	<!-- Greeting card -->
	<div class="relative z-10 w-full max-w-2xl flex flex-col">
		<div class="relative w-full">
			<!-- Orange mini-card peeking out top-left -->
			<div class="absolute bg-orange-300 w-20 h-20 rounded-2xl -top-8 -left-9 z-0"></div>

			<div class="bg-white rounded-4xl p-10 sm:p-12 w-full relative z-10 border-2 border-gray-200 shadow-code-box">
				<?php echo portfolio_render_fp_greeting(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
			</div>
		</div>

		<!-- Subtitle under the card -->
		<?php echo portfolio_render_fp_subtitle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in callback. ?>
	</div>

	<!-- Read more scroll cue -->
	<div class="absolute bottom-16 left-1/2 -translate-x-1/2 flex items-center gap-4 bg-white pl-7 pr-2 py-2 rounded-full shadow-md z-20">
		<span class="font-medium text-gray-700"><?php esc_html_e( 'Read more', 'portfolio' ); ?></span>
		<div class="bg-primary text-white w-10 h-10 flex items-center justify-center rounded-full shadow-btn-glow cursor-pointer hover:bg-orange-700 transition-colors">
			<svg class="read-more-arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
		</div>
	</div>
</section>
