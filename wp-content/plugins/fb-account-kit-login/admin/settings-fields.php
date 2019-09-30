<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

/* ============================================================================================== 
                                           general
============================================================================================== */

function fbak_app_id_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <input id="fbak-appid" name="fbak_plugin_settings[fbak_app_id]" type="number" size="60" style="width:60%;" placeholder="<?php _e( 'Enter facebook app id', 'fb-account-kit-login' ); ?>" required value="<?php if (isset($fbak_settings['fbak_app_id'])) { echo $fbak_settings['fbak_app_id']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enter the facebook app id you have created here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <p style="font-size: 12px; font-style:italic;">
            <a href="https://developers.facebook.com/apps/" target="_blank"><?php _e( 'Create a new Facebook App', 'fb-account-kit-login' ); ?></a> | 
            <a href="https://ps.w.org/fb-account-kit-login/assets/enable-account-kit-step-1.png" data-fancybox="enable-ak" data-caption="<?php _e( 'How to enable Account Kit? - Step 1', 'fb-account-kit-login' ); ?>"><?php _e( 'How to enable Account Kit?', 'fb-account-kit-login' ); ?></a>
            <a href="https://ps.w.org/fb-account-kit-login/assets/enable-account-kit-step-2.png" style="display: none;" data-fancybox="enable-ak" data-caption="<?php _e( 'How to enable Account Kit? - Step 2', 'fb-account-kit-login' ); ?>"><?php _e( 'How to enable Account Kit? - Step 2', 'fb-account-kit-login' ); ?></a>
            <a href="https://ps.w.org/fb-account-kit-login/assets/enable-account-kit-step-3.png" style="display: none;" data-fancybox="enable-ak" data-caption="<?php _e( 'How to enable Account Kit? - Step 3', 'fb-account-kit-login' ); ?>"><?php _e( 'How to enable Account Kit? - Step 3', 'fb-account-kit-login' ); ?></a>
            <a href="https://ps.w.org/fb-account-kit-login/assets/enable-account-kit-step-4.png" style="display: none;" data-fancybox="enable-ak" data-caption="<?php _e( 'How to enable Account Kit? - Step 4', 'fb-account-kit-login' ); ?>"><?php _e( 'How to enable Account Kit? - Step 3', 'fb-account-kit-login' ); ?></a>
        </p>
    <?php
}

function fbak_accountkit_secret_key_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <input id="fbak-seckey" name="fbak_plugin_settings[fbak_accountkit_secret_key]" type="password" size="60" style="width:60%;" placeholder="<?php _e( 'Enter account kit secret key', 'fb-account-kit-login' ); ?>" required value="<?php if (isset($fbak_settings['fbak_accountkit_secret_key'])) { echo $fbak_settings['fbak_accountkit_secret_key']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enter the Account Kit app secret. This is different than the Facebook app secret.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <p style="font-size: 12px; font-style:italic;"><a href="https://ps.w.org/fb-account-kit-login/assets/secret-key.png" data-fancybox="app-key" data-caption="<?php _e( 'How to get Account Kit Secret Key?', 'fb-account-kit-login' ); ?>"><?php _e( 'How to get Account Kit App Secret?', 'fb-account-kit-login' ); ?></a></p>
    <?php
}

function fbak_ac_locale_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_ac_locale']) ) {
        $fbak_settings['fbak_ac_locale'] = 'en_US';
    } ?>  <input id="fbak-locale" name="fbak_plugin_settings[fbak_ac_locale]" type="text" size="60" style="width:60%;" placeholder="en_US" required value="<?php if (isset($fbak_settings['fbak_ac_locale'])) { echo $fbak_settings['fbak_ac_locale']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set in which language login page will be shown.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <p style="font-size: 12px; font-style:italic;"><a href="https://developers.facebook.com/docs/accountkit/languages" target="_blank"><?php _e( 'View the list of available locale codes.', 'fb-account-kit-login' ); ?></a></p>
    <?php
}

function fbak_ac_res_url_display() { ?>
    <input id="fbak-resurl" type="text" size="60" style="width:60%;" readonly value="<?php echo esc_url( home_url( '/fbak-auth/?fbak_check_auth=true' ) ); ?>" />
    &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'It is the url where facebook app redirects after a successful email login.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <p style="font-size: 12px; font-style:italic;"><a href="https://ps.w.org/fb-account-kit-login/assets/enable-account-kit-step-5.png" data-fancybox="enable-ak" data-caption="<?php _e( 'Where to add this Redirect URL? - Step 5', 'fb-account-kit-login' ); ?>"><?php _e( 'Where to add this Redirect URL?', 'fb-account-kit-login' ); ?></a></p>
    <?php
}

/* ============================================================================================== 
                                           sms login
============================================================================================== */

function fbak_enable_sms_login_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-enablesms" name="fbak_plugin_settings[fbak_enable_sms_login]" value="1" <?php checked(isset($fbak_settings['fbak_enable_sms_login']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to use SMS Login.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_sms_new_register_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-sms-reg" name="fbak_plugin_settings[fbak_sms_new_register]" value="1" <?php checked(isset($fbak_settings['fbak_sms_new_register']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to register new users if user a not exists with provided phone number.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_sms_new_register_user_type_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    global $wp_roles;

    if( !isset($fbak_settings['fbak_sms_new_register_user_type']) ) {
        $fbak_settings['fbak_sms_new_register_user_type'] = get_option( 'default_role' );
    }
    
    $roles = $wp_roles->get_names();
    $roles = array_reverse( $roles );

    echo '<select id="fbak-sms-reg-user-type" name="fbak_plugin_settings[fbak_sms_new_register_user_type]" style="width:25%;">';
    foreach( $roles as $key => $value ) {
        $selected = ( $fbak_settings['fbak_sms_new_register_user_type'] == $key ) ? ' selected="selected"' : '';
        echo '<option value="' . $key . '"' . $selected . '>' . translate_user_role( $value ) . '</option>';
    }
    echo '</select>';
    ?>
    &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the role for the new users of SMS Login.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_sms_label_text_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_sms_label_text']) ) {
        $fbak_settings['fbak_sms_label_text'] = 'Login with SMS';
    } ?>  <input id="fbak-sms-label-text" name="fbak_plugin_settings[fbak_sms_label_text]" type="text" size="40" style="width:40%;" placeholder="<?php _e( 'Login with SMS', 'fb-account-kit-login' ); ?>" required value="<?php if (isset($fbak_settings['fbak_sms_label_text'])) { echo $fbak_settings['fbak_sms_label_text']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the sms login button\'s label from here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_sms_btn_class_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_sms_btn_class']) ) {
        $fbak_settings['fbak_sms_btn_class'] = 'button button-primary';
    } ?>  <input id="fbak-sms-btn-class" name="fbak_plugin_settings[fbak_sms_btn_class]" type="text" size="40" style="width:40%;" placeholder="button" required value="<?php if (isset($fbak_settings['fbak_sms_btn_class'])) { echo $fbak_settings['fbak_sms_btn_class']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the sms login button\'s css class from here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_sms_login_redirect_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    
    if( !isset($fbak_settings['fbak_sms_login_redirect']) ) {
        $fbak_settings['fbak_sms_login_redirect'] = 'admin_url';
    }
    $items = array(
        'admin_url'       => __( 'Dashboard Page', 'fb-account-kit-login' ),
        'home_page'       => __( 'Home Page', 'fb-account-kit-login' ),
        'profile_page'    => __( 'Profile Page', 'fb-account-kit-login' ),
        'custom_url'      => __( 'Custom URL', 'fb-account-kit-login' ),
    );
    echo '<select id="fbak-sms-redirect" name="fbak_plugin_settings[fbak_sms_login_redirect]" style="width:25%;">';
    foreach( $items as $item => $label ) {
        $selected = ( $fbak_settings['fbak_sms_login_redirect'] == $item ) ? ' selected="selected"' : '';
        echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
    ?>
    <span class="custom-sms-redir-url" style="display: none;">&nbsp;&nbsp;<label for="fbak-sms-redir-url" style="vertical-align: baseline;font-size: 14px;font-weight: 600;"><?php _e( 'URL:', 'fb-account-kit-login' ); ?></label>&nbsp;
    <input id="fbak-sms-redir-url" name="fbak_plugin_settings[fbak_sms_login_redirect_custom_url]" type="text" size="50" style="width:50%;" placeholder="<?php echo home_url( '/success/' ); ?>" value="<?php if (isset($fbak_settings['fbak_sms_login_redirect_custom_url'])) { echo $fbak_settings['fbak_sms_login_redirect_custom_url']; } ?>" />
    </span>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Choose the destination page where a user redirects after a successful SMS Login.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

/* ============================================================================================== 
                                           email login
============================================================================================== */

function fbak_enable_email_login_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-enableemail" name="fbak_plugin_settings[fbak_enable_email_login]" value="1" <?php checked(isset($fbak_settings['fbak_enable_email_login']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to use Email Login.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_email_new_register_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-email-reg" name="fbak_plugin_settings[fbak_email_new_register]" value="1" <?php checked(isset($fbak_settings['fbak_email_new_register']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to register new users if user a not exists with provided email address.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_email_new_register_user_type_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    global $wp_roles;

    if( !isset($fbak_settings['fbak_email_new_register_user_type']) ) {
        $fbak_settings['fbak_email_new_register_user_type'] = get_option( 'default_role' );
    }
    
    $roles = $wp_roles->get_names();
    $roles = array_reverse( $roles );

    echo '<select id="fbak-email-reg-user-type" name="fbak_plugin_settings[fbak_email_new_register_user_type]" style="width:25%;">';
    foreach( $roles as $key => $value ) {
        $selected = ( $fbak_settings['fbak_email_new_register_user_type'] == $key ) ? ' selected="selected"' : '';
        echo '<option value="' . $key . '"' . $selected . '>' . translate_user_role( $value ) . '</option>';
    }
    echo '</select>';
    ?>
    &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the role for the new users of Email Login.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_email_label_text_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_email_label_text']) ) {
        $fbak_settings['fbak_email_label_text'] = 'Login with Email';
    } ?>  <input id="fbak-email-label-text" name="fbak_plugin_settings[fbak_email_label_text]" type="text" size="40" style="width:40%;" placeholder="<?php _e( 'Login with SMS', 'fb-account-kit-login' ); ?>" required value="<?php if (isset($fbak_settings['fbak_email_label_text'])) { echo $fbak_settings['fbak_email_label_text']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the email login button\'s label from here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_email_btn_class_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_email_btn_class']) ) {
        $fbak_settings['fbak_email_btn_class'] = 'button';
    } ?>  <input id="fbak-email-btn-class" name="fbak_plugin_settings[fbak_email_btn_class]" type="text" size="40" style="width:40%;" placeholder="button" required value="<?php if (isset($fbak_settings['fbak_email_btn_class'])) { echo $fbak_settings['fbak_email_btn_class']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the email login button\'s css class from here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_email_login_redirect_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    
    if( !isset($fbak_settings['fbak_email_login_redirect']) ) {
        $fbak_settings['fbak_email_login_redirect'] = 'admin_url';
    }
    $items = array(
        'admin_url'       => __( 'Dashboard Page', 'fb-account-kit-login' ),
        'home_page'       => __( 'Home Page', 'fb-account-kit-login' ),
        'profile_page'    => __( 'Profile Page', 'fb-account-kit-login' ),
        'custom_url'      => __( 'Custom URL', 'fb-account-kit-login' ),
    );
    echo '<select id="fbak-email-redirect" name="fbak_plugin_settings[fbak_email_login_redirect]" style="width:25%;">';
    foreach( $items as $item => $label ) {
        $selected = ( $fbak_settings['fbak_email_login_redirect'] == $item ) ? ' selected="selected"' : '';
        echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
    ?>
    <span class="custom-email-redir-url" style="display: none;">&nbsp;&nbsp;<label for="fbak-email-redir-url" style="vertical-align: baseline;font-size: 14px;font-weight: 600;"><?php _e( 'URL:', 'fb-account-kit-login' ); ?></label>&nbsp;
    <input id="fbak-email-redir-url" name="fbak_plugin_settings[fbak_email_login_redirect_custom_url]" type="text" size="50" style="width:50%;" placeholder="<?php echo home_url( '/success/' ); ?>" value="<?php if (isset($fbak_settings['fbak_email_login_redirect_custom_url'])) { echo $fbak_settings['fbak_email_login_redirect_custom_url']; } ?>" />
    </span>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Choose the destination page where a user redirects after a successful Email Login.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_email_login_success_page_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    
    if( !isset($fbak_settings['fbak_email_login_success_page']) ) {
        $fbak_settings['fbak_email_login_success_page'] = 'default';
    }
    $items = array(
        'default'      => __( 'Plugin Default', 'fb-account-kit-login' ),
        'fb_default'   => __( 'Facebook Default', 'fb-account-kit-login' ),
        'custom'       => __( 'Custom URL', 'fb-account-kit-login' ),
    );
    echo '<select id="fbak-email-success" name="fbak_plugin_settings[fbak_email_login_success_page]" style="width:25%; margin-top: -3px;">';
    foreach( $items as $item => $label ) {
        $selected = ( $fbak_settings['fbak_email_login_success_page'] == $item ) ? ' selected="selected"' : '';
        echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
    ?>  
    <span class="custom-email-url" style="display: none;">&nbsp;&nbsp;<label for="fbak-email-success-url" style="vertical-align: baseline;font-size: 14px;font-weight: 600;"><?php _e( 'URL:', 'fb-account-kit-login' ); ?></label>&nbsp;
    <input id="fbak-email-success-url" name="fbak_plugin_settings[fbak_email_login_success_page_url]" type="text" size="50" style="width:50%;" placeholder="<?php echo home_url( '/auth-success/' ); ?>" value="<?php if (isset($fbak_settings['fbak_email_login_success_page_url'])) { echo $fbak_settings['fbak_email_login_success_page_url']; } ?>" />
    </span>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the email authentication success URL freom here. Plugin Default uses this URL '.esc_url( home_url( '/fbak-auth/?fbak_check_auth=true' ) ), 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <p class="custom-email-url" style="font-size: 12px; font-style: italic;display: none;"><a href="https://ps.w.org/fb-account-kit-login/assets/email-success.png" data-fancybox="email-success" data-caption="<?php _e( 'Where to add this Redirect URL?', 'fb-account-kit-login' ); ?>"><?php _e( 'Where to add this Redirect URL?', 'fb-account-kit-login' ); ?></a></p>
    <?php
}

/* ============================================================================================== 
                                           display options
============================================================================================== */

function fbak_hide_default_login_form_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    
    if( !isset($fbak_settings['fbak_hide_default_login_form']) ) {
        $fbak_settings['fbak_hide_default_login_form'] = 'no';
    }
    $items = array(
        'yes'   => __( 'Hide Login Form', 'fb-account-kit-login' ),
        'no'    => __( 'Show Login Form', 'fb-account-kit-login' ),
    );
    echo '<select id="fbak-hide-form" name="fbak_plugin_settings[fbak_hide_default_login_form]" style="width:30%;">';
    foreach( $items as $item => $label ) {
        $selected = ( $fbak_settings['fbak_hide_default_login_form'] == $item ) ? ' selected="selected"' : '';
        echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
    ?>
    &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'If you want to hide the default native login from, you can set it from here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_login_form_type_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    
    if( !isset($fbak_settings['fbak_login_form_type']) ) {
        $fbak_settings['fbak_login_form_type'] = 'popup';
    }
    $items = array(
        'popup'    => __( 'Popup Form', 'fb-account-kit-login' ),
        'modal'    => __( 'Modal Form', 'fb-account-kit-login' ),
    );
    echo '<select id="fbak-form-type" name="fbak_plugin_settings[fbak_login_form_type]" style="width:30%;">';
    foreach( $items as $item => $label ) {
        $selected = ( $fbak_settings['fbak_login_form_type'] == $item ) ? ' selected="selected"' : '';
        echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
    ?>
    &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Choose the login box type from here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_enable_login_form_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    
    if( !isset($fbak_settings['fbak_enable_login_form']) ) {
        $fbak_settings['fbak_enable_login_form'] = 'enable';
    }
    $items = array(
        'enable'    => __( 'Enable', 'fb-account-kit-login' ),
        'disable'   => __( 'Disable', 'fb-account-kit-login' ),
    );
    echo '<select id="fbak-form" name="fbak_plugin_settings[fbak_enable_login_form]" style="width:30%;">';
    foreach( $items as $item => $label ) {
        $selected = ( $fbak_settings['fbak_enable_login_form'] == $item ) ? ' selected="selected"' : '';
        echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
    ?>
    &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Choose the Enable option if you want to enable account kit login on WordPress Login Page.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_login_description_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?> <textarea id="fbak-description" placeholder="<?php _e( 'Enter the text to show on login page.', 'fb-account-kit-login' ); ?>" name="fbak_plugin_settings[fbak_login_description]" rows="3" cols="90" style="width:90%;"><?php if (isset($fbak_settings['fbak_login_description'])) { echo $fbak_settings['fbak_login_description']; } ?></textarea>
    <?php
}

/* ============================================================================================== 
                                           woocommerce options
============================================================================================== */

function fbak_enable_woo_login_form_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-woo-login" name="fbak_plugin_settings[fbak_enable_woo_login_form]" value="1" <?php checked(isset($fbak_settings['fbak_enable_woo_login_form']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to add passwordless login on WooCommerce Login Form.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_enable_woo_reg_form_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-woo-reg" name="fbak_plugin_settings[fbak_enable_woo_reg_form]" value="1" <?php checked(isset($fbak_settings['fbak_enable_woo_reg_form']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to add passwordless login on WooCommerce Registration Form.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_redirect_to_checkout_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-woo-redir" name="fbak_plugin_settings[fbak_redirect_to_checkout]" value="1" <?php checked(isset($fbak_settings['fbak_redirect_to_checkout']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to redirect to Checkout Page when a customer logs in from Checkout Page directly.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_allow_customer_auth_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <label class="switch"><input type="checkbox" id="fbak-woo-cus" name="fbak_plugin_settings[fbak_allow_customer_auth]" value="1" <?php checked(isset($fbak_settings['fbak_allow_customer_auth']), 1); ?> /> 
        <span class="cb-slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to allow customers to link their email or mobile number to login to their account without password.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_account_kit_endpoint_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_account_kit_endpoint']) ) {
        $fbak_settings['fbak_account_kit_endpoint'] = 'account-kit';
    } ?>  <input id="fbak-woo-ep" name="fbak_plugin_settings[fbak_account_kit_endpoint]" type="text" size="40" style="width:40%;" placeholder="account-kit" required value="<?php if (isset($fbak_settings['fbak_account_kit_endpoint'])) { echo $fbak_settings['fbak_account_kit_endpoint']; } ?>" />
    <input type="hidden" id="changetrigger" value="no">&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the authentication url endpoint here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_account_kit_endpoint_label_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_account_kit_endpoint_label']) ) {
        $fbak_settings['fbak_account_kit_endpoint_label'] = __( 'Authentication', 'fb-account-kit-login' );
    } ?>  <input id="fbak-woo-ep-label" name="fbak_plugin_settings[fbak_account_kit_endpoint_label]" type="text" size="40" style="width:40%;" placeholder="Authentication" required value="<?php if (isset($fbak_settings['fbak_account_kit_endpoint_label'])) { echo $fbak_settings['fbak_account_kit_endpoint_label']; } ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the authentication url endpoint label here.', 'fb-account-kit-login' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
    <?php
}

function fbak_woo_auth_description_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    if( empty($fbak_settings['fbak_woo_auth_description']) ) {
        $fbak_settings['fbak_woo_auth_description'] = __( 'If your Account is not linked with Facebook Account Kit, please connect your account with Facebook Account Kit for a secure and passwordless login.', 'fb-account-kit-login' );
    } ?> <textarea id="fbak-woo-des" placeholder="<?php _e( 'Enter the text to show before authentication buttons.', 'fb-account-kit-login' ); ?>" name="fbak_plugin_settings[fbak_woo_auth_description]" rows="3" cols="90" style="width:90%;"><?php if (isset($fbak_settings['fbak_woo_auth_description'])) { echo $fbak_settings['fbak_woo_auth_description']; } ?></textarea>
    <?php
}

/* ============================================================================================== 
                                           misc options
============================================================================================== */

function fbak_auth_waiting_message_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <input id="fbak-waiting" name="fbak_plugin_settings[fbak_auth_waiting_message]" type="text" size="90" style="width:90%;" placeholder="<?php _e( 'Please wait until we authenticate you.', 'fb-account-kit-login' ); ?>" value="<?php if (isset($fbak_settings['fbak_auth_waiting_message'])) { echo $fbak_settings['fbak_auth_waiting_message']; } ?>" />
    <?php
}

function fbak_disable_user_reg_message_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <input id="fbak-disablereg" name="fbak_plugin_settings[fbak_disable_user_reg_message]" type="text" size="90" style="width:90%;" placeholder="<?php _e( 'You are not a registered user of this website.', 'fb-account-kit-login' ); ?>" value="<?php if (isset($fbak_settings['fbak_disable_user_reg_message'])) { echo $fbak_settings['fbak_disable_user_reg_message']; } ?>" />
    <?php
}

function fbak_custom_css_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?> <textarea id="fbak-css" placeholder=".button { color: #000 !important; font-weight: bold; }" name="fbak_plugin_settings[fbak_custom_css]" rows="5" cols="90" style="width:90%;"><?php if (isset($fbak_settings['fbak_custom_css'])) { echo $fbak_settings['fbak_custom_css']; } ?></textarea>
    <br><small><?php printf(__( 'Do not add %s tag. This tag is not required, as it is already added.', 'fb-account-kit-login' ), '<code>&lt;style&gt;&lt;/style&gt;</code>'); ?></small>
    <?php
}

function fbak_delete_data_display() {
    $fbak_settings = get_option('fbak_plugin_settings');
    ?>  <input type="checkbox" id="fbak-delete-data" name="fbak_plugin_settings[fbak_delete_data]" value="1" <?php checked(isset($fbak_settings['fbak_delete_data']), 1); ?> /> 
        <label for="fbak-delete-data" style="font-size: 12px;"><?php _e( 'Yes, I want to delete all plugin data at the time of uninstallation.', 'fb-account-kit-login' ); ?></label>
    <?php
}

?>