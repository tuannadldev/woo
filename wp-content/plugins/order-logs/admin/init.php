<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
ini_set('memory_limit', '1024M');
add_action( 'admin_menu', 'cbe_admin_menu', 8 );
function cbe_admin_menu() {
    global $_wp_last_object_menu;
    $_wp_last_object_menu++;

    $contact_admin = add_menu_page(
        __( 'Order Logs', 'order-logs' ),
        __( 'Order Logs', 'order-logs' ),
        'manage_options', 'order-logs',
        'cbe_contact_admin_page', 'dashicons-feedback',
        $_wp_last_object_menu );
    add_action( 'load-' . $contact_admin, 'cbe_admin_order_logs' );
}
function cbe_contact_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( __( 'Order Logs', 'order-logs' ) );?></h1>
    </div>
    <?php do_action( 'cbe_admin_updated_message' ); ?>
    <p><?php _e('Enter the order id to check the log'); ?></p>
    <form method="get" id="cbe-export-filters" action="<?php echo admin_url( 'admin.php?page=order-logs' );?>">
        <fieldset>
            <input type="hidden" class="order_id" name="page" value="order-logs"/>
            <input type="text" class="order_id" name="order_id" value=""/>
        </fieldset>
        <?php submit_button( __('Submit') ); ?>
    </form>
        <?php if ( isset( $_GET['order_id'] ) && $_GET['order_id'] != '' ) {
            get_order_log($_GET['order_id']);
        } ?>

    <?php
}

