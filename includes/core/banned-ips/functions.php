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
 * Check if an IP address is banned from making purchases.
 *
 * This function checks whether the provided IP address is in the list
 * of banned IPs, which includes individual IPs, IP blocks, or CIDR ranges.
 *
 * @param string $ip The IP address to check.
 *
 * @return bool Whether the IP address is banned.
 */
function is_ip_banned( $ip = '' ): bool {
// Trim the IP address and check if it's empty
	$ip = trim( $ip );
	if ( empty( $ip ) || ! is_valid_ip( $ip ) ) {
		return false;
	}

	// Convert IP to lowercase and get banned IPs
	$ip         = strtolower( $ip );
	$banned_ips = get_banned_ips();

	// If banned IPs are not available, return false
	if ( ! is_array( $banned_ips ) || empty( $banned_ips ) ) {
		return false;
	}

	// Initialize the return value
	$return = false;

	// Loop through banned IPs to check against provided IP
	foreach ( $banned_ips as $banned_ip ) {
		$banned_ip = strtolower( $banned_ip );

		// Check if the banned IP is an IP range (CIDR notation)
		if ( is_valid_ip_range( $banned_ip ) ) {
			if ( is_ip_in_range( $ip, $banned_ip ) ) {
				$return = true;
				break;
			}
		} else {
			// Check for an exact IP match
			if ( $banned_ip === $ip ) {
				$return = true;
				break;
			}
		}
	}

	// Apply a filter and return the result
	return apply_filters( 'edd_is_ip_banned', $return, $ip );
}

/**
 * Validate an IP address (IPv4 or IPv6).
 *
 * This function validates the given IP address using the FILTER_VALIDATE_IP filter,
 * which supports both IPv4 and IPv6 addresses.
 *
 * @param string $ip The IP address to validate.
 *
 * @return bool True if the IP address is valid, false otherwise.
 */
function is_valid_ip( $ip ): bool {
	return filter_var( $ip, FILTER_VALIDATE_IP ) !== false;
}

/**
 * Checks if a given input is a valid IP address or IP range in CIDR format.
 *
 * This function validates whether the provided input is either a valid IP address or
 * a valid IP range in CIDR format. It supports IPv4 addresses, IPv6 addresses, and CIDR
 * notation for both IPv4 and IPv6.
 *
 * @param string $ip_or_range The IP address or range to validate. Examples: "192.168.1.1",
 *                            "2001:0db8::1", "192.168.1.0/24", "2001:0db8::/32".
 *
 * @return bool True if the input is a valid IP address or IP range, false otherwise.
 *
 * @example is_valid_ip_or_range('192.168.1.1') Returns true.
 * @example is_valid_ip_or_range('2001:0db8::1') Returns true.
 * @example is_valid_ip_or_range('192.168.1.0/24') Returns true.
 * @example is_valid_ip_or_range('2001:0db8::/32') Returns true.
 * @example is_valid_ip_or_range('invalid-ip') Returns false (invalid IP format).
 * @example is_valid_ip_or_range('192.168.1.0/33') Returns false (invalid CIDR range).
 * @example is_valid_ip_or_range('2001:0db8::/129') Returns false (invalid CIDR range).
 */
function is_valid_ip_or_range( $ip_or_range ): bool {
	if ( empty( $ip_or_range ) ) {
		return false;
	}

	return is_valid_ip( $ip_or_range ) || is_valid_ip_range( $ip_or_range );
}

/**
 * Generate a CIDR notation from an IP address and prefix length.
 *
 * This function takes an IP address (IPv4 or IPv6), verifies the input, masks it with the prefix length,
 * and returns the CIDR notation.
 *
 * @param string $ip_address    The IP address (IPv4 or IPv6).
 * @param int    $prefix_length The prefix length (e.g., 16, 24, 64, etc.).
 *
 * @return string|false The CIDR notation, or false on invalid input.
 */
function ip_to_cidr( $ip_address, $prefix_length ): bool|string {
	// Validate IP address
	if ( ! filter_var( $ip_address, FILTER_VALIDATE_IP ) ) {
		return false; // Invalid IP address
	}

	// Validate prefix length
	if ( $prefix_length < 0 || ( $prefix_length > 32 && filter_var( $ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) || ( $prefix_length > 128 && filter_var( $ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) ) {
		return false; // Invalid prefix length
	}

	return $ip_address . '/' . $prefix_length;
}