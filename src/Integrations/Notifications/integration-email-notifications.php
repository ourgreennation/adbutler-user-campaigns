<?php
/**
 * Integration: Email Notification
 *
 * @package Lift\AdbutlerUserCampaigns\Integrations\Notifications
 * @since v0.1.0
 */

namespace Lift\AdbutlerUserCampaigns\Integrations\Notifications;
use Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog;
use Lift\AdbutlerUserCampaigns\Integrations\Integration;
use Lift\AdbutlerUserCampaigns\Interfaces\Plugin_Integration;
use Lift\AdbutlerUserCampaigns\Interfaces\Provider;
use Lift\AdbutlerUserCampaigns\Providers\Email_Provider;

/**
 * Class: Integration Integration_Payments
 *
 * @uses Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog
 * @uses Lift\AdbutlerUserCampaigns\Integrations\Integration
 * @uses Lift\AdbutlerUserCampaigns\Interfaces\Plugin_Integration
 * @uses Lift\AdbutlerUserCampaigns\Interfaces\Provider
 * @since v0.1.0
 */
class Integration_Email_Notifications extends Integration implements Plugin_Integration {
	/**
	 * @var Email_Provider
	 */
	public $email_provider;

	/**
	 * Hook Catalog
	 *
	 * @var Hook_Catalog Instance of Hook_Catalog
	 */
	public $hook_catalog;

	/**
	 * Constructor
	 *
	 * @since  v0.1.0
	 * @param Hook_Catalog $hook_catalog Instance of Hook_Catalog to register hooks to.
	 * @return Integration_Email_Notification Instance of self
	 */
	public function __construct( Hook_Catalog $hook_catalog, Provider ... $providers ) {
		$this->hook_catalog = $hook_catalog;

		if ( isset( $providers[0] ) && $providers[0] instanceof Email_Provider ) {
			$this->email_provider = $providers[0];
		}

		return $this;
	}

	/**
	 * Maybe notify site admin of pending campaigns
	 *
	 * @since  v0.1.0
	 * @return bool True if admin should be notified, false otherwise
	 */
	public function maybe_notify_site_admin_of_pending_campaign() {
		if ( apply_filters( 'adbutler_cc\maybe_notify_site_admin_of_pending_campaign', true ) ) {
			$this->add_hook( 'transition_post_status', 'notify_site_admin', 10, 3 );
			return true;
		}
		return false;
	}

	/**
	 * Maybe notify campaign author of approval
	 *
	 * @since v0.1.0
	 * @return bool True if author should be notified of approval, false otherwise.
	 */
	public function maybe_notify_campaign_author_of_approval() {
		if ( apply_filters( 'adbutler_cc\maybe_notify_campaign_author_of_approval', true ) ) {
			$this->add_hook( 'transition_post_status', 'notify_campaign_author', 10, 3 );
			return true;
		}
		return false;
	}

	/**
	 * Notify Site Admin
	 *
	 * @since  v0.1.0
	 * @param  string   $new_status The new status.
	 * @param  string   $old_status The old status.
	 * @param  \WP_Post $post       WP_Post object.
	 * @return bool                 True if email was sent, false otherwise.
	 */
	public function notify_site_admin( $new_status, $old_status, \WP_Post $post ) {
		// Check if we should even do anything.
		if ( 'adbutler_campaign' !== $post->post_type ) {
			return false;
		}

		// Make Sure Post Stati matches our desired scenario ( to pending from ! pending ).
		if ( 'pending' === $old_status || 'pending' !== $new_status ) {
			return false;
		}

		if ( ! $this->email_provider ) {
			return false;
		}
		return $this->email_provider->notify_site_admin( $post );
	}

	/**
	 * Notify Site Admin
	 *
	 * @since  v0.1.0
	 * @param  string   $new_status The new status.
	 * @param  string   $old_status The old status.
	 * @param  \WP_Post $post       WP_Post object.
	 * @return bool                 True if email was sent, false otherwise.
	 */
	public function notify_campaign_author( $new_status, $old_status, \WP_Post $post ) {
		// Check if we should even do anything.
		if ( 'adbutler_campaign' !== $post->post_type ) {
			return false;
		}

		// Make Sure Post Stati matches our desired scenario ( to publish from  pending ).
		if ( 'pending' !== $old_status || 'publish' !== $new_status ) {
			return false;
		}

		if ( ! $this->email_provider ) {
			return false;
		}
		return $this->email_provider->notify_campaign_author( $post );
	}
}
