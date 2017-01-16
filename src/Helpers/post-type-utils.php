<?php
/**
 * Post Type Utils
 *
 * @package  AdbutlerUserCampaigns
 *
 * @subpackage  Helpers
 */

namespace Lift\AdbutlerUserCampaigns\Helpers;

/**
 * Trait: Post Type Utils
 *
 * @since  v0.1.0
 */
trait Post_Type_Utils {

	/**
	 * Fill Post Type Args with Labels
	 *
	 * @since  v0.1.0
	 * @param  array  $args      An array of Post_Type arguments to add labels to.
	 * @param  string $singular A string representation of the singular post type label.
	 * @param  string $plural   A string representation of the plural post type label.
	 * @return array            An arrya of Post_Type arguments with labels attached
	 */
	function fill_post_type_args_labels( $args, $singular, $plural ) {
		$args['label'] = $singular;
		$args['labels'] = array(
			'name'                  => _x( "$plural", 'Post Type General Name', 'adbutler-cc-i18n' ),
			'singular_name'         => _x( "$singular", 'Post Type Singular Name', 'adbutler-cc-i18n' ),
			'menu_name'             => __( "$plural", 'adbutler-cc-i18n' ),
			'name_admin_bar'        => __( "$singular", 'adbutler-cc-i18n' ),
			'archives'              => __( "$singular Archives", 'adbutler-cc-i18n' ),
			'attributes'            => __( "$singular Attributes", 'adbutler-cc-i18n' ),
			'parent_item_colon'     => __( "$singular Parent Item:", 'adbutler-cc-i18n' ),
			'all_items'             => __( "All $plural", 'adbutler-cc-i18n' ),
			'add_new_item'          => __( "Add New $singular", 'adbutler-cc-i18n' ),
			'add_new'               => __( 'Add New', 'adbutler-cc-i18n' ),
			'new_item'              => __( "New $singular", 'adbutler-cc-i18n' ),
			'edit_item'             => __( "Edit $singular", 'adbutler-cc-i18n' ),
			'update_item'           => __( "Update $singular", 'adbutler-cc-i18n' ),
			'view_item'             => __( "View $singular", 'adbutler-cc-i18n' ),
			'view_items'            => __( "View $plural", 'adbutler-cc-i18n' ),
			'search_items'          => __( "Search $singular", 'adbutler-cc-i18n' ),
			'not_found'             => __( 'Not found', 'adbutler-cc-i18n' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'adbutler-cc-i18n' ),
			'featured_image'        => __( 'Featured Image', 'adbutler-cc-i18n' ),
			'set_featured_image'    => __( 'Set featured image', 'adbutler-cc-i18n' ),
			'remove_featured_image' => __( 'Remove featured image', 'adbutler-cc-i18n' ),
			'use_featured_image'    => __( 'Use as featured image', 'adbutler-cc-i18n' ),
			'insert_into_item'      => __( 'Insert into item', 'adbutler-cc-i18n' ),
			'uploaded_to_this_item' => __( "Uploaded to this $singular", 'adbutler-cc-i18n' ),
			'items_list'            => __( "$plural list", 'adbutler-cc-i18n' ),
			'items_list_navigation' => __( "$plural list navigation", 'adbutler-cc-i18n' ),
			'filter_items_list'     => __( "Filter $plural list", 'adbutler-cc-i18n' ),
			);

		return $args;
	}

	/**
	 * Fill Post Type Args
	 *
	 * @since  v0.1.0
	 * @param  array  $args     An array of Post_Type arguments.
	 * @param  string $singular String representation of the singular post type label.
	 * @param  string $plural   String representation of the plural post type label.
	 * @return array            An array of Post_Type arguments filled with typical defaults
	 */
	public function fill_post_type_args( $args = array(), $singular = 'item', $plural = 'items' ) {
		$defaults = array(
			'label'					=> __( $singular, 'adbutler-cc-i18n' ),
			'description'           => __( 'Custom Post Type', 'adbutler-cc-i18n' ),
			'supports'              => array(),
			'taxonomies'            => array( 'category', 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		);

		// Fill the labels.
		$labelled = $this->fill_post_type_args_labels( $defaults, $singular, $plural );

		// Merge the user provided arguments with our defaults.
		return array_merge( $labelled, $args );
	}
}
