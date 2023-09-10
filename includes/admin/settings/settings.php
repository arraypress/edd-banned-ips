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
 * Registers gateway settings related to banned IP addresses.
 *
 * @param array $settings The existing settings array.
 *
 * @return array Updated settings array with banned IP settings.
 */
function register_gateway_settings( $settings ): array {
	$settings['checkout']['banned_ip_settings'] = array(
		'id'            => 'banned_ip_settings',
		'name'          => '<h3>' . __( 'Banned IPs', 'edd-banned-ips' ) . '</h3>',
		'desc'          => '',
		'type'          => 'header',
		'tooltip_title' => __( 'Banned IP Settings', 'edd-banned-ips' ),
		'tooltip_desc'  => __( 'Prevent specific IP addresses from making purchases by configuring these settings. This is useful for temporarily blocking certain potential customers who may be causing issues.', 'edd-banned-ips' ),
	);

	// Banned IPs setting
	$settings['checkout']['banned_ips'] = array(
		'id'          => 'banned_ips',
		'name'        => __( 'Banned IPs', 'edd-banned-ips' ),
		'desc'        => __( 'IP addresses placed in the box above will not be allowed to make purchases.', 'edd-banned-ips' ) . '<br>' .
		                 __( 'One per line, enter: IP addresses, IPv4 (<code>192.168.1.1</code>), or IPv6 (<code>2001:0db8:85a3:0000:0000:8a2e:0370:7334</code>).', 'edd-banned-ips' ) . '<br>' .
		                 __( 'You can also specify IP ranges in CIDR notation (<code>192.168.1.0/24</code> for IPv4 or <code>2001:0db8::/32</code> for IPv6).', 'edd-banned-ips' ),
		'type'        => 'textarea',
		'placeholder' => __( '127.0.0.1', 'edd-banned-ips' )
	);

	// Banned User Agents setting
	$settings['checkout']['banned_user_agents'] = array(
		'id'            => 'banned_user_agents',
		'name'          => __( 'Banned User Agents', 'edd-banned-ips' ),
		'desc'          => __( 'User agents placed in the box above will be blocked from making purchases.', 'edd-banned-ips' ) . '<br>' .
		                   __( 'One per line, enter user agent strings to block.', 'edd-banned-ips' ) . '<br>' .
		                   __( 'Add a suffix of <code>**</code> to a user agent string to indicate a partial match.', 'edd-banned-ips' ) . '<br>' .
		                   __( 'For example, <code>bot-crawler**</code> will match user agents like <code>MyBot-crawler/1.0</code>.', 'edd-banned-ips' ) . '<br>',
		'type'          => 'textarea',
		'placeholder'   => __( 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'edd-banned-ips' ),
	);

	// Allow Existing Customers setting
	$settings['checkout']['allow_existing_customers'] = array(
		'id'      => 'allow_existing_customers',
		'name'    => __( 'Allow Existing Customers', 'edd-banned-ips' ),
		'desc'    => __( 'Check this box to allow existing customers to bypass the IP check.', 'edd-banned-ips' ),
		'type'    => 'checkbox',
		'default' => false,
	);

	// Custom Banned IP Message setting
	$settings['checkout']['banned_ip_message'] = array(
		'id'          => 'banned_ip_message',
		'name'        => __( 'Custom Message', 'edd-banned-ips' ),
		'desc'        => __( 'Enter a custom message to display to customers attempting to purchase from a banned IP address.', 'edd-banned-ips' ),
		'type'        => 'textarea',
		'placeholder' => __( 'An internal error has occurred, please try again or contact support.', 'edd-banned-ips' ),
	);

	return $settings;
}

// Register the settings filter
add_filter( 'edd_settings_gateways', __NAMESPACE__ . '\\register_gateway_settings', 999, 1 );