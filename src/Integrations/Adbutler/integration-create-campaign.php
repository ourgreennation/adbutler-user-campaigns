<?php
/**
 * Create Campaign
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
use \AdButler\BannerCampaign;

/**
 * Integration: Create Campaign
 *
 * @since  v0.1.0
 */
class Integration_Create_Campaign extends Integration implements Plugin_Integration {

	/**
	 * Constructor
	 *
	 * @param Hook_Catalog     $hook_catalog The main Hook_Catalog instance.
	 * @param array|Provider[] ...$providers Array of providers.
	 * @return  Integration_Create_Campaign Instance of self.
	 */
	public function __construct( Hook_Catalog $hook_catalog, Provider ...$providers ) {
		parent::__construct( $hook_catalog );
	}

	/**
	 * (Must) Create Campaign in AdButler
	 *
	 * In AdButler, Campaigns are used to group creatives together beneath an advertiser.
	 * This allows advertisers to create Campaigns for different offers, a new product,
	 * an upcoming event, etc.  Advertisers can create campaigns and have them run as they
	 * see fit.
	 *
	 * @return bool Always returns true, this hook must run.
	 */
	public function must_create_campaign_if_not_exists() {
		$this->add_hook( 'save_post', 'create_campaign_on_save_post', 20, 3 );
		return true;
	}

	/**
	 * Create Campaign On Save Post
	 *
	 * @link https://developer.wordpress.org/reference/hooks/save_post/
	 * @uses   Integration::_save_post_hook_should_cease_execution()
	 *
	 * @since  v0.1.0
	 * @param  int      $post_id \WP_Post::$ID.
	 * @param  \WP_Post $post    \WP_Post object.
	 * @param  bool     $update  True if this is a post being updated.
	 * @return bool              True on success, false if no action taken.
	 */
	public function create_campaign_on_save_post( $post_id, \WP_Post $post, $update ) {
		if ( 'adbutler_campaign' !== $post->post_type ) {
			return;
		}
		if ( ! isset( $_POST['post_title'] ) || empty( $_POST['post_title'] ) ) {
			return;
		}

		// Run a helper check to make sure we have all the right condition.
		if ( self::_save_post_hook_should_cease_execution( $post_id ) ) {
			return false;
		}

		// Let's see if this is assocaiated with an AdButler Campaign, if it is, we're done.
		$campaign_id = get_post_meta( intval( $post_id ), 'adbutler_campaign_id', true );
		if ( is_numeric( $campaign_id ) ) {
			return false;
		}

		// We also need to make sure we have an advertiser id to work with.
		$user_id = intval( $post->post_author );
		$advertiser_id = get_user_meta( $user_id, 'adbutler_advertiser_id', true );
		if ( empty( $advertiser_id ) ) {
			// Uh oh, this campaign doesn't have an author who is an advertiser. Let's fix.
			$advertiser = new Integration_Create_Advertiser( $this->hook_catalog );
			call_user_func_array( array( $advertiser, 'create_advertiser_on_save_post' ), func_get_args() );
			unset( $advertiser );
			$advertiser_id = get_user_meta( $user_id, 'adbutler_advertiser_id', true );
		}

		// Okay, we have stuff to do.
		try {
			// The title is stored in $_POST, so let's extract and clean it up.
			$title = sanitize_text_field( wp_unslash( $_POST['post_title'] ) );
			// Okay, send off the request.
			$response = $this->create_campaign( $title , intval( $advertiser_id ) );
			$data = $response->getData();
		} catch ( \Exception $e ) {
			wp_die( 'Client Error' . esc_html( $e->getMessage() ) );
		}

		// If we didn't catch and die above, we probably have a good campaign id.  Let's make sure.
		if ( ! is_numeric( $data['id'] ) ) {
			wp_die( 'Could not save AdButler Campaign ID.  Please try again.' );
		}
		$success = update_post_meta( $post_id, 'adbutler_campaign_id', intval( $data['id'] ) );

		if ( false === $success ) {
			wp_die( 'Could not save AdButler Campaign ID.  Please try again.' );
		}

		return  ( ! ( false === $success ) );
	}

	/**
	 * Create BannerCampaign
	 *
	 * Creates an BannerCampaign on AdButler's platform.  Uses AdButler's BannerCampaign.
	 *
	 * @uses   \AdButler\BannerCampaign::create()
	 * @since  v0.1.0
	 * @param  string $name 		WordPress Post Title.
	 * @param  int    $advertiser   AdButler Advertiser ID.
	 * @return \Adbutler\BannerCampaign
	 */
	public function create_campaign( $name, $advertiser ) {
		return BannerCampaign::create([
			'name' => esc_html( $name ),
			'advertiser' => intval( $advertiser ),
			'height' => 250,
			'width' => 300,
		]);
	}
}
