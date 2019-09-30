<?php
// Add to our admin_init function
add_action('quick_edit_custom_box', 'wpfm_product_quick_edit_box', 10, 2);

function wpfm_product_quick_edit_box($column_name, $post_type) {

    global $post;

    switch ($post_type) {

        case $post_type:

            switch ($column_name) {

                case 'wpfm_display_faq':
                    ?>


                    <fieldset class="inline-edit-col-right">
                        <div class="inline-edit-col">
                            <div class="inline-edit-group">
                                <label class="alignleft">

                                    <span class="checkbox-title"><?php _e('Hide FAQ Tab?', 'bwl-wpfm'); ?></span>
                                    <select name="wpfm_display_faq">
                                        <option value="3"><?php _e('- No Change -', 'bwl-wpfm'); ?></option>
                                        <option value="1"><?php _e('Yes', 'bwl-wpfm'); ?></option>
                                        <option value="2"><?php _e('No', 'bwl-wpfm'); ?></option>
                                    </select>
                                </label>

                            </div>
                        </div>
                    </fieldset>


                    <?php
                    break;
            }

            break;
    }
}

// Add to our admin_init function

add_action('save_post', 'wpfm_product_save_quick_edit_data', 10, 2);

function wpfm_product_save_quick_edit_data($post_id, $post) {

    // pointless if $_POST is empty (this happens on bulk edit)
    if (empty($_POST))
        return $post_id;

    // verify quick edit nonce
    if (isset($_POST['_inline_edit']) && !wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
        return $post_id;

    // don't save for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    // dont save for revisions
    if (isset($post->post_type) && $post->post_type == 'revision')
        return $post_id;

    switch ($post->post_type) {

        case $post->post_type:

            /**
             * Because this action is run in several places, checking for the array key
             * keeps WordPress from editing data that wasn't in the form, i.e. if you had
             * this post meta on your "Quick Edit" but didn't have it on the "Edit Post" screen.
             */
            $custom_fields = array('wpfm_display_faq');

            foreach ($custom_fields as $field) {

                if (array_key_exists($field, $_POST)) {

                    update_post_meta($post_id, $field, esc_attr( $_POST[$field] ) );
                }
            }

            break;
    }
}

/* ------------------------------  Buik Edit --------------------------------- */


add_action('bulk_edit_custom_box', 'wpfm_product_bulk_edit_box', 10, 2);

function wpfm_product_bulk_edit_box($column_name, $post_type) {

    global $post;

    switch ($post_type) {

        case $post_type:

            switch ($column_name) {

                case 'wpfm_display_faq':
                    ?>
                    <fieldset class="inline-edit-col-right">
                        <div class="inline-edit-col">
                            <div class="inline-edit-group">
                                <label class="alignleft">
                                    <span class="checkbox-title"><?php _e('Hide FAQ Tab?', 'bwl-wpfm'); ?></span>
                                    <select name="wpfm_display_faq">
                                        <option value="3"><?php _e('- No Change -', 'bwl-wpfm'); ?></option>
                                        <option value="1"><?php _e('Yes', 'bwl-wpfm'); ?></option>
                                        <option value="2"><?php _e('No', 'bwl-wpfm'); ?></option>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <?php
                    break;
            }

            break;
    }
}

add_action('wp_ajax_manage_wp_posts_using_bulk_edit_wpfm', 'manage_wp_posts_using_bulk_edit_wpfm');

function manage_wp_posts_using_bulk_edit_wpfm() {

    // we need the post IDs
    $post_ids = ( isset($_POST['post_ids']) && !empty($_POST['post_ids']) ) ? $_POST['post_ids'] : NULL;

    // if we have post IDs
    if (!empty($post_ids) && is_array($post_ids)) {

        // Get the custom fields

        $custom_fields = array('wpfm_display_faq');

        foreach ($custom_fields as $field) {

            // if it has a value, doesn't update if empty on bulk
            if (isset($_POST[$field]) && trim($_POST[$field]) != "") {

                // update for each post ID
                foreach ($post_ids as $post_id) {

                    if ($_POST[$field] == 2) {

                        update_post_meta($post_id, $field, "");
                        
                    } elseif ($_POST[$field] == 1) {

                        update_post_meta($post_id, $field, 1);
                        
                    } else {
                        // do nothing
                    }
                }
            }
        }
    }
}
