<?php
/**
 * Projects section.
 *
 * Horizontal, snap-scrolling row of project cards. Each card is one `project`
 * CPT post (title + link + images), entered through the Project Details meta
 * box. Layout mirrors the reference design: gradient card, dot-prefixed title,
 * screenshot(s), and a footer link. A round arrow button scrolls the track.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_projects = new WP_Query(
	array(
		'post_type'      => 'project',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
?>
<section id="projects" data-section="projects" class="section relative min-h-screen flex flex-col justify-center p-10 overflow-hidden">

	<!-- Heading -->
	<div class="flex items-center justify-between mb-10">
		<h2 class="text-4xl sm:text-5xl font-bold text-dark flex items-center gap-3">
			<?php esc_html_e( 'My best projects', 'portfolio' ); ?>
			<span class="text-primary">&#10022;</span>
		</h2>
	</div>

	<?php if ( $portfolio_projects->have_posts() ) : ?>

		<div class="relative">
			<!-- Scroll track -->
			<div id="projects-track" class="projects-scroll flex gap-6 overflow-x-auto pb-4 snap-x scroll-smooth">
				<?php
				while ( $portfolio_projects->have_posts() ) :
					$portfolio_projects->the_post();

					$p_title  = get_post_meta( get_the_ID(), '_portfolio_project_title', true );
					$p_title  = ( '' !== $p_title ) ? $p_title : get_the_title();
					$p_link   = get_post_meta( get_the_ID(), '_portfolio_project_link', true );
					$p_info   = get_post_meta( get_the_ID(), '_portfolio_project_info', true );
					$p_laptop = (int) get_post_meta( get_the_ID(), '_portfolio_project_laptop', true );
					$p_others = get_post_meta( get_the_ID(), '_portfolio_project_others', true );
					if ( ! is_array( $p_others ) ) {
						$p_others = array();
					}

					// Ordered slides: laptop first, then the other images.
					$slides = array();
					if ( $p_laptop ) {
						$slides[] = array(
							'id'     => $p_laptop,
							'laptop' => true,
						);
					}
					foreach ( $p_others as $oid ) {
						$slides[] = array(
							'id'     => (int) $oid,
							'laptop' => false,
						);
					}
					$slide_count = count( $slides );
					?>
					<article class="project-card snap-start shrink-0 w-[380px] sm:w-[560px] rounded-4xl border border-gray-100 shadow-code-box p-8 flex flex-col">

						<!-- Title -->
						<h3 class="text-2xl font-bold text-dark mb-6"><?php echo esc_html( $p_title ); ?></h3>

						<!-- Media: single image, or a horizontal sliding carousel for 2+ slides -->
						<div class="flex-1 mb-6 flex items-center">
							<?php if ( $slide_count ) : ?>
								<div class="project-carousel relative overflow-hidden w-full h-80 sm:h-96" data-slides="<?php echo esc_attr( $slide_count ); ?>">
									<div class="pc-track flex h-full">
										<?php foreach ( $slides as $slide ) : ?>
											<div class="pc-slide">
												<?php if ( $slide['laptop'] ) : ?>
													<div class="laptop-mock">
														<div class="laptop-screen-frame">
															<?php
															echo wp_get_attachment_image(
																$slide['id'],
																'large',
																false,
																array(
																	'class'   => 'laptop-screen',
																	'loading' => 'lazy',
																	'alt'     => esc_attr( $p_title ),
																)
															);
															?>
														</div>
														<div class="laptop-base"></div>
													</div>
												<?php else : ?>
													<?php
													echo wp_get_attachment_image(
														$slide['id'],
														'large',
														false,
														array(
															'class'   => 'max-h-64 w-auto object-contain rounded-lg',
															'loading' => 'lazy',
															'alt'     => esc_attr( $p_title ),
														)
													);
													?>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php else : ?>
								<div class="w-full h-80 sm:h-96 rounded-xl bg-white/60 flex items-center justify-center text-neutral text-sm">
									<?php esc_html_e( 'No images yet', 'portfolio' ); ?>
								</div>
							<?php endif; ?>
						</div>

						<!-- Footer -->
						<div class="mt-auto">
							<?php if ( $p_link ) : ?>
								<a href="<?php echo esc_url( $p_link ); ?>" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 text-dark font-medium hover:text-primary transition-colors">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
									<?php esc_html_e( 'Link to the platform', 'portfolio' ); ?>
								</a>
							<?php endif; ?>
							<?php if ( '' !== trim( (string) $p_info ) ) : ?>
								<div class="text-neutral mt-1 leading-snug"><?php echo nl2br( esc_html( $p_info ) ); ?></div>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<!-- Scroll-next arrow -->
			<button type="button" id="projects-next"
				class="absolute -right-2 top-1/2 -translate-y-1/2 z-20 bg-dark text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:bg-primary transition-colors"
				aria-label="<?php esc_attr_e( 'Next projects', 'portfolio' ); ?>">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
			</button>
		</div>

	<?php else : ?>

		<p class="text-neutral text-lg">
			<?php esc_html_e( 'No projects yet. Add one under Projects in the WordPress admin.', 'portfolio' ); ?>
		</p>

	<?php endif; ?>
</section>
<?php
wp_reset_postdata();
