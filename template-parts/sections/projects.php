<?php
/**
 * Projects section.
 *
 * Projects are grouped into rows of 4. Each row is its own independent
 * carousel: up to 4 cards show at once and the row's > arrow slides that row
 * (on narrower screens fewer show per view, so the arrow scrolls through the
 * row's four). Each card is one `project` CPT post (title + link + images +
 * additional info), entered through the Project Details meta box.
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
<?php
// z-50 lifts the whole section above the fixed "Download CV" tab (z-40), so
// the cards scroll over it instead of under it. The hero is left unlifted, so
// the tab stays on top — and hoverable — there.
?>
<section id="projects" data-section="projects" class="section relative z-50 min-h-screen flex flex-col justify-center p-10 overflow-hidden">

	<!-- Heading -->
	<div class="flex items-center justify-between mb-10">
		<h2 class="text-[42px] font-semibold text-dark flex items-center gap-3">
			<?php esc_html_e( 'My best projects', 'portfolio' ); ?>
			<span class="text-primary">&#10022;</span>
		</h2>
	</div>

	<?php if ( $portfolio_projects->have_posts() ) : ?>

		<?php
		// Split into rows of 4; each row is its own carousel.
		$portfolio_rows = array_chunk( $portfolio_projects->posts, 4 );
		foreach ( $portfolio_rows as $portfolio_row ) :
			?>
			<div class="project-row carousel-row relative mb-8" data-carousel>
				<div class="projects-scroll flex gap-6 overflow-x-auto pb-4 snap-x scroll-smooth" data-carousel-track>
					<?php
					foreach ( $portfolio_row as $portfolio_index => $portfolio_post ) :
						$post = $portfolio_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- scoped loop var for template tags.
						setup_postdata( $post );

						// Pastel tint baked into a class rather than :nth-child, so the
						// clones the infinite carousel appends keep their card's colour.
						$p_tint = 'tint-' . ( $portfolio_index % 3 + 1 );

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
						<article class="project-card <?php echo esc_attr( $p_tint ); ?> snap-start shrink-0 w-[380px] sm:w-[500px] hover:sm:w-[600px] rounded-[5px] p-8 flex flex-col transition-all duration-200">

							<!-- Title -->
							<h3 class="text-[25px] font-light text-dark mb-6"><?php echo esc_html( $p_title ); ?></h3>

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
										class="project-link inline-flex items-center gap-2 text-dark font-normal transition-all">
										<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
										<?php esc_html_e( 'Link to the platform', 'portfolio' ); ?>
									</a>
								<?php endif; ?>
								<?php if ( '' !== trim( (string) $p_info ) ) : ?>
									<div class="text-neutral mt-1 leading-snug"><?php echo nl2br( esc_html( $p_info ) ); ?></div>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<!-- Row slide arrows (rendered only when the row overflows; faded in on hover) -->
				<button type="button" data-carousel-prev
					class="carousel-nav hidden absolute -left-2 top-1/2 -translate-y-1/2 z-20"
					aria-label="<?php esc_attr_e( 'Previous projects', 'portfolio' ); ?>">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
				</button>
				<button type="button" data-carousel-next
					class="carousel-nav hidden absolute -right-2 top-1/2 -translate-y-1/2 z-20"
					aria-label="<?php esc_attr_e( 'Next projects', 'portfolio' ); ?>">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
				</button>
			</div>
		<?php endforeach; ?>

	<?php else : ?>

		<p class="text-neutral text-lg">
			<?php esc_html_e( 'No projects yet. Add one under Projects in the WordPress admin.', 'portfolio' ); ?>
		</p>

	<?php endif; ?>
</section>
<?php
wp_reset_postdata();
