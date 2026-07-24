<?php
/**
 * Skill category meta box.
 *
 * Combines the two admin patterns already in this theme: the wp.media image
 * picker from the Project meta box, and the jquery-ui-sortable repeater from
 * the Experience screen. Fields:
 *   - Category name  (post_title, native title hidden — relabelled here)
 *   - Chip icon      (_portfolio_skill_chip)   single attachment ID, the
 *                    rounded-square icon beside the category title
 *   - Skills         (_portfolio_skill_items)  ordered array of
 *                    [ 'icon' => attachment ID, 'name' => 'React.js' ]
 *
 * Each skill row carries its own icon uploader and name field, and the rows are
 * drag-sortable; DOM order at submit is the saved order.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

class Portfolio_Skill_Meta_Box {

	const POST_TYPE = 'skill';

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/** Load media + sortable and hide the native title, on this screen only. */
	public function enqueue( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		global $post;
		if ( ! isset( $post ) || self::POST_TYPE !== $post->post_type ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		// Category name lives in the meta box, so hide the native title box.
		wp_add_inline_style( 'wp-admin', '#post-body #titlediv{display:none;}' );
	}

	public function register() {
		add_meta_box(
			'portfolio_skill_fields',
			__( 'Skill Category Details', 'portfolio' ),
			array( $this, 'render' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render one skill row. Also the "add skill" prototype, with __INDEX__ as a
	 * placeholder the JS swaps for a fresh index.
	 *
	 * @param string $index Row index, or __INDEX__ for the prototype.
	 * @param array  $item  [ 'icon' => int, 'name' => string ].
	 */
	private function skill_row( $index, $item = array() ) {
		$item  = wp_parse_args( $item, array( 'icon' => 0, 'name' => '' ) );
		$icon  = (int) $item['icon'];
		$thumb = $icon ? wp_get_attachment_image_url( $icon, 'thumbnail' ) : '';
		$base  = 'portfolio_skill_items[' . $index . ']';
		?>
		<li class="pf-skill-row">
			<span class="pf-skill-handle dashicons dashicons-menu" aria-hidden="true"></span>

			<div class="pf-skill-icon">
				<input type="hidden" class="pf-skill-icon-input" name="<?php echo esc_attr( $base ); ?>[icon]" value="<?php echo esc_attr( $icon ? $icon : '' ); ?>">
				<button type="button" class="pf-skill-icon-btn" aria-label="<?php esc_attr_e( 'Choose icon', 'portfolio' ); ?>">
					<?php if ( $thumb ) : ?>
						<img src="<?php echo esc_url( $thumb ); ?>" alt="">
					<?php else : ?>
						<span class="dashicons dashicons-plus"></span>
					<?php endif; ?>
				</button>
			</div>

			<input type="text" class="pf-skill-name" name="<?php echo esc_attr( $base ); ?>[name]"
				value="<?php echo esc_attr( $item['name'] ); ?>"
				placeholder="<?php esc_attr_e( 'e.g. React.js', 'portfolio' ); ?>">

			<button type="button" class="button-link delete pf-skill-remove" aria-label="<?php esc_attr_e( 'Remove skill', 'portfolio' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</li>
		<?php
	}

	public function render( $post ) {
		wp_nonce_field( 'portfolio_skill_save', 'portfolio_skill_nonce' );

		$title = $post->post_title;
		$chip  = (int) get_post_meta( $post->ID, '_portfolio_skill_chip', true );
		$items = get_post_meta( $post->ID, '_portfolio_skill_items', true );
		if ( ! is_array( $items ) ) {
			$items = array();
		}
		$chip_thumb = $chip ? wp_get_attachment_image_url( $chip, 'thumbnail' ) : '';
		?>
		<div class="portfolio-meta-wrap pf-skill-wrap">

			<!-- Category name -->
			<div class="pf-field">
				<label for="portfolio_skill_title"><?php esc_html_e( 'Category name', 'portfolio' ); ?></label>
				<input type="text" id="portfolio_skill_title" name="portfolio_skill_title"
					value="<?php echo esc_attr( $title ); ?>"
					placeholder="<?php esc_attr_e( 'e.g. Frontend', 'portfolio' ); ?>">
			</div>

			<!-- Chip icon -->
			<div class="pf-field">
				<label><?php esc_html_e( 'Category icon', 'portfolio' ); ?></label>
				<input type="hidden" id="portfolio_skill_chip" name="portfolio_skill_chip" value="<?php echo esc_attr( $chip ? $chip : '' ); ?>">
				<button type="button" class="pf-chip-btn" id="portfolio_skill_chip_btn">
					<?php if ( $chip_thumb ) : ?>
						<img src="<?php echo esc_url( $chip_thumb ); ?>" alt="">
					<?php else : ?>
						<span class="dashicons dashicons-plus"></span>
					<?php endif; ?>
				</button>
				<p class="description"><?php esc_html_e( 'The rounded-square icon shown beside the category title.', 'portfolio' ); ?></p>
			</div>

			<!-- Skills -->
			<div class="pf-field">
				<label><?php esc_html_e( 'Skills', 'portfolio' ); ?></label>
				<ul id="pf-skill-rows">
					<?php
					foreach ( $items as $i => $item ) {
						$this->skill_row( (string) $i, $item );
					}
					?>
				</ul>
				<button type="button" class="button button-secondary" id="pf-skill-add">
					<?php esc_html_e( '+ Add Skill', 'portfolio' ); ?>
				</button>
				<p class="description"><?php esc_html_e( 'Each skill gets a circular icon and a name. Drag the handle to reorder.', 'portfolio' ); ?></p>
			</div>

		</div>

		<script type="text/html" id="pf-skill-template">
			<?php $this->skill_row( '__INDEX__' ); ?>
		</script>

		<style>
		.portfolio-meta-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
		.pf-field { margin-bottom: 18px; }
		.pf-field > label { display:block; font-weight:600; margin-bottom:6px; color:#1d2327; }
		.pf-field input[type=text] { width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:4px; box-sizing:border-box; }
		/* Chip icon button (category). */
		.pf-chip-btn {
			width:56px; height:56px; border:1px dashed #c3c4c7; border-radius:10px; background:#f6f7f7;
			cursor:pointer; padding:0; display:grid; place-items:center; overflow:hidden;
		}
		.pf-chip-btn img { width:100%; height:100%; object-fit:contain; }
		.pf-chip-btn .dashicons { color:#787c82; }
		/* Skill rows. */
		#pf-skill-rows { margin:0 0 12px; max-width:520px; }
		.pf-skill-row {
			display:flex; align-items:center; gap:10px; background:#fff;
			border:1px solid #e0e0e0; border-radius:6px; padding:8px 10px; margin-bottom:8px;
		}
		.pf-skill-placeholder { border:1px dashed #c3c4c7; border-radius:6px; background:#f6f7f7; margin-bottom:8px; height:56px; }
		.pf-skill-handle { cursor:move; color:#787c82; }
		.pf-skill-icon-btn {
			width:40px; height:40px; border:1px solid #ddd; border-radius:50%; background:#f6f7f7;
			cursor:pointer; padding:0; display:grid; place-items:center; overflow:hidden; flex:0 0 auto;
		}
		.pf-skill-icon-btn img { width:100%; height:100%; object-fit:contain; }
		.pf-skill-icon-btn .dashicons { color:#787c82; }
		.pf-skill-name { flex:1; }
		.pf-skill-remove { color:#b32d2e; cursor:pointer; }
		.pf-skill-remove .dashicons { line-height:1.4; }
		</style>

		<script>
		( function () {
			var wrap = document.querySelector( '.pf-skill-wrap' );
			if ( ! wrap ) { return; }

			// Open the media frame and route the picked attachment into the
			// hidden input + button preview passed in. wp.media is footer-loaded,
			// so it is only referenced inside handlers.
			function pickIcon( input, button ) {
				if ( typeof wp === 'undefined' || ! wp.media ) { return; }
				var frame = wp.media( {
					title: 'Select Icon',
					button: { text: 'Use this icon' },
					library: { type: 'image' },
					multiple: false
				} );
				frame.on( 'select', function () {
					var att = frame.state().get( 'selection' ).first().toJSON();
					var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
					input.value = String( att.id );
					button.innerHTML = '<img src="' + url + '" alt="">';
				} );
				frame.open();
			}

			// Category chip.
			var chipBtn   = document.getElementById( 'portfolio_skill_chip_btn' );
			var chipInput = document.getElementById( 'portfolio_skill_chip' );
			chipBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				pickIcon( chipInput, chipBtn );
			} );

			// Skill rows: delegated clicks for icon picker + remove.
			var rows = document.getElementById( 'pf-skill-rows' );
			rows.addEventListener( 'click', function ( e ) {
				var iconBtn = e.target.closest( '.pf-skill-icon-btn' );
				if ( iconBtn ) {
					e.preventDefault();
					var input = iconBtn.parentNode.querySelector( '.pf-skill-icon-input' );
					pickIcon( input, iconBtn );
					return;
				}
				var rm = e.target.closest( '.pf-skill-remove' );
				if ( rm ) {
					e.preventDefault();
					rm.closest( '.pf-skill-row' ).remove();
				}
			} );

			// Add a new row from the prototype. Indices only need to be unique;
			// order comes from the DOM on submit and PHP re-indexes on save.
			var nextIndex = <?php echo (int) ( count( $items ) + 1 ); ?>;
			document.getElementById( 'pf-skill-add' ).addEventListener( 'click', function () {
				var html = document.getElementById( 'pf-skill-template' ).innerHTML.replace( /__INDEX__/g, String( nextIndex++ ) );
				rows.insertAdjacentHTML( 'beforeend', html );
			} );

			// Drag to reorder.
			jQuery( rows ).sortable( {
				handle: '.pf-skill-handle',
				placeholder: 'pf-skill-placeholder',
				forcePlaceholderSize: true,
				axis: 'y'
			} );
		} )();
		</script>
		<?php
	}

	public function save( $post_id ) {
		// ---- guards ----
		if ( ! isset( $_POST['portfolio_skill_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['portfolio_skill_nonce'] ) ), 'portfolio_skill_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// ---- Category name -> post_title ----
		$title = isset( $_POST['portfolio_skill_title'] ) ? sanitize_text_field( wp_unslash( $_POST['portfolio_skill_title'] ) ) : '';

		// ---- Chip icon ----
		$chip = isset( $_POST['portfolio_skill_chip'] ) ? absint( wp_unslash( $_POST['portfolio_skill_chip'] ) ) : 0;
		$this->save_or_delete( $post_id, '_portfolio_skill_chip', $chip ? $chip : null );

		// ---- Skills (ordered; DOM order preserved by array_values) ----
		$clean = array();
		$raw   = isset( $_POST['portfolio_skill_items'] ) ? wp_unslash( $_POST['portfolio_skill_items'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
		if ( is_array( $raw ) ) {
			foreach ( $raw as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$name = isset( $row['name'] ) ? sanitize_text_field( $row['name'] ) : '';
				$icon = isset( $row['icon'] ) ? absint( $row['icon'] ) : 0;
				// A row with neither a name nor an icon is a leftover blank.
				if ( '' === $name && 0 === $icon ) {
					continue;
				}
				$clean[] = array(
					'icon' => $icon,
					'name' => $name,
				);
			}
		}
		$this->save_or_delete( $post_id, '_portfolio_skill_items', $clean ? array_values( $clean ) : null );

		// ---- Mirror the Category name into the post title (recursion-guarded) ----
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

new Portfolio_Skill_Meta_Box();
