<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Barn2\Lib\Util;

/**
 * General admin functions for WooCommerce PPC.
 *
 * @package   WooCommerce_Protected_Categories\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Admin_Controller implements \Barn2\Lib\Attachable {

	private $settings_page;

	public function __construct( Barn2_Plugin_License $license ) {
		$this->settings_page = new WC_PPC_Admin_Settings_Page( $license );
	}

	public function attach() {
		// Add links to settings, docs, etc to main Plugins page
		add_filter( 'plugin_action_links_' . WC_PPC_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		if ( Util::is_woocommerce_active() ) {
			$this->settings_page->attach();
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 20 );
		}
	}

	public function plugin_action_links( $links ) {
		if ( class_exists( 'WooCommerce' ) ) {
			array_unshift( $links, sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=wc-settings&tab=products&section=protected-cats' ), __( 'Settings', 'wc-cat-protect' ) ) );
		}
		return $links;
	}

	public function plugin_row_meta( $links, $file ) {
		if ( WC_PPC_PLUGIN_BASENAME === $file ) {
			$row_meta = array(
				'docs' => '<a href="https://barn2.co.uk/kb-categories/wpc-kb/" aria-label="' . esc_attr__( 'View WooCommerce Protected Categories documentation', 'wc-cat-protect' ) . '" target="_blank">' . esc_html__( 'Docs', 'wc-cat-protect' ) . '</a>'
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	public function register_admin_scripts( $hook ) {
		$screen = get_current_screen();

		$suffix = Util::get_script_suffix();

		wp_register_style( 'wc-ppc-admin', plugins_url( "assets/css/admin/wc-ppc-admin{$suffix}.css", WC_Protected_Categories_Plugin::FILE ), array( 'woocommerce_admin_styles' ), WC_Protected_Categories_Plugin::VERSION );

		// Back-compat: selectWoo was introducted in WC 3.2
		$script_deps = array( 'jquery' );
		if ( wp_script_is( 'selectWoo', 'registered' ) ) {
			$script_deps[] = 'selectWoo';
		} elseif ( wp_script_is( 'select2', 'registered' ) ) {
			$script_deps[] = 'select2';
		}

		wp_register_script( 'wc-ppc-admin', plugins_url( "assets/js/admin/wc-ppc-admin{$suffix}.js", WC_Protected_Categories_Plugin::FILE ), $script_deps, WC_Protected_Categories_Plugin::VERSION, true );

		wp_localize_script( 'wc-ppc-admin', 'wc_ppc_params', array(
			'confirm_delete' => __( 'Are you sure you want to remove this password?', 'wc-cat-protect' )
		) );

		// Plugin settings or edit category screen.
		if ( 'woocommerce_page_wc-settings' === $hook || ( $screen && 'product_cat' === $screen->taxonomy ) ) {
			wp_enqueue_style( 'wc-ppc-admin' );
			wp_enqueue_script( 'wc-ppc-admin' );
		}
	}

}
