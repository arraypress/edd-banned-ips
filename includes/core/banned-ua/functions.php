<?php
/**
 * Banned User-Agent Functions
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
 * Retrieves and sanitizes the HTTP_USER_AGENT from $_SERVER.
 *
 * This function retrieves the value of HTTP_USER_AGENT from the $_SERVER superglobal,
 * sanitizes it using sanitize_text_field(), and returns the sanitized user agent.
 *
 * @return string The sanitized HTTP_USER_AGENT.
 */
function get_user_agent(): string {
	return isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';
}

/**
 * Checks if a user agent string is valid based on its length.
 *
 * @param string $user_agent The user agent string to validate.
 * @param int    $min_length The minimum allowed length for the user agent.
 * @param int    $max_length The maximum allowed length for the user agent.
 *
 * @return bool Returns true if the user agent is valid, false otherwise.
 */
function is_valid_user_agent( $user_agent, $min_length = 5, $max_length = 300 ): bool {
	if ( ! is_string( $user_agent ) || empty( $user_agent ) ) {
		return false; // Not a valid string
	}

	$length = strlen( $user_agent );

	return ( $length >= $min_length && $length <= $max_length );
}

/**
 * Checks if a given user agent is banned based on a list of banned user agents.
 *
 * This function determines whether a user agent is banned by comparing it against a list
 * of banned user agents. The comparison can be based on exact matches or partial matches
 * if the banned user agent ends with '**'. A filter is applied to the return value to allow
 * custom modification of the final decision.
 *
 * @param string $user_agent The user agent string to check against the list of banned agents.
 *
 * @return bool Returns true if the user agent is banned, false otherwise.
 *
 * @since 1.0.0
 */
function is_user_agent_banned( $user_agent ): bool {
	// Check if the user agent is a valid string
	if ( ! is_string( $user_agent ) || empty( $user_agent ) ) {
		return false; // Not a valid user agent
	}

	// Get the list of banned user agents
	$banned_user_agents = get_banned_user_agents();

	// Check if there are banned user agents to compare against
	if ( ! is_array( $banned_user_agents ) || empty( $banned_user_agents ) ) {
		return false; // No banned user agents to compare against
	}

	$return = false;

	foreach ( $banned_user_agents as $banned_agent ) {
		$banned_agent = strtolower( trim( $banned_agent ) );
		$user_agent   = strtolower( trim( $user_agent ) );

		$is_partial_match = false;

		// Check for partial match if the banned agent ends with "**"
		if ( substr( $banned_agent, - 2 ) === '**' ) {
			$is_partial_match = true;
			$banned_agent     = rtrim( $banned_agent, '*' ); // Remove "**" from the end
		}

		// Compare user agent based on partial or full match
		if ( $is_partial_match ) {
			if ( strpos( $user_agent, $banned_agent ) !== false ) {
				$return = true; // Partial match found
				break;
			}
		} else {
			if ( $user_agent === $banned_agent ) {
				$return = true; // Exact match found
				break;
			}
		}
	}

	// Filter & return
	return apply_filters( 'edd_is_user_agent_banned', $return, $user_agent );
}
