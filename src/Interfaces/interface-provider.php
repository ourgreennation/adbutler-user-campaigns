<?php
/**
 * Provider
 *
 * @package  AdbutlerUserCampaigns
 * @subpackage  Interfaces
 */

namespace Lift\AdbutlerUserCampaigns\Interfaces;

/**
 * Interface: Provider
 *
 * @since  v0.1.0
 */
interface Provider {

	/**
	 * Provide
	 *
	 * @since  v0.1.0
	 * @param  array|mixed[] ...$args Arguments.
	 * @return mixed
	 */
	public function provide( ...$args );
}
