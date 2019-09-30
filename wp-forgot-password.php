<?php
/** Make sure that the WordPress bootstrap has run before continuing. */
require( dirname( __FILE__ ) . '/wp-load.php' );

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $username = $_POST['log'];
  $password = $_POST['pwd'];

  $login_data = oms_login_with_phone($username, $password);

  if ($login_data == false){
    $error_codes = base64_encode('Xin lỗi quý khách, hệ thống tài khoản tạm thời bị lỗi <br> xin vui lòng đăng nhập lại sau');
    $login_url = home_url('/forgot-password');
    $login_url = add_query_arg('login_error', $error_codes, $login_url);

    wp_redirect($login_url);
    exit;
  }
  elseif ((empty($login_data['data']["loginWithPhone"]) && !empty($login_data["errors"]))) {
    if($login_data["errors"][0]['message'] == 'validation'){
      $error_codes = base64_encode("Vui lòng nhập số điện thoại và mã xác nhận để đăng nhập.");
      $login_url = home_url('/forgot-password');
      $login_url = add_query_arg('login_error', $error_codes, $login_url);
    }
    else{
      $error_codes = base64_encode($login_data["errors"][0]['message']);
      $login_url = home_url('/forgot-password');
      $login_url = add_query_arg('login_error', $error_codes, $login_url);
    }
    wp_redirect($login_url);
    exit;
  }
  else {
    // Create a new user if it's not exist
    if (!username_exists( $username )){

      $email = (isset($login_data['data']["loginWithPhone"]['email'])) ? $login_data['data']["loginWithPhone"]['email'] : '';
      if (email_exists($login_data['data']["loginWithPhone"]['email']) || empty($login_data['data']["loginWithPhone"]['email'])){
        $email = $username . '@' . str_replace([
            'https://',
            'http://',
            'www.',
          ], '', home_url()); // generate a fake email address
      }

      $user_data = array(
        'user_login'    => $username,
        'user_email'    => $email,
        'user_pass'     => $password,
        'display_name' => (isset($login_data['data']["loginWithPhone"]['name']))?$login_data['data']["loginWithPhone"]['name']:'',
        'user_nicename' => (isset($login_data['data']["loginWithPhone"]['name']))?$login_data['data']["loginWithPhone"]['name']:'',
        'first_name' => (isset($login_data['data']["loginWithPhone"]['first_name']))?$login_data['data']["loginWithPhone"]['first_name']:'',
        'last_name' => (isset($login_data['data']["loginWithPhone"]['last_name']))?$login_data['data']["loginWithPhone"]['last_name']:'',
      );
      $user_id = wp_insert_user( $user_data );


      //User OMS id

      update_user_meta($user_id,'user_oms_id',$login_data['data']["loginWithPhone"]['id']);
      update_user_meta($user_id,'code',$login_data['data']["loginWithPhone"]['code']);
      update_user_meta($user_id,'customer_id',$login_data['data']["loginWithPhone"]['customer_id']);

      // Billing user information
      update_user_meta($user_id,'billing_first_name',$login_data['data']["loginWithPhone"]['first_name']);
      update_user_meta($user_id,'billing_last_name',$login_data['data']["loginWithPhone"]['last_name']);
      update_user_meta($user_id,'billing_company',$login_data['data']["loginWithPhone"]['company']);
      update_user_meta($user_id,'billing_address_1',$login_data['data']["loginWithPhone"]['address'][0]['address']);
      update_user_meta($user_id,'billing_city',$login_data['data']["loginWithPhone"]['address'][0]['province']);
      update_user_meta($user_id,'billing_state',$login_data['data']["loginWithPhone"]['address'][0]['province']);
      update_user_meta($user_id,'billing_country','VN');
      update_user_meta($user_id,'billing_email',$login_data['data']["loginWithPhone"]['email']);
      update_user_meta($user_id,'billing_phone',$login_data['data']["loginWithPhone"]['phone_number']);

      // Shipping user information
      update_user_meta($user_id,'shipping_first_name',$login_data['data']["loginWithPhone"]['first_name']);
      update_user_meta($user_id,'shipping_last_name',$login_data['data']["loginWithPhone"]['last_name']);
      update_user_meta($user_id,'shipping_company',$login_data['data']["loginWithPhone"]['company']);
      update_user_meta($user_id,'shipping_address_1',$login_data['data']["loginWithPhone"]['address'][0]['address']);
      update_user_meta($user_id,'shipping_city',$login_data['data']["loginWithPhone"]['address'][0]['province']);
      update_user_meta($user_id,'shipping_state',$login_data['data']["loginWithPhone"]['address'][0]['province']);
      update_user_meta($user_id,'shipping_country','VN');
      update_user_meta($user_id,'shipping_email',$login_data['data']["loginWithPhone"]['email']);
      update_user_meta($user_id,'shipping_phone',$login_data['data']["loginWithPhone"]['phone_number']);


      if (is_wp_error($user_id)) {
        $error_codes  = $result->get_error_message();
        $login_url = home_url('/signin');
        $login_url = add_query_arg('login_error', base64_encode($error_codes), $login_url);

        wp_redirect($login_url);
        exit;
      }

      $user = get_user_by('id',$user_id);

    }
    else{
      $user = get_user_by('login',$username);
    }

    if (isset($login_data['data']["loginWithPhone"]['api_token'])) {
      setcookie('oms_user_token', $login_data['data']["loginWithPhone"]['api_token'], time()+31556926);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, TRUE);

    do_action('wp_login', $user->user_login, $user);
    wp_redirect(home_url('/my-account/edit-account'));
    exit;
  }
}