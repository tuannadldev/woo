<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

 // register settings
add_action( 'admin_init', 'fbak_register_plugin_settings' );

function fbak_register_plugin_settings() {

    add_settings_section('fbak_plugin_general_section', '', null, 'fbak_plugin_general_option');
        add_settings_field('fbak_app_id', __( 'Enter Facebook App ID:', 'fb-account-kit-login' ), 'fbak_app_id_display', 'fbak_plugin_general_option', 'fbak_plugin_general_section', array( 'label_for' => 'fbak-appid' ));
        add_settings_field('fbak_accountkit_secret_key', __( 'Enter Account Kit App Secret:', 'fb-account-kit-login' ), 'fbak_accountkit_secret_key_display', 'fbak_plugin_general_option', 'fbak_plugin_general_section', array( 'label_for' => 'fbak-seckey' ));
        add_settings_field('fbak_ac_locale', __( 'Enter Account Kit Locale:', 'fb-account-kit-login' ), 'fbak_ac_locale_display', 'fbak_plugin_general_option', 'fbak_plugin_general_section', array( 'label_for' => 'fbak-locale' ));
        add_settings_field('fbak_ac_res_url', __( 'Account Kit Redirect URL:', 'fb-account-kit-login' ), 'fbak_ac_res_url_display', 'fbak_plugin_general_option', 'fbak_plugin_general_section', array( 'label_for' => 'fbak-resurl' ));
        
    add_settings_section('fbak_plugin_sms_section', '', null, 'fbak_plugin_sms_option');
        add_settings_field('fbak_enable_sms_login', __( 'Enable SMS Login:', 'fb-account-kit-login' ), 'fbak_enable_sms_login_display', 'fbak_plugin_sms_option', 'fbak_plugin_sms_section', array( 'label_for' => 'fbak-enablesms' ));
        add_settings_field('fbak_sms_new_register', __( 'New User Registration:', 'fb-account-kit-login' ), 'fbak_sms_new_register_display', 'fbak_plugin_sms_option', 'fbak_plugin_sms_section', array( 'label_for' => 'fbak-sms-reg' ));
        add_settings_field('fbak_sms_new_register_user_type', __( 'New User Default Role:', 'fb-account-kit-login' ), 'fbak_sms_new_register_user_type_display', 'fbak_plugin_sms_option', 'fbak_plugin_sms_section', array( 'label_for' => 'fbak-sms-reg-user-type', 'class' => 'fbak-sms-user-type' ));
        add_settings_field('fbak_sms_label_text', __( 'SMS Login Button Label:', 'fb-account-kit-login' ), 'fbak_sms_label_text_display', 'fbak_plugin_sms_option', 'fbak_plugin_sms_section', array( 'label_for' => 'fbak-sms-label-text' ));
        add_settings_field('fbak_sms_btn_class', __( 'SMS Login Button Class:', 'fb-account-kit-login' ), 'fbak_sms_btn_class_display', 'fbak_plugin_sms_option', 'fbak_plugin_sms_section', array( 'label_for' => 'fbak-sms-btn-class' ));
        add_settings_field('fbak_sms_login_redirect', __( 'After Login Redirect to:', 'fb-account-kit-login' ), 'fbak_sms_login_redirect_display', 'fbak_plugin_sms_option', 'fbak_plugin_sms_section', array( 'label_for' => 'fbak-sms-redirect' ));
        
    add_settings_section('fbak_plugin_email_section', '', null, 'fbak_plugin_email_option');
        add_settings_field('fbak_enable_email_login', __( 'Enable Email Login:', 'fb-account-kit-login' ), 'fbak_enable_email_login_display', 'fbak_plugin_email_option', 'fbak_plugin_email_section', array( 'label_for' => 'fbak-enableemail' ));
        add_settings_field('fbak_email_new_register', __( 'New User Registration:', 'fb-account-kit-login' ), 'fbak_email_new_register_display', 'fbak_plugin_email_option', 'fbak_plugin_email_section', array( 'label_for' => 'fbak-email-reg' ));
        add_settings_field('fbak_email_new_register_user_type', __( 'New User Default Role:', 'fb-account-kit-login' ), 'fbak_email_new_register_user_type_display', 'fbak_plugin_email_option', 'fbak_plugin_email_section', array( 'label_for' => 'fbak-email-reg-user-type', 'class' => 'fbak-email-user-type' ));
        add_settings_field('fbak_email_label_text', __( 'Email Login Button Label:', 'fb-account-kit-login' ), 'fbak_email_label_text_display', 'fbak_plugin_email_option', 'fbak_plugin_email_section', array( 'label_for' => 'fbak-email-label-text' ));
        add_settings_field('fbak_email_btn_class', __( 'Email Login Button Class:', 'fb-account-kit-login' ), 'fbak_email_btn_class_display', 'fbak_plugin_email_option', 'fbak_plugin_email_section', array( 'label_for' => 'fbak-email-btn-class' ));
        add_settings_field('fbak_email_login_redirect', __( 'After Login Redirect to:', 'fb-account-kit-login' ), 'fbak_email_login_redirect_display', 'fbak_plugin_email_option', 'fbak_plugin_email_section', array( 'label_for' => 'fbak-email-redirect' ));
        add_settings_field('fbak_email_login_success_page', __( 'Authentication Success URL:', 'fb-account-kit-login' ), 'fbak_email_login_success_page_display', 'fbak_plugin_email_option', 'fbak_plugin_email_section', array( 'label_for' => 'fbak-email-success' ));
        
    add_settings_section('fbak_plugin_display_section', '', null, 'fbak_plugin_display_option');
        add_settings_field('fbak_login_form_type', __( 'Login Form Display Type:', 'fb-account-kit-login' ), 'fbak_login_form_type_display', 'fbak_plugin_display_option', 'fbak_plugin_display_section', array( 'label_for' => 'fbak-form-type' ));
        add_settings_field('fbak_enable_login_form', __( 'Enable on WP Login Form:', 'fb-account-kit-login' ), 'fbak_enable_login_form_display', 'fbak_plugin_display_option', 'fbak_plugin_display_section', array( 'label_for' => 'fbak-form' ));
        add_settings_field('fbak_hide_default_login_form', __( 'Default WP Login Form:', 'fb-account-kit-login' ), 'fbak_hide_default_login_form_display', 'fbak_plugin_display_option', 'fbak_plugin_display_section', array( 'label_for' => 'fbak-hide-form', 'class' => 'fbak-loginform' ));
        add_settings_field('fbak_login_description', __( 'Login Form Description:', 'fb-account-kit-login' ), 'fbak_login_description_display', 'fbak_plugin_display_option', 'fbak_plugin_display_section', array( 'label_for' => 'fbak-description' ));
        
    if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        add_settings_section('fbak_plugin_woo_section', '', null, 'fbak_plugin_woo_option');
            add_settings_field('fbak_enable_woo_login_form', __( 'Enable on Login Form:', 'fb-account-kit-login' ), 'fbak_enable_woo_login_form_display', 'fbak_plugin_woo_option', 'fbak_plugin_woo_section', array( 'label_for' => 'fbak-woo-login' ));
            add_settings_field('fbak_enable_woo_reg_form', __( 'Enable on Registration Form:', 'fb-account-kit-login' ), 'fbak_enable_woo_reg_form_display', 'fbak_plugin_woo_option', 'fbak_plugin_woo_section', array( 'label_for' => 'fbak-woo-reg' ));
            add_settings_field('fbak_redirect_to_checkout', __( 'Auto Redirect to Checkout:', 'fb-account-kit-login' ), 'fbak_redirect_to_checkout_display', 'fbak_plugin_woo_option', 'fbak_plugin_woo_section', array( 'label_for' => 'fbak-woo-redir' ));
            add_settings_field('fbak_allow_customer_auth', __( 'Allow User Authentication:', 'fb-account-kit-login' ), 'fbak_allow_customer_auth_display', 'fbak_plugin_woo_option', 'fbak_plugin_woo_section', array( 'label_for' => 'fbak-woo-cus' ));
            add_settings_field('fbak_account_kit_endpoint', __( 'Account Kit Auth Endpoint:', 'fb-account-kit-login' ), 'fbak_account_kit_endpoint_display', 'fbak_plugin_woo_option', 'fbak_plugin_woo_section', array( 'label_for' => 'fbak-woo-ep', 'class' => 'fbak-woo-ep' ));
            add_settings_field('fbak_account_kit_endpoint_label', __( 'WooCommerce Menu Label:', 'fb-account-kit-login' ), 'fbak_account_kit_endpoint_label_display', 'fbak_plugin_woo_option', 'fbak_plugin_woo_section', array( 'label_for' => 'fbak-woo-ep-label', 'class' => 'fbak-woo-ep-label' ));
            add_settings_field('fbak_woo_auth_description', __( 'Menu Item/Page Description:', 'fb-account-kit-login' ), 'fbak_woo_auth_description_display', 'fbak_plugin_woo_option', 'fbak_plugin_woo_section', array( 'label_for' => 'fbak-woo-des', 'class' => 'fbak-woo-des' ));
    }

    add_settings_section('fbak_plugin_misc_section', '', null, 'fbak_plugin_misc_option');
        add_settings_field('fbak_auth_waiting_message', __( 'Authentication Waiting Text:', 'fb-account-kit-login' ), 'fbak_auth_waiting_message_display', 'fbak_plugin_misc_option', 'fbak_plugin_misc_section', array( 'label_for' => 'fbak-waiting' ));
        add_settings_field('fbak_disable_user_reg_message', __( 'Registration Disable Message:', 'fb-account-kit-login' ), 'fbak_disable_user_reg_message_display', 'fbak_plugin_misc_option', 'fbak_plugin_misc_section', array( 'label_for' => 'fbak-disablereg' ));
        add_settings_field('fbak_custom_css', __( 'Custom CSS Code:', 'fb-account-kit-login' ), 'fbak_custom_css_display', 'fbak_plugin_misc_option', 'fbak_plugin_misc_section', array( 'label_for' => 'fbak-css' ));
        add_settings_field('fbak_delete_data', __( 'Delete Plugin Data?', 'fb-account-kit-login' ), 'fbak_delete_data_display', 'fbak_plugin_misc_option', 'fbak_plugin_misc_section', array( 'label_for' => 'fbak-delete-data' ));
        
    //register settings
    register_setting( 'fbak_plugin_settings_fields', 'fbak_plugin_settings' );
}