<?php
/**
 * Project meta box.
 *
 * Structure mirrors the cloudcolleague theme meta boxes: a single class owns
 * one nonce, one save handler, and loads the WP media uploader only on its own
 * edit screen. Fields:
 *   - Project Title    (_portfolio_project_title)  also mirrored to post_title
 *   - Project Link     (_portfolio_project_link)
 *   - Additional Info  (_portfolio_project_info)    free text under the card
 *   - Laptop Image     (_portfolio_project_laptop)  single attachment ID
 *   - Other Images     (_portfolio_project_others)  array of attachment IDs
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

class Portfolio_Project_Meta_Box {

	const POST_TYPE = 'project';

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/** Load WP media uploader + hide the native title only on the project edit screen. */
	public function enqueue( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		global $post;
		if ( ! isset( $post ) || self::POST_TYPE !== $post->post_type ) {
			return;
		}
		wp_enqueue_media();
		// The Project Title lives inside the meta box, so hide the native title box.
		wp_add_inline_style( 'wp-admin', '#post-body #titlediv{display:none;}' );
	}

	public function register() {
		add_meta_box(
			'portfolio_project_fields',
			__( 'Project Details', 'portfolio' ),
			array( $this, 'render' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	public function render( $post ) {
		wp_nonce_field( 'portfolio_project_save', 'portfolio_project_nonce' );

		$title  = get_post_meta( $post->ID, '_portfolio_project_title', true );
		$link   = get_post_meta( $post->ID, '_portfolio_project_link', true );
		$info   = get_post_meta( $post->ID, '_portfolio_project_info', true );
		$laptop = (int) get_post_meta( $post->ID, '_portfolio_project_laptop', true );
		$others = get_post_meta( $post->ID, '_portfolio_project_others', true );
		if ( ! is_array( $others ) ) {
			$others = array();
		}
		if ( '' === $title ) {
			$title = $post->post_title;
		}
		$laptop_thumb = $laptop ? wp_get_attachment_image_url( $laptop, 'thumbnail' ) : '';
		?>
		<div class="portfolio-meta-wrap">

			<!-- Project Title -->
			<div class="pf-field">
				<label for="portfolio_project_title"><?php esc_html_e( 'Project Title', 'portfolio' ); ?></label>
				<input type="text" id="portfolio_project_title" name="portfolio_project_title"
					value="<?php echo esc_attr( $title ); ?>"
					placeholder="<?php esc_attr_e( 'e.g. Bykahomes', 'portfolio' ); ?>">
			</div>

			<!-- Project Link -->
			<div class="pf-field">
				<label for="portfolio_project_link"><?php esc_html_e( 'Project Link', 'portfolio' ); ?></label>
				<input type="url" id="portfolio_project_link" name="portfolio_project_link"
					value="<?php echo esc_attr( $link ); ?>"
					placeholder="https://example.com">
			</div>

			<!-- Additional Info -->
			<div class="pf-field">
				<label for="portfolio_project_info"><?php esc_html_e( 'Additional Info', 'portfolio' ); ?></label>
				<textarea id="portfolio_project_info" name="portfolio_project_info"
					placeholder="<?php esc_attr_e( "Web design / Presentation\nLogo and branding", 'portfolio' ); ?>"><?php echo esc_textarea( $info ); ?></textarea>
				<p class="description"><?php esc_html_e( 'One item per line. Shown under the project link.', 'portfolio' ); ?></p>
			</div>

			<!-- Laptop Image (single) -->
			<div class="pf-field">
				<label><?php esc_html_e( 'Laptop Image', 'portfolio' ); ?></label>
				<input type="hidden" id="portfolio_project_laptop" name="portfolio_project_laptop" value="<?php echo esc_attr( $laptop ? $laptop : '' ); ?>">
				<div id="pf-laptop-preview" class="pf-image-preview">
					<?php if ( $laptop_thumb ) : ?>
						<div class="pf-image-item" data-id="<?php echo esc_attr( $laptop ); ?>">
							<img src="<?php echo esc_url( $laptop_thumb ); ?>" alt="">
							<button type="button" class="pf-image-remove" aria-label="<?php esc_attr_e( 'Remove', 'portfolio' ); ?>">&times;</button>
						</div>
					<?php endif; ?>
				</div>
				<button type="button" class="button button-secondary pf-media-add"
					data-target="portfolio_project_laptop" data-preview="pf-laptop-preview" data-multiple="0">
					<?php esc_html_e( '+ Add / Change Laptop Image', 'portfolio' ); ?>
				</button>
				<p class="description"><?php esc_html_e( 'Screenshot shown inside a laptop frame. Optional.', 'portfolio' ); ?></p>
			</div>

			<!-- Other Images (multiple) -->
			<div class="pf-field">
				<label><?php esc_html_e( 'Other Images', 'portfolio' ); ?></label>
				<input type="hidden" id="portfolio_project_others" name="portfolio_project_others"
					value="<?php echo esc_attr( implode( ',', array_map( 'absint', $others ) ) ); ?>">
				<div id="pf-others-preview" class="pf-image-preview">
					<?php foreach ( $others as $img_id ) : ?>
						<?php $thumb = wp_get_attachment_image_url( (int) $img_id, 'thumbnail' ); ?>
						<?php if ( $thumb ) : ?>
							<div class="pf-image-item" data-id="<?php echo esc_attr( (int) $img_id ); ?>">
								<img src="<?php echo esc_url( $thumb ); ?>" alt="">
								<button type="button" class="pf-image-remove" aria-label="<?php esc_attr_e( 'Remove', 'portfolio' ); ?>">&times;</button>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button button-secondary pf-media-add"
					data-target="portfolio_project_others" data-preview="pf-others-preview" data-multiple="1">
					<?php esc_html_e( '+ Add Other Images', 'portfolio' ); ?>
				</button>
				<p class="description"><?php esc_html_e( 'Product shots, mockups, etc. If a laptop image and other images both exist, they auto-slide as a carousel.', 'portfolio' ); ?></p>
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
		.pf-field textarea { min-height:70px; }
		.pf-image-preview { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
		.pf-image-item {
			position:relative; width:90px; height:90px; border:1px solid #e0e0e0; border-radius:6px;
			overflow:hidden; background:#f6f7f7;
		}
		.pf-image-item img { width:100%; height:100%; object-fit:cover; display:block; }
		.pf-image-remove {
			position:absolute; top:2px; right:2px; width:20px; height:20px; line-height:18px;
			border:none; border-radius:50%; background:rgba(0,0,0,.6); color:#fff; cursor:pointer; font-size:14px;
		}
		</style>

		<script>
		( function () {
			var wrap = document.querySelector( '.portfolio-meta-wrap' );
			if ( ! wrap ) return;

			function idsOf( input ) {
				return input.value ? input.value.split( ',' ).filter( Boolean ) : [];
			}
			function makeItem( id, url ) {
				var item = document.createElement( 'div' );
				item.className = 'pf-image-item';
				item.setAttribute( 'data-id', id );
				item.innerHTML = '<img src="' + url + '" alt="">'
					+ '<button type="button" class="pf-image-remove" aria-label="Remove">&times;</button>';
				return item;
			}

			// Open the media frame for whichever button was clicked. wp.media is
			// available by click time (media scripts load in the footer), so it is
			// only referenced inside the handler — never at parse time.
			wrap.addEventListener( 'click', function ( e ) {
				var addBtn = e.target.closest( '.pf-media-add' );
				if ( addBtn ) {
					e.preventDefault();
					if ( typeof wp === 'undefined' || ! wp.media ) return;

					var input    = document.getElementById( addBtn.getAttribute( 'data-target' ) );
					var preview   = document.getElementById( addBtn.getAttribute( 'data-preview' ) );
					var multiple  = addBtn.getAttribute( 'data-multiple' ) === '1';

					var frame = wp.media( {
						title: multiple ? 'Select Images' : 'Select Laptop Image',
						button: { text: 'Use ' + ( multiple ? 'these images' : 'this image' ) },
						multiple: multiple ? 'add' : false
					} );

					frame.on( 'select', function () {
						var sel = frame.state().get( 'selection' ).toJSON();
						if ( ! multiple ) {
							preview.innerHTML = '';
							var att = sel[0];
							var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
							input.value = String( att.id );
							preview.appendChild( makeItem( att.id, url ) );
							return;
						}
						var current = idsOf( input );
						sel.forEach( function ( att ) {
							if ( current.indexOf( String( att.id ) ) !== -1 ) return;
							current.push( String( att.id ) );
							var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
							preview.appendChild( makeItem( att.id, url ) );
						} );
						input.value = current.join( ',' );
					} );

					frame.open();
					return;
				}

				// Remove a preview thumbnail.
				if ( e.target.classList.contains( 'pf-image-remove' ) ) {
					var item    = e.target.closest( '.pf-image-item' );
					var box     = item.closest( '.pf-image-preview' );
					var field   = box.parentNode.querySelector( 'input[type=hidden]' );
					var id      = item.getAttribute( 'data-id' );
					field.value = idsOf( field ).filter( function ( x ) { return x !== id; } ).join( ',' );
					item.remove();
				}
			} );
		} )();
		</script>
		<?php
	}

	public function save( $post_id ) {
		// ---- guards ----
		if ( ! isset( $_POST['portfolio_project_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_project_nonce'] ) ), 'portfolio_project_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// ---- Title (also mirrored to post_title) ----
		$title = isset( $_POST['portfolio_project_title'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_project_title'] ) ) : '';
		$this->save_or_delete( $post_id, '_portfolio_project_title', '' !== $title ? $title : null );

		// ---- Link ----
		$link = isset( $_POST['portfolio_project_link'] ) ? esc_url_raw( wp_unslash( $_POST['portfolio_project_link'] ) ) : '';
		$this->save_or_delete( $post_id, '_portfolio_project_link', '' !== $link ? $link : null );

		// ---- Additional Info ----
		$info = isset( $_POST['portfolio_project_info'] ) ? sanitize_textarea_field( wp_unslash( $_POST['portfolio_project_info'] ) ) : '';
		$this->save_or_delete( $post_id, '_portfolio_project_info', '' !== trim( $info ) ? $info : null );

		// ---- Laptop image (single ID) ----
		$laptop = isset( $_POST['portfolio_project_laptop'] ) ? absint( wp_unslash( $_POST['portfolio_project_laptop'] ) ) : 0;
		$this->save_or_delete( $post_id, '_portfolio_project_laptop', $laptop ? $laptop : null );

		// ---- Other images (CSV of IDs) ----
		$others = array();
		if ( isset( $_POST['portfolio_project_others'] ) && '' !== $_POST['portfolio_project_others'] ) {
			$raw    = explode( ',', sanitize_text_field( wp_unslash( $_POST['portfolio_project_others'] ) ) );
			$others = array_values( array_filter( array_map( 'absint', $raw ) ) );
		}
		$this->save_or_delete( $post_id, '_portfolio_project_others', $others ? $others : null );

		// ---- Mirror the Project Title into the post title (recursion-guarded) ----
		if ( '' !== $title && $title !== get_post_field( 'post_title', $post_id ) ) {
			remove_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ) );
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => $title,
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

new Portfolio_Project_Meta_Box();
