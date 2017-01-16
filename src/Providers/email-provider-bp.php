<?php
/**
 * BuddyPress Email Provider
 *
 * @since  v0.1.0
 * @package  AdbutlerUserCampaigns
 * @subpackage Providers
 */

namespace Lift\AdbutlerUserCampaigns\Providers;
use Lift\AdbutlerUserCampaigns\Interfaces\Provider;
use Lift\AdbutlerUserCampaigns\Integrations\Hook_Catalog;
use Lift\AdbutlerUserCampaigns\Integrations\Hook_Definition;

/**
 * Class: BP_Email_Provider
 *
 * @todo  We should have BuddyPress send the default emails if possible.
 * @since  v0.1.0
 */
class BP_Email_Provider extends Email_Provider implements Provider {

	/**
	 * Activate
	 *
	 * Runs required activation functions
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	public function activate() {
		$this->_create_email_situations();
		$this->_create_site_admin_notification();
		$this->_create_campaign_author_notification();
	}

	/**
	 * Notify Site Admin
	 *
	 * @since  v0.1.0
	 * @param  \WP_Post $post WP_Post object.
	 * @return bool|void
	 */
	public function notify_site_admin( \WP_Post $post ) {
		if ( ! $author = intval( $post->post_author ) ) {
			return false;
		}
		$author = get_userdata( $author );

		if ( user_can( $author, 'publish_adbutler_campaigns' ) ) {
			return false;
		}

		$admins = apply_filters( 'adbutler\admin_notification_list', [ get_option( 'admin_email' ) ] );
		$args = array(
			'tokens' => array(
				'site.name' => get_bloginfo( 'name' ),
				'campaign.title' => $post->post_title,
				'campaign.author' => $author->data->display_name,
				'campaign.link' => get_edit_post_link( $post->ID ),
			),
		);

		$mail = bp_send_email( 'ad_campaign_submission', $admins, $args );

		if ( is_wp_error( $mail ) ) {
			parent::notify_site_admin( $post );
		}
	}

	/**
	 * Notify Campaign Author
	 *
	 * @since  v0.1.0
	 * @param  \WP_Post $post WP_Post object.
	 * @return bool|void
	 */
	public function notify_campaign_author( \WP_Post $post ) {
		if ( ! $author = intval( $post->post_author ) ) {
			return false;
		}
		$author = get_userdata( $author );

		if ( user_can( $author, 'publish_adbutler_campaigns' ) ) {
			return false;
		}

		$admins = apply_filters( 'adbutler\admin_notification_list', [ get_option( 'admin_email' ) ] );
		$args = array(
			'tokens' => array(
				'site.name' => get_bloginfo( 'name' ),
				'campaign.title' => $post->post_title,
				'campaign.author' => $author->data->display_name,
				'campaign.link' => get_edit_post_link( $post->ID ),
			),
		);

		$mail = bp_send_email( 'ad_campaign_approval', $admins, $args );

		if ( is_wp_error( $mail ) ) {
			parent::notify_campaign_author( $post );
		}
	}

	/**
	 * Create Email Situations
	 *
	 * Creates two situations where BuddyPress should send emails:
	 *  - When a contributor submits a new campaign for review.
	 *  - When an administrator approves a campaign to be published on the site.
	 *
	 * @since  v0.1.0
	 * @return void
	 */
	protected function _create_email_situations() {
		$submission = get_term_by( 'slug', 'ad_campaign_submission', bp_get_email_tax_type() );
		if ( ! $submission instanceof \WP_Term ) {
			$submission = wp_insert_term( 'ad_campaign_submission', bp_get_email_tax_type(), [
				'description' => 'A contributor submits a new advertising campaign for review.',
			] );
		}

		$approval = get_term_by( 'slug', 'ad_campaign_approval', bp_get_email_tax_type() );
		if ( ! $approval instanceof \WP_Term ) {
			$approval = wp_insert_term( 'ad_campaign_approval', bp_get_email_tax_type(), [
				'description' => 'A site administrator approves a submitted advertising campaign.',
			] );
		}
	}

	/**
	 * Create Site Admin Notification
	 *
	 * Creates a BuddyPress Email Template to be sent to administrators when a new campaign
	 * is submitted to review.  Run once on activation if no email exists with this name.
	 *
	 * @return void|null
	 */
	protected function _create_site_admin_notification() {
		if ( $this->_site_admin_notification_exists() ) {
			return;
		}

		$block = function( $text ) {
			return $text . PHP_EOL;
		};

		$message = $block( '[{{{campaign.author}}}] has submitted a new ad campaign for your review:' );
		$message .= $block( 'Title: [{{{campaign.title}}}]' );
		$message .= $block( 'Link: [{{{campaign.link}}}]' );

		$post_id = wp_insert_post( [
			'post_title' => '[{{{site.name}}}]: New Ad Campaign',
			'post_content' => $message,
			'post_excerpt' => $message,
			'post_status' => 'publish',
			'post_type' => bp_get_email_post_type(),
		] );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$tt_ids = wp_set_object_terms( $post_id, 'ad_campaign_submission', bp_get_email_tax_type() );
		}
	}

	/**
	 * Create Campaign Author Notification
	 *
	 * Creates a BuddyPress Email to notify campaign authors when their campaigns have been
	 * approved.  Runs once on activation is no email exists with the given name.
	 *
	 * @since  v0.1.0
	 * @return void|null
	 */
	protected function _create_campaign_author_notification() {
		if ( $this->_site_admin_notification_exists() ) {
			return;
		}

		$block = function( $text ) {
			return $text . PHP_EOL;
		};

		$message = $block( 'The campaign [{{{campaign.title}}}] has been approved!' );
		$message .= $block( 'Link: [{{{campaign.link}}}]' );

		$post_id = wp_insert_post( [
			'post_title' => '[{{{site.name}}}]: Ad Campaign Approved',
			'post_content' => $message,
			'post_excerpt' => $message,
			'post_status' => 'publish',
			'post_type' => bp_get_email_post_type(),
		] );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$tt_ids = wp_set_object_terms( $post_id, 'ad_campaign_approval', bp_get_email_tax_type() );
		}
	}

	/**
	 * Site Admin Notification Exists
	 *
	 * Check if the Site Admin Notification exists as a BuddyPress email.
	 *
	 * @since  v0.1.0
	 * @return bool True if it exists, false otherwise.
	 */
	protected function _site_admin_notification_exists() {
		if ( ! function_exists( 'post_exists' ) ) {
			require_once( ABSPATH . '/wp-admin/post.php' );
		}

		$post_exists = post_exists( '[{{{site.name}}}] New Ad Campaign' );

		if ( 0 !== $post_exists && 'publish' === get_post_status( $post_exists ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Campaign Author Notification Exists
	 *
	 * Checks if the Author Notification exists as BuddyPress email.
	 *
	 * @since  v0.1.0
	 * @return bool True if the email exists, false otherwise
	 */
	protected function _campaign_author_notification_exists() {
		if ( ! function_exists( 'post_exists' ) ) {
			require_once( ABSPATH . '/wp-admin/post.php' );
		}

		$post_exists = post_exists( '[{{{site.name}}}] Your Ad Campaign was Approved' );

		if ( 0 !== $post_exists && 'publish' === get_post_status( $post_exists ) ) {
			return true;
		}
		return false;
	}
}
