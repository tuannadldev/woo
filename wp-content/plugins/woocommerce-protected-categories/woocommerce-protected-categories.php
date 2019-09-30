<?php

/**
 * The main plugin file for WooCommerce Protected Categories.
 *
 * This file is included during the WP bootstrap if the plugin is active.
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Protected Categories
 * Plugin URI:        https://barn2.co.uk/wordpress-plugins/woocommerce-password-protected-categories/
 * Description:       Protect your WooCommerce categories by password, user role or user account.
 * Version:           2.2.3
 * Author:            Barn2 Media
 * Author URI:        https://barn2.co.uk
 * Text Domain:       wc-cat-protect
 * Domain Path:       /languages
 *
 * WC requires at least: 2.6
 * WC tested up to: 3.6
 *
 * Copyright:		  Barn2 Media Ltd
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Barn2\Lib\Util;

/**
 * The main plugin class for WooCommerce Protected Categories.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class WC_Protected_Categories_Plugin {

	/**
	 * The plugin name.
	 */
	const NAME = 'WooCommerce Protected Categories';

	/**
	 * The current plugin version.
	 */
	const VERSION = '2.2.3';

	/**
	 * The main plugin __FILE__.
	 */
	const FILE = __FILE__;

	// Plugin helper classes stored in an array.
	private $helpers = array();

	/* Our plugin license manager. */
	private $license;

	/* The singleton instance. */
	private static $_instance = null;

	private function __construct() {
		$this->define_constants();
		$this->includes();

		$this->license = new Barn2_Plugin_License( self::FILE, self::NAME, self::VERSION, 'wc_ppc' );
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function load() {
		add_action( 'plugins_loaded', array( $this, 'init_hooks' ) );
	}

	private function define_constants() {
		if ( ! defined( 'WC_PPC_INCLUDES_DIR' ) ) {
			define( 'WC_PPC_INCLUDES_DIR', plugin_dir_path( self::FILE ) . 'includes/' );
		}
		if ( ! defined( 'WC_PPC_PLUGIN_BASENAME' ) ) {
			define( 'WC_PPC_PLUGIN_BASENAME', plugin_basename( self::FILE ) );
		}
		if ( ! defined( 'WC_PPC_COOKIE_PREFIX' ) ) {
			// Cookie has to be same one used by WP for password protected posts, as all caching plugins exclude it.
			define( 'WC_PPC_COOKIE_PREFIX', 'wp-postpass_' );
		}
	}

	private function includes() {
		// License
		require_once WC_PPC_INCLUDES_DIR . 'license/class-b2-plugin-license.php';

		// Core plugin
		require_once WC_PPC_INCLUDES_DIR . 'lib/class-util.php';
		require_once WC_PPC_INCLUDES_DIR . 'lib/interface-attachable.php';
		require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-util.php';
		require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-category-visibility.php';
		require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-login-shortcode.php';
		require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-password-form.php';
		require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-loop-buffer.php';

		// Front end
		if ( Util::is_front_end() ) {
			require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-template-functions.php';
			require_once WC_PPC_INCLUDES_DIR . 'compat/class-wc-ppc-theme-compat.php';
			require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-template-handler.php';
			require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-category-prefixer.php';
			require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-category-protector.php';
			require_once WC_PPC_INCLUDES_DIR . 'class-wc-ppc-loop-buffer.php';
			require_once WC_PPC_INCLUDES_DIR . 'integration/class-quick-view-pro-integration.php';
		}

		// Admin
		if ( Util::is_admin() ) {
			require_once WC_PPC_INCLUDES_DIR . 'admin/class-wc-ppc-admin-controller.php';
			require_once WC_PPC_INCLUDES_DIR . 'admin/class-wc-ppc-admin-settings-page.php';
			require_once WC_PPC_INCLUDES_DIR . 'admin/class-wc-ppc-admin-category-visibility.php';
		}
	}

	public function init_hooks() {
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

	public function after_setup_theme() {
		if ( Util::is_woocommerce_active() && $this->license->is_valid() && Util::is_front_end() ) {
			WC_PPC_Template_Functions::load_template_overrides();
			WC_PPC_Theme_Compat::register_theme_hooks();
		}
	}

	public function init() {
		$this->load_textdomain();

		if ( Util::is_admin() ) {
			$this->helpers['admin'] = new WC_PPC_Admin_Controller( $this->license );
		}

		if ( Util::is_woocommerce_active() && $this->license->is_valid() ) {

			if ( Util::is_admin() ) {
				$this->helpers['admin_cat_visibility'] = new WC_PPC_Admin_Category_Visibility();
			}

			if ( Util::is_front_end() ) {
				$this->helpers['login_shortcode']	 = new WC_PPC_Login_Shortcode();
				$this->helpers['password_form']		 = new WC_PPC_Password_Form();
				$this->helpers['template_handler']	 = new WC_PPC_Template_Handler();
				$this->helpers['category_protector'] = new WC_PPC_Category_Protector();
				$this->helpers['category_prefixer']	 = new WC_PPC_Category_Prefixer();

				// Ingegrations
				$this->helpers['quick_view_pro'] = new WC_PPC_Quick_View_Pro_Integration();
			}
		}

		// Attach helpers to WordPress.
		foreach ( $this->helpers as $attachable ) {
			$attachable->attach();
		}
	}

	/**
	 * Retrieve a plugin helper class.
	 *
	 * @param string $helper The helper class key to retrieve
	 * @return mixed The helper class
	 */
	public function get_helper( $helper ) {
		return isset( $this->helpers[$helper] ) ? $this->helpers[$helper] : false;
	}

	/**
	 * Does the plugin have a valid license?
	 *
	 * @return boolean true if valid
	 */
	public function has_valid_license() {
		return $this->license->is_valid();
	}

	private function load_textdomain() {
		load_plugin_textdomain( 'wc-cat-protect', false, dirname( self::FILE ) . '/languages' );
	}

}

/**
 * Helper function to return the plugin instance.
 *
 * @return WC_Protected_Categories_Plugin
 */
function wc_ppc() {
	return WC_Protected_Categories_Plugin::instance();
}

// Load the plugin
WC_Protected_Categories_Plugin::instance()->load();
