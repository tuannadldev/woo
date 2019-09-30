<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
ini_set('memory_limit', '1024M');
add_action( 'admin_menu', 'cbe_bubble_admin_menu', 8 );
function cbe_bubble_admin_menu() {
    global $_wp_last_object_menu;
    $_wp_last_object_menu++;

    $contact_admin = add_menu_page(
        __( 'Bubble', 'bubble-products' ),
        __( 'Bubble', 'bubble-products' ),
        'manage_options', 'bubble-products',
        'cbe_bubble_contact_admin_page', 'dashicons-feedback',
        $_wp_last_object_menu );
    add_action( 'load-' . $contact_admin, 'cbe_bubble_admin_order_logs' );
}
function cbe_bubble_contact_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( __( 'Bubble', 'bubble-products' ) );?></h1>
    </div>
    <?php do_action( 'cbe_admin_updated_message' ); ?>
    <p><?php _e('Enter the product SKU to set the bubble'); ?></p>
    <form method="post" id="cbe-export-filters" action="<?php echo admin_url( 'admin.php?page=bubble-products' );?>">
        <fieldset>
            <input type="hidden" class="order_id" name="page" value="order-logs"/>
            <textarea class="bubble-sku" name="bubble-sku"></textarea>

        </fieldset>
        <?php submit_button( __('Submit') ); ?>
    </form>
        <?php if ( isset( $_REQUEST['bubble-sku'] ) && $_REQUEST['bubble-sku'] != '' ) {
            set_bubble($_REQUEST['bubble-sku']);
        } ?>
    <?php
}

