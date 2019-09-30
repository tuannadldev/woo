<?php

function bwpfm_css_compressor($stylesheet) {
    
    /* remove comments */
    $stylesheet = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $stylesheet);
    
    /* remove tabs, spaces, newlines, etc. */
    $stylesheet = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $stylesheet);
    
    return $stylesheet;
    
}

function bwpfm_is_default_theme( $current_theme ) {
    
   $wpfm_default_themes = array('red_theme','orange_theme','blue_theme','green_theme','yellow_theme');
    
    if ( in_array( $current_theme, $wpfm_default_themes )) {
        return TRUE;
    } else {
        return FALSE;
    }
    
}




function get_wpfm_theme_class( $theme ){
    
     if( $theme == "red") {
        
        return 'red_theme';
        
    } else if( $theme == "blue") {
        
        return 'blue_theme';
        
    } else if( $theme == "green") {
        
        return 'green_theme';
        
    } else if( $theme == "yellow") {
        
        return 'yellow_theme';
        
    } else if( $theme == "orange") {
        
        return 'orange_theme';
        
    } else if( $theme != "" ) {
        
        return $theme;
        
    } else {
        
        return "";
        
    }
    
}



function bwpfm_default_themes() {
    
     /*------------------------------ Default Theme Info  ---------------------------------*/

    $bwpfm_default_themes= array(
        'red_theme' => array(
            'bwpfm_acc_heading_bg' => '#FF3E3E',
            'bwpfm_acc_heading_color' => '#FFFFFF',
            'bwpfm_acc_heading_active_bg' => '#FF1A1A',
            'bwpfm_acc_heading_active_color' =>  '#FFFFFF',
            'bwpfm_acc_content_bg' => '#F8F8F8',
            'bwpfm_acc_content_color' => '#2c2c2c',
            'bwpfm_highlight_bg' => '#FEF5CB',
            'bwpfm_highlight_color' => '#2C2C2C'
            
        ),
        'blue_theme' => array(
            'bwpfm_acc_heading_bg' => '#116597',
            'bwpfm_acc_heading_color' => '#FFFFFF',
            'bwpfm_acc_heading_active_bg' => '#157ebc',
            'bwpfm_acc_heading_active_color' =>  '#FFFFFF',
            'bwpfm_acc_content_bg' => '#F8F8F8',
            'bwpfm_acc_content_color' => '#2c2c2c',
            'bwpfm_highlight_bg' => '#FEF5CB',
            'bwpfm_highlight_color' => '#2C2C2C'
        ),
        'green_theme' => array(
            'bwpfm_acc_heading_bg' => '#3F9C3D',
            'bwpfm_acc_heading_color' => '#FFFFFF',
            'bwpfm_acc_heading_active_bg' => '#4bb847',
            'bwpfm_acc_heading_active_color' =>  '#FFFFFF',
            'bwpfm_acc_content_bg' => '#F8F8F8',
            'bwpfm_acc_content_color' => '#2c2c2c',
            'bwpfm_highlight_bg' => '#FEF5CB',
            'bwpfm_highlight_color' => '#2C2C2C'
        ),
        'orange_theme' => array(
            'bwpfm_acc_heading_bg' => '#f45000',
            'bwpfm_acc_heading_color' => '#FFFFFF',
            'bwpfm_acc_heading_active_bg' => '#ff732f',
            'bwpfm_acc_heading_active_color' =>  '#FFFFFF',
            'bwpfm_acc_content_bg' => '#F8F8F8',
            'bwpfm_acc_content_color' => '#2c2c2c',
            'bwpfm_highlight_bg' => '#2C2C2C',
            'bwpfm_highlight_color' => '#FEF5CB'
        ),
        'yellow_theme' => array(
            'bwpfm_acc_heading_bg' => '#FFE033',
            'bwpfm_acc_heading_color' => '#2C2C2C',
            'bwpfm_acc_heading_active_bg' => '#FFEB79',
            'bwpfm_acc_heading_active_color' => '#2C2C2C',
            'bwpfm_acc_content_bg' => '#F8F8F8',
            'bwpfm_acc_content_color' => '#2C2C2C',
            'bwpfm_highlight_bg' => '#2C2C2C',
            'bwpfm_highlight_color' => '#FEF5CB'
        )
    );
    
    return $bwpfm_default_themes;
    
}

function bwpfm_custom_theme_generator() {

    $bwpfm_data = get_option('bwpfm_options');
    
    if( isset($bwpfm_data['bwpfm_custom_themes']) ) {
        
        $bwpfm_custom_themes = $bwpfm_data['bwpfm_custom_themes'];
        
    } else {
        
        $bwpfm_custom_themes = array(); // initialize.
        
    }

    $bwpfm_custom_theme = '';

    $bwpfm_custom_theme .= '<style type="text/css">';

    if (sizeof($bwpfm_custom_themes) > 0) {

        foreach ($bwpfm_custom_themes as $themes_info) {

            $bwpfm_theme_unique_title = trim($themes_info['bwpfm_theme_unique_title']);
            $bwpfm_acc_heading_bg = ( trim($themes_info['bwpfm_acc_heading_bg']) == "" ) ? "#2C2C2C" : trim($themes_info['bwpfm_acc_heading_bg']);
            $bwpfm_acc_heading_color = ( trim($themes_info['bwpfm_acc_heading_color']) == "" ) ? "#FFFFFF" : trim($themes_info['bwpfm_acc_heading_color']);
            $bwpfm_acc_heading_active_bg = ( trim($themes_info['bwpfm_acc_heading_active_bg']) == "" ) ? "#006666" : trim($themes_info['bwpfm_acc_heading_active_bg']);
            $bwpfm_acc_heading_active_color = ( trim($themes_info['bwpfm_acc_heading_active_color']) == "" ) ? "#F0F0F0" : trim($themes_info['bwpfm_acc_heading_active_color']);
            $bwpfm_acc_content_bg = ( trim($themes_info['bwpfm_acc_content_bg']) == "" ) ? "#F1F1F1" : trim($themes_info['bwpfm_acc_content_bg']);
            $bwpfm_acc_content_color = ( trim($themes_info['bwpfm_acc_content_color']) == "" ) ? "#F1F1F1" : trim($themes_info['bwpfm_acc_content_color']);

            $bwpfm_custom_theme .='div.bwl_acc_container h2.acc_trigger_' . $bwpfm_theme_unique_title . '{
                                                            background: ' . $bwpfm_acc_heading_bg . ';   
                                                            color: ' . $bwpfm_acc_heading_color . ';   
                                                          }
                                                          
                                                         div.bwl_acc_container div.acc_container_' . $bwpfm_theme_unique_title . ' {
                                                            background: ' . $bwpfm_acc_content_bg . ';
                                                            color: ' . $bwpfm_acc_content_color . ';
                                                          } 
                                                          
                                                          div.bwl_acc_container h2.acc_trigger_' . $bwpfm_theme_unique_title . ' a,
                                                            div.bwl_acc_container h2.acc_trigger_' . $bwpfm_theme_unique_title . ' a:hover{
                                                              background: ' . $bwpfm_acc_heading_bg . ';   
                                                              color: ' . $bwpfm_acc_heading_color . ';   
                                                          }
                                                          
                                                          div.bwl_acc_container h2.acc_trigger_' . $bwpfm_theme_unique_title . ' a:before {
                                                            content: "\f067";
                                                          }

                                                          div.bwl_acc_container h2.acc_trigger_' . $bwpfm_theme_unique_title . ' a:hover:before {
                                                            content: "\f067";
                                                          }

                                                          div.bwl_acc_container h2.active_' . $bwpfm_theme_unique_title . ' a:before {
                                                              content: "\f068";
                                                          }

                                                           div.bwl_acc_container h2.active_' . $bwpfm_theme_unique_title . ' a:hover:before {
                                                              content: "\f068";
                                                          }
                                                          
                                                          div.bwl_acc_container h2.active_' . $bwpfm_theme_unique_title . '{
                                                            background: ' . $bwpfm_acc_heading_active_bg . ';   
                                                          }
                                                          
                                                          div.bwl_acc_container h2.active_' . $bwpfm_theme_unique_title . ' a,
                                                          div.bwl_acc_container h2.active_' . $bwpfm_theme_unique_title . ' a:hover{
                                                            background: ' . $bwpfm_acc_heading_active_bg . ';   
                                                            color: ' . $bwpfm_acc_heading_active_color . ';   
                                                          }
                                                          
                                                          div.bwl_acc_container h2.active_' . $bwpfm_theme_unique_title . ' a:hover:before,
                                                            content: "\f068";
                                                          }

                                                         ';
        }
        
    }
    
        
    /*------------------------------ Default Theme Info  ---------------------------------*/

    $bwpfm_default_theme= bwpfm_default_themes();

    foreach ( $bwpfm_default_theme as $default_theme_key=>$default_theme_value  ) {

        $bwpfm_custom_theme .='div.bwl_acc_container h2.acc_trigger_' . $default_theme_key . '{
                                                        background: ' . $default_theme_value['bwpfm_acc_heading_bg'] . ';   
                                                        color: ' . $default_theme_value['bwpfm_acc_heading_color'] . ';   
                                                      }

                                                      div.bwl_acc_container h2.acc_trigger_' . $default_theme_key . ' a,
                                                      div.bwl_acc_container h2.acc_trigger_' . $default_theme_key . ' a:hover{
                                                        background: ' . $default_theme_value['bwpfm_acc_heading_bg'] . ';   
                                                        color: ' . $default_theme_value['bwpfm_acc_heading_color'] . ';   
                                                      }

                                                      div.bwl_acc_container h2.acc_trigger_' . $default_theme_key . ' a:before {
                                                        content: "\f067";
                                                      }

                                                      div.bwl_acc_container h2.acc_trigger_' . $default_theme_key . ' a:hover:before {
                                                        content: "\f067";
                                                      }

                                                      div.bwl_acc_container h2.active_' . $default_theme_key . ' a:before {
                                                          content: "\f068";
                                                      }

                                                       div.bwl_acc_container h2.active_' . $default_theme_key . ' a:hover:before {
                                                          content: "\f068";
                                                      }

                                                      div.bwl_acc_container h2.active_' . $default_theme_key . '{
                                                        background: ' . $default_theme_value['bwpfm_acc_heading_active_bg'] . ';   
                                                      }

                                                      div.bwl_acc_container h2.active_' . $default_theme_key . ' a,
                                                      div.bwl_acc_container h2.active_' . $default_theme_key . ' a:hover{
                                                        background: ' . $default_theme_value['bwpfm_acc_heading_active_bg'] . ';   
                                                        color: ' . $default_theme_value['bwpfm_acc_heading_active_color'] . ';   
                                                      }

                                                      div.bwl_acc_container h2.active_' . $default_theme_key . ' a:hover:before,
                                                        content: "\f068";
                                                      }

                                                      div.bwl_acc_container div.acc_container_' . $default_theme_key . '{
                                                        background: ' . $default_theme_value['bwpfm_acc_content_bg'] . ';  
                                                        color: ' . $default_theme_value['bwpfm_acc_content_color'] . ';  
                                                      }';
    }


    /*---------------------------- Custom CSS -----------------------------------*/

    $bwpfm_custom_theme = bwpfm_css_compressor ($bwpfm_custom_theme);
    
    $bwpfm_custom_css = "";

    if( isset( $bwpfm_data['bwpfm_custom_css'] ) && $bwpfm_data['bwpfm_custom_css'] !="" ) {
        $bwpfm_custom_css = $bwpfm_data['bwpfm_custom_css'];
    }

    $bwpfm_custom_theme .= $bwpfm_custom_css;

    $bwpfm_custom_theme .= '</style>';

    echo $bwpfm_custom_theme;
    
}

add_action('wp_head', 'bwpfm_custom_theme_generator');


/*------------------------------ Custom Scripts  ---------------------------------*/

function bwpfm_custom_scripts_generator() {
    
    global $post;
    
    $bwpfm_data = get_option('bwpfm_options');
    
    if( isset($bwpfm_data['bwpfm_custom_themes']) ) {
        
        $bwpfm_custom_themes = $bwpfm_data['bwpfm_custom_themes'];
        
    } else {
        
        $bwpfm_custom_themes = array(); // initialize.
        
    }
    
    $bwpfm_custom_scripts = "";
    
    // Default.
            
    $bwpfm_highlight_bg = "#FEF5CB";
    $bwpfm_highlight_color = "#2C2C2C";
            
    $bwpfm_display_faq_counter = ( ( isset($bwpfm_data['bwpfm_display_faq_counter']) && $bwpfm_data['bwpfm_display_faq_counter'] == "" ) ? 0 : 1 );
    
        $bwpfm_custom_scripts .= '<script type="text/javascript">';
    
        if ( $bwpfm_display_faq_counter == 1 ) {

            $bwpfm_custom_scripts .= 'var bwpfm_display_faq_counter= '.$bwpfm_display_faq_counter.';';

        }
    
        $wpfm_theme = get_post_meta( $post->ID, 'wpfm_theme', true );
        
        if ( isset( $wpfm_theme) && $wpfm_theme != "" && ! bwpfm_is_default_theme( $wpfm_theme ) ) {
            
            if (sizeof($bwpfm_custom_themes) > 0) {

                foreach ($bwpfm_custom_themes as $themes_info) {
                    
                    $bwpfm_theme_unique_title = trim($themes_info['bwpfm_theme_unique_title']);
                    
                    if ( $bwpfm_theme_unique_title == $wpfm_theme ) {
                        
                        $bwpfm_highlight_bg = ( ! isset( $themes_info['bwpfm_highlight_bg'] ) ) ? "#F1F1F1" : $themes_info['bwpfm_highlight_bg'];
                        $bwpfm_highlight_color = ( ! isset( $themes_info['bwpfm_highlight_color'] ) ) ? "#2C2C2C" : $themes_info['bwpfm_highlight_color'];
                        
                    }
                    
                }
                
            }

        } else {

            $themes_info = bwpfm_default_themes();
            
            foreach ($themes_info as $themes_name => $themes_info) {
             
                    $bwpfm_theme_unique_title = $themes_name;
                    
                    if ( $bwpfm_theme_unique_title == $wpfm_theme ) {
                        
                        $bwpfm_highlight_bg = ( trim($themes_info['bwpfm_highlight_bg']) == "" ) ? "#F1F1F1" : trim($themes_info['bwpfm_highlight_bg']);
                        $bwpfm_highlight_color = ( trim($themes_info['bwpfm_highlight_color']) == "" ) ? "#F1F1F1" : trim($themes_info['bwpfm_highlight_color']);
                        
                    }
                    
                }
            
        }
        
        $bwpfm_custom_scripts .= 'var bwpfm_highlight_bg= "'.$bwpfm_highlight_bg.'";';
        $bwpfm_custom_scripts .= 'var bwpfm_highlight_color= "'.$bwpfm_highlight_color.'";';
        $bwpfm_custom_scripts .= 'var bwpfm_txt_nothing_found= "'.__('Nothing Found !', 'bwl-wpfm').'";';
        $bwpfm_custom_scripts .= 'var bwpfm_txt_item_found= "'.__('Item(s) Found!', 'bwl-wpfm').'";';
        $bwpfm_custom_scripts .= '</script>';
    
    echo $bwpfm_custom_scripts;
    
}

add_action('wp_head', 'bwpfm_custom_scripts_generator');