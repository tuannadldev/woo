<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Barn2\Lib\Util;

/**
 * Handles integration with WooCommerce Quick View Pro.
 *
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Quick_View_Pro_Integration implements \Barn2\Lib\Attachable {

	public function attach() {
		if ( ! Util::is_quick_view_pro_active() ) {
			return;
		}

		add_filter( 'wc_quick_view_pro_can_view_quick_view_content', array( $this, 'can_view_product' ), 20, 2 );
	}

	public function can_view_product( $can_view, $product ) {
		if ( $can_view ) {
			$categories = WC_PPC_Util::get_the_category_visibility( WC_PPC_Util::get_product_id_for_protection_check( $product ) );

			if ( WC_PPC_Util::is_protected( $categories ) ) {
				return false;
			}
		}

		return $can_view;
	}

}
