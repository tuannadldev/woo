<?php

/* ------------------------------  Custom Column Section --------------------------------- */


$post_types = 'product';

// After manage text we need to add "custom_post_type" value.

add_filter('manage_' . $post_types . '_posts_columns', 'wpfm_custom_column_header');

// After manage text we need to add "custom_post_type" value.

add_action('manage_' . $post_types . '_posts_custom_column', 'wpfm_display_custom_column', 10, 1);

function wpfm_custom_column_header($columns) {

    return array_merge($columns, array(
        'bwpfm_total_faqs' => 'FAQs',
        'wpfm_display_faq' => 'Display',
        'bwpfm_faqs_sort' => 'FAQs Sort'
            )
    );
}

function wpfm_display_custom_column($column) {

    // Add A Custom Image Size For Admin Panel.

    global $post;

    switch ($column) {

        case 'bwpfm_total_faqs':

            $bwpfm_total_faqs = (int) count(apply_filters('wpfm_process_meta_info', $post->ID));
            echo '<div id="bwpfm_total_faqs-' . $post->ID . '" >&nbsp;' . $bwpfm_total_faqs . '</div>';

            break;

        case 'wpfm_display_faq':

            $wpfm_display_faq = ( get_post_meta($post->ID, "wpfm_display_faq", true) == "" ) ? "" : get_post_meta($post->ID, "wpfm_display_faq", true);

            // FAQ Display Status In Text.

            $wpfm_display_faq_status_in_text = ( $wpfm_display_faq == 1 ) ? __("Hidden", "bwl-wpfm") : __("Visible", "bwl-wpfm");

            echo '<div id="wpfm_display_faq-' . $post->ID . '" data-status_code="' . $wpfm_display_faq . '" >' . $wpfm_display_faq_status_in_text . '</div>';

            break;

        case 'bwpfm_faqs_sort':

            $bwpfm_total_faqs = (int) count(apply_filters('wpfm_process_meta_info', $post->ID));
            echo '<div id="' . $post->ID . '" class="ico_bwpfm_faqs_sort"><button class="wpfm_sort" product_id="' . $post->ID . '"></button></div>';

            break;
    }
}



/* ------------------------------  Global FAQ Automatic Integration --------------------------------- */


$bwfm_post_type = 'bwl-woo-faq-manager';

// After manage text we need to add "custom_post_type" value.

add_filter('manage_' . $bwfm_post_type . '_posts_columns', 'bwfm_custom_column_header');

// After manage text we need to add "custom_post_type" value.

add_action('manage_' . $bwfm_post_type . '_posts_custom_column', 'bwfm_display_custom_column', 10, 1);

function bwfm_custom_column_header($columns) {

    return array_merge($columns, array(
        'wpfm_auto_global_faq' => __('Auto Integration', "bwl-wpfm") 
            )
    );
}

function bwfm_display_custom_column($column) {

    // Add A Custom Image Size For Admin Panel.

    global $post;

    switch ($column) {

        case 'wpfm_auto_global_faq':

            $wpfm_auto_global_faq = ( get_post_meta($post->ID, "wpfm_auto_global_faq", true) == "" ) ? "" : get_post_meta($post->ID, "wpfm_auto_global_faq", true);

            // FAQ Display Status In Text.

            $wpfm_auto_global_faq_status_in_text = ( $wpfm_auto_global_faq == 1 ) ? __("Yes", "bwl-wpfm") : __("No", "bwl-wpfm");

            echo '<div id="wpfm_auto_global_faq-' . $post->ID . '" data-status_code="' . $wpfm_auto_global_faq . '" >' . $wpfm_auto_global_faq_status_in_text . '</div>';

            break;
    }
}
