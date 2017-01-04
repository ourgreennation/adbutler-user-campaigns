<?php
/**
 * Plugin Name:     Adbutler User Campaigns
 * Plugin URI:      https://ourgreennation.org
 * Description:     Support for User Campaigns with AdButler's Ad Platform. Custom built for Our Green Nation.
 * Author:          Lift UX <christian@liftux.com>
 * Author URI:      https://liftux.com
 * Text Domain:     adbutler-cc
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         AdbutlerUserCampaigns
 */

namespace Lift\AdbutlerUserCampaigns;

use Lift\AdbutlerUserCampaigns\Integrations\Adbutler\Integration_Create_Advertiser;
use Lift\AdbutlerUserCampaigns\Integrations\Adbutler\Integration_Create_Campaign;
use Lift\AdbutlerUserCampaigns\Integrations\Adbutler\Integration_Create_Banner;

define( 'ADBUTLER_CONTRIBUTED_CREATIVES_VERSION', '0.1.0' );
define( 'ADBUTLER_CONTRIBUTED_CREATIVES_DIR', __DIR__ );
define( 'ADBUTLER_CONTRIBUTED_CREATIVES_FILE', __FILE__ );
define( 'ADBUTLER_CONTRIBUTED_CREATIVES_TEST', true );

require_once( ADBUTLER_CONTRIBUTED_CREATIVES_DIR . '/vendor/autoload.php' );

/**
 * Action: adbutler_cc_composer_did_autoload
 *
 * Informs WordPress Event System that Adbutler CC has registered it autoload classes.
 */
do_action( 'adbutler_cc_composer_did_autoload' );

// Register Activation Hook
register_activation_hook( __FILE__, array( __NAMESPACE__ . '\\Plugin', 'activate' ) );

// Register DeActivation Hook
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\\Plugin', 'deactivate' ) );

function AdbutlerUserCampaigns() {
	// Get a new Dependency_Injector
	$injector = new Dependency_Injector;
	$injector->setup();

	// Ensure we have all dependencies
	if( ! $injector->ensure_dependencies() ) {
		return;
	}

	// Get Instance of Plugin
	global $adbutler_cc;
	$adbutler_cc = new Plugin( $injector );

	// Setup Plugin and Define All Integrations
	$adbutler_cc->setup()
		->register_integration( new Integration_Create_Advertiser(
			$adbutler_cc->injector->inject( 'hook_catalog' ) ) )
		->register_integration( new Integration_Create_Campaign(
			$adbutler_cc->injector->inject( 'hook_catalog' ) ) )
		->register_integration( new Integration_Create_Banner(
			$adbutler_cc->injector->inject( 'hook_catalog' ),
			$adbutler_cc->injector->inject( 'creative_post_meta_provider' ) ) );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\AdbutlerUserCampaigns' );

