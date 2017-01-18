<?php
/**
 * Creative_Meta
 *
 * @since  v0.1.0
 * @package  AdbutlerUserCampaigns
 * @subpackage Providers
 */

namespace Lift\AdbutlerUserCampaigns\Providers;
use Lift\Core\Interfaces\Provider;

/**
 * Class: Creative Meta
 *
 * @since  v0.1.0
 */
class Creative_Meta implements Provider {

	/**
	 * Constructor
	 *
	 * @since  v0.1.0
	 */
	public function __construct() {}

	/**
	 * Provide
	 *
	 * @since  v0.1.0
	 * @param  mixed[] ...$args Variable number of arguments.
	 * @return array An array that describes the creatives registered to the post
	 */
	public function provide( ...$args ) {
		return $this->get_creatives( $args[0] ? $args[0] : 0 );
	}

	/**
	 * Get Creatives
	 *
	 * @todo   This is the base class, needs defaults if publicly released.
	 * @since  v0.1.0
	 * @param  int $post_id Post ID.
	 * @return array          An array that describes the creatives registered to the post
	 */
	public function get_creatives( $post_id ) {
		return array();
	}

	/**
	 * Do Post Meta Box
	 *
	 * @todo   This is the base class, needs defaults if publicly released.
	 * @since  v0.1.0
	 * @return void
	 */
	public function do_post_meta_box() {
		return;
	}
}
