<?php
/**
 * Banned Actions
 *
 * @package   edd-banned-ips
 * @copyright Copyright (c) 2023, ArrayPress Limited
 * @license   GPL2+
 * @since     1.0.0
 */

namespace ArrayPress\EDD\Banned_IPs;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check purchase IP and user emails for potential issues.
 *
 * This function checks if the purchase IP is banned and validates user emails during checkout.
 *
 * @param array $valid_data The validated data for the purchase.
 * @param array $posted     The posted data from the checkout form.
 */
function check_purchase_ip( $valid_data, $posted ): void {
	// Retrieve the list of banned IP addresses
	$banned = get_banned_ips();

	// If no IP addresses are banned, return early
	if ( empty( $banned ) ) {
		return;
	}

	$user_emails = array( $posted['edd_email'] );

	// Check if the user is logged in
	if ( is_user_logged_in() ) {
		// The user is logged in, check that their account email is not banned
		$user_data     = get_userdata( get_current_user_id() );
		$user_emails[] = $user_data->user_email;
	} // Check if the user is trying to log in
	elseif ( isset( $posted['edd-purchase-var'] ) && $posted['edd-purchase-var'] == 'needs-to-login' ) {
		// The user is logging in, check that their email is not banned
		if ( $user_data = get_user_by( 'login', $posted['edd_user_login'] ) ) {
			$user_emails[] = $user_data->user_email;
		}
	}

	// Check if existing customers are allowed to bypass the IP check
	$allow_existing = (bool) edd_get_option( 'allow_existing_customers', false );

	// If allowed and user has existing orders, return early
	if ( $allow_existing && has_customer_order( $user_emails ) ) {
		return;
	}

	// Check if the purchase IP is banned
	if ( is_ip_banned( edd_get_ip() ) || is_user_agent_banned( get_user_agent() ) ) {
		// Set a custom error message for banned IP addresses
		$custom_message = edd_get_option( 'banned_ip_message', __( 'An internal error has occurred, please try again or contact support.', 'edd-banned-ips' ) );
		edd_set_error( 'ip_banned', $custom_message );
	}
}

add_action( 'edd_checkout_error_checks', __NAMESPACE__ . '\\check_purchase_ip', 10, 2 );