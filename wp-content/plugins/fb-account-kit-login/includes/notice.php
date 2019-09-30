<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_action( 'admin_notices', 'fbak_new_plugin_install_notice' );
add_action( 'admin_init', 'fbak_dismiss_profile_link_notice' );

function fbak_new_plugin_install_notice() {
    // Show activation notice
    if( get_transient( 'fbak-admin-notice-on-activation' ) ) { ?>
        <div class="notice notice-success">
            <p><strong><?php printf( __( 'Thanks for installing %1$s v%2$s plugin. Click <a href="%3$s">here</a> to configure plugin settings.', 'fb-account-kit-login' ), 'Facebook Account Kit Login', FBAK_PLUGIN_VERSION, admin_url( 'admin.php?page=fb-account-kit-login' ) ); ?></strong></p>
        </div> <?php
        delete_transient( 'fbak-admin-notice-on-activation' );
    }

    // Show notice to unlinked users
    if( ! get_user_meta( get_current_user_id(), '_fb_accountkit_id', true ) ) {
        if ( '1' !== get_user_meta( get_current_user_id(), '_fbak_link_notice_hide', true ) && ( fbak_enable_sms_login_method() || fbak_enable_email_login_method() ) ) {
            $dismiss = wp_nonce_url( add_query_arg( 'fbak_profile_link_action', 'fbak_pl_hide_true' ), 'fbak_pl_hide_true' ); ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong><?php printf( __( 'Your Account is not linked with Facebook Account Kit yet. Please connect your account with Facebook Account Kit from your <a href="%s">profile</a> for a secure and passwordless login.', 'fb-account-kit-login' ), admin_url( 'profile.php' ) ); ?></strong>
                <span style="float: right;"><a href="<?php echo $dismiss; ?>"><strong><?php _e( 'Hide', 'fb-account-kit-login' ); ?></strong></a><span></p>
            </div> <?php
        }
    }
}

function fbak_dismiss_profile_link_notice() {
    if ( ! isset( $_GET['fbak_profile_link_action'] ) ) {
        return;
    }

    if ( 'fbak_pl_hide_true' === $_GET['fbak_profile_link_action'] ) {
        check_admin_referer( 'fbak_pl_hide_true' );
        update_user_meta( get_current_user_id(), '_fbak_link_notice_hide', '1' );
    }

    wp_redirect( remove_query_arg( 'fbak_profile_link_action' ) );
    exit;
}