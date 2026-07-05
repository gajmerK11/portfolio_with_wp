<?php
/**
 * Projects custom post type.
 *
 * Classic (no block editor): show_in_rest is disabled and the native title
 * UI is turned off, so the meta box (inc/meta-boxes/class-project-meta-box.php)
 * is the single place to enter a project. The Project Title field there is
 * mirrored into the WP post_title on save so the admin list and permalinks
 * stay readable.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Projects CPT.
 */
function portfolio_register_projects_cpt() {
	register_post_type(
		'project',
		array(
			'labels'       => array(
				'name'               => __( 'Projects', 'portfolio' ),
				'singular_name'      => __( 'Project', 'portfolio' ),
				'add_new'            => __( 'Add New', 'portfolio' ),
				'add_new_item'       => __( 'Add New Project', 'portfolio' ),
				'edit_item'          => __( 'Edit Project', 'portfolio' ),
				'new_item'           => __( 'New Project', 'portfolio' ),
				'view_item'          => __( 'View Project', 'portfolio' ),
				'search_items'       => __( 'Search Projects', 'portfolio' ),
				'not_found'          => __( 'No projects found', 'portfolio' ),
				'not_found_in_trash' => __( 'No projects found in trash', 'portfolio' ),
			),
			'public'       => true,
			'has_archive'  => false,
			'show_in_rest' => false,             // No block editor.
			'supports'     => array( 'title' ),  // Title kept for admin list; hidden on the edit screen (see below).
			'menu_icon'    => 'dashicons-portfolio',
			'rewrite'      => array(
				'slug'       => 'projects',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'portfolio_register_projects_cpt' );

/**
 * Order the Projects admin column heading.
 *
 * @param array $columns Existing columns.
 * @return array
 */
add_filter(
	'manage_project_posts_columns',
	function ( $columns ) {
		$columns['title'] = __( 'Project Title', 'portfolio' );
		return $columns;
	}
);
