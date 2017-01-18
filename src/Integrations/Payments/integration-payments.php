<?php
/**
 * Integration: Payments
 *
 * @package Lift\AdbutlerUserCampaigns\Integrations\Payments
 * @since v0.1.0
 */

namespace Lift\AdbutlerUserCampaigns\Integrations\Payments;
use Lift\Core\Hook_Catalog;
use Lift\Core\Base_Integration;
use Lift\Core\Interfaces\Integration;
use Lift\Core\Interfaces\Provider;
use Lift\AdbutlerUserCampaigns\Providers\Payment_Provider;

/**
 * Class: Integration Integration_Payments
 *
 * @uses Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog;
 * @uses Lift\AdbutlerUserCampaigns\Integrations\Integration;
 * @uses Lift\AdbutlerUserCampaigns\Interfaces\Integration;
 * @uses Lift\AdbutlerUserCampaigns\Interfaces\Provider;
 * @since v0.1.0
 */
class Integration_Payments extends Base_Integration implements Integration {

	/**
	 * Hook Catalog
	 *
	 * @var Hook_Catalog Hook Catalog
	 */
	public $hook_catalog;

	/**
	 * Payment Provider
	 *
	 * @var Payment_Provider Payment Provider
	 */
	public $payment_provider;

	/**
	 * Javascript Callaback
	 *
	 * @var string
	 */
	protected $default_js_callback = 'alert("No Payment Process Configured.")';

	/**
	 * Constructor
	 *
	 * @since v0.1.0
	 * @param Hook_Catalog $hook_catalog The main Hook_Catalog instance.
	 * @param Provider     ...$providers A variable array of providers.
	 * @return  Integration_Payments Self instance
	 */
	public function __construct( Hook_Catalog $hook_catalog, Provider ...$providers ) {
		$this->hook_catalog = $hook_catalog;
		if ( isset( $providers[0] ) && $providers[0] instanceof Payment_Provider ) {
			$this->payment_provider = $providers[0];
		}
		return $this;
	}

	/**
	 * Init Scripts
	 *
	 * @since  v0.1.0
	 * @return bool Methods that must run always return true.
	 */
	public function must_init_scripts() {
		$this->add_hook( 'admin_enqueue_scripts', 'register_scripts' );
		$this->add_hook( 'admin_enqueue_scripts', 'enqueue_scripts' );
		return true;
	}

	/**
	 * [MUST] Print Payment Form
	 *
	 * @since  v0.1.0
	 * @return bool Methods that must run always return true.
	 */
	public function must_print_payment_form() {
		$this->add_hook( 'admin_footer', 'print_payment_form' );
		return true;
	}

	/**
	 * [MUST] Provide no script payment alert
	 *
	 * @return bool Methods that must run always return true.
	 */
	public function must_provide_no_script_payment_alert() {
		$this->add_hook( 'edit_form_after_title', 'ask_for_payment_if_pending' );
		return true;
	}

	/**
	 * Ask For Payment if Pending
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function ask_for_payment_if_pending() {
		global $post;
		$heading = __( 'Payment', 'adbutler-cc' );
		$message = __( 'If you have not already completed payment, you must do so, as your creatives will not be approved until payment is confirmed.  Click on the link below and you will be taken to a secure payment page.', 'adbutler-cc' );
		$button_text = __( 'Continue to Payment', 'adbutler-cc' );
		$callback = $this->payment_provider->get_js_callback() ?
			$this->payment_provider->get_js_callback() :
			$this->default_js_callback;
		if ( 'pending' === $post->post_status || 'publish' === $post->post_status ) : ?>
			<style>
				.payment-nag {
					margin-top: 20px;
				}
			</style>
			<div class="payment-nag postbox">
				<h2 class="hndle"><?php echo esc_html( $heading ); ?></h2>
				<div class="inside">
					<p>
						<?php echo esc_html( $message ); ?>
					</p>
					<p class="adbutler_cc--payment-wrapper">
						<button
							class="button button-primary button-small"
							type="button"
							onclick="<?php echo esc_js( $callback ); ?>">
							<?php echo esc_html( $button_text ); ?>
						</button>
					</p>
				</div>
			</div>
		<?php endif;
	}

	/**
	 * Register External JS
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function register_external_js() {
		if ( defined( 'ADBUTLER_CONTRIBUTED_CREATIVES_TEST' ) && ADBUTLER_CONTRIBUTED_CREATIVES_TEST ) {
			wp_register_script( 'adbutler_cc\acceptjs', 'https://jstest.authorize.net/v1/Accept.js', [], ADBUTLER_CONTRIBUTED_CREATIVES_VERSION, true );
		} else {
			wp_register_script( 'adbutler_cc\acceptjs', 'https://js.authorize.net/v1/Accept.js', [], ADBUTLER_CONTRIBUTED_CREATIVES_VERSION, true );
		}
	}

	/**
	 * Register Scripts
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function register_scripts() {
		// Register Externals for Providers to use as dependencies.
		$this->register_external_js();

		// Register the Bundle.
		$url = ADBUTLER_CONTRIBUTED_CREATIVES_URI . '/scripts/bundle.js';
		$ver = ADBUTLER_CONTRIBUTED_CREATIVES_VERSION;
		wp_register_script( 'adbutler_cc\bundle', $url, [], $ver, true );

		// Load Provider Javascript.
		$this->payment_provider->load_javascript();
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'adbutler_cc\bundle' );
	}

	/**
	 * Print Payment Form
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function print_payment_form() {
		?>
		<form name="PrePage" method = "post" action = "https://Simplecheckout.authorize.net/payment/CatalogPayment.aspx" id="adbutler_cc--payment-form" target="_blank"> <input type = "hidden" name = "LinkId" value ="a7cf5c8a-71bd-48d7-9948-36abe11e28fe"/></form>
		<?php
	}
}
