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

		// Merge the user provided arguments with our defaults.
		return array_merge( $defaults, $args );
	}
}
