<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

function fbak_guess_username_by_email( $email ) {
    $username = sanitize_user( current( explode( '@', $email ) ), true );

    // Ensure username is unique.
    $append = 1;
    $o_username = $username;

    while ( username_exists( $username ) ) {
        $username = $o_username . $append;
        $append++;
    }
    return $username;
}

function fbak_guess_username_by_phone( $phone ) {
    $username = sanitize_user( $phone, true );

    // Ensure username is unique.
    $append = 1;
    $o_username = $username . '_';

    while ( username_exists( $username ) ) {
        $username = $o_username . $append;
        $append++;
    }
    return $username;
}

function fbak_redirect_after_sms_login() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );
    
    $redirect = admin_url();
    if ( isset($fbak_settings['fbak_sms_login_redirect']) && $fbak_settings['fbak_sms_login_redirect'] == 'home_page' ) {
        $redirect = home_url();
    } elseif ( isset($fbak_settings['fbak_sms_login_redirect']) && $fbak_settings['fbak_sms_login_redirect'] == 'profile_page' ) {
        $redirect = admin_url( 'profile.php' );
    } elseif ( isset($fbak_settings['fbak_sms_login_redirect']) && $fbak_settings['fbak_sms_login_redirect'] == 'custom_url' ) {
        $redirect = !empty($fbak_settings['fbak_sms_login_redirect_custom_url']) ? esc_url( $fbak_settings['fbak_sms_login_redirect_custom_url'] ) : admin_url();
    }

    $redirect = apply_filters( 'fbak/account_kit_sms_login_success_url', $redirect );
    return $redirect;
}

function fbak_redirect_after_email_login() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );
    
    $redirect = admin_url();
    if ( isset($fbak_settings['fbak_email_login_redirect']) && $fbak_settings['fbak_email_login_redirect'] == 'home_page' ) {
        $redirect = home_url();
    } elseif ( isset($fbak_settings['fbak_email_login_redirect']) && $fbak_settings['fbak_email_login_redirect'] == 'profile_page' ) {
        $redirect = admin_url( 'profile.php' );
    } elseif ( isset($fbak_settings['fbak_email_login_redirect']) && $fbak_settings['fbak_email_login_redirect'] == 'custom_url' ) {
        $redirect = !empty($fbak_settings['fbak_email_login_redirect_custom_url']) ? esc_url( $fbak_settings['fbak_email_login_redirect_custom_url'] ) : admin_url();
    }

    $redirect = apply_filters( 'fbak/account_kit_email_login_success_url', $redirect );
    return $redirect;
}

function fbak_get_fb_app_api_version() {
    $apikit_ver = 'v1.3';
    $apikit_ver = apply_filters( 'fbak/account_kit_api_version', $apikit_ver );

    return $apikit_ver;
}

function fbak_get_account_kit_locale() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );
    
    $locale = 'en_US';
    if ( !empty($fbak_settings['fbak_ac_locale']) ) {
        $locale = $fbak_settings['fbak_ac_locale'];
    }

    return $locale;
}

function fbak_get_email_login_redirect_url() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    $redir_url = esc_url( home_url( '/fbak-auth/?fbak_check_auth=true' ) );
    if ( isset($fbak_settings['fbak_email_login_success_page']) && $fbak_settings['fbak_email_login_success_page'] == 'custom' ) {
        if ( !empty($fbak_settings['fbak_email_login_success_page_url']) ) {
            $redir_url = esc_url( $fbak_settings['fbak_email_login_success_page_url'] );
        }
    }

    if ( isset($fbak_settings['fbak_email_login_success_page']) && $fbak_settings['fbak_email_login_success_page'] == 'fb_default' ) {
        $redir_url = '';
    }

    $redir_url = apply_filters( 'fbak/account_kit_email_login_redirect_url', $redir_url );
    return $redir_url;
}

function fbak_get_disconnect_confirm_message() {
    $msg = __( 'Are you really want to disconnect this account from Account Kit?', 'fb-account-kit-login' );
    $msg = apply_filters( 'fbak/account_kit_disconnect_message', $msg );

    return $msg;
}

function fbak_get_sms_new_user_role() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );
    $role = get_option( 'default_role' );

    if ( isset($fbak_settings['fbak_sms_new_register_user_type']) ) {
        $role = $fbak_settings['fbak_sms_new_register_user_type'];
    }
    return $role;
}

function fbak_get_email_new_user_role() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );
    $role = get_option( 'default_role' );

    if ( isset($fbak_settings['fbak_email_new_register_user_type']) ) {
        $role = $fbak_settings['fbak_email_new_register_user_type'];
    }
    return $role;
}

function fbak_enable_sms_login_method() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    if ( isset($fbak_settings['fbak_enable_sms_login']) && $fbak_settings['fbak_enable_sms_login'] == 1 ) {
        return true;
    }
    return false;
}

function fbak_enable_email_login_method() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    if ( isset($fbak_settings['fbak_enable_email_login']) && $fbak_settings['fbak_enable_email_login'] == 1 ) {
        return true;
    }
    return false;
}

function fbak_enable_on_wp_login_form() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    if ( isset($fbak_settings['fbak_enable_login_form']) && $fbak_settings['fbak_enable_login_form'] == 'enable' ) {
        return true;
    }
    return false;
}

function fbak_get_site_http_protocol() {
    if ( is_ssl() || ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) ) {
        return 'https://';
    } else {
        return 'http://';
    }
}