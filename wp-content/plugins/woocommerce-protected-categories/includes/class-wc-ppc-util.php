<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utility functions for WooCommerce Protected Categories.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Util {

	// PRODUCT CATEGORIES

	public static function get_product_categories() {
		global $wp_version;

		// Arguments for get_terms changed in WP 4.5
		if ( version_compare( $wp_version, '4.5', '>=' ) ) {
			$terms = get_terms( array(
				'taxonomy' => 'product_cat',
				'hide_empty' => false,
				'fields' => 'ids',
				'wc_ppc' => true
				) );
		} else {
			$terms = get_terms( 'product_cat', array(
				'hide_empty' => false,
				'fields' => 'ids',
				'wc_ppc' => true
				) );
		}

		return is_array( $terms ) ? $terms : array();
	}

	public static function get_term_meta( $term_id, $key, $single = true ) {
		// Back-compat: get_term_meta() added in WP 4.4.
		return function_exists( 'get_term_meta' ) ? get_term_meta( $term_id, $key, $single ) : get_metadata( 'woocommerce_term', $term_id, $key, $single );
	}

	// CATEGORY VISIBILITY

	public static function get_category_visibility( $term_id ) {

		$term_id = absint( $term_id );
		if ( ! $term_id ) {
			return false;
		}

		$visibilities = self::get_category_visibility_cache();

		if ( ! array_key_exists( $term_id, $visibilities ) ) {
			$visibilities[$term_id] = new WC_PPC_Category_Visibility( $term_id );
			self::update_category_visibility_cache( $visibilities );
		}

		return $visibilities[$term_id];
	}

	public static function get_the_category_visibility( $post = null ) {

		$categories = get_the_terms( $post, 'product_cat' );

		if ( $categories && is_array( $categories ) ) {
			return self::to_category_visibilities( $categories );
		}

		return array();
	}

	public static function to_category_visibilities( $terms ) {
		if ( ! $terms || ! is_array( $terms ) ) {
			return array();
		}

		$result	 = array();
		$cache	 = self::get_category_visibility_cache();

		foreach ( $terms as $term ) {
			$term_id = ( $term instanceof WP_Term ) ? $term->term_id : absint( $term );

			if ( array_key_exists( $term_id, $cache ) ) {
				$result[] = $cache[$term_id];
			} else {
				$cache[$term_id] = new WC_PPC_Category_Visibility( $term_id );
				$result[]		 = $cache[$term_id];
			}
		}

		self::update_category_visibility_cache( $cache );

		return $result;
	}

	private static function get_category_visibility_cache() {
		$cache = wp_cache_get( 'wc_ppc_visibilities' );

		if ( false !== $cache || ! is_array( $cache ) ) {
			$cache = array();
		}
		return $cache;
	}

	private static function update_category_visibility_cache( $term_visibilities ) {
		wp_cache_set( 'wc_ppc_visibilities', $term_visibilities );
	}

	// PROTECTION

	public static function get_product_id_for_protection_check( $product ) {
		if ( ! $product ) {
			return false;
		}

		$product_id = $product->is_type( 'variation' ) ? self::get_parent_id( $product ) : $product->get_id();
		return apply_filters( 'wc_ppc_product_id_for_protection_check', $product_id, $product );
	}

	/**
	 * Determines whether the supplied category or categories are protected.
	 *
	 * Checks the supplied categories including all ancestors of those categories (if any). Returns
	 * one of the following values:
	 *
	 *  - 'password' - One or more categories is password protected
	 *  - 'user'     - One or more categories is protected to specific users
	 *  - 'role'     - One or more categories is protected to specific user roles
	 *  - 'private'  - One or more categories is private
	 *  - false      - All categories are public or at least one category has been 'unlocked' (see below).
	 *
	 * The function will return false (i.e. not protected) if all categories including ancestors are public.
	 *
	 * It also returns false if at least one protected category has been unlocked - e.g. the correct password
	 * has been entered, or the user has the required role (depending on the protection type). In this instance,
	 * the category is only considered 'unlocked' only if the there are no child categories of that category
	 * which are protected by another means.
	 *
	 * The function will always return false (i.e. unlocked) if at least one category is unlocked, regardless
	 * of the other categories supplied, even if the other categories are protected.
	 *
	 * If two or more protected categories are found, or if one protected category has multiple types of protection,
	 * the function will return the first type of protection found, in the following order of  precedence:
	 * password, private, user, role. This can be controlled using the 'wc_ppc_category_protection_priority_order' filter.
	 *
	 * @param array|WC_PPC_Category_Visibility $categories The category or array of WC_PPC_Category_Visibility objects to check.
	 * @return boolean|string false if not protected, otherwise 'password', 'private', 'user' or 'role' to denote the protection type.
	 */
	public static function is_protected( $categories ) {
		if ( ! $categories ) {
			return false;
		}

		if ( $categories instanceof WC_PPC_Category_Visibility ) {
			$categories = array( $categories );
		}

		$protection = array();

		foreach ( $categories as $category ) {
			$full_hierarchy = $category->ancestors();
			array_unshift( $full_hierarchy, $category );

			$level = 0;

			foreach ( $full_hierarchy as $category ) {
				if ( $category->is_public() ) {
					$level ++;
				} else {
					if ( $category->is_unlocked() ) {
						return false;
					}
					self::build_protection_for_level( $protection, $category, $level );

					// We found something protected, so no point continuing up ancestor tree; break and continue to next category.
					break;
				}
			}
		}

		if ( empty( $protection ) ) {
			return false;
		}

		$lowest_protection = reset( $protection );

		foreach ( apply_filters( 'wc_ppc_category_protection_priority_order', array( 'password', 'private', 'user', 'role' ) ) as $protection_type ) {
			if ( in_array( $protection_type, $lowest_protection ) ) {
				return $protection_type;
			}
		}

		// Shouldn't ever get here, but return false (not protected) just in case.
		return false;
	}

	private static function build_protection_for_level( &$protection, $category, $level = 0 ) {
		if ( $category->has_password_protection() ) {
			$protection[$level][] = 'password';
		}
		if ( $category->has_role_protection() ) {
			$protection[$level][] = 'role';
		}
		if ( $category->has_user_protection() ) {
			$protection[$level][] = 'user';
		}
		if ( $category->has_private_protection() ) {
			$protection[$level][] = 'private';
		}
	}

	// PASSWORD COOKIE

	public static function set_password_cookie( $term_id, $password ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';

		$hasher				 = new PasswordHash( 8, true );
		$expires_after_days	 = absint( get_option( 'wc_ppc_password_expires', 10 ) );

		// Double-check we have a valid number of days
		if ( $expires_after_days < 1 ) {
			$expires_after_days = 10;
		}

		$expiry_time = apply_filters( 'wc_ppc_password_expires', apply_filters( 'post_password_expires', time() + $expires_after_days * DAY_IN_SECONDS ) );

		if ( $referrer = wp_get_referer() ) {
			$secure = ( 'https' === parse_url( $referrer, PHP_URL_SCHEME ) );
		} else {
			$secure = false;
		}

		// Cookie is stored in this format: <term id>|<password hash>
		$cookie_value = "{$term_id}|" . $hasher->HashPassword( wp_unslash( $password ) );
		setcookie( WC_PPC_COOKIE_PREFIX . COOKIEHASH, $cookie_value, $expiry_time, COOKIEPATH, COOKIE_DOMAIN, $secure );
	}

	public static function get_password_cookie() {
		if ( ! isset( $_COOKIE[WC_PPC_COOKIE_PREFIX . COOKIEHASH] ) ) {
			return false;
		}

		$password_cookie = explode( '|', $_COOKIE[WC_PPC_COOKIE_PREFIX . COOKIEHASH], 2 );

		if ( 2 !== count( $password_cookie ) ) {
			// Cookie not in correct format (e.g. password protected post).
			return false;
		}

		$term_id = $password_cookie[0];
		$hash	 = wp_unslash( $password_cookie[1] );

		if ( ! is_numeric( $term_id ) || (int) $term_id < 1 ) {
			// Invalid term ID.
			return false;
		}

		if ( 0 !== strpos( $hash, '$P$B' ) ) {
			// Invalid password hash.
			return false;
		}

		return array(
			'term_id' => (int) $term_id,
			'password_hash' => $hash
		);
	}

	// PASSWORD LOGIN FORM

	public static function get_password_form_heading() {
		return apply_filters( 'wc_ppc_password_form_heading', get_option( 'wc_ppc_login_title', __( 'Please Login', 'wc-cat-protect' ) ) );
	}

	public static function get_password_form_message() {
		return apply_filters( 'wc_ppc_password_form_message', get_option( 'wc_ppc_password_form' ) );
	}

	// TEXT FORMATTING

	/**
	 * Removes any new line characters and long whitespace sequences (2 or more) from HTML output so that
	 * wpautop doesn't mess up the formatting.
	 *
	 * @param string $text The text to sanitize.
	 * @return string The sanitized text, which can be passed safely to wpautop.
	 */
	public static function sanitize_whitespace_for_autop( $text ) {
		return preg_replace( '/\R|\s{2,}/', '', $text );
	}

	// PLUGIN OPTIONS

	public static function prefixing_protected_categories() {
		return 'yes' === get_option( 'wc_ppc_prefix_categories', 'no' );
	}

	public static function showing_protected_categories() {
		return 'yes' === get_option( 'wc_ppc_show_protected', 'yes' );
	}

	// BACK COMPAT

	public static function get_parent_id( $product ) {
		// Added in WC 3.0
		if ( method_exists( $product, 'get_parent_id' ) ) {
			return $product->get_parent_id();
		} elseif ( property_exists( $product, 'parent' ) ) {
			return $product->parent->id;
		}
		return 0;
	}

	// DEPRECATED

	/**
	 * Is the current page a protected product category?
	 *
	 * @param int|string $term A term ID or slug to check, or leave blank to check for any category.
	 * @return boolean true if it's a protected category, false otherwise.
	 * @deprecated 2.0 - no longer used.
	 */
	public static function is_protected_category( $term = '' ) {
		_deprecated_function( __FUNCTION__, '2.0' );

		if ( is_product_category( $term ) ) {
			$category = self::get_category_visibility( get_queried_object_id() );
			return $category->is_protected( true );
		}
		return false;
	}

	/**
	 * Is the current page a product which has one or more categories that are password protected?
	 *
	 * Will return true only if:
	 *  - the current page is a product
	 *  - one or more of its categories have a visibility of 'password protected'.
	 *
	 * If the product has only one password protected category and it has been unlocked by the current user, then this function will return false.
	 *
	 * @return boolean true if it's a password protected product, false otherwise
	 * @deprecated 2.0 - no longer used.
	 */
	public static function is_protected_product() {
		_deprecated_function( __FUNCTION__, '2.0' );

		if ( is_product() ) {
			return self::product_password_required();
		}
		return false;
	}

	/**
	 * Does the specified product category require a password?
	 *
	 * Will return true if this category (or one of its parents) requires a password, and the
	 * category has not been 'unlocked' with the correct password.
	 *
	 * @param int $term_id The product category to check
	 * @return boolean true if password protected, false otherwise
	 * @deprecated 2.0 - no longer used.
	 */
	public static function category_password_required( $term_id ) {
		_deprecated_function( __FUNCTION__, '2.0' );

		$category = self::get_category_visibility( $term_id );
		return $category->is_password_protected( true );
	}

	/**
	 * Does the specified product require a password?
	 *
	 * Will return true if any of the product's categories is password protected and not unlocked.
	 *
	 * @param int|WP_Post $post The post to check, defaults to the current global $post if not specified.
	 * @return boolean true if password protected, false otherwise
	 * @deprecated 2.0 - no longer used.
	 */
	public static function product_password_required( $post = null ) {
		_deprecated_function( __FUNCTION__, '2.0' );

		if ( $product_cats = self::get_the_category_visibility( $post ) ) {
			foreach ( $product_cats as $category ) {
				// Show the password form if any of its categories are protected
				if ( $category->is_password_protected( true ) ) {
					return true;
				}
			}
		}
		return false;
	}

}

// class WC_PPC_Util
