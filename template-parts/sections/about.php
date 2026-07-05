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
	'label'    => get_user_meta( $portfolio_about_id, 'portfolio_about_link_label', true ),
	'url'      => get_user_meta( $portfolio_about_id, 'portfolio_about_link_url', true ),
	'g1title'  => get_user_meta( $portfolio_about_id, 'portfolio_about_group1_title', true ),
	'g2title'  => get_user_meta( $portfolio_about_id, 'portfolio_about_group2_title', true ),
);
// Fall back to the user's display name when no explicit name is set.
if ( '' === $a['name'] ) {
	$portfolio_about_user = get_userdata( $portfolio_about_id );
	$a['name']            = $portfolio_about_user ? $portfolio_about_user->display_name : '';
}
$portfolio_groups = array(
	array(
		'title' => $a['g1title'],
		'icons' => get_user_meta( $portfolio_about_id, 'portfolio_about_group1_icons', true ),
	),
	array(
		'title' => $a['g2title'],
		'icons' => get_user_meta( $portfolio_about_id, 'portfolio_about_group2_icons', true ),
	),
);
?>
<section id="about" data-section="about" class="section relative min-h-screen flex flex-col justify-center p-10 max-w-4xl">

	<!-- Name + location -->
	<?php if ( $a['name'] ) : ?>
		<h2 class="text-4xl sm:text-5xl font-bold text-dark uppercase tracking-tight"><?php echo esc_html( $a['name'] ); ?></h2>
	<?php endif; ?>
	<?php if ( $a['location'] ) : ?>
		<p class="text-gray-600 mt-2 text-lg"><?php echo esc_html( $a['location'] ); ?></p>
	<?php endif; ?>

	<!-- Block 1 -->
	<?php if ( $a['lead1'] ) : ?>
		<h3 class="text-2xl sm:text-3xl font-medium text-dark mt-10 leading-snug"><?php echo esc_html( $a['lead1'] ); ?></h3>
	<?php endif; ?>
	<?php if ( $a['desc1'] ) : ?>
		<p class="text-gray-600 mt-4 leading-relaxed max-w-2xl"><?php echo nl2br( esc_html( $a['desc1'] ) ); ?></p>
	<?php endif; ?>

	<!-- Block 2 -->
	<?php if ( $a['lead2'] ) : ?>
		<h3 class="text-2xl sm:text-3xl font-medium text-dark mt-10 leading-snug"><?php echo esc_html( $a['lead2'] ); ?></h3>
	<?php endif; ?>
	<?php if ( $a['desc2'] ) : ?>
		<p class="text-gray-600 mt-4 leading-relaxed max-w-2xl"><?php echo nl2br( esc_html( $a['desc2'] ) ); ?></p>
	<?php endif; ?>

	<!-- Journey link -->
	<?php if ( $a['url'] && $a['label'] ) : ?>
		<a href="<?php echo esc_url( $a['url'] ); ?>" target="_blank" rel="noopener noreferrer"
			class="inline-block mt-6 text-dark font-medium underline underline-offset-4 hover:text-primary transition-colors w-fit">
			<?php echo esc_html( $a['label'] ); ?>
		</a>
	<?php endif; ?>

	<!-- Skill groups -->
	<div class="flex flex-wrap gap-12 mt-14">
		<?php
		foreach ( $portfolio_groups as $group ) :
			$icons = is_array( $group['icons'] ) ? $group['icons'] : array();
			if ( ! $group['title'] && ! $icons ) {
				continue;
			}
			?>
			<div>
				<?php if ( $group['title'] ) : ?>
					<h4 class="font-semibold text-dark mb-4">
						<span class="text-neutral">.</span><?php echo esc_html( $group['title'] ); ?>
					</h4>
				<?php endif; ?>
				<div class="flex flex-wrap items-center gap-3">
					<?php foreach ( $icons as $icon_id ) : ?>
						<span class="w-12 h-12 flex items-center justify-center border border-gray-300 rounded-lg p-2">
							<?php
							echo wp_get_attachment_image(
								(int) $icon_id,
								'thumbnail',
								false,
								array(
									'class'   => 'max-w-full max-h-full object-contain',
									'loading' => 'lazy',
									'alt'     => esc_attr( $group['title'] ),
								)
							);
							?>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
