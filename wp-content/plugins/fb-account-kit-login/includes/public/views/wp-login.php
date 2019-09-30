<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

$fbak_settings = get_option( 'fbak_plugin_settings' );

$description = !empty($fbak_settings['fbak_login_description']) ? $fbak_settings['fbak_login_description'] : '';
$sms_label = !empty($fbak_settings['fbak_sms_label_text']) ? $fbak_settings['fbak_sms_label_text'] : 'Login with SMS';
$email_label = !empty($fbak_settings['fbak_email_label_text']) ? $fbak_settings['fbak_email_label_text'] : 'Login with Email';
$sms_btn_class = !empty($fbak_settings['fbak_sms_btn_class']) ? $fbak_settings['fbak_sms_btn_class'] : 'button button-primary';
$email_btn_class = !empty($fbak_settings['fbak_email_btn_class']) ? $fbak_settings['fbak_email_btn_class'] : 'button';

?>

<div class="fb-ackit-wrap">
    <div class="fb-ackit-login-forms">
        <p class="fb-ackit-desc" style="text-align: center;">
            <?php echo $description; ?>
        </p>
        <?php if ( fbak_enable_sms_login_method() ) : ?>
            <a href="#" onclick="smsLogin(); return false;" class="<?php echo $sms_btn_class; ?>" style="text-align: center;"><span class="fback-icon"><span class="fback-icon dashicons dashicons-testimonial"></span> </span><?php echo $sms_label; ?></a>
        <?php endif; ?>

        <?php if ( fbak_enable_email_login_method() ) : ?>
            <a href="#" onclick="emailLogin(); return false;" class="<?php echo $email_btn_class; ?>" style="text-align: center;"><span class="fback-icon"><span class="dashicons dashicons-email"></span> </span><?php echo $email_label; ?></a>
        <?php endif; ?>
    </div>

    <?php if ( isset($fbak_settings['fbak_hide_default_login_form']) && $fbak_settings['fbak_hide_default_login_form'] == 'no' ) : ?>
        <div class="fb-ackit-or">
            <span class="fb-ackit-or-sep"><?php _e( 'Or', 'fb-account-kit-login' ); ?></span>
        </div>
        <div class="fb-ackit-toggle">
            <a href="#" class="default"><?php _e( 'Login with Username and Password', 'fb-account-kit-login' ); ?></a>
            <a href="#" class="ackit"><?php _e( 'Login with SMS or Email', 'fb-account-kit-login' ); ?></a>
        </div>
    <?php endif; ?>
</div>
