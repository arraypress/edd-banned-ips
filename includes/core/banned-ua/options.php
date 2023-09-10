<?php
/**
 * Banned User-Agent Options
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
 * Retrieve the list of banned user agents.
 *
 * This function retrieves the list of user agents that have been banned
 * from making purchases.
 *
 * @return array List of banned user agents.
 */
function get_banned_user_agents(): array {
	// Retrieve the banned user agents setting
	$banned = edd_get_option( 'banned_user_agents', array() );

	// If banned user agents are not in array format, split by newlines
	$user_agents = ! is_array( $banned )
		? explode( "\n", $banned )
		: $banned;

	// Trim each user agent string to remove whitespace
	$user_agents = array_map( 'trim', $user_agents );

	// Apply a filter and return the list of banned user agents
	return apply_filters( 'edd_get_banned_user_agents', $user_agents );
}

/**
 * Add a user agent to the banned list.
 *
 * @param string $user_agent The user agent to add to the banned list.
 *
 * @return bool Whether the user agent was successfully added to the list.
 */
function add_user_agent_to_banned_list( $user_agent ): bool {
	$banned_user_agents = edd_get_option( 'banned_user_agents', array() );

	// Trim and validate the user agent
	$user_agent = trim( $user_agent );
	if ( empty( $user_agent ) || ! is_valid_user_agent( $user_agent ) ) {
		return false;
	}

	// Check if the user agent is already in the list
	if ( in_array( $user_agent, $banned_user_agents ) ) {
		return false;
	}

	// Add the user agent to the list and update the option
	$banned_user_agents[] = $user_agent;
	edd_update_option( 'banned_user_agents', $banned_user_agents );

	// User Agent Added to Banned List
	do_action( 'edd_add_user_agent_to_banned_list', $user_agent );

	return true;
}

/**
 * Remove a user agent from the banned list.
 *
 * @param string $user_agent The user agent to remove from the banned list.
 *
 * @return bool Whether the user agent was successfully removed from the list.
 */
function remove_user_agent_from_banned_list( $user_agent ): bool {
	$banned_user_agents = edd_get_option( 'banned_user_agents', array() );

	// Trim and validate the user agent
	$user_agent = trim( $user_agent );
	if ( empty( $user_agent ) || ! is_valid_user_agent( $user_agent ) ) {
		return false;
	}

	// Check if the user agent is in the list
	$key = array_search( $user_agent, $banned_user_agents );
	if ( false === $key ) {
		return false;
	}

	// Remove the user agent from the list and update the option
	unset( $banned_user_agents[ $key ] );
	edd_update_option( 'banned_user_agents', $banned_user_agents );

	// User Agent Removed from Banned List
	do_action( 'edd_remove_user_agent_from_banned_list', $user_agent );

	return true;
}