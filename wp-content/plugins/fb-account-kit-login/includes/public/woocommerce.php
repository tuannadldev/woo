<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'init', 'fbak_woocommerce_element_init' );
}

function fbak_woocommerce_element_init() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    // add rewrite endpoint
    $endpoint = !empty($fbak_settings['fbak_account_kit_endpoint']) ? esc_html( $fbak_settings['fbak_account_kit_endpoint'] ) : 'account-kit';
    add_rewrite_endpoint( $endpoint, EP_PAGES );

    if( fbak_enable_sms_login_method() || fbak_enable_email_login_method() ) {
        if( isset($fbak_settings['fbak_enable_woo_login_form']) && $fbak_settings['fbak_enable_woo_login_form'] == 1 ) {
            add_action( 'woocommerce_login_form_end', 'fbak_add_html_element_to_woo_login_form' );
        }

        if( isset($fbak_settings['fbak_enable_woo_reg_form']) && $fbak_settings['fbak_enable_woo_reg_form'] == 1 ) {
            add_action( 'woocommerce_register_form_end', 'fbak_add_html_element_to_woo_login_form' );
        }

        if( isset($fbak_settings['fbak_allow_customer_auth']) && $fbak_settings['fbak_allow_customer_auth'] == 1 ) {
            add_filter( 'woocommerce_account_menu_items', 'fbak_add_custom_account_kit_link', 40 );
            add_action( "woocommerce_account_{$endpoint}_endpoint", 'fbak_my_account_endpoint_content' );
        }
        
        if( isset($fbak_settings['fbak_redirect_to_checkout']) && $fbak_settings['fbak_redirect_to_checkout'] == 1 ) {
            add_filter( 'fbak/account_kit_sms_login_success_url', 'fbak_add_auto_redir_to_checkout' );
        }
    }
}

function fbak_add_html_element_to_woo_login_form() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );

    $sms_label = !empty($fbak_settings['fbak_sms_label_text']) ? $fbak_settings['fbak_sms_label_text'] : __( 'Login with SMS', 'fb-account-kit-login' );
    $email_label = !empty($fbak_settings['fbak_email_label_text']) ? $fbak_settings['fbak_email_label_text'] : __( 'Login with Email', 'fb-account-kit-login' );
    $sms_class = !empty($fbak_settings['fbak_sms_btn_class']) ? $fbak_settings['fbak_sms_btn_class'] : 'button btn';
    $email_class = !empty($fbak_settings['fbak_email_btn_class']) ? $fbak_settings['fbak_email_btn_class'] : 'button btn';
    
    $sms_label = apply_filters( 'fbak/woocommerce_sms_label', $sms_label );
    $email_label = apply_filters( 'fbak/woocommerce_email_label', $email_label );
    $sms_class = apply_filters( 'fbak/woocommerce_sms_class', $sms_class );
    $email_class = apply_filters( 'fbak/woocommerce_email_class', $email_class );
    $sep = apply_filters( 'fbak/woocommerce_form_separator', __( 'Or', 'fb-account-kit-login' ) ); 
    
    $notice_text = __( 'Please wait until we authenticate you.', 'fb-account-kit-login' );
    if( !empty($fbak_settings['fbak_auth_waiting_message']) ) {
        $notice_text = strip_tags( $fbak_settings['fbak_auth_waiting_message'] );
    }
    $notice_text = apply_filters( 'fbak/account_kit_woo_login_notice_message', $notice_text ); ?>

    <div class="fb-ackit-wrap">
        <div class="fb-ackit-or">
            <span class="fb-ackit-or-sep"><?php echo $sep; ?></span>
        </div>
        <div class="fb-ackit-buttons">
            <?php if( fbak_enable_sms_login_method() ) : ?>
                <button href="#" onclick="smsLogin(); return false;" class="<?php echo $sms_class; ?>"><?php echo $sms_label; ?></button>
            <?php endif; ?>
            <?php if( fbak_enable_email_login_method() ) : ?>
                <button href="#" onclick="emailLogin(); return false;" class="<?php echo $email_class; ?>"><?php echo $email_label; ?></button>
            <?php endif; ?>
        </div>
    </div>
    <div class="fb-ackit-wait" style="text-align: center;display: none;"><?php echo $notice_text; ?></div>
    <?php
}

function fbak_add_custom_account_kit_link( $menu_links ) {
    $fbak_settings = get_option( 'fbak_plugin_settings' );
    $endpoint = !empty($fbak_settings['fbak_account_kit_endpoint']) ? esc_html( $fbak_settings['fbak_account_kit_endpoint'] ) : 'account-kit';
    $label = !empty($fbak_settings['fbak_account_kit_endpoint_label']) ? esc_html( $fbak_settings['fbak_account_kit_endpoint_label'] ) : __( 'Authentication', 'fb-account-kit-login' );
    
	$menu_links = array_slice( $menu_links, 0, 5, true ) 
	+ array( $endpoint => $label )
	+ array_slice( $menu_links, 5, NULL, true );
 
	return $menu_links;
}

function fbak_my_account_endpoint_content() {
    $fbak_settings = get_option( 'fbak_plugin_settings' );
    $description = !empty($fbak_settings['fbak_woo_auth_description']) ? $fbak_settings['fbak_woo_auth_description'] : __( 'If your Account is not linked with Facebook Account Kit, please connect your account with Facebook Account Kit for a secure and passwordless login.', 'fb-account-kit-login' );
    $button = apply_filters( 'fbak/account_kit_woocommerce_button_css_class', 'button' );

    $connected = get_user_meta( get_current_user_id(), '_fb_accountkit_id', true );
    $mode = get_user_meta( get_current_user_id(), '_fb_accountkit_auth_mode', true );

    $success = __( 'Connected', 'fb-account-kit-login' );
    if ( $mode === 'phone' ) {
        $success = __( 'Connected via Phone', 'fb-account-kit-login' );
    } elseif ( $mode === 'email' ) {
        $success = __( 'Connected via Email', 'fb-account-kit-login' );
    } ?>
    <div class="fbak-woocommerce-main"><p class="fbak-woocommerce-description"><?php echo $description; ?></p></div>
    <div class="fbak-woocommerce-content">
        <?php if( ! $connected ) { ?>
            <?php if( fbak_enable_sms_login_method() ) { ?>
                <button class="<?php echo $button; ?>" onclick="smsLogin(); return false;"><?php _e( 'Connect with Phone', 'fb-account-kit-login' ); ?></button>
            <?php } ?>
            <?php if( fbak_enable_email_login_method() ) { ?>
                <button class="<?php echo $button; ?>" onclick="emailLogin(); return false;"><?php _e( 'Connect with Email', 'fb-account-kit-login' ); ?></button>
            <?php } ?>
        <?php } else { ?>
            <button class="<?php echo $button; ?>" disabled><?php echo $success; ?></button>
            <button class="<?php echo $button; ?>" onclick="fbAcDisconnect(); return false;"><?php _e( 'Disconnect', 'fb-account-kit-login' ); ?></button>
        <?php } ?>
        <span id="fbak-user-id" style="display: none;"><?php echo get_current_user_id(); ?></span><span id="fbak-check-msg" style="display: none;"><?php echo fbak_get_disconnect_confirm_message(); ?></span>
    </div>
 <?php
}

function fbak_add_auto_redir_to_checkout( $redirect ) {
    if ( is_page( wc_get_page_id( 'checkout' ) ) ) {
        return get_permalink( wc_get_page_id( 'checkout' ) );
    }
    return $redirect;
}