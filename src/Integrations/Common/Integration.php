<?php
/**
 * Abstact Integration
 *
 * @package  AdbutlerUserCampaigns
 * @subpackage  Integrations
 */

namespace Lift\AdbutlerUserCampaigns\Integrations;
use Lift\AdbutlerUserCampaigns\Interfaces\Plugin_Integration;
use Lift\AdbutlerUserCampaigns\Interfaces\Provider;

/**
 * Abstract Class: Integration
 *
 * @since  v0.1.0
 * @see  Lift\AdbutlerUserCampaigns\Interfaces\Plugin_Integration
 */
abstract class Integration implements Plugin_Integration {

	protected $hook_catalog;

	public function __construct( Hook_Catalog $hook_catalog, Provider ...$providers ) {
		$this->hook_catalog = $hook_catalog;
		return $this;
	}

	/**
	 * Add All Integrations
	 *
	 * Adds all integrations defined as methods within the called class.  Those methods
	 * prefixed with `maybe_` will have a corresponding call to `apply_filters` that an
	 * end user can hook into and return false from, thus removing that integration.
	 * Those methods prefixed with `must_` will not have any call to `apply_filters`, and
	 * are required.
	 *
	 * @since v2.0.0
	 * @return  array An array of integration method names that were added
	 */
	public function add_all_hooks() {
		$integrations = array_filter( get_class_methods( $this ), function( $method ) {
			return ( strpos( $method, 'maybe_' ) === 0 || strpos( $method, 'must_' ) === 0 );
		});

		$added_integrations = array();

		foreach( $integrations as $integration ) {
			array_push( $added_integrations, $this->$integration() ? $this->$integration : false );
		}

		return array_filter( $added_integrations );
	}

	/**
	 * Get All Integrations
	 *
	 * @since  v2.0.0
	 * @return array An array of integrations added by class within the HookCatalog
	 */
	public function get_all_hooks() {
		return array_filter( $this->hook_catalog->entries, function( $entry ) use ( $class ) {;
			return ( $this === get_class( $entry->callable[0] ) );
		});
	}

	/**
	 * Add Integration
	 *
	 * @since v2.0.0
	 * @param string      $tag      String reference to the hook to apply function to
	 * @param string      $method   Method to hook to $tag
	 * @param int|integer $priority Priority in which it should run, default 10.
	 * @param int|integer $args     Number of arguments to pass to the method, default 1.
	 *
	 * @return  bool True if the Hook_Definition describing integration was added to HookCatalog
	 */
	public function add_hook( $tag, $method, $priority = 10, $args = 1 ) {
		$definition = new Hook_Definition( $tag, array( $this, $method ), $priority, $args );
		$this->hook_catalog->add_entry( $definition );
		return true;
	}

	/**
	 * Helper: Should we continue execution in save_post hook?
	 *
	 * @since  v0.1.0
	 * @param  int    $post_id WP_Post ID
	 * @return bool            True if execution should cease, false otherwise.
	 */
	final protected static function _save_post_hook_should_cease_execution( $post_id ) {
		// Autosave, do nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return true;
		}
		// AJAX? Not used here
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return true;
		}
		// Return if it's a post revision
		if ( false !== wp_is_post_revision( $post_id ) ) {
			return true;
		}

		return false;
	}
}
