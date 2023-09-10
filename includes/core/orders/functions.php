<?php
/**
 * Order Functions
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
 * Check if a customer has any orders based on provided email addresses.
 *
 * This function accepts an array of email addresses or a single email address as a string.
 * It checks whether the customer associated with the email address has any orders.
 *
 * @param array|string $user_emails Array or single email address to check.
 *
 * @return bool Whether the customer has any orders, or false if no valid input.
 */
function has_customer_order( $user_emails ): bool {
	// Ensure $user_emails is an array even if a single email is passed as a string
	if ( is_string( $user_emails ) ) {
		$user_emails = array( $user_emails );
	}

	// Validate input and perform check only if there are non-empty email addresses
	if ( is_array( $user_emails ) && ! empty( array_filter( $user_emails ) ) ) {
		$customer_ids = array();

		foreach ( $user_emails as $user_email ) {
			$user_email = trim( $user_email );
			$customer   = edd_get_customer_by( 'email', $user_email );

			if ( $customer ) {
				$customer_ids[] = absint( $customer->id );
			}
		}

		// Filter out duplicate customer IDs
		$customer_ids = array_unique( $customer_ids );

		// Prepare query args for retrieving orders
		$query_args = array(
			'customer_id__in' => $customer_ids,
			'order'           => 'DESC',
			'status'          => edd_get_net_order_statuses(),
			'type'            => 'sale',
			'number'          => 1,
		);

		// Check if any orders exist for the provided customer IDs
		$orders = \edd_get_orders( $query_args );

		return ! empty( $orders );
	} else {
		// Return false if input is invalid or empty
		return false;
	}
}