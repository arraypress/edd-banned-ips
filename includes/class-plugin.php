<?php
/**
 * Main Plugin Class.
 *
 * @since 1.0.0
 */

namespace ArrayPress\EDD\Banned_IPs;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Main Plugin Class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var object|Plugin
	 */
	private static $instance = null;

	/**
	 * Loader file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $file = '';

	/**
	 * Current version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $version = '1.0.0';

	/**
	 * Prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $prefix = 'edd_banned_ips'; // change_me

	/**
	 * Main instance.
	 *
	 * Insures that only one instance exists in memory at any one time.
	 * Also prevents needing to define globals all over the place.
	 *
	 * @static
	 * @staticvar array $instance
	 * @return object|Plugin
	 */
	public static function instance( $file = '' ) {

		// Return if already instantiated
		if ( self::is_instantiated() ) {
			return self::$instance;
		}

		// Setup the singleton
		self::setup_instance( $file );

		// Bootstrap
		self::$instance->setup_constants();
		self::$instance->setup_files();
		self::$instance->setup_application();

		// Return the instance
		return self::$instance;
	}

	/**
	 * Main installer.
	 *
	 * @since 1.0.0
	 */
	public static function install() {}

	/**
	 * Main uninstaller.
	 *
	 * @since 1.0.0
	 */
	public static function uninstall() {}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __NAMESPACE__, '1.0.0' );
	}

	/**
	 * Disable un-serializing of the class.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __NAMESPACE__, '1.0.0' );
	}

	/**
	 * Public magic isset method allows checking any key from any scope.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function __isset( $key = '' ) {
		return (bool) isset( $this->{$key} );
	}

	/**
	 * Public magic get method allows getting any value from any scope.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function __get( $key = '' ) {
		return $this->__isset( $key )
			? $this->{$key}
			: null;
	}

	/**
	 * Return whether the main loading class has been instantiated or not.
	 *
	 * @return boolean True if instantiated. False if not.
	 * @since 1.0.0
	 *
	 */
	private static function is_instantiated(): bool {

		// Return true if instance is correct class
		if ( ! empty( self::$instance ) && ( self::$instance instanceof Plugin ) ) {
			return true;
		}

		// Return false if not instantiated correctly
		return false;
	}

	/**
	 * Setup the singleton instance
	 *
	 * @param string $file
	 *
	 * @since 1.0.0
	 */
	private static function setup_instance( $file = '' ) {
		self::$instance       = new Plugin;
		self::$instance->file = $file;
	}

	/**
	 * Setup plugin constants.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function setup_constants() {

		// Uppercase
		$prefix = strtoupper( $this->prefix );

		// Plugin Version.
		if ( ! defined( "{$prefix}_PLUGIN_VERSION" ) ) {
			define( "{$prefix}_PLUGIN_VERSION", $this->version );
		}

		// Plugin Root File.
		if ( ! defined( "{$prefix}_PLUGIN_FILE" ) ) {
			define( "{$prefix}_PLUGIN_FILE", $this->file );
		}

		// Plugin Class.
		if ( ! defined( "{$prefix}_PLUGIN_CLASS" ) ) {
			define( "{$prefix}_PLUGIN_CLASS", __CLASS__ );
		}

		// Prepare file & directory
		$file = $this->file;

		// Plugin Base Name.
		if ( ! defined( "{$prefix}_PLUGIN_BASE" ) ) {
			define( "{$prefix}_PLUGIN_BASE", trailingslashit( plugin_basename( $file ) ) );
		}

		// Plugin Folder Path.
		if ( ! defined( "{$prefix}_PLUGIN_DIR" ) ) {
			define( "{$prefix}_PLUGIN_DIR", trailingslashit( plugin_dir_path( $file ) ) );
		}

		// Plugin Folder URL.
		if ( ! defined( "{$prefix}_PLUGIN_URL" ) ) {
			define( "{$prefix}_PLUGIN_URL", trailingslashit( plugin_dir_url( $file ) ) );
		}

	}

	/**
	 * Setup files.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function setup_files() {

		// Files
		$this->include_classes();

		// Admin specific
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$this->include_admin();
		}

		// Dependencies
		$this->include_dependencies();

		// Common Files
		$this->include_common();

		// Drop-ins
		$this->include_dropins();

		// Integrations
		$this->include_integrations();

	}

	/**
	 * Setup the rest of the application
	 *
	 * @since 1.0.0
	 */
	private function setup_application() {

		add_action( 'admin_init', array( $this, 'install' ) );

		// Instantiate any classes or setup any dependency injections here
		self::$instance->setup_globals();

		// Instantiate any classes or setup any dependency injections here
		self::$instance->setup_licensing();

		// Instantiate any classes or setup any dependency injections here
		self::$instance->setup_hooks();

		// Register our components
		self::$instance->setup_components();

	}

	/**
	 * Setup components
	 *
	 * @access private
	 * @return void
	 * @since  1.0
	 */
	private function setup_components(): void {

		// Register plugin meta
		new \ArrayPress\Utils\EDD_Plugin_Meta( EDD_BANNED_IPS_PLUGIN_FILE,'gateways', 'checkout' );

	}

	/**
	 * Setup globals
	 *
	 * @access private
	 * @return void
	 * @since  1.0
	 */
	private function setup_globals(): void {}

	/**
	 * Setup licensing
	 *
	 * @access private
	 * @return void
	 * @since  1.0
	 */
	private function setup_licensing(): void {}

	/**
	 * Setup hooks
	 *
	 * @access private
	 * @return void
	 * @since  1.0
	 */
	private function setup_hooks(): void {}

	/** Hooks *****************************************************************/

	/** Includes **************************************************************/

	/**
	 * Automatically include files (classes) that are shared between all contexts.
	 *
	 * @since 1.0.0
	 */
	private function include_classes(): void {

		// Include classes
		$this->slurp( 'classes' );

	}

	/**
	 * Automatically include administration specific files.
	 *
	 * @since 1.0.0
	 */
	private function include_admin(): void {

		// Include settings
		$this->slurp( 'admin/settings' );

	}

	/**
	 * Automatically include administration specific files.
	 *
	 * @since 1.0.0
	 */
	private function include_dependencies() {}

	/**
	 * Automatically include files that are shared between all contexts.
	 *
	 * @since 1.0.0
	 */
	private function include_common(): void {

		// Include banned emails functions
		$this->slurp( 'core/banned-ips' );

		// Include banned emails functions
		$this->slurp( 'core/orders' );

	}

	/**
	 * Automatically include any files in the /includes/drop-ins/ directory.
	 *
	 * @since 1.0.0
	 */
	private function include_dropins(): void {
		$this->slurp( 'drop-ins' );
	}

	/**
	 * Automatically include any files in the /includes/integrations/ directory.
	 *
	 * @since 1.0.0
	 */
	private function include_integrations() {}

	/**
	 * Automatically include any files in a given directory.
	 *
	 * @param string $dir The name of the directory to include files from.
	 *
	 * @since 1.0.0
	 *
	 */
	private function slurp( $dir = '' ): void {

		// Files & directory
		$files = array();
		$dir   = trailingslashit( __DIR__ ) . $dir;

		// Bail if standard directory does not exist
		if ( ! is_dir( $dir ) ) {
			return;
		}

		// Try to open the directory
		$dh = opendir( $dir );

		// Bail if directory exists but cannot be opened
		if ( empty( $dh ) ) {
			return;
		}

		// Look for files in the directory
		while ( ( $plugin = readdir( $dh ) ) !== false ) {
			$ext = substr( $plugin, - 4 );

			if ( $ext === '.php' ) {
				$name           = substr( $plugin, 0, strlen( $plugin ) - 4 );
				$files[ $name ] = trailingslashit( $dir ) . $plugin;
			}
		}

		// Close the directory
		closedir( $dh );

		// Skip empty index files
		unset( $files['index'] );

		// Bail if no files
		if ( empty( $files ) ) {
			return;
		}

		// Sort files alphabetically
		ksort( $files );

		// Include each file
		foreach ( $files as $file ) {
			require_once $file;
		}
	}
}