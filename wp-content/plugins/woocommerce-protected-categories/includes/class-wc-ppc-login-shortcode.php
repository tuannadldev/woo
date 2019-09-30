<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the display and processing of the category login shortcode.
 *
 * This is used to allow customers to login to their own product category using their category password.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Login_Shortcode implements \Barn2\Lib\Attachable {

	const SHORTCODE = 'category_login';

	private $password_form;

	public function __construct() {
		$this->password_form = new WC_PPC_Password_Form();
	}

	public function attach() {
		add_shortcode( self::SHORTCODE, array( $this, 'login_shortcode' ) );
	}

	/**
	 * Handles the category login shortcode.
	 *
	 * @param array $atts The attributes passed in to the shortcode
	 * @param string $content The content passed to the shortcode (not used)
	 * @return string The shortcode output
	 */
	public function login_shortcode( $atts = array() ) {
		$atts = shortcode_atts( array(
			'message' => false,
			'button_text' => false,
			'label' => false,
			'heading' => false,
			'show_heading' => true,
			'central_login' => true,
			), $atts, self::SHORTCODE );

		// If these args are set to false, unset them so we use defaults from the plugin settings.
		foreach ( array( 'heading', 'message', 'button_text', 'label' ) as $att ) {
			if ( false === $atts[$att] ) {
				unset( $atts[$att] );
			}
		}

		$atts['show_heading']	 = ( true === $atts['show_heading'] || 'true' === $atts['show_heading'] );
		$atts['central_login']	 = ( true === $atts['central_login'] || 'true' === $atts['central_login'] );

		return $this->password_form->get_password_login_form( $atts );
	}

}
