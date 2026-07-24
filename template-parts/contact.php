<?php
/**
 * Slide-in contact panel.
 *
 * Replicates the reference design's `.contact` drawer: a full-height white
 * panel fixed to the right edge, hidden off-screen and slid in when "Work with
 * me" is clicked, over a dark overlay that closes it. Fields mirror the
 * reference (email, full name, message) and submit over admin-ajax to wp_mail.
 *
 * Contact details: e-mail defaults to the site admin address; both are
 * filterable so no hard-coded personal data lives in the template.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

$cp_email = apply_filters( 'portfolio_contact_email', get_option( 'admin_email' ) );
$cp_phone = apply_filters( 'portfolio_contact_phone', '' );
?>

<!-- Dark overlay: click to close -->
<div class="contact-back" data-contact-close aria-hidden="true"></div>

<!-- The panel itself -->
<aside class="contact-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Contact', 'portfolio' ); ?>" aria-hidden="true">

	<!-- Back -->
	<button type="button" class="cp-back" data-contact-close>
		<span class="cp-back-ico" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
		</span>
		<span class="cp-back-label"><?php esc_html_e( 'Back', 'portfolio' ); ?></span>
	</button>

	<!-- Contacts -->
	<div class="cp-num">
		<h2><?php esc_html_e( 'Contacts', 'portfolio' ); ?></h2>
		<?php if ( $cp_email ) : ?>
			<a href="mailto:<?php echo esc_attr( antispambot( $cp_email ) ); ?>"><h3><?php echo esc_html( antispambot( $cp_email ) ); ?></h3></a>
		<?php endif; ?>
		<?php if ( $cp_phone ) : ?>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $cp_phone ) ); ?>"><h3><?php echo esc_html( $cp_phone ); ?></h3></a>
		<?php endif; ?>
	</div>

	<!-- Form -->
	<form class="cp-form" id="cp-form" novalidate>
		<p class="cp-cour"><?php esc_html_e( 'Your email', 'portfolio' ); ?></p>
		<div class="cp-group">
			<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email', 'portfolio' ); ?>" required>
		</div>

		<p class="cp-cour"><?php esc_html_e( 'Your full name', 'portfolio' ); ?></p>
		<div class="cp-group">
			<input type="text" name="name" placeholder="<?php esc_attr_e( 'Your full name', 'portfolio' ); ?>" required>
		</div>

		<p class="cp-cour"><?php esc_html_e( 'Your message', 'portfolio' ); ?></p>
		<div class="cp-group">
			<textarea name="message" placeholder="<?php esc_attr_e( 'Your message', 'portfolio' ); ?>" required></textarea>
		</div>

		<p class="cp-err" role="alert"></p>
		<p class="cp-succ" role="status"></p>

		<div class="cp-group">
			<button type="submit"><?php esc_html_e( 'Send', 'portfolio' ); ?></button>
		</div>
	</form>
</aside>
