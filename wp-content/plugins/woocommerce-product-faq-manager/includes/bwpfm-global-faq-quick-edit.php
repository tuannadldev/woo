<?php
// Add to our admin_init function
add_action('quick_edit_custom_box', 'wpfm_global_faq_quick_edit_box', 10, 2);

function wpfm_global_faq_quick_edit_box($column_name, $post_type) {

    global $post;

    switch ($post_type) {

        case $post_type:

            switch ($column_name) {

                case 'wpfm_auto_global_faq':
                    
                    ?>

                    <fieldset class="inline-edit-col-right">
                        <div class="inline-edit-col">
                            <div class="inline-edit-group">
                                <label class="alignleft">

                                    <span class="checkbox-title"><?php _e('Add To Automatic FAQ?', 'bwl-wpfm'); ?></span>
                                    <select name="wpfm_auto_global_faq">
                                        <option value="2"><?php _e('- No Change -', 'bwl-wpfm'); ?></option>
                                        <option value="1"><?php _e('Yes', 'bwl-wpfm'); ?></option>
                                        <option value="0"><?php _e('No', 'bwl-wpfm'); ?></option>
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

add_action('save_post', 'wpfm_global_faq_save_quick_edit_data', 10, 2);

function wpfm_global_faq_save_quick_edit_data($post_id, $post) {

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
            
            $custom_fields = array('wpfm_auto_global_faq');

            foreach ($custom_fields as $field) {

                if (array_key_exists($field, $_POST)) {

                    update_post_meta( $post_id, $field, esc_attr($_POST[$field]) );
                    
                }
            }

            break;
    }
}
