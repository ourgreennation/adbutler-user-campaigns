<?php
/**
 * Hook_Definition
 *
 * @since  v0.1.0
 *
 * @package  AdbutlerUserCampaigns
 * @subpackage Integrations
 */

namespace Lift\AdbutlerUserCampaigns\Integrations;

/**
 * Class: Hook_Definition
 *
 * @since  v0.1.0
 */
class Hook_Definition {

	/**
	 * Tag to hook method to
	 * @var string
	 */
	public $tag;

	/**
	 * Callable function to call on $tag
	 * @var callable
	 */
	public $callable;

	/**
	 * Priority to call the function with
	 * @var integer
	 */
	public $priority;

	/**
	 * Number of arguments to pass to the function
	 * @var integer
	 */
	public $args;

	/**
	 * If this HookDefinition was added to the WordPress event system, default false.
	 * @var boolean
	 */
	public $added = false;

	/**
	 * Constructor
	 *
	 * @since  v0.1.0
	 * @param string      $tag      The tag to hook the function to
	 * @param callable    $callable The function hooked to the tag
	 * @param int|integer $priority The priority to call the function with
	 * @param int|integer $args     The number of arguments to pass to the function
	 * @return  self instance
	 */
	public function __construct( $tag, $callable, $priority = 10, $args = 1 ) {
		$this->tag = $tag;
		$this->callable = $callable;
		$this->priority = $priority;
		$this->args = $args;

		return $this;
	}

	/**
	 * Add to WordPress event system
	 *
	 * @since  v0.1.0
	 * @return  self instance
	 */
	public function add() {
		add_filter( $this->tag, $this->callable, $this->priority, $this->args );
		$this->added = true;
		return $this;
	}

	/**
	 * Remove from WordPress event system
	 *
	 * @since  v0.1.0
	 * @return self Instance
	 */
	public function remove() {
		remove_filter( $this->tag, $this->callable, $this->priority, $this->args );
		$this->added = false;
		return $this;
	}
}
