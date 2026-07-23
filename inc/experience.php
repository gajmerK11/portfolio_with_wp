<?php
/**
 * Experience entries — admin screen + data access.
 *
 * Deliberately not a CPT: an entry is four short fields, and the only thing
 * that matters beyond the text is the order. So the whole list lives in one
 * option as an ordered array, edited on a single screen with drag-sortable
 * rows (jquery-ui-sortable, the same library the Menus screen uses). Array
 * order is display order — there is no separate "order" field to keep in sync.
 *
 * Shape of the option:
 *   [
 *     [ 'label' => '2021 — Present', 'company' => 'Nakshatra Technohub',
 *       'role'  => 'Senior Backend Developer', 'bullets' => "line\nline" ],
 *     …
 *   ]
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

const PORTFOLIO_EXPERIENCE_OPTION = 'portfolio_experience';

/**
 * Read the entries.
 *
 * @return array List of entries, each with label/company/role/bullets.
 */
function portfolio_get_experience() {
	$rows = get_option( PORTFOLIO_EXPERIENCE_OPTION, array() );
	return is_array( $rows ) ? $rows : array();
}

/**
 * Split an entry's bullets textarea into individual lines.
 *
 * @param string $bullets Raw textarea value.
 * @return array Non-empty lines.
 */
function portfolio_experience_bullets( $bullets ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $bullets );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Register the admin menu.
 */
function portfolio_experience_menu() {
	add_menu_page(
		__( 'Experience', 'portfolio' ),
		__( 'Experience', 'portfolio' ),
		'manage_options',
		'portfolio-experience',
		'portfolio_experience_screen',
		'dashicons-clock',
		26
	);
}
add_action( 'admin_menu', 'portfolio_experience_menu' );

/**
 * Load the drag-and-drop dependency on this screen only.
 *
 * @param string $hook Current admin page.
 */
function portfolio_experience_admin_assets( $hook ) {
	if ( 'toplevel_page_portfolio-experience' !== $hook ) {
		return;
	}
	wp_enqueue_script( 'jquery-ui-sortable' );
}
add_action( 'admin_enqueue_scripts', 'portfolio_experience_admin_assets' );

/**
 * Render one editable row.
 *
 * Also used as the "add new" prototype, with __INDEX__ as a placeholder that
 * the JS swaps for a fresh index.
 *
 * @param string $index Row index, or __INDEX__ for the prototype.
 * @param array  $row   Row values.
 * @param bool   $open  Whether the row starts expanded.
 */
function portfolio_experience_row( $index, $row = array(), $open = false ) {
	$row = wp_parse_args(
		$row,
		array(
			'label'   => '',
			'company' => '',
			'role'    => '',
			'bullets' => '',
		)
	);

	$name = PORTFOLIO_EXPERIENCE_OPTION . '[' . $index . ']';
	?>
	<li class="pf-exp-row<?php echo $open ? '' : ' is-collapsed'; ?>">
		<div class="pf-exp-head">
			<span class="pf-exp-handle dashicons dashicons-menu" aria-hidden="true"></span>
			<span class="pf-exp-title">
				<span class="pf-exp-title-label"><?php echo esc_html( $row['label'] ); ?></span>
				<span class="pf-exp-title-sep"><?php echo $row['label'] && $row['company'] ? '&middot;' : ''; ?></span>
				<span class="pf-exp-title-company"><?php echo esc_html( $row['company'] ); ?></span>
			</span>
			<button type="button" class="button-link pf-exp-toggle" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle entry', 'portfolio' ); ?></span>
				<span class="dashicons dashicons-arrow-down" aria-hidden="true"></span>
			</button>
		</div>

		<div class="pf-exp-body">
			<p class="pf-exp-field">
				<label><?php esc_html_e( 'Date label', 'portfolio' ); ?></label>
				<input type="text" name="<?php echo esc_attr( $name ); ?>[label]" value="<?php echo esc_attr( $row['label'] ); ?>"
					placeholder="<?php esc_attr_e( '2021 — Present', 'portfolio' ); ?>" data-title-part="label">
			</p>
			<p class="pf-exp-field">
				<label><?php esc_html_e( 'Company', 'portfolio' ); ?></label>
				<input type="text" name="<?php echo esc_attr( $name ); ?>[company]" value="<?php echo esc_attr( $row['company'] ); ?>"
					placeholder="<?php esc_attr_e( 'Nakshatra Technohub', 'portfolio' ); ?>" data-title-part="company">
			</p>
			<p class="pf-exp-field">
				<label><?php esc_html_e( 'Role', 'portfolio' ); ?></label>
				<input type="text" name="<?php echo esc_attr( $name ); ?>[role]" value="<?php echo esc_attr( $row['role'] ); ?>"
					placeholder="<?php esc_attr_e( 'Senior Backend Developer', 'portfolio' ); ?>">
			</p>
			<p class="pf-exp-field">
				<label><?php esc_html_e( 'Bullets', 'portfolio' ); ?></label>
				<textarea name="<?php echo esc_attr( $name ); ?>[bullets]" rows="4"
					placeholder="<?php esc_attr_e( "Built custom WordPress themes\nOwned SEO and performance work", 'portfolio' ); ?>"><?php echo esc_textarea( $row['bullets'] ); ?></textarea>
				<span class="description"><?php esc_html_e( 'One bullet per line.', 'portfolio' ); ?></span>
			</p>
			<p class="pf-exp-actions">
				<button type="button" class="button-link delete pf-exp-remove"><?php esc_html_e( 'Remove entry', 'portfolio' ); ?></button>
			</p>
		</div>
	</li>
	<?php
}

/**
 * Render the admin screen.
 */
function portfolio_experience_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$rows = portfolio_get_experience();
	?>
	<div class="wrap pf-exp-wrap">
		<h1><?php esc_html_e( 'Experience', 'portfolio' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Entries shown in the "Where I\'ve worked" section on the front page. Drag to reorder — the top entry is treated as the current role.', 'portfolio' ); ?>
		</p>

		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only notice flag. ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Experience saved.', 'portfolio' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="portfolio_save_experience">
			<?php wp_nonce_field( 'portfolio_save_experience', 'portfolio_experience_nonce' ); ?>

			<ul id="pf-exp-rows">
				<?php
				foreach ( $rows as $i => $row ) {
					portfolio_experience_row( (string) $i, $row );
				}
				?>
			</ul>

			<p>
				<button type="button" class="button button-secondary" id="pf-exp-add">
					<?php esc_html_e( '+ Add Entry', 'portfolio' ); ?>
				</button>
			</p>

			<?php submit_button( __( 'Save Experience', 'portfolio' ) ); ?>
		</form>

		<script type="text/html" id="pf-exp-template">
			<?php portfolio_experience_row( '__INDEX__', array(), true ); ?>
		</script>
	</div>

	<style>
	.pf-exp-wrap #pf-exp-rows { margin: 20px 0; max-width: 720px; }
	.pf-exp-row {
		background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; margin-bottom: 8px;
	}
	.pf-exp-row.is-sorting { opacity: .7; }
	.pf-exp-placeholder { border: 1px dashed #c3c4c7; border-radius: 4px; background: #f6f7f7; margin-bottom: 8px; }
	.pf-exp-head { display: flex; align-items: center; gap: 8px; padding: 10px 12px; }
	.pf-exp-handle { cursor: move; color: #787c82; }
	.pf-exp-title { flex: 1; font-weight: 600; color: #1d2327; }
	.pf-exp-title-label { color: #2271b1; }
	.pf-exp-title-sep, .pf-exp-title-company { font-weight: 400; color: #50575e; }
	.pf-exp-toggle { color: #787c82; text-decoration: none; }
	.pf-exp-row.is-collapsed .pf-exp-body { display: none; }
	.pf-exp-row.is-collapsed .pf-exp-toggle .dashicons { transform: rotate(-90deg); }
	.pf-exp-body { padding: 4px 12px 12px 34px; border-top: 1px solid #f0f0f1; }
	.pf-exp-field { margin: 12px 0; }
	.pf-exp-field > label { display: block; font-weight: 600; margin-bottom: 4px; }
	.pf-exp-field input[type=text], .pf-exp-field textarea { width: 100%; }
	.pf-exp-actions { margin: 12px 0 0; text-align: right; }
	</style>

	<script>
	jQuery( function ( $ ) {
		var rows  = $( '#pf-exp-rows' );
		// Indices only have to be unique — order comes from the DOM on submit,
		// and PHP re-indexes on save.
		var nextIndex = <?php echo (int) ( count( $rows ) + 1 ); ?>;

		rows.sortable( {
			handle: '.pf-exp-handle',
			placeholder: 'pf-exp-placeholder',
			forcePlaceholderSize: true,
			axis: 'y',
			start: function ( e, ui ) { ui.item.addClass( 'is-sorting' ); },
			stop: function ( e, ui ) { ui.item.removeClass( 'is-sorting' ); }
		} );

		$( '#pf-exp-add' ).on( 'click', function () {
			var html = $( '#pf-exp-template' ).html().replace( /__INDEX__/g, String( nextIndex++ ) );
			rows.append( html );
		} );

		rows.on( 'click', '.pf-exp-toggle', function () {
			var row = $( this ).closest( '.pf-exp-row' ).toggleClass( 'is-collapsed' );
			$( this ).attr( 'aria-expanded', ! row.hasClass( 'is-collapsed' ) );
		} );

		rows.on( 'click', '.pf-exp-remove', function () {
			$( this ).closest( '.pf-exp-row' ).remove();
		} );

		// Keep the collapsed header in step with what is being typed.
		rows.on( 'input', '[data-title-part]', function () {
			var row  = $( this ).closest( '.pf-exp-row' );
			var part = $( this ).data( 'title-part' );
			row.find( '.pf-exp-title-' + part ).text( $( this ).val() );
			var both = row.find( '.pf-exp-title-label' ).text() && row.find( '.pf-exp-title-company' ).text();
			row.find( '.pf-exp-title-sep' ).text( both ? '·' : '' );
		} );
	} );
	</script>
	<?php
}

/**
 * Save handler.
 */
function portfolio_save_experience() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'portfolio' ) );
	}
	check_admin_referer( 'portfolio_save_experience', 'portfolio_experience_nonce' );

	$clean = array();

	// Submission order is DOM order, so array_values() below preserves whatever
	// order the rows were dragged into, regardless of their input indices.
	$raw = isset( $_POST[ PORTFOLIO_EXPERIENCE_OPTION ] ) ? wp_unslash( $_POST[ PORTFOLIO_EXPERIENCE_OPTION ] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.

	if ( is_array( $raw ) ) {
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$entry = array(
				'label'   => isset( $row['label'] ) ? sanitize_text_field( $row['label'] ) : '',
				'company' => isset( $row['company'] ) ? sanitize_text_field( $row['company'] ) : '',
				'role'    => isset( $row['role'] ) ? sanitize_text_field( $row['role'] ) : '',
				'bullets' => isset( $row['bullets'] ) ? sanitize_textarea_field( $row['bullets'] ) : '',
			);
			// An entry with nothing in it is a leftover blank row.
			if ( '' === $entry['label'] && '' === $entry['company'] && '' === $entry['role'] && '' === trim( $entry['bullets'] ) ) {
				continue;
			}
			$clean[] = $entry;
		}
	}

	update_option( PORTFOLIO_EXPERIENCE_OPTION, array_values( $clean ) );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'    => 'portfolio-experience',
				'updated' => 1,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_portfolio_save_experience', 'portfolio_save_experience' );
