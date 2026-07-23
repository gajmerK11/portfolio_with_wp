<?php
/**
 * Testimonials custom post type.
 *
 * Same shape as the Projects CPT: classic admin (no block editor), the native
 * title UI hidden, and every field entered through the Testimonial Details
 * meta box (inc/meta-boxes/class-testimonial-meta-box.php). The person's name
 * is mirrored into post_title so the admin list stays readable.
 *
 * @package Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Testimonials CPT.
 */
function portfolio_register_testimonials_cpt() {
	register_post_type(
		'testimonial',
		array(
			'labels'       => array(
				'name'               => __( 'Testimonials', 'portfolio' ),
				'singular_name'      => __( 'Testimonial', 'portfolio' ),
				'add_new'            => __( 'Add New', 'portfolio' ),
				'add_new_item'       => __( 'Add New Testimonial', 'portfolio' ),
				'edit_item'          => __( 'Edit Testimonial', 'portfolio' ),
				'new_item'           => __( 'New Testimonial', 'portfolio' ),
				'view_item'          => __( 'View Testimonial', 'portfolio' ),
				'search_items'       => __( 'Search Testimonials', 'portfolio' ),
				'not_found'          => __( 'No testimonials found', 'portfolio' ),
				'not_found_in_trash' => __( 'No testimonials found in trash', 'portfolio' ),
			),
			'public'       => false,   // Only ever shown in the front page section.
			'show_ui'      => true,
			'has_archive'  => false,
			'show_in_rest' => false,   // No block editor.
			'supports'     => array( 'title', 'page-attributes' ), // Order controls the card order.
			'menu_icon'    => 'dashicons-format-quote',
		)
	);
}
add_action( 'init', 'portfolio_register_testimonials_cpt' );

/**
 * Name the Testimonials admin title column after the field that feeds it.
 *
 * @param array $columns Existing columns.
 * @return array
 */
add_filter(
	'manage_testimonial_posts_columns',
	function ( $columns ) {
		$columns['title'] = __( 'Name', 'portfolio' );
		return $columns;
	}
);
