<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_action( 'personal_options', 'fbak_show_connect_button_on_profile', 5 );
add_filter( 'user_contactmethods', 'fbak_add_phone_contact_info' );

/**
 * Show the connect/disconnect button
 *
 * @param $user
 *
 * @return void
 */
function fbak_show_connect_button_on_profile( $user ) {
    $connected = get_user_meta( $user->ID, '_fb_accountkit_id', true );
    $mode = get_user_meta( $user->ID, '_fb_accountkit_auth_mode', true );

    $success = __( 'Connected', 'fb-account-kit-login' );
    if ( $mode === 'phone' ) {
        $success = __( 'Connected via Phone', 'fb-account-kit-login' );
    } elseif ( $mode === 'email' ) {
        $success = __( 'Connected via Email', 'fb-account-kit-login' );
    } ?>
    <?php if( fbak_enable_sms_login_method() || fbak_enable_email_login_method() ) { ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php _e( 'Facebook Account Kit', 'fb-account-kit-login' ); ?></th>
                    <td>
                        <?php if( ! $connected ) { ?>
                            <?php if( fbak_enable_sms_login_method() ) { ?>
                                <button class="button" onclick="smsLogin(); return false;"><span class="dashicons dashicons-testimonial" style="margin-top:3px;"></span> <?php _e( 'Connect with Phone', 'fb-account-kit-login' ); ?></button>
                            <?php } ?>
                            <?php if( fbak_enable_email_login_method() ) { ?>
                                <button class="button" onclick="emailLogin(); return false;"><span class="dashicons dashicons-email" style="margin-top:3px;"></span> <?php _e( 'Connect with Email', 'fb-account-kit-login' ); ?></button>
                            <?php } ?>
                        <?php } else { ?>
                            <button class="button" disabled><span class="dashicons dashicons-admin-links" style="margin-top:3px;"></span> <?php echo $success; ?></button>
                            <button class="button button-danger" onclick="fbAcDisconnect(); return false;"><span class="dashicons dashicons-trash" style="margin-top:3px;"></span> <?php _e( 'Disconnect', 'fb-account-kit-login' ); ?></button>
                        <?php } ?>
                        <span class="fb-ackit-wait spinner" style="float: none;"></span><span id="fbak-user-id" style="display: none;"><?php echo $user->ID; ?></span><span id="fbak-check-msg" style="display: none;"><?php echo fbak_get_disconnect_confirm_message(); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php }
}

function fbak_add_phone_contact_info( $fields ) {
     
    unset( $fields['phone_number'] );
    
    // Add Phone Number.
    $fields['phone_number'] = __( 'Phone Number', 'fb-account-kit-login' );
    
    // Return the amended contact fields.
    return $fields;
     
}