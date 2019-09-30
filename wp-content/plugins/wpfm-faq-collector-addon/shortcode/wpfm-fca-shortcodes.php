<?php 

add_shortcode( 'bwl_fca_form', 'bwl_fca_form' );

function bwl_fca_form($atts) {
    
    $atts = shortcode_atts(array(
                        'product_id' => 0,
                        'status' => 1,
                        'fca_container_extra_class' => '',
                        'fca_form_heading' => __('Write Your Question !', 'bwl-wpfmfc'),
                        'title_min_length' => '3',
                        'title_max_length' => '100'
                    ), $atts);
    
    extract($atts);
    
    $bwpfm_data = get_option('bwpfm_options');
    
    //@Load Custom Scripts.
    wp_enqueue_script( 'bkb-wpfm-fac-custom-scripts' );
    
    // Form Validation Message.
    
     $err_bwl_fca_captcha = __(" Incorrect Captcha Value!", "bwl-wpfmfc");
     $err_bwl_fca_question = sprintf( __( " Write your question. Min length %d characters & Max length %d characters !", "bwl-wpfmfc"), $title_min_length, $title_max_length );
     $err_bwl_fca_email = __( " Valid Email address required!", "bwl-wpfmfc");
     $err_bwl_fca_success_msg = __( " FAQ successfully added for review!", "bwl-wpfmfc");
     $err_bwl_fca_error_msg = __( " Unable to add faq. Please try again!", "bwl-wpfmfc");
    
    
    $bwpfm_captcha_status = 1;
        
    if ( isset( $bwpfm_data['bwpfm_captcha_status'] ) && $bwpfm_data['bwpfm_captcha_status'] == 1 ) { 
            
            // Disable captcha.
        
           $bwpfm_captcha_status =  0; 

    }
 
    $login_required = TRUE; // Default we required logged in to post a new KB.
    
    if( is_user_logged_in() ) {
                
        $login_required = FALSE;

    }
    
    if ( isset( $bwpfm_data['bwpfm_login_status'] ) ) {
         
        if ( $bwpfm_data['bwpfm_login_status'] == 1 ) {
            
            if( is_user_logged_in() ) {
                
                $login_required = FALSE;
                
            }            
            
        } else  {
            
            $login_required = FALSE;
            
        }            
        
    }
    
    
   if ( $login_required == FALSE ) :
       
    $bwl_fca_form_id = wp_rand();
    
    if ( $bwpfm_captcha_status == 1 ) :
        
        $bwl_fca_captcha_generator = '<p>
                                                            <label for="captcha">' . __('Captcha:', 'bwl-wpfmfc') . '</label><input id="num1" class="sum" type="text" name="num1" value="' . rand(1,4) . '" readonly="readonly" />  + <input id="num2" class="sum" type="text" name="num2" value="' . rand(5,9) . '" readonly="readonly" /> = <input id="captcha" class="captcha" type="text" name="captcha" maxlength="2" data-error_msg="' . $err_bwl_fca_captcha . '"/> <span id="spambot"> '. __('Verify Human or Spambot ?', 'bwl-wpfmfc') .'</span>
                                                            <input id="captcha_status" type="hidden" name="captcha_status" value="' . $bwpfm_captcha_status . '" />
                                                        </p>';    
        
    else:        
        
        $bwl_fca_captcha_generator = '<input id="captcha_status" type="hidden" name="captcha_status" value="' . $bwpfm_captcha_status . '" />';    
        
    endif;
    
    
    $bwl_fca_ques_form_container = ( isset( $fca_container_extra_class ) && $fca_container_extra_class !="" ) ? $fca_container_extra_class .' bwl-fca-ques-form-container': 'bwl-fca-ques-form-container';
    
    
    $bwl_fca_ques_form_body = '<section class="'.$bwl_fca_ques_form_container.'" id="' . $bwl_fca_form_id . '">
                                        <h2>' . $fca_form_heading . ' </h2>
                                        <form id="bwl_fca_ques_form" class="bwl_fca_ques_form" name="bwl_fca_ques_form" method="post" action="#"> 
                                                
                                                <div class="bwl-fca-message-box"></div>
                                                <p>        
                                                    <label for="title">' . __('Question: ', 'bwl-wpfmfc') . '</label><input type="text" id="title" value="" name="title" data-error_msg="' . $err_bwl_fca_question . '" data-min_length="' . $title_min_length . '" data-max_length="' . $title_max_length . '"/> 
                                                </p>
                                                <p>        
                                                    <label for="sender_name">' . __('Your Name: ', 'bwl-wpfmfc') . '</label><input type="text" id="sender_name" value="" name="sender_name"/> 
                                                </p>
                                                <p>        
                                                    <label for="email">' . __('Your Email: ', 'bwl-wpfmfc') . '</label><input type="text" id="email" value="" name="email" data-error_msg="' . $err_bwl_fca_email . '"/>  <small>' . __('You will get a notification email when FAQ answerd/updated!', 'bwl-wpfmfc') . '</small>
                                                </p>
                                                
                                                ' . $bwl_fca_captcha_generator . '

                                                <p class="bwl_fca_question_submit_container">
                                                    <input type="submit" value="' . __('Submit Question', 'bwl-wpfmfc') . '" tabindex="6" id="submit" name="submit" bwl_fca_ques_form_id= "' . $bwl_fca_form_id . '" data-success_msg="'.$err_bwl_fca_success_msg.'" data-error_msg="'.$err_bwl_fca_error_msg.'" />
                                                </p>

                                                <input type="hidden" name="post_type" id="post_type" value="bwl-woo-faq-manager" />
                                                <input type="hidden" name="product_id" id="product_id" value="'.$product_id.'" />

                                                <input type="hidden" name="action" value="bwl_fca_ques" />'

                                                . wp_nonce_field( 'wpfm_fca_nonce_action','wpfm_fca_nonce_field' ) .
            
                                           '</form>

                                        </section>';
    
    else:
        
        $bwl_fca_ques_form_body = '<h4 class="wpfm_login_required_msg"><i class="fa fa-info-circle"></i> '.__("Login required to submit question !", 'bwl-wpfmfc' ).'</h4>';
        $bwl_fca_ques_form_body .= do_shortcode('[wpfm_login_form]');
        
    endif;
    
    if ( isset($bwpfm_data['bwl_fca_display_question_submission_form']) && $bwpfm_data['bwl_fca_display_question_submission_form'] == 1 ) {
                
        $bwl_fca_ques_form_body = "";
            
    }
    
    
    return $bwl_fca_ques_form_body;

}


/**
 * @Description: Shortcode For Create Login Form
 * @Since: Version 1.0.0
 * @Author: Mahbub
 * @Last Update:11-04-2016
 */

add_shortcode('wpfm_login_form', 'wpfm_login_form');

if (!function_exists('wpfm_login_form')) {

    function wpfm_login_form($atts, $content = null) {

        extract(shortcode_atts(array(
            'redirect' => ''
                        ), $atts));

        $form = "";

        if (!is_user_logged_in()) {

            if ($redirect) {
                $redirect_url = $redirect;
            } else {
                $redirect_url = get_permalink();
            }

            $args = array(
                'echo' => false,
                'redirect' => $redirect_url,
                'form_id' => 'wpfm_login_form',
                'label_username' => __('Username', 'bwl-wpfmfc'),
                'label_password' => __('Password', 'bwl-wpfmfc'),
                'label_remember' => __('Remember Me', 'bwl-wpfmfc'),
                'label_log_in' => __('Log In', 'bwl-wpfmfc'),
                'id_username' => 'user_login',
                'id_password' => 'user_pass',
                'id_remember' => 'rememberme',
                'id_submit' => 'wp-submit',
                'remember' => true,
                'value_username' => NULL,
                'value_remember' => false
            );

            $form = wp_login_form($args);
        }

        return $form;
    }

}