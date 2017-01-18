<?php
/**
 * Main Plugin File
 *
 * @package  AdbutlerUserCampaigns
 */

namespace Lift\AdbutlerUserCampaigns;

// Integrations.
use Lift\Core\Hook_Catalog;
use Lift\Core\Interfaces\Integration;

// Helpers.
use Lift\AdbutlerUserCampaigns\Helpers\Capability_Utils;
use Lift\AdbutlerUserCampaigns\Helpers\Post_Type_Utils;
use Lift\AdbutlerUserCampaigns\Providers\BP_Email_Provider;

/**
 * Class: Plugin
 *
 * Main Plugin Class
 *
 * @since  v.0.1.0
 */
final class Plugin {
	// Adds utilities to grant capabilities to .
	use Capability_Utils;

	// Adds helper functions to register new post types.
	use Post_Type_Utils;

	/**
	 * Path the root plugin directory
	 *
	 * @var string
	 */
	public static $plugin_dir;

	/**
	 * Plugin version
	 *
	 * @var string SEMVER version
	 */
	public static $plugin_version;

	/**
	 * Path the application root directory
	 *
	 * @var string
	 */
	public static $app_dir;

	/**
	 * Dependency Injector
	 *
	 * @var Dependency_Injector
	 */
	public $injector;

	/**
	 * Integrations
	 *
	 * @var Integration[] Array of Integrations
	 */
	protected $integrations;

	/**
	 * Post Types
	 *
	 * @var \WP_Post_Type[] An array of \WP_Post_Type objects
	 */
	protected $post_types = array();

	/**
	 * Constructor
	 *
	 * @param  Dependency_Injector $injector An instance of Dependency_Injector.
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
		// Meta Boxes.
		if ( apply_filters( 'adbutler_cc_build_acf_fields_with_function', false ) ) {
			$provider = $this->injector->inject( 'creative_post_meta_provider' );
			add_action( 'init', array( $provider, 'do_post_meta_box' ) );
		}
		return $this;
	}

	/**
	 * Register Integration
	 *
	 * @since  v0.1.0
	 * @param  Integration $integration A discreet integration with another part of WP.
	 * @return Plugin                          Instance of self
	 */
	public function register_integration( Integration $integration ) {
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
		// Post Types.
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
	protected function register_adbutler_creative_post_type() {
		// Arguments specific to this post type.
		$args = array(
			'description' => 'Adbutler Campaigns',
			'menu_position' => 20,
			'menu_icon' => 'dashicons-megaphone',
			'exclude_from_search' => true,
			'capabilities' => self::get_capabilities(),
			'supports' => [ 'title', 'author' ],
			'taxonomies' => [],
			);

		// Fill arguments and labels.
		$filled_args = $this->fill_post_type_args_labels( $this->fill_post_type_args( $args ) );

		// Store reference to the create post type.
		array_push( $this->post_types, register_post_type( 'adbutler_campaign', $filled_args ) );

		return $this;
	}

	/**
	 * Fill Post Type Args Labels
	 *
	 * @since  v0.1.0
	 * @param  array $args An array of post type arguments.
	 * @return array       An array of post type arguments with labels
	 */
	protected function fill_post_type_args_labels( $args ) {
		$args['label'] = 'Ad Campaign';
		$args['labels'] = array(
			'name'                  => _x( 'Ad Campaigns', 'Post Type General Name', 'adbutler-cc-i18n' ),
			'singular_name'         => _x( 'Ad Campaign', 'Post Type Singular Name', 'adbutler-cc-i18n' ),
			'menu_name'             => __( 'Ad Campaigns', 'adbutler-cc-i18n' ),
			'name_admin_bar'        => __( 'Ad Campaign', 'adbutler-cc-i18n' ),
			'archives'              => __( 'Ad Campaign Archives', 'adbutler-cc-i18n' ),
			'attributes'            => __( 'Ad Campaign Attributes', 'adbutler-cc-i18n' ),
			'parent_item_colon'     => __( 'Ad Campaign Parent Item:', 'adbutler-cc-i18n' ),
			'all_items'             => __( 'All Ad Campaigns', 'adbutler-cc-i18n' ),
			'add_new_item'          => __( 'Add New Ad Campaign', 'adbutler-cc-i18n' ),
			'add_new'               => __( 'Add New', 'adbutler-cc-i18n' ),
			'new_item'              => __( 'New Ad Campaign', 'adbutler-cc-i18n' ),
			'edit_item'             => __( 'Edit Ad Campaign', 'adbutler-cc-i18n' ),
			'update_item'           => __( 'Update Ad Campaign', 'adbutler-cc-i18n' ),
			'view_item'             => __( 'View Ad Campaign', 'adbutler-cc-i18n' ),
			'view_items'            => __( 'View Ad Campaigns', 'adbutler-cc-i18n' ),
			'search_items'          => __( 'Search Ad Campaign', 'adbutler-cc-i18n' ),
			'not_found'             => __( 'Not found', 'adbutler-cc-i18n' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'adbutler-cc-i18n' ),
			'featured_image'        => __( 'Featured Image', 'adbutler-cc-i18n' ),
			'set_featured_image'    => __( 'Set featured image', 'adbutler-cc-i18n' ),
			'remove_featured_image' => __( 'Remove featured image', 'adbutler-cc-i18n' ),
			'use_featured_image'    => __( 'Use as featured image', 'adbutler-cc-i18n' ),
			'insert_into_item'      => __( 'Insert into item', 'adbutler-cc-i18n' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Ad Campaign', 'adbutler-cc-i18n' ),
			'items_list'            => __( 'Ad Campaigns list', 'adbutler-cc-i18n' ),
			'items_list_navigation' => __( 'Ad Campaigns list navigation', 'adbutler-cc-i18n' ),
			'filter_items_list'     => __( 'Filter Ad Campaigns list', 'adbutler-cc-i18n' ),
			);

		return $args;
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
		return array(
			'edit_post' => 'edit_adbutler_campaign',
			'read_post' => 'read_adbutler_campaign',
			'delete_post' => 'delete_adbutler_campaign',
			'edit_posts' => 'edit_adbutler_campaigns',
			'edit_others_posts' => 'edit_others_adbutler_campaigns',
			'publish_posts' => 'publish_adbutler_campaigns',
			'read_private_posts' => 'read_private_adbutler_campaigns',
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
	public static function activate() {
		self::_activate_capabilities();
		self::_buddypress_emails();
	}

	/**
	 * Deactivate
	 *
	 * Runs on plugin deactivation.  Public accessor to protected deactivation methods.
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public static function deactivate() {
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
		// Grant some capabilities to admins and editors.
		$roles = [ 'administrator', 'editor' ];
		foreach ( $roles as $role ) {
			self::grant_capabilities( get_role( $role ), $caps );
		}

		// Just contributors this time, as they get a few less, listed below.
		self::grant_capabilities( get_role( 'contributor' ), $limited_caps = array_diff( $caps, array(
			'edit_others_adbutler_campaigns',
			'publish_adbutler_campaigns',
			'read_private_adbutler_campaigns',
		) ) );

		// Grant Cababiliites to other roles as well.
		foreach ( apply_filters( 'adbutler_cc_additional_roles', [] ) as $role ) {
			self::grant_capabilities( get_role( $role ), $limited_caps );
		}
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
		$roles = array_merge( apply_filters( 'adbutler_cc_additional_roles', [] ), [
			'administrator',
			'editor',
			'contributor',
		] );
		foreach ( $roles as $role ) {
			self::ungrant_capabilities( get_role( $role ), array_values( self::get_capabilities() ) );
		}
	}

	/**
	 * Setup Buddy Press Custom Emails
	 *
	 * If BuddyPress is installed and running, upon activation this function will setup
	 * our custom emails.
	 *
	 * @uses Lift\AdbutlerUserCampaigns\Providers\BP_Email_Provider::activate();
	 * @return void
	 */
	protected static function _buddypress_emails() {
		if ( class_exists( '\\BuddyPress' ) ) {
			$provider = new BP_Email_Provider;
			$provider->activate();
		}
	}
}
