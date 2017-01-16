<?php
/**
 * Create Advertiser
 *
 * @package  AdbutlerUserCampaigns
 * @subpackage  Integrations
 */

namespace Lift\AdbutlerUserCampaigns\Integrations\Adbutler;
use Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog;
use Lift\AdbutlerUserCampaigns\Integrations\Integration;
use Lift\AdbutlerUserCampaigns\Interfaces\Plugin_Integration;
use Lift\AdbutlerUserCampaigns\Interfaces\Provider;

// Vendor.
use \AdButler\Advertiser;

/**
 * Class: Integration_Create_Advertiser
 *
 * @see Lift\AdbutlerUserCampaigns\Integrations\Integration
 * @see Lift\AdbutlerUserCampaigns\Intefaces\Plugin_Integration
 * @since  v.0.1.0
 */
class Integration_Create_Advertiser extends Integration implements Plugin_Integration {

	/**
	 * Constructor
	 *
	 * @param Hook_Catalog $hook_catalog The main Hook_Catalog instance.
	 * @param Provider     ...$providers A variable array of providers.
	 */
	public function __construct( Hook_Catalog $hook_catalog, Provider ...$providers ) {
		parent::__construct( $hook_catalog );
	}

	/**
	 * (Must) Create Advertiser in AdButler
	 *
	 * In AdButler, banners and campaigns are tied to an advertiser. We create associations
	 * between WordPress Users and Advertisers in AdButler.  If a WP user has the ability
	 * to upload banners to AdButler, they must already have an Advertiser ID to associate
	 * the user with.  This function adds hooks to `profile_update` and `save_post` to ensure
	 * we are creating the association betweent advertisers and users.
	 *
	 * @return bool Always returns true, this hook must run.
	 */
	public function must_create_advertiser_if_not_exists() {
		$this->add_hook( 'save_post', 'create_advertiser_on_save_post', 10, 3 );
		$this->add_hook( 'profile_update', 'create_advertiser_on_profile_update', 10, 2 );
		return true;
	}

	/**
	 * Create Advertiser On Save Post
	 *
	 * @link https://developer.wordpress.org/reference/hooks/save_post/
	 * @uses   Integration::_save_post_hook_should_cease_execution()
	 * @since  v0.1.0
	 * @param  int      $post_id \WP_Post::$ID.
	 * @param  \WP_Post $post    \WP_Post object.
	 * @param  bool     $update  True if this is a post being updated.
	 * @return bool              True on success, false if no action taken.
	 */
	public function create_advertiser_on_save_post( $post_id, \WP_Post $post, $update ) {
		if ( 'adbutler_campaign' !== $post->post_type || ! $author = $post->post_author ) {
			return;
		}

		// Run a helper check to make sure we have all the right condition.
		if ( self::_save_post_hook_should_cease_execution( $post_id ) ) {
			return false;
		}

		// The Author is the User we're going to ensure is an advertiser.
		$user_id = intval( $author );

		// Check for advertiser ID, if it already exists, we're done here.
		$advertiser_id = get_user_meta( $user_id, 'adbutler_advertiser_id', true );
		if ( ! empty( $advertiser_id ) ) {
			return false;
		}

		// Okay, we don't have an advertiser id, let's create an advertiser.
		$user_data = get_userdata( $user_id );

		try {
			$descriptive_name = sprintf( '%s | %s | %d ',
				$user_data->data->display_name,
				$user_data->user_login,
				$user_data->ID
			);
			$response = $this->create_advertiser( $descriptive_name, $user_data->user_email );
			$data = $response->getData();
		} catch ( \Exception $e ) {
			wp_die( esc_html( $e->getMessage() ) );
		}

		// If we haven't thrown, request was most likely successful, let's make sure and then update.
		if ( ! is_numeric( $data['id'] ) ) {
			wp_die( 'Could not save AdButler advertiser ID.  Please try again.' );
		}
		$success = update_user_meta( $user_data->ID, 'adbutler_advertiser_id', intval( $data['id'] ) );

		if ( false === $success ) {
			wp_die( 'Could not save AdButler advertiser ID.  Please try again.' );
		}

		return  ( ! ( false === $success ) );
	}

	/**
	 * Create Advertiser on Profile Update
	 *
	 * @link   https://developer.wordpress.org/reference/hooks/profile_update/
	 * @since  v0.1.0
	 * @param  int      $user_id      \WP_User:ID.
	 * @param  \WP_User $old_userdata \WP_User object.
	 * @return bool                   True if action taken, false otherwise
	 */
	public function create_advertiser_on_profile_update( $user_id, \WP_User $old_userdata ) {
		// There are a variety of ways to grant permissions to users, we check caps, not role.
		if ( ! \user_can( $user_id, 'edit_adbutler_creatives' ) ) {
			return false;
		}

		// Now check if they have a advertiser id.
		$advertiser_id = \get_user_meta( $user_id, 'adbutler_advertiser_id', true );
		if ( ! empty( $advertiser_id ) ) {
			return;
		}

		// Old userdata is irrelevant, we want the new user data.
		$user_data = get_userdata( $user_id );

		// Okay we need to create an Advertiser.
		try {
			$response = $this->create_advertiser( $user_data->user_login, $user_data->user_email );
			$data = $response->getData();
		} catch ( \Exception $e ) {
			wp_die( esc_html( $e->getMessage() ) );
		}

		// If we haven't thrown, request was most likely successful, let's make sure and then update.
		if ( ! is_numeric( $data['id'] ) ) {
			wp_die( 'Could not save AdButler Advertiser ID.  Please try again.' );
		}
		$success = update_user_meta( $user_data->ID, 'adbutler_advertiser_id', intval( $data['id'] ) );

		if ( false === $success ) {
			wp_die( 'Could not save AdButler Advertiser ID.  Please try again.' );
		}

		return  ( ! ( false === $success ) );
	}

	/**
	 * Create Advertiser
	 *
	 * Creates an Advertiser on AdButler's platform.  Uses AdButler's Client.
	 *
	 * @uses   \AdButler\Advertiser::create()
	 * @since  v0.1.0
	 * @param  string $username WordPress unique username.
	 * @param  string $email    WordPress unique email.
	 * @return \Adbutler\Advertiser
	 */
	public function create_advertiser( $username, $email ) {
		return Advertiser::create([
			'can_change_password' => true,
			'can_add_banners' => true,
			'email' => esc_html( $email ),
			'name' => esc_html( $username ),
			'password' => wp_generate_password( 12 ),
		]);
	}
}
