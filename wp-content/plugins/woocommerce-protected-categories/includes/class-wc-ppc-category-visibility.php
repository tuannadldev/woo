<?php

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class represents a WooCommerce category with various functions to test its visibility.
 *
 * @package   WooCommerce_Protected_Categories
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Category_Visibility {

	public $term_id;
	public $visibility	 = 'public';
	public $passwords	 = array();
	public $roles		 = array();
	public $users		 = array();
	private $ancestors	 = null;

	public function __construct( $term_id ) {
		$this->term_id		 = absint( $term_id );
		$this->visibility	 = WC_PPC_Util::get_term_meta( $this->term_id, 'visibility', true );

		// Default to public if visibility not set.
		if ( ! $this->visibility ) {
			$this->visibility = 'public';
		}

		// Back-compat
		if ( 'password' === $this->visibility ) {
			$this->visibility = 'protected';
		}

		if ( 'protected' === $this->visibility ) {
			$passwords = WC_PPC_Util::get_term_meta( $this->term_id, 'password', false );

			// Back-compat - for passwords stored as separate meta items
			if ( $passwords && ! empty( $passwords[0] ) && is_array( $passwords[0] ) ) {
				$passwords = $passwords[0];
			}

			$this->passwords = $passwords ? $passwords : array();

			$roles		 = WC_PPC_Util::get_term_meta( $this->term_id, 'user_roles', true );
			$this->roles = $roles ? (array) $roles : array();

			$users		 = WC_PPC_Util::get_term_meta( $this->term_id, 'users', true );
			$this->users = $users ? (array) $users : array();
		}
	}

	/**
	 * Get the ancestors for this category as an array of WC_PPC_Category_Visibility objects.
	 *
	 * @return array The array of ancestors.
	 */
	public function ancestors() {
		// Lazy loaded so we only do this once
		if ( null === $this->ancestors ) {
			$this->ancestors = WC_PPC_Util::to_category_visibilities( get_ancestors( $this->term_id, 'product_cat', 'taxonomy' ) );
		}

		return $this->ancestors;
	}

	/**
	 * Checks if the supplied password is valid for this category. If the password is valid, it returns
	 * the term_id (an integer) for the category the password is valid for.
	 *
	 * @param string $password The password to check
	 * @param boolean $check_ancestors Whether to check passwords against ancestor categories as well.
	 * @return boolean|int The term ID the password is valid for, or false if not valid.
	 */
	public function check_password( $password, $check_ancestors = true ) {
		if ( ! $password ) {
			return false;
		}

		$valid_for_term = false;

		if ( $this->has_password_protection() && in_array( $password, $this->passwords ) ) {
			$valid_for_term = $this->term_id;
		}

		if ( ! $valid_for_term && $check_ancestors ) {
			foreach ( $this->ancestors() as $ancestor ) {
				if ( $ancestor->check_password( $password, false ) ) {
					$valid_for_term = $ancestor->term_id;
					break;
				}
			}
		}

		return $valid_for_term;
	}

	/**
	 * Does this category have password, role or user protection?
	 *
	 * @return boolean true if protected
	 */
	public function has_protection() {
		return $this->has_password_protection() || $this->has_role_protection() || $this->has_user_protection();
	}

	/**
	 * Does this category have password protection?
	 *
	 * @return boolean true if password protected
	 */
	public function has_password_protection() {
		return 'protected' === $this->visibility && ! empty( $this->passwords );
	}

	/**
	 * Does this category have role protection?
	 *
	 * @return boolean true if role protected
	 */
	public function has_role_protection() {
		return 'protected' === $this->visibility && ! empty( $this->roles );
	}

	/**
	 * Does this category have user protection?
	 *
	 * @return boolean true if user protected
	 */
	public function has_user_protection() {
		return 'protected' === $this->visibility && ! empty( $this->users );
	}

	/**
	 * Does this category have private protection?
	 *
	 * @return boolean true if private protection set
	 */
	public function has_private_protection() {
		return 'private' === $this->visibility;
	}

	/**
	 * Is this category unlocked by password, role, user, or for private access?
	 *
	 * @return boolean true if unlocked.
	 */
	public function is_unlocked() {
		return $this->is_unlocked_by_password() || $this->is_unlocked_by_role() || $this->is_unlocked_by_user() || $this->is_unlocked_for_private_access();
	}

	/**
	 * Is this category unlocked by password?
	 *
	 * @return boolean true if password protected and the correct password has been entered.
	 */
	public function is_unlocked_by_password() {
		return $this->has_password_protection() && $this->correct_password_entered();
	}

	/**
	 * Is this category unlocked by role?
	 *
	 * @return boolean true if role protected and the current user has one of the required roles.
	 */
	public function is_unlocked_by_role() {
		return $this->has_role_protection() && $this->current_user_allowed_by_role();
	}

	/**
	 * Is this category unlocked by the current user?
	 *
	 * @return boolean true if user protected and the current user is allowed access.
	 */
	public function is_unlocked_by_user() {
		return $this->has_user_protection() && $this->current_user_allowed_by_id();
	}

	/**
	 * Is this category private and unlocked by the current user?
	 *
	 * @return boolean true if unlocked for private access.
	 */
	public function is_unlocked_for_private_access() {
		return $this->has_private_protection() && $this->current_user_allowed_private_access();
	}

	/**
	 * Is this category public?
	 *
	 * If $check_ancestors is true, and this category is public, all ancestor categories will also be
	 * checked. If any are not public, then this function returns false.
	 *
	 * @param boolean $check_ancestors Whether the check the ancestors as well.
	 * @return boolean true if public.
	 */
	public function is_public( $check_ancestors = false ) {
		$public = ( 'public' === $this->visibility );

		// Only check ancestors if flag set and this category is public
		if ( $public && $check_ancestors ) {
			foreach ( $this->ancestors() as $ancestor ) {
				if ( ! $ancestor->is_public( false ) ) {
					return false;
				}
			}
		}

		return $public;
	}

	/**
	 * Is this category protected? 'Protected' can mean a password is required, or the category
	 * is locked to specific roles or users.
	 *
	 * This function will return false for private categories, and there is a separate function
	 * (is_private) to check for private level access.
	 *
	 * If $check_ancestors is true, and this category is not protected, its ancestor categories will
	 * also be checked. The function halts once the first protected ancestor is found.
	 * *
	 * @param boolean $check_ancestors Whether to check the ancestor categories as well (if any).
	 * @return boolean true if this category is protected.
	 */
	public function is_protected( $check_ancestors = false ) {
		$protected = $this->has_protection();

		// If this category itself is unlocked, then it's not protected (regardless of parent protection level).
		if ( $protected && $this->is_unlocked() ) {
			return false;
		}

		// Only check ancestors if flag set and this category itself is not protected.
		if ( ! $protected && $check_ancestors ) {
			foreach ( $this->ancestors() as $ancestor ) {
				if ( $ancestor->is_protected( false ) ) {
					return true;
				}
			}
		}

		return $protected;
	}

	/**
	 * Is this category password protected?
	 *
	 * If $check_ancestors is true, and this category is not password protected, the ancestors will also be
	 * checked. The function halts once the first password protected ancestor is found.
	 *
	 * @param boolean $check_ancestors Whether to check the ancestor categories as well (if any).
	 * @return boolean true if this category is password protected.
	 */
	public function is_password_protected( $check_ancestors = false ) {
		$password_protected = $this->has_password_protection();

		// It's not password protected if the correct password has been entered
		if ( $password_protected && $this->correct_password_entered() ) {
			return false;
		}

		// Only check ancestors if flag set and this category itself is not password protected
		if ( ! $password_protected && $check_ancestors ) {
			foreach ( $this->ancestors() as $ancestor ) {
				if ( $ancestor->is_password_protected( false ) ) {
					return true;
				}
			}
		}

		return $password_protected;
	}

	/**
	 * Is this category protected by user role?
	 *
	 * @param boolean $check_ancestors Whether to check the ancestor categories as well (if any).
	 * @return boolean true if this category is role protected.
	 */
	public function is_role_protected( $check_ancestors = false ) {
		$role_protected = $this->has_role_protection();

		if ( $role_protected && $this->current_user_allowed_by_role() ) {
			return false;
		}

		// Only check ancestors if flag set and this category itself is not role protected
		if ( ! $role_protected && $check_ancestors ) {
			foreach ( $this->ancestors() as $ancestor ) {
				if ( $ancestor->is_role_protected( false ) ) {
					return true;
				}
			}
		}

		return $role_protected;
	}

	/**
	 * Is this category protected by user (i.e. only specific users have access)?
	 *
	 * @param boolean $check_ancestors Whether to check the ancestor categories as well (if any).
	 * @return boolean true if this category is user protected.
	 */
	public function is_user_protected( $check_ancestors = false ) {
		$user_protected = $this->has_user_protection();

		if ( $user_protected && $this->current_user_allowed_by_id() ) {
			return false;
		}

		// Only check ancestors if flag set and this category itself is not user protected
		if ( ! $user_protected && $check_ancestors ) {
			foreach ( $this->ancestors() as $ancestor ) {
				if ( $ancestor->is_user_protected( false ) ) {
					return true;
				}
			}
		}

		return $user_protected;
	}

	/**
	 * Is this category private? A private category is one that can only be viewed by users with
	 * the 'read_private_products' capability.
	 *
	 * If $check_ancestors is true, and this category is not private, its ancestor categories will
	 * also be checked. The function halts once the first private ancestor is found.
	 *
	 * @param boolean $check_ancestors Whether to check the ancestors as well.
	 * @return boolean true if private.
	 */
	public function is_private( $check_ancestors = false ) {
		$private = $this->has_private_protection();

		if ( $private && $this->current_user_allowed_private_access() ) {
			return false;
		}

		// Only check ancestors if flag set and this category itself is not private
		if ( ! $private && $check_ancestors ) {
			foreach ( $this->ancestors() as $ancestor ) {
				if ( $ancestor->is_private( false ) ) {
					return true;
				}
			}
		}

		return $private;
	}

	/**
	 * Returns true if this category has had the correct password entered by the current user. The
	 * category password cookie is checked, and if it matches any one of the passwords for this category,
	 * then it returns true, otherwise returns false.
	 *
	 * @return boolean true if the correct password has been entered.
	 */
	private function correct_password_entered() {
		// Can't be unlocked by password if it's not password protected.
		if ( ! $this->has_password_protection() ) {
			return false;
		}

		$cookie = WC_PPC_Util::get_password_cookie();

		// Can't be unlocked by password if there's no cookie set.
		if ( ! $cookie ) {
			return false;
		}

		if ( $cookie['term_id'] !== $this->term_id ) {
			// Term ID doesn't match, so not valid.
			return false;
		}

		// Valid cookie, so now check password.
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$hasher = new PasswordHash( 8, true );

		foreach ( $this->passwords as $password ) {
			if ( $hasher->CheckPassword( $password, $cookie['password_hash'] ) ) {
				// Correct password, so category is unlocked.
				return true;
			}
		}

		return false;
	}

	private function current_user_allowed_by_role() {
		// If there are no roles, then it can't be unlocked by role.
		if ( ! $this->roles ) {
			return false;
		}

		$allowed = false;

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			// If there's a role overlap, then the current user has at least one of the required roles, so category is unlocked.
			$allowed = count( array_intersect( $user->roles, $this->roles ) ) > 0;
		}

		return apply_filters( 'wc_ppc_current_user_allowed_by_role', $allowed, $this->term_id );
	}

	private function current_user_allowed_by_id() {
		// If there are no users, then it can't be unlocked by user.
		if ( ! $this->users ) {
			return false;
		}

		$allowed = false;

		if ( is_user_logged_in() ) {
			$user	 = wp_get_current_user();
			$allowed = in_array( $user->ID, $this->users );
		}

		return apply_filters( 'wc_ppc_current_user_allowed_by_id', $allowed, $this->term_id );
	}

	private function current_user_allowed_private_access() {
		//@todo: Create 'read_private_product_categories' cap and inherit settings from 'read_private_products'.
		return apply_filters( 'wc_ppc_current_user_allowed_private_access', current_user_can( 'read_private_products' ), $this->term_id );
	}

}
