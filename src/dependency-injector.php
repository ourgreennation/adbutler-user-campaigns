<?php
/**
 * Dependency Injector
 *
 * @package  AdbutlerUserCampaigns;
 */

namespace Lift\AdbutlerUserCampaigns;
use \AdButler\API as Client;
use Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog;
use Lift\AdbutlerUserCampaigns\Providers\ACF_Creative_Meta;
use Lift\AdbutlerUserCampaigns\Providers\Creative_Meta;
use Lift\AdbutlerUserCampaigns\Providers\Authorize_Net_Payment_Provider;
use Lift\AdbutlerUserCampaigns\Providers\Email_Provider;
use Lift\AdbutlerUserCampaigns\Providers\BP_Email_Provider;

/**
 * Class: Dependency Injector
 *
 * @since  v0.1.0
 */
class Dependency_Injector {
	/**
	 * Required Dependencies
	 *
	 * @var string[]
	 */
	protected $required;

	/**
	 * Dependencies Registered to the Injector
	 *
	 * @var mixed[]
	 */
	protected $dependencies;

	/**
	 * Constructor
	 *
	 * @return  Dependency_Injector Instance of self
	 */
	public function __construct() {
		$this->dependencies = array();
		$this->required = array();

		return $this;
	}

	/**
	 * Setup
	 *
	 * Calls the internal methods that register the dependencies, and pushes them onto
	 * the dependencies property
	 *
	 * @since  v0.1.0
	 * @return Dependency_Injector Instance of self
	 */
	public function setup() {
		array_push( $this->required, 'adbutler_client' );
		$this->dependencies['adbutler_client'] = $this->register_adbutler_client();

		array_push( $this->required, 'hook_catalog' );
		$this->dependencies['hook_catalog'] = $this->register_hook_catalog();

		array_push( $this->required, 'creative_post_meta_provider' );
		$this->dependencies['creative_post_meta_provider'] = $this->register_creative_meta_provider();

		array_push( $this->required, 'payment_provider' );
		$this->dependencies['payment_provider'] = $this->register_payment_provider();

		array_push( $this->required, 'email_provider' );
		$this->dependencies['email_provider'] = $this->register_email_provider();

		return $this;
	}

	/**
	 * Register AdButler Client
	 *
	 * @since  v0.1.0
	 * @return Client AdButler Client
	 */
	public function register_adbutler_client() {
		$key = get_option( 'adbutler_client_api_key' );
		$key = 'fe03c61d65e5b0a4ccf5f2f417c965e9';
		if ( ! $key ) {
			return null;
		}
		$client = new Client;
		$client->init( [ 'api_key' => $key ] );
		return $client;
	}

	/**
	 * Register Hook Catalog
	 *
	 * @return Hook_Catalog Instance of Hook_Catalog
	 */
	public function register_hook_catalog() {
		return new Hook_Catalog;
	}

	/**
	 * Register Creative Meta Box Provider
	 *
	 * @since  v0.1.0
	 * @return Creative_Meta An instance of child of Creative_Meta
	 */
	public function register_creative_meta_provider() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			return new ACF_Creative_Meta;
		}
		return new Creative_Meta;
	}

	/**
	 * Registers a Payment Provider
	 *
	 * @since  v0.1.0
	 * @return Payment_Provider Instance of Payment_Provider.
	 */
	public function register_payment_provider() {
		return new Authorize_Net_Payment_Provider;
	}

	/**
	 * Registers an Email Provider
	 *
	 * @since  v0.1.0
	 * @return Email_Provider Instance of Email_Provider.
	 */
	public function register_email_provider() {
		if ( class_exists( '\\BuddyPress' ) ) {
			return new BP_Email_Provider;
		}
		return new Email_Provider;
	}

	/**
	 * Inject
	 *
	 * @since  v0.1.0
	 * @param  string $reference  Dependency Reference.
	 * @return mixed|null             The dependency
	 */
	public function inject( $reference ) {
		if ( array_key_exists( $reference, $this->dependencies ) ) {
			return $this->dependencies[ $reference ];
		}
		return null;
	}

	/**
	 * Register Dependency
	 *
	 * @since  v0.1.0
	 * @param  string $reference   String to reference the dependency by.
	 * @param  mixed  $dependency  The class to register.
	 * @return Dependency_Injector Instance of self
	 */
	public function register_dependency( $reference, $dependency ) {
		$this->dependency[ $reference ] = $dependency;

		return $this;
	}

	/**
	 * Ensure Dependency
	 *
	 * @since  v0.1.0
	 * @return bool True if all required dependencies can be resolved, false otherwise.
	 */
	public function ensure_dependencies() {
		foreach ( $this->required as $req_dep ) {
			if ( ! isset( $this->dependencies[ $req_dep ] ) || is_null( $this->dependencies[ $req_dep ] ) ) {
				return false;
			}
		}
		return true;
	}
}
