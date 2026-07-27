<?php
/**
 * Sidebar render callbacks.
 *
 * Shared by template-parts/sidebar.php and the Customizer partial.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The profile image URL, or the packaged placeholder when none is set.
 *
 * @return string
 */
function portfolio_profile_image_src() {
	$image = get_theme_mod( 'portfolio_sidebar_profile_image', '' );

	return $image ? $image : get_template_directory_uri() . '/assets/img/profile.svg';
}

/**
 * Render the profile image, filling whatever box it is placed in.
 *
 * Rendered twice on the front page: once in the sidebar column and once in the
 * hero, since the sidebar is a floating icon pill below the nav breakpoint and
 * has no room for a photo. Only one is ever visible. The id is therefore
 * optional â€” it is the Customizer partial's selector, so only the sidebar copy
 * carries it and the document keeps a single #sidebar-profile.
 *
 * @param string $id Element id, or '' for none.
 * @return string
 */
function portfolio_render_sidebar_profile( $id = 'sidebar-profile' ) {
	$src = portfolio_profile_image_src();

	ob_start();
	?>
	<div <?php echo $id ? 'id="' . esc_attr( $id ) . '" ' : ''; ?>class="absolute inset-0 rounded-full overflow-hidden border-4 border-white shadow-lg">
		<img
			alt="<?php esc_attr_e( 'Profile picture', 'portfolio' ); ?>"
			class="w-full h-full object-cover"
			src="<?php echo esc_url( $src ); ?>"
		>
	</div>
	<?php
	return ob_get_clean();
}
