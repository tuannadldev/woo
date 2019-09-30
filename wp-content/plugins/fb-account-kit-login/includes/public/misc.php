<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_action( 'wp_head', 'fbak_add_custom_css_to_head' );
add_action( 'wp_footer', 'fbak_add_click_login_support' );
add_action( 'init', 'fbak_email_login_redirect_to_url' );

function fbak_add_custom_css_to_head() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    $style = '';
    if( !empty( $fbak_settings['fbak_custom_css'] ) ) {
        $style .= '<style type="text/css">' . $fbak_settings['fbak_custom_css'] . '</style>'."\n";
    }
    if( is_user_logged_in() ) {
        $style .= '<style type="text/css"> .fbak-sms-login, .fbak-email-login { display: none !important; } </style>'."\n";
    }
    echo $style;
}

function fbak_add_click_login_support() { 
    if( ! is_user_logged_in() ) { ?>
        <script type="text/javascript">
            jQuery(document).ready( function($) {
                $(".fbak-sms-login").click(function (e) {
                    smsLogin();
                    e.preventDefault();
                });
                $(".fbak-email-login").click(function (e) {
                    emailLogin();
                    e.preventDefault();
                });
            });
        </script> 
    <?php }
}

function fbak_email_login_redirect_to_url() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    $url = html_entity_decode( esc_url( fbak_get_site_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
    
    $message = sprintf( __( '%1$sAuthentication Successful%2$sAuthentication with your email address is successful. Now go back to the login page and login page will be automatically redirected to your account if you are a valid user of this website.%3$sPlease close this window.%4$s', 'fb-account-kit-login' ), '<h1 style="color:#46b450;margin-top:-15px;">', '</h1><p style="text-align: justify;font-weight: 700;margin-top: 8px;margin-bottom: -4px;">', '</p><p style="text-align: justify;font-weight: 700;margin-bottom: -10px;">', '</p>' );
    if ( isset($fbak_settings['fbak_email_new_register']) && $fbak_settings['fbak_email_new_register'] == 1 ) {
        $message = sprintf( __( '%1$sAuthentication Successful%2$sAuthentication with your email address is successful. Now go back to the login page and login page will be automatically redirected to your account.%3$sPlease close this window.%4$s', 'fb-account-kit-login' ), '<h1 style="color:#46b450;margin-top:-15px;">', '</h1><p style="text-align: justify;font-weight: 700;margin-top: 8px;margin-bottom: -4px;">', '</p><p style="text-align: justify;font-weight: 700;margin-bottom: -10px;">', '</p>' );
    }
    $message = apply_filters( 'fbak/account_kit_email_login_redirect_message', $message );

    $title = __( 'Authentication Successful - ', 'fb-account-kit-login' ) . get_bloginfo( 'name' );
    $title = apply_filters( 'fbak/account_kit_email_login_redirect_title', $title );

    $args = array(
        'response' => '200'
    );

    if ( isset($_GET['status']) && $_GET['status'] === 'PARTIALLY_AUTHENTICATED' && isset($_GET['code']) && strpos( $url, home_url( '/fbak-auth/?code=' ) ) !== false ) {
        wp_die( $message, $title, $args );
        exit;
    }
}


 