<?php
/**
 * Authorize.net Payment Provider
 *
 * @since  v0.1.0
 * @package  AdbutlerUserCampaigns
 * @subpackage Providers
 */

namespace Lift\AdbutlerUserCampaigns\Providers;
use Lift\AdbutlerUserCampaigns\Interfaces\Provider;

/**
 * Class: Authorize_Net_Payment_Provider
 *
 * @since  v0.1.0
 */
class Authorize_Net_Payment_Provider extends Payment_Provider implements Provider {

	/**
	 * Get Javascript Callback
	 *
	 * @since  v0.1.0
	 * @return string The javascript callback
	 */
	public function get_js_callback() {
		return 'window.wp.adbutler_cc.toPaymentPage()';
	}
}
