<?php
/**
 * Plugin Integration
 *
 * @since  v0.1.0
 *
 * @package  AdbutlerUserCampaigns
 * @subpackage  Interfaces
 */

namespace Lift\AdbutlerUserCampaigns\Interfaces;
use Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog;

/**
 * Interface: Plugin Integration
 */
interface Plugin_Integration {

	/**
	 * Constructor
	 * @param Hook_Catalog $hook_catalog Hook Catalog instance
	 * @param Provider[]   $providers    A variable parameter of Providers
	 */
	public function __construct( Hook_Catalog $hook_catalog, Provider ...$providers );

	/**
	 * Get All Hooks
	 * @since  v0.1.0
	 */
	public function add_all_hooks();

	/**
	 * Get All Hooks
	 * @since  v0.1.0
	 * @return mixed
	 */
	public function get_all_hooks();

	/**
	 * Add Hook
	 *
	 * @since v0.1.0
	 *
	 * @param int      $tag      Tag
	 * @param callable $callable Callable function
	 * @param int      $priority Priority
	 * @param int      $args     Number of arguments
	 *
	 * @return  mixed
	 */
	public function add_hook( $tag, $callable, $priority, $args );
}
