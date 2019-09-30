<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_action( 'admin_notices', 'fbak_rating_admin_notice' );
add_action( 'admin_init', 'fbak_dismiss_rating_admin_notice' );

function fbak_rating_admin_notice() {
    // Show notice after 240 hours (10 days) from installed time.
    if ( fbak_plugin_get_installed_time() > strtotime( '-240 hours' )
        || '1' === get_option( 'fbak_plugin_dismiss_rating_notice' )
        || ! current_user_can( 'manage_options' )
        || apply_filters( 'fbak_plugin_show_sticky_notice', false ) ) {
        return;
    }

    $dismiss = wp_nonce_url( add_query_arg( 'fbak_rating_notice_action', 'fbak_dismiss_rating_true' ), 'fbak_dismiss_rating_true' ); 
    $no_thanks = wp_nonce_url( add_query_arg( 'fbak_rating_notice_action', 'fbak_no_thanks_rating_true' ), 'fbak_no_thanks_rating_true' ); ?>
    
    <div class="notice notice-success">
        <p><?php _e( 'Hey, I noticed you\'ve been using Facebook Account Kit Login for more than 1 week – that’s awesome! Could you please do me a BIG favor and give it a <strong>5-star</strong> rating on WordPress? Just to help me spread the word and boost my motivation.', 'fb-account-kit-login' ); ?></p>
        <p><a href="https://wordpress.org/support/plugin/fb-account-kit-login/reviews/?filter=5#new-post" target="_blank" class="button button-secondary"><?php _e( 'Ok, you deserve it', 'fb-account-kit-login' ); ?></a>&nbsp;
        <a href="<?php echo $dismiss; ?>" class="already-did"><strong><?php _e( 'I already did', 'fb-account-kit-login' ); ?></strong></a>&nbsp;<strong>|</strong>
        <a href="<?php echo $no_thanks; ?>" class="later"><strong><?php _e( 'Nope&#44; maybe later', 'fb-account-kit-login' ); ?></strong></a></p>
    </div>
<?php
}

function fbak_dismiss_rating_admin_notice() {

    if( get_option( 'fbak_plugin_no_thanks_rating_notice' ) === '1' ) {
        if ( get_option( 'fbak_plugin_dismissed_time' ) > strtotime( '-168 hours' ) ) {
            return;
        }
        delete_option( 'fbak_plugin_dismiss_rating_notice' );
        delete_option( 'fbak_plugin_no_thanks_rating_notice' );
    }

    if ( ! isset( $_GET['fbak_rating_notice_action'] ) ) {
        return;
    }

    if ( 'fbak_dismiss_rating_true' === $_GET['fbak_rating_notice_action'] ) {
        check_admin_referer( 'fbak_dismiss_rating_true' );
        update_option( 'fbak_plugin_dismiss_rating_notice', '1' );
    }

    if ( 'fbak_no_thanks_rating_true' === $_GET['fbak_rating_notice_action'] ) {
        check_admin_referer( 'fbak_no_thanks_rating_true' );
        update_option( 'fbak_plugin_no_thanks_rating_notice', '1' );
        update_option( 'fbak_plugin_dismiss_rating_notice', '1' );
        update_option( 'fbak_plugin_dismissed_time', time() );
    }

    wp_redirect( remove_query_arg( 'fbak_rating_notice_action' ) );
    exit;
}

function fbak_plugin_get_installed_time() {
    $installed_time = get_option( 'fbak_plugin_installed_time' );
    if ( ! $installed_time ) {
        $installed_time = time();
        update_option( 'fbak_plugin_installed_time', $installed_time );
    }
    return $installed_time;
}