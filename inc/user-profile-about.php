<?php
/**
 * About Me content on the user profile.
 *
 * The About section describes the site owner, so its content lives on a user
 * (Users > Profile) instead of a CPT or a public Page — no /about URL, no meta
 * box leaking onto other screens. The front section reads from the designated
 * "About user" (Customizer > About Me), defaulting to the first administrator.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID of the user whose profile feeds the About section.
 *
 * @return int
 */
function portfolio_about_user_id() {
	$id = (int) get_theme_mod( 'portfolio_about_user', 0 );
	if ( ! $id ) {
		$admins = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
				'fields' => 'ID',
			)
		);
		$id = $admins ? (int) $admins[0] : 0;
	}
	return (int) apply_filters( 'portfolio_about_user_id', $id );
}

/** Render the About Me fields on the profile screen. */
function portfolio_about_profile_fields( $user ) {
	$uid = $user->ID;

	$name     = get_user_meta( $uid, 'portfolio_about_name', true );
	$location = get_user_meta( $uid, 'portfolio_about_location', true );
	$lead1    = get_user_meta( $uid, 'portfolio_about_lead1', true );
	$desc1    = get_user_meta( $uid, 'portfolio_about_desc1', true );
	$lead2    = get_user_meta( $uid, 'portfolio_about_lead2', true );
	$desc2    = get_user_meta( $uid, 'portfolio_about_desc2', true );
	?>
	<h2><?php esc_html_e( 'About Me Section', 'portfolio' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Content for the About Me section on the portfolio front page.', 'portfolio' ); ?></p>
	<?php wp_nonce_field( 'portfolio_about_save', 'portfolio_about_nonce' ); ?>
	<table class="form-table portfolio-about-fields" role="presentation">
		<tr>
			<th><label for="portfolio_about_name"><?php esc_html_e( 'Name', 'portfolio' ); ?></label></th>
			<td><input type="text" id="portfolio_about_name" name="portfolio_about_name" value="<?php echo esc_attr( $name ); ?>" class="regular-text" placeholder="e.g. Brice Clain"></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_location"><?php esc_html_e( 'Location', 'portfolio' ); ?></label></th>
			<td><input type="text" id="portfolio_about_location" name="portfolio_about_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" placeholder="e.g. Trois-Rivières (Quebec)"></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_lead1"><?php esc_html_e( 'Lead heading 1', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_lead1" name="portfolio_about_lead1" rows="2" class="large-text" placeholder="I design and develop websites…"><?php echo esc_textarea( $lead1 ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_desc1"><?php esc_html_e( 'Description 1', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_desc1" name="portfolio_about_desc1" rows="3" class="large-text"><?php echo esc_textarea( $desc1 ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_lead2"><?php esc_html_e( 'Lead heading 2', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_lead2" name="portfolio_about_lead2" rows="2" class="large-text" placeholder="I also specialize in creating your brand image…"><?php echo esc_textarea( $lead2 ); ?></textarea></td>
		</tr>
		<tr>
			<th><label for="portfolio_about_desc2"><?php esc_html_e( 'Description 2', 'portfolio' ); ?></label></th>
			<td><textarea id="portfolio_about_desc2" name="portfolio_about_desc2" rows="3" class="large-text"><?php echo esc_textarea( $desc2 ); ?></textarea></td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'portfolio_about_profile_fields' );
add_action( 'edit_user_profile', 'portfolio_about_profile_fields' );

/** Save the About Me profile fields. */
function portfolio_about_save_profile( $user_id ) {
	if ( ! isset( $_POST['portfolio_about_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_about_nonce'] ) ), 'portfolio_about_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	$text = array( 'portfolio_about_name', 'portfolio_about_location' );
	foreach ( $text as $key ) {
		portfolio_about_save_meta( $user_id, $key, isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '' );
	}

	$area = array( 'portfolio_about_lead1', 'portfolio_about_desc1', 'portfolio_about_lead2', 'portfolio_about_desc2' );
	foreach ( $area as $key ) {
		portfolio_about_save_meta( $user_id, $key, isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '' );
	}
}
add_action( 'personal_options_update', 'portfolio_about_save_profile' );
add_action( 'edit_user_profile_update', 'portfolio_about_save_profile' );

/** Update the meta when non-empty, otherwise delete it. */
function portfolio_about_save_meta( $user_id, $key, $value ) {
	if ( '' !== trim( (string) $value ) ) {
		update_user_meta( $user_id, $key, $value );
	} else {
		delete_user_meta( $user_id, $key );
	}
}
