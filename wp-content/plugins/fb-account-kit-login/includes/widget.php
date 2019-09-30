<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

// Register and load the widget
add_action( 'widgets_init', 'fbak_load_accountkit_login_widget' );

function fbak_load_accountkit_login_widget() {
    register_widget( 'FBAK_Login_Widget' );
}

class FBAK_Login_Widget extends WP_Widget {

    /**
     * Sets up the widgets name
     */
    public function __construct() {
        $widget_ops = array( 
            'classname'   => 'account_kit_login_widget',
            'description' => esc_html__( 'Facebook Account Kit Login Widget.', 'fb-account-kit-login' ),
        );
        parent::__construct( 'fb_account_kit_login', esc_html__( 'Facebook Account Kit Login', 'fb-account-kit-login' ), $widget_ops );
    }
    
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        // don't display if already logged in
        if ( is_user_logged_in() ) {
            return;
        }
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        ?>
        <div class="fb-ackit-wrap">
            <div class="fb-ackit-desc"><?php echo $instance['description']; ?></div>
            <div class="fb-ackit-buttons">
                <?php if ( $instance['sms_on'] == 'on' ) : ?>
                    <button href="#" onclick="smsLogin(); return false;" class="<?php echo $instance['sms_class']; ?>"><?php echo $instance['sms_label']; ?></button>
                <?php endif; ?>
                <?php if ( $instance['email_on'] == 'on' ) : ?>
                    <button href="#" onclick="emailLogin(); return false;" class="<?php echo $instance['email_class']; ?>"><?php echo $instance['email_label']; ?></button>
                <?php endif; ?>
            </div>
        </div>
        <div class="fb-ackit-wait" style="display: none;"><?php echo $instance['wait_msg']; ?></div>
        <?php
        echo $args['after_widget'];
    }
    
    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __( 'Account Login', 'fb-account-kit-login' ),
            'description' => __( 'Account Kit Login description', 'fb-account-kit-login' ),
            'sms_on' => 'on',
            'sms_label' => __( 'Login with SMS', 'fb-account-kit-login' ),
            'sms_class' => 'button btn',
            'email_on' => 'on',
            'email_label' => __( 'Login with Email', 'fb-account-kit-login' ),
            'email_class' => 'button btn',
            'wait_msg' => __( 'Please wait until we authenticate you.', 'fb-account-kit-login' )
        ) );

        $title = sanitize_text_field( $instance['title'] );
        $description = sanitize_text_field( $instance['description'] );
        $sms_on = $instance['sms_on'];
        $sms_label = sanitize_text_field( $instance['sms_label'] );
        $sms_class = sanitize_text_field( $instance['sms_class'] );
        $email_on = $instance['email_on'];
        $email_label = sanitize_text_field( $instance['email_label'] );
        $email_class = sanitize_text_field( $instance['email_class'] );
        $wait_msg = sanitize_text_field( $instance['wait_msg'] );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'fb-account-kit-login' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_attr_e( 'Description:', 'fb-account-kit-login' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" type="text" value="<?php echo esc_attr( $description ); ?>">
        </p>
        <p>
            <input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sms_on' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sms_on' ) ); ?>" type="checkbox" <?php checked( $sms_on, 'on' ); ?> /> 
            <label for="<?php echo esc_attr( $this->get_field_id( 'sms_on' ) ); ?>"><?php esc_attr_e( 'Enable SMS Login?', 'fb-account-kit-login' ); ?></label>
        </p>
        <p class="sms-on-main">
            <label for="<?php echo esc_attr( $this->get_field_id( 'sms_label' ) ); ?>"><?php esc_attr_e( 'SMS Button Label:', 'fb-account-kit-login' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sms_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sms_label' ) ); ?>" type="text" value="<?php echo esc_attr( $sms_label ); ?>">
        </p>
        <p class="sms-on-main">
            <label for="<?php echo esc_attr( $this->get_field_id( 'sms_class' ) ); ?>"><?php esc_attr_e( 'SMS Button CSS Class:', 'fb-account-kit-login' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sms_class' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sms_class' ) ); ?>" type="text" value="<?php echo esc_attr( $sms_class ); ?>">
        </p>
        <p>
            <input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'email_on' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email_on' ) ); ?>" type="checkbox" <?php checked( $email_on, 'on' ); ?> /> 
            <label for="<?php echo esc_attr( $this->get_field_id( 'email_on' ) ); ?>"><?php esc_attr_e( 'Enable Email Login?', 'fb-account-kit-login' ); ?></label>
        </p>
        <p class="email-on-main">
            <label for="<?php echo esc_attr( $this->get_field_id( 'email_label' ) ); ?>"><?php esc_attr_e( 'Email Button Label:', 'fb-account-kit-login' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email_label' ) ); ?>" type="text" value="<?php echo esc_attr( $email_label ); ?>">
        </p>
        <p class="email-on-main">
            <label for="<?php echo esc_attr( $this->get_field_id( 'email_class' ) ); ?>"><?php esc_attr_e( 'Email Button CSS Class:', 'fb-account-kit-login' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email_class' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email_class' ) ); ?>" type="text" value="<?php echo esc_attr( $email_class ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'wait_msg' ) ); ?>"><?php esc_attr_e( 'Waiting Message:', 'fb-account-kit-login' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wait_msg' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wait_msg' ) ); ?>" type="text" value="<?php echo esc_attr( $wait_msg ); ?>">
        </p>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $("#<?php echo esc_attr( $this->get_field_id( 'sms_on' ) ); ?>").change(function () {
                    if ($('#<?php echo esc_attr( $this->get_field_id( 'sms_on' ) ); ?>').is(':checked')) {
                        $('.sms-on-main').show();
                    }
                    if (!$('#<?php echo esc_attr( $this->get_field_id( 'sms_on' ) ); ?>').is(':checked')) {
                        $('.sms-on-main').hide();
                    }
                });
                $("#<?php echo esc_attr( $this->get_field_id( 'sms_on' ) ); ?>").trigger('change');
                $("#<?php echo esc_attr( $this->get_field_id( 'email_on' ) ); ?>").change(function () {
                    if ($('#<?php echo esc_attr( $this->get_field_id( 'email_on' ) ); ?>').is(':checked')) {
                        $('.email-on-main').show();
                    }
                    if (!$('#<?php echo esc_attr( $this->get_field_id( 'email_on' ) ); ?>').is(':checked')) {
                        $('.email-on-main').hide();
                    }
                });
                $("#<?php echo esc_attr( $this->get_field_id( 'email_on' ) ); ?>").trigger('change');
            });
        </script>
        <?php
    }
    
    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['description'] = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';
        $instance['sms_on'] = ( isset( $new_instance['sms_on'] ) ) ? $new_instance['sms_on'] : '';
        $instance['sms_label'] = ( ! empty( $new_instance['sms_label'] ) ) ? strip_tags( $new_instance['sms_label'] ) : __( 'Login with SMS', 'fb-account-kit-login' );
        $instance['sms_class'] = ( ! empty( $new_instance['sms_class'] ) ) ? strip_tags( $new_instance['sms_class'] ) : '';
        $instance['email_on'] = ( isset( $new_instance['email_on'] ) ) ? $new_instance['email_on'] : '';
        $instance['email_label'] = ( ! empty( $new_instance['email_label'] ) ) ? strip_tags( $new_instance['email_label'] ) : __( 'Login with Email', 'fb-account-kit-login' );
        $instance['email_class'] = ( ! empty( $new_instance['email_class'] ) ) ? strip_tags( $new_instance['email_class'] ) : '';
        $instance['wait_msg'] = ( ! empty( $new_instance['wait_msg'] ) ) ? strip_tags( $new_instance['wait_msg'] ) : '';
    
        return $instance;
    }
}
    