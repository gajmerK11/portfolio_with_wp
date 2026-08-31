<?php
/**
 * Skills custom post type.
 *
 * One post = one skill *category* (WordPress, Backend, Frontend, …). The
 * category name is the post title (native title box hidden); the chip icon and
 * the list of skills live in the Skill Category Details meta box
 * (inc/meta-boxes/class-skill-meta-box.php).
 *
 * Category order is controlled by dragging rows on the Skills list table, which
 * writes menu_order over AJAX — the same ordering the front-end query reads.
 * page-attributes is still supported so the order survives if JS is off.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Skills CPT.
 */
function portfolio_register_skills_cpt() {
	register_post_type(
		'skill',
		array(
			'labels'       => array(
				'name'               => __( 'Skills', 'portfolio' ),
				'singular_name'      => __( 'Skill Category', 'portfolio' ),
				'add_new'            => __( 'Add New', 'portfolio' ),
				'add_new_item'       => __( 'Add New Category', 'portfolio' ),
				'edit_item'          => __( 'Edit Category', 'portfolio' ),
				'new_item'           => __( 'New Category', 'portfolio' ),
				'view_item'          => __( 'View Category', 'portfolio' ),
				'search_items'       => __( 'Search Categories', 'portfolio' ),
				'not_found'          => __( 'No skill categories found', 'portfolio' ),
				'not_found_in_trash' => __( 'No skill categories found in trash', 'portfolio' ),
				'menu_name'          => __( 'Skills', 'portfolio' ),
			),
			'public'       => false,   // Only ever shown in the front page section.
			'show_ui'      => true,
			'has_archive'  => false,
			'show_in_rest' => false,   // No block editor.
			'supports'     => array( 'title', 'page-attributes' ), // Title = category name; order = card order.
			'menu_icon'    => 'dashicons-screenoptions',
		)
	);
}
add_action( 'init', 'portfolio_register_skills_cpt' );

/**
 * Name the Skills admin title column after the field that feeds it.
 *
 * @param array $columns Existing columns.
 * @return array
 */
add_filter(
	'manage_skill_posts_columns',
	function ( $columns ) {
		$columns['title'] = __( 'Category', 'portfolio' );

		// Drag handle goes first, ahead of the checkbox, so the grab target is
		// the leading edge of the row the way it is on the Experience screen.
		// Without a visible handle the list looks like an ordinary post list
		// and nothing suggests the rows can be moved at all.
		return array( 'portfolio_order' => '<span class="screen-reader-text">' . esc_html__( 'Reorder', 'portfolio' ) . '</span>' ) + $columns;
	}
);

/**
 * Render the drag handle cell.
 *
 * @param string $column  Column key.
 */
add_action(
	'manage_skill_posts_custom_column',
	function ( $column ) {
		if ( 'portfolio_order' === $column ) {
			echo '<span class="portfolio-skill-handle dashicons dashicons-menu" aria-hidden="true"></span>';
		}
	}
);

/**
 * Show categories in menu_order on the admin list, so the drag order is what
 * the editor sees (WP defaults this CPT list to date order otherwise).
 *
 * @param WP_Query $query Current query.
 */
function portfolio_skills_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'edit-skill' !== $screen->id ) {
		return;
	}
	// Only the default view. Clicking a sortable column header sets orderby in
	// the URL, and forcing menu_order over it would make those headers dead.
	// The drag handle is hidden in that state (see the list script) because a
	// drop would write an order the visible list is not showing.
	if ( isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list-table state.
		return;
	}
	$query->set( 'orderby', 'menu_order' );
	$query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'portfolio_skills_admin_order' );

/**
 * Enqueue the drag-to-reorder script on the Skills list screen only.
 *
 * @param string $hook Current admin page.
 */
function portfolio_skills_list_assets( $hook ) {
	if ( 'edit.php' !== $hook ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'edit-skill' !== $screen->id ) {
		return;
	}

	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_style( 'dashicons' );

	// The list table has no styling for a handle column, and the sort needs to
	// look like it did something — the AJAX write is otherwise invisible.
	$css = <<<'CSS'
	.wp-list-table .column-portfolio_order { width: 32px; text-align: center; }
	.wp-list-table .portfolio-skill-handle { cursor: grab; color: #a7aaad; }
	.wp-list-table .portfolio-skill-handle:active { cursor: grabbing; }
	.wp-list-table tr:hover .portfolio-skill-handle { color: #2271b1; }
	.wp-list-table tr.portfolio-skill-sorting { opacity: .7; }
	.wp-list-table tr.portfolio-skill-placeholder > td,
	.wp-list-table tr.portfolio-skill-placeholder > th { background: #f0f6fc; height: 46px; }
	.wp-list-table tr.portfolio-skill-saved > td,
	.wp-list-table tr.portfolio-skill-saved > th { animation: portfolio-skill-flash 1s ease; }
	@keyframes portfolio-skill-flash { from { background: #f0f6fc; } to { background: transparent; } }
CSS;
	wp_add_inline_style( 'dashicons', $css );

	$inline = <<<'JS'
	jQuery( function ( $ ) {
		var list = $( '#the-list' );
		if ( ! list.length ) { return; }

		// Dragging only means anything while the list is in menu_order, which
		// is the unsorted default. Under a column sort the rows are in some
		// other order, so a drop would save positions that do not match what
		// is on screen — drop the handles instead.
		if ( /[?&]orderby=/.test( window.location.search ) ) {
			$( '.portfolio-skill-handle' ).remove();
			return;
		}

		list.sortable( {
			items: 'tr',
			// Restricted to the handle so the row's own controls — the
			// checkbox, the title link, the row actions — stay clickable
			// instead of starting a drag.
			handle: '.portfolio-skill-handle',
			axis: 'y',
			cursor: 'grabbing',
			placeholder: 'portfolio-skill-placeholder',
			helper: function ( e, tr ) {
				// Lock cell widths so the dragged row keeps its column layout.
				var helper = tr.clone();
				tr.children().each( function ( i ) {
					$( helper.children()[ i ] ).width( $( this ).width() );
				} );
				return helper;
			},
			start: function ( e, ui ) {
				ui.item.addClass( 'portfolio-skill-sorting' );
				// The placeholder is a bare <tr>; give it cells or it collapses
				// to nothing and the list jumps while dragging.
				ui.placeholder.html( '<td colspan="' + ui.item.children().length + '"></td>' );
			},
			stop: function ( e, ui ) {
				ui.item.removeClass( 'portfolio-skill-sorting' );
			},
			update: function ( e, ui ) {
				var ids = list.find( 'tr' ).map( function () {
					return this.id ? this.id.replace( 'post-', '' ) : null;
				} ).get();

				$.post( ajaxurl, {
					action: 'portfolio_reorder_skills',
					nonce: PortfolioSkillsReorder.nonce,
					order: ids
				} ).done( function () {
					ui.item.removeClass( 'portfolio-skill-saved' );
					// Reflow so the animation restarts on a repeated drop.
					void ui.item[ 0 ].offsetWidth;
					ui.item.addClass( 'portfolio-skill-saved' );
				} ).fail( function () {
					window.alert( PortfolioSkillsReorder.error );
				} );
			}
		} ).disableSelection();

		// Zebra striping is baked into the markup, so it goes wrong the moment
		// a row moves. Recolour from position after every drop.
		list.on( 'sortupdate', function () {
			list.find( 'tr' ).each( function ( i ) {
				$( this ).toggleClass( 'alternate', 0 === i % 2 );
			} );
		} );
	} );
JS;

	wp_add_inline_script( 'jquery-ui-sortable', $inline );
	wp_localize_script(
		'jquery-ui-sortable',
		'PortfolioSkillsReorder',
		array(
			'nonce' => wp_create_nonce( 'portfolio_reorder_skills' ),
			'error' => __( 'The new order could not be saved. Reload the page and try again.', 'portfolio' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'portfolio_skills_list_assets' );

/**
 * AJAX: persist the dragged order as menu_order.
 */
function portfolio_reorder_skills() {
	check_ajax_referer( 'portfolio_reorder_skills', 'nonce' );

	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}

	$order = isset( $_POST['order'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['order'] ) ) : array();
	$order = array_values( array_filter( $order ) );

	foreach ( $order as $position => $post_id ) {
		// Only reorder posts of our type.
		if ( 'skill' !== get_post_type( $post_id ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'         => $post_id,
				'menu_order' => $position,
			)
		);
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_portfolio_reorder_skills', 'portfolio_reorder_skills' );
