<?php
/**
 * Plugin Name:       EDD - Banned IPs
 * Plugin URI:        https://arraypress.com/products/banned-ips-for-easy-digital-downloads
 * Description:       Block specific IP addresses from purchasing on your store.
 * Author:            ArrayPress
 * Author URI:        https://arraypress.com
 * License:           GNU General Public License v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       edd-banned-ips
 * Domain Path:       /includes/languages/
 * Requires PHP:      7.4
 * Requires at least: 6.3
 * Version:           1.0.0
 */

namespace ArrayPress\EDD\Banned_IPs;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Include required files and initialize the Plugin class if available.
 */
require_once __DIR__ . '/vendor/autoload.php';

\EDD\ExtensionUtils\v1\ExtensionLoader::loadOrQuit(
	__FILE__,
	function () {
		// Check if the class file exists
		if ( file_exists( __DIR__ . '/includes/class-plugin.php' ) ) {
			require_once __DIR__ . '/includes/class-plugin.php';

			// Check if the class exists before instantiating
			if ( class_exists( __NAMESPACE__ . '\\Plugin' ) ) {
				Plugin::instance( __FILE__ );
			}
		}
	},
	array(
		'php'                    => '7.4',
		'easy-digital-downloads' => '3.2.0',
		'wp'                     => '6.3.1',
	)
);