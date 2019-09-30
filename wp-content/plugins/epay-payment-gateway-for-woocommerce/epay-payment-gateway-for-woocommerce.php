<?php
/**
 * Plugin Name: VNPT EPAY payment gateway for WooCommerce - Credit card and ATM
 * Description: Full integration for Epay payment gateway for WooCommerce
 * Version: 1.0
 * Author: hoqkhanh@gmail.com
 * Author URI: mailto:hoqkhanh@gmail.com
 * License: GPL2
 */

add_action('plugins_loaded', 'woocommerce_epayUS_init', 0);

function woocommerce_epayUS_init(){
  if(!class_exists('WC_Payment_Gateway')) return;

  class WC_epayUS extends WC_Payment_Gateway{

    // URL checkout của onepay.vn - Checkout URL for OnePay
    private $epay_url;

    // Mã merchant site code
    private $merchant_site_code;

    // Mật khẩu bảo mật - Secure password
    private $secure_pass;

    // Debug parameters
    private $debug_params;
    private $debug_md5;

    function __construct(){

      $this->icon = plugins_url( 'epay-payment-gateway-for-woocommerce/download.jpeg', dirname(__FILE__) ); // Icon URL
      $this->id = 'epay';
      $this->method_title = 'EPAY';
      $this->has_fields = false;

      $this->init_form_fields();
      $this->init_settings();

      $current_slug = $_SERVER['REQUEST_URI'];


      if ( strpos($current_slug, 'checkout/') != false) {
        wp_enqueue_script('jqueylibjs',plugins_url( 'jquery.min.js', __FILE__ ), '1.0', true);
        wp_enqueue_script('epaylibjs',MEGAPAY_URL.'/pg_was/js/payment/layer/paymentClient.js',array('jquery'), '1.0', true);
        wp_enqueue_script( 'epay', plugins_url( 'epay.js', __FILE__ ), array('jquery'), '1.0', true );
        wp_enqueue_style('epaylibcss', MEGAPAY_URL.'/pg_was/css/payment/layer/paymentClient.css');
      }


      wp_localize_script( 'epay', 'postepay', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
      ));

      wp_localize_script('epay', 'wpurl', array(
        'siteurl' => get_option('siteurl')
      ));

      $this->title = $this->settings['title'];
      $this->description = $this->settings['description'];

      $this->epay_url = $this->settings['onepay_url'];
      $this->merchant_access_code = $this->settings['merchant_access_code'];
      $this->merchant_id = $this->settings['merchant_id'];
      $this->secure_secret = $this->settings['secure_secret'];
      //$this->redirect_page_id = $this->settings['redirect_page_id'];

      $this->debug = $this->settings['debug'];
      $this->order_button_text = __( 'Pay now', 'monepayus' );

      $this->msg['message'] = "";
      $this->msg['class'] = "";

      if ( version_compare( WOOCOMMERCE_VERSION, '2.0.8', '>=' ) ) {
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
      } else {
        add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
      }
      add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
      add_action( 'woocommerce_thankyou_epay', array( $this, 'thankyou_page'));
      add_action( 'woocommerce_api_'.strtolower(get_class($this)), array( $this, 'callback_handler' ) );
      add_action( 'woocommerce_api_notify', array( $this, 'notify_handler' ) );
      add_action( 'woocommerce_api_force_payment', array( $this, 'payment_handler' ) );
    }

    function notify_handler(){


      $data_raw = file_get_contents('php://input');

      if(!empty($data_raw)) {
        $data = json_decode($data_raw,'array');

        $order_id = $data['invoiceNo'];
        $order = wc_get_order($order_id);

        if(isset($order) && !empty($order)){
          if($order->get_status() =='pending'){
            $token_data = [
              'timeStamp' => $data['timeStamp'],
              'merTrxId' => $data['merTrxId'],
              'trxId' => $data['trxId'],
              'amount' => $data['amount'],
              'merchantToken' => $data['merchantToken']
            ];

            if(checkToken($token_data)){
              if(empty($order->get_transaction_id())){
                $order->payment_complete($data['merTrxId']);
                $order->reduce_order_stock();
                $order->add_order_note( __( 'Payment completed (API Notify) - '.$data['merTrxId'], 'woocommerce' ) );
              }
            }
          }
        }

      }
      exit;
    }

    function callback_handler(){


      if(!empty($_GET)){
        $token_data = [
          'timeStamp' => $_GET['timeStamp'],
          'merTrxId' =>$_GET['merTrxId'],
          'trxId' => $_GET['trxId'],
          'amount' => $_GET['amount'],
          'merchantToken' => $_GET['merchantToken']
        ];

        $order = wc_get_order( $_GET['invoiceNo'] );



        if (checkToken($token_data)){
          if($_GET['resultMsg'] == 'SUCCESS' && $_GET['resultCd'] == '00_000'){
            $order->payment_complete($_GET['merTrxId']);
            $order->reduce_order_stock();
            $order->add_order_note( __( 'Payment completed', 'woocommerce' ) );
            wp_safe_redirect( WC_Payment_Gateway::get_return_url($order)."&payType=".$_GET['payType']);
            exit;
          }
          else{

            $tranDesc = $this->getResponseDescription($_GET['resultCd']);
            $order->update_status( 'failed' );
            $order->add_order_note( __( 'Payment failed-'.$tranDesc, 'woocommerce' ) );
            $order->add_order_note( __( 'Transaction ID-'.$_GET['merTrxId'], 'woocommerce' ) );
            wp_safe_redirect( WC_Payment_Gateway::get_return_url($order)."&resultCd=".$_GET['resultCd']);
            exit;
          }
        }
        else{
          $tranDesc = $this->getResponseDescription(99);
          $order->update_status( 'failed' );
          $order->add_order_note( __( 'Transaction ID-'.$_GET['merTrxId'], 'woocommerce' ) );
          $order->add_order_note( __( 'Payment failed-'.$tranDesc, 'woocommerce' ) );
          wp_safe_redirect( WC_Payment_Gateway::get_return_url($order)."&resultCd=99");
          exit;
        }
      }

      wp_die();

    }

    function payment_handler(){

      if( current_user_can('administrator') ) {

        $key = '1q2w3easd378hdncmye@12hff';

        if(empty($_GET['check']) || $_GET['check'] != $key){
          echo "Key is wrong";exit;
        }

        if(empty($_GET['order_id']) || empty($_GET['id'])){
          echo "Data is empty";exit;
        }

        $order = wc_get_order( $_GET['order_id'] );

        if($order){
          $order->payment_complete($_GET['id']);
          $order->reduce_order_stock();
          $order->add_order_note( __( 'Payment completed', 'woocommerce' ) );exit;
        }

      }
      else{
        echo "You are is not administrator";exit;
      }
    }


    function init_form_fields(){
      // Admin fields
      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Activate', 'monepayus'),
          'type' => 'checkbox',
          'label' => __('Activate the payment gateway for PAY', 'monepayus'),
          'default' => 'no'),
        'title' => array(
          'title' => __('Name:', 'monepayus'),
          'type'=> 'text',
          'description' => __('Name of payment method (as the customer sees it)', 'monepayus'),
          'default' => __('Megapay', 'monepayus')),
        'description' => array(
          'title' => __('', 'monepayus'),
          'type' => 'textarea',
          'description' => __('Payment gateway description', 'monepayus'),
          'default' => __('Click place order and you will be directed to the Epay popup in order to make payment', 'monepayus')),


        'nlcurrency' => array(
          'title' => __('Currency', 'monepayus'),
          'type' => 'text',
          'default' => 'VND',
          'description' => __('"VND" or "USD"', 'monepayus')
        ),
        'merchant_id' => array(
          'title' => __( 'Merchant ID', 'monepayus'),
          'type' => 'text'
        ),
        'merchant_access_code' => array(
          'title' => __( 'Checksum key', 'monepayus'),
          'type' => 'text'
        ),
        'mega_url' => array(
          'title' => __( 'Mega URL', 'monepayus'),
          'type' => 'text'
        )
      );
    }

    public function admin_options(){
      echo '<h3>'.__('onepayUS Payment Gateway', 'monepayus').'</h3>';
      echo '<table class="form-table">';
      // Generate the HTML For the settings form.
      $this->generate_settings_html();
      echo '</table>';
    }

    /**
     *  There are no payment fields for onepayUS, but we want to show the description if set.
     **/
    function payment_fields(){
      if($this->description) {
        echo wpautop(wptexturize(__($this->description, 'monepayus')));
      }

      global $wp;
      $current_url = home_url(add_query_arg(array(), $wp->request));
      if(isset($_SESSION['deposit_enabled'] ) && $_SESSION['deposit_enabled']  == true){
        $amount = WC()->cart->deposit_info['deposit_amount'];
      }
      else{
        $amount = WC_Payment_Gateway::get_order_total();
      }
      $order_no = "";
      if(isset($_GET['process']) && !empty($_GET['process'])){
        $order_no = $_GET['process'];
      }


      echo '<div id="payment-form-wrapper"><form id ="megapayForm" name="megapayForm" method="post" class="form-inline">
								<input type="hidden" name="merId" value="'.$this->merchant_id.'">
                <input type="hidden" name="currency" value="VND">
                <input type="hidden" name="invoiceNo" value="'.$order_no.'">
                <input id="goodsAmount" type="hidden" name="goodsAmount" value="'.$amount.'" class="form-control">
                <input type="hidden" name="goodsNm" value="'."goodsNm_" . rand().'">
                <input type="hidden" name="payType" value="NO"> <!-- IC: Visa, DC: the ATM, VA: Virtual Account -->
                <input type="hidden" name="buyerFirstNm" value="'."buyerFirstNm" . rand().'">
                <input type="hidden" name="buyerLastNm" value="'."buyerLastNm" . rand().'">
                <input type="hidden" id="callBackUrl" name="callBackUrl" value="'.$current_url.'/wc-api/wc_epayus/">
                <input type="hidden" name="notiUrl" value="'.$current_url.'/wc-api/notify/">
                <input type="hidden" name="reqDomain" value="https://www.pharmacity.vn">
                <input type="hidden" name="vat" value="0">
                <input type="hidden" name="fee" value="0" id="fee">
                <input type="hidden" name="notax" value="123456789">
                <input type="hidden" name="description" value="'."description_" . rand().'">
                <input type="hidden" name="merchantToken" value="">
              <!--  <input type="hidden" name="reqServerIP" value="--><?php //echo $_SERVER[\'REMOTE_ADDR\'];  ?><!--">-->
              <!--  <input type="hidden" name="reqClientVer" value="1">-->
                <input type="hidden" name="userIP" value="'.$_SERVER["REMOTE_ADDR"].'">
              <!--  <input type="hidden" name="userSessionID" value="--><?php //echo "userSessionID_" . rand();  ?><!--">-->
              <!--  <input type="hidden" name="userAgent" value="--><?php //echo $_SERVER[\'HTTP_USER_AGENT\'];  ?><!--">-->
                <input type="hidden" name="userLanguage" value="VN">
                <input type="hidden" name="timeStamp" value="">
                <input type="hidden" name="merTrxId" value="">
                <input type="hidden" name="userFee" id="userFee" value="0">
                <input type="hidden" name="amount" value="0">
                <input type="hidden" name="windowColor" value="#1b74e7">
                <input type="hidden" name="windowType" value=""> <!-- 0:desktop, 1:mobile -->
                <input type="hidden" name="vaStartDt" value="'.date("Ymd", time()) . "000000" .'">
                <input type="hidden" name="vaEndDt" value="'.date("Ymd", time()) . "235959" .'">
                <input type="hidden" name="vaCondition" value="03">
							</form></div>';

    }

    /**
     * Process the payment and return the result
     **/
    function process_payment( $order_id ) {
      global $woocommerce;

      // we need it to get any order detailes
      $order = wc_get_order( $order_id );

      $status = $order->get_status();
      $method = $order->get_payment_method();


      /*
       * Your API interaction could be built with wp_remote_post()
       */
      //$response = $_POST;

      if($status =='pending' && $method =='epay'){
        global $wp;
        return array(
          'result' => 'success',
          'redirect' => home_url(add_query_arg(array('process' => $order_id), '/checkout'))
        );
      }
      else{
        return array(
          'result' => 'success',
          'redirect' => $this->get_return_url( $order )
        );
      }
    }


    function thankyou_page( $order_id ) {

      // Return to site after checking out with OnePAY
      // Note this has not been fully-tested
      global $woocommerce;

      $order = new WC_Order( $order_id );

      $status = $order->get_status();

      $transStatus = "";
      if(!isset($_GET['resultCd']) || empty($_GET['resultCd']) && $status =='processing'){
        $transStatus = '<h1 class = "entry-title" style="color:green;">Thanh toán thành công.</h1>';
        WC()->cart->empty_cart();
      }else{
        $tranDesc = $this->getResponseDescription($_GET['resultCd']);
        $transStatus = '<h1 class = "entry-title" style="color:red;">Giao dich thất bại-'.$tranDesc.'</h1>';
      }
      print $transStatus;
    }

    function showMessage($content){
      return '<div class="box '.$this->msg['class'].'-box">'.$this->msg['message'].'</div>'.$content;
    }

    // @return String containing the appropriate description
    //
    function getResponseDescription($responseCode)
    {

      switch ($responseCode) {
        case "00_000" :
          $result = "Giao dịch thành công";
          break;
        case "FL_900" :
          $result = "Lỗi kết nối";
          break;
        case "FL_901" :
          $result = "Lỗi kết nối socket";
          break;
        case "FL_902" :
          $result = "Có lỗi xảy ra trong quá trình xử lý";
          break;
        case "FL_903" :
          $result = "Lỗi kết nối socket quá thời gian quy định";
          break;
        case "OR_101" :
          $result = "MerId không hợp lệ hoặc merchant chưa được đăng ký thông tin. Liên hệ với trung tâm dịch vụ khách hàng để biết thêm thông tin";
          break;
        case "OR_102" :
          $result = "Hình thức thanh toán này không tồn tại hoặc chưa được kích hoạt. Liên hệ với trung tâm dịch vụ khách hàng để biết thêm thông tin";
          break;
        case "OR_103" :
          $result = "Mã tiền tệ chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [currencyCode] được định nghĩa";
          break;
        case "OR_104" :
          $result = "Tên thành phố người mua chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [buyerCity] được định nghĩa";
          break;
        case "OR_105" :
          $result = "Mã hóa đơn chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [invoiceNo] được định nghĩa";
          break;
        case "OR_106" :
          $result = "Tên hàng hóa chưa được định nghĩa hoặc sai định dạng. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [goodsNm] được định nghĩa";
          break;
        case "OR_107" :
          $result = "Tên hoặc họ người mua chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [buyerFirstNm] và [buyerLastNm] được định nghĩa";
          break;
        case "OR_108" :
          $result = "Số điện thoại người mua chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [buyerPhone] được định nghĩa";
          break;
        case "OR_109" :
          $result = "Địa chỉ email người mua chưa được định nghĩa hoặc chưa đúng định dạng. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [buyerEmail] được định nghĩa đúng";
          break;
        case "OR_110" :
          $result = "Callback URL chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [callbackUrl] được định nghĩa";
          break;
        case "OR_111" :
          $result = "Notification URL chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [notiUrl] được định nghĩa";
          break;
        case "OR_112" :
          $result = "Số tiền thanh toán không hợp lệ. Số tiền chỉ nên là số không có phần thập phân";
          break;
        case "OR_113" :
          $result = "Chữ ký của merchant không hợp lệ. Liên hệ với trung tâm dịch vụ khách hàng để biết thêm thông tin";
          break;
        case "OR_114" :
          $result = "Số tiền thanh toán phải lớn hơn 0. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [amount] được định nghĩa";
          break;
        case "OR_115" :
          $result = "Lỗi trường flag để xác định merchant có kiểm tra order no trùng lặp hay không bị null";
          break;
        case "OR_116" :
          $result = "Số hóa đơn bị trùng lặp";
          break;
        case "OR_117" :
          $result = "Có lỗi xảy ra trong quá trình thực hiện nghiệp vụ";
          break;
        case "OR_118" :
          $result = "Lỗi do 1 trong các nguyên nhân: Request domain chưa được định nghĩa hoặc Tổng giá trị món hàng và phí merchant không bằng tổng giá trị giao dịch thanh toán hoặc MerchantId do merchant gửi lên bị null hoặc Thông tin merchantId không khớp (Chức năng truy vấn thông tin giao dịch";
          break;
        case "OR_119" :
          $result = "";
          break;
        case "OR_120" :
          $result = "Lỗi trạng thái của merchant (Merchant không hoạt động)";
          break;
        case "OR_123" :
          $result = "Lỗi merchant chưa được khai báo trên hệ thống";
          break;
        case "OR_124" :
          $result = "Lỗi trạng thái của merchant (Merchant không hoạt động)";
          break;
        case "OR_125" :
          $result = "Merchant không được đăng ký phương thức thanh toán này hoặc thời gian thanh toán Cybersource chưa được định nghĩa";
          break;
        case "OR_126" :
          $result = "Loại cổng thanh toán chưa được thiết lập";
          break;
        case "OR_127" :
          $result = "Lỗi khi kiểm tra hạn mức áp dụng của merchant";
          break;
        case "OR_128" :
          $result = "Số tiền thanh toán vượt quá định mức giới hạn";
          break;
        case "OR_130" :
          $result = "Trường thông tin xác định merchant là online hay offline chưa được định nghĩa. Xin vui lòng kiểm tra các tham số được yêu cầu và đảm bảo trường [merType] được định nghĩa";
          break;
        case "OR_131" :
          $result = "Loại merchant online này hiện tại chưa được kích hoạt";
          break;
        case "OR_132" :
          $result = "Loại merchant offline này hiện tại chưa được kích hoạt";
          break;
        case "OR_133" :
          $result = "Thông tin hợp đồng chưa được định nghĩa";
          break;
        case "OR_134" :
          $result = "Sai số tiền";
          break;
        case "OR_135" :
          $result = "Số tiền hàng chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [goodsAmount] được định nghĩa";
          break;
        case "OR_136" :
          $result = "Cước phí user chưa được định nghĩa. Xin vui lòng kiểm tra lại các tham số được yêu cầu của bạn và đảm bảo trường [userFee] được định nghĩa";
          break;
        case "OR_140" :
          $result = "Không tìm thấy giao dịch";
          break;
        case "OR_141" :
          $result = "Địa chỉ người mua không được để trống";
          break;
        case "OR_142" :
          $result = "Bang/tỉnh thành người mua không được để trống khi đất nước là 'us' hoặc 'ca'";
          break;
        case "OR_143" :
          $result = "Quốc gia người mua không được để trốngd";
          break;
        case "OR_147" :
          $result = "escription không hợp lệ";
          break;
        case "DC_101" :
          $result = "Lỗi khi kiểm tra các trường thông tin gửi lên cũng như trả về từ NAPAS";
          break;
        case "DC_102" :
          $result = "Mã giao dịch chưa được tạo";
          break;
        case "DC_103" :
          $result = "Giao dịch đã tồn tại. Xin hãy tạo giao dịch mới";
          break;
        case "DC_104" :
          $result = "Số hóa đơn bị null. Xin hãy đảm bảo trường [invoiceNo] đã được khai báo";
          break;
        case "DC_105" :
          $result = "Lỗi dữ liệu bị null";
          break;
        case "DC_110" :
          $result = "Trường hình thức thanh toán không xác định. Liên hệ với nhà cung cấp Megapay để có thêm thông tin";
          break;
        case "DC_112" :
          $result = "Lỗi khi cập nhật hoặc thêm dữ liệu vào các bảng liên quan tới giao dịch ATM";
          break;
        case "99" :
          $result = "Token không hợp ";
          break;
        default  :
          $result = "Giao dịch thanh toán thất ";
      }
      return $result;
    }

  }


  function woocommerce_add_epayUS_gateway($methods) {
    $methods[] = 'WC_epayUS';
    return $methods;
  }

  add_filter('woocommerce_payment_gateways', 'woocommerce_add_epayUS_gateway' );



  function token_generate() {

    $post = $_POST;
    if($post){
      $type = $post['type'];
      $encodeKey = ENCODE_KEY;
      $timeStamp = date('YmdHis');

      if ($type == 1){
        // create merchant token
        $domain = MEGAPAY_URL;
        $merId = MER_ID;
        $merTrxId = 'MGP'.$timeStamp;
        $amount = $post['amount'];
        $order_id = $post['order_id'];

        $order = wc_get_order( $order_id );

        if($order){
          $order->add_order_note('Processing Transaction Id: '.$merTrxId);
        }

        $name = $order->get_billing_last_name();


        $str = $timeStamp . $merTrxId . $merId . $amount . $encodeKey;

        // encrypt
        $token = hash('sha256', $str);

        echo json_encode(array('success' => true, 'token'  => $token, 'timeStamp' => $timeStamp, 'merTrxId' => $merTrxId, 'domain' => $domain , 'name' => $name));exit;
      } elseif ($type == 2){
        // create merchant token
        $merTrxId = $post['merTrxId'];
        $merId = $post['merId'];

        $str = $timeStamp . $merTrxId . $merId . $encodeKey;

        // encrypt
        $token = hash('sha256', $str);

        echo json_encode(array('success' => true, 'token'  => $token, 'timeStamp' => $timeStamp));
      } else {
        // create merchant token
        $trxId = $post['trxId'];
        $merTrxId = $post['merTrxId'];
        $merId = $post['merId'];
        $amount = $post['amount'];

        $str = $timeStamp . $merTrxId . $trxId  . $merId . $amount . $encodeKey;

        // encrypt
        $token = hash('sha256', $str);



        echo json_encode(array('success' => true, 'token'  => $token, 'timeStamp' => $timeStamp, 'cancelPw' => MEGAPAY_CANCEL_PW));
      }
    }
  }
  add_action( 'wp_ajax_nopriv_token_generate', 'token_generate' );
  add_action( 'wp_ajax_token_generate', 'token_generate' );

  function remove_order_fail(){

    $post = $_POST;
    if($post){
      $order_id = $post['order_id'];
      $order = wc_get_order( $order_id );
      $order->update_status( 'cancelled' );
      $order->add_order_note( __( 'Payment Log - User closed the payment popup', 'woocommerce' ) );
      echo json_encode(array('success' => true));exit;
    }

  }
  add_action( 'wp_ajax_nopriv_remove_order_fail', 'remove_order_fail' );
  add_action( 'wp_ajax_remove_order_fail', 'remove_order_fail' );

  function checkToken($data)
  {
    $timeStamp = $data['timeStamp'];
    $merTrxId = $data['merTrxId'];
    $trxId = $data['trxId'];
    $amount = $data['amount'];

    $str = $timeStamp . $merTrxId . $trxId . MER_ID . $amount . ENCODE_KEY;

    $token = hash('sha256', $str);

    $tokenResponse = $data['merchantToken'];

    if ($token != $tokenResponse) {
      return false;
    }

    return true;
  }

}

