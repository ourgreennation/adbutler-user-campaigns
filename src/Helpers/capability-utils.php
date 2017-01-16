<?php
/**
 * Capability Utilities
 *
 * @since  v0.1.0
 *
 * @package  AdbutlerUserCampaigns
 * @subpackage  Helpers
 */

namespace Lift\AdbutlerUserCampaigns\Helpers;

/**
 * Trait: Capability Utilities
 *
 * @since  v0.1.0
 */
trait Capability_Utils {

	/**
	 * Grant Capabilities
	 *
	 * @since  v0.1.0
	 * @param  \WP_Role $role WP_Role object to grant capabilities to
	 * @param  array    $caps An array of capabilities to grant
	 * @return void
	 */
	final protected static function grant_capabilities( \WP_Role $role, array $caps ) {
		foreach ( $caps as $cap ) {
			$role->add_cap( strval( $cap ) );
		}
	}

	/**
	 * Ungrant Capabilities
	 *
	 * @since  v0.1.0
	 * @param  \WP_Role $role WP_Role object to remove capabilities from
	 * @param  array    $caps An array of capabilities to remove
	 * @return void
	 */
	final protected static function ungrant_capabilities( \WP_Role $role, array $caps ) {
		foreach ( $caps as $cap ) {
			$role->remove_cap( $cap );
		}
	}
}
