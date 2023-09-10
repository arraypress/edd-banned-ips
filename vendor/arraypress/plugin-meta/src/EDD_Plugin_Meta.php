<?php
/**
 * Plugin Meta Class
 *
 * This class handles the registration of custom plugin action links and row meta links,
 * allowing developers to easily add external links to the plugin's action links or row meta section
 * in the WordPress admin Plugins page.
 *
 * @package   arraypress/plugin-meta
 * @copyright Copyright (c) 2023, ArrayPress Limited
 * @license   GPL2+
 * @since     1.0.0
 */

namespace ArrayPress\Utils;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Check if the class `EDD_Plugin_Meta` is defined, and if not, define it.
 */
if ( ! class_exists( __NAMESPACE__ . '\\EDD_Plugin_Meta' ) ) {

	class EDD_Plugin_Meta extends Plugin_Meta {

		/**
		 * @var string The settings tab for EDD
		 */
		public string $settings_tab = '';

		/**
		 * @var string The settings section for EDD
		 */
		public string $settings_section = '';

		/**
		 * EDD_Plugin_Meta constructor.
		 *
		 * @param string $file             Plugin file path.
		 * @param string $settings_tab     EDD settings tab.
		 * @param string $settings_section EDD settings section.
		 * @param array  $external_links   Array of external links.
		 *
		 * @throws \InvalidArgumentException If the plugin file path is empty.
		 */
		public function __construct( string $file = '', $settings_tab = '', $settings_section = '', array $external_links = array() ) {
			parent::__construct( $file, $external_links ); // Call parent constructor

			$this->settings_tab     = $settings_tab;
			$this->settings_section = $settings_section;

			$this->setup_links();
			$this->setup_actions();
		}

		/**
		 * Setup the default external links.
		 *
		 * @since 1.0.0
		 */
		public function setup_links() {
			// Setup the default labels and URLs
			$this->external_links = wp_parse_args( $this->external_links, array(
				'support'    => array(
					'label' => __( 'Support', 'arraypress' ),
					'url'   => 'https://arraypress.com/support',
				),
				'extensions' => array(
					'label' => __( 'Extensions', 'arraypress' ),
					'url'   => 'https://arraypress.com/products',
				),
			) );
		}

		/**
		 * Setup the action links.
		 *
		 * @since 1.0.0
		 */
		public function setup_actions() {
			if ( $this->settings_section ) {
				$this->external_links['settings'] = array(
					'action'  => true,
					'new_tab' => false,
					'label'   => __( 'Settings', 'arraypress' ),
					'url'     => \edd_get_admin_url( array(
						'page'    => 'edd-settings',
						'tab'     => $this->settings_tab ?: 'extensions',
						'section' => $this->settings_section,
					) )
				);
			}
		}

	}

} // End if class exists check