<?php
/**
 * "My skills" section.
 *
 * A responsive grid of category cards. Each card is one `skill` CPT post: a
 * rounded-square chip icon beside the category title, then the skills stacked
 * vertically, each with a circular icon. Cards are ordered by menu_order, set
 * by dragging rows on the Skills admin list.
 *
 * Icons are entered at any size/shape; the CSS (.skill-chip / .skill-ico)
 * normalizes them into a uniform square chip and uniform circles so a row of
 * mismatched logos still lines up.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$portfolio_skills = new WP_Query(
	array(
		'post_type'      => 'skill',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $portfolio_skills->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<?php
// z-50 for the same reason as the projects/testimonials sections: content
// passes over the fixed "Download CV" tab rather than under it.
?>
<section id="skills" data-section="skills" class="section relative z-50 py-24 px-10 overflow-hidden">

	<h2 class="relative z-10 text-[42px] font-semibold text-dark flex items-center gap-3 mb-14">
		<?php esc_html_e( 'My skills', 'portfolio' ); ?>
		<span class="text-primary">&#10022;</span>
	</h2>

	<div class="skills-grid relative z-10">
		<?php
		while ( $portfolio_skills->have_posts() ) :
			$portfolio_skills->the_post();

			$sk_id    = get_the_ID();
			$sk_chip  = (int) get_post_meta( $sk_id, '_portfolio_skill_chip', true );
			$sk_items = get_post_meta( $sk_id, '_portfolio_skill_items', true );
			if ( ! is_array( $sk_items ) ) {
				$sk_items = array();
			}
			?>
			<article class="skill-card">

				<!-- Header: chip icon + category name -->
				<div class="skill-head">
					<?php if ( $sk_chip ) : ?>
						<span class="skill-chip">
							<?php
							echo wp_get_attachment_image(
								$sk_chip,
								'thumbnail',
								false,
								array(
									'alt'     => '',
									'loading' => 'lazy',
								)
							);
							?>
						</span>
					<?php endif; ?>
					<h3 class="skill-cat-title"><?php the_title(); ?></h3>
				</div>

				<!-- Skills -->
				<?php if ( $sk_items ) : ?>
					<ul class="skill-list">
						<?php foreach ( $sk_items as $sk_item ) : ?>
							<?php
							$item_icon = isset( $sk_item['icon'] ) ? (int) $sk_item['icon'] : 0;
							$item_name = isset( $sk_item['name'] ) ? $sk_item['name'] : '';
							if ( '' === trim( (string) $item_name ) && ! $item_icon ) {
								continue;
							}
							?>
							<li class="skill-row">
								<span class="skill-ico">
									<?php
									if ( $item_icon ) {
										echo wp_get_attachment_image(
											$item_icon,
											'thumbnail',
											false,
											array(
												'alt'     => '',
												'loading' => 'lazy',
											)
										);
									}
									?>
								</span>
								<span class="skill-name"><?php echo esc_html( $item_name ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php
wp_reset_postdata();
