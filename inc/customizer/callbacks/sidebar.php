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
 * Render the sidebar profile image.
 *
 * @return string
 */
function portfolio_render_sidebar_profile() {
	$image = get_theme_mod( 'portfolio_sidebar_profile_image', '' );
	$src   = $image ? $image : get_template_directory_uri() . '/assets/img/profile.svg';

	ob_start();
	?>
	<div id="sidebar-profile" class="absolute inset-0 rounded-full overflow-hidden border-4 border-white shadow-lg">
		<img
			alt="<?php esc_attr_e( 'Profile picture', 'portfolio' ); ?>"
			class="w-full h-full object-cover"
			src="<?php echo esc_url( $src ); ?>"
		>
	</div>
	<?php
	return ob_get_clean();
}
