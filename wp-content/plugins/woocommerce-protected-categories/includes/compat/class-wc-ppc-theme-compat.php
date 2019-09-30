<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility and integration with different themes.
 *
 * @package   WooCommerce_Protected_Categories\Compat
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Theme_Compat {

	public static function register_theme_hooks() {
		$theme = strtolower( get_template() );

		if ( 'avada' === $theme ) {
			add_action( 'wp', array( __CLASS__, 'avada_reset_page_id' ), 30 );
		} elseif ( 'kallyas' === $theme ) {
			add_action( 'wc_ppc_handle_404', array( __CLASS__, 'kallyas_private_category_404' ) );
		}

		// Prefix title for Qode themes, e.g. Bridge.
		add_filter( 'qode_title_text', array( 'WC_PPC_Category_Prefixer', 'prefix_category_title' ) );
	}

	public static function avada_reset_page_id() {
		if ( class_exists( 'Fusion' ) ) {
			// Reset Avada's page ID so it picks up our spoofed ID later.
			Fusion::$c_page_id = false;
		}
	}

	public static function kallyas_private_category_404() {
		// Kallyas theme - hide the title/subtitle for private products.
		add_filter( 'zn_sub_header', array( __CLASS__, 'kallyas_private_subheader' ) );
	}

	public static function kallyas_private_subheader( $args ) {
		$args['title']		 = '';
		$args['subtitle']	 = '';
		return $args;
	}

}