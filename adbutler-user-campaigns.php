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
use Lift\AdbutlerUserCampaigns\Integrations\Payments\Integration_Payments;
use Lift\AdbutlerUserCampaigns\Integrations\Notifications\Integration_Email_Notifications;

define( 'ADBUTLER_CONTRIBUTED_CREATIVES_VERSION', '0.1.0' );
define( 'ADBUTLER_CONTRIBUTED_CREATIVES_DIR', __DIR__ );
define( 'ADBUTLER_CONTRIBUTED_CREATIVES_FILE', __FILE__ );
define( 'ADBUTLER_CONTRIBUTED_CREATIVES_TEST', true );
define( 'ADBUTLER_CONTRIBUTED_CREATIVES_URI', \plugins_url( '', __FILE__ ) );

require_once( ADBUTLER_CONTRIBUTED_CREATIVES_DIR . '/vendor/autoload.php' );

/**
 * Action: adbutler_cc_composer_did_autoload
 *
 * Informs WordPress Event System that Adbutler CC has registered its autoload classes.
 */
do_action( 'adbutler_cc_composer_did_autoload' );

add_action( 'adbutler_cc_additional_roles', function() {
	return [ 'ogn_contributor' ];
});

// Register Activation Hook
register_activation_hook( __FILE__, array( __NAMESPACE__ . '\\Plugin', 'activate' ) );

// Register DeActivation Hook
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\\Plugin', 'deactivate' ) );

/**
 * AdButler User Campaigns
 *
 * Sets up the plugin and all the associated functionality
 *
 * @return bool|Plugin An instance of the main plugin class, false if dependencies aren't met
 */
function adbutler_user_campaigns() {
	// Get a new Dependency_Injector
	$injector = new Dependency_Injector;
	$injector->setup();

	// Allow other plugins and the theme to access the Dependency Injector
	$injector = apply_filters( 'adbutler_cc_dependency_injector', $injector );

	// Ensure we have all dependencies
	if ( ! $injector->ensure_dependencies() ) {
		echo '<pre>';
		var_dump( $injector );
		return false;
	}

	// Declare a Global Instance of Plugin because WordPress uses Globals everywhere.
	global $adbutler_cc;

	// Instantiate a new instance of the Plugin
	$adbutler_cc = new Plugin( $injector );

	// Setup Plugin and Define All Integrations
	$adbutler_cc->setup()
		->register_integration(
			new Integration_Create_Advertiser(
				$adbutler_cc->injector->inject( 'hook_catalog' )
			)
		)
		->register_integration(
			new Integration_Create_Campaign(
				$adbutler_cc->injector->inject( 'hook_catalog' )
			)
		)
		->register_integration(
			new Integration_Create_Banner(
				$adbutler_cc->injector->inject( 'hook_catalog' ),
				$adbutler_cc->injector->inject( 'creative_post_meta_provider' )
			)
		)
		->register_integration(
			new Integration_Payments(
				$adbutler_cc->injector->inject( 'hook_catalog' ),
				$adbutler_cc->injector->inject( 'payment_provider' )
			)
		)
		->register_integration(
			new Integration_Email_Notifications(
				$adbutler_cc->injector->inject( 'hook_catalog' ),
				$adbutler_cc->injector->inject( 'email_provider' )
			)
		);

	// Allow plugins and themes to add additional integrations.
	return apply_filters( 'adbutler_cc_plugin', $adbutler_cc, $injector );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\adbutler_user_campaigns', 20 );
