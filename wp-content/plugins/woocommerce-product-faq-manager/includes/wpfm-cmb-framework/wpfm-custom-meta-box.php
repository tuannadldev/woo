<?php
/* ------------------------------  Modal --------------------------------- */

function bwl_wpfm_faq_form_modal($hook) {

    $current_post_type = "";

    if (isset($_GET['post_type']) && $_GET['post_type'] == "product") {

        $current_post_type = "product";
    } else if (isset($_GET['post']) && get_post_type($_GET['post']) === 'product') {

        $current_post_type = "product";
    } else {

        $current_post_type = "";
    }

    // If current page is not relate with product then we return null.

    if ($current_post_type != "product") {
        return '';
    }


    // Translation Area.

    $text_wpfm_faq_type = __('FAQ Type', 'bwl-wpfm');
    $text_wpfm_faq_title = __('FAQ Title', 'bwl-wpfm');
    $text_wpfm_faq_details = __('FAQ Details', 'bwl-wpfm');
    $text_wpfm_new_faq = __('New FAQ', 'bwl-wpfm');
    $text_wpfm_ready_made_faq = __('Global FAQs', 'bwl-wpfm');
    $text_wpfm_select_faq = __('Select FAQ', 'bwl-wpfm');

    // @Description:Get list of global FAQs.
    // @Since:; Version 1.0.3
    // Readyâ€“made FAQs  ids

    $args = array(
        'post_type' => 'bwl-woo-faq-manager',
        'orderby' => 'title',
        'order' => 'ASC',
        'posts_per_page' => -1
    );

    $loop = new WP_Query($args);

    $wpfm_global_faqs = array();

    if ($loop->have_posts()) :

        while ($loop->have_posts()) :

            $loop->the_post();

            $post_status = "";

            if (get_post_status() == "pending" || get_post_status() == "draft" || get_post_status() == "private") {

                $post_status = " (" . get_post_status() . ")";
            }

            $wpfm_global_faqs[get_the_ID()] = get_the_title() . $post_status;

        endwhile;

    endif;

    wp_reset_query();
    ?>
    <div id="wpfm-dialog-form" title="<?php _e("FAQ Settings", "bwl-wpfm"); ?>" style="display: none;">

        <div class="wpfm-faq-form-container">

            <li class="wpfm_cmb_repeat_row custom_wpfm_faq_rows">

                <p class="cmb_repeat_row cont_wpfm_faq_type">
                    <label><?php echo $text_wpfm_faq_type; ?> : </label>
                    <input type="radio" checked="checked" value="0" name="wpfm_faq_type" class="wpfm_faq_type"> <?php echo $text_wpfm_new_faq; ?>                            
                    &nbsp; <input type="radio" value="1" name="wpfm_faq_type" class="wpfm_faq_type"> <?php echo $text_wpfm_ready_made_faq; ?> 
                </p>

                <p class="cmb_repeat_row cont_wpfm_faq_title">
                    <label><?php echo $text_wpfm_faq_title; ?> :</label>
                    <input type="text" value="" name="faq_title" class="widefat wpfm_faq_title">
                </p>

                <p class="cmb_repeat_row cont_wpfm_faq_details">
                    <label><?php echo $text_wpfm_faq_details; ?> :</label>
    <?php echo wp_editor('', 'faq_desc'); ?>
                </p>

                <p class="cmb_repeat_row cont_wpfm_global_faqs" style="display: none;">
                    <label><?php echo $text_wpfm_ready_made_faq; ?> :</label>
                    <select name="wpfm_global_faq_id" id="wpfm_global_faq_id" class="wpfm_global_faq_id">
                        <option value="" selected="selected">- <?php echo $text_wpfm_select_faq; ?> -</option>
    <?php foreach ($wpfm_global_faqs as $faq_key => $faq_value) : ?>
                            <option value="<?php echo $faq_key ?>"><?php echo $faq_value ?></option>
    <?php endforeach; ?>
                    </select>
                </p>

            </li>

        </div>

    </div>


    <?php
}

add_action('admin_footer', 'bwl_wpfm_faq_form_modal'); // Fired on the page with the posts table


/* ------------------------------  Custom Meta Box Section --------------------------------- */

class WPFM_Meta_Box {

    function __construct($custom_fields) {

        $this->custom_fields = $custom_fields; //Set custom field data as global value.

        add_action('add_meta_boxes', array(&$this, 'wpfm_metaboxes'));

        add_action('save_post', array(&$this, 'save_wpfm_meta_box_data'));  
    }

    //Custom Meta Box.

    function wpfm_metaboxes() {

        $wpfm_cmb_custom_fields = $this->custom_fields;

        // First parameter is meta box ID.
        // Second parameter is meta box title.
        // Third parameter is callback function.
        // Last paramenter must be same as post_type_name

        add_meta_box(
                $wpfm_cmb_custom_fields['meta_box_id'], $wpfm_cmb_custom_fields['meta_box_heading'], array(&$this, 'show_meta_box'), $wpfm_cmb_custom_fields['post_type'], $wpfm_cmb_custom_fields['context'], $wpfm_cmb_custom_fields['priority']
        );
    }

    function show_meta_box($post) {

        $wpfm_cmb_custom_fields = $this->custom_fields;

        foreach ($wpfm_cmb_custom_fields['fields'] as $custom_field) :

            $field_value = get_post_meta($post->ID, $custom_field['id'], true);
            ?>

            <?php if ($custom_field['type'] == 'info') : ?>

                <p class="wpfm_cmb_row">
                    <label for="<?php echo $custom_field['id'] ?>"><?php echo $custom_field['title'] ?> </label>
                <?php if ($field_value != "") : ?>
                        <span><?php echo esc_attr($field_value); ?></span>
                <?php elseif (isset($custom_field['default_value']) && $custom_field['default_value'] != "") : ?>
                        <span><?php echo $custom_field['default_value']; ?></span>
                <?php else: ?>
                        <span>-</span>
                <?php endif; ?>
                </p>

            <?php endif; ?>

            <?php if ($custom_field['type'] == 'text') : ?>

                <p class="wpfm_cmb_row">
                    <label for="<?php echo $custom_field['id'] ?>"><?php echo $custom_field['title'] ?> </label>
                    <input type="<?php echo $custom_field['type'] ?>" id="<?php echo $custom_field['id'] ?>" name="<?php echo $custom_field['name'] ?>" class="<?php echo $custom_field['class'] ?>" value="<?php echo esc_attr($field_value); ?>"/>
                </p>

                <?php endif; ?>

                <?php if ($custom_field['type'] == 'textarea') : ?>

                <p class="wpfm_cmb_row">
                    <label for="<?php echo $custom_field['id'] ?>"><?php echo $custom_field['title'] ?> </label>
                    <textarea id="<?php echo $custom_field['id'] ?>" name="<?php echo $custom_field['name'] ?>" class="<?php echo $custom_field['class'] ?>"><?php echo esc_attr($field_value); ?></textarea>
                </p>

            <?php endif; ?>

            <?php if ($custom_field['type'] == 'select') : ?>

                <?php
                $values = get_post_custom($post->ID);

                $selected = isset($values[$custom_field['name']]) ? esc_attr($values[$custom_field['name']][0]) : $custom_field['default_value'];
                ?>

                <p class="wpfm_cmb_row">
                    <label for="<?php echo $custom_field['id'] ?>"><?php echo $custom_field['title'] ?> </label> 
                    <select name="<?php echo $custom_field['name'] ?>" id="<?php echo $custom_field['id'] ?>"> 

                        <option value="" selected="selected">- Select -</option>

                <?php foreach ($custom_field['value'] as $key => $value) : ?>
                            <option value="<?php echo $key ?>" <?php selected($selected, $key); ?> ><?php echo $value; ?></option> 
                <?php endforeach; ?>

                    </select>

                <?php if (isset($custom_field['desc']) && $custom_field['desc'] != "") { ?>
                        <i><?php echo $custom_field['desc']; ?></i>
                <?php } ?>
                </p> 

            <?php endif; ?>

            <?php if ($custom_field['type'] == 'ques_info_box' && $custom_field['value'] == 1) : ?>

                <p class="wpfm_cmb_row">
                    <label for="<?php echo $custom_field['id'] ?>"><?php echo $custom_field['title'] ?></label>
                    <br />
                    <em><?php echo $field_value; ?></em>
                </p>  

            <?php endif; ?>   

                <?php
                if ($custom_field['type'] == 'checkbox') :
                    echo $field_value;
                    ?>

                <p> 
                    <input type="checkbox" id="<?php echo $custom_field['id'] ?>" name="<?php echo $custom_field['name'] ?>" <?php if ($field_value != "") {
                    echo "checked=checked";
                } ?> />  
                    <label for="<?php echo $custom_field['id'] ?>"><?php echo $custom_field['title'] ?></label>  
                </p>  

            <?php endif; ?>

            <?php
            if ($custom_field['type'] == 'wpfm_repeatable') :

                // Translation Area.

                $text_wpfm_faq_type = __('FAQ Type', 'bwl-wpfm');
                $text_wpfm_faq_title = __('FAQ Title', 'bwl-wpfm');
                $text_wpfm_faq_details = __('FAQ Details', 'bwl-wpfm');
                $text_wpfm_new_faq = __('New FAQ', 'bwl-wpfm');
                $text_wpfm_ready_made_faq = __('Global FAQs', 'bwl-wpfm');
                $text_wpfm_select_faq = __('Select FAQ', 'bwl-wpfm');
                // @Description:Get list of global FAQs.

                $wpfm_global_faqs = array();

                if (isset($custom_field['wpfm_global_faqs'])) {
                    $wpfm_global_faqs = $custom_field['wpfm_global_faqs'];
                }

                ?>

                <p class="wpfm_cmb_row wpfm_cmb_db">
                    <label for="<?php echo $custom_field['id'] ?>"><?php echo $custom_field['title'] ?>: </label>

                <?php if (isset($custom_field['desc']) && $custom_field['desc'] != ""): ?>
                        <small class="small-text"><?php echo $custom_field['desc']; ?></small>
                <?php endif; ?>
                </p>

                <ul class="wpfm_cmb_repeat_field_container">

                <?php
                $i = 0;
                $wpfm_row_string = '';

                $field_value = apply_filters('wpfm_process_meta_info', $post->ID);

                if (!empty($field_value) && is_array($field_value)) {

                    foreach ($field_value as $data) {


                        // Checking if it's a global FAQ or Inline FAQ.
                        // New FAQ(Inline FAQ) = 0
                        // Global FAQ = 1


                        $wpfm_global_faq_checked = "";
                        $wpfm_new_faq_checked = "";

                        if (isset($data['wpfm_faq_type']) && $data['wpfm_faq_type'] == 1) {
                            // Global  FAQ section.
                            $wpfm_global_faq_checked = "checked=checked";
                            $wpfm_global_faq_id = $data['wpfm_global_faq_id'];
                            $display_wpfm_faq_title = isset($wpfm_global_faqs[$data['wpfm_global_faq_id']]) ? $wpfm_global_faqs[$data['wpfm_global_faq_id']] : '';
                        } else {

                            //New FAQ Section.
                            $wpfm_new_faq_checked = "checked=checked";
                            $wpfm_global_faq_id = "";
                            $display_wpfm_faq_title = $data['faq_title'];
                        }

                        // Finally we check if the title is null then we are not going to include this row in to meta box.
                        // Fixed in versin 1.0.8

                        if ($display_wpfm_faq_title != "") {

                            $wpfm_row_string .= $i . ',';
                            ?>

                                <li class="wpfm_cmb_repeat_row custom_wpfm_faq_rows wpfm_row_id_<?php echo $i; ?>" data-row_count="<?php echo $i; ?>">

                                    <p class="cmb_wpfm_title_container">
                                        <span class="cmb_wpfm_post_title"><?php echo $display_wpfm_faq_title; ?></span>  
                                        <span class="cmb_wpfm_ctrl_btns">
                                            <a href="#" class="wpfm_edit" title="<?php echo _e("Edit", 'bwl-wpfm'); ?>">E</a>
                                            <a href="#" class="wpfm_copy" title="<?php echo _e("Copy", 'bwl-wpfm'); ?>">C</a>
                                            <a href="#" class="wpfm_delete" title="<?php echo _e("Delete", 'bwl-wpfm'); ?>">X</a>
                                        </span>
                                    </p>

                                    <p class="cmb_repeat_row cont_wpfm_faq_type">
                                        <label><?php echo $text_wpfm_faq_type; ?> : </label>
                                        <input name="<?php echo 'wpfm_faq_type[' . $i . ']' ?>" type="radio" value="0" <?php echo $wpfm_new_faq_checked; ?>> <?php echo $text_wpfm_new_faq; ?>
                                        &nbsp; <input name="<?php echo 'wpfm_faq_type[' . $i . ']' ?>"type="radio" value="1" <?php echo $wpfm_global_faq_checked; ?>> <?php echo $text_wpfm_ready_made_faq; ?>
                                    </p>

                                    <p class="cmb_repeat_row cont_wpfm_faq_title">
                                        <label><?php echo $text_wpfm_faq_title; ?> :</label>
                                        <input type="text" class="widefat" name="<?php echo 'faq_title[' . $i . ']' ?>" value="<?php echo $data['faq_title']; ?>" />
                                    </p>

                                    <p class="cmb_repeat_row cont_wpfm_faq_details">
                                        <label><?php echo $text_wpfm_faq_details; ?> :</label>
                                        <textarea name="<?php echo 'faq_desc_' . $i; ?>" id="<?php echo 'faq_desc_' . $i; ?>" cols="30" rows="5"><?php echo $data['faq_desc']; ?></textarea>
                                    </p>

                                    <p class="cmb_repeat_row cont_wpfm_global_faqs">
                                        <label><?php echo $text_wpfm_ready_made_faq; ?> :</label>
                                        <select name="<?php echo 'wpfm_global_faq_id[' . $i . ']' ?>" id="">
                                            <option value="" selected="selected">- <?php echo $text_wpfm_select_faq; ?> -</option>
                                <?php foreach ($wpfm_global_faqs as $faq_key => $faq_value) : ?>
                                                <option value="<?php echo $faq_key ?>" <?php echo ( $faq_key == $wpfm_global_faq_id ) ? "selected=selected" : ""; ?>><?php echo $faq_value ?></option>
                            <?php endforeach; ?>
                                        </select>
                                    </p>

                                </li>	



                            <?php
                            $i++;
                        }
                    }
                }
                ?>
                </ul>

                <input type="hidden" name="wpfm_rows" value="<?php echo $i ?>" />
                <input type="hidden" name="wpfm_row_string" id="wpfm_row_string" value="<?php echo $wpfm_row_string; ?>" />
                <textarea id="wpfm_cmb_data_set" style="display: none;"><?php echo json_encode($wpfm_global_faqs); ?></textarea>
                <input type="hidden" id="wpfm_row_id" value="" />

                <input id="add_new_row"  type="button" 
                       class="button" value="<?php echo $custom_field['btn_text']; ?>" 
                       data-delete_text="X" 
                       data-text_wpfm_faq_type="<?php echo $text_wpfm_faq_type; ?>" 
                       data-text_wpfm_faq_title="<?php echo $text_wpfm_faq_title; ?>" 
                       data-text_wpfm_faq_details="<?php echo $text_wpfm_faq_details; ?>" 
                       data-text_wpfm_new_faq="<?php echo $text_wpfm_new_faq; ?>" 
                       data-text_wpfm_ready_made_faq="<?php echo $text_wpfm_ready_made_faq; ?>" 
                       data-text_wpfm_select_faq="<?php echo $text_wpfm_select_faq; ?>" 
                       data-upload_text="<?php _e('Upload', 'bwl-kb') ?>" 
                       data-field_type="<?php echo $custom_field['field_type'] ?>" 
                       data-field_name="<?php echo $custom_field['name'] ?>" data-label_text ="<?php echo $custom_field['label_text']; ?>" />

                            <?php endif; ?>

            <?php
        endforeach;
    }

    function save_wpfm_meta_box_data($id) {

        global $post;
        
        if (empty($_POST)) {
            
            return $id;
            
        } else if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

            return $id;
            
        } else {

            if ($post->post_type == 'product') {

                $wpfm_meta_custom_fields = $this->custom_fields;

                $wpfm_excluded_fileds = array('wpfm_rows', 'wpfm_row_string', 'bwpfm_fc_email_send_status');

                foreach ($wpfm_meta_custom_fields['fields'] as $custom_field) {

                    if (isset($_POST[$custom_field['name']])) {

                        // Updated in version 1.1.2

                        if (!in_array($custom_field['name'], $wpfm_excluded_fileds)) {

                            update_post_meta($id, $custom_field['name'], wp_filter_post_kses($_POST[$custom_field['name']]));
                        }
                    }
                }

                // Repeatable Fields Data Saving In Here.
                // Introduced in version 1.1.2

                if (isset($_POST['wpfm_row_string']) && $_POST['wpfm_row_string'] == "") {

                    delete_post_meta($id, apply_filters('filter_wpfm_content_meta', ''));
                } else if (isset($_POST['wpfm_row_string']) && $_POST['wpfm_row_string'] != "") {

                    // @Description: Get POST value and then first remove trailing comma from the string and then explode that string by comma delimeter.
                    // This will return us an array.
                    // If array size is zero. Then we remove all the data. Else insert the FAQ values.
                    // We need to remove trailing comma from the string.

                    $wpfm_row_string = array();

                    if (strlen(trim($_POST['wpfm_row_string'])) > 0) {

                        $wpfm_row_string = explode(',', substr($_POST['wpfm_row_string'], 0, strlen($_POST['wpfm_row_string']) - 1));
                    }


                    // @Description: First always remove the old datas.
                    // @Paramaters: wpfm_contents
                    // @Data Type: Array.

                    if (sizeof($wpfm_row_string) > 0) {

                        $wpfm_contents = array();

                        foreach ($wpfm_row_string as $key => $value) {

                            // Assign Row ID in to i.
                            $i = $value;

                            $wpfm_contents[] = array(
                                'wpfm_faq_type' => isset($_POST['wpfm_faq_type'][$i]) ? $_POST['wpfm_faq_type'][$i] : 0,
                                'faq_title' => isset($_POST['faq_title'][$i]) ? wp_filter_post_kses($_POST['faq_title'][$i]) : '',
                                'faq_desc' => isset($_POST['faq_desc_' . $i]) ? wp_filter_post_kses($_POST['faq_desc_' . $i]) : '',
                                'wpfm_global_faq_id' => isset($_POST['wpfm_global_faq_id'][$i]) ? $_POST['wpfm_global_faq_id'][$i] : ''
                            );
                        }

                        update_post_meta($id, apply_filters('filter_wpfm_content_meta', ''), $wpfm_contents);
                    }
                } else {

                    // Do Nothing.
                }
            }


            // Automatic Global FAQ.

            if ($post->post_type == 'bwl-woo-faq-manager') {

                $wpfm_auto_global_faq = sanitize_text_field( $_POST['wpfm_auto_global_faq'] );
                update_post_meta($id, 'wpfm_auto_global_faq', $wpfm_auto_global_faq);
            }
        }
    }

}

// Register Custom Meta Box For BWL Pro Related Post Manager

function wpfm_custom_meta_box_init() {

    // Start Coding From here.

    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');

    wp_register_script('wpfm-cmb-admin-main', BWL_WPFM_PLUGIN_DIR . 'includes/wpfm-cmb-framework/admin/js/wpfm_cmb.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), BWL_WPFM_PLUGIN_VERSION, false);
    wp_register_style('wpfm-cmb-admin-style', BWL_WPFM_PLUGIN_DIR . 'includes/wpfm-cmb-framework/admin/css/wpfm_cmb.css', array(), BWL_WPFM_PLUGIN_VERSION, 'all');

    wp_enqueue_script('wpfm-cmb-admin-main');
    wp_enqueue_style('wpfm-cmb-admin-style');

    $wpfm_custom_post_types = array('product');

    foreach ($wpfm_custom_post_types as $post_type_key => $post_type_value) {


        // @Description: Get All The Themes.
        // @Since: version 1.0.2

        $plan_themes_array = array(
            'red_theme' => 'Red Theme',
            'blue_theme' => 'Blue Theme',
            'green_theme' => 'Green Theme',
            'orange_theme' => 'Orange Theme',
            'yellow_theme' => 'Yellow Theme'
        );

        $bwpfm_data = get_option('bwpfm_options');

        if (isset($bwpfm_data['bwpfm_custom_themes'])) {

            $bwpfm_custom_themes = $bwpfm_data['bwpfm_custom_themes'];
        } else {

            $bwpfm_custom_themes = array(); // initialize.
        }

        if (sizeof($bwpfm_custom_themes) > 0) {
            foreach ($bwpfm_custom_themes as $themes_info) {
                $bptm_theme_title = trim($themes_info['bwpfm_theme_title']);
                $bptm_theme_unique_title = trim($themes_info['bwpfm_theme_unique_title']);
                $plan_themes_array[$bptm_theme_unique_title] = ucwords(( $bptm_theme_title == "" ) ? "untitled" : $bptm_theme_title);
            }
        }

        // Related KB Posts Custom Meta Box.

        $wpfm_display_themes_custom_fields = array(
            'meta_box_id' => 'wpfm_display_themes_custom_fields', // Unique id of meta box.
            'meta_box_heading' => __('BWL Woo FAQ Display & Theme Settings', 'bwl-wpfm'), // That text will be show in meta box head section.
            'post_type' => $post_type_value, // define post type. go to register_post_type method to view post_type name.        
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                'wpfm_display_faq' => array(
                    'title' => __('Hide FAQ Tab?', 'bwl-wpfm'),
                    'id' => 'wpfm_display_faq',
                    'name' => 'wpfm_display_faq',
                    'type' => 'select',
                    'value' => array(
                        '1' => __('Yes', 'bwl-wpfm'),
                        '0' => __('No', 'bwl-wpfm')
                    ),
                    'default_value' => '',
                    'class' => 'widefat'
                ),
                'wpfm_theme' => array(
                    'title' => __('FAQ Theme', 'bwl-kb'),
                    'id' => 'wpfm_theme',
                    'name' => 'wpfm_theme',
                    'type' => 'select',
                    'value' => $plan_themes_array,
                    'default_value' => '',
                    'class' => 'widefat'
                )
            )
        );


        new WPFM_Meta_Box( $wpfm_display_themes_custom_fields );

        // Custom Attachment Fields.
        //@introduced in version 1.1.2


        $args = array(
            'post_type' => 'bwl-woo-faq-manager',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1
        );

        $loop = new WP_Query($args);

        $wpfm_global_faqs = array();

        if ($loop->have_posts()) :

            while ($loop->have_posts()) :

                $loop->the_post();

                $post_status = "";

                if (get_post_status() == "pending" || get_post_status() == "draft" || get_post_status() == "private") {

                    $post_status = " (" . get_post_status() . ")";
                }

                $wpfm_global_faqs[get_the_ID()] = get_the_title() . $post_status;

            endwhile;

        endif;

        wp_reset_query();


        $wpfm_faq_fields = array(
            'meta_box_id' => 'cmb_wpfm_faq_fields', // Unique id of meta box.
            'meta_box_heading' => __('BWL Woo FAQ Settings', 'bwl-kb'), // That text will be show in meta box head section.
            'post_type' => $post_type_value, // define post type. go to register_post_type method to view post_type name.        
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                'wpfm_contents' => array(
                    'title' => __('Add FAQ Questions', 'bwl-wpfm'),
                    'id' => 'wpfm_contents',
                    'name' => 'wpfm_contents',
                    'type' => 'wpfm_repeatable',
                    'value' => '',
                    'default_value' => '',
                    'class' => 'widefat',
                    'btn_text' => __('Add New FAQ', 'bwl-kb'),
                    'label_text' => __('File: ', 'bwl-kb'),
                    'field_type' => 'wpfm_repeatable',
                    'wpfm_global_faqs' => $wpfm_global_faqs
                )
            )
        );


        new WPFM_Meta_Box( $wpfm_faq_fields );


        // Automatic FAQ Meta Box.

        $wpfm_auto_faq_custom_fields = array(
            'meta_box_id' => 'wpfm_wpfm_auto_global_faq_fields', // Unique id of meta box.
            'meta_box_heading' => __('Automatic FAQ Settings', 'bwl-wpfm'), // That text will be show in meta box head section.
            'post_type' => 'bwl-woo-faq-manager', // define post type. go to register_post_type method to view post_type name.        
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                'wpfm_auto_global_faq' => array(
                    'title' => __('Add To Automatic FAQ?', 'bwl-wpfm'),
                    'id' => 'wpfm_auto_global_faq',
                    'name' => 'wpfm_auto_global_faq',
                    'type' => 'select',
                    'value' => array(
                        '1' => __('Yes', 'bwl-wpfm'),
                        '0' => __('No', 'bwl-wpfm')
                    ),
                    'default_value' => '',
                    'class' => 'widefat'
                )
            )
        );


        new WPFM_Meta_Box( $wpfm_auto_faq_custom_fields );
    }
}

// META BOX START EXECUTION FROM HERE.

add_action('admin_init', 'wpfm_custom_meta_box_init');
