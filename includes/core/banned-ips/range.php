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
 * Checks if an IP address or range is in a valid format (IPv4, IPv6, CIDR).
 *
 * This function validates whether the provided IP address or range is in a valid
 * format. It supports IPv4 addresses, IPv6 addresses, and CIDR notation for both
 * IPv4 and IPv6.
 *
 * @param string $ip The IP address or range to validate. Examples: "192.168.1.1", "2001:0db8::1", "192.168.1.0/24",
 *                   "2001:0db8::/32".
 *
 * @return bool True if the format is valid, false otherwise.
 *
 * @example is_valid_ip_range('192.168.1.1') Returns true.
 * @example is_valid_ip_range('2001:0db8::1') Returns true.
 * @example is_valid_ip_range('192.168.1.0/24') Returns true.
 * @example is_valid_ip_range('2001:0db8::/32') Returns true.
 * @example is_valid_ip_range('invalid-ip') Returns false (invalid IP format).
 * @example is_valid_ip_range('192.168.1.0/33') Returns false (invalid CIDR range).
 * @example is_valid_ip_range('2001:0db8::/129') Returns false (invalid CIDR range).
 */
function is_valid_ip_range( $ip ): bool {
	if ( empty( $ip ) || is_array( $ip ) ) {
		return false; // Empty value or array, invalid format
	}

	// Check for IPv4 or IPv6 CIDR format
	if ( strpos( $ip, '/' ) !== false ) {
		list( $ip, $subnet ) = explode( '/', $ip, 2 );

		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			if ( is_numeric( $subnet ) && $subnet >= 0 && $subnet <= 32 ) {
				return true; // Valid IPv4 CIDR format
			}
		} elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			if ( is_numeric( $subnet ) && $subnet >= 0 && $subnet <= 128 ) {
				return true; // Valid IPv6 CIDR format
			}
		}
	}

	return false; // Invalid CIDR format
}

/**
 * Checks if an IP address is within a specified range in CIDR format.
 *
 * This function validates whether the provided IP address falls within the specified
 * CIDR range. It supports both IPv4 and IPv6 addresses in dotted decimal notation and CIDR notation.
 *
 * @param string $ip    The IP address to check (e.g., "192.168.1.10" or "2001:0db8::1").
 * @param string $range The IP range in CIDR format to compare against (e.g., "192.168.1.0/24" or "2001:0db8::/32").
 *
 * @return bool True if the IP address is within the range, false otherwise.
 *
 * @example is_ip_in_range('192.168.1.10', '192.168.1.0/24') Returns true.
 * @example is_ip_in_range('10.0.0.50', '10.0.0.0/8') Returns true.
 * @example is_ip_in_range('192.168.1.10', '192.168.2.0/24') Returns false.
 * @example is_ip_in_range('2001:0db8::1', '2001:0db8::/32') Returns true.
 * @example is_ip_in_range('2a00:1450::1', '2a00:1450::/32') Returns true.
 * @example is_ip_in_range('2001:0db8::1', '2001:0db8::/64') Returns false.
 * @example is_ip_in_range('invalid-ip', '192.168.1.0/24') Returns false (invalid IP format).
 * @example is_ip_in_range('192.168.1.10', 'invalid-range') Returns false (invalid range format).
 * @example is_ip_in_range('2001:0db8::1', 'invalid-range') Returns false (invalid range format).
 */
function is_ip_in_range( $ip, $range ): bool {
	if ( empty( $ip ) || ! is_string( $ip ) || empty( $range ) || ! is_string( $range ) ) {
		return false; // Invalid input format
	}

	// Check for IPv4 or IPv6 CIDR format
	if ( strpos( $range, '/' ) !== false ) {
		list( $subnet, $subnet_bits ) = explode( '/', $range, 2 );

		if ( filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			if ( is_numeric( $subnet_bits ) && $subnet_bits >= 0 && $subnet_bits <= 32 ) {
				return is_ipv4_in_range( $ip, $range ); // IPv4 CIDR format
			}
		} elseif ( filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			if ( is_numeric( $subnet_bits ) && $subnet_bits >= 0 && $subnet_bits <= 128 ) {
				return is_ipv6_in_range( $ip, $range ); // IPv6 CIDR format
			}
		}
	}

	return false; // Invalid CIDR format
}

/**
 * Checks if an IPv4 address is within a specified range in CIDR format.
 *
 * This function validates whether the provided IPv4 address falls within the specified
 * CIDR range. It supports IPv4 addresses in dotted decimal notation and CIDR notation.
 *
 * @param string $ip    The IPv4 address to check (e.g., "192.168.1.10").
 * @param string $range The IPv4 range in CIDR format to compare against (e.g., "192.168.1.0/24").
 *
 * @return bool True if the IPv4 address is within the range, false otherwise.
 *
 * @example ipv4_in_range('192.168.1.10', '192.168.1.0/24') Returns true.
 * @example ipv4_in_range('10.0.0.50', '10.0.0.0/8') Returns true.
 * @example ipv4_in_range('192.168.1.10', '192.168.2.0/24') Returns false.
 * @example ipv4_in_range('invalid-ip', '192.168.1.0/24') Returns false (invalid IP format).
 * @example ipv4_in_range('192.168.1.10', 'invalid-range') Returns false (invalid range format).
 */
function is_ipv4_in_range( $ip, $range ): bool {
	// Validate the IP address and range format
	if ( empty( $ip ) || ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		return false; // Invalid IP address
	}

	// Split the range into subnet and subnet bits
	list( $subnet, $subnet_bits ) = explode( '/', $range );

	// Validate the subnet format and subnet bits
	if ( empty( $subnet ) || ! filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) || ! is_numeric( $subnet_bits ) || $subnet_bits < 0 || $subnet_bits > 32 ) {
		return false; // Invalid subnet or subnet bits
	}

	// Convert IP and subnet to long integers
	$ip_long     = ip2long( $ip );
	$subnet_long = ip2long( $subnet );

	// Calculate the subnet mask
	$subnet_mask = - 1 << ( 32 - $subnet_bits );
	$subnet_long &= $subnet_mask;

	// Check if the IP address falls within the subnet range
	return ( $ip_long & $subnet_mask ) == $subnet_long;
}

/**
 * Checks if an IPv6 address is within a specified range in CIDR format.
 *
 * This function validates whether the provided IPv6 address falls within the specified
 * CIDR range. It supports IPv6 addresses in full and compressed notation and CIDR notation.
 *
 * @param string $ip    The IPv6 address to check (e.g., "2001:0db8:85a3:0000:0000:8a2e:0370:7334").
 * @param string $range The IPv6 range in CIDR format to compare against (e.g., "2001:0db8::/32").
 *
 * @return bool True if the IPv6 address is within the range, false otherwise.
 *
 * @example ipv6_in_range('2001:0db8:85a3:0000:0000:8a2e:0370:7334', '2001:0db8::/32') Returns true.
 * @example ipv6_in_range('2a00:1450::1', '2a00:1450::/32') Returns true.
 * @example ipv6_in_range('2001:0db8:85a3:0000:0000:8a2e:0370:7334', '2001:0db8::/64') Returns false.
 * @example ipv6_in_range('invalid-ip', '2001:0db8::/32') Returns false (invalid IP format).
 * @example ipv6_in_range('2001:0db8:85a3:0000:0000:8a2e:0370:7334', 'invalid-range') Returns false (invalid range
 *          format).
 */
function is_ipv6_in_range( $ip, $range ): bool {
	// Validate the IPv6 address and range format
	if ( empty( $ip ) || ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
		return false; // Invalid IPv6 address
	}

	// Split the range into subnet and subnet bits
	list( $subnet, $subnet_bits ) = explode( '/', $range );

	// Validate the subnet format and subnet bits
	if ( empty( $subnet ) || ! filter_var( $subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) || ! is_numeric( $subnet_bits ) || $subnet_bits < 0 || $subnet_bits > 128 ) {
		return false; // Invalid subnet or subnet bits
	}

	// Convert IP and subnet to binary form
	$ip_binary     = inet_pton( $ip );
	$subnet_binary = inet_pton( $subnet );

	// Calculate the mask for subnet bits
	$mask_bits = 128 - $subnet_bits;
	$mask      = ( 1 << $mask_bits ) - 1;

	// Check if the IP address falls within the subnet range
	return (bool) ( $ip_binary & $mask ) === ( $subnet_binary & $mask );
}