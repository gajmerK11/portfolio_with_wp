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
		return $columns;
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
	if ( $screen && 'edit-skill' === $screen->id ) {
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );
	}
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

	$inline = <<<'JS'
	jQuery( function ( $ ) {
		var list = $( '#the-list' );
		if ( ! list.length ) { return; }

		list.sortable( {
			items: 'tr',
			axis: 'y',
			cursor: 'move',
			opacity: 0.7,
			helper: function ( e, tr ) {
				// Lock cell widths so the dragged row keeps its column layout.
				var helper = tr.clone();
				tr.children().each( function ( i ) {
					$( helper.children()[ i ] ).width( $( this ).width() );
				} );
				return helper;
			},
			update: function () {
				var ids = list.find( 'tr' ).map( function () {
					return this.id ? this.id.replace( 'post-', '' ) : null;
				} ).get();

				$.post( ajaxurl, {
					action: 'portfolio_reorder_skills',
					nonce: PortfolioSkillsReorder.nonce,
					order: ids
				} );
			}
		} ).disableSelection();
	} );
JS;

	wp_add_inline_script( 'jquery-ui-sortable', $inline );
	wp_localize_script(
		'jquery-ui-sortable',
		'PortfolioSkillsReorder',
		array( 'nonce' => wp_create_nonce( 'portfolio_reorder_skills' ) )
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
