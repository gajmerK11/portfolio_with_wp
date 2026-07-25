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
);
// Fall back to the user's display name when no explicit name is set.
if ( '' === $a['name'] ) {
	$portfolio_about_user = get_userdata( $portfolio_about_id );
	$a['name']            = $portfolio_about_user ? $portfolio_about_user->display_name : '';
}
?>
<?php
// Flow from the top (py rhythm shared with skills/experience) rather than
// force a full-screen, vertically centred block — with little content the
// centring left a large empty gap after the Skills section.
?>
<?php
// The width cap sits on an inner wrapper rather than the section, so the
// mascot strip below can span the full content column — out to the divider
// line on the left — while the copy stays at a readable measure.
?>
<section id="about" data-section="about" class="section relative flex flex-col pt-24 pb-12 px-10">
	<div class="flex flex-col max-w-4xl">

	<!-- Name + location -->
	<?php if ( $a['name'] ) : ?>
		<h2 class="text-[48px] font-bold text-dark uppercase tracking-tight"><?php echo esc_html( $a['name'] ); ?></h2>
	<?php endif; ?>
	<?php if ( $a['location'] ) : ?>
		<p class="text-dark mt-1 text-xl"><?php echo esc_html( $a['location'] ); ?></p>
	<?php endif; ?>

	<!-- Block 1 -->
	<?php if ( $a['lead1'] ) : ?>
		<h3 class="text-[32px] font-normal text-dark mt-10 leading-snug"><?php echo esc_html( $a['lead1'] ); ?></h3>
	<?php endif; ?>
	<?php if ( $a['desc1'] ) : ?>
		<p class="text-dark text-[22px] mt-4 leading-relaxed max-w-2xl"><?php echo nl2br( esc_html( $a['desc1'] ) ); ?></p>
	<?php endif; ?>

	<!-- Block 2 -->
	<?php if ( $a['lead2'] ) : ?>
		<h3 class="text-[32px] font-normal text-dark mt-10 leading-snug"><?php echo esc_html( $a['lead2'] ); ?></h3>
	<?php endif; ?>
	<?php if ( $a['desc2'] ) : ?>
		<p class="text-dark text-[22px] mt-4 leading-relaxed max-w-2xl"><?php echo nl2br( esc_html( $a['desc2'] ) ); ?></p>
	<?php endif; ?>

	</div>

	<?php
	// Mascot paces the foot of the section. -mx-10 cancels the section padding
	// so its left edge is the divider line and its right edge the content
	// edge — those are the two walls it turns around at.
	?>
	<div class="-mx-10 mt-24">
		<?php
		get_template_part(
			'template-parts/mascot',
			null,
			array(
				'variant' => 'about',
				'icon'    => 'dj',
				'trail'   => true,
				'setup'   => __( "If you find anything broken, trust me, it's his fault", 'portfolio' ),
			)
		);
		?>
	</div>

</section>
