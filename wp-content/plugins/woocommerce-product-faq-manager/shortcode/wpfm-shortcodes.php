<?php

function wpfm_faq_id_unique_key($array, $keyname) {

    $new_array = array();

    foreach ($array as $key => $value) {

        if ($value[$keyname] == "") {
             //For Inline FAQs
            $new_array[wp_rand()] = $value;
        } else if (!isset($new_array[$value[$keyname]])) {
            //For Global FAQs
            $new_array[$value[$keyname]] = $value;
        } else {
            //do nothing.
        }
    }

    return array_values($new_array);
    
}

//@Description: Generate Search Box.
//@Since: Version 1.1.1
//@Last Update: 03-03-2018

add_shortcode('wpfm_sbox', 'wpfm_sbox');

function wpfm_sbox($atts) {

    $atts = shortcode_atts(array(
        'sbox' => 1,
        'placeholder' => __('Search ...', 'bwl-wpfm')
            ), $atts);

    extract($atts);

    $wpfm_sbox_html = '';

    if (isset($sbox) && $sbox == 1) {

        $wpfm_sbox_html .= '<div class="accordion_search_container">            
                                            <input type="text" class="accordion_search_input_box" id="accordion_search_input_box" value="" placeholder="' . $placeholder . '" />
                                        </div>
                                        <div id="search_result_container"></div>';
    }

    return $wpfm_sbox_html;
}

//@Description: Generate FAQ items for product tab.
//@Since: Version 1.0.0
//@Last Update: 11-04-2016
//@Change Log: 1.0.7: Fixed enqueue script issue.

add_shortcode('bwpfm_faq', 'bwpfm_faq');

function bwpfm_faq($atts) {

    // Note: Version 1.0.1 Fix product_id notice.

    extract(shortcode_atts(array(
        'product_id' => 0,
        'sbox' => 1,
        'status' => 1,
        'placeholder' => __('Search ...', 'bwl-wpfm')
                    ), $atts));

    //@Load Scripts.
    wp_enqueue_script('wpfm-highlight-regx-script');
    wp_enqueue_script('wpfm-bwl-wpfm-script');

    $wpfm_content = "";

    $wpfm_faqs = apply_filters('wpfm_process_meta_info', $product_id);

    /* ------------------------------ Suffle array Content  --------------------------------- */

//      shuffle($wpfm_faqs); 


    /* -----  Default Theme ---- */

    $bwpfm_data = get_option('bwpfm_options');

    $get_wpfm_theme = get_post_meta($product_id, 'wpfm_theme', true);

    if (isset($get_wpfm_theme) && $get_wpfm_theme != "") {

        $wpfm_theme = $get_wpfm_theme;
    } else if (isset($bwpfm_data['bwpfm_default_theme']) && $bwpfm_data['bwpfm_default_theme'] != "") {

        $wpfm_theme = $bwpfm_data['bwpfm_default_theme'];
    } else {

        $wpfm_theme = "";
    }

    if (isset($wpfm_theme) && $wpfm_theme != "") {

        $wpfm_trigger_theme_class = "acc_trigger acc_trigger_" . $wpfm_theme;
        $wpfm_container_theme_class = "acc_container acc_container_" . $wpfm_theme;
        $wpfm_container_theme_id = "active_" . $wpfm_theme;
    } else {

        $wpfm_trigger_theme_class = "acc_trigger";
        $wpfm_container_theme_class = "acc_container";
        $wpfm_container_theme_id = "active";
    }


    if (isset($bwpfm_data['bwpfm_auto_faq_status']) && $bwpfm_data['bwpfm_auto_faq_status'] == 1) {

        $wpfm_auto_global_faqs = wpfm_get_auto_faq_items();

        foreach ($wpfm_auto_global_faqs as $key => $value) {
            $wpfm_faqs[] = array(
                'wpfm_faq_type' => 1,
                'wpfm_global_faq_id' => $value
            );
        }
    }

    if (count($wpfm_faqs) > 0) {

        $i = 1;

        $wpfm_content .= '<div class="bwl_acc_container" data-theme-id="' . $wpfm_container_theme_id . '">';

        if ($sbox == 1) :

            $wpfm_content .= do_shortcode('[wpfm_sbox sbox="' . $sbox . '" placeholder="' . $placeholder . '"]');

        endif;

        // Deleting the duplicate items

        $wpfm_faqs = wpfm_faq_id_unique_key($wpfm_faqs, 'wpfm_global_faq_id');
        
        foreach ($wpfm_faqs as $faq_info) {

            // This code introducing in veresion 1.0.1

            if (isset($faq_info['wpfm_faq_type']) && $faq_info['wpfm_faq_type'] == 1) {

                $wpfm_faq_type = 1;
                $wpfm_global_faq_info = wpfm_get_global_faq_details($faq_info['wpfm_global_faq_id']);

                if (sizeof($wpfm_global_faq_info) > 0) {
                    $faq_info['faq_title'] = $wpfm_global_faq_info['faq_title'];
                    $faq_info['faq_desc'] = $wpfm_global_faq_info['faq_desc'];
                    $wpfm_faq_display_status = 1;
                } else {
                    $wpfm_faq_display_status = 0;
                }
                
            } else {

                $wpfm_faq_type = 0;
                $wpfm_faq_display_status = 1;
            }

            if ($i == 1) {

                $open_class = ' in';
                $open_class_style = 'style="height: auto;"';
                $collapsed = "";
            } else {

                $open_class = '';
                $open_class_style = 'style="height: 0px;"';
                $collapsed = "collapsed";
            }


            if ($wpfm_faq_display_status == 1 && $faq_info['faq_title'] != "") {

                $wpfm_content .= '<section>
                                                    <h2 class=" ' . $wpfm_trigger_theme_class . '"><a href="#"> ' . $faq_info['faq_title'] . ' </a></h2>
                                                    <div class="' . $wpfm_container_theme_class . '">
                                                        <div class="block">
                                                          ' . $faq_info['faq_desc'] . '
                                                        </div>
                                                    </div>
                                                </section>';

                $i++;
            }
        }

        $wpfm_content .= '</div>';
        
    } else {

        $wpfm_content .= '<p class="wpfm_no_faq">' . __("No FAQ Found", 'bwl-wpfm') . '</p>';
    }

    return $wpfm_content;
}

function wpfm_get_global_faq_details($faq_id) {

    $wpfm_global_faq_details = array();

    $post = get_post($faq_id, ARRAY_A);

    if (!empty($post) && $post['post_status'] == "publish" ) {

        $wpfm_global_faq_details['faq_title'] = $post['post_title'];
        $wpfm_global_faq_details['faq_desc'] = do_shortcode($post['post_content']);
    }

    wp_reset_query();

    return $wpfm_global_faq_details;
}

//@Description: Display Global FAQs In a Page
//@Since: Version 1.1.1
//@Last Update: 03-03-2018

add_shortcode('wpfm_global_faqs', 'wpfm_global_faqs');

function wpfm_global_faqs($atts) {

    $atts = shortcode_atts(array(
        'sbox' => 1,
        'placeholder' => __('Search ...', 'bwl-wpfm'),
        'orderby' => 'ID',
        'order' => 'DESC',
        'limit' => -1,
        'theme' => ''
            ), $atts);

    extract($atts);

    //@Load Scripts.
    wp_enqueue_script('wpfm-highlight-regx-script');
    wp_enqueue_script('wpfm-bwl-wpfm-script');

    $wpfm_content = '';

    $wpfm_theme = get_wpfm_theme_class($theme);

    if (isset($wpfm_theme) && $wpfm_theme != "") {

        $wpfm_trigger_theme_class = "acc_trigger acc_trigger_" . $wpfm_theme;
        $wpfm_container_theme_class = "acc_container acc_container_" . $wpfm_theme;
        $wpfm_container_theme_id = "active_" . $wpfm_theme;
    } else {

        $wpfm_trigger_theme_class = "acc_trigger";
        $wpfm_container_theme_class = "acc_container";
        $wpfm_container_theme_id = "active";
    }

    // Global FAQ ids

    $args = array(
        'post_status' => 'publish',
        'post_type' => 'bwl-woo-faq-manager',
        'limit' => -1,
        'orderby' => $orderby,
        'order' => $order,
        'posts_per_page' => $limit
    );

    $loop = new WP_Query($args);

    $wpfm_content .= '<div class="bwl_acc_container" data-theme-id="' . $wpfm_container_theme_id . '">';

    if ($loop->have_posts()) :

        if ($sbox == 1) :

            $wpfm_content .= do_shortcode('[wpfm_sbox sbox="' . $sbox . '" placeholder="' . $placeholder . '"]');

        endif;

        while ($loop->have_posts()) :

            $loop->the_post();

            $wpfm_content .= '<section>
                                            <h2 class=" ' . $wpfm_trigger_theme_class . '"><a href="#"> ' . get_the_title() . ' </a></h2>
                                            <div class="' . $wpfm_container_theme_class . '">
                                                <div class="block">
                                                  ' . get_the_content() . '
                                                </div>
                                            </div>
                                        </section>';
        endwhile;

    else:

        $wpfm_content .= '<p class="wpfm_no_faq">' . __("No FAQ Found", 'bwl-wpfm') . '</p>';

    endif;

    wp_reset_query();

    $wpfm_content .= '</div>';

    return $wpfm_content;
}

function wpfm_get_auto_faq_items() {

    $wpfm_auto_faq_ids = array();

    $args = array(
        'post_type' => 'bwl-woo-faq-manager',
        'meta_key' => 'wpfm_auto_global_faq',
        'meta_value' => 1,
        'meta_compare' => '=',
    );

    $loop = new WP_Query($args);

    if ($loop->have_posts()) :

        while ($loop->have_posts()) :

            $loop->the_post();

            $wpfm_auto_faq_ids[] = get_the_ID();

        endwhile;

    endif;

    wp_reset_query();

    return $wpfm_auto_faq_ids;
    
}