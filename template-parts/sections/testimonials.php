<?php
/**
 * "They trusted me" testimonials section.
 *
 * Sits directly under the projects section, mirroring the reference design's
 * `.secav` block: a centred heading with a star glyph, then the cards in a
 * wrapping centred row. Each card is one `testimonial` CPT post, entered
 * through the Testimonial Details meta box.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_testimonials = new WP_Query(
	array(
		'post_type'      => 'testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $portfolio_testimonials->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<?php
// z-50 for the same reason as the projects section: the cards pass over the
// fixed "Download CV" tab rather than under it.
?>
<section id="testimonials" class="section relative z-50 pb-10 px-10 overflow-hidden">

	<!-- Soft animated gradient at the bottom, as on the hero -->
	<div class="hero-glow" aria-hidden="true"></div>

	<h2 class="relative z-10 text-center text-[2em] font-semibold text-dark mb-5 flex items-center justify-center gap-3">
		<?php esc_html_e( 'They trusted me', 'portfolio' ); ?>
		<img class="w-[25px]" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/review-star2.svg' ); ?>" alt="">
	</h2>

	<div class="carousel-row relative" data-carousel>
		<div class="reviews projects-scroll" data-carousel-track>
		<?php
		while ( $portfolio_testimonials->have_posts() ) :
			$portfolio_testimonials->the_post();

			$t_id       = get_the_ID();
			$t_name     = get_post_meta( $t_id, '_portfolio_testimonial_name', true );
			$t_name     = ( '' !== $t_name ) ? $t_name : get_the_title();
			$t_role     = get_post_meta( $t_id, '_portfolio_testimonial_role', true );
			$t_text     = get_post_meta( $t_id, '_portfolio_testimonial_text', true );
			$t_linkedin = get_post_meta( $t_id, '_portfolio_testimonial_linkedin', true );
			?>
			<article class="review-card">
				<div class="client-info">
					<h4><?php echo esc_html( $t_name ); ?></h4>
					<?php if ( '' !== trim( (string) $t_role ) ) : ?>
						<p><?php echo esc_html( $t_role ); ?></p>
					<?php endif; ?>
					<?php if ( $t_linkedin ) : ?>
						<a class="client-linkedin" href="<?php echo esc_url( $t_linkedin ); ?>" target="_blank" rel="noopener noreferrer"
							aria-label="<?php echo esc_attr( sprintf( /* translators: %s: person's name. */ __( '%s on LinkedIn', 'portfolio' ), $t_name ) ); ?>">
							<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
						</a>
					<?php endif; ?>
				</div>
				<?php if ( '' !== trim( (string) $t_text ) ) : ?>
					<p class="client-review">&ldquo;<?php echo nl2br( esc_html( $t_text ) ); ?>&rdquo;</p>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
		</div>

		<!-- Slide arrows (rendered only when the row overflows; faded in on hover) -->
		<button type="button" data-carousel-prev
			class="carousel-nav hidden absolute -left-2 top-1/2 -translate-y-1/2 z-20"
			aria-label="<?php esc_attr_e( 'Previous testimonials', 'portfolio' ); ?>">
			<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
		</button>
		<button type="button" data-carousel-next
			class="carousel-nav hidden absolute -right-2 top-1/2 -translate-y-1/2 z-20"
			aria-label="<?php esc_attr_e( 'Next testimonials', 'portfolio' ); ?>">
			<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
		</button>
	</div>
</section>
<?php
wp_reset_postdata();
