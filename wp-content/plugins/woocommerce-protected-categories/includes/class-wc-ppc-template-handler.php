<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the protection of product categories and products by overriding the template or main
 * query for the current page.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Template_Handler implements \Barn2\Lib\Attachable {

	public function attach() {
		// Run on 'wp' hook so we can set cache constants.
		add_action( 'wp', array( $this, 'protect_shop_pages' ), 20 ); // after WC_PPC_Password_Form->do_login()
	}

	public function protect_shop_pages() {
		global $wp_did_header;

		// Return if we're not loading a template on the front-end.
		if ( ! $wp_did_header ) {
			return;
		}

		$protected = false;

		if ( is_product_category() ) {
			$protected = WC_PPC_Util::is_protected( WC_PPC_Util::get_category_visibility( get_queried_object_id() ) );
		} elseif ( is_product() ) {
			$protected = WC_PPC_Util::is_protected( WC_PPC_Util::get_the_category_visibility() );
		} elseif ( is_shop() ) {
			$shop_page = wc_get_page_id( 'shop' );

			// If central login page is the main shop page, hide all products/categories (the shortcode will handle display of form).
			if ( $shop_page > 0 && $shop_page === absint( get_option( 'wc_ppc_category_login_page' ) ) ) {
				$protected	 = true;
				$loop_buffer = new WC_PPC_Loop_Buffer( false );

				add_action( 'woocommerce_before_shop_loop', array( $loop_buffer, 'start_buffer' ), 1 ); // before ordering & results count
				add_action( 'woocommerce_after_shop_loop', array( $loop_buffer, 'end_buffer' ), 100 ); // after pagination

				add_action( 'woocommerce_before_template_part', array( $loop_buffer, 'start_buffer_no_products' ), 1 );
				add_action( 'woocommerce_after_template_part', array( $loop_buffer, 'end_buffer_no_products' ), 100 );

				do_action( 'wc_ppc_handle_protected_shop_page' );
			}
		}

		if ( 'password' === $protected ) {
			$this->handle_password_protection();
		} elseif ( in_array( $protected, array( 'user', 'role', 'private' ) ) ) {
			$this->handle_user_protection();
		}

		if ( $protected ) {
			do_action( 'wc_ppc_protected_woocommerce_page' );

			$this->prevent_indexing();
			$this->prevent_caching();
		}
	}

	private function handle_password_protection() {
		global $wp, $wp_query;

		$form = new WC_PPC_Password_Form();

		$post_id				 = rand( 1000000, 10000000 ); // attempt to avoid clash with a valid post
		$post					 = new stdClass();
		$post->ID				 = $post_id;
		$post->post_author		 = 1;
		$post->post_date		 = current_time( 'mysql' );
		$post->post_date_gmt	 = current_time( 'mysql', 1 );
		$post->post_status		 = 'publish';
		$post->comment_status	 = 'closed';
		$post->comment_count	 = 0;
		$post->ping_status		 = 'closed';
		$post->post_type		 = 'page';
		$post->filter			 = 'raw'; // important
		$post->post_name		 = 'category-login-' . $post_id; // append post ID to avoid clash
		$post->post_title		 = WC_PPC_Util::get_password_form_heading();
		$post->post_content		 = $form->get_password_login_form( array(
			'show_heading' => false,
			'central_login' => false
			) );

		// Convert to WP_Post object
		$wp_post = new WP_Post( $post );

		// Add our fake post to the cache
		wp_cache_add( $post_id, $wp_post, 'posts' );
		wp_cache_add( $post_id, array( true ), 'post_meta' );

		// Override main query
		$wp_query->post					 = $wp_post;
		$wp_query->posts				 = array( $wp_post );
		$wp_query->queried_object		 = $wp_post;
		$wp_query->queried_object_id	 = $wp_post->ID;
		$wp_query->found_posts			 = 1;
		$wp_query->post_count			 = 1;
		$wp_query->max_num_pages		 = 1;
		$wp_query->comment_count		 = 0;
		$wp_query->comments				 = array();
		$wp_query->is_singular			 = true;
		$wp_query->is_page				 = true;
		$wp_query->is_single			 = false;
		$wp_query->is_attachment		 = false;
		$wp_query->is_archive			 = false;
		$wp_query->is_category			 = false;
		$wp_query->is_tag				 = false;
		$wp_query->is_tax				 = false;
		$wp_query->is_author			 = false;
		$wp_query->is_date				 = false;
		$wp_query->is_year				 = false;
		$wp_query->is_month				 = false;
		$wp_query->is_day				 = false;
		$wp_query->is_time				 = false;
		$wp_query->is_search			 = false;
		$wp_query->is_feed				 = false;
		$wp_query->is_comment_feed		 = false;
		$wp_query->is_trackback			 = false;
		$wp_query->is_home				 = false;
		$wp_query->is_embed				 = false;
		$wp_query->is_404				 = false;
		$wp_query->is_paged				 = false;
		$wp_query->is_admin				 = false;
		$wp_query->is_preview			 = false;
		$wp_query->is_robots			 = false;
		$wp_query->is_posts_page		 = false;
		$wp_query->is_post_type_archive	 = false;

		// Update globals
		$GLOBALS['wp_query'] = $wp_query;
		$wp->register_globals();

		// Add body class for the password login page.
		add_filter( 'body_class', array( __CLASS__, 'add_password_required_class' ) );

		do_action( 'wc_ppc_handle_password_protection' );
	}

	public static function add_password_required_class( $classes ) {
		$classes[] = 'category-password-required';
		return $classes;
	}

	private function handle_user_protection() {
		do_action( 'wc_ppc_handle_user_protection' );

		$user_protected_option = get_option( 'wc_ppc_user_protected', '404' );

		if ( '404' === $user_protected_option ) {
			$this->handle_404();
		} elseif ( 'wplogin' === $user_protected_option ) {
			wp_safe_redirect( $this->get_login_url_with_current_page_redirect() );
		} elseif ( 'page' === $user_protected_option ) {
			if ( $page_id = get_option( 'wc_ppc_user_protected_redirect' ) ) {
				$redirect = add_query_arg( 'redirect_to', urlencode( add_query_arg( null, null ) ), get_permalink( $page_id ) );
				wp_safe_redirect( $redirect );
			} else {
				wp_safe_redirect( $this->get_login_url_with_current_page_redirect() );
			}
		}
	}

	private function get_login_url_with_current_page_redirect() {
		return apply_filters( 'wc_ppc_user_protected_login_url', wp_login_url( add_query_arg( null, null ) ) );
	}

	private function handle_404() {
		global $wp, $wp_query;

		$wp->handle_404();
		$wp_query->is_404		 = true;
		$wp_query->is_single	 = false;
		$wp_query->is_singular	 = false;
		$wp_query->is_tax		 = false;
		$wp_query->is_archive	 = false;

		/* deprecated 2.0 - renamed 'wc_ppc_handle_404' */
		do_action( 'wc_ppc_private_category_handle_404' );

		do_action( 'wc_ppc_handle_404' );
	}

	private function prevent_indexing() {
		@header( 'X-Robots-Tag: noindex, nofollow' );
		add_action( 'wp_head', array( $this, 'meta_robots_noindex_head' ), 5 );

		do_action( 'wc_ppc_prevent_indexing' );
	}

	public function meta_robots_noindex_head() {
		echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
	}

	private function prevent_caching() {
		// Set headers to prevent caching
		nocache_headers();

		// Set constants to prevent caching in certain caching plugins
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( 'DONOTCACHEOBJECT', true );
		}
		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}

		do_action( 'wc_ppc_prevent_caching' );
	}

}
