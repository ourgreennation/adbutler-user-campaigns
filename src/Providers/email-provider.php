<?php
/**
 * Email Provider
 *
 * @since  v0.1.0
 * @package  AdbutlerUserCampaigns
 * @subpackage Providers
 */

namespace Lift\AdbutlerUserCampaigns\Providers;
use Lift\AdbutlerUserCampaigns\Interfaces\Provider;

/**
 * Class: Email_Provider
 *
 * Default Email Provider that just uses core wp_mail and has no real outside dependencies.
 *
 * @since  v0.1.0
 */
class Email_Provider implements Provider {

	/**
	 * Provide
	 *
	 * @since  v0.1.0
	 * @param  mixed[] ...$args Arguments.
	 * @return Email_Provider   Instance of self
	 */
	public function provide( ...$args ) {
		return $this;
	}

	/**
	 * Notify Site Admin
	 *
	 * @since  v0.1.0
	 * @param  \WP_Post $post       WP_Post object.
	 * @return bool                 True if email was sent, false otherwise.
	 */
	public function notify_site_admin( \WP_Post $post ) {
		if ( ! $author = intval( $post->post_author ) ) {
			return false;
		}
		$author = get_userdata( $author );

		if ( user_can( $author, 'publish_adbutler_campaigns' ) ) {
			return false;
		}

		$admins = apply_filters( 'adbutler_cc_admin_notification_list', [ get_option( 'admin_email' ) ] );
		$subject = sprintf( '%s Has Submitted an Ad Campaign for your Review', $author->data->display_name );
		$message = sprintf( 'Click here to review: %s.', get_edit_post_link( $post->ID, '' ) );

		return wp_mail( $admins, $subject, $message );
	}

	/**
	 * Notify Site Admin
	 *
	 * @since  v0.1.0
	 * @param  \WP_Post $post       WP_Post object.
	 * @return bool                 True if email was sent, false otherwise.
	 */
	public function notify_campaign_author( \WP_Post $post ) {
		if ( ! $author = intval( $post->post_author ) ) {
			return false;
		}
		$author = get_userdata( $author );

		if ( user_can( $author, 'publish_adbutler_campaigns' ) ) {
			return false;
		}

		$subject = 'Your Ad Campaign is now live!';
		$message = sprintf( 'Your Ad Campaign, %s, was just published.', esc_html( $post->post_title ) );

		return wp_mail( $author->user_email, $subject, $message );
	}
}
