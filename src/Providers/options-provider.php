<?php
/**
 * Options Provider
 *
 * @since  v0.1.0
 * @package  AdbutlerUserCampaigns
 * @subpackage Providers
 */

namespace Lift\AdbutlerUserCampaigns\Providers;
use Lift\Core\Interfaces\Provider;
use Lift\Core\Utils\Admin\Sub_Menu;

/**
 * Class: Options_Provider
 *
 * Default Options Provider that just uses core options store and has no real outside dependencies.
 *
 * @since  v0.1.0
 */
class Options_Provider implements Provider {
	use Sub_Menu;

	/**
	 * Option Name
	 *
	 * @var string
	 */
	protected $option_name = 'adbutler_api_options';

	/**
	 * Hook Suffix
	 *
	 * @var string|null
	 */
	protected $hook_suffix;

	/**
	 * Provide
	 *
	 * @since  v0.1.0
	 * @param  mixed[] ...$args Arguments.
	 * @return mixed|array      Array of options, or a single option value if passed as first argument
	 */
	public function provide( ...$args ) {
		$options = get_option( $this->option_name );

		if ( ! empty( $args ) && isset( $options[ $args[0] ] ) ) {
			return $options[ $args[0] ];
		}
		return $options;
	}

	/**
	 * Setup
	 *
	 * @since  v0.1.0
	 * @return Options_Provider Instance of self.
	 */
	public function setup() {
		$this->add_page()
			->register_settings()
			->add_submenu_section( 'main', 'Settings', '\__return_false' )
			->add_submenu_field( $this->option_name, [ 'input' => 'password' ], 'api_key', 'API Key' );

		return $this;
	}

	/**
	 * Add Page
	 *
	 * @since  v0.1.0
	 * @return Options_Provider Instance of self.
	 */
	public function add_page() {
		$this->hook_suffix = \add_submenu_page(
			$this->set_submenu_page( 'parent_slug', 'edit.php?post_type=adbutler_campaign' ),
			$this->set_submenu_page( 'page_title', 'Options' ),
			$this->set_submenu_page( 'menu_title', 'Options' ),
			$this->set_submenu_page( 'capability', 'manage_options' ),
			$this->set_submenu_page( 'menu_slug', 'adbutler_api' ),
			$this->set_submenu_page( 'callback', array( $this, 'render_submenu_page' ) )
		);
		return $this;
	}

	/**
	 * Register Settings
	 *
	 * @since  v0.1.0
	 * @return Options_Provider Instance of self.
	 */
	public function register_settings() {
		register_setting( $this->option_name, $this->option_name );
		return $this;
	}
}
