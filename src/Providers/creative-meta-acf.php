<?php
/**
 * ACF_Creative_Meta
 *
 * @since  v0.1.0
 * @package  AdbutlerUserCampaigns
 * @subpackage Providers
 */

namespace Lift\AdbutlerUserCampaigns\Providers;
use Lift\Core\Interfaces\Provider;

/**
 * Class: ACF_Creative_Meta
 *
 * @see    Lift\AdbutlerUserCampaigns\Providers\Creative_Meta
 * @since  v0.1.0
 */
class ACF_Creative_Meta extends Creative_Meta implements Provider {
	/**
	 * Get Creatives
	 *
	 * @since  v0.1.0
	 * @param  int $post_id WP_Post ID.
	 * @return array           An array of creatives
	 */
	public function get_creatives( $post_id ) {
		return get_field( 'adbutler_campaign_creatives', intval( $post_id ) );
	}

	/**
	 * Do Post Meta Box
	 *
	 * Adds ACF fields to our post type
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function do_post_meta_box() {
		if ( function_exists( 'acf_add_local_field_group' ) ) :
			acf_add_local_field_group(array(
				'key' => 'group_586c3e6c6e296',
				'title' => 'Add Your Creatives',
				'fields' => array(
					array(
						'key' => 'field_586c3e88f718d',
						'label' => 'Campaign Creatives',
						'name' => 'adbutler_campaign_creatives',
						'type' => 'repeater',
						'instructions' => 'Thanks for advertising with us.	Upload your campaign creatives here, (maximum of 20), just follow the on-screen instructions.',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'collapsed' => '',
						'min' => '',
						'max' => 20,
						'layout' => 'row',
						'button_label' => 'Add Creative',
						'sub_fields' => array(
							array(
								'key' => 'field_586c9fecc7393',
								'label' => 'Name',
								'name' => 'name',
								'type' => 'text',
								'instructions' => 'Name this something unique that is relevant to your creative.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => 150,
							),
							array(
								'key' => 'field_586c3ecef718e',
								'label' => 'Creative',
								'name' => 'creative',
								'type' => 'image',
								'instructions' => 'Upload a Creative.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'return_format' => 'array',
								'preview_size' => 'medium',
								'library' => 'all',
								'min_width' => 300,
								'min_height' => 250,
								'min_size' => '',
								'max_width' => 600,
								'max_height' => 500,
								'max_size' => '',
								'mime_types' => '.jpeg, .jpg, .png, .gif',
							),
							array(
								'key' => 'field_586c72364bb52',
								'label' => 'Location',
								'name' => 'location',
								'type' => 'url',
								'instructions' => 'The url you want the user to go if they click on the ad.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'Target URL',
							),
							array(
								'key' => 'field_586c72984bb53',
								'label' => 'Alt Text',
								'name' => 'html_alt_text',
								'type' => 'text',
								'instructions' => 'Alternative Text to provide to Screen Reader software.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'adbutler_campaign',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'acf_after_title',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
			));

		endif;
	}
}
