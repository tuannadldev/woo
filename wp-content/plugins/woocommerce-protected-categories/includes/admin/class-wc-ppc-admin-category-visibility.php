<?php
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages the visibility field for WooCommerce categories in the WordPress admin.
 *
 * @package   WooCommerce_Protected_Categories\Admin
 * @author    Barn2 Media <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class WC_PPC_Admin_Category_Visibility implements \Barn2\Lib\Attachable {

	public function attach() {
		// Add visibility field
		add_action( 'product_cat_add_form_fields', array( $this, 'add_visibility_field' ), 20 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_visibility_field' ), 10 );

		// Save visibility field
		add_action( 'created_product_cat', array( $this, 'save_visibility_field' ), 10, 2 );
		add_action( 'edit_product_cat', array( $this, 'save_visibility_field' ), 10, 2 );

		// Add visibility column to product category table
		add_filter( 'manage_edit-product_cat_columns', array( $this, 'cat_table_visibility_column_heading' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'cat_table_visibility_column' ), 10, 3 );

		//@todo To make Visibility column sortable we need to filter 'get_terms_args' and change 'orderby' args into a meta query (if possible)
		//add_filter( 'manage_edit-product_cat_sortable_columns', array( $this, 'cat_table_make_visibility_sortable' ) );
	}

	/**
	 * Add visibility field to 'add product category' screen
	 */
	public function add_visibility_field() {
		?>
		<div class="form-field term-visibility-wrap">
			<label><?php _e( 'Visibility', 'wc-cat-protect' ); ?></label>
			<?php $this->display_visibility_group(); ?>
		</div>
		<?php
	}

	/**
	 * Add visibility field to 'edit product category' screen
	 *
	 * @param mixed $term The product category being edited
	 */
	public function edit_visibility_field( $term ) {
		?>
		<tr class="form-field term-visibility-wrap">
			<th scope="row" valign="top">
				<label><?php _e( 'Visibility', 'wc-cat-protect' ); ?></label>
			</th>
			<td>
				<?php $this->display_visibility_group( $term->term_id ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save visibility and password for product category.
	 * Use deprecated add, update and delete_woocommerce_term_meta functions for now, to preserve compatibility for WP < 4.4.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id The term taxonomy ID
	 */
	public function save_visibility_field( $term_id, $tt_id = '' ) {
		$visibility = filter_input( INPUT_POST, 'product_cat_visibility', FILTER_SANITIZE_STRING );

		// Bail if no visibility to save (e.g. on 'quick edit')
		if ( ! $visibility ) {
			return;
		}

		if ( 'protected' === $visibility ) {
			$protection = filter_input( INPUT_POST, 'product_cat_protection', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

			// If no protection options set, revert to public visibility, then bail.
			if ( ! $protection ) {
				update_woocommerce_term_meta( $term_id, 'visibility', 'public' );
				return;
			}

			// Remove existing meta for this category
			delete_woocommerce_term_meta( $term_id, 'password' );
			delete_woocommerce_term_meta( $term_id, 'user_roles' );
			delete_woocommerce_term_meta( $term_id, 'users' );

			// Passwords
			if ( in_array( 'password', $protection ) ) {
				$passwords = filter_input( INPUT_POST, 'product_cat_passwords', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

				// Filter array to remove any empty inputs
				$passwords = is_array( $passwords ) ? array_values( array_filter( $passwords ) ) : array();

				if ( empty( $passwords ) ) {
					// Set default password in case none was entered
					$passwords = array( 'password123' );
				}

				add_woocommerce_term_meta( $term_id, 'password', $passwords );
			}

			// User roles
			if ( in_array( 'user_role', $protection ) ) {
				$user_roles = filter_input( INPUT_POST, 'product_cat_user_roles', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
				add_woocommerce_term_meta( $term_id, 'user_roles', $user_roles );
			}

			// Users
			if ( in_array( 'user', $protection ) ) {
				$user_ids = filter_input( INPUT_POST, 'product_cat_users', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
				add_woocommerce_term_meta( $term_id, 'users', $user_ids );
			}
		}

		update_woocommerce_term_meta( $term_id, 'visibility', $visibility );
	}

	public function cat_table_make_visibility_sortable( $columns ) {
		return $this->list_table_insert_after_column( $columns, 'name', 'visibility', 'visibility' );
	}

	public function cat_table_visibility_column_heading( $columns ) {
		return $this->list_table_insert_after_column( $columns, 'name', 'visibility', __( 'Visibility', 'wc-cat-protect' ) );
	}

	public function cat_table_visibility_column( $output, $column, $term_id ) {
		if ( 'visibility' === $column ) {
			$category = WC_PPC_Util::get_category_visibility( $term_id );

			if ( 'public' === $category->visibility ) {
				foreach ( $category->ancestors() as $ancestor ) {
					if ( 'public' !== $ancestor->visibility ) {
						$output = sprintf( '<em>(%s)</em>', __( 'inherited', 'wc-cat-protect' ) );
						break;
					}
				}
				if ( ! $output ) {
					$output = __( 'Public', 'wc-cat-protect' );
				}
			} elseif ( 'protected' === $category->visibility ) {
				$output			 = __( 'Protected', 'wc-cat-protect' );
				$protection_type = array();

				if ( $category->has_password_protection() ) {
					$protection_type[] = __( 'password', 'wc-cat-protect' );
				}
				if ( $category->has_role_protection() ) {
					$protection_type[] = __( 'user role', 'wc-cat-protect' );
				}
				if ( $category->has_user_protection() ) {
					$protection_type[] = __( 'user', 'wc-cat-protect' );
				}

				if ( $protection_type ) {
					/* translators: 1: 'Protected' label, 2: the protection type. */
					$output = sprintf( _x( '%1$s - %2$s', 'protected visibility format', 'wc-cat-protect' ), $output, implode( $protection_type, ', ' ) );
				}
			} elseif ( 'private' === $category->visibility ) {
				$output = __( 'Private', 'wc-cat-protect' );
			}
		}

		return $output;
	}

	private function display_visibility_group( $term_id = false ) {
		$category = $term_id ? WC_PPC_Util::get_category_visibility( $term_id ) : null;

		$visibility		 = $category ? $category->visibility : 'public';
		$cat_passwords	 = $category ? $category->passwords : array();
		$cat_user_roles	 = $category ? $category->roles : array();
		$cat_users		 = $category ? $category->users : array();

		$has_passwords	 = count( $cat_passwords ) > 0;
		$has_user_roles	 = count( $cat_user_roles ) > 0;
		$has_users		 = count( $cat_users ) > 0;

		// If there are no passwords, add an empty one so we always display at least one password input
		if ( empty( $cat_passwords ) ) {
			$cat_passwords = array( '' );
		}
		?>
		<fieldset id="product_cat_visibility" class="cat-visibility">
			<legend class="screen-reader-text"><?php _e( 'Visibility', 'wc-cat-protect' ); ?></legend>
			<label class="cat-visibility__option"><input type="radio" name="product_cat_visibility" id="public_visibility" value="public" <?php checked( $visibility, 'public' ); ?> /> <?php _e( 'Public', 'wc-cat-protect' ); ?></label>
			<label class="cat-visibility__option"><input type="radio" name="product_cat_visibility" id="protected_visibility" value="protected" <?php checked( $visibility, 'protected' ); ?> /> <?php _e( 'Protected', 'wc-cat-protect' ); ?> <?php echo wc_help_tip( __( 'Protect the category by password, or restrict to specific roles or users. Its products and sub-categories will inherit the same protection.', 'wc-cat-protect' ) ); ?></label>
			<fieldset id="product_cat_protection" class="cat-protection cat-visibility__field"<?php
			if ( 'protected' !== $visibility ) {
				echo ' style="display:none;"';
			}
			?>>
				<legend class="screen-reader-text"><?php _e( 'Protect by:', 'wc-cat-protect' ); ?></legend>

				<div class="cat-protection__item">
					<label class="cat-protection__option"><input type="checkbox" class="cat-protection__check" id="password_protection" name="product_cat_protection[]" value="password"<?php checked( $has_passwords ); ?> /> <?php _e( 'Password protected', 'wc-cat-protect' ); ?></label>
					<div id="product_cat_passwords" class="cat-protection__field"<?php
					if ( ! $has_passwords ) {
						echo ' style="display:none;"';
					}
					?>>
							 <?php
							 foreach ( $cat_passwords as $index => $password ) :
								 $first = $index === 0;
								 ?>
							<div class="cat-password" data-first="<?php echo esc_attr( $first ? 'true' : 'false'  ); ?>" data-index="<?php echo esc_attr( $index ); ?>">
								<label class="screen-reader-text"><?php _e( 'Enter password', 'wc-cat-protect' ); ?></label>
								<input class="cat-password__field" id="product_cat_password_<?php echo esc_attr( $index ); ?>" type="text" name="product_cat_passwords[]" value="<?php echo esc_attr( $password ); ?>" placeholder="<?php esc_attr_e( 'Enter password&hellip;', 'wc-cat-protect' ); ?>" />
								<span class="cat-password__icons">
									<a class="cat-password__icon cat-password__icon--add" data-action="add" href="#"><span class="dashicons dashicons-plus"></span></a>
									<?php if ( ! $first ) : ?>
										<a class="cat-password__icon cat-password__icon--delete" data-action="delete" href="#"><span class="dashicons dashicons-minus"></span></a>
									<?php endif; ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="cat-protection__item">
					<label class="cat-protection__option"><input type="checkbox" class="cat-protection__check" id="user_role_protection" name="product_cat_protection[]" value="user_role"<?php checked( $has_user_roles ); ?> /> <?php _e( 'User roles', 'wc-cat-protect' ); ?></label>
					<div class="cat-protection__field"<?php
					if ( ! $has_user_roles ) {
						echo ' style="display:none;"';
					}
					?>>
						<select name="product_cat_user_roles[]" id="product_cat_user_roles" class="cat-protection__select" multiple="multiple" style="width:95%;" data-placeholder="<?php esc_attr_e( 'Select or search user roles&hellip;', 'wc-cat-protect' ); ?>">
							<?php foreach ( apply_filters( 'wc_ppc_admin_available_roles', wp_roles()->roles ) as $role => $details ) : ?>
								<?php
								$name		 = translate_user_role( $details['name'] );
								$selected	 = in_array( $role, $cat_user_roles );
								?>
								<option value="<?php echo esc_attr( $role ); ?>"<?php selected( $selected ); ?>><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="cat-protection__item cat-protection__item--last">
					<label class="cat-protection__option"><input type="checkbox" class="cat-protection__check" id="user_protection" name="product_cat_protection[]" value="user"<?php checked( $has_users ); ?> /> <?php _e( 'Users', 'wc-cat-protect' ); ?></label>
					<div class="cat-protection__field"<?php
					if ( ! $has_users ) {
						echo ' style="display:none;"';
					}
					?>>
						<select name="product_cat_users[]" id="product_cat_users" class="cat-protection__select" multiple="multiple" style="width:95%;" data-placeholder="<?php esc_attr_e( 'Select or search users&hellip;', 'wc-cat-protect' ); ?>">
							<?php
							$get_users_args	 = apply_filters( 'wc_ppc_admin_user_select_args', array(
								'blog_id' => get_current_blog_id(),
								'orderby' => 'display_name',
								'order' => 'ASC',
								'fields' => array( 'ID', 'user_login', 'display_name' )
								) );
							$site_users		 = apply_filters( 'wc_ppc_admin_available_users', get_users( $get_users_args ) );
							?>
							<?php foreach ( $site_users as $user ) : ?>
								<?php
								/* translators: 1: display name, 2: user_login */
								$display	 = sprintf( _x( '%1$s (%2$s)', 'user dropdown', 'wc-cat-protect' ), $user->display_name, $user->user_login );
								$selected	 = in_array( $user->ID, $cat_users );
								?>
								<option value="<?php echo esc_attr( $user->ID ); ?>"<?php selected( $selected ); ?>><?php echo $display ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</fieldset>
			<label class="cat-visibility__option"><input type="radio" name="product_cat_visibility" id="private_visibility" value="private" <?php checked( $visibility, 'private' ); ?> /> <?php _e( 'Private', 'wc-cat-protect' ); ?> <?php echo wc_help_tip( __( 'Hide the category from everyone except users with access to private content (normally Administrators and Store Managers).', 'wc-cat-protect' ) ); ?></label>
		</fieldset>
		<?php
	}

	private function list_table_insert_after_column( $columns, $after_key, $insert_key, $insert_value ) {
		$new_columns = array();

		foreach ( $columns as $key => $column ) {
			if ( $after_key === $key ) {
				$new_columns[$key]			 = $column;
				$new_columns[$insert_key]	 = $insert_value;
			} else {
				$new_columns[$key] = $column;
			}
		}

		return $new_columns;
	}

}
