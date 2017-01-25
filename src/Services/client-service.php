<?php
/**
 * Client Service
 *
 * @package Lift\AdbutlerUserCampaigns\Integrations\Settings
 * @since v0.1.0
 */

namespace Lift\AdbutlerUserCampaigns\Services;
use Lift\Core\Interfaces\Service;
use Lift\Core\Interfaces\Provider;
use \AdButler\API;

/**
 * Service: Client
 *
 * Wraps the AdButler API Config Class during initialization.
 *
 * @since  v0.1.0
 */
class Client implements Service {

	/**
	 * Client
	 *
	 * @var \AdButler\API
	 */
	public $client;

	/**
	 * Options Provider
	 *
	 * @var Options_Provider
	 */
	public $options_provider;

	/**
	 * Constructor
	 *
	 * @since v0.1.0
	 * @param Provider $options_provider Provider that can pass api key to class.
	 *
	 * @return Client Instance of self
	 */
	public function __construct( Provider $options_provider ) {
		$this->client = new API;
		$this->options_provider = $options_provider;
	}

	/**
	 * Call
	 *
	 * @since  v.0.1.0
	 * @param  string $method  The method of call to make.
	 * @param  array  ...$args Arguments to configure the call.
	 * @return mixed           Response from call.
	 */
	public function call( $method, ...$args ) {
		return call_user_func_array( array( $this->client, $method ), $args );
	}

	/**
	 * Call Static
	 *
	 * @since  v.0.1.0
	 * @param  string $method  The method of call to make statically.
	 * @param  array  ...$args Arguments to configure the call.
	 * @return mixed           Response from call.
	 */
	public function call_static( $method, ...$args ) {
		return call_user_func_array( array( API::class, $type ), $args );
	}
}
