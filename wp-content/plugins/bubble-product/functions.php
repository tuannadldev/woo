<?php
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
/*
 * Support functions
 */
 function set_bubble($skuss){
     global $wpdb;

     $arg = array(
             'posts_per_page' => -1,
             'post_type' => 'product',
             'post_status' => 'public'

         );
     $related_posts = get_posts($arg);
     foreach ($related_posts as $key => $products) {
//         $product = wc_get_product($products->ID);
         $product_id = $products->ID;
         $data = get_post_meta($product_id,'wc_productdata_options', true );
         if($data == ''){
             $data = array();
         }
         $data[0]['_bubble_new'] = '';
         $data[0]['_bubble_text'] = '';
         update_post_meta($product_id, 'wc_productdata_options', $data);

     }


     $sku_array =  explode("\n",$skuss);

     foreach ($sku_array as $sku){
         if(trim($sku) != ''){
             $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", trim($sku) ) );

             $data = get_post_meta($product_id,'wc_productdata_options', true );
             if($data == ''){
                 $data = array();
             }
             $data[0]['_bubble_new'] = '"yes"';
             $data[0]['_bubble_text'] = 'Rẻ nhất';
             update_post_meta($product_id, 'wc_productdata_options', $data);
         }

     }

 ?>

<?php }?>
