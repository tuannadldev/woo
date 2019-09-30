<?php

// Important Fix For Version 1.0.8
// NEVER DELETE THIS FUNCTION.
// @Author: Mahbub Alam Khan
// @Date: 15-04-16

add_filter('filter_wpfm_content_meta', 'filter_wpfm_content_meta');

function filter_wpfm_content_meta($old_meta_key = 'none') {

    if (BWL_WPFM_PLUGIN_VERSION >= '1.0.8') {

        return 'cmb_wpfm_contents';
    } else {

        return 'wpfm_contents';
    }
}

// If it's version greater than 1.0.8 then we are going to filter the data and send a cunck from multidimenssional array.
// @Since: Version 1.0.8
// @Date: 15-04-2016

add_filter('filter_wpfm_content_data', 'filter_wpfm_content_data');

function filter_wpfm_content_data($field_value) {

    if (BWL_WPFM_PLUGIN_VERSION >= '1.0.8') {

        if (!empty($field_value[0]) && is_array($field_value[0])) {

            return $field_value[0]; // Return only first indexed value of array.
        } else {

            return ""; // return nothing.
        }
    } else {

        return $field_value; // for old version less then 1.0.8
    }
}

add_filter('wpfm_process_meta_info', 'wpfm_process_meta_info');

function wpfm_process_meta_info($id) {

    //First it will try to get value for variable cmb_wpfm_contents
    // if it's return nothing then it will try again to get value for wpfm_contents.
    // If cmb_wpfm_contents return value then we are going to filter it and return $field_value[0]; other wise it return an empty string.
    // if wpfm_contents return any value then we just return it to our CMB.
    // Bit complex but really enjoy while working on this thing :P

    $field_value = get_post_meta($id, apply_filters('filter_wpfm_content_meta', ''));

    if (empty($field_value)) {

        $field_value = get_post_meta($id, 'wpfm_contents');
    } else {

        $field_value = apply_filters('filter_wpfm_content_data', $field_value);
    }

    return $field_value;
}