<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_filter( 'manage_users_columns', 'fbak_auth_status_to_user_table', 10, 3 );
add_action( 'manage_users_custom_column', 'fbak_manage_users_custom_column', 10, 3 );
add_action( 'admin_print_styles-users.php', 'fbak_print_admin_users_css' ); 

  // create custom column
function fbak_auth_status_to_user_table( $columns ) {
	// build custom columns
	$columns['fbak-login'] = '<span class="dashicons dashicons-facebook" title="' . __( 'Account Kit Status', 'fb-account-kit-login' ) . '"><span class="screen-reader-text">' . __( 'Account Kit Status', 'fb-account-kit-login' ) . '</span></span>';
	
	return $columns;
}

function fbak_manage_users_custom_column( $value, $column_name, $user_id ) {
	// get author meta
	$chk_login = get_user_meta( $user_id, '_fb_accountkit_id', true );
	$chk_mode = get_user_meta( $user_id, '_fb_accountkit_auth_mode', true );

	$success = __( 'Connected with Account Kit', 'fb-account-kit-login' );
    if ( $chk_mode === 'phone' ) {
        $success .= __( ' via Phone Number', 'fb-account-kit-login' );
    } elseif ( $chk_mode === 'email' ) {
        $success .= __( ' via Email Address', 'fb-account-kit-login' );
	}
	
	switch ( $column_name ) {
		case 'fbak-login' :
			if( ! $chk_login ) {
                return '<span class="dashicons dashicons-editor-unlink" style="color:#e14d43;font-size:18px;" title="' . esc_attr__( 'Not Connected with Account Kit', 'fb-account-kit-login' ) . '"></span>';;
            }
            return '<span class="dashicons dashicons-admin-links" style="color:#3cb371;font-size:18px;" title="' . $success . '"></span>';
			break;
    }

	return $value;
}

function fbak_print_admin_users_css() { ?>
    <style type="text/css"> .fixed .column-fbak-login { width: 5%; } </style>
    <?php
}