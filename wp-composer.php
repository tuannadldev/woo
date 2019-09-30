<?php
/** Make sure that the WordPress bootstrap has run before continuing. */
require( dirname( __FILE__ ) . '/wp-load.php' );

    $order_id = $_REQUEST['order'];

$order = wc_get_order($order_id);

$oder_items = array();
$order_items_oms = array();

$total_origin_price = 0;
foreach ($order->get_items() as $key => $item_data) {

    $product = $item_data->get_product();
    $item_quantity = $item_data->get_quantity(); // Get the item quantity



    $item_total = $item_data->get_total(); // Get the item line total

    $total_discount=0;
    if($product->get_sale_price()>0){
        $total_discount = $product->get_regular_price()*$item_quantity - $product->get_sale_price()*$item_quantity;
    }

    if($item_total == 0){
        $total_discount = $product->get_regular_price()*$item_quantity;
    }



    $oder_items[] = '{
         "product_id":"' . trim($product->get_sku()) . '",
         "unit":"' . ucfirst(convert_vi_to_en(get_post_meta($product->id, 'unit', TRUE))) . '",
         "price":"' . $product->get_regular_price() . '",
         "quantity":"' . $item_quantity . '",
         "discount_id":"",
         "discount":"'. $total_discount.'",
         "total_price":"' . $item_total . '"
      }';

}

print_r($oder_items);