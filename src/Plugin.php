<?php
/**
 * Main Plugin File
 *
 * @package  AdbutlerUserCampaigns
 */

namespace Lift\AdbutlerUserCampaigns;

// Integrations:
use Lift\AdbutlerUserCampaigns\Interfaces\Plugin_Integration;

// Helpers
use Lift\AdbutlerUserCampaigns\Helpers\Capability_Utils;
use Lift\AdbutlerUserCampaigns\Helpers\Post_Type_Utils;

/**
 * Class: Plugin
 *
 * Main Plugin Class
 *
 * @since  v.0.1.0
 */
final class Plugin {
	// Adds utilities to grant capabilities to roles
	use Capability_Utils;

	// Adds helper functions to register new post types
	use Post_Type_Utils;

	/**
	 * Path the root plugin directory
	 * @var string
	 */
	public static $plugin_dir;

	/**
	 * Plugin version
	 * @var string SEMVER version
	 */
	public static $plugin_version;

	/**
	 * Path the application root directory
	 * @var string
	 */
	public static $app_dir;

	/**
	 * Dependency Injector
	 * @var Dependency_Injector
	 */
	public $injector;

	/**
	 * Integrations
	 * @var Plugin_Integration[] Array of Plugin_Integrations
	 */
	protected $integrations;

	/**
	 * Post Types
	 * @var \WP_Post_Type[] An array of \WP_Post_Type objects
	 */
	protected $post_types = array();

	/**
	 * Constructor
	 *
	 * @since  v0.1.0
	 * @return  Plugin Instance of self
	 */
	public function __construct( Dependency_Injector $injector ) {
		if ( is_null( static::$plugin_dir ) ) {
			static::$plugin_dir = ADBUTLER_CONTRIBUTED_CREATIVES_DIR;
		}

		if ( is_null( static::$plugin_version ) ) {
			static::$plugin_version = ADBUTLER_CONTRIBUTED_CREATIVES_VERSION;
		}

		if ( is_null( static::$app_dir ) ) {
			static::$app_dir = dirname( __FILE__ );
		}

		if ( is_null( $this->injector ) ) {
			$this->injector = $injector;
		}

		if ( ! is_array( $this->integrations ) ) {
			$this->integrations = array();
		}

		return $this;
	}

	/**
	 * Setup
	 *
	 * @since  v0.1.0
	 * @return Plugin Instance of self
	 */
	public function setup() {
		$this->run_now()
			->register_actions()
			->register_filters();

		return $this;
	}

	/**
	 * Run now
	 *
	 * Executes operations that are not hooked to actions, but should rather be
	 * ran immediately.  These operations may add their own actions and filters.
	 *
	 * @since  v0.1.0
	 * @return Plugin Instance of self
	 */
	public function run_now() {
		// Meta Boxes
		if( false ) {
			$provider = $this->injector->inject( 'creative_post_meta_provider' );
			add_action( 'init', array( $provider , 'do_post_meta_box' ) );
		}
		return $this;
	}

	/**
	 * Register Integration
	 *
	 * @since  v0.1.0
	 * @param  Plugin_Integration $integration A discreet integration with another part of WP.
	 * @return Plugin                          Instance of self
	 */
	public function register_integration( Plugin_Integration $integration ) {
		array_push( $this->integrations, $integration->add_all_hooks() );

		return $this;
	}

	/**
	 * Register Actions
	 *
	 * @since  v0.1.0
	 * @return Plugin Instance of self
	 */
	public function register_actions() {
		// Post Types
		add_action( 'init', array( $this, 'register_post_types' ) );

		return $this;
	}

	/**
	 * Register Filters
	 *
	 * @since  v0.1.0
	 * @return Plugin Instance of self
	 */
	public function register_filters() {

		return $this;
	}

	/**
	 * Register Post Types
	 *
	 * @since  v0.1.0
	 * @return Plugin Instance of self
	 */
	public function register_post_types() {
		$this->register_adbutler_creative_post_type();
		return $this;
	}

	/**
	 * Register Adbutler Creative Post Type
	 *
	 * Registers a custom post type referenced by adbutler_creative.  Post Type stores
	 * creative line items uploaded by Contributors, Editors, and Administrators to use
	 * on the site, served via AdButler.
	 *
	 * @access protected
	 * @since  v0.1.0
	 * @return Plugin  Instance of self
	 */
	final protected function register_adbutler_creative_post_type() {
		// Arguments specific to this post type
		$args = array(
			'description' => 'Adbutler Campaigns',
			'menu_position' => 20,
			'exclude_from_search' => true,
			'capabilities' => self::get_capabilities(),
			'supports' => [ 'title' ],
			'taxonomies' => []
			);

		// Fill arguments and labels
		$filled_args = $this->fill_post_type_args( $args, 'Ad Campaign', 'Ad Campaigns' );

		// Store reference to the create post type
		array_push( $this->post_types, register_post_type( 'adbutler_campaign', $filled_args ) );

		return $this;
	}

	/**
	 * Get Capabilities
	 *
	 * Returns an array of the custom capabilities passed to the Adbutler Campaign custom
	 * post type.
	 *
	 * @since  v0.1.0
	 * @return array An array of capabilites where key is mapping and value is capability
	 */
	public static function get_capabilities() {
		return array (
			'edit_post' => 'edit_adbutler_campaign',
			'read_post' => 'read_adbutler_campaign',
			'delete_post' => 'delete_adbutler_campaign',
			'edit_posts' => 'edit_adbutler_campaigns',
			'edit_others_posts' => 'edit_others_adbutler_campaigns',
			'publish_posts' => 'publish_adbutler_campaigns',
			'read_private_posts' => 'read_private_adbutler_campaigns'
			);
	}

	/**
	 * Activate
	 *
	 * Runs on plugin activation.  Public accessor to protected activate methods.
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public final static function activate() {
		self::_activate_capabilities();
	}

	/**
	 * Deactivate
	 *
	 * Runs on plugin deactivation.  Public accessor to protected deactivation methods.
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public final static function deactivate() {
		self::_deactivate_capabilities();
	}

	/**
	 * Activate Capabilities
	 *
	 * Grants capabilities that will be set on Custom Post Types or other areas of
	 * functionality to appropriate WP User Roles.
	 *
	 * @see    Lift\AdbutlerUserCampaigns\Helpers\CapabilityUtils
	 * @uses   Lift\AdbutlerUserCampaigns\Helpers\CapabilityUtils::grant_capabilities()
	 * @access protected
	 * @since  v0.1.0
	 * @return void
	 */
	protected static function _activate_capabilities() {
		$caps = array_values( self::get_capabilities() );
		// Grant some capabilities to admins and editors
		$roles = [ 'administrator', 'editor' ];
		foreach ( $roles as $role ) {
			self::grant_capabilities( get_role( $role ), $caps );
		}

		// Just contributors this time, as they get a few less, listed below.
		self::grant_capabilities( get_role( 'contributor' ), array_diff( $caps, array(
			'edit_others_adbutler_creatives',
			'publish_adbutler_creatives',
			'read_private_adbutler_creatives'
			) ) );
	}

	/**
	 * Activate Capabilities
	 *
	 * Removes capabilities that were set on Custom Post Types or other areas of
	 * functionality to appropriate WP User Roles.
	 *
	 * @see    Lift\AdbutlerUserCampaigns\Helpers\CapabilityUtils
	 * @uses   Lift\AdbutlerUserCampaigns\Helpers\CapabilityUtils::ungrant_capabilities()
	 * @access protected
	 * @since  v0.1.0
	 * @return void
	 */
	protected static function _deactivate_capabilities() {
		foreach( [ 'administrator', 'editor', 'contributor' ] as $role ) {
			self::ungrant_capabilities( get_role( $role ), array_values( self::get_capabilities() ) );
		}
	}
}
