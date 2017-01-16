<?php
/**
 * Payment_Provider
 *
 * @since  v0.1.0
 * @package  AdbutlerUserCampaigns
 * @subpackage Providers
 */

namespace Lift\AdbutlerUserCampaigns\Providers;
use Lift\AdbutlerUserCampaigns\Interfaces\Provider;

/**
 * Class: Creative Meta
 *
 * @since  v0.1.0
 */
class Payment_Provider implements Provider {

	/**
	 * Constructor
	 *
	 * @since  v0.1.0
	 */
	public function __construct() {
		return $this;
	}

	/**
	 * Provide
	 *
	 * @since  v0.1.0
	 * @param  mixed[] ...$args Variable number of arguments.
	 * @return array An array that describes the creatives registered to the post
	 */
	public function provide( ...$args ) {
		return $this;
	}

	/**
	 * Load Javascript
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function load_javascript() {}

	/**
	 * Get JS Callback
	 *
	 * @return string A Javascript function callback
	 */
	public function get_js_callback() {
		return 'alert("No Payment Process Configured")';
	}
}
