<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_action('wp_ajax_nopriv_fbak_fb_account_kit_auth_login', 'fbak_process_auth_login');

// admin profile
add_action('wp_ajax_fbak_fb_account_kit_associate', 'fbak_associate_phone_number_email');
add_action('wp_ajax_fbak_fb_account_kit_disconnect', 'fbak_disconnect_phone_number_email');

/**
 * Send a GET request to the API
 *
 * @param  string $url
 *
 * @return array
 */
function fbak_send_remote_request_url($url) {
  $response = wp_remote_get($url);
  $result = wp_remote_retrieve_body($response);

  return json_decode($result, TRUE);
}

/**
 * Authorize accountkit with a authorization code
 *
 * @param  string $code
 *
 * @return array
 */
function fbak_authorize_with_account_kit($code) {
  $fbak_settings = get_option('fbak_plugin_settings');

  $app_id = $fbak_settings['fbak_app_id'];
  $secret = $fbak_settings['fbak_accountkit_secret_key'];
  $version = fbak_get_fb_app_api_version();

  $token_exchange_url = 'https://graph.accountkit.com/' . $version . '/access_token?' .
    'grant_type=authorization_code' .
    '&code=' . $code .
    "&access_token=AA|$app_id|$secret";
  $data = fbak_send_remote_request_url($token_exchange_url);
  $user_id = $data['id'];
  $access_token = $data['access_token'];
  $refresh_interval = $data['token_refresh_interval_sec'];
  $appsecret_proof = hash_hmac('sha256', $access_token, $secret);

  // Get Account Kit information
  $me_endpoint_url = 'https://graph.accountkit.com/' . $version . '/me?' .
    'access_token=' . $access_token . '&appsecret_proof=' . $appsecret_proof;


  $me_data = fbak_send_remote_request_url($me_endpoint_url);
  $me_data['access_token'] = $access_token;

  return $me_data;
}

/**
 * Process user login
 *
 * @return void
 */
function fbak_process_auth_login() {
  // Check the referrer for the AJAX call.
  //check_ajax_referer('fbak_fb_account_kit', 'csrf');

  $data_oms = oms_login_with_accountkit($_POST['code'], $_POST['first_name'], $_POST['last_name']);



  $phone = isset($data_oms['data']['loginWithAccountKit']['phone_number']) ? $data_oms['data']['loginWithAccountKit']['phone_number'] : '';
  //$email = isset($me_data['email']) ? $me_data['email']['address'] : '';
  $id = isset($data_oms['data']['loginWithAccountKit']['id']) ? $data_oms['data']['loginWithAccountKit']['id'] : 0;

  $login_error_url = add_query_arg('fbak_login_error', 'true', home_url('/signup'));
  $login_error_url = apply_filters('fbak/account_kit_login_error_url', $login_error_url);

  if ($email) {
    $user = fbak_handle_email_login($email, $id);

    if ($user) {
      wp_set_current_user($user->ID);
      wp_set_auth_cookie($user->ID, TRUE);

      do_action('wp_login', $user->user_login, $user);

      // update the account kit reference
      update_user_meta($user->ID, '_fb_accountkit_id', $id);
      update_user_meta($user->ID, '_fb_accountkit_auth_mode', 'email');

      do_action('fbak_user_login_via_email', $user);

      wp_send_json_success([
        'redirect' => esc_url($_POST['email_redir']),
      ]);
    }
    else {
      wp_send_json_error([
        'redirect' => esc_url($login_error_url),
      ]);
    }
  }

  if ($phone) {

    $user = fbak_handle_phone_login($phone, $id,$data_oms);

    if ($user) {

      if (isset($data_oms['data']["loginWithAccountKit"]['api_token'])) {
        setcookie('oms_user_token', $data_oms['data']["loginWithAccountKit"]['api_token'], time() + 31556926);
      }

      //sync point when login

      $phoen_update_data  = (isset($data_oms['data']["loginWithAccountKit"]['point'])) ? $data_oms['data']["loginWithAccountKit"]['point'] : 0 ;


      update_user_meta($user->ID, 'reward_point',$phoen_update_data);

      update_user_points($phoen_update_data,$user->ID);

      wp_set_current_user($user->ID);
      wp_set_auth_cookie($user->ID, TRUE);

      do_action('wp_login', $user->user_login, $user);

      // update the account kit reference
      update_user_meta($user->ID, '_fb_accountkit_id', $id);
      update_user_meta($user->ID, '_fb_accountkit_auth_mode', 'phone');

      do_action('fbak_user_login_via_sms', $user);

      if(empty($user->first_name) || empty($user->last_name) || empty($user->display_name)){
        $redirect = home_url("/my-account/edit-account/");
      }
      else{
        $redirect = $_POST['sms_redir'];
      }

      wp_send_json_success([
        'redirect' => esc_url($redirect),
      ]);
    }
    else {
      wp_send_json_error([
        'redirect' => esc_url($login_error_url),
      ]);
    }
  }

  die();
}

function writeOrderDataToCSV($jsonDecoded,$user_name) {
  $upload_dir   = wp_upload_dir();

  $URL = $upload_dir['basedir'].'/'.'wpallimport'.'/'.'files';
  $csvFileName = '/orders.csv';
  $URL .= $csvFileName;

  $array_csv = [];
  $header_csv = [
    'order_number',
    'user_name',
    'created_at',
    'payment_status',
    'payment_method',
    'status',
    'cancel_reason',
    'note',
    'total_price',
    'total_item_price',
    'total_discount',
    'discount_code',
    'discount_amount',
    'shipping_title',
    'shipping_method',
    'shipping_price',
    'products',
    'quantity',
    'product_price',
    'store',
    //      'tags'
  ];




  //Loop through the associative array.
  $data = [];
  $index = 0;

  foreach($jsonDecoded as $row) {


    if ($row['order_number'] < 20000000){
      // Product Basic Information
      $data[$index]['order_number'] = $row['order_number'];
      $data[$index]['user_name'] = $row['phone'];

      $time = strtotime( $row['created_at']);

      $created = date('m/d/Y',$time);
      $data[$index]['created_at'] = $created;
      $data[$index]['payment_status'] = $row['payment_status'];


      if($row['paymentMethod']['id'] == 3){
        $method = 'epay';
      }else{
        $method = 'cod';
      }

      $data[$index]['payment_method'] = $method;

      if($row['status'] == 'Giao Thành Công'){
        $status = 'wc-completed';
      }
      else if ($row['status'] == 'Đã Hủy'){
        $status = 'wc-cancelled';
      }
      else if ($row['status'] == 'Chờ Duyệt'){
        $status = 'wc-pending-approve';
      }
      else if ($row['status'] == 'Đang xử lý'){
        $status = 'wc-processing';
      }
      else if ($row['status'] == 'Từ chối'){
        $status = 'wc-rejected';
      }
      else if ($row['status'] == 'Thất bại'){
        $status = 'wc-failed';
      }
      else if ($row['status'] == 'Đã hoàn lại tiền'){
        $status = 'wc-refunded';
      }
      else if ($row['status'] == 'Tạm giữ'){
        $status = 'wc-on-hold';
      }
      else if ($row['status'] == 'Chờ thanh toán'){
        $status = 'wc-pending';
      }
      else{
        $status = '';
      }

      $data[$index]['status'] = $status;

      $data[$index]['cancel_reason'] = $row['cancel_reason'];
      $data[$index]['note'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '',$row['note'])));
      // Product Variants
      $data[$index]['total_price'] = $row['total_price'];
      $data[$index]['total_item_price'] = $row['total_item_price'];
      $data[$index]['total_discount'] = $row['total_discount'];
      $data[$index]['discount_code'] = $row['discount_code'];
      $data[$index]['discount_amount'] = $row['discount_amount'];

      if(!empty($row['store']['name'])){
        $shipping_method = 'local_pickup_plus';
        $shipping_title = "Lấy hàng tại quyầy: ".$row['store']['address'].'. '.$row['store']['ward'].', '.$row['store']['district'].', '.$row['store']['province'];
      }
      else{

        if($row['shipping_price'] == 0){
          $shipping_title = "Giao hàng miễn phí";
          $shipping_method = 'free_shipping';
        }
        else{
          $shipping_title = "Giao hàng miễn phí";
          $shipping_method = 'flat_rate';
        }
      }
      $data[$index]['shipping_title'] = $shipping_title;
      $data[$index]['shipping_method'] = $shipping_method;
      $data[$index]['shipping_price'] = $row['shipping_price'];

      $products = $row['orderItems'];
      $skus = [];
      $quantity = [];
      $prices = [];

      foreach ($products as $product){
        $skus[] = $product['productVariant']['sku'];
        $quantity[] = $product['quantity'];
        $prices[] = $product['price'];
      }

      $sku_string = implode('|',$skus);
      $quantity_string = implode('|',$quantity);
      $price_string = implode('|',$prices);

      $data[$index]['products'] = $sku_string;
      $data[$index]['quantity'] = $quantity_string;
      $data[$index]['price'] = $price_string;
      $data[$index]['store'] = $row['store']['name'];

      $index++;
    }
  }

  $array_csv = $data;


  if (!file_exists($URL)) {
    array_unshift($array_csv, $header_csv);
  }

  //Open file pointer.
  $fp = fopen($URL, 'a');

  foreach ($array_csv as $line) {
    fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($fp, array_values($line),',', "'");
  }
  //Finally, close the file pointer.
  fclose($fp);

}

/**
 * Associate phone number with a account
 *
 * @since 1.0.0
 *
 * @return void
 */
function fbak_associate_phone_number_email() {
  // Check the referrer for the AJAX call.
  check_ajax_referer('fbak_fb_account_kit', 'csrf');

  $me_data = fbak_authorize_with_account_kit($_POST['code']);

  $phone = isset($me_data['phone']) ? $me_data['phone']['number'] : '';
  $email = isset($me_data['email']) ? $me_data['email']['address'] : '';
  $id = isset($me_data['id']) ? $me_data['id'] : 0;

  $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : get_current_user_id();

  if ($id) {
    update_user_meta($user_id, '_fb_accountkit_id', $id);
  }

  if ($phone) {
    update_user_meta($user_id, 'phone_number', $phone);
    update_user_meta($user_id, 'billing_phone', $phone); // update woocommerce phone number
    update_user_meta($user_id, '_fb_accountkit_auth_mode', 'phone');
  }

  if ($email) {
    update_user_meta($user_id, '_fb_accountkit_auth_mode', 'email');
  }

  wp_send_json_success();

  die();
}

/**
 * Disconnect a phone number
 *
 * @since 1.0.0
 *
 * @return void
 */
function fbak_disconnect_phone_number_email() {
  $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : get_current_user_id();

  delete_user_meta($user_id, '_fb_accountkit_id');
  delete_user_meta($user_id, '_fb_accountkit_auth_mode');

  wp_send_json_success();

  die();
}

/**
 * Handle the user email response
 *
 * @param  string $email
 *
 * @return $user
 */
function fbak_handle_email_login($email, $account_id) {
  $fbak_settings = get_option('fbak_plugin_settings');
  global $wpdb;

  $user = get_user_by('email', $email);

  if (!$user) {
    $get_user = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_fb_accountkit_id' AND meta_value = %s", $account_id));
    $user = get_user_by('id', $get_user);

    if (!$user) {
      $username = current(explode('@', $email));
      $user = get_user_by('login', $username);

      if (isset($fbak_settings['fbak_email_new_register']) && $fbak_settings['fbak_email_new_register'] == 1) {
        if (!$user) {
          $username = fbak_guess_username_by_email($email);
          $user_pass = wp_generate_password(12, TRUE);

          $userdata = [
            'user_login' => $username,
            'user_pass' => $user_pass,
            'user_email' => $email,
            'role' => fbak_get_email_new_user_role(),
          ];

          $user_id = wp_insert_user($userdata);
          do_action('fbak_create_new_user_via_email', $user_id, $username, $user_pass, $email);
          $user = get_user_by('id', $user_id);
        }
      }
    }
  }

  return $user;
}

/**
 * Handle the phone authentication response
 *
 * @param  string $phone
 * @param  integer $account_id
 *
 * @return $user
 */
function fbak_handle_phone_login($phone_no, $account_id, $login_data) {
  $fbak_settings = get_option('fbak_plugin_settings');
  global $wpdb;

  $get_user = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '_fb_accountkit_id' AND meta_value = %s", $account_id));
  $user = get_user_by('id', $get_user);

  if (!$user) {
    $phone = str_replace('+84', '0', $phone_no); // remove the '+' sign
    $phone = apply_filters('fbak/custom_phone_number_format', $phone);
    $user = get_user_by('login', $phone);

    if (!$user) {
      $username = fbak_guess_username_by_phone($phone);
      $user_pass = wp_generate_password(12, TRUE);
      if(isset($login_data['data']["loginWithAccountKit"]['email']) && !empty($login_data['data']["loginWithAccountKit"]['email'])){
        $email = $login_data['data']["loginWithAccountKit"]['email'];
      }
      else{
        //$email = 'nhap_email_o_day_'.rand(10,10000).rand(99,9999999). '@email.com'; // generate a fake email address
        $email ='';
      }


      $first_name = (isset($login_data['data']["loginWithAccountKit"]['first_name'])) ? $login_data['data']["loginWithAccountKit"]['first_name'] : '';
      $last_name = (isset($login_data['data']["loginWithAccountKit"]['last_name'])) ? $login_data['data']["loginWithAccountKit"]['last_name'] : '';
      $userdata = [
        'user_login' => $username,
        'user_pass' => $user_pass,
        'user_email' => $email,
        'role' => fbak_get_sms_new_user_role(),
        'display_name' => $first_name . ' ' . $last_name,
        'user_nicename' => $first_name . ' ' . $last_name,
        'first_name' => $first_name,
        'last_name' => $last_name,
      ];

      $user_id = wp_insert_user($userdata);


      //User OMS id

      update_user_meta($user_id, 'user_oms_id', $login_data['data']["loginWithAccountKit"]['id']);
      update_user_meta($user_id, 'code', $login_data['data']["loginWithAccountKit"]['code']);
      update_user_meta($user_id, 'customer_id', $login_data['data']["loginWithAccountKit"]['customer_id']);

      // Billing user information


      do_action('fbak_create_new_user_via_sms', $user_id, $username, $user_pass, $email);


      $user = get_user_by('id', $user_id);
    }
  }

  //Update avatar
  $avatar_url = (isset($login_data['data']["loginWithAccountKit"]['avatar'])) ? $login_data['data']["loginWithAccountKit"]['avatar'] : '';
  if (!empty($avatar_url)){
    global $wpdb;
    $user_avatar = get_user_meta( $user->ID,$wpdb->get_blog_prefix() . 'user_avatar');
      $file = upload_product_image($avatar_url);
      $wp_filetype = wp_check_filetype($file['file']);
      $attachment = array(
        'guid' => $file['url'],
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($file['file'])),
        'post_content' => '',
        'post_status' => 'inherit'
      );
      $attach_id = wp_insert_attachment($attachment, $file['file']);
      $attach_data = wp_generate_attachment_metadata($attach_id, $file['file']);
      wp_update_attachment_metadata($attach_id, $attach_data);
      update_user_meta($user->ID, $wpdb->get_blog_prefix() . 'user_avatar', $attach_id);
  }

  //Update email
  if(isset($login_data['data']["loginWithAccountKit"]['email']) && !empty($login_data['data']["loginWithAccountKit"]['email'])){
    $args = array(
      'ID'         => $user->ID,
      'user_email' => esc_attr( $login_data['data']["loginWithAccountKit"]['email'] )
    );
    wp_update_user( $args );
  }

  if(empty(get_user_meta( $user->ID,'user_oms_id') && isset($login_data['data']["loginWithAccountKit"]['id']))){
    update_user_meta($user->ID, 'user_oms_id', $login_data['data']["loginWithAccountKit"]['id']);
  }

  if(empty(get_user_meta( $user->ID,'code') && isset($login_data['data']["loginWithAccountKit"]['code']))){
    update_user_meta($user->ID, 'code', $login_data['data']["loginWithAccountKit"]['code']);
  }

  if(empty(get_user_meta( $user->ID,'customer_id') && isset($login_data['data']["loginWithAccountKit"]['customer_id']))){
    update_user_meta($user->ID, 'customer_id', $login_data['data']["loginWithAccountKit"]['customer_id']);
  }


  $province = get_id_city($login_data['data']["loginWithAccountKit"]['address'][0]['province']);
  $district = get_id_district($login_data['data']["loginWithAccountKit"]['address'][0]['district']);
  $ward = get_id_village($login_data['data']["loginWithAccountKit"]['address'][0]['ward']);

  if(empty(get_user_meta( $user->ID,'billing_first_name') && isset($login_data['data']["loginWithAccountKit"]['first_name']))){
    update_user_meta($user->ID, 'billing_first_name', $login_data['data']["loginWithAccountKit"]['first_name']);
  }

  if(empty(get_user_meta( $user->ID,'billing_last_name') && isset($login_data['data']["loginWithAccountKit"]['name']))){
    update_user_meta($user->ID, 'billing_last_name', $login_data['data']["loginWithAccountKit"]['name']);
  }

  if(empty(get_user_meta( $user->ID,'billing_company') && isset($login_data['data']["loginWithAccountKit"]['company']))){
    update_user_meta($user->ID, 'billing_company', $login_data['data']["loginWithAccountKit"]['company']);
  }

  if(empty(get_user_meta( $user->ID,'billing_address_1') && isset($login_data['data']["loginWithAccountKit"]['address']))){
    update_user_meta($user->ID, 'billing_address_1', $login_data['data']["loginWithAccountKit"]['address'][0]['address']);
  }

  if(empty(get_user_meta( $user->ID,'billing_city') && isset($district))){
    update_user_meta($user->ID, 'billing_city', $district);
  }

  if(empty(get_user_meta( $user->ID,'billing_state') && isset($province))){
    update_user_meta($user->ID, 'billing_state', $province);
  }

  if(empty(get_user_meta( $user->ID,'billing_district') && isset($district))){
    update_user_meta($user->ID, 'billing_district', $district);
  }

  if(empty(get_user_meta( $user->ID,'billing_address_2') && isset($login_data['data']["loginWithAccountKit"]['name']))){
    update_user_meta($user->ID, 'billing_address_2', $ward);
  }

  if(empty(get_user_meta( $user->ID,'billing_country'))){
    update_user_meta($user->ID, 'billing_country', 'VN');
  }

  if(empty(get_user_meta( $user->ID,'billing_email') && isset($login_data['data']["loginWithAccountKit"]['email']))){
    update_user_meta($user->ID, 'billing_email', $login_data['data']["loginWithAccountKit"]['email']);
  }

  if(empty(get_user_meta( $user->ID,'billing_phone') && isset($login_data['data']["loginWithAccountKit"]['phone_number']))){
    update_user_meta($user->ID, 'billing_phone', $login_data['data']["loginWithAccountKit"]['phone_number']);
  }

  // Shipping user information

  if(empty(get_user_meta( $user->ID,'shipping_first_name') && isset($login_data['data']["loginWithAccountKit"]['first_name']))){
    update_user_meta($user->ID, 'shipping_first_name', $login_data['data']["loginWithAccountKit"]['first_name']);
  }

  if(empty(get_user_meta( $user->ID,'shipping_last_name') && isset($login_data['data']["loginWithAccountKit"]['name']))){
    update_user_meta($user->ID, 'shipping_last_name', $login_data['data']["loginWithAccountKit"]['name']);
  }

  if(empty(get_user_meta( $user->ID,'shipping_company') && isset($login_data['data']["loginWithAccountKit"]['company']))){
    update_user_meta($user->ID, 'shipping_company', $login_data['data']["loginWithAccountKit"]['company']);
  }

  if(empty(get_user_meta( $user->ID,'shipping_address_1') && isset($login_data['data']["loginWithAccountKit"]['address']))){
    update_user_meta($user->ID, 'shipping_address_1', $login_data['data']["loginWithAccountKit"]['address'][0]['address']);
  }

  if(empty(get_user_meta( $user->ID,'shipping_city') && isset($district))){
    update_user_meta($user->ID, 'shipping_city', $district);
  }

  if(empty(get_user_meta( $user->ID,'shipping_state') && isset($province))){
    update_user_meta($user->ID, 'shipping_state', $province);
  }

  if(empty(get_user_meta( $user->ID,'shipping_district') && isset($district))){
    update_user_meta($user->ID, 'shipping_district', $district);
  }

  if(empty(get_user_meta( $user->ID,'shipping_address_2') && isset($login_data['data']["loginWithAccountKit"]['name']))){
    update_user_meta($user->ID, 'shipping_address_2', $ward);
  }

  if(empty(get_user_meta( $user->ID,'shipping_country'))){
    update_user_meta($user->ID, 'shipping_country', 'VN');
  }

  if(empty(get_user_meta( $user->ID,'shipping_email') && isset($login_data['data']["loginWithAccountKit"]['email']))){
    update_user_meta($user->ID, 'shipping_email', $login_data['data']["loginWithAccountKit"]['email']);
  }

  if(empty(get_user_meta( $user->ID,'shipping_pone') && isset($login_data['data']["loginWithAccountKit"]['phone_number']))){
    update_user_meta($user->ID, 'shipping_pone', $login_data['data']["loginWithAccountKit"]['phone_number']);
  }

  if(empty(get_user_meta( $user->ID,'phone_number') && isset($phone_no))){
    update_user_meta($user->ID, 'phone_number', $phone_no);
  }

  //Get Order by User
  if(empty(get_user_meta( $user->ID,'synced')[0]) || get_user_meta( $user->ID,'synced')[0] != 1){
    $order_number = get_oms_order_by_token();
    writeOrderDataToCSV($order_number,$user->user_login);
    update_user_meta($user->ID, 'synced', 1);
  }


  return $user;
}

/**
 * WooCommerce class-wc-api-products.php
 * See https://github.com/justinshreve/woocommerce/blob/master/includes/api/class-wc-api-products.php
 * Upload image from URL
 *
 * @since 2.2
 * @param string $image_url
 * @return int|WP_Error attachment id
 */
function upload_product_image($image_url) {
  $file_name = basename(current(explode('?', $image_url)));
  $wp_filetype = wp_check_filetype($file_name, null);
  $parsed_url = @parse_url($image_url);

  // Check parsed URL
  if(!$parsed_url || !is_array($parsed_url)) {
    throw new WC_API_Exception('woocommerce_api_invalid_product_image', sprintf(__('Invalid URL %s', 'woocommerce'), $image_url), 400);
  }

  // Ensure url is valid
  $image_url = str_replace(' ', '%20', $image_url);

  // Get the file
  $response = wp_safe_remote_get($image_url, array(
    'timeout' => 10
  ));

  if(is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
    throw new WC_API_Exception('woocommerce_api_invalid_remote_product_image', sprintf(__('Error getting remote image %s', 'woocommerce'), $image_url), 400);
  }

  // Ensure we have a file name and type
  if(!$wp_filetype['type']) {
    $headers = wp_remote_retrieve_headers($response);
    if(isset($headers['content-disposition']) && strstr($headers['content-disposition'], 'filename=')) {
      $disposition = end(explode('filename=', $headers['content-disposition']));
      $disposition = sanitize_file_name($disposition);
      $file_name = $disposition;
    }
    elseif(isset($headers['content-type']) && strstr($headers['content-type'], 'image/')) {
      $file_name = 'image.' . str_replace('image/', '', $headers['content-type']);
    }
    unset($headers);
  }

  // Upload the file
  $upload = wp_upload_bits($file_name, '', wp_remote_retrieve_body($response));

  if($upload['error']) {
    throw new WC_API_Exception('woocommerce_api_product_image_upload_error', $upload['error'], 400);
  }

  // Get filesize
  $filesize = filesize($upload['file']);

  if(0 == $filesize) {
    @unlink($upload['file']);
    unset($upload);
    throw new WC_API_Exception('woocommerce_api_product_image_upload_file_error', __('Zero size file downloaded', 'woocommerce'), 400);
  }

  unset($response);

  return $upload;
}

function get_id_city($name = ''){
  include WP_PLUGIN_DIR.'/devvn-vietnam-shipping/cities/tinh_thanhpho.php';

  $id = array_search(ucwords($name),$tinh_thanhpho);

  if($id == FALSE){
    return "";
  }

  return $id;
}

function get_id_district($id = ''){
  include WP_PLUGIN_DIR.'/devvn-vietnam-shipping/cities/quan_huyen.php';
  if(is_array($quan_huyen) && !empty($quan_huyen)){
    $nameQuan = search_in_array($quan_huyen,'name',$id);
    $nameQuan = isset($nameQuan[0]['name'])?$nameQuan[0]['maqh']:'';
    return $nameQuan;
  }
  return false;
}

function get_id_village($id = ''){
  include WP_PLUGIN_DIR.'/devvn-vietnam-shipping/cities/xa_phuong_thitran.php';
  if(is_array($xa_phuong_thitran) && !empty($xa_phuong_thitran)){
    $name = search_in_array($xa_phuong_thitran,'name',$id);
    $name = isset($name[0]['name'])?$name[0]['xaid']:'';
    return $name;
  }
  return false;
}

function search_in_array($array, $key, $value)
{
  $results = array();

  if (is_array($array)) {
    if (isset($array[$key]) && $array[$key] == $value) {
      $results[] = $array;
    }elseif(isset($array[$key]) && is_serialized($array[$key]) && in_array($value,maybe_unserialize($array[$key]))){
      $results[] = $array;
    }
    foreach ($array as $subarray) {
      $results = array_merge($results, search_in_array($subarray, $key, $value));
    }
  }

  return $results;
}
