<?php
if ( ! defined( 'ABSPATH' ) ) {
    /** Set up WordPress environment */
    require_once( dirname( __FILE__ ) . '/wp-load.php' );
}



$args_checkstore = [
    'posts_per_page' => -1,
    'offset' => 0,
    'post_type' => 'wc_pickup_location',
    'suppress_filters' => TRUE,
];
$args_checkstore['meta_query'] = [
    'relation' => 'AND',
    [
        'key' => 'code',
        'value' => $_REQUEST['store_code'],
        'compare' => '=',
    ],
];
$blocks = get_posts($args_checkstore);
if(count($blocks)){
    echo json_encode(array('result'=>$blocks[0]->ID));
    exit;
}

echo json_encode(array('result'=>'error'));
exit;