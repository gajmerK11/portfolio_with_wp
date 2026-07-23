<?php
/**
 * Testimonial meta box.
 *
 * Mirrors Portfolio_Project_Meta_Box: one nonce, one save handler, native
 * title hidden on this screen. Fields:
 *   - Name        (_portfolio_testimonial_name)      also mirrored to post_title
 *   - Role        (_portfolio_testimonial_role)      e.g. "CEO, Bykaomes"
 *   - Testimonial (_portfolio_testimonial_text)      the quote itself
 *   - LinkedIn    (_portfolio_testimonial_linkedin)  optional profile URL
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

class Portfolio_Testimonial_Meta_Box {

	const POST_TYPE = 'testimonial';

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/** Hide the native title box on the testimonial edit screen only. */
	public function enqueue( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		global $post;
		if ( ! isset( $post ) || self::POST_TYPE !== $post->post_type ) {
			return;
		}
		// The Name field lives inside the meta box, so hide the native title box.
		wp_add_inline_style( 'wp-admin', '#post-body #titlediv{display:none;}' );
	}

	public function register() {
		add_meta_box(
			'portfolio_testimonial_fields',
			__( 'Testimonial Details', 'portfolio' ),
			array( $this, 'render' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	public function render( $post ) {
		wp_nonce_field( 'portfolio_testimonial_save', 'portfolio_testimonial_nonce' );

		$name     = get_post_meta( $post->ID, '_portfolio_testimonial_name', true );
		$role     = get_post_meta( $post->ID, '_portfolio_testimonial_role', true );
		$text     = get_post_meta( $post->ID, '_portfolio_testimonial_text', true );
		$linkedin = get_post_meta( $post->ID, '_portfolio_testimonial_linkedin', true );
		if ( '' === $name ) {
			$name = $post->post_title;
		}
		?>
		<div class="portfolio-meta-wrap">

			<!-- Name -->
			<div class="pf-field">
				<label for="portfolio_testimonial_name"><?php esc_html_e( 'Name of the person', 'portfolio' ); ?></label>
				<input type="text" id="portfolio_testimonial_name" name="portfolio_testimonial_name"
					value="<?php echo esc_attr( $name ); ?>"
					placeholder="<?php esc_attr_e( 'e.g. Pierre', 'portfolio' ); ?>">
			</div>

			<!-- Role -->
			<div class="pf-field">
				<label for="portfolio_testimonial_role"><?php esc_html_e( 'Role', 'portfolio' ); ?></label>
				<input type="text" id="portfolio_testimonial_role" name="portfolio_testimonial_role"
					value="<?php echo esc_attr( $role ); ?>"
					placeholder="<?php esc_attr_e( 'e.g. CEO, Bykaomes', 'portfolio' ); ?>">
			</div>

			<!-- Testimonial -->
			<div class="pf-field">
				<label for="portfolio_testimonial_text"><?php esc_html_e( 'Testimonial', 'portfolio' ); ?></label>
				<textarea id="portfolio_testimonial_text" name="portfolio_testimonial_text"
					placeholder="<?php esc_attr_e( 'What the client said…', 'portfolio' ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Quotation marks are added by the template.', 'portfolio' ); ?></p>
			</div>

			<!-- LinkedIn URL -->
			<div class="pf-field">
				<label for="portfolio_testimonial_linkedin"><?php esc_html_e( 'LinkedIn URL', 'portfolio' ); ?></label>
				<input type="url" id="portfolio_testimonial_linkedin" name="portfolio_testimonial_linkedin"
					value="<?php echo esc_attr( $linkedin ); ?>"
					placeholder="https://www.linkedin.com/in/…">
				<p class="description"><?php esc_html_e( 'Optional. Adds a LinkedIn link to the card.', 'portfolio' ); ?></p>
			</div>

		</div>

		<style>
		.portfolio-meta-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
		.pf-field { margin-bottom: 18px; }
		.pf-field > label { display:block; font-weight:600; margin-bottom:6px; color:#1d2327; }
		.pf-field input[type=text],
		.pf-field input[type=url],
		.pf-field textarea {
			width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:4px; box-sizing:border-box;
		}
		.pf-field textarea { min-height:110px; }
		</style>
		<?php
	}

	public function save( $post_id ) {
		// ---- guards ----
		if ( ! isset( $_POST['portfolio_testimonial_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_testimonial_nonce'] ) ), 'portfolio_testimonial_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// ---- Name (also mirrored to post_title) ----
		$name = isset( $_POST['portfolio_testimonial_name'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_testimonial_name'] ) ) : '';
		$this->save_or_delete( $post_id, '_portfolio_testimonial_name', '' !== $name ? $name : null );

		// ---- Role ----
		$role = isset( $_POST['portfolio_testimonial_role'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_testimonial_role'] ) ) : '';
		$this->save_or_delete( $post_id, '_portfolio_testimonial_role', '' !== $role ? $role : null );

		// ---- Testimonial ----
		$text = isset( $_POST['portfolio_testimonial_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['portfolio_testimonial_text'] ) ) : '';
		$this->save_or_delete( $post_id, '_portfolio_testimonial_text', '' !== trim( $text ) ? $text : null );

		// ---- LinkedIn URL ----
		$linkedin = isset( $_POST['portfolio_testimonial_linkedin'] ) ? esc_url_raw( wp_unslash( $_POST['portfolio_testimonial_linkedin'] ) ) : '';
		$this->save_or_delete( $post_id, '_portfolio_testimonial_linkedin', '' !== $linkedin ? $linkedin : null );

		// ---- Mirror the Name into the post title (recursion-guarded) ----
		if ( '' !== $name && $name !== get_post_field( 'post_title', $post_id ) ) {
			remove_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ) );
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => $name,
				)
			);
			add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ) );
		}
	}

	/** Update the meta when $value is non-null, otherwise delete it. */
	private function save_or_delete( $post_id, $key, $value ) {
		if ( null === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

new Portfolio_Testimonial_Meta_Box();
