<?php
/**
 * Contact form handler.
 *
 * Receives the slide-in contact panel's submission over admin-ajax and sends it
 * with wp_mail to the site's contact address. Available to logged-in and
 * logged-out visitors; guarded by a nonce and per-field sanitisation.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle a contact-form submission.
 */
function portfolio_contact_submit() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'portfolio_contact' ) ) {
		wp_send_json_error( array( 'message' => __( 'Your session expired. Please refresh and try again.', 'portfolio' ) ) );
	}

	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please fill in every field with a valid email.', 'portfolio' ) ) );
	}

	$to      = apply_filters( 'portfolio_contact_email', get_option( 'admin_email' ) );
	$subject = sprintf(
		/* translators: %s: sender name. */
		__( 'New portfolio message from %s', 'portfolio' ),
		$name
	);
	$body = sprintf(
		"Name: %s\nEmail: %s\n\n%s",
		$name,
		$email,
		$message
	);
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => __( 'Message sent. Thank you!', 'portfolio' ) ) );
	}
	wp_send_json_error( array( 'message' => __( 'The message could not be sent. Please email directly instead.', 'portfolio' ) ) );
}
add_action( 'wp_ajax_portfolio_contact', 'portfolio_contact_submit' );
add_action( 'wp_ajax_nopriv_portfolio_contact', 'portfolio_contact_submit' );
