<?php
/**
 * Banned IP Functions
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
 * Retrieve the list of banned IP addresses.
 *
 * This function retrieves the list of IP addresses that have been banned
 * from making purchases. The list includes individual IPs, IP blocks, or CIDR ranges.
 *
 * @return array List of banned IP addresses.
 */
function get_banned_ips(): array {
	// Retrieve the banned IPs setting
	$banned = edd_get_option( 'banned_ips', array() );

	// If banned IPs are not in array format, split by newlines
	$ips = ! is_array( $banned )
		? explode( "\n", $banned )
		: $banned;

	// Trim each IP address to remove whitespace
	$ips = array_map( 'trim', $ips );

	// Apply a filter and return the list of banned IPs
	return apply_filters( 'edd_get_banned_ips', $ips );
}

/**
 * Add an IP address to the banned list.
 *
 * @param string $ip The IP address to add to the banned list.
 *
 * @return bool Whether the IP address was successfully added to the list.
 */
function add_ip_to_banned_list( $ip ): bool {
	$banned_ips = edd_get_option( 'banned_ips', array() );

	// Trim and validate the IP address
	$ip = trim( $ip );
	if ( empty( $ip ) || ! is_valid_ip_or_range( $ip ) ) {
		return false;
	}

	// Check if the IP is already in the list
	if ( in_array( $ip, $banned_ips ) ) {
		return false;
	}

	// Add the IP to the list and update the option
	$banned_ips[] = $ip;
	edd_update_option( 'banned_ips', $banned_ips );

	// IP Added to Banned List
	do_action( 'edd_add_ip_to_banned_list', $ip );

	return true;
}

/**
 * Remove an IP address from the banned list.
 *
 * @param string $ip The IP address to remove from the banned list.
 *
 * @return bool Whether the IP address was successfully removed from the list.
 */
function remove_ip_from_banned_list( $ip ): bool {
	$banned_ips = edd_get_option( 'banned_ips', array() );

	// Trim and validate the IP address
	$ip = trim( $ip );
	if ( empty( $ip ) || ! is_valid_ip_or_range( $ip ) ) {
		return false;
	}

	// Check if the IP is in the list
	$key = array_search( $ip, $banned_ips );
	if ( false === $key ) {
		return false;
	}

	// Remove the IP from the list and update the option
	unset( $banned_ips[ $key ] );
	edd_update_option( 'banned_ips', $banned_ips );

	// IP Removed from Banned List
	do_action( 'edd_remove_ip_from_banned_list', $ip );

	return true;
}
