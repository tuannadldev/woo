<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates an output buffer to capture and discard the contents of WooCommerce loop content
 * (i.e. products and categories).
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Loop_Buffer {

	private $show_form;
	private $buffering = false;

	public function __construct( $show_form = true ) {
		$this->show_form = $show_form;
	}

	public function start_buffer() {
		if ( ! $this->buffering ) {
			ob_start();
			$this->buffering = true;
		}
	}

	public function end_buffer() {
		if ( $this->buffering ) {
			// Discard buffered content
			ob_end_clean();

			// Show the password form
			$this->show_password_form();

			// Reset buffering
			$this->buffering = false;
		}
	}

	public function start_buffer_no_products( $template_name ) {
		if ( 'loop/no-products-found.php' === $template_name ) {
			$this->start_buffer();
		}
	}

	public function end_buffer_no_products( $template_name ) {
		if ( 'loop/no-products-found.php' === $template_name ) {
			$this->end_buffer();
		}
	}

	public function show_password_form() {
		if ( $this->show_form ) {
			$form = new WC_PPC_Password_Form();
			$form->show_password_login_form();
		}
	}

}
