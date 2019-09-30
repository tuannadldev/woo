<?php

//Add custom Theme Functions here
function pharma_scripts_styles() {
  wp_enqueue_style('pharma_main', '/wp-content/themes/mypham9-mst40315/lib/custom.css');
  wp_enqueue_script('pharma_submit', '/wp-content/themes/mypham9-mst40315/lib/main.js', []);
}

add_action('wp_enqueue_scripts', 'pharma_scripts_styles');

include 'inc/sync-order.php';

if (!wp_next_scheduled('oms_task_hook')) {
  wp_schedule_event(time(), 'hourly', 'oms_task_hook');
}

add_action('oms_task_hook', 'cron_task', 10, 0);


//Add tags

if (!wp_next_scheduled('add_tags_campaign_task_hook')) {
  wp_schedule_event(time(), 'monthly', 'add_tags_campaign_task_hook');
}

add_action('add_tags_campaign_task_hook', 'addTagsBySku', 10, 0);


function cron_task() {
  $product = new product_oms();
  $product->cron_task();
}

function smallenvelop_widgets_init() {
  register_sidebar([
    'name' => __('Footer Cloud tags', 'smallenvelop'),
    'id' => 'footer-cloud-tags',
    'before_widget' => '<div>',
    'after_widget' => '</div>',
    'before_title' => '<div style="margin-bottom:20px;"><span class="widget-title" >',
    'after_title' => '</span></div>',
  ]);
}

add_action('widgets_init', 'smallenvelop_widgets_init');


if (!wp_next_scheduled('oms_store_hook')) {
  wp_schedule_event(time(), 'daily', 'oms_store_hook');
}

add_action('oms_store_hook', 'cron_store', 10, 0);

function cron_store() {
  $url = 'http://testapi.pharmacity.vn/api/Ecomerce/allStore';
//    $url = 'https://sgpmcaxdev01.pharmacity.vn:78/api/Ecomerce/allStore';

  // Khởi tạo CURL
  $ch = curl_init($url);
  $headers = [
    'Content-Type: application/json',
    'admin-api-key: 5SM27dC9KdKyll4g1Dk2xY9gcMgQ81cIbjbCwytxNvloWa/vg0yicRB7oiY=',
  ];
  // Thiết lập có return
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HTTPHEADER, TRUE);
  $response = curl_exec($ch);
  curl_exec($ch);
  $as_store_data = [];
  $check_existing_store = [];
  global $wpdb;
  foreach (json_decode($response) as $result) {

    $as_store_data['title'] = $result->address;
    $as_store_data['description'] = '';
    $as_store_data['street'] = $result->address;
    $as_store_data['city'] = $result->district;
    $as_store_data['state'] = $result->province;
    $as_store_data['country'] = 203;
    $as_store_data['lat'] = $result->latitude;
    $as_store_data['lng'] = $result->longitude;
    $as_store_data['phone'] = $result->phone;
    $as_store_data['logo_id'] = 1;
    $as_store_data['marker_id'] = 1;
    $as_store_data['fax'] = $result->code;
    $as_store_data['open_hours'] = '{"mon":["09:30 AM - 06:30 PM"],"tue":["09:30 AM - 06:30 PM"],"wed":["09:30 AM - 06:30 PM"],"thu":["09:30 AM - 06:30 PM"],"fri":["09:30 AM - 06:30 PM"],"sat":["09:30 AM - 06:30 PM"],"sun":["09:30 AM - 06:30 PM"]}';

    if ($result->status == 1 && $result->store_status == 1) {
      $as_store_data['is_disabled'] = '';
    }
    else {
      $as_store_data['is_disabled'] = 1;
    }
    $count_sotre = $wpdb->get_results("SELECT id FROM " . ASL_PREFIX . "stores WHERE fax = '" . $result->code . "'", ARRAY_A);

    if ($count_sotre) {
      $new_store_id = $count_sotre[0]['id'];
      $wpdb->update(ASL_PREFIX . 'stores', $as_store_data, ['id' => $new_store_id]);
    }
    else {
      $wpdb->insert(ASL_PREFIX . 'stores', $as_store_data);
      $new_store_id = $wpdb->insert_id;
    }


    $category_count = $wpdb->get_results("SELECT id FROM " . ASL_PREFIX . "categories WHERE category_name = '" . $result->province . "'", ARRAY_A);
    if ($category_count) {
      $category_id = $category_count[0]['id'];
    }
    else {
      $wpdb->insert(ASL_PREFIX . 'categories', [
        'category_name' => $result->province,
        'is_active' => 1,
        'icon' => 'default.png',
      ]);
      $category_id = $wpdb->insert_id;
    }
    $check_existing_store[] = $result->code;

    $check_store_category = $wpdb->get_results("SELECT id FROM " . ASL_PREFIX . "stores_categories WHERE store_id=" . $new_store_id, ARRAY_A);
    if (!$check_store_category) {
      $wpdb->insert(ASL_PREFIX . 'stores_categories',
        ['store_id' => $new_store_id, 'category_id' => $category_id]);
    }
    if ($result->status == 1 && $result->store_status == 1) {
      $args = array(
          'post_title' => $as_store_data['title'].' '.$as_store_data['city'] ,
          'post_type' => 'wc_pickup_location',
          'post_status' => 'publish'
      );

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
              'value' => $result->code,
              'compare' => '=',
          ],
      ];
      $blocks = get_posts($args_checkstore);

      if(count($blocks) >0){
        $pickup_store_id = $blocks[0]->ID;
      }
      else{
        $pickup_store_id = wp_insert_post($args);
      }

      $_address_1 =  $result->address;
      $_city =  $result->district;
      $_phone =  $result->phone;
      $_pickup_location_latitude =  $result->latitude;
      $_pickup_location_longitude =  $result->longitude;
      $code =  $result->code;

      if ($pickup_store_id) {
        $table = "{$wpdb->prefix}woocommerce_pickup_locations_geodata";
        update_post_meta($pickup_store_id, 'code', $code);
        update_post_meta( $pickup_store_id, '_pickup_location_override_geocoding', 'yes' );
        $success = (bool) $wpdb->update(
            $table,
            array(
                'lat'          => $_pickup_location_latitude,
                'lon'          => $_pickup_location_longitude,
                'country'      => 'VN',
                'state'        => Get_State::get_city_code_by_name(ucwords($result->province)),
                'postcode'     => '',
                'city'         => $_city,
                'address_1'    => $_address_1,
                'address_2'    => $_address_1,
                'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
            ),
            array( 'post_id' => $pickup_store_id )
        );
      }


    }
    else{
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
              'value' => $result->code,
              'compare' => '=',
          ],
      ];
      $blocks = get_posts($args_checkstore);

      if(count($blocks) >0){
        $pickup_store_id = $blocks[0]->ID;
          wp_update_post(array(
              'ID'    => $pickup_store_id,
              'post_status'   =>  'draft'
          ));
//        wp_delete_post($pickup_store_id);
      }
    }
  }


  $args_checkstore = [
      'posts_per_page' => -1,
      'offset' => 0,
      'post_type' => 'wc_pickup_location',
      'suppress_filters' => TRUE,
  ];

  $blocks = get_posts($args_checkstore);
  foreach( $blocks as $key => $block){
    if(!in_array(get_field('code',$block->ID),$check_existing_store))
    {
        wp_update_post(array(
            'ID'    =>  $block->ID,
            'post_status'   =>  'draft'
        ));
//      wp_delete_post($block->ID);
    }
  }
}

function convert_vi_to_en($str) {
  $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
  $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
  $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
  $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
  $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
  $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
  $str = preg_replace("/(đ)/", 'd', $str);
  $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
  $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
  $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
  $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
  $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
  $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
  $str = preg_replace("/(Đ)/", 'D', $str);
  //  $str = str_replace(" ", "-", $str);
  return lcfirst($str);
}


function query_store() {
  $args = [
    'posts_per_page' => -1,
    'offset' => 0,
    'post_type' => 'pharmacy_store',
    'suppress_filters' => TRUE,
  ];
  $args['meta_query'] = [
    'relation' => 'AND',
    [
      'key' => 'store_status',
      'value' => 1,
      'compare' => '=',
    ],
  ];
  $blocks = get_posts($args);

  return $blocks;
}

// [bartag foo="foo-value"]
function nearest_store_map() {
  global $wpdb;
  $count_sotre = $wpdb->get_results("SELECT * FROM " . ASL_PREFIX . "stores WHERE  is_new_Store = 1", ARRAY_A);
  $content = '<div class="nearest-store">';
  if ($count_sotre) {

    $content .= '<div class="nearest-address store-manual"><span><img src="' . home_url() . '/wp-content/themes/mypham9-mst40315/lib/assets/u105.svg"></span><h4>' . $count_sotre[0]['street'] . ', ' . $count_sotre[0]['city'] . ', ' . $count_sotre[0]['state'] . ' <a href="' . home_url() . '/he-thong-cua-hang/">Change</a></h4></div>';
    $content .= '<div class="store-hotline"><span><img src="' . home_url() . '/wp-content/themes/mypham9-mst40315/lib/assets/u108.svg"></span><h4><a href="tel:18006821">Hotline - 1800 6821</a></h4></div>';
    $content .= '<div class="nearest-direction"><span><img src="' . home_url() . '/wp-content/themes/mypham9-mst40315/lib/assets/u111.svg"></span></span><h4><a href="' . home_url() . '/he-thong-cua-hang/?action=dir&long='.$count_sotre[0]['lng'].'&lat='.$count_sotre[0]['lat'].'">Directions</a></h4>';

  }
  else {
    $ip = $_SERVER['REMOTE_ADDR'];
    $location = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip));
    $count_sotre = $wpdb->get_results("SELECT * , (3956 * 2 * ASIN(SQRT( POWER(SIN(( " . $location['geoplugin_latitude'] . " - lat) *  pi()/180 / 2), 2) +COS( " . $location['geoplugin_latitude'] . " * pi()/180) * COS(lat * pi()/180) * POWER(SIN(( " . $location['geoplugin_longitude'] . "- lng) * pi()/180 / 2), 2) ))) as distance
from pharm_asl_stores WHERE is_disabled = 0 order by distance", ARRAY_A);

    $content .= '<div class="nearest-address store-auto"><span><img src="' . home_url() . '/wp-content/themes/mypham9-mst40315/lib/assets/u105.svg"></span><h4>' . $count_sotre[0]['street'] . ', ' . $count_sotre[0]['city'] . ', ' . $count_sotre[0]['state'] . ' <a href="' . home_url() . '/he-thong-cua-hang/">Change</a></h4></div>';
    $content .= '<div class="store-hotline"><span><img src="' . home_url() . '/wp-content/themes/mypham9-mst40315/lib/assets/u108.svg"></span><h4><a href="tel:18006821">Hotline - 1800 6821</a></h4></div>';
    $content .= '<div class="nearest-direction"><span><img src="' . home_url() . '/wp-content/themes/mypham9-mst40315/lib/assets/u111.svg"></span></span><h4><a href="' . home_url() . '/he-thong-cua-hang/?action=dir&long='.$count_sotre[0]['lng'].'&lat='.$count_sotre[0]['lat'].'">Directions</a></h4>';

  }
  $content .= '</div>';
  return $content;

}

add_shortcode('nearest_store_map', 'nearest_store_map');


// [bartag foo="foo-value"]
function our_store_map($atts) {
  $stores = query_store();

  $content = '<div class="tab">';
  $i = 0;
  foreach ($stores as $a_block) {
    $i++;

    $content .= '<button class="tablinks" onclick="openCity(event, \'' . get_field('code', $a_block->ID) . '\', \'' . get_field('latitude', $a_block->ID) . '\',\'' . get_field('longitude', $a_block->ID) . '\')">' . get_field('address', $a_block->ID) . '</button>';
  }
  $content .= '</div>';
  foreach ($stores as $a_block) {
    $content .= '<div id="' . get_field('code', $a_block->ID) . '" class="tabcontent"><h3>' . get_field('address', $a_block->ID) . '</h3><p class="map-' . get_field('code', $a_block->ID) . ' map-content" id="maps-' . get_field('code', $a_block->ID) . '"></p></div>';
  }
  return $content;

}

add_shortcode('our_store_map', 'our_store_map');


function recent_post_by_category() {
  global $post;
  $categories = get_the_category($post->ID);
  $catidlist = '';
  foreach ($categories as $category) {
    $catidlist .= $category->cat_ID . ",";
  }
  // Build our category based custom query arguments
  $custom_query_args = [
    'posts_per_page' => 8,
    // Number of related posts to display
    'post__not_in' => [$post->ID],
    // Ensure that the current post is not displayed
    'orderby' => 'rand',
    // Randomize the results
    'cat' => $catidlist,
    // Select posts in the same categories as the current post
  ];
  // Initiate the custom query
  $custom_query = new WP_Query($custom_query_args);


  $content = '<div class="widget">';
  $content .= '<ul>';

  if ($custom_query->have_posts()) {
    while ($custom_query->have_posts()) {
      $custom_query->the_post();
      $image_style = 'style="background: linear-gradient( rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.2) ), url(' . wp_get_attachment_thumb_url(get_post_thumbnail_id(get_the_ID())) . '); color:#fff; text-shadow:1px 1px 0px rgba(0,0,0,.5); border:0;"';

      $content .= '

            <li class="recent-blog-posts-li">
                <div class="flex-row recent-blog-posts align-top pt-half pb-half">
                    <div class="flex-col mr-half">
                        <div class="badge post-date badge-' . flatsome_option('blog_badge_style') . '">
                            <div class="badge-inner bg-fill" ' . $image_style . '>

                                <span class="post-date-day">' . get_the_time('d', get_the_ID()) . '</span><br>
                                <span class="post-date-month is-xsmall">' . get_the_time('M', get_the_ID()) . '</span>
                            </div>
                        </div>
                    </div><!-- .flex-col -->
                    <div class="flex-col flex-grow">
                        <a href="' . get_permalink(get_the_ID()) . '" title="' . esc_attr(get_the_title()) . '">' . get_the_title() . '</a>
                    </div>
                </div><!-- .flex-row -->
            </li>';


    }
  }
  $content .= '</ul>';
  $content .= '</div>';
  return $content;

}

add_shortcode('recent_post_by_category', 'recent_post_by_category');


function recent_product_by_post_tag() {
  global $post;
  $post_tags = get_the_tags();
  $tags_array = [];
  if ($post_tags) {
    foreach ($post_tags as $tag) {
      $tags_array[] .= $tag->name;
    }
  }


  foreach ($tags_array as $key => $value) {
    $district_term = term_exists($value, 'product_tag');
    if ($district_term) {
      $tags_ids[] = $district_term['term_id'];
    }
  }

  $arg =
    [
      'orderby' => 'rand',
      'posts_per_page' => 10,
      'post_type' => 'product',

      'tax_query' => [
        [
          'taxonomy' => 'product_tag',
          'field' => 'id',
          'terms' => $tags_ids,
        ],
      ],

    ];
  $related_posts = get_posts($arg);
  $content = '';
  if ($related_posts) {
    $content .= '<span class="widget-title "><span>Sản phẩm hữu ích</span></span>';
    $content .= '<div class="is-divider small"></div>';
    $content .= '<div class="widget">';
    $content .= '<ul>';

    foreach ($related_posts as $key => $products) {
      $product = wc_get_product($products->ID);
      $image_style = 'style="background:url(' . get_the_post_thumbnail_url($product->get_id(), 'full') . '); color:#fff; text-shadow:1px 1px 0px rgba(0,0,0,.5); border:0;"';

      $content .= '

                <li class="recent-blog-posts-li">
                    <div class="flex-row recent-blog-posts align-top pt-half pb-half">
                        <div class="flex-col mr-half">
                            <div class="badge post-date badge-product badge-' . flatsome_option('blog_badge_style') . '">
                                <div class="badge-inner bg-fill" ' . $image_style . '> </div>
                            </div>
                        </div><!-- .flex-col -->
                        <div class="flex-col flex-grow">
                            <a href="' . get_permalink($products->ID) . '" title="' . esc_attr($product->get_name()) . '">' . $product->get_name() . '</a>
                            <p>' . $product->get_price_html() . '</p>
                        </div>
                    </div><!-- .flex-row -->
                </li>';
    }

    $content .= '</ul>';
    $content .= '</div>';
  }

  return $content;

}

add_shortcode('recent_product_by_post_tag', 'recent_product_by_post_tag');

/**
 * Handle logic for custom login form.
 */
add_filter('authenticate', 'maybe_redirect_at_authenticate', 101, 3);
function maybe_redirect_at_authenticate($user, $username, $password) {
  // Check if the earlier authenticate filter (most likely,
  // the default WordPress authentication) functions have found errors

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login_data = oms_login_with_phone($username, $password);

    if ($login_data == FALSE) {
      $error_codes = encode('Xin lỗi quý khách, hệ thống tài khoản tạm thời bị lỗi <br> xin vui lòng đăng nhập lại sau');
      $login_url = home_url('/signin');
      $login_url = add_query_arg('login_error', $error_codes, $login_url);

      wp_redirect($login_url);
    }
    elseif ((empty($login_data['data']["loginWithPhone"]) && !empty($login_data["errors"]))) {

      if ($login_data["errors"][0]['message'] == 'validation') {
        $error_codes = encode("Vui lòng nhập số điện thoại và mật khẩu để đăng nhập");
        $login_url = home_url('/signin');
        $login_url = add_query_arg('login_error', $error_codes, $login_url);
      }
      else {
        $error_codes = encode($login_data["errors"][0]['message']);
        $login_url = home_url('/signin');
        $login_url = add_query_arg('login_error', $error_codes, $login_url);
      }

      wp_redirect($login_url);

    }
    else {

      // Create a new user if it's not exist
      if (!username_exists($username)) {
        $email = (isset($login_data['data']["loginWithPhone"]['email'])) ? $login_data['data']["loginWithPhone"]['email'] : '';

        if (email_exists($login_data['data']["loginWithPhone"]['email']) || empty($login_data['data']["loginWithPhone"]['email'])){
          $email = $username . '@' . str_replace([
              'https://',
              'http://',
              'www.',
            ], '', home_url()); // generate a fake email address
        }

        $user_data = [
          'user_login' => $username,
          'user_email' => $email,
          'user_pass' => $password,
          'display_name' => (isset($login_data['data']["loginWithPhone"]['name'])) ? $login_data['data']["loginWithPhone"]['name'] : '',
          'user_nicename' => (isset($login_data['data']["loginWithPhone"]['name'])) ? $login_data['data']["loginWithPhone"]['name'] : '',
          'first_name' => (isset($login_data['data']["loginWithPhone"]['first_name'])) ? $login_data['data']["loginWithPhone"]['first_name'] : '',
          'last_name' => (isset($login_data['data']["loginWithPhone"]['last_name'])) ? $login_data['data']["loginWithPhone"]['last_name'] : '',
        ];
        $user_id = wp_insert_user($user_data);

        //User OMS id

        update_user_meta($user_id, 'user_oms_id', $login_data['data']["loginWithPhone"]['id']);
        update_user_meta($user_id, 'code', $login_data['data']["loginWithPhone"]['code']);
        update_user_meta($user_id, 'customer_id', $login_data['data']["loginWithPhone"]['customer_id']);
        update_user_meta($user_id, 'reward_point', $login_data['data']["loginWithPhone"]['point']);

        // Billing user information
        update_user_meta($user_id, 'billing_first_name', $login_data['data']["loginWithPhone"]['first_name']);
        update_user_meta($user_id, 'billing_last_name', $login_data['data']["loginWithPhone"]['last_name']);
        update_user_meta($user_id, 'billing_company', $login_data['data']["loginWithPhone"]['company']);
        update_user_meta($user_id, 'billing_address_1', $login_data['data']["loginWithPhone"]['address'][0]['address']);
        update_user_meta($user_id, 'billing_city', $login_data['data']["loginWithPhone"]['address'][0]['province']);
        update_user_meta($user_id, 'billing_state', $login_data['data']["loginWithPhone"]['address'][0]['province']);
        update_user_meta($user_id, 'billing_country', 'VN');
        update_user_meta($user_id, 'billing_email', $login_data['data']["loginWithPhone"]['email']);
        update_user_meta($user_id, 'billing_phone', $login_data['data']["loginWithPhone"]['phone_number']);

        // Shipping user information
        update_user_meta($user_id, 'shipping_first_name', $login_data['data']["loginWithPhone"]['first_name']);
        update_user_meta($user_id, 'shipping_last_name', $login_data['data']["loginWithPhone"]['last_name']);
        update_user_meta($user_id, 'shipping_company', $login_data['data']["loginWithPhone"]['company']);
        update_user_meta($user_id, 'shipping_address_1', $login_data['data']["loginWithPhone"]['address'][0]['address']);
        update_user_meta($user_id, 'shipping_city', $login_data['data']["loginWithPhone"]['address'][0]['province']);
        update_user_meta($user_id, 'shipping_state', $login_data['data']["loginWithPhone"]['address'][0]['province']);
        update_user_meta($user_id, 'shipping_country', 'VN');
        update_user_meta($user_id, 'shipping_email', $login_data['data']["loginWithPhone"]['email']);
        update_user_meta($user_id, 'shipping_pone', $login_data['data']["loginWithPhone"]['phone_number']);


        if (is_wp_error($user_id)) {
          $error_codes  = $result->get_error_message();
          $login_url = home_url('/signin');
          $login_url = add_query_arg('login_error', encode($error_codes), $login_url);

          wp_redirect($login_url);
          exit;
        }

        $user = get_user_by('id', $user_id);

      }
      else {
        $user = get_user_by('login', $username);
      }

      if (isset($login_data['data']["loginWithPhone"]['api_token'])) {
        setcookie('oms_user_token', $login_data['data']["loginWithPhone"]['api_token'], time() + 31556926);
      }

      wp_set_current_user($user->ID);
      wp_set_auth_cookie($user->ID, TRUE);

      //sync point when login

      $phoen_update_data  = (isset($login_data['data']["loginWithPhone"]['point'])) ? $login_data['data']["loginWithPhone"]['point'] : 0 ;


      update_user_meta($user->ID, 'reward_point',$phoen_update_data);

      update_user_points($phoen_update_data,$user->ID);
      do_action('wp_login', $user->user_login, $user);

    }
  }

  if (get_user_meta($user->ID, 'account_activated')[0] == '0') {
    $login_url = home_url('/signin');
    $login_url = add_query_arg('login_error', 'inactive_account', $login_url);

    wp_redirect($login_url);
    exit;
  }

  return $user;
}

/**
 * Handle logic for custom reset password form.
 */
add_action('login_form_rp', 'do_password_reset');
add_action('login_form_resetpass', 'do_password_reset');
function do_password_reset() {
  if ('POST' == $_SERVER['REQUEST_METHOD']) {
    $rp_key = $_REQUEST['rp_key'];
    $rp_login = $_REQUEST['rp_login'];
    $user = check_password_reset_key($rp_key, $rp_login);
    if (!empty($user->get_error_code())) {
      if ($user && $user->get_error_code() === 'expired_key') {
        wp_redirect(home_url('/signin?reset_key=expiredkey'));
      }
      else {
        wp_redirect(home_url('/signin?reset_key=invalidkey'));
      }
      exit;
    }

    if (isset($_POST['pass1'])) {

      $redirect_url = home_url('/reset-password');
      if (empty($_POST['pass1'])) {
        // Password is empty
        $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
        $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
        $redirect_url = add_query_arg('errors', 'password_reset_empty', $redirect_url);

        wp_redirect($redirect_url);
        exit;
      }
      else {
        if (!preg_match('/[A-Z]/', $_POST['pass1']) || !preg_match("/[a-z]/", $_POST['pass1']) || strlen($_POST['pass1']) < 8 || !preg_match("#[0-9]+#", $_POST['pass1'])) {
          $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
          $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
          $redirect_url = add_query_arg('errors', 'invalid_pass', $redirect_url);
          wp_redirect($redirect_url);
          exit;
        }
        else {
          // Parameter checks OK, reset password
          reset_password($user, $_POST['pass1']);
          wp_redirect(home_url('/signin?password=changed'));
        }
      }
    }
    else {
      echo "Invalid request.";
    }

    exit;
  }
}

/**
 * Redirect non-login user to custom login page.
 */
function redirect_user() {
  if (!is_user_logged_in() && is_page('my-account')) {
    $return_url = esc_url(home_url('/signin/'));
    wp_redirect($return_url);
    exit;
  }
}

add_action('template_redirect', 'redirect_user');

/**
 * Change reset password email template.
 */
add_filter("retrieve_password_message", "pharma_custom_password_reset", 99, 4);
function pharma_custom_password_reset($message, $key, $user_login, $user_data) {

  $message = "Someone has requested a password reset for the following account:

" . sprintf(__('%s'), $user_data->user_email) . "

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:

" . network_site_url("reset-password?key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n";


  return $message;

}


/**
 * Redirect user after login.
 */

add_filter('login_redirect', 'redirect_after_login', 10, 3);
function redirect_after_login($redirect_to, $request, $user) {
  //is there a user to check?
  if (isset($user->roles) && is_array($user->roles)) {
    //check for admins
    if (in_array('administrator', $user->roles)) {
      // redirect them to the default place
      return $redirect_to;
    }
    else {
      return home_url();
    }
  }
  else {
    return $redirect_to;
  }
}

//
//function login_oms_validate($user, $password)
//{
//
//  die;
//    remove_action('authenticate', 'wp_authenticate_username_password', 20);
//    $user = new WP_Error( 'denied', __("<strong>ERROR</strong>: Please agree to our terms.") );
//
//
//
//  return $user;
//}
//add_filter( 'wp_authenticate_user', 'login_oms_validate', 20, 3 );

function oms_login_with_phone($phone, $password) {
  $ch = curl_init();

  $phone = preg_replace('/[^A-Za-z0-9\-]/', '', $phone);
  $password = preg_replace('/[^A-Za-z0-9\-]/', '', $password);

  curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
//  curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $query = '{"query":"mutation{\n  loginWithPhone(phone:\"' . $phone . '\", password:\"' . $password . '\"){\n    api_token,id,email,point,name,first_name,last_name,customer_id,code,phone_number,address {\n      address\n      province\n      district\n      ward\n      province_id\n      district_id\n      ward_id\n    },company, birthday\n  }\n}","variables":null}';
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $headers = [];
  $headers[] = 'Content-Type: application/json';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);


  $data = json_decode($result, 'array');

  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }

  curl_close($ch);

  if (!empty($data)) {
    return $data;
  }

  return FALSE;
}

/*
 * loginWithAccountKit
 */
function oms_login_with_accountkit($code, $first_name, $last_name) {
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
//  curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $query = '{"query":"mutation{\n  loginWithAccountKit(token:\"' . $code . '\",first_name:\"' . $first_name . '\",last_name:\"' . $last_name . '\"){\n    api_token,id,email,point,name,first_name,last_name,customer_id,code,phone_number,avatar,address {\n      address\n      province\n      district\n      ward\n      province_id\n      district_id\n      ward_id\n    },company, birthday\n  }\n}","variables":null}';
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $headers = [];
  $headers[] = 'Content-Type: application/json';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);

  $data = json_decode($result, 'array');


  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }

  curl_close($ch);

  if (!empty($data)) {
    return $data;
  }

  return FALSE;
}

/**
 * Redirect to custom login page after the user has been logged out.
 */
add_action('wp_logout', 'redirect_after_logout');
function redirect_after_logout() {
  $redirect_url = home_url('signin?logged_out=true');
  wp_safe_redirect($redirect_url);
  exit;
}

add_action('wp_ajax_nopriv_ajax_forgot_password', 'oms_forgot_password_with_phone');

/*
 * loginWithAccountKit
 */
function oms_forgot_password_with_phone() {
  if ($_POST['phone']) {
    $phone = $_POST['phone'];
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
//    curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $query = '{"query":"mutation{\n  forgetPasswordWithPhone(phone:\"' . $phone . '\")\n}","variables":null}';
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $headers = [];
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    $data = json_decode($result, 'array');


    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    if (!empty($data['data']['forgetPasswordWithPhone'])) {
      wp_send_json_success([
        'message' => $data['data']['forgetPasswordWithPhone'],
      ]);
    }
    else {
      wp_send_json_error([
        'message' => $data['errors'][0]['message'],
      ]);
    }
  }
  else {
    wp_send_json_error([
      'message' => 'Vui lòng nhập số điện thoại của bạn.',
    ]);
  }
}

function oms_edit_customer($password) {
  $ch = curl_init();

  if (!isset($_COOKIE["oms_user_token"])) {
    return FALSE;
  }

  curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
//  curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $query = '{"query":"mutation{\n  editCustomer(password:\"' . $password . '\",re_password:\"' . $password . '\"){\n id\n  }\n}","variables":null}';
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $headers = [];
  $headers[] = 'Content-Type: application/json';
  $headers[] = 'pharmacity-user-api-token: ' . $_COOKIE["oms_user_token"];
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);

  $data = json_decode($result, 'array');


  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }
  curl_close($ch);


  if (!empty($data)) {
    return $data;
  }

  return FALSE;
}


function get_oms_order_by_token() {

  $total = count_oms_order_by_token();
  $page_total = ceil($total/256);

  $data_final = [];

  //$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjc5OTEyLCJpYXQiOjE1NDUwMjgwNzgsImV4cCI6MTU3NjU2NDA3OCwiaXNzIjoiaHR0cHM6XC9cL2VhcGkucGhhcm1hY2l0eS52blwvZ3JhcGhxbCIsImp0aSI6IjVjODk3Y2Y5OGJjNWY0ZjcyMzM1MmEzYzZmYjBjN2ZkIiwic291cmNlIjoiV2Vic2l0ZSJ9.cH5-modWSmd7SzMrAUNagGOE_uLtCUeMnrw5GeRb638';
  $token = $_COOKIE['oms_user_token'];

  for ($i = 1; $i <= $page_total; $i++) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
    //curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $query = '{ "query": "query GetCustomerOnlineOrders($limit: Int, $page: Int, $id: Int) { order(limit: $limit, page: $page, id: $id){ order_number, name, phone, created_at, payment_status,paymentMethod {name, id, db_id}, status, title, address, district, ward, province,point_earned, cancel_reason, note, total_price, total_item_price, total_discount,discount_code, discount_amount, shipping_price , store{name, address, district, ward, province} , orderItems{price ,quantity, discount, productVariant{unit, sku } }}}", "variables": { "page": '.$i.' } }';

    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $headers = [];
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'pharmacity-user-api-token: ' . $token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }

    $data = json_decode($result, 'array');

    if (!empty($data)) {
      $data = $data['data']['order'];
      $data_final = array_merge($data_final,$data);
    }
    curl_close($ch);
  }

  return $data_final;
}

function count_oms_order_by_token() {

  $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjc5OTEyLCJpYXQiOjE1NDUwMjgwNzgsImV4cCI6MTU3NjU2NDA3OCwiaXNzIjoiaHR0cHM6XC9cL2VhcGkucGhhcm1hY2l0eS52blwvZ3JhcGhxbCIsImp0aSI6IjVjODk3Y2Y5OGJjNWY0ZjcyMzM1MmEzYzZmYjBjN2ZkIiwic291cmNlIjoiV2Vic2l0ZSJ9.cH5-modWSmd7SzMrAUNagGOE_uLtCUeMnrw5GeRb638';
  //  $token = $_COOKIE['oms_user_token'];
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
//  curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $query = '{ "query": "query CountCustomerOnlineOrder { countOrder { count } }", "variables": {} }';
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $headers = [];
  $headers[] = 'Content-Type: application/json';
  $headers[] = 'pharmacity-user-api-token: ' . $token;
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);


  if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
  }

  $data = json_decode($result, 'array');

  curl_close($ch);

  if (!empty($data)) {
    $count = $data['data']['countOrder']['count'];
    return $count;
  }

  return 0;
}

add_action('woocommerce_thankyou', ['Wc_class', 'my_uber_function']);

class Wc_class {


  public static function my_uber_function($order_id) {


    //    $order_id = 26115;

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
      // Displaying this data (to check)
      $total_origin_price += $product->get_regular_price()*$item_quantity;

      $product = $item_data->get_product();
      $item_quantity = $item_data->get_quantity(); // Get the item quantity

      $order_items_oms[] = '{
         "sku":"' . trim($product->get_sku()) . '",
         "unit":"' . ucfirst( get_post_meta($product->id, 'unit', TRUE)) . '",
         "price":' . $product->get_regular_price() . ',
         "quantity":' . $item_quantity . '
      }';

//      $order_items_oms[] = '{
//         "sku":"P00001",
//         "unit":"Hop",
//         "price":' . $product->get_price() . ',
//         "quantity":' . $item_quantity . '
//      }';

    }
    switch ($order->get_shipping_total()) {
      case 70000:
            $oder_items[] = '{
             "product_id":"P09713",
             "unit":"Lan",
             "price":"' . $order->get_shipping_total() . '",
             "quantity":"1",
             "discount_id":"",
             "discount":"",
             "total_price":"' . $order->get_shipping_total() . '"
          }';
        break;
      case 80000:
          $oder_items[] = '{
               "product_id":"P09714",
               "unit":"Lan",
               "price":"' . $order->get_shipping_total() . '",
               "quantity":"1",
               "discount_id":"",
               "discount":"",
               "total_price":"' . $order->get_shipping_total() . '"
            }';
          break;
      case 50000:
        $oder_items[] = '{
               "product_id":"P09712",
               "unit":"Lan",
               "price":"' . $order->get_shipping_total() . '",
               "quantity":"1",
               "discount_id":"",
               "discount":"",
               "total_price":"' . $order->get_shipping_total() . '"
            }';
        break;
      case 33000:
        $oder_items[] = '{
               "product_id":"P09711",
               "unit":"Lan",
               "price":"' . $order->get_shipping_total() . '",
               "quantity":"1",
               "discount_id":"",
               "discount":"",
               "total_price":"' . $order->get_shipping_total() . '"
            }';
        break;
      default:
        break;

    }

    $address = $order->get_billing_address_1();
    if($order->get_shipping_address_1() != ''){
      $address = $order->get_shipping_address_1();
    }

    $province = vn_shipping()->get_name_city($order->get_shipping_state());
    if($order->get_shipping_state() != ''){
      $province = vn_shipping()->get_name_city($order->get_shipping_state());
    }

    $district =vn_shipping()->get_name_district($order->get_billing_city());
    if($order->get_shipping_city() != ''){
      $district = vn_shipping()->get_name_district($order->get_shipping_city());
    }

    $ward = vn_shipping()->get_name_village($order->get_billing_address_2());
    if($order->get_shipping_address_2() != ''){
      $ward = vn_shipping()->get_name_village($order->get_shipping_address_2());
    }

    $order = wc_get_order($order_id);

// Iterating through order shipping items
    foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
      // Get the data in an unprotected array
      $shipping_item_data = $shipping_item_obj->get_data();

    }

    $fulfillment_type = 0;
    $store_code_ax = 'ECO';
    $store_code_oms = '';
    if($shipping_item_data['method_id'] == 'local_pickup_plus'){
    $fulfillment_type = 1;

    $store_code_ax = get_post_meta( json_decode(json_encode( $shipping_item_data['meta_data'][0]),true)['value'], 'code', true );
    $store_code_oms = get_post_meta( json_decode(json_encode( $shipping_item_data['meta_data'][0]),true)['value'], 'code', true );

    }



      $point = 0;

      $point_amount = 0;

      if($order->get_meta( '_wc_deposits_deposit_amount' , true )){
          $point = $order->get_meta( '_wc_deposits_deposit_amount' , true );
          $point_amount = $order->get_meta( '_wc_deposits_deposit_amount' , true );

          update_user_meta($order->get_user_id(), 'reward_point',get_field('reward_point', 'user_' . $order->get_user_id())-$point);

      }


    unset($_SESSION['deposit_enabled']);

    $transaction_id =  $order->get_transaction_id();

    if($order->get_status() == 'failed'){
          $payment_status = 'fail';
    }
    else if ($order->has_status( 'processing' ) && !empty($transaction_id)){
          $payment_status = "paid";
    }
    else{
          $payment_status = "pending";
    }
      $invoice_type = '';
      $invoice_company = '';
      $invoice_address = '';
      $invoice_company_tax_code = '';
      $invoice_note = '';
      $invoice_email = '';
      $extra_query_invoice_ax = '';
      if(get_post_meta( $order_id, 'get_vat', true )){
          $invoice_type = 0;
          $invoice_company = str_replace('"','',get_post_meta( $order_id, 'billing_vat_company', true ));
          $invoice_address = str_replace('"','',get_post_meta( $order_id, 'billing_vat_address', true ));
          $invoice_email = str_replace('"','',get_post_meta( $order_id, 'billing_vat_email', true ));
          if($invoice_email == ''){
              $invoice_email = $order->get_billing_email();
          }
          $invoice_name = '';
          $invoice_company_tax_code = str_replace(array('"',' '),array('',''),trim(get_post_meta( $order_id, 'billing_vat_mst', true )));
          $invoice_note = str_replace('"','',get_post_meta( $order_id, 'billing_vat_note', true ));
          $extra_query_invoice_ax = ',"invoice_type":'.$invoice_type.',"invoice_company":"'.$invoice_company.'","invoice_company_tax_code":"'.$invoice_company_tax_code.'","invoice_name":"'.$invoice_name.'","invoice_address":"'.$invoice_address.'","invoice_email":"'.$invoice_email.'","invoice_note":"'.$invoice_note.'"';
      }

    $place_order_body = '{
     "customer_id":"'.get_field('customer_id', 'user_' . $order->get_user_id()) .'",
     "loyaltyCard":"'. get_field('code', 'user_' . $order->get_user_id()).'",
     "address":"' . $address . '",
     "province":"' . $province . '",
     "district":"' . $district . '",
     "ward":"'. $ward .'",
     "first_name":"' . $order->get_billing_first_name() . '",
     "last_name":"' . $order->get_billing_last_name() . '",
     "phone":"' . str_replace(array(' ','.'),array('',''),$order->get_billing_phone()) . '",
     "email":"' . $order->get_billing_email() . '",
     "company":"' . $order->get_shipping_company() . '",
     "title":"",
     "longitude":"",
     "latitude":"",
     "created_at":"' . date_format($order->get_date_created(), "Y-m-d H:i:s") . '",
     "order_number":"' . change_woocommerce_order_number($order_id) . '",
     "payment_status":"'.$payment_status.'",
     "payment_method": '.(isset($transaction_id) && !empty($transaction_id)?"\"CK\"":"\"COD\"").',
     "payment_online_transaction_id":"' . $transaction_id . '",
     "fulfillment_type":"'.$fulfillment_type.'",
     "point_used":'.$point.',
     "point_amount":'.$point_amount.',
     "store_id":"'.$store_code_ax.'",
     "note":"' .trim(str_replace(array("\r","\n"), array(" "," "),preg_replace('/\s*\([^)]*\)/', '', $order->get_customer_note()))) . '",
     "total_item_price":"' . $total_origin_price . '",
     "total_discount":"' . ($total_origin_price + $order->get_shipping_total() - $order->get_total()) . '",
     "total_price":"' . $order->get_total() . '",
     "warning_message":"",
     "transfer_order":"0",
     "expected_delivery_time":"",
     "source":"Website",
     "employee_id":"",
     "employee_store_id":"",
     "order_item":[
         ' . implode(",", $oder_items) . '
     ]'.$extra_query_invoice_ax.'

  }';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'http://testapi.pharmacity.vn/api/Ecomerce/order');
//    curl_setopt($ch, CURLOPT_URL, 'https://sgpmcaxdev01.pharmacity.vn:78/api/Ecomerce/order');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $place_order_body);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $headers = [];
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'admin-api-key: 5SM27dC9KdKyll4g1Dk2xY9gcMgQ81cIbjbCwytxNvloWa/vg0yicRB7oiY=';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }

    $data = json_decode($result, 'array');

    curl_close($ch);

    global $wpdb;

    $description = json_encode($data,JSON_UNESCAPED_UNICODE);
    $body = json_encode($place_order_body,JSON_UNESCAPED_UNICODE);
    $sql = "INSERT INTO checkout_log (order_id, log,body,env) VALUES ({$order_id}, '{$description}','{$body}','ax')";
    $wpdb->query($sql);
    update_post_meta($order_id, 'ax_synchronize_status', 0);
    if(isset($data['service_status'])){
      update_post_meta($order_id, 'ax_synchronize_status', $data['service_status']);
    }
    update_post_meta($order_id, 'ax_synchronize_log','Resopnse: '.$description.' ,body'.$body);


    /************************sync with OMS*******************/


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
//    curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if (isset($_GET['payType']) && $_GET['payType'] == "IC"){
          $method_id = "3";
    }
    else{
          $method_id = "3";
    }

    $extra_query ="";
    if(isset($transaction_id) && !empty($transaction_id)){
          $extra_query = ',"payment_status":"Paid","payment_method":'.$method_id;
    }else if($payment_status == 'fail' && get_post_meta( $order_id, '_payment_method', true ) == 'epay'){
          $extra_query = ',"payment_status":"Fail","payment_method":'.$method_id;
    }
    $shipping_price = $order->get_shipping_total()!=''?$order->get_shipping_total():0;

    $oms_invoice_type = '';
    $oms_invoice_company = '';
    $oms_invoice_company_tax_code = '';
    $oms_invoice_note = '';
    $oms_invoice_name = $order->get_billing_last_name();
    $oms_invoice_address = $address;
    $oms_invoice_email = $order->get_billing_email();
      $extra_query_invoice = '';
    if(get_post_meta( $order_id, 'get_vat', true )  && get_post_meta( $order_id, 'billing_vat_mst', true ) != ''){
        $oms_invoice_type = 0;
        $oms_invoice_company = str_replace('"','',get_post_meta( $order_id, 'billing_vat_company', true ));
        $oms_invoice_company_tax_code = str_replace(array('"',' '),array('',''),trim(get_post_meta( $order_id, 'billing_vat_mst', true )));
        $oms_invoice_note = str_replace('"','',get_post_meta( $order_id, 'billing_vat_note', true ));
        $oms_invoice_address = str_replace('"','',get_post_meta( $order_id, 'billing_vat_address', true ));
        $oms_invoice_email = str_replace('"','',get_post_meta( $order_id, 'billing_vat_email', true ));
        $oms_invoice_name = '';
        if($oms_invoice_email == ''){
            $oms_invoice_email = $order->get_billing_email();
        }
        $extra_query_invoice = ',"invoice_type":'.$oms_invoice_type.',"invoice_company":"'.$oms_invoice_company.'","invoice_company_tax_code":"'.$oms_invoice_company_tax_code.'","invoice_name":"'.$oms_invoice_name.'","invoice_address":"'.$oms_invoice_address.'","invoice_email":"'.$oms_invoice_email.'","invoice_note":"'.$oms_invoice_note.'"';

    }

    $query = '{
      "query": "mutation SyncOrder($customer_id: String, $email: String, $first_name: String!, $last_name: String, $phone: String!, $fulfillment_type: Int, $address: String, $province: String, $district: String, $ward: String, $store_code: String, $company: String, $title: String, $note: String, $source: OrderSourceEnum, $items: [OrderItemInput!]!, $order_number: String!, $shipping_price: Int, $total_price: Int, $total_item_price: Int, $total_discount: Int, $point_used: Int, $payment_method: Int, $payment_status: OrderPaymentStatusEnum, $invoice_type: Int, $invoice_company: String, $invoice_company_tax_code: String, $invoice_name: String, $invoice_address: String, $invoice_email: String, $invoice_note: String) { syncOrder(customer_id: $customer_id, email: $email, first_name: $first_name, last_name: $last_name, phone: $phone, fulfillment_type: $fulfillment_type, address: $address, province: $province, district: $district, ward: $ward, store_code: $store_code, company: $company, title: $title, note: $note, source: $source, items: $items, order_number: $order_number, shipping_price: $shipping_price, total_price: $total_price, total_item_price: $total_item_price, total_discount: $total_discount, point_used: $point_used, payment_method: $payment_method, payment_status: $payment_status, invoice_type: $invoice_type, invoice_company: $invoice_company, invoice_company_tax_code: $invoice_company_tax_code, invoice_name: $invoice_name, invoice_address: $invoice_address, invoice_email: $invoice_email, invoice_note: $invoice_note) { id, db_id, order_number, total_price } }",
      "variables": {
          "customer_id": "'.get_field('customer_id', 'user_' . $order->get_user_id()) .'",
          "first_name":  "'.$order->get_billing_first_name().' '.$order->get_billing_last_name().'",
          "phone": "'.str_replace(array(' ','.'),array('',''),$order->get_billing_phone()).'",
          "address": "'.$address.'",
          "province":"'.$province.'",
          "district": "'.$district.'",
          "fulfillment_type":"'.$fulfillment_type.'",
          "note":"' . trim(str_replace(array("\r","\n"), array(" "," "), preg_replace('/\s*\([^)]*\)/', '',$order->get_customer_note()))) . '",
          "ward": "'.$ward.'",
          "point_used":'.$point.',
          "store_code":"'.$store_code_oms.'",
          "order_number": "'.change_woocommerce_order_number($order_id).'",
          "items": [
              ' . implode(",", $order_items_oms) . '
          ],
          "shipping_price": '.$shipping_price.',
          "total_price": '.($order->get_total() - $point).',
          "total_item_price": '.$total_origin_price.',
          "total_discount": '.($total_origin_price + $order->get_shipping_total() - $order->get_total()) .
            $extra_query_invoice.$extra_query.'
      }
  }';
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $headers = [];
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'pharmacity-partner-api-token: FizjGypAs5PlmReYq9dEQ0RhT21NM9kcgh5fGuP6fVOT9Ras';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $data = json_decode($result, 'array');


    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);
    $description = json_encode($data,JSON_UNESCAPED_UNICODE);
    $body = json_encode($query,JSON_UNESCAPED_UNICODE);

    $sql = "INSERT INTO checkout_log (order_id, log,body,env) VALUES ({$order_id}, '{$description}','{$body}','oms')";
    $wpdb->query($sql);

    update_post_meta($order_id, 'oms_synchronize_status', 0);
    if(isset($data['data']) && isset($data['data']['syncOrder'])){
      update_post_meta($order_id, 'oms_synchronize_status', 1);
    }
    update_post_meta($order_id, 'oms_synchronize_log','Resopnse: '.$description.' \n,body'.$body);

//      update_user_points($point,$order->get_user_id());

  }

}

function update_user_points($point,$user_id){

    $phoen_update_data  =  $point ;

    $phoen_current_dates_update = new DateTime();

    $phoen_current_dates_updates = $phoen_current_dates_update->format('d-m-Y H:i:s');

    update_post_meta( $user_id, 'phoes_customer_points_update_valss', $phoen_update_data );

    update_option('phoeni_update_dates',$phoen_current_dates_updates);

    update_post_meta($user_id,'phoeni_update_dates_checkeds',$phoen_current_dates_updates);

    update_post_meta($user_id, 'phoes_customer_points_update_valss_empty', $phoen_update_data );

    update_user_meta($user_id, 'phoen_update_customer_points', $phoen_update_data);

    update_user_meta( $user_id, 'phoen_update_date', $phoen_current_dates_updates);

    update_user_meta($user_id,'phoen_update_customer_hiden_val',$phoen_update_data);

}
function cspd_call_after_for_submit($contact_data) {

    $wpcf = WPCF7_ContactForm::get_current();
    $filename = $_FILES['file-962']['name'];
    $filedata = $_FILES['file-962']['tmp_name'];
    $filesize = $_FILES['file-962']['size'];
    $filename = time().'_'.$filename;
    if($filesize > 0){
        $form_to_DB = WPCF7_Submission::get_instance();
        if ($form_to_DB) {
            $formData = $form_to_DB->get_posted_data(); // Get all data from the posted form
            $uploaded_files = $form_to_DB->uploaded_files(); // this allows you access to the upload file in the temp location
        }

        $upload_folder = $_SERVER["DOCUMENT_ROOT"].'/wp-content/uploads/prescription';
        if (!file_exists($upload_folder)) {
            mkdir($upload_folder, 0777, true);
        }

        copy($uploaded_files['file-962'],$upload_folder.'/'.$filename);
        $headers = array("Content-Type:multipart/form-data");
        $file = curl_file_create($upload_folder.'/'.$filename);
        if ( is_user_logged_in() ){
          $user =  wp_get_current_user();
          $customer_name =  $user->display_name;
        }
        else{
          $customer_name =  'Khach Hang';
        }
        $postfields =  array(
            'query'=>'mutation PlacePrescriptionOrder($first_name: String!, $last_name: String, $phone: String!, $note: String, $source: OrderSourceEnum, $customer_id: Int, $employee_store_id: Int, $type: Int, $cancel_reason: String, $receipt_id: String, $not_prescription: Boolean) { placePrescriptionOrder(first_name: $first_name, last_name: $last_name, phone: $phone, note: $note, source: $source, customer_id: $customer_id, employee_store_id: $employee_store_id, type: $type, cancel_reason: $cancel_reason, receipt_id: $receipt_id, not_prescription: $not_prescription) { id, db_id, order_number } }',
            'variables[first_name]' => $customer_name,
            'variables[last_name]' => '',
            'variables[phone]' => $_POST['tel-226'],
            'variables[source]' => 'Website',
            'prescription' => $file
        );

        if ( is_user_logged_in() ){
            $user =  wp_get_current_user();
            $postfields['customer_id'] = get_field('customer_id', 'user_' . $user->ID);

        }
        $ch = curl_init('https://eapi.pharmacity.vn/graphql');
//        $ch = curl_init('https://omsapi.pharmacity.vn/graphql');

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Cấu hình sử dụng method POST
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

        // Thiết lập có gửi file và thông tin file
        curl_setopt($ch, CURLOPT_INFILESIZE, $filesize);

        // Cấu hình return
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Thực thi
        $result =  curl_exec($ch);

        // Nếu không tồn tại lỗi nào trong CURL
        if(!curl_errno($ch))
        {
            $info = curl_getinfo($ch);
            if ($info['http_code'] == 200){

            }
        }
        else
        {
            echo curl_error($ch);
        }

        // Đóng CURL
        curl_close($ch);

        $postcode_id = wp_insert_post(array('post_title' => $customer_name .' - '. $_POST['tel-226'], 'post_type' => 'prescription_history', 'post_status' => 'publish'));
        if ($postcode_id) {
            update_post_meta($postcode_id, 'name', $customer_name);
            update_post_meta($postcode_id, 'phone', $_POST['tel-226']);
            update_post_meta($postcode_id, 'file', home_url().'/wp-content/uploads/prescription/'.$filename);
            update_post_meta($postcode_id, 'log', json_encode($result,JSON_UNESCAPED_UNICODE));
        }
    }


}

add_action('wpcf7_before_send_mail', 'cspd_call_after_for_submit');


function addTagsBySku(){
  $sku_array  = ["P08207", "P01559", "P10883", "P03840", "P03547", "P10679", "P03910", "P12977", "P11400", "P03879", "P05295", "P01566", "P10882", "P03245", "P05329", "P03694", "P10970", "P03732", "P08178", "P03655", "P05290", "P10415", "P12976", "P02542", "P03377", "P03823", "P07984", "P04372", "P09524", "P09121", "P03728", "P03870", "P03874", "P11314", "P07076", "P04590", "P06300", "P00570", "P03237", "P12932", "P01567", "P07595", "P03648", "P08642", "P11316", "P07391", "P03548", "P10436", "P03722", "P03722", "P09161", "P11552", "P12978", "P10835", "P03606", "P03379", "P03558", "P08647", "P10118", "P03244", "P06297", "P03352", "P10971", "P03235", "P08209", "P03233", "P07177", "P11313", "P11553", "P03663", "P09754", "P01434", "P03333", "P05258", "P09527", "P05570", "P04381", "P06920", "P03670", "P07075", "P08788", "P03645", "P03353", "P07074", "P09420", "P09528", "P12993", "P03642", "P07606", "P11405", "P12016", "P03295", "P10879", "P03640", "P03869", "P07477", "P07289", "P07192", "P10972", "P08648", "P11317", "P12979", "P09533", "P10491", "P03729", "P09173", "P03839", "P09560", "P09159", "P09422", "P05201", "P09421", "P09526", "P07528", "P07392", "P03646", "P09640", "P11401", "P11499", "P09751", "P11399", "P11402", "P09531", "P03759", "P10329", "P03713", "P09525", "P12997", "P07073", "P11398", "P11404", "P08210", "P03734", "P03665", "P05260", "P08790", "P09530", "P03641", "P01436", "P01452", "P03549", "P05866", "P09423", "P12980", "P05891", "P10890", "P07854", "P09168", "P10887", "P11403", "P11531", "P10337", "P13097", "P10888", "P10889", "P13101", "P03842", "P03876", "P10907", "P05169", "P05291", "P12934", "P07175", "P12938", "P03837", "P09499", "P11500", "P03638", "P13100", "P12998", "P03378", "P09497", "P11498", "P12940", "P09532", "P03871", "P04871", "P09529", "P08211", "P12935", "P07597", "P08012", "P12937", "P08015", "P01448", "P03644", "P11315", "P03380", "P03699", "P03773", "P07176", "P11497", "P11543", "P13099", "P10328", "P12936", "P10427", "P12941", "P03700", "P12981", "P13096", "P10428", "P07194", "P09222", "P13102", "P05121", "P08014", "P07180", "P07579", "P11533", "P12939", "P12942", "P07596", "P08022", "P10492", "P10494", "P12999", "P11318", "P11320", "P13095", "P10493", "P08020", "P09626", "P13098", "P05292", "P05203", "P07578", "P10910", "P11484", "P05289", "P10429", "P03643", "P05296", "P09625", "P03880", "P05117", "P05162", "P03581", "P03585", "P05569", "P08017", "P10837", "P03583", "P03586", "P03587", "P03588", "P03589", "P03590", "P03591", "P03592", "P03594", "P03602", "P03603", "P03604", "P03605", "P04676", "P05328", "P05567", "P05568", "P07690", "P08021", "P09627", "P09628", "P10838", "P10839", "P03297", "P11587", "P10692", "P10917", "P11830", "P11829", "P12995", "P11586", "P07302", "P08789", "P10145", "P12973", "P08302", "P09485", "P10915", "P10564", "P10891", "P08303", "P12974", "P10916", "P11828", "P12022", "P12020", "P10691", "P10918", "P04864", "P10848", "P04369", "P03349", "P12021", "P12943", "P10147", "P10849", "P11874", "P07607", "P11875", "P10892", "P03836", "P08208", "P09500", "P11585", "P10857", "P03304", "P10426", "P10401", "P10695", "P03355", "P03719", "P03719", "P06435", "P10912", "P03848", "P10139", "P10856", "P10858", "P11876", "P13110", "P07195", "P10400", "P12878", "P12975", "P13008", "P03299", "P03654", "P07593", "P07860", "P09520", "P07530", "P07981", "P09172", "P10399", "P10880", "P03650", "P03657", "P03760", "P03847", "P09226", "P09519", "P10694", "P10881", "P03301", "P03309", "P07885", "P10398", "P10563", "P10565", "P10621", "P10909", "P11877", "P03714", "P03865", "P04368", "P10146", "P10364", "P10698", "P10854", "P10878", "P10973", "P03838", "P07580", "P09162", "P09224", "P09431", "P09726", "P10140", "P10389", "P12018", "P03303", "P05576", "P09215", "P09223", "P10362", "P10490", "P10697", "P10699", "P10855", "P10924", "P11834", "P01462", "P03302", "P03821", "P03867", "P04200", "P04364", "P07193", "P07393", "P07441", "P07445", "P08019", "P08345", "P08893", "P09753", "P10547", "P10925", "P01464", "P01466", "P01467", "P01468", "P01564", "P01565", "P03345", "P04199", "P05574", "P08243", "P08892", "P09140", "P09141", "P09142", "P09164", "P09553", "P10388", "P10551", "P10574", "P10759", "P10760", "P11319", "P11869", "P11871", "P01465", "P02420", "P03077", "P03298", "P03300", "P03311", "P03312", "P03314", "P03334", "P03381", "P03575", "P03577", "P03661", "P03693", "P03716", "P03736", "P03863", "P03873", "P03878", "P04378", "P05123", "P05202", "P05801", "P06298", "P06299", "P06413", "P07045", "P07050", "P07051", "P07052", "P07190", "P07191", "P07390", "P07442", "P07443", "P07444", "P07464", "P07465", "P07466", "P07529", "P07577", "P07608", "P07684", "P07685", "P07686", "P07781", "P07855", "P07856", "P07884", "P08013", "P08016", "P08023", "P08025", "P08026", "P08027", "P08028", "P08029", "P08643", "P08891", "P09139", "P09160", "P09165", "P09225", "P09273", "P09332", "P09333", "P09430", "P09750", "P09752", "P10363", "P10488", "P10489", "P10531", "P10550", "P10700", "P10908", "P11836", "P11870", "P11872"];
  global $wpdb;

  foreach ($sku_array as $sku){
    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
    $tag = [ 'không đâu rẻ hơn' ];
    wp_update_post( array(
      'ID' => $product_id,
      'post_status' => 'publish',
    ) );
    wp_set_object_terms( $product_id, $tag, 'product_tag' );


//    $data = get_post_meta($product_id,'wc_productdata_options', true );
//      if($data == ''){
//        $data = array();
//      }
//    $data[0]['_bubble_new'] = '"yes"';
//    $data[0]['_bubble_text'] = 'test';
//    update_post_meta($product_id, 'wc_productdata_options', $data);
  }
}

add_filter( 'woocommerce_order_number', 'change_woocommerce_order_number' );

function change_woocommerce_order_number( $order_id ) {


  $new_order_id =  $order_id + 20000000;

  return $new_order_id;
}

if (!wp_next_scheduled('oms_recall_order_hook')) {
  wp_schedule_event(time(), 'recall_order', 'oms_recall_order_hook');
}

add_action('oms_recall_order_hook', 'oms_recall_order', 10, 0);

function oms_recall_order() {
  $args = array(
      'limit'       => -1,
      'status' => array('processing','pending-approve','pending','failed','approved'),
  );
  $args['meta_query'] = [
      'relation' => 'AND',
      [
          'key' => 'ax_synchronize_status',
          'value' => 1,
          'compare' => '!=',
      ],
  ];
  $args['meta_query'] = [
      'relation' => 'OR',
      [
          'key' => 'oms_synchronize_status',
          'value' => 1,
          'compare' => '!=',
      ],
  ];

  $orders = wc_get_orders( $args );
  if(count($orders)){


    global $wpdb;
    foreach($orders as $key => $order){
        if(get_post_meta( $order->ID, '_payment_method', true )=='epay' && $order->get_status()=='pending'){
            $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($order->get_date_created());
            $hours = floor($seconds / 3600);
            $mins = floor(($seconds - ($hours*3600)) / 60);

            if($mins >= 5){
                $order->update_status('cancelled', 'order_note');
                email_cancel_order($order->ID);
            }

        }
        else{
            $transaction_id =  $order->get_transaction_id();
            $order_id = $order->ID;
            $oder_items = array();
            $order_items_oms = array();

            $total_origin_price = 0;
            foreach ($order->get_items() as $key => $item_data) {
                $product = $item_data->get_product();
                $item_quantity = $item_data->get_quantity(); // Get the item quantity


                $total_discount = ($product->get_regular_price() - $product->get_sale_price())*$item_quantity;
                $item_total = $item_data->get_total(); // Get the item line total
                $total_discount = 0;
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
                                 "discount":"'.$total_discount.'",
                                 "total_price":"' . $item_total . '"
                              }';
                // Displaying this data (to check)
                $total_origin_price += $product->get_regular_price()*$item_quantity;

                $product = $item_data->get_product();
                $item_quantity = $item_data->get_quantity(); // Get the item quantity

                $order_items_oms[] = '{
                                         "sku":"' . trim($product->get_sku()) . '",
                                         "unit":"' . ucfirst( get_post_meta($product->id, 'unit', TRUE)) . '",
                                         "price":' . $product->get_regular_price() . ',
                                         "quantity":' . $item_quantity . '
                                      }';

            }
            switch ($order->get_shipping_total()) {
                case 70000:
                    $oder_items[] = '{
                                     "product_id":"P09713",
                                     "unit":"Lan",
                                     "price":"' . $order->get_shipping_total() . '",
                                     "quantity":"1",
                                     "discount_id":"",
                                     "discount":"",
                                     "total_price":"' . $order->get_shipping_total() . '"
                                  }';
                    break;
                case 80000:
                    $oder_items[] = '{
                                       "product_id":"P09714",
                                       "unit":"Lan",
                                       "price":"' . $order->get_shipping_total() . '",
                                       "quantity":"1",
                                       "discount_id":"",
                                       "discount":"",
                                       "total_price":"' . $order->get_shipping_total() . '"
                                    }';
                    break;
                case 50000:
                    $oder_items[] = '{
                                       "product_id":"P09712",
                                       "unit":"Lan",
                                       "price":"' . $order->get_shipping_total() . '",
                                       "quantity":"1",
                                       "discount_id":"",
                                       "discount":"",
                                       "total_price":"' . $order->get_shipping_total() . '"
                                    }';
                    break;
                case 33000:
                    $oder_items[] = '{
                                       "product_id":"P09711",
                                       "unit":"Lan",
                                       "price":"' . $order->get_shipping_total() . '",
                                       "quantity":"1",
                                       "discount_id":"",
                                       "discount":"",
                                       "total_price":"' . $order->get_shipping_total() . '"
                                    }';
                    break;
                default:
                    break;

            }

            $point = 0;
            $point_amount = 0;
            if($order->get_meta( '_wc_deposits_deposit_amount' , true )){
                $point = $order->get_meta( '_wc_deposits_deposit_amount' , true );
                $point_amount = $order->get_meta( '_wc_deposits_deposit_amount' , true );
                update_user_meta($order->get_user_id(), 'reward_point',get_field('reward_point', 'user_' . $order->get_user_id())-$point);
            }


            $address = $order->get_billing_address_1();
            if($order->get_shipping_address_1() != ''){
                $address = $order->get_shipping_address_1();
            }

            $province = vn_shipping()->get_name_city($order->get_shipping_state());
            if($order->get_shipping_state() != ''){
                $province = vn_shipping()->get_name_city($order->get_shipping_state());
            }

            $district =vn_shipping()->get_name_district($order->get_billing_city());
            if($order->get_shipping_city() != ''){
                $district = vn_shipping()->get_name_district($order->get_shipping_city());
            }

            $ward = vn_shipping()->get_name_village($order->get_billing_address_2());
            if($order->get_shipping_address_2() != ''){
                $ward = vn_shipping()->get_name_village($order->get_shipping_address_2());
            }

// Iterating through order shipping items
            foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
                // Get the data in an unprotected array
                $shipping_item_data = $shipping_item_obj->get_data();


            }


            $fulfillment_type = 0;
            $store_code_ax = 'ECO';
            $store_code_oms = '';
            if($shipping_item_data['method_id'] == 'local_pickup_plus'){
                $fulfillment_type = 1;

                $store_code_ax = get_post_meta( json_decode(json_encode( $shipping_item_data['meta_data'][0]),true)['value'], 'code', true );
                $store_code_oms = get_post_meta( json_decode(json_encode( $shipping_item_data['meta_data'][0]),true)['value'], 'code', true );

            }

            if($order->get_status() == 'failed'){
                $payment_status = 'fail';
            }
            else if ($order->has_status( 'processing' ) && !empty($transaction_id)){
                $payment_status = "paid";
            }
            else{
                $payment_status = "pending";
            }

            /************************sync with OMS*******************/

            if(get_post_meta($order->ID,'oms_synchronize_status',true) != 1){

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
//                curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


                if (isset($_GET['payType']) && $_GET['payType'] == "IC"){
                    $method_id = "3";
                }
                else{
                    $method_id = "3";
                }


                $extra_query ="";
                if(isset($transaction_id) && !empty($transaction_id)){
                    $extra_query = ',"payment_status":"Paid","payment_method":'.$method_id;
                }else if($payment_status == 'fail' && get_post_meta( $order_id, '_payment_method', true ) == 'epay'){
                    $extra_query = ',"payment_status":"Fail","payment_method":'.$method_id;
                }


                $shipping_price = $order->get_shipping_total()!=''?$order->get_shipping_total():0;

                $oms_invoice_type = '';
                $oms_invoice_company = '';
                $oms_invoice_company_tax_code = '';
                $oms_invoice_note = '';
                $oms_invoice_name = $order->get_billing_last_name();
                $oms_invoice_address = $address;
                $oms_invoice_email = $order->get_billing_email();

                $extra_query_invoice ="";

                if(get_post_meta( $order_id, 'get_vat', true ) && get_post_meta( $order_id, 'billing_vat_mst', true ) != ''){
                    $oms_invoice_type = 0;
                    $oms_invoice_company = str_replace('"','',get_post_meta( $order_id, 'billing_vat_company', true ));
                    $oms_invoice_company_tax_code = str_replace(array('"',' '),array('',''),trim(get_post_meta( $order_id, 'billing_vat_mst', true )));
                    $oms_invoice_note = str_replace('"','',get_post_meta( $order_id, 'billing_vat_note', true ));
                    $oms_invoice_name = '';
                    $oms_invoice_address = str_replace('"','',get_post_meta( $order_id, 'billing_vat_address', true ));
                    $oms_invoice_email = str_replace('"','',get_post_meta( $order_id, 'billing_vat_email', true ));
                    if($oms_invoice_email == ''){
                        $oms_invoice_email = $order->get_billing_email();
                    }
                    $extra_query_invoice = ',"invoice_type":'.$oms_invoice_type.',"invoice_company":"'.$oms_invoice_company.'","invoice_company_tax_code":"'.$oms_invoice_company_tax_code.'","invoice_name":"'.$oms_invoice_name.'","invoice_address":"'.$oms_invoice_address.'","invoice_email":"'.$oms_invoice_email.'","invoice_note":"'.$oms_invoice_note.'"';
                }

                $query = '{
                              "query": "mutation SyncOrder($customer_id: String, $email: String, $first_name: String!, $last_name: String, $phone: String!, $fulfillment_type: Int, $address: String, $province: String, $district: String, $ward: String, $store_code: String, $company: String, $title: String, $note: String, $source: OrderSourceEnum, $items: [OrderItemInput!]!, $order_number: String!, $shipping_price: Int, $total_price: Int, $total_item_price: Int, $total_discount: Int, $point_used: Int, $payment_method: Int, $payment_status: OrderPaymentStatusEnum, $invoice_type: Int, $invoice_company: String, $invoice_company_tax_code: String, $invoice_name: String, $invoice_address: String, $invoice_email: String, $invoice_note: String) { syncOrder(customer_id: $customer_id, email: $email, first_name: $first_name, last_name: $last_name, phone: $phone, fulfillment_type: $fulfillment_type, address: $address, province: $province, district: $district, ward: $ward, store_code: $store_code, company: $company, title: $title, note: $note, source: $source, items: $items, order_number: $order_number, shipping_price: $shipping_price, total_price: $total_price, total_item_price: $total_item_price, total_discount: $total_discount, point_used: $point_used, payment_method: $payment_method, payment_status: $payment_status, invoice_type: $invoice_type, invoice_company: $invoice_company, invoice_company_tax_code: $invoice_company_tax_code, invoice_name: $invoice_name, invoice_address: $invoice_address, invoice_email: $invoice_email, invoice_note: $invoice_note) { id, db_id, order_number, total_price } }",
                              "variables": {
                                  "customer_id": "'.get_field('customer_id', 'user_' . $order->get_user_id()) .'",
                                  "first_name":  "'.$order->get_billing_first_name().' '.$order->get_billing_last_name().'",
                                  "phone": "'.str_replace(array(' ','.'),array('',''),$order->get_billing_phone()).'",
                                  "address": "'.$address.'",
                                  "province":"'.$province.'",
                                  "district": "'.$district.'",
                                  "fulfillment_type":"'.$fulfillment_type.'",
                                  "note":"' . trim(str_replace(array("\r","\n"), array(" "," "), preg_replace('/\s*\([^)]*\)/', '',$order->get_customer_note()))) . '",
                                  "ward": "'.$ward.'",
                                  "point_used":'.$point.',
                                  "store_code":"'.$store_code_oms.'",
                                  "order_number": "'.change_woocommerce_order_number($order_id).'",
                                  "items": [
                                      ' . implode(",", $order_items_oms) . '
                                  ],
                                  "shipping_price": '.$shipping_price.',
                                  "total_price": '.($order->get_total() - $point).',
                                  "total_item_price": '.$total_origin_price.',
                                  "total_discount": '.($total_origin_price + $order->get_shipping_total() - $order->get_total())
                                  .$extra_query_invoice.$extra_query.'
                              }
                          }';


                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $headers = [];
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'pharmacity-partner-api-token: FizjGypAs5PlmReYq9dEQ0RhT21NM9kcgh5fGuP6fVOT9Ras';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $data = json_decode($result, 'array');


                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }

                curl_close($ch);
                $description = json_encode($data,JSON_UNESCAPED_UNICODE);
                $body = json_encode($query,JSON_UNESCAPED_UNICODE);
//        $sql = "INSERT INTO checkout_log (order_id, log,body) VALUES ({$order_id}, '{$description}','{$body}')";
//        $wpdb->query($sql);

                $check_oms_log = $wpdb->get_results("SELECT * FROM checkout_log WHERE env = 'oms' AND order_id = ".$order_id, ARRAY_A);

                if ($check_oms_log) {
                    $oms_log = $check_oms_log[0]['log'];
                    if(isset(json_decode($oms_log, 'array')['data']) && (isset(json_decode($oms_log, 'array')['data']['syncOrder']) && json_decode($oms_log, 'array')['data']['syncOrder'] > 0)){
                        update_post_meta($order_id, 'oms_synchronize_status', 1);
                        update_post_meta($order_id, 'oms_synchronize_log','Resopnse: '.$oms_log.' \n,body'.$body);
                    }
                    else{
                        update_post_meta($order_id, 'oms_synchronize_status', 0);
                        if(isset($data['data']) && (isset($data['data']['syncOrder']) && $data['data']['syncOrder'])){
                            update_post_meta($order_id, 'oms_synchronize_status', 1);
                        }
                        update_post_meta($order_id, 'oms_synchronize_log','Resopnse: '.$description.' \n,body'.$body);
                    }
                }
                else{
                    update_post_meta($order_id, 'oms_synchronize_status', 0);
                    if(isset($data['data']) && (isset($data['data']['syncOrder']) && $data['data']['syncOrder'])){
                        update_post_meta($order_id, 'oms_synchronize_status', 1);
                    }
                    update_post_meta($order_id, 'oms_synchronize_log','Resopnse: '.$description.' \n,body'.$body);
                }

            }

            if(get_post_meta($order->ID,'ax_synchronize_status',true) != 1){
                $invoice_type = '';
                $invoice_company = '';
                $invoice_address = '';
                $invoice_company_tax_code = '';
                $invoice_note = '';
                $invoice_email = '';
                $extra_query_invoice_ax = '';
                if(get_post_meta( $order_id, 'get_vat', true )){
                    $invoice_type = 0;
                    $invoice_company = str_replace('"','',get_post_meta( $order_id, 'billing_vat_company', true ));
                    $invoice_address = str_replace('"','',get_post_meta( $order_id, 'billing_vat_address', true ));
                    $invoice_email = str_replace('"','',get_post_meta( $order_id, 'billing_vat_email', true ));
                    $invoice_company_tax_code = str_replace(array('"',' '),array('',''),trim(get_post_meta( $order_id, 'billing_vat_mst', true )));
                    $invoice_note = str_replace('"','',get_post_meta( $order_id, 'billing_vat_note', true) );
                    $invoice_name = '';
                    if($invoice_email == ''){
                        $invoice_email = $order->get_billing_email();
                    }
                    $extra_query_invoice_ax = ',"invoice_type":'.$invoice_type.',"invoice_company":"'.$invoice_company.'","invoice_company_tax_code":"'.$invoice_company_tax_code.'","invoice_name":"'.$invoice_name.'","invoice_address":"'.$invoice_address.'","invoice_email":"'.$invoice_email.'","invoice_note":"'.$invoice_note.'"';

                }
                $place_order_body = '{
                                 "customer_id":"'.get_field('customer_id', 'user_' . $order->get_user_id()) .'",
                                 "loyaltyCard":"'. get_field('code', 'user_' . $order->get_user_id()).'",
                                 "address":"' . $address . '",
                                 "province":"' . $province . '",
                                 "district":"' . $district . '",
                                 "ward":"'. $ward .'",
                                 "first_name":"' . $order->get_billing_first_name() . '",
                                 "last_name":"' . $order->get_billing_last_name() . '",
                                 "phone":"' . str_replace(array(' ','.'),array('',''),$order->get_billing_phone()) . '",
                                 "email":"' . $order->get_billing_email() . '",
                                 "company":"' . $order->get_shipping_company() . '",
                                 "title":"",
                                 "longitude":"",
                                 "latitude":"",
                                 "created_at":"' . date_format($order->get_date_created(), "Y-m-d H:i:s") . '",
                                 "order_number":"' . change_woocommerce_order_number($order_id) . '",
                                 "payment_status":"'.$payment_status.'",
                                 "payment_method": '.(isset($transaction_id) && !empty($transaction_id)?"\"CK\"":"\"COD\"").',
                                 "payment_online_transaction_id":"' . $transaction_id . '",
                                 "fulfillment_type":"'.$fulfillment_type.'",
                                 "point_used":'.$point.',
                                 "point_amount":'.$point_amount.',
                                 "store_id":"'.$store_code_ax.'",
                                 "note":"' .trim(str_replace(array("\r","\n"), array(" "," "),preg_replace('/\s*\([^)]*\)/', '', $order->get_customer_note()))) . '",
                                 "total_item_price":"' . $total_origin_price . '",
                                 "total_discount":"' . ($total_origin_price + $order->get_shipping_total() - $order->get_total()) . '",
                                 "total_price":"' . $order->get_total() . '",
                                 "warning_message":"",
                                 "transfer_order":"0",
                                 "expected_delivery_time":"",
                                 "source":"Website",
                                 "employee_id":"",
                                 "employee_store_id":"",
                                 "order_item":[
                                     ' . implode(",", $oder_items) . '
                                 ]'.$extra_query_invoice_ax.'
                              }';

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'http://testapi.pharmacity.vn/api/Ecomerce/order');
//                curl_setopt($ch, CURLOPT_URL, 'https://sgpmcaxdev01.pharmacity.vn:78/api/Ecomerce/order');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $place_order_body);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $headers = [];
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'admin-api-key: 5SM27dC9KdKyll4g1Dk2xY9gcMgQ81cIbjbCwytxNvloWa/vg0yicRB7oiY=';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }

                $data = json_decode($result, 'array');

                curl_close($ch);

                global $wpdb;

                $description = json_encode($data,JSON_UNESCAPED_UNICODE);
                $body = json_encode($place_order_body,JSON_UNESCAPED_UNICODE);
//        $sql = "INSERT INTO checkout_log (order_id, log,body) VALUES ({$order_id}, '{$description}','{$body}')";
//        $wpdb->query($sql);

                $check_ax_log = $wpdb->get_results("SELECT * FROM checkout_log WHERE env = 'ax' AND order_id = ".$order_id, ARRAY_A);

                if ($check_ax_log) {
                    $ax_log = $check_ax_log[0]['log'];
                    if(isset(json_decode($ax_log, 'array')['service_status']) && json_decode($ax_log, 'array')['service_status']){
                        update_post_meta($order_id, 'ax_synchronize_status', 1);
                        update_post_meta($order_id, 'ax_synchronize_log','Resopnse: '.$ax_log.' \n,body'.$body);
                    }
                    else{
                        update_post_meta($order_id, 'ax_synchronize_status', 0);
                        if(isset($data['service_status'])){
                            update_post_meta($order_id, 'ax_synchronize_status', $data['service_status']);
                        }
                        update_post_meta($order_id, 'ax_synchronize_log','Resopnse: '.$description.' ,body'.$body);
                    }
                }
                else{
                    update_post_meta($order_id, 'ax_synchronize_status', 0);
                    if(isset($data['service_status'])){
                        update_post_meta($order_id, 'ax_synchronize_status', $data['service_status']);
                    }
                    update_post_meta($order_id, 'ax_synchronize_log','Resopnse: '.$description.' ,body'.$body);
                }
            }
        }

    }

  }
}

add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
  $reordered_columns = array();

  // Inserting columns to a specific location
  foreach( $columns as $key => $column){
    $reordered_columns[$key] = $column;
    if( $key ==  'order_status' ){
      // Inserting after "Status" column
      $reordered_columns['payment-method'] = __( 'Payment Method','theme_domain');
      $reordered_columns['oms-status'] = __( 'OMS Status','theme_domain');
      $reordered_columns['ax-status'] = __( 'AX status','theme_domain');
    }
  }
  return $reordered_columns;
}

add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
  switch ( $column )
  {
      case 'payment-method' :
          // Get custom post meta data
          echo '<span  style="padding-left: 10px;">'.get_post_meta( $post_id, '_payment_method', true ).'<span>';
          break;
    case 'oms-status' :
      // Get custom post meta data
      $my_var_one = get_post_meta( $post_id, 'oms_synchronize_status', true );
      if(!empty($my_var_one))
        if($my_var_one == 1){
          echo '<span class="order-status status-processing tips" style="padding-left: 10px;">Done<span>';
        }
        else{
          echo '<span  class="order-status status-trash" style="padding-left: 10px;">Fail<span>';
        }


      // Testing (to be removed) - Empty value case
      else
        echo '<span  class="order-status status-trash" style="padding-left: 10px;">Fail<span>';

      break;
    case 'ax-status' :
      // Get custom post meta data
      $my_var_two = get_post_meta( $post_id, 'ax_synchronize_status', true );
      if(!empty($my_var_two))
        if($my_var_two == 1){
          echo '<span class="order-status status-processing tips" style="padding-left: 10px;">Done<span>';
        }
        else{
          echo '<span  class="order-status status-trash" style="padding-left: 10px;">Fail<span>';
        }
      // Testing (to be removed) - Empty value case
      else
        echo '<span  class="order-status status-trash" style="padding-left: 10px;">Fail<span>';

      break;
  }
}

//
//add_filter('woocommerce_is_purchasable', 'my_woocommerce_is_purchasable', 10, 2);
//function my_woocommerce_is_purchasable($is_purchasable, $product) {
//  $cats = $product->category_ids;
//  return (in_array('2221',$cats) ? false : $is_purchasable);
// }
function wpex_add_custom_fonts() {
  return array( 'iCielVAGRoundedNext' ); // You can add more then 1 font to the array!
}


add_action('send_headers', function(){
// Enforce the use of HTTPS
  header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
// Prevent Clickjacking
  header("X-Frame-Options: SAMEORIGIN");
// Block Access If XSS Attack Is Suspected
  header("X-XSS-Protection: 1; mode=block");
// Prevent MIME-Type Sniffing
  header("X-Content-Type-Options: nosniff");
// Referrer Policy
  header("Referrer-Policy: no-referrer-when-downgrade");
}, 1);

function short_code_login_form() {
 include 'inc/login-form.php';

}

add_shortcode('short_code_login_form', 'short_code_login_form');

function encode($value){
  $key = sha1('EnCRypT10nK#Y!RiSRNn');
  if(!$value){return false;}
  $strLen = strlen($value);
  $keyLen = strlen($key);
  $j=0;
  $crypttext= '';
  for ($i = 0; $i < $strLen; $i++) {
    $ordStr = ord(substr($value,$i,1));
    if ($j == $keyLen) { $j = 0; }
    $ordKey = ord(substr($key,$j,1));
    $j++;
    $crypttext .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
  }
  return base64_encode($crypttext);
}

function decode($value){
  $value = base64_decode($value);
  if(!$value){return false;}
  $key = sha1('EnCRypT10nK#Y!RiSRNn');
  $strLen = strlen($value);
  $keyLen = strlen($key);
  $j=0;
  $decrypttext= '';
  for ($i = 0; $i < $strLen; $i+=2) {
    $ordStr = hexdec(base_convert(strrev(substr($value,$i,2)),36,16));
    if ($j == $keyLen) { $j = 0; }
    $ordKey = ord(substr($key,$j,1));
    $j++;
    $decrypttext .= chr($ordStr - $ordKey);
  }

  return $decrypttext;
}

// Return all post IDs
function pharma_get_all_mobile_content() {
    $paged = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

    //if having tag is game, get all posts having tags by game
    if(isset($_REQUEST['tag']) && $_REQUEST['tag'] == 'game'){
        $tag = 'game';
        $status = array('publish', 'private');
    }
    else{
        $tag = 'promotion';
        $status = 'public';
    }

    $total = count(get_posts( array(
        'posts_per_page' => -1,
        'post_type'   => 'mobile_content',
        'post_status' => $status,
        'tax_query' => array(
            array(
                'taxonomy' => 'post_tag',
                'field' => 'slug',
                'terms' => $tag
            )
        )
    )));

    if($paged == 1){
        $custom_offset = 0;

    }
    else{
        $custom_offset = 1*($paged-1)*5;

    }
    $all_post_ids = get_posts( array(
        'posts_per_page' => 5,
        'post_type'   => 'mobile_content',
        'post_status' => $status,
        'tax_query' => array(
            array(
                'taxonomy' => 'post_tag',
                'field' => 'slug',
                'terms' => $tag
            )
        ),
        'offset' => $custom_offset,

    ));
    foreach($all_post_ids as $key => $content){
        $tags = wp_get_post_tags($content->ID);
        $content->post_content =$content->post_content;
        $content->feature_image = get_the_post_thumbnail_url($content->ID, 'full');
        $content->tags = $tags;
        $content->enddate = get_field('end_date', $content->ID) ;
        $content->button_label = get_field('button_label', $content->ID) ;
        $content->custom_url = get_field('custom_url', $content->ID) ;
        $content->campaign_id = get_field('campaign_id', $content->ID) ;
        $content->campaign_status = get_field('campaign_status', $content->ID) ;
        $content->total = $total ;
    }
    if($paged > $total/($custom_offset+1)) return array('result'=>'number of page is invalid');
    return $all_post_ids;
}

// Add Walden/v1/get-all-post-ids route
add_action( 'rest_api_init', function () {
  register_rest_route( 'pharma/v1', '/get-mobile-content/', array(
      'methods' => 'GET',
      'callback' => 'pharma_get_all_mobile_content',
  ) );
} );




//add_action('wp_head', function () {
//
//  global $wp_scripts;
//
//  foreach($wp_scripts->queue as $handle) {
//    $script = $wp_scripts->registered[$handle];
//
//    //-- Weird way to check if script is being enqueued in the footer.
//
//    if($script->extra['group'] === 1) {
//
//      //-- If version is set, append to end of source.
//      $source = $script->src . ($script->ver ? "?ver={$script->ver}" : "");
//
//      //-- Spit out the tag.
//      echo "<link rel='preload' href='{$source}' as='script'/>\n";
//    }
//  }
//}, 1);

// Custom Scripting to Move JavaScript from the Head to the Footer
//function remove_head_scripts() {
//  remove_action("wp_head", "wp_print_scripts");
//  remove_action("wp_head", "wp_print_head_scripts", 9);
//  remove_action("wp_head", "wp_enqueue_scripts", 1);
//
//  add_action("wp_footer", "wp_print_scripts", 5);
//  add_action("wp_footer", "wp_enqueue_scripts", 5);
//  add_action("wp_footer", "wp_print_head_scripts", 5);
//}
//add_action( "wp_enqueue_scripts", "remove_head_scripts" );

// END Custom Scripting to Move JavaScript



if ( is_front_page() || is_home() ) {
  add_filter( 'wpcf7_load_js', '__return_false' );
  add_filter( 'wpcf7_load_css', '__return_false' );
}
remove_action( 'wp_head', 'wlwmanifest_link' ) ;
remove_action( 'wp_head', 'wp_generator' ) ;


add_filter('woocommerce_save_account_details_required_fields', 'hide_field');
function hide_field($required_fields)
{
    unset($required_fields["account_password"]);
    unset($required_fields["account_password-2"]);
    unset($required_fields["account_email"]);

    return $required_fields;
}

//function updating_existing_products_once(){
//    $args = array(
//        // WC product post type
//        'post_type'   => 'product',
//        // all posts
//        'numberposts' => -1,
//        'comment_status' => 'closed',
//        'post_status' => 'publish',
//    );
//
//    $shop_products = get_posts( $args );
//    foreach( $shop_products as $item){
//        $product = new WC_Product($item->ID);
//        wp_update_post( array(
//            'ID'    => $item->ID,
//            'comment_status' => 'open',
//        ) );
//    }
//}
//// After usage comment this line below
//updating_existing_products_once();
//
//add_action('transition_post_status', 'creating_a_new_product', 10, 3);
//function creating_a_new_product($new_status, $old_status, $post) {
//    if( $old_status != 'publish' && $new_status == 'publish' && !empty($post->ID)  && in_array( $post->post_type, array( 'product') ) ) {
//        if ($post->comment_status != 'open' ){
//            $product = new WC_Product($post->ID);
//            wp_update_post( array(
//                'ID'    => $post->ID,
//                'comment_status' => 'open',
//            ) );
//        }
//    }
//
//}

if (!wp_next_scheduled('cancel_pending_order_hook')) {
    wp_schedule_event(time(), 'cancel_order', 'cancel_pending_order_hook');
}

add_action('cancel_pending_order_hook', 'cancel_pending_order', 10, 0);

function cancel_pending_order() {
    $args = array(
        'limit'       => -1,
        'status' => array('pending'),
    );

    $orders = wc_get_orders( $args );
    if(count($orders)){
        foreach($orders as $key => $order){
            if(get_post_meta( $order->ID, '_payment_method', true )=='epay'){
                $seconds  = strtotime(date('Y-m-d H:i:s')) - strtotime($order->get_date_created());
                $hours = floor($seconds / 3600);
                $mins = floor(($seconds - ($hours*3600)) / 60);

                if($mins >= 5){
                    $order->update_status('cancelled', 'order_note');
                    email_cancel_order($order->ID);
                }

            }

        }
    }

}

function email_cancel_order($order_id){
    $subject = "Đơn Hàng Pharmacity của bạn vừa bị hủy vì lý do kỹ thuật";
    $headers = "MIME-Version: 1.0\r\n From: " . "Pharmacity <no_reply@pharmacity.vn>" . "\r\n" . "Content-Type: text/html; charset=\"" . get_settings('blog_charset') . "\"n";
    $order = wc_get_order($order_id);
    $mail_template =  'Chào '.$order->get_billing_first_name() .' '. $order->get_billing_last_name().'<br>';
    $mail_template .=  'Pharmacity đã hủy đơn hàng (ĐH) của bạn vì hệ thống có vấn đề tương tác với ePay. Chúng tôi thành thật xin lỗi vì lý do này. Quý khách vui lòng đặt lại hàng bằng phương thức thanh toán tiền mặt.';

    if ($order->get_billing_email()) {
         wp_mail($order->get_billing_email(), $subject, $mail_template, $headers);
    }
}
// Remove WP Version From Styles
add_filter( 'style_loader_src', 'sdt_remove_ver_css_js', 9999 );
// Remove WP Version From Scripts
add_filter( 'script_loader_src', 'sdt_remove_ver_css_js', 9999 );

// Function to remove version numbers
function sdt_remove_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;

}

function product_sku_register_settings() {
  add_option( 'product_sku_option_name', 'This is product sku for 500sp function.');
  register_setting( 'product_sku_options_group', 'product_sku_option_name', 'product_sku_callback' );
}
add_action( 'admin_init', 'product_sku_register_settings' );


function product_sku_register_options_page() {
  add_options_page('Product Sku (500sp)', '500sp setting', 'manage_options', 'productsku', 'product_sku_options_page');
}
add_action('admin_menu', 'product_sku_register_options_page');

function product_sku_options_page()
{
  ?>
  <div>
    <?php screen_icon(); ?>
    <h2>500sp - Product Sku</h2>
    <form method="post" action="options.php">
      <?php settings_fields( 'product_sku_options_group' ); ?>
      <h3>This is product sku for 500sp function.</h3>
      <p>We will ignore this products </p>
      <table>
        <tr valign="top">
          <th scope="row"><label for="myplugin_option_name">Sku</label></th>
          <td><textarea rows="10" cols="100" type="text" id="product_sku_option_name" name="product_sku_option_name" ><?php echo get_option('product_sku_option_name'); ?></textarea></td>
        </tr>
      </table>
      <?php  submit_button(); ?>
    </form>
  </div>
  <?php
}

// Ajax send OTP
add_action('wp_ajax_nopriv_ajax_request_otp', 'oms_request_otp');

/*
 * loginWithAccountKit
 */
function oms_request_otp() {
    if ($_POST['phone']) {
        $phone = $_POST['phone'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
        //curl_setopt($ch, CURLOPT_URL, 'https://omsapi.pharmacity.vn/graphql');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $query = '{"query":"mutation{\n  createVerifyCode(phone:\"' . $phone . '\")\n}","variables":null}';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $headers = [];
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $data = json_decode($result, 'array');
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        if (!empty($data['data']['createVerifyCode'])) {
            wp_send_json_success([
                'message' => $data['data']['createVerifyCode'],
            ]);
        }
        else {
            wp_send_json_error([
                'message' => $data['errors'][0]['message'],
            ]);
        }
    }
    else {
        wp_send_json_error([
            'message' => 'Vui lòng nhập số điện thoại của bạn.',
        ]);
    }
}

// End Ajax send OTP

