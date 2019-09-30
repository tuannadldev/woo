<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function wpfm_custom_js_string() {
    ?>
    <script type="text/javascript">

        var wpfm_saving_string = '<?php _e('Saving.....', 'bwl-wpfm'); ?>',
                wpfm_saved_string = '<?php _e('Saved !', 'bwl-wpfm'); ?>',
                wpfm_loading_string = '<?php _e('Loading .....', 'bwl-wpfm'); ?>',
                wpfm_no_faq_string = '<?php _e('No FAQ Found!', 'bwl-wpfm'); ?>';

    </script>

    <?php
}

add_action('admin_head', 'wpfm_custom_js_string');

/* ------------------------------  Modal --------------------------------- */

function bwpfm_fsa_modal_window($hook) {
    ?>

    <div id="dialog-form" title="Woo Product FAQ Sorting" style="display: none;">

        <div class="faq-sort-container">
            <p class="notification_box"></p>
            <ul id="bwpfm_fsa_items">
            </ul>
        </div>
    </div>

    <?php
}

add_action('admin_footer-edit.php', 'bwpfm_fsa_modal_window'); // Fired on the page with the posts table

function wpfm_fs_get_faq_data() {

    $wpfm_content = "";
    $product_id = esc_attr($_POST['product_id']);
    $wpfm_faqs = apply_filters('wpfm_process_meta_info', $product_id);

    if (count($wpfm_faqs) > 0) {

        foreach ($wpfm_faqs as $faq_key => $faq_info) {

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


            if ($wpfm_faq_display_status == 1) {

                $wpfm_content .= '<li id="' . $faq_key . '"> ' . $faq_info['faq_title'] . ' </li>';
            }
        }
    }

    echo $wpfm_content;

    die();
}

add_action('wp_ajax_wpfm_fs_get_faq_data', 'wpfm_fs_get_faq_data');

add_action('wp_ajax_nopriv_wpfm_fs_get_faq_data', 'wpfm_fs_get_faq_data');

/* ------------------------------ Apply Sorting --------------------------------- */

function bwpfm_fsa_apply_sort() {

    $product_id = esc_attr($_POST['product_id']);
    $order = explode(',', esc_attr($_POST['order']));

    $wpfm_faqs = apply_filters('wpfm_process_meta_info', $product_id);

    $wpfm_contents = array();

    if (count($order) > 0) {

        foreach ($order as $order_key => $order_value) {

            $wpfm_contents[] = $wpfm_faqs[$order_value];
        }

        update_post_meta($product_id, apply_filters('filter_wpfm_content_meta', ''), $wpfm_contents);
    }

    echo 1;
    die();
}

add_action('wp_ajax_bwpfm_fsa_apply_sort', 'bwpfm_fsa_apply_sort');

add_action('wp_ajax_nopriv_bwpfm_fsa_apply_sort', 'bwpfm_fsa_apply_sort');