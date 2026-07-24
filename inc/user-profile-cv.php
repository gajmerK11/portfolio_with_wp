<?php
/**
 * CV upload on the user profile.
 *
 * Adds a "CV / Résumé" media uploader to Users > Profile. The chosen file's
 * attachment ID is stored in user meta, and the front-end "Download CV" tab
 * links to whatever the site owner uploads here.
 *
 * The CV shown on the front end belongs to the same user that feeds the About
 * section (see portfolio_about_user_id), defaulting to the first administrator.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL of the CV to link from the "Download CV" tab.
 *
 * Prefers the file uploaded on the About user's profile, falling back to the
 * legacy Customizer setting so existing sites keep working.
 *
 * @return string CV URL, or '' when none is set.
 */
function portfolio_cv_url() {
	$url = '';

	if ( function_exists( 'portfolio_about_user_id' ) ) {
		$attachment_id = (int) get_user_meta( portfolio_about_user_id(), 'portfolio_cv_id', true );
		if ( $attachment_id ) {
			$maybe = wp_get_attachment_url( $attachment_id );
			if ( $maybe ) {
				$url = $maybe;
			}
		}
	}

	if ( '' === $url ) {
		$url = (string) get_theme_mod( 'portfolio_cv_url', '' );
	}

	return apply_filters( 'portfolio_cv_url', $url );
}

/**
 * Suggested download filename for the CV, e.g. "Jane-Doe-CV.pdf".
 *
 * @return string
 */
function portfolio_cv_download_name() {
	$name = '';

	if ( function_exists( 'portfolio_about_user_id' ) ) {
		$uid           = portfolio_about_user_id();
		$attachment_id = (int) get_user_meta( $uid, 'portfolio_cv_id', true );
		if ( $attachment_id ) {
			$person = get_user_meta( $uid, 'portfolio_about_name', true );
			if ( ! $person ) {
				$user   = get_userdata( $uid );
				$person = $user ? $user->display_name : '';
			}
			$file = get_attached_file( $attachment_id );
			$ext  = $file ? pathinfo( $file, PATHINFO_EXTENSION ) : 'pdf';
			$base = $person ? sanitize_file_name( str_replace( ' ', '-', $person ) . '-CV' ) : 'CV';
			$name = $ext ? $base . '.' . $ext : $base;
		}
	}

	return apply_filters( 'portfolio_cv_download_name', $name );
}

/** Load the WordPress media uploader on the profile screens. */
function portfolio_cv_enqueue_media( $hook ) {
	if ( 'profile.php' !== $hook && 'user-edit.php' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script( 'jquery-core', portfolio_cv_uploader_js() );
}
add_action( 'admin_enqueue_scripts', 'portfolio_cv_enqueue_media' );

/**
 * Inline JS that wires the media frame to the CV fields.
 *
 * @return string
 */
function portfolio_cv_uploader_js() {
	return <<<'JS'
jQuery(function ($) {
	var frame;
	$('#portfolio_cv_upload').on('click', function (e) {
		e.preventDefault();
		if (frame) {
			frame.open();
			return;
		}
		frame = wp.media({
			title: 'Select or upload your CV',
			button: { text: 'Use this file' },
			library: { type: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'] },
			multiple: false
		});
		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			$('#portfolio_cv_id').val(att.id);
			$('#portfolio_cv_filename').text(att.filename || att.url);
			$('#portfolio_cv_link').attr('href', att.url).show();
			$('#portfolio_cv_remove').show();
			$('#portfolio_cv_none').hide();
		});
		frame.open();
	});
	$('#portfolio_cv_remove').on('click', function (e) {
		e.preventDefault();
		$('#portfolio_cv_id').val('');
		$('#portfolio_cv_filename').text('');
		$('#portfolio_cv_link').removeAttr('href').hide();
		$(this).hide();
		$('#portfolio_cv_none').show();
	});
});
JS;
}

/** Render the CV uploader on the profile screen. */
function portfolio_cv_profile_fields( $user ) {
	$attachment_id = (int) get_user_meta( $user->ID, 'portfolio_cv_id', true );
	$file_url      = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
	$file_name     = '';
	if ( $attachment_id ) {
		$path      = get_attached_file( $attachment_id );
		$file_name = $path ? wp_basename( $path ) : $file_url;
	}
	?>
	<h2><?php esc_html_e( 'CV / Resume', 'portfolio' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Upload the CV that the "Download CV" tab on the portfolio links to. PDF recommended.', 'portfolio' ); ?></p>
	<?php wp_nonce_field( 'portfolio_cv_save', 'portfolio_cv_nonce' ); ?>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="portfolio_cv_upload"><?php esc_html_e( 'CV file', 'portfolio' ); ?></label></th>
			<td>
				<input type="hidden" id="portfolio_cv_id" name="portfolio_cv_id" value="<?php echo esc_attr( $attachment_id ); ?>">
				<p>
					<span id="portfolio_cv_none" <?php echo $attachment_id ? 'style="display:none;"' : ''; ?>><em><?php esc_html_e( 'No CV uploaded yet.', 'portfolio' ); ?></em></span>
					<a id="portfolio_cv_link" href="<?php echo esc_url( $file_url ); ?>" target="_blank" rel="noopener noreferrer" <?php echo $attachment_id ? '' : 'style="display:none;"'; ?>>
						<strong id="portfolio_cv_filename"><?php echo esc_html( $file_name ); ?></strong>
					</a>
				</p>
				<p>
					<button type="button" class="button" id="portfolio_cv_upload"><?php esc_html_e( 'Select / Upload CV', 'portfolio' ); ?></button>
					<button type="button" class="button" id="portfolio_cv_remove" <?php echo $attachment_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'portfolio' ); ?></button>
				</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'portfolio_cv_profile_fields' );
add_action( 'edit_user_profile', 'portfolio_cv_profile_fields' );

/** Save the CV attachment ID from the profile screen. */
function portfolio_cv_save_profile( $user_id ) {
	if ( ! isset( $_POST['portfolio_cv_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_cv_nonce'] ) ), 'portfolio_cv_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	$attachment_id = isset( $_POST['portfolio_cv_id'] ) ? absint( $_POST['portfolio_cv_id'] ) : 0;
	if ( $attachment_id && 'attachment' === get_post_type( $attachment_id ) ) {
		update_user_meta( $user_id, 'portfolio_cv_id', $attachment_id );
	} else {
		delete_user_meta( $user_id, 'portfolio_cv_id' );
	}
}
add_action( 'personal_options_update', 'portfolio_cv_save_profile' );
add_action( 'edit_user_profile_update', 'portfolio_cv_save_profile' );
