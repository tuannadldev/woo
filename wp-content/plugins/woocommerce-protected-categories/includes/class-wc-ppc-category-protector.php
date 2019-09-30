<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class protects categories (and their products) inside product loops, the main shop page,
 * search results, widgets, navigation menus, and in WooCommerce shortcodes.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Category_Protector {

	private $hidden_category_ids = false;
	private $unlocked_categories = false;

	public function attach() {
		// Adjust query to exclude products in private or protected categories
		add_action( 'pre_get_posts', array( $this, 'protect_products_in_loop' ) );

		// Protect products in WC shortcodes
		add_filter( 'woocommerce_shortcode_products_query', array( $this, 'protect_products_in_shortcode' ), 10, 3 );

		// Protect product categories, e.g. in product categories widget
		add_filter( 'get_terms_args', array( $this, 'protect_product_categories' ), 10, 2 );

		// Adjust category counts - priority 15 to run after 'wc_change_term_counts' filter
		add_filter( 'get_terms', array( $this, 'update_category_counts' ), 15, 3 );

		// Protect categories and products in nav menus
		add_filter( 'wp_get_nav_menu_items', array( $this, 'protect_nav_menus' ), 10, 3 );

		// Ensure protected products are not purchasable
		add_filter( 'woocommerce_is_purchasable', array( $this, 'is_product_purchasable' ), 10, 2 );
	}

	public function protect_products_in_loop( $query ) {

		if ( isset( $query->query_vars['post_type'] ) && in_array( 'product', (array) $query->query_vars['post_type'] ) ) {
			// Query is for product post type
		} elseif ( $query->is_search() ) {
			// Search results page
		} elseif ( $query->is_tax( get_object_taxonomies( 'product' ) ) ) {
			// Product category, product tag or other archive (e.g. product attribute)
		} elseif ( $query->get_queried_object() && ( $query->is_post_type_archive( 'product' ) || $query->is_page( wc_get_page_id( 'shop' ) ) ) ) {
			// Main shop page
		} else {
			return;
		}

		$query->set( 'tax_query', $this->build_tax_query( $query->query_vars ) );
	}

	public function protect_products_in_shortcode( $query_args, $atts, $loop_name = false ) {

		// Exclude protected categories from query used in shortcode
		$query_args['tax_query'] = $this->build_tax_query( $query_args );

		if ( 'product' === $loop_name && ! empty( $query_args['p'] ) ) {
			$query_args['post__in'] = array( $query_args['p'] );
			unset( $query_args['p'] );
		}
		return $query_args;
	}

	public function protect_product_categories( $args, $taxonomies ) {

		// Check we have the right taxonomy
		if ( ! in_array( 'product_cat', (array) $taxonomies ) ) {
			return $args;
		}

		// Bail if our internal flag is set ('wc_ppc')
		if ( ! empty( $args['wc_ppc'] ) ) {
			return $args;
		}

		// Bail if we're getting the terms for one or more objects
		if ( ! empty( $args['object_ids'] ) ) {
			return $args;
		}

		// Bail if 'get' => 'all' set, e.g. when getting the term hierarchy or get_term_by() called
		if ( 'all' === $args['get'] || 1 === $args['number'] ) {
			return $args;
		}

		// Bail if we're building the exclusions list within get_terms() itself.
		// get_terms() is recursive when 'exclude_tree' is specified so we need to check here to avoid an infinite loop.
		if ( ! empty( $args['child_of'] ) && 'ids' === $args['fields'] && ! $args['hide_empty'] ) {
			return $args;
		}

		// Fix WP bug where 'include' is set to the non-empty array "Array ( [0] => null )"
		if ( is_array( $args['include'] ) ) {
			$args['include'] = array_filter( $args['include'] );
		}

		// Bail if 'include' is set, as 'exclude_tree' will be ignored.
		if ( ! empty( $args['include'] ) ) {
			return $args;
		}

		// Bail if there are no hidden categories.
		if ( ! ( $hidden_category_ids = $this->hidden_category_ids() ) ) {
			return $args;
		}

		// Merge in any other exclude trees.
		if ( ! empty( $args['exclude_tree'] ) ) {
			$hidden_category_ids = array_unique( array_merge( wp_parse_id_list( $args['exclude_tree'] ), $hidden_category_ids ) );
		}

		$args['exclude_tree'] = $hidden_category_ids;

		return $args;
	}

	public function update_category_counts( $terms, $taxonomies, $args ) {
		// Bail if there are no terms, we have the wrong taxonomy, we're not padding term counts, or we're not returning term objects
		if ( ! $terms || ( is_array( $taxonomies ) && ! in_array( 'product_cat', $taxonomies ) ) || empty( $args['pad_counts'] ) || ( 'all' !== $args['fields'] && 'all_with_object_id' !== $args['fields'] ) ) {
			return $terms;
		}

		// Adjust category counts - loop through each top-level category or sub-category
		foreach ( $terms as $term ) {
			if ( $hidden_descendants = array_intersect( $this->hidden_category_ids(), get_term_children( $term->term_id, 'product_cat' ) ) ) {
				// Get number of (unique) products in the hidden child categories
				$count = count( array_unique( get_objects_in_term( $hidden_descendants, 'product_cat' ) ) );

				if ( $count && $count <= $term->count ) {
					$term->count = $term->count - $count;
				}
			}
		}

		return $terms;
	}

	public function protect_nav_menus( $menu_items, $menu, $args ) {
		$removed_items = array();

		// Back-compat - default nav menu visibility to the same as main visibility setting
		$menu_vis_default	 = get_option( 'wc_ppc_show_protected', 'yes' );
		$show_protected		 = get_option( 'wc_ppc_show_protected_menu', $menu_vis_default ) === 'yes';

		foreach ( $menu_items as $key => $menu_item ) {
			$categories = false;

			if ( 'product' === $menu_item->object ) {
				// Product menu item
				$categories = WC_PPC_Util::get_the_category_visibility( $menu_item->object_id );
			} elseif ( 'product_cat' === $menu_item->object ) {
				// Product category menu item
				$categories = WC_PPC_Util::get_category_visibility( $menu_item->object_id );
			}

			if ( $categories && $protection = WC_PPC_Util::is_protected( $categories ) ) {
				if ( 'private' === $protection || ( ! $show_protected && in_array( $protection, array( 'password', 'user', 'role' ) ) ) ) {
					$removed_items[] = $menu_item->ID;
					unset( $menu_items[$key] );
				}
			}
		}

		// Now find and remove any children of any removed menu item - simples!
		while ( $removed_items ) {
			$child_items_removed = array();

			foreach ( $menu_items as $key => $menu_item ) {
				if ( in_array( $menu_item->menu_item_parent, $removed_items ) ) {
					$child_items_removed[] = $menu_item->ID;
					unset( $menu_items[$key] );
				}
			}

			// Update the removed list with the removed child items and start over
			$removed_items = $child_items_removed;
		}

		return array_values( $menu_items );
	}

	public function is_product_purchasable( $purchasable, $product ) {
		$product_id	 = WC_PPC_Util::get_product_id_for_protection_check( $product );
		$categories	 = WC_PPC_Util::get_the_category_visibility( $product_id );

		if ( WC_PPC_Util::is_protected( $categories ) ) {
			return false;
		}

		return $purchasable;
	}

	private function build_tax_query( $query_args ) {
		$tax_query			 = isset( $query_args['tax_query'] ) ? $query_args['tax_query'] : array();
		$hidden_categories	 = $this->hidden_category_ids();

		// Bail early if no categories to protect.
		if ( ! $hidden_categories ) {
			return $tax_query;
		}

		// Is there already a tax query present?
		if ( ! empty( $tax_query ) ) {
			// If current tax query is an 'OR' we need to nest it and wrap our query outside with an 'AND' relation
			if ( ! empty( $tax_query['relation'] ) && ( 'OR' === $tax_query['relation'] ) && count( $tax_query ) > 2 ) {
				$tax_query = array( $tax_query );
			}

			$tax_query = array( 'relation' => 'AND' ) + $tax_query;
		}

		$visibility_tax_query = array(
			'taxonomy' => 'product_cat',
			'field' => 'term_id',
			'terms' => $hidden_categories,
			'operator' => 'NOT IN'
		);

		if ( $unlocked = $this->unlocked_categories() ) {
			// If we have unlocked categories, we need to ensure that these are not parents of a hidden category.
			// In this case, the hidden category will take precedence over the unlocked parent, as it's lower in the category hierarchy.
			$hidden_ancestors = array();

			// Get the ancestors for each hidden category.
			foreach ( $hidden_categories as $hidden_id ) {
				$hidden_ancestors = array_merge( $hidden_ancestors, get_ancestors( $hidden_id, 'product_cat', 'taxonomy' ) );
			}

			// Remove duplicates.
			$hidden_ancestors = array_unique( $hidden_ancestors );

			// Now remove any hidden ancestors from the list of unlocked categories.
			$unlocked = array_diff( $unlocked, $hidden_ancestors );

			// If we still have unlocked categories, make sure we include these (IN) in our tax query.
			if ( $unlocked ) {
				$visibility_tax_query				 = array( $visibility_tax_query );
				$visibility_tax_query['relation']	 = 'OR';
				$visibility_tax_query[]				 = array(
					'taxonomy' => 'product_cat',
					'field' => 'term_id',
					'terms' => $unlocked,
					'operator' => 'IN'
				);
			}
		}

		$tax_query[] = $visibility_tax_query;
		return $tax_query;
	}

	private function hidden_category_ids() {
		if ( false === $this->hidden_category_ids ) {
			$this->hidden_category_ids	 = array();
			$show_protected				 = WC_PPC_Util::showing_protected_categories();

			// Get all the product categories, and check which are hidden.
			foreach ( WC_PPC_Util::to_category_visibilities( WC_PPC_Util::get_product_categories() ) as $category ) {
				if ( $category->is_private() || ( ! $show_protected && $category->is_protected() ) ) {
					$this->hidden_category_ids[] = $category->term_id;
				}
			}
		}
		return $this->hidden_category_ids;
	}

	private function unlocked_categories() {
		if ( false === $this->unlocked_categories ) {
			$this->unlocked_categories = array();

			// Get all the product categories, and check which are unlocked.
			foreach ( WC_PPC_Util::to_category_visibilities( WC_PPC_Util::get_product_categories() ) as $category ) {
				if ( $category->is_unlocked() ) {
					$this->unlocked_categories[] = $category->term_id;
				}
			}
		}
		return $this->unlocked_categories;
	}

}
