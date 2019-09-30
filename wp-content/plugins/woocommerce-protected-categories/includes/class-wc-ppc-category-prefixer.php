<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Responsible for prefixing the names of protected and private categories.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Category_Prefixer implements \Barn2\Lib\Attachable {

	public function attach() {
		//Register hooks for adjusting category titles (i.e. 'Private' or 'Protected' prefix).
		if ( WC_PPC_Util::prefixing_protected_categories() ) {
			// Prefix category title
			add_filter( 'single_term_title', array( __CLASS__, 'prefix_category_title' ) );

			// Prefix menu items
			add_filter( 'wp_get_nav_menu_items', array( __CLASS__, 'prefix_category_menu_items' ), 15, 3 );
		}
	}

	public static function prefix_category_title( $term_title ) {
		// 'wp_head' check prevents adjustment inside <title> tag
		if ( ! doing_action( 'wp_head' ) && is_product_category( $term_title ) ) {
			return self::prefix_category( $term_title );
		}

		return $term_title;
	}

	public static function prefix_category_menu_items( $menu_items, $menu, $args ) {

		foreach ( $menu_items as $menu_item ) {
			if ( 'product_cat' === $menu_item->object ) {
				$menu_item->title = self::prefix_category( $menu_item->title, $menu_item->object_id );
			}
		}

		return $menu_items;
	}

	/**
	 * Add 'Protected' or 'Private' to category name in breadcrumb.
	 *
	 * @param array The breadcrumb trail
	 * @return array The updated breadcrumb trail
	 */
	public static function prefix_category_breadcrumb( $breadcrumb ) {

		if ( $breadcrumb && is_product_category() ) {
			$last_crumb = array_pop( $breadcrumb );

			if ( isset( $last_crumb[0] ) ) {
				$last_crumb[0] = self::prefix_category( $last_crumb[0] );
			}
			$breadcrumb[] = $last_crumb;
		}

		return $breadcrumb;
	}

	public static function prefix_category( $term_title, $term_id = false ) {

		if ( ! $term_id ) {
			if ( ! is_product_category() ) {
				return $term_title;
			}
			$term_id = get_queried_object_id();
		}

		$category = WC_PPC_Util::get_category_visibility( $term_id );

		if ( 'private' === $category->visibility ) {
			$private_title_format	 = apply_filters( 'wc_ppc_private_category_format', _x( 'Private: %s', 'private category title', 'wc-cat-protect' ) );
			$term_title				 = sprintf( $private_title_format, $term_title );
		} elseif ( 'protected' === $category->visibility ) {
			$protected_title_format	 = apply_filters( 'wc_ppc_protected_category_format', _x( 'Protected: %s', 'protected category title', 'wc-cat-protect' ) );
			$term_title				 = sprintf( $protected_title_format, $term_title );
		}

		return $term_title;
	}

}
