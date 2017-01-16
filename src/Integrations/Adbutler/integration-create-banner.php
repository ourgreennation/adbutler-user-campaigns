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
use Lift\AdbutlerUserCampaigns\Providers\Creative_Meta;

// Vendor.
use \AdButler\ImageBanner;
use \AdButler\CampaignAssignment;

/**
 * Integration: Create Banner
 *
 * @since  v0.1.0
 */
class Integration_Create_Banner extends Integration implements Plugin_Integration {

	/**
	 * Post Meta Provider
	 *
	 * @var Creative_Meta
	 */
	protected $post_meta_provider;

	/**
	 * Constructor
	 *
	 * @param Hook_Catalog     $hook_catalog The main Hook_Catalog instance.
	 * @param array|Provider[] ...$providers Variadic array of Providers.
	 * @return Integration_Create_Banner
	 */
	public function __construct( Hook_Catalog $hook_catalog, Provider ...$providers ) {
		parent::__construct( $hook_catalog );

		// We expect a single provider.
		if ( isset( $providers[0] ) && $providers[0] instanceof Creative_Meta ) {
			$this->post_meta_provider = $providers[0];
		}

		return $this;
	}

	/**
	 * [Must] Create Creatives if they don't exist
	 *
	 * @return void
	 */
	public function must_create_banner_if_not_exists() {
		$this->add_hook( 'save_post', 'create_banner_on_save_post', 30, 3 );
	}

	/**
	 * Create Banners on `save_post` hook
	 *
	 * @since  v0.1.0
	 * @param  int      $post_id WP_Post ID.
	 * @param  \WP_Post $post    WP_Post object.
	 * @param  bool     $update  If this is an update.
	 * @return void
	 */
	public function create_banner_on_save_post( $post_id, \WP_Post $post, $update ) {
		if ( 'adbutler_campaign' !== $post->post_type ) {
			return;
		}
		// Run a helper check to make sure we have all the right condition.
		if ( self::_save_post_hook_should_cease_execution( $post_id ) ) {
			return false;
		}

		$creatives = $this->post_meta_provider->get_creatives( $post_id );
		$advertiser_id = get_user_meta( $post->post_author, 'adbutler_advertiser_id', true );
		$campaign_id = get_post_meta( $post_id, 'adbutler_campaign_id', true );

		if ( ! empty( $creatives ) && false !== $advertiser_id && false !== $campaign_id ) {
			foreach ( $creatives as $index => $creative ) {
				// Don't create any banners that already have a creative id.
				if ( isset( $creative['advertisement_id'] ) && ! empty( $creative['advertisement_id'] ) ) {
					continue;
				}

				if ( $this->fields_present_on( $creative ) ) {
					$name = $creative['name'];
					$creative_url = $creative['creative']['url'];
					$location = $creative['location'];
					$alt = $creative['html_alt_text'];
					try {
						$create = $this->create_banner( $name, $creative_url, $location, $alt );
						$create_data = $create->getData();
					} catch ( \Exception $e ) {
						wp_die( 'Client Error: ' . esc_html( $e->getMessage() ) );
					}

					try {
						$link = $this->link_banner( $create_data, $advertiser_id, $campaign_id );
						$link_data = $link->getData();
					} catch ( \Exception $e ) {
						wp_die( 'Link Error: ' . esc_html( $e->getMessage() ) );
					}
					update_sub_field( array(
						'adbutler_campaign_creatives',
						$index + 1,
						'advertisement_id',
					), $create_data['id'] );
				} else {
					wp_die( 'Missing fields on Creative' );
				}
			}
		}
	}

	/**
	 * Fields Present On
	 *
	 * @since  v0.1.0
	 * @param  array $creative A creative array.
	 * @return bool             Returns true if required fields are present, false otherwise.
	 */
	public function fields_present_on( array $creative ) {
		$required_fields = [ 'name', 'creative', 'location', 'html_alt_text' ];

		foreach ( $required_fields as $req_field ) {
			if ( ! isset( $creative[ $req_field ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Create Creative
	 *
	 * @since  v0.1.0
	 * @param  string $name         Name of Creative.
	 * @param  string $creative_url URL of Creative.
	 * @param  string $location     URL to direct clicks to.
	 * @param  string $alt          Screen reader text.
	 * @return ImageBanner          ImageBanner object
	 */
	public function create_banner( $name, $creative_url, $location, $alt ) {
		if ( defined( 'ADBUTLER_CONTRIBUTED_CREATIVES_TEST' ) && ADBUTLER_CONTRIBUTED_CREATIVES_TEST ) {
			$creative_url = 'http://www.lipsum.com/images/banners/black_300x250.gif';
		}

		$params = array(
			'name' => $name,
			'creative_url' => $creative_url,
			'height' => 250,
			'width' => 300,
			'html_target' => '_blank',
			);

		if ( $location ) {
			$params['location'] = $location;
		}

		if ( $alt ) {
			$params['html_alt_text'] = $alt;
		}

		return ImageBanner::create( $params );
	}

	/**
	 * Link Creative
	 *
	 * @since  v0.1.0
	 * @param  array $creative_data An ImageBanner array.
	 * @param  int   $advertiser_id An advertiser ID of the current user.
	 * @param  int   $campaign_id   Campaign ID of the campaign this creative is in.
	 * @return CampaignAssignment    CampaignAssignment object
	 */
	public function link_banner( $creative_data, $advertiser_id, $campaign_id ) {
		return CampaignAssignment::create([
			'campaign' => intval( $campaign_id ),
			'advertisement' => [ 'id' => $creative_data['id'], 'type' => 'banner' ],
			'weight' => 2,
			'active' => true,
		]);
	}
}
