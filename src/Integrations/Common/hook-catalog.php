<?php
/**
 * Hook Catalog
 *
 * @since  v0.1.0
 *
 * @package  AdbutlerUserCampaigns
 * @subpackage  Integrations\Common
 */

namespace Lift\AdbutlerUserCampaigns\Integrations;

/**
 * Class: HookCatalog
 *
 * @since  v0.1.0
 */
class Hook_Catalog {

	/**
	 * Entries
	 *
	 * @var Hook_Definition[]
	 */
	public $entries;

	/**
	 * Constructor
	 *
	 * @since  v0.1.0
	 * @return  Self instance
	 */
	public function __construct() {
		$this->entries = array();
		return $this;
	}

	/**
	 * Get Catalog Entries
	 *
	 * @since  v0.1.0
	 * @return Hook_Definition[]
	 */
	public function get_catalog_entries() {
		return $this->entries;
	}

	/**
	 * Add Entry
	 *
	 * @since  v0.1.0
	 * @param Hook_Definition $entry HookDefinition to add to catalog.
	 * @return  Hook_Definition[]	An array of the stored HookDefinitions
	 */
	public function add_entry( Hook_Definition $entry ) {
		array_push( $this->entries, $entry->add() );
		return $this->entries;
	}

	/**
	 * Remove Entry
	 *
	 * @since  v0.1.0
	 * @param  string   $tag      The tag identifying the entry to remove.
	 * @param  callable $callable The callable function/method hooked to the tag.
	 * @return Hook_Definition[]   An array of the stored HookDefinitions
	 */
	public function remove_entry( string $tag, callable $callable ) {
		return $this->entries = array_filter(
			$this->entries,
			function ( Hook_Definition $entry ) use ( $tag, $callable ) {
				if ( $tag === $entry->tag && $callable === $entry->callable ) {
					$entry->remove();
					return false;
				}
				return true;
			}
		);
	}
}
