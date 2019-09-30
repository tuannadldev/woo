<?php
/**
 * Includes WooCommerce template function overrides.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_PPC_Template_Functions {

	public static function load_template_overrides() {
		if ( WC_PPC_Util::prefixing_protected_categories() ) {
			wc_ppc_include_template_loop_category_title();
		}
	}

}

function wc_ppc_include_template_loop_category_title() {

	if ( ! function_exists( 'woocommerce_template_loop_category_title' ) ) {

		// Override WC function to show the subcategory title inside the product loop
		function woocommerce_template_loop_category_title( $category ) {
			?>
			<h2 class="woocommerce-loop-category__title"><?php
				echo esc_html( WC_PPC_Category_Prefixer::prefix_category( $category->name, $category->term_id ) );

				if ( $category->count > 0 ) {
					echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $category->count ) . ')</mark>', $category );
				}
				?></h2>
			<?php
		}
	}
}
