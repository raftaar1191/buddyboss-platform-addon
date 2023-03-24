<?php
/**
 * Plugin Name: BuddyBoss Platform Add-on
 * Plugin URI:  https://buddyboss.com/
 * Description: Example plugin to show developers how to add their own settings into BuddyBoss Platform.
 * Author:      BuddyBoss
 * Author URI:  https://buddyboss.com/
 * Version:     1.0.0
 * Text Domain: buddyboss-platform-addon
 * Domain Path: /languages/
 * License:     GPLv3 or later (license.txt)
 */

/**
 * This file should always remain compatible with the minimum version of
 * PHP supported by WordPress.
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BuddyBoss_Platform_Addon' ) ) {

	/**
	 * Main MYPlugin Custom Emails Class
	 *
	 * @class BuddyBoss_Platform_Addon
	 * @version	1.0.0
	 */
	final class BuddyBoss_Platform_Addon {

		/**
		 * @var BuddyBoss_Platform_Addon The single instance of the class
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main BuddyBoss_Platform_Addon Instance
		 *
		 * Ensures only one instance of BuddyBoss_Platform_Addon is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see BuddyBoss_Platform_Addon()
		 * @return BuddyBoss_Platform_Addon - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'buddyboss-platform-addon' ), '1.0.0' );
		}
		/**
		 * Unserializing instances of this class is forbidden.
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'buddyboss-platform-addon' ), '1.0.0' );
		}

		/**
		 * BuddyBoss_Platform_Addon Constructor.
		 */
		public function __construct() {

			// Set up localisation.
			$this->load_plugin_textdomain();


			add_action( 'bp_loaded', array( $this, 'bp_init' ) );
		}

		/**
		 * Load this function when the BuddyBoss Platform Plugin is loaded
		 */
		public function bp_init() {

			$this->define_constants();

			if ( ! defined( 'BP_PLATFORM_VERSION' ) ) {
				add_action( 'admin_notices', array( $this, 'install_bb_platform_notice' ) );
				add_action( 'network_admin_notices', array( $this, 'install_bb_platform_notice' ) );
				return;
			}

			if ( empty( $this->platform_is_active() ) ) {
				add_action( 'admin_notices', array( $this, 'update_bb_platform_notice' ) );
				add_action( 'network_admin_notices', array( $this, 'update_bb_platform_notice' ) );
				return;
			}

			$this->includes();
		}

		/**
		 * Define WCE Constants
		 */
		private function define_constants() {
			$this->define( 'BUDDYBOSS_PLATFORM_ADDO_PLUGIN_FILE', __FILE__ );
			$this->define( 'BUDDYBOSS_PLATFORM_ADDO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'BUDDYBOSS_PLATFORM_ADDO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'BUDDYBOSS_PLATFORM_ADDO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'BP_PLATFORM_VERSION_MINI_VERSION', '2.2.9.1' );
		}

		/**
		 * Define constant if not already set
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			include_once( 'functions.php' );
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 */
		public function load_plugin_textdomain() {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'buddyboss-platform-addon' );

			unload_textdomain( 'buddyboss-platform-addon' );
			load_textdomain( 'buddyboss-platform-addon', WP_LANG_DIR . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' . plugin_basename( dirname( __FILE__ ) ) . '-' . $locale . '.mo' );
			load_plugin_textdomain( 'buddyboss-platform-addon', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		public function install_bb_platform_notice() {
			echo '<div class="error fade"><p>';
			_e('<strong>BuddyBoss Platform Add-on</strong></a> requires the BuddyBoss Platform plugin to work. Please <a href="https://buddyboss.com/platform/" target="_blank">install BuddyBoss Platform</a> first.', 'buddyboss-platform-addon');
			echo '</p></div>';
		}
	
		public function update_bb_platform_notice() {
			echo '<div class="error fade"><p>';
			_e('<strong>BuddyBoss Platform Add-on</strong></a> requires BuddyBoss Platform plugin version 1.2.6 or higher to work. Please update BuddyBoss Platform.', 'buddyboss-platform-addon');
			echo '</p></div>';
		}

		/**
		 * Check if the platform is acitve or not
		 * User: BuddyBoss_Platform_Addon::instance->platform_is_active();
		 * 
		 * return Bool True if the Platform is active and has the mini version requre or else false
		 */

		public function platform_is_active() {
			if ( defined( 'BP_PLATFORM_VERSION' ) && version_compare( BP_PLATFORM_VERSION, BP_PLATFORM_VERSION_MINI_VERSION , '>=' ) ) {
				return true;
			}
			return false;
		}
	}
}


if ( ! function_exists( 'buddyBoss_platform_addon' ) ) {
	/**
	 * Returns the main instance of BuddyBoss_Platform_Addon to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return BuddyBoss_Platform_Addon
	 */
	function buddyBoss_platform_addon() {
		return BuddyBoss_Platform_Addon::instance();
	}

	/**
	 * Call the main function to load the plugin
	 */
	buddyBoss_platform_addon();
}


