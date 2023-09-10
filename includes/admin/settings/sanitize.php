<?php
/**
 * Settings
 *
 * @package   edd-banned-ips
 * @copyright Copyright (c) 2023, ArrayPress Limited
 * @license   GPL2+
 * @since     1.0.0
 */

namespace ArrayPress\EDD\Banned_IPs\Admin;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use ArrayPress\EDD\Banned_IPs as Core;

/**
 * Sanitizes banned IPs input.
 *
 * @param array $input The input array from the settings page.
 *
 * @return array Sanitized input array.
 */
function sanitize_banned_ips( $input ): array {
	$ips = '';
	if ( ! empty( $input['banned_ips'] ) ) {
		// Sanitize the input
		$ips = array_map( 'trim', explode( "\n", $input['banned_ips'] ) );
		$ips = array_unique( $ips );
		$ips = array_map( 'sanitize_text_field', $ips );

		foreach ( $ips as $id => $ip ) {
			if ( ! Core\is_valid_ip_or_range( $ip ) ) {
				unset( $ips[ $id ] );
			}
		}
	}
	$input['banned_ips'] = $ips;

	return $input;
}

// Hook into the sanitize filter for the specific settings section
add_filter( 'edd_settings_gateways-checkout_sanitize', __NAMESPACE__ . '\\sanitize_banned_ips', 10, 1 );

/**
 * Sanitizes banned user agent's input.
 *
 * @param array $input The input array from the settings page.
 *
 * @return array Sanitized input array.
 */
function sanitize_banned_user_agents( $input ): array {
	$user_agents = '';
	if ( ! empty( $input['banned_user_agents'] ) ) {
		// Sanitize the input
		$user_agents = array_map( 'trim', explode( "\n", $input['banned_user_agents'] ) );
		$user_agents = array_unique( $user_agents );
		$user_agents = array_map( 'sanitize_text_field', $user_agents );

		foreach ( $user_agents as $id => $user_agent ) {
			if ( ! Core\is_valid_user_agent( $user_agent ) ) {
				unset( $user_agents[ $id ] );
			}
		}
	}
	$input['banned_user_agents'] = $user_agents;

	return $input;
}

// Hook into the sanitize filter for the specific settings section
add_filter( 'edd_settings_gateways-checkout_sanitize', __NAMESPACE__ . '\\sanitize_banned_user_agents', 10, 1 );