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

/**
 * Contact details shown in the panel and used as the mail recipient.
 * Email doubles as the wp_mail destination (see portfolio_contact_submit).
 */
add_filter(
	'portfolio_contact_email',
	function () {
		return 'gajmerk9@gmail.com';
	}
);
add_filter(
	'portfolio_contact_phone',
	function () {
		return '9803280069';
	}
);

/**
 * Route wp_mail through SMTP when credentials are defined in wp-config.php.
 *
 * Local (and stock PHP mail on Windows) does not deliver to real inboxes, so a
 * real send to Gmail needs SMTP. Define these in wp-config.php — NEVER in the
 * theme — using a Gmail App Password (not the account password):
 *
 *   define( 'PORTFOLIO_SMTP_HOST', 'smtp.gmail.com' );
 *   define( 'PORTFOLIO_SMTP_PORT', 587 );
 *   define( 'PORTFOLIO_SMTP_SECURE', 'tls' );
 *   define( 'PORTFOLIO_SMTP_USER', 'gajmerk9@gmail.com' );
 *   define( 'PORTFOLIO_SMTP_PASS', 'your-16-char-app-password' );
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer Mailer instance (by ref).
 */
function portfolio_contact_smtp( $phpmailer ) {
	if ( ! defined( 'PORTFOLIO_SMTP_HOST' ) || ! PORTFOLIO_SMTP_HOST ) {
		return;
	}
	$phpmailer->isSMTP();
	$phpmailer->Host       = PORTFOLIO_SMTP_HOST;
	$phpmailer->Port       = defined( 'PORTFOLIO_SMTP_PORT' ) ? PORTFOLIO_SMTP_PORT : 587;
	$phpmailer->SMTPSecure = defined( 'PORTFOLIO_SMTP_SECURE' ) ? PORTFOLIO_SMTP_SECURE : 'tls';
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = defined( 'PORTFOLIO_SMTP_USER' ) ? PORTFOLIO_SMTP_USER : '';
	$phpmailer->Password   = defined( 'PORTFOLIO_SMTP_PASS' ) ? PORTFOLIO_SMTP_PASS : '';
	// Gmail requires the From address to be the authenticated account.
	$phpmailer->From     = $phpmailer->Username;
	$phpmailer->FromName = get_bloginfo( 'name' );
}
add_action( 'phpmailer_init', 'portfolio_contact_smtp' );
