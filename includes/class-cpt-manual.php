<?php
/**
 * Registers the `manual` Custom Post Type.
 *
 * The CPT is intentionally non-public: it exists solely as a target for ACF
 * Relationship fields and should never appear as a front-end archive or
 * singular page.  Editors can inspect synced entries via the admin sidebar.
 */

defined( 'ABSPATH' ) || exit;

class ARSL_CPT_Manual {

	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
	}

	public function register_cpt(): void {
		$labels = [
			'name'                  => _x( 'Manuals', 'post type general name', 'arsl' ),
			'singular_name'         => _x( 'Manual', 'post type singular name', 'arsl' ),
			'add_new'               => __( 'Add New', 'arsl' ),
			'add_new_item'          => __( 'Add New Manual', 'arsl' ),
			'edit_item'             => __( 'Edit Manual', 'arsl' ),
			'new_item'              => __( 'New Manual', 'arsl' ),
			'view_item'             => __( 'View Manual', 'arsl' ),
			'search_items'          => __( 'Search Manuals', 'arsl' ),
			'not_found'             => __( 'No manuals found.', 'arsl' ),
			'not_found_in_trash'    => __( 'No manuals found in Trash.', 'arsl' ),
			'all_items'             => __( 'All Manuals', 'arsl' ),
			'menu_name'             => __( 'Manuals', 'arsl' ),
			'name_admin_bar'        => __( 'Manual', 'arsl' ),
		];

		register_post_type( 'manual', [
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,   // No block editor needed.
			'supports'            => [ 'title' ],
			'has_archive'         => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'menu_icon'           => 'dashicons-media-document',
			'menu_position'       => 25,
			'rewrite'             => false,
		] );
	}
}
