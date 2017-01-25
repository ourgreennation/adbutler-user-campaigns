<?php
/**
 * Integration: Settings
 *
 * @package Lift\AdbutlerUserCampaigns\Integrations\Settings
 * @since v0.1.0
 */

namespace Lift\AdbutlerUserCampaigns\Integrations\Settings;
use Lift\Core\Hook_Catalog;
use Lift\Core\Base_Integration;
use Lift\Core\Interfaces\Integration;
use Lift\Core\Interfaces\Provider;
use Lift\AdbutlerUserCampaigns\Providers\Options_Provider;

/**
 * Class: Integration Integration_Settings
 *
 * @uses Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog;
 * @uses Lift\AdbutlerUserCampaigns\Integrations\Integration;
 * @uses Lift\AdbutlerUserCampaigns\Interfaces\Integration;
 * @uses Lift\AdbutlerUserCampaigns\Interfaces\Provider;
 * @since v0.1.0
 */
class Integration_Settings extends Base_Integration implements Integration {
	/**
	 * Hook Catalog
	 *
	 * @var Hook_Catalog Hook Catalog
	 */
	public $hook_catalog;

	/**
	 * Options Provider
	 *
	 * @var Options_Provider Options Provider
	 */
	public $options_provider;

	/**
	 * Constructor
	 *
	 * @since v0.1.0
	 * @param Hook_Catalog $hook_catalog The main Hook_Catalog instance.
	 * @param Provider     ...$providers A variable array of providers.
	 * @return  Integration_Optionss Self instance
	 */
	public function __construct( Hook_Catalog $hook_catalog, Provider ...$providers ) {
		$this->hook_catalog = $hook_catalog;
		if ( isset( $providers[0] ) && $providers[0] instanceof Options_Provider ) {
			$this->options_provider = $providers[0];
		}
		return $this;
	}

	/**
	 * Maybe: Render Settings Page
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function maybe_render_settings_page() {
		if ( apply_filters( 'adbutler_user_creatives_render_settings_page', true ) ) {
			$this->add_hook( 'admin_menu', 'render_settings_page', 99 );
		}
	}

	/**
	 * Render Settings Page
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function render_settings_page() {
		$this->options_provider->setup();
	}
}
