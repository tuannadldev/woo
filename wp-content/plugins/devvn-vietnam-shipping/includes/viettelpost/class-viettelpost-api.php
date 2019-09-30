<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('ViettelPostShipping')) {

    class ViettelPostShipping
    {
        protected static $_instance = null;

        public $_VTPostName = 'devvn_VTPost_option';
        public $_VTPostGroup = 'devvn-VTPost-options-group';
        public $_VTPostDefaultOptions = array(
            'username'   =>  '',
            'userpass'   =>  '',
            'type_service'   =>  1,
            'all_service'   =>  array(),
            'extra_service'   =>  array(),
            'product_type'   =>  'HH',
        );

        public $_VTPostHubsName = 'devvn_VTPost_Hubs_option';
        public $_VTPostHubsGroup = 'devvn-VTPost-Hubs-options-group';
        public $_VTPostHubsDefaultOptions = array(
            'listhubs'   =>  '',
        );

        public $_VTPostWebhookName = 'devvn_VTPost_Webhook_option';
        public $_VTPostWebhookGroup = 'devvn-VTPost-Webhook-options-group';
        public $_VTPostWebhookDefaultOptions = array(
            'token'   =>  '',
        );

        protected $api_link = 'https://api.viettelpost.vn/';
        public $login_infor_options = 'vtpost_login_infor';
        public $vtpost_allhubs = 'vtpost_allhubs';

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct(){

            global $vtpost_user, $sync_to_viettelpost, $vtpost_fullhubs;
            $vtpost_user = $this->vtpost_user_func();
            $vtpost_fullhubs = $this->vtpost_fullhubs_func();

            include 'sync_tinhthanh.php';

            add_action( 'admin_init', array( $this, 'register_mysettings') );
            add_action( 'vn_shipping_tabs_nav', array( $this, 'vt_vn_shipping_tabs'));
            add_action( 'vn_shipping_tabs_content_viettelpost', array( $this, 'viettelpost_content_tabs'));

            add_action( 'wp_ajax_vtpost_login', array( $this, 'vtpost_login_func') );
            add_action( 'wp_ajax_vtpost_sync_hubs', array( $this, 'vtpost_sync_hubs_func') );
            add_action( 'wp_ajax_vtpost_get_service', array( $this, 'vtpost_get_service_func') );
            add_action( 'wp_ajax_creat_order_html', array( $this, 'ajax_creat_order_html_func') );
            add_action( 'wp_ajax_vtpost_creat_order', array( $this, 'vtpost_creat_order_func') );
            //add_action( 'wp_ajax_vtpost_update_order', array( $this, 'vtpost_update_order_func') );
            add_action( 'wp_ajax_vtpost_cancel_order', array( $this, 'vtpost_cancel_order_func') );
            add_action( 'wp_ajax_creat_order_calc', array( $this, 'creat_order_calc_func') );
            //add_action( 'wp_ajax_nopriv_vtpost_webhook', array( $this, 'webhook_func') );

            add_action( 'add_meta_boxes', array($this, 'vtpost_order_action') );

            add_action('vtpost_login_daily', array($this, 'vtpost_login_daily_func'));

        }

        function register_mysettings(){
            register_setting($this->_VTPostGroup, $this->_VTPostName);
            register_setting($this->_VTPostHubsGroup, $this->_VTPostHubsName);
        }

        function vtpost_login($field = 'TokenKey'){
            $options = wp_parse_args( get_option($this->login_infor_options) , array(
                'TokenKey'  =>  '',
                'FromSource'  =>  '',
                'Role'  =>  '',
                'UserId'  =>  '',
                'UserName'  =>  '',
                'Partner'  =>  '',
                'Phone'  =>  '',
            ));
            return isset($options[$field]) ? $options[$field] : '';
        }

        function vtpost_user_func(){
            return wp_parse_args(get_option($this->_VTPostName), $this->_VTPostDefaultOptions);
        }

        function vtpost_fullhubs_func(){
            return wp_parse_args(get_option($this->_VTPostHubsName), $this->_VTPostHubsDefaultOptions);
        }

        function is_vtpost_login(){
            global $vtpost_user;
            if($vtpost_user['username'] && $vtpost_user['userpass'] && $this->vtpost_login()) return true;
            return false;
        }

        function vt_vn_shipping_tabs($current_tab){
            ?>
            <a href="?page=devvn-vietnam-shipping&tab=viettelpost" class="nav-tab <?php echo ($current_tab == 'viettelpost') ? 'nav-tab-active' : '' ?>"> <?php _e('ViettelPost', 'devvn-vnshipping'); ?></a>
            <?php
        }

        function viettelpost_content_tabs(){
            include 'options-viettelpost.php';
        }

        function vtpost_login_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "vtpost_login_action")) {
                wp_send_json_error('Lỗi bảo mật');
            }
            $username = isset($_POST['username']) ? esc_attr(wp_slash($_POST['username'])) : '';
            $userpass = isset($_POST['userpass']) ? esc_attr(wp_slash($_POST['userpass'])) : '';
            if(!$username) wp_send_json_error('Username không được để trống!');
            if(!$userpass) wp_send_json_error('Password không được để trống!');
            $data = array(
                "USERNAME" => $username,
                "PASSWORD" => $userpass,
                "SOURCE" => 0
            );
            $result = $this->api_vtpost_login($data);

            $schedule = wp_get_schedule( 'vtpost_login_daily' );
            if(!$schedule){
                wp_schedule_event(time() + 86400, 'daily', 'vtpost_login_daily');
            }else{
                wp_clear_scheduled_hook( 'vtpost_login_daily' );
                wp_schedule_event(time() + 86400, 'daily', 'vtpost_login_daily');
            }

            if(!$result['error']){
                wp_send_json_success($result['mess']);
            }else{
                wp_send_json_error($result['mess']);
            }
            die();
        }

        function api_vtpost_login($data = array()){

            global $vtpost_user;

            $out = array(
                'error' => true,
                'mess'  =>  ''
            );

            if(!$data || !is_array($data)) {
                if($vtpost_user['username'] && $vtpost_user['userpass']) {
                    $data = array(
                        "USERNAME" => $vtpost_user['username'],
                        "PASSWORD" => $vtpost_user['userpass'],
                        "SOURCE" => 0
                    );
                }else{
                    return $out;
                }
            };

            $response = wp_remote_post(esc_url_raw($this->api_link . 'api/user/Login'), array(
                'headers' => array(
                    'Content-Type' => 'application/json'
                ),
                'body' => json_encode($data)
            ));

            $response_code = wp_remote_retrieve_response_code( $response );
            if($response_code == 200 && !is_wp_error($response)) {
                $response_body = json_decode(wp_remote_retrieve_body($response), true);
                if(isset($response_body['error']) && $response_body['error']){
                    if(isset($response_body['status']) && $response_body['status'] == 202){
                        $out['mess'] = 'Bạn đã thực hiện đăng nhập sai nhiều lần. IP của bạn đã bị TẠM khóa. Vui lòng thử lại sau!';
                    }else{
                        $out['mess'] = $response_body['message'];
                    }
                }else {
                    if (get_option($this->login_infor_options) !== false) {
                        update_option($this->login_infor_options, $response_body, 'no');
                    } else {
                        add_option($this->login_infor_options, $response_body, '', 'no');
                    }
                    $this->api_vtpost_getHubs();
                    $out = array(
                        'error' => false,
                        'mess'  =>  'Đăng nhập thành công! Ấn okie để lưu thông tin đăng nhập.'
                    );
                }
            }else{
                $out['mess'] =  'Lỗi kết nối';
            }
            return $out;
        }

        function api_vtpost_getHubs(){

            $again = 1;

            if(!$this->vtpost_login())  return false;

            $response = wp_remote_post(esc_url_raw($this->api_link . 'api/setting/listInventory'), array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Token' => $this->vtpost_login()
                )
            ));

            $response_code = wp_remote_retrieve_response_code( $response );
            if($response_code == 200 && !is_wp_error($response)) {
                $response_body = json_decode(wp_remote_retrieve_body($response), true);
                if(isset($response_body['error']) && $response_body['error']) {
                    if(isset($response_body['status']) && $response_body['status'] == 201 && $again <= 3){
                        $result = $this->api_vtpost_login();
                        if(!$result['error']){
                            $again++;
                            $result = $this->api_vtpost_getHubs();
                        }
                    }else{
                        return false;
                    }
                }else{
                    if (get_option($this->vtpost_allhubs) !== false) {
                        update_option($this->vtpost_allhubs, $response_body, 'no');
                    } else {
                        add_option($this->vtpost_allhubs, $response_body, '', 'no');
                    }
                    return $response_body;
                }
            }
            return false;
        }

        function get_allhubs(){
            $all_hub = get_option($this->vtpost_allhubs);
            if(!$all_hub){
                $all_hub = $this->api_vtpost_getHubs();
            }
            return $all_hub;
        }

        function get_name_city_vt($cityID){
            global $sync_to_viettelpost;
            $city_code = '';
            foreach($sync_to_viettelpost as $k=>$v){
                if($v['PROVINCE_ID'] == $cityID){
                    $city_code = $k;
                }
            }
            return vn_shipping()->get_name_city($city_code);
        }

        function get_city_vt($cityID, $field = 'PROVINCE_ID'){
            global $sync_to_viettelpost;
            return isset($sync_to_viettelpost[$cityID][$field]) ? $sync_to_viettelpost[$cityID][$field] : '';
        }

        function vtpost_sync_hubs_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "vtpost_sync_hubs_action")) {
                wp_send_json_error('Lỗi bảo mật');
            }
            $result = $this->api_vtpost_getHubs();
            if(is_array($result) && !empty($result)){
                wp_send_json_success('Cập nhật thành công');
            }elseif(is_array($result) && empty($result)){
                wp_send_json_error('Không có kho nào trên ViettelPost');
            }
            wp_send_json_error('Lỗi');
            die();
        }

        function vtp_city_id_get_city($citiID = ''){
            global $sync_to_viettelpost;
            if($sync_to_viettelpost && is_array($sync_to_viettelpost)){
                foreach($sync_to_viettelpost as $k=>$item){
                    if(isset($item['PROVINCE_ID']) && $item['PROVINCE_ID'] == $citiID){
                        return $k;
                    }
                }
            }
            return false;
        }

        function get_hubs_near($city_customer_id = '', $field = 'city'){
            global $vtpost_fullhubs;
            if(!$city_customer_id) return false;
            $main_hub = $this->get_main_hubs();
            $city_customer_id = $this->vtp_city_id_get_city($city_customer_id);
            $listhubs = isset($vtpost_fullhubs['listhubs']) ? $vtpost_fullhubs['listhubs'] : array();
            $hub_near = vn_shipping()->search_in_array_value($listhubs, $city_customer_id);
            if(in_array($main_hub, $hub_near)) {
                $hub_near = $main_hub;
            }elseif(!empty($hub_near)) {
                $hub_near = $hub_near[0];
            }else{
                $hub_near = $main_hub;
            }
            $hub_near = isset($listhubs[$hub_near]) ? $listhubs[$hub_near] : '';
            if($hub_near) {
                if ($field == 'all') {
                    $hub_near = $hub_near;
                } else {
                    $hub_near = isset($hub_near[$field]) ? $hub_near[$field] : '';
                }
                return $hub_near;
            }else{
                return false;
            }
        }

        function get_main_hubs($field = 'hubid'){
            global $vtpost_fullhubs;
            $listhubs = isset($vtpost_fullhubs['listhubs']) ? $vtpost_fullhubs['listhubs'] : array();
            $mainHub = vn_shipping()->search_in_array($listhubs, 'ismain', 1);
            if(isset($mainHub[0])) {
                if ($field == 'all') {
                    return $mainHub[0];
                } else {
                    return $mainHub[0][$field];
                }
            }else{
                return false;
            }
        }

        function get_hub_by_id($hubID = '', $field = 'city'){
            global $vtpost_fullhubs;
            $listhubs = isset($vtpost_fullhubs['listhubs']) ? $vtpost_fullhubs['listhubs'] : array();
            $mainHub = vn_shipping()->search_in_array($listhubs, 'hubid', $hubID);
            if(isset($mainHub[0])) {
                if ($field == 'all') {
                    return $mainHub[0];
                } else {
                    return $mainHub[0][$field];
                }
            }else{
                return false;
            }
        }

        function findAvailableShipping($package = array(), $hubid = '', $payment_methob = ''){

            if(!$package) return false;

            global $vtpost_user;

            $all_service = $vtpost_user['all_service'];
            $out = array();

            $city = isset($package['destination']['state']) ? intval($this->get_city_vt($package['destination']['state'])) : '';
            $district = isset($package['destination']['city']) ? intval($package['destination']['city']) : '';

            $hub_near = $this->get_hubs_near($city, 'hubid');

            if($hubid){
                $sender_city = $this->get_hub_by_id($hubid);
                $sender_district = $this->get_hub_by_id($hubid, 'district');
                $hub_near = $hubid;
            }
            $cod_amout = isset($package['cart_subtotal']) ? (float) $package['cart_subtotal'] : '';
            if($payment_methob && $payment_methob != 'cod'){
                $cod_amout = 0;
            }

            if($all_service){
                foreach($all_service as $service){
                    if($payment_methob && $payment_methob != 'cod'){
                        if($service == 'SCOD') continue;
                    }
                    $data = array(
                        "SENDER_PROVINCE" => isset($sender_city) ? (int) $sender_city : $this->get_hubs_near($city),
                        "SENDER_DISTRICT" => isset($sender_district) ? (int) $sender_district : $this->get_hubs_near($city, 'district'),

                        "RECEIVER_PROVINCE" => $city,
                        "RECEIVER_DISTRICT" => $district,

                        "PRODUCT_TYPE" => $vtpost_user['product_type'],

                        "ORDER_SERVICE" => $service,
                        "ORDER_SERVICE_ADD" => (!empty($vtpost_user["extra_service"])) ? implode(',', $vtpost_user["extra_service"]) : '',

                        "PRODUCT_WEIGHT" => (float) vn_shipping()->get_cart_contents_weight($package),
                        "PRODUCT_PRICE" => isset($package['cart_subtotal']) ? (float) $package['cart_subtotal'] : '',
                        "MONEY_COLLECTION" => $cod_amout,
                        "PRODUCT_QUANTITY" => (float) vn_shipping()->get_cart_quantity_package($package),

                        "NATIONAL_TYPE" => 1
                    );

                    $response = wp_remote_post(esc_url_raw($this->api_link . 'api/tmdt/getPrice'), array(
                        'headers' => array(
                            'Content-Type' => 'application/json',
                            'Token' => $this->vtpost_login()
                        ),
                        'body' => json_encode($data)
                    ));

                    $response_code = wp_remote_retrieve_response_code( $response );
                    if($response_code == 200 && !is_wp_error($response)) {
                        $response_body = json_decode(wp_remote_retrieve_body($response), true);
                        if(!isset($response_body['error']) || !$response_body['error']) {
                            $response_body['service_id'] = $service;
                            foreach($response_body as $item){
                                if(isset($item['SERVICE_CODE']) && $item['SERVICE_CODE'] == $service){
                                    $response_body['service_name'] = isset($item['SERVICE_NAME']) ? $item['SERVICE_NAME'] : '';
                                }
                            }
                            $response_body['hubid'] = $hub_near;
                            $out[] = $response_body;
                        }
                    }
                }
            }

            return $out;
        }
        function order_findAvailableShipping($order_id = '', $hubid = '', $args = array()){

            if(!$order_id || !$hubid) return false;

            global $vtpost_user;

            $args = wp_parse_args($args, array(
                "PRODUCT_WEIGHT" => 0,
                "PRODUCT_PRICE" => 0,
                "MONEY_COLLECTION" => 0,
                "PRODUCT_QUANTITY" => 0,
                "ORDER_SERVICE_ADD" => '',
                "order_payment" =>  3
            ));

            $all_service = $vtpost_user['all_service'];
            $out = array();

            $customer_infor = vn_shipping()->get_customer_address_shipping($order_id);

            $sender_city = $this->get_hub_by_id($hubid);
            $sender_district = $this->get_hub_by_id($hubid, 'district');
            $hub_near = $hubid;

            if($all_service){
                foreach($all_service as $service){
                    if($args['order_payment'] && $args['order_payment'] == 1){
                        if($service == 'SCOD') continue;
                    }
                    $data = array(
                        "SENDER_PROVINCE" => isset($sender_city) ? (int) $sender_city : $this->get_hubs_near($customer_infor['province']),
                        "SENDER_DISTRICT" => isset($sender_district) ? (int) $sender_district : $this->get_hubs_near($customer_infor['disrict'], 'district'),

                        "RECEIVER_PROVINCE" => $this->get_city_vt($customer_infor['province']),
                        "RECEIVER_DISTRICT" => $customer_infor['disrict'],

                        "PRODUCT_TYPE" => $vtpost_user['product_type'],

                        "ORDER_SERVICE" => $service,
                        "ORDER_SERVICE_ADD" => (isset($args['ORDER_SERVICE_ADD']) && $args['ORDER_SERVICE_ADD'] != '') ? implode(',', $args['ORDER_SERVICE_ADD']) : implode(',', $vtpost_user["extra_service"]),

                        "PRODUCT_WEIGHT" => (float) $args['PRODUCT_WEIGHT'],
                        "PRODUCT_PRICE" => (float) $args['PRODUCT_PRICE'],
                        "MONEY_COLLECTION" => (float) $args['MONEY_COLLECTION'],
                        "PRODUCT_QUANTITY" => (float) $args['PRODUCT_QUANTITY'],

                        "NATIONAL_TYPE" => 1
                    );

                    $response = wp_remote_post(esc_url_raw($this->api_link . 'api/tmdt/getPrice'), array(
                        'headers' => array(
                            'Content-Type' => 'application/json',
                            'Token' => $this->vtpost_login()
                        ),
                        'body' => json_encode($data)
                    ));

                    $response_code = wp_remote_retrieve_response_code( $response );
                    if($response_code == 200 && !is_wp_error($response)) {
                        $response_body = json_decode(wp_remote_retrieve_body($response), true);
                        if(!isset($response_body['error']) || !$response_body['error']) {
                            $response_body['service_id'] = $service;
                            foreach($response_body as $item){
                                if(isset($item['SERVICE_CODE']) && $item['SERVICE_CODE'] == $service){
                                    $response_body['service_name'] = isset($item['SERVICE_NAME']) ? $item['SERVICE_NAME'] : '';
                                }
                            }
                            $response_body['hubid'] = $hub_near;
                            $out[] = $response_body;
                        }
                    }
                }
            }

            return $out;
        }
        function  api_listService($type = 1){
            $data = array(
                'TYPE' => $type
            );
            $name_cache = 'listservice_' . intval($type);
            if ( false === ( $response_body = get_transient( $name_cache ) ) ) {
                $response = wp_remote_post(esc_url_raw($this->api_link . 'api/setting/listService'), array(
                    'headers' => array(
                        'Content-Type' => 'application/json',
                    ),
                    'body' => json_encode($data)
                ));

                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code == 200 && !is_wp_error($response)) {
                    $response_body = json_decode(wp_remote_retrieve_body($response), true);
                    if (isset($response_body['error']) && $response_body['error']) {
                        return false;
                    } else {
                        set_transient( $name_cache, $response_body, 7 * DAY_IN_SECONDS );
                        return $response_body;
                    }
                }
                return false;
            }
            return $response_body;
        }

        function  api_listServiceExtra(){
            $name_cache = 'listservice_extra';
            if ( false === ( $response_body = get_transient( $name_cache ) ) ) {
                $response = wp_remote_get(esc_url_raw($this->api_link . 'api/setting/listServiceExtra'), array(
                    'headers' => array(
                        'Content-Type' => 'application/json',
                    )
                ));

                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code == 200 && !is_wp_error($response)) {
                    $response_body = json_decode(wp_remote_retrieve_body($response), true);
                    if (isset($response_body['error']) && $response_body['error']) {
                        return false;
                    } else {
                        set_transient( $name_cache, $response_body, 7 * DAY_IN_SECONDS );
                        return $response_body;
                    }
                }
                return false;
            }
            return $response_body;
        }

        function vtpost_get_service_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "vtpost_login_action")) {
                wp_send_json_error('Lỗi bảo mật');
            }
            $type = isset($_POST['type']) ? intval($_POST['type']) : 1;
            $listService = $this->api_listService($type);
            if($listService) {
                wp_send_json_success($listService);
            }
            wp_send_json_error();
            die();
        }

        function vtpost_order_action(){
            add_meta_box(
                'vtpost-metabox-id',
                __( 'ViettelPost', 'devvn-vnshipping' ),
                array($this, 'vtpost_order_action_callback'),
                'shop_order',
                'side',
                'high'
            );
        }

        function creat_order_html_func($orderid = '', $make = true){
            if(!$orderid) return false;
            ob_start();
            include 'vtpost-creat-order-html.php';
            echo ob_get_clean();
        }

        function ajax_creat_order_html_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "vtpost_action_nonce_action")) {
                exit("No naughty business please");
            }
            $orderid = isset($_GET['orderid']) ? (int) $_GET['orderid'] : '';
            $make = isset($_GET['make']) ? wc_clean(wp_slash($_GET['make'])) : 1;
            if($orderid) {
                ob_start();
                $this->creat_order_html_func($orderid, $make);
                echo ob_get_clean();
            }
            die();
        }

        function button_update_html($post_id){
            $vtpost_order_number = get_post_meta($post_id, 'vtpost_order_number', true);
            ob_start();
            ?>
            <div class="vtpost_metabox_updae">
                <p><strong>Mã đơn trên ViettelPost:</strong> <?php echo $vtpost_order_number;?></p>
                <?php
                /*
                <a href="<?php echo admin_url('admin-ajax.php?action=creat_order_html&orderid='.$post->ID.'&make=update&nonce='.wp_create_nonce('vtpost_action_nonce_action'));?>" class="button button-primary vtpost_creat_order_popup" data-orderid="<?php echo $post->ID;?>"><?php _e('Cập nhật đơn', 'devvn-vnshipping')?></a>
                */?>
                <button type="button" class="button button-link-delete vtpost_cancel_order" data-ordernumber="<?php echo $vtpost_order_number;?>" data-orderid="<?php echo $post_id;?>" data-nonce="<?php echo wp_create_nonce('cancel_order_action');?>"><?php _e('Hủy đơn hàng', 'devvn-vnshipping');?></button>
            </div>
            <?php
            return ob_get_clean();
        }

        function button_creat_html($post_id){
            ob_start();
            ?>
            <div class="vtpost_metabox_creat">
                <a href="<?php echo admin_url('admin-ajax.php?action=creat_order_html&orderid='.$post_id.'&nonce='.wp_create_nonce('vtpost_action_nonce_action'));?>" class="button button-primary vtpost_creat_order_popup" data-orderid="<?php echo $post_id;?>"><?php _e('Tạo vận đơn', 'devvn-vnshipping')?></a>
            </div>
            <?php
            return ob_get_clean();
        }

        function vtpost_order_action_callback($post){
            $vtpost_order_full_submit = get_post_meta($post->ID, 'vtpost_order_full_submit', true);
            $vtpost_order_full_respon = get_post_meta($post->ID, 'vtpost_order_full_respon', true);
            $vtpost_order_number = get_post_meta($post->ID, 'vtpost_order_number', true);
            ?>
            <div class="vtpost_metabox_wrap">
                <?php if($vtpost_order_number && $vtpost_order_full_respon && $vtpost_order_full_submit):?>
                    <?php echo $this->button_update_html($post->ID);?>
                <?php else:?>
                    <?php echo $this->button_creat_html($post->ID);?>
                <?php endif;?>
            </div>
            <?php
        }

        function vtpost_creat_order_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "creat_order_action")) {
                wp_send_json_error("No naughty business please");
            }
            global $vtpost_fullhubs, $vtpost_user;

            $all_hubs = isset($vtpost_fullhubs['listhubs']) ? $vtpost_fullhubs['listhubs'] : array();
            $orderid = isset($_POST['orderid']) ? intval($_POST['orderid']) : '';
            $hubsID = isset($_POST['hubsID']) ? intval($_POST['hubsID']) : '';

            $prod_price = isset($_POST['prod_price']) ? (float) $_POST['prod_price'] : 0;
            $prod_weight = isset($_POST['prod_weight']) ? (float) $_POST['prod_weight'] : 0;
            $prod_length = isset($_POST['prod_length']) ? (float) $_POST['prod_length'] : 0;
            $prod_width = isset($_POST['prod_width']) ? (float) $_POST['prod_width'] : 0;
            $prod_height = isset($_POST['prod_height']) ? (float) $_POST['prod_height'] : 0;

            $prod_type = isset($_POST['prod_type']) ? sanitize_text_field($_POST['prod_type']) : $vtpost_user['product_type'];
            $order_payment = isset($_POST['order_payment']) ? intval($_POST['order_payment']) : 2;
            $service_id = isset($_POST['service_id']) ? sanitize_text_field($_POST['service_id']) : '';
            $order_service_extra = isset($_POST['service_extras']) ? (array) $_POST['service_extras'] : array();
            $order_note = isset($_POST['order_note']) ? sanitize_textarea_field($_POST['order_note']) : '';

            $cod_amout = isset($_POST['cod_amout']) ? sanitize_text_field($_POST['cod_amout']) : 0;

            $current_time = date_i18n('d/m/Y H:i:s', strtotime( 'NOW + 1 hour', current_time( 'timestamp' ) ));
            $delivery_date = isset($_POST['delivery_date']) ? sanitize_text_field($_POST['delivery_date']) : $current_time;

            $customer_infor = vn_shipping()->get_customer_address_shipping($orderid);
            $product_arg = vn_shipping()->get_product_args($orderid);

            $name_prod = array();
            $quantity_prod = 0;
            if($product_arg){
                foreach($product_arg as $prod){
                    $name_prod[] = $prod['name'];
                    $quantity_prod += $prod['quantity'];
                }
            }

            $data = array(
                "ORDER_NUMBER" => $orderid,
                "GROUPADDRESS_ID" => $hubsID,
                "CUS_ID" => isset($all_hubs[$hubsID]['cus_id']) ? (float) $all_hubs[$hubsID]['cus_id'] : '',
                "DELIVERY_DATE" => $delivery_date,

                "SENDER_FULLNAME" => isset($all_hubs[$hubsID]['name']) ? $all_hubs[$hubsID]['name'] : '',
                "SENDER_ADDRESS" => isset($all_hubs[$hubsID]['address']) ? $all_hubs[$hubsID]['address'] : '',
                "SENDER_PHONE" => isset($all_hubs[$hubsID]['phone']) ? $all_hubs[$hubsID]['phone'] : '',
                "SENDER_EMAIL" => '',
                "SENDER_WARD" => isset($all_hubs[$hubsID]['wards']) ? (int)$all_hubs[$hubsID]['wards'] : '',
                "SENDER_DISTRICT" => isset($all_hubs[$hubsID]['district']) ? (int) $all_hubs[$hubsID]['district'] : '',
                "SENDER_PROVINCE" => isset($all_hubs[$hubsID]['city']) ? (int) $all_hubs[$hubsID]['city'] : '',
                "SENDER_LATITUDE" => 0,
                "SENDER_LONGITUDE" => 0,

                "RECEIVER_FULLNAME" => isset($customer_infor['name']) ? $customer_infor['name'] : '',
                "RECEIVER_ADDRESS" => isset($customer_infor['address']) ? $customer_infor['address'] : '',
                "RECEIVER_PHONE" => isset($customer_infor['phone']) ? $customer_infor['phone'] : '',
                "RECEIVER_EMAIL" => isset($customer_infor['email']) ? $customer_infor['email'] : '',
                "RECEIVER_WARD" => isset($customer_infor['ward']) ? intval($customer_infor['ward']) : '',
                "RECEIVER_DISTRICT" => isset($customer_infor['disrict']) ? intval($customer_infor['disrict']) : '',
                "RECEIVER_PROVINCE" => isset($customer_infor['province']) ? intval($this->get_city_vt($customer_infor['province'])) : '',
                "RECEIVER_LATITUDE" => 0,
                "RECEIVER_LONGITUDE" => 0,

                "PRODUCT_NAME" => implode('/', $name_prod),
                "PRODUCT_DESCRIPTION" => "",
                "PRODUCT_QUANTITY" => $quantity_prod,
                "PRODUCT_PRICE" => $prod_price,
                "PRODUCT_WEIGHT" => $prod_weight,
                "PRODUCT_LENGTH" => $prod_length,
                "PRODUCT_WIDTH" => $prod_width,
                "PRODUCT_HEIGHT" => $prod_height,
                "PRODUCT_TYPE" => $prod_type,

                "ORDER_PAYMENT" => $order_payment,
                "ORDER_SERVICE" => $service_id,
                "ORDER_SERVICE_ADD" => implode(',', $order_service_extra),
                "ORDER_VOUCHER" => "", //Todo voucher
                "ORDER_NOTE" => $order_note,

                "MONEY_COLLECTION" => $cod_amout,
                "MONEY_TOTALFEE" => 0,
                "MONEY_FEECOD" => 0,
                "MONEY_FEEVAS" => 0,
                "MONEY_FEEINSURRANCE" => 0,
                "MONEY_FEE" => 0,
                "MONEY_FEEOTHER" => 0,
                "MONEY_TOTALVAT" => 0,
                "MONEY_TOTAL" => 0
            );
            //wp_send_json_success($data);
            $response = wp_remote_post(esc_url_raw($this->api_link . 'api/tmdt/InsertOrder'), array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Token' =>  $this->vtpost_login()
                ),
                'body' => json_encode($data)
            ));

            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code == 200 && !is_wp_error($response)) {
                $response_body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($response_body['error']) && $response_body['error']) {
                    wp_send_json_error($response_body['message']);
                } else {
                    add_post_meta($orderid, 'vtpost_order_full_submit', $data);
                    add_post_meta($orderid, 'vtpost_order_full_respon', $response_body);
                    $ORDER_NUMBER = isset($response_body['data']['ORDER_NUMBER']) ? $response_body['data']['ORDER_NUMBER'] : '';
                    add_post_meta($orderid, 'vtpost_order_number', $ORDER_NUMBER);
                    wp_send_json_success(array(
                        'fragments' => array(
                            '.vtpost_metabox_wrap'  =>  $this->button_update_html($orderid)
                        ),
                        'order_number' => $ORDER_NUMBER,
                        'mess'  =>  'Đăng đơn thành công!',
                    ));
                }
            }
            wp_send_json_error(__('Lỗi khi tạo đơn. Vui lòng thử lại sau!', 'devvn-vnshipping'));
            die();
        }
        function vtpost_update_order_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "creat_order_action")) {
                wp_send_json_error("No naughty business please");
            }
            global $vtpost_fullhubs;

            $vtpost_options = wp_parse_args(get_option(vn_shipping()->_VTPostName),vn_shipping()->_VTPostDefaultOptions);

            $all_hubs = isset($vtpost_fullhubs['listhubs']) ? $vtpost_fullhubs['listhubs'] : array();
            $orderid = isset($_POST['orderid']) ? intval($_POST['orderid']) : '';
            $hubsID = isset($_POST['hubsID']) ? intval($_POST['hubsID']) : '';

            $prod_price = isset($_POST['prod_price']) ? (float) $_POST['prod_price'] : 0;
            $prod_weight = isset($_POST['prod_weight']) ? (float) $_POST['prod_weight'] : 0;
            $prod_length = isset($_POST['prod_length']) ? (float) $_POST['prod_length'] : 0;
            $prod_width = isset($_POST['prod_width']) ? (float) $_POST['prod_width'] : 0;
            $prod_height = isset($_POST['prod_height']) ? (float) $_POST['prod_height'] : 0;

            $prod_type = isset($_POST['prod_type']) ? sanitize_text_field($_POST['prod_type']) : $vtpost_options['product_type'];
            $order_payment = isset($_POST['order_payment']) ? intval($_POST['order_payment']) : 2;
            $service_id = isset($_POST['service_id']) ? sanitize_text_field($_POST['service_id']) : '';
            $order_service_extra = isset($_POST['service_extras']) ? (array) $_POST['service_extras'] : array();
            $order_note = isset($_POST['order_note']) ? sanitize_textarea_field($_POST['order_note']) : '';

            $cod_amout = isset($_POST['cod_amout']) ? sanitize_text_field($_POST['cod_amout']) : 0;
            $order_number = isset($_POST['order_number']) ? sanitize_text_field($_POST['order_number']) : '';

            $customer_infor = vn_shipping()->get_customer_address_shipping($orderid);
            $product_arg = vn_shipping()->get_product_args($orderid);

            $name_prod = array();
            $quantity_prod = 0;
            if($product_arg){
                foreach($product_arg as $prod){
                    $name_prod[] = $prod['name'];
                    $quantity_prod += $prod['quantity'];
                }
            }

            $data = array(
                "TYPE" => 5,
                "ORDER_NUMBER" => $order_number,
                "GROUPADDRESS_ID" => $hubsID,
                "CUS_ID" => isset($all_hubs[$hubsID]['cus_id']) ? (float) $all_hubs[$hubsID]['cus_id'] : '',
                "DELIVERY_DATE" => date('d/m/Y H:i:s', time() + 172800), //Todo Thay đổi lại phần này cho khách đổi

                "SENDER_FULLNAME" => isset($all_hubs[$hubsID]['name']) ? $all_hubs[$hubsID]['name'] : '',
                "SENDER_ADDRESS" => isset($all_hubs[$hubsID]['address']) ? $all_hubs[$hubsID]['address'] : '',
                "SENDER_PHONE" => isset($all_hubs[$hubsID]['phone']) ? $all_hubs[$hubsID]['phone'] : '',
                "SENDER_EMAIL" => '',
                "SENDER_WARD" => isset($all_hubs[$hubsID]['wards']) ? (int)$all_hubs[$hubsID]['wards'] : '',
                "SENDER_DISTRICT" => isset($all_hubs[$hubsID]['district']) ? (int) $all_hubs[$hubsID]['district'] : '',
                "SENDER_PROVINCE" => isset($all_hubs[$hubsID]['city']) ? (int) $all_hubs[$hubsID]['city'] : '',
                "SENDER_LATITUDE" => 0,
                "SENDER_LONGITUDE" => 0,

                "RECEIVER_FULLNAME" => isset($customer_infor['name']) ? $customer_infor['name'] : '',
                "RECEIVER_ADDRESS" => isset($customer_infor['address']) ? $customer_infor['address'] : '',
                "RECEIVER_PHONE" => isset($customer_infor['phone']) ? $customer_infor['phone'] : '',
                "RECEIVER_EMAIL" => isset($customer_infor['email']) ? $customer_infor['email'] : '',
                "RECEIVER_WARD" => isset($customer_infor['ward']) ? intval($customer_infor['ward']) : '',
                "RECEIVER_DISTRICT" => isset($customer_infor['disrict']) ? intval($customer_infor['disrict']) : '',
                "RECEIVER_PROVINCE" => isset($customer_infor['province']) ? intval($this->get_city_vt($customer_infor['province'])) : '',
                "RECEIVER_LATITUDE" => 0,
                "RECEIVER_LONGITUDE" => 0,

                "PRODUCT_NAME" => implode('/', $name_prod),
                "PRODUCT_DESCRIPTION" => "",
                "PRODUCT_QUANTITY" => $quantity_prod,
                "PRODUCT_PRICE" => $prod_price,
                "PRODUCT_WEIGHT" => $prod_weight,
                "PRODUCT_LENGTH" => $prod_length,
                "PRODUCT_WIDTH" => $prod_width,
                "PRODUCT_HEIGHT" => $prod_height,
                "PRODUCT_TYPE" => $prod_type,

                "ORDER_PAYMENT" => $order_payment,
                "ORDER_SERVICE" => $service_id,
                "ORDER_SERVICE_ADD" => implode(',', $order_service_extra),
                "ORDER_VOUCHER" => "", //Todo voucher
                "ORDER_NOTE" => $order_note,

                "MONEY_COLLECTION" => $cod_amout,
                "MONEY_TOTALFEE" => 0,
                "MONEY_FEECOD" => 0,
                "MONEY_FEEVAS" => 0,
                "MONEY_FEEINSURRANCE" => 0,
                "MONEY_FEE" => 0,
                "MONEY_FEEOTHER" => 0,
                "MONEY_TOTALVAT" => 0,
                "MONEY_TOTAL" => 0
            );
            //wp_send_json_success($data);
            $response = wp_remote_post(esc_url_raw($this->api_link . 'api/tmdt/UpdateOrder'), array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Token' =>  $this->vtpost_login()
                ),
                'body' => json_encode($data)
            ));

            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code == 200 && !is_wp_error($response)) {
                $response_body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($response_body['error']) && $response_body['error']) {
                    wp_send_json_error($response_body['message']);
                } else {
                    add_post_meta($orderid, 'vtpost_order_full_submit', $data);
                    add_post_meta($orderid, 'vtpost_order_full_respon', $response_body);
                    $ORDER_NUMBER = isset($response_body['data']['ORDER_NUMBER']) ? $response_body['data']['ORDER_NUMBER'] : '';
                    add_post_meta($orderid, 'vtpost_order_number', $ORDER_NUMBER);
                    wp_send_json_success(array(
                        'fragments' => array(),
                        'order_number' => $ORDER_NUMBER,
                        'mess'  =>  'Cập nhật đơn hàng thành công!',
                    ));
                }
            }
            wp_send_json_error(__('Lỗi khi cập nhật. Vui lòng thử lại sau!', 'devvn-vnshipping'));
            die();
        }

        function vtpost_cancel_order_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "cancel_order_action")) {
                wp_send_json_error("No naughty business please");
            }
            $order_number = isset($_POST['ordernumber']) ? wc_clean($_POST['ordernumber']) : '';
            $order_id = isset($_POST['orderid']) ? intval($_POST['orderid']) : '';
            if($order_number && $order_id) {
                $vtpost_order_number = get_post_meta($order_id, 'vtpost_order_number', true);
                if($vtpost_order_number == $order_number) {
                    $data = array(
                        "TYPE" => 4,
                        "ORDER_NUMBER" => $order_number,
                        "NOTE" => "Khách yêu cầu hủy đơn hàng",
                        "DATE" => ""
                    );
                    $response = wp_remote_post(esc_url_raw($this->api_link . 'api/tmdt/UpdateOrder'), array(
                        'headers' => array(
                            'Content-Type' => 'application/json',
                            'Token' => $this->vtpost_login()
                        ),
                        'body' => json_encode($data)
                    ));

                    $response_code = wp_remote_retrieve_response_code($response);
                    if ($response_code == 200 && !is_wp_error($response)) {
                        $response_body = json_decode(wp_remote_retrieve_body($response), true);
                        if (isset($response_body['error']) && $response_body['error']) {
                            if($response_body['message'] == 'Đơn hàng đã hủy!'){
                                delete_post_meta($order_id, 'vtpost_order_full_submit');
                                delete_post_meta($order_id, 'vtpost_order_full_respon');
                                delete_post_meta($order_id, 'vtpost_order_number');
                                wp_send_json_success(array(
                                    'fragments' => array(
                                        '.vtpost_metabox_wrap' => $this->button_creat_html($order_id)
                                    ),
                                    'mess' => 'Hủy đơn hàng thành công!',
                                ));
                            }else {
                                wp_send_json_error($response_body['message']);
                            }

                        } else {
                            delete_post_meta($order_id, 'vtpost_order_full_submit');
                            delete_post_meta($order_id, 'vtpost_order_full_respon');
                            delete_post_meta($order_id, 'vtpost_order_number');
                            wp_send_json_success(array(
                                'fragments' => array(
                                    '.vtpost_metabox_wrap' => $this->button_creat_html($order_id)
                                ),
                                'mess' => 'Hủy đơn hàng thành công!',
                            ));
                        }
                    }
                }
            }
            wp_send_json_error(__('Lỗi khi hủy đơn hàng. Vui lòng thử lại sau!', 'devvn-vnshipping'));
            die();
        }

        function creat_order_calc_func(){
            if ( !wp_verify_nonce( $_REQUEST['nonce'], "creat_order_action")) {
                wp_send_json_error("No naughty business please");
            }
            global $vtpost_user;

            $orderid = isset($_POST['orderid']) ? intval($_POST['orderid']) : '';
            $hubsID = isset($_POST['hubsID']) ? intval($_POST['hubsID']) : '';

            $prod_price = isset($_POST['prod_price']) ? (float) $_POST['prod_price'] : 0;
            $prod_weight = isset($_POST['prod_weight']) ? (float) $_POST['prod_weight'] : 0;
            $prod_length = isset($_POST['prod_length']) ? (float) $_POST['prod_length'] : 0;
            $prod_width = isset($_POST['prod_width']) ? (float) $_POST['prod_width'] : 0;
            $prod_height = isset($_POST['prod_height']) ? (float) $_POST['prod_height'] : 0;

            $prod_type = isset($_POST['prod_type']) ? sanitize_text_field($_POST['prod_type']) : $vtpost_user['product_type'];
            $order_payment = isset($_POST['order_payment']) ? intval($_POST['order_payment']) : 2;
            $service_id_select = isset($_POST['service_id']) ? sanitize_text_field($_POST['service_id']) : '';
            $order_service_extra = isset($_POST['service_extras']) ? (array) $_POST['service_extras'] : array();
            $order_note = isset($_POST['order_note']) ? sanitize_textarea_field($_POST['order_note']) : '';

            $cod_amout = isset($_POST['cod_amout']) ? sanitize_text_field($_POST['cod_amout']) : 0;

            $shipping = $this->order_findAvailableShipping($orderid, $hubsID, array(
                "PRODUCT_WEIGHT" => $prod_weight,
                "PRODUCT_PRICE" => $prod_price,
                "MONEY_COLLECTION" => $cod_amout,
                "PRODUCT_QUANTITY" => 1,
                "ORDER_SERVICE_ADD" => $order_service_extra,
                "order_payment"    => $order_payment
            ));

            ob_start();
            if($shipping && !empty($shipping)){
                echo '<div id="service_id_list" data-value="'.$service_id_select.'">';
                foreach($shipping as $rate){
                    $service_id = isset($rate['service_id']) ? $rate['service_id'] : '';
                    $hubid = isset($rate['hubid']) ? $rate['hubid'] : '';
                    $service_name = isset($rate['service_name']) ? $rate['service_name'] : '';
                    foreach($rate as $item){
                        if(isset($item['SERVICE_CODE']) && $item['SERVICE_CODE'] == 'ALL'){
                            echo '<label><input type="radio" class="service_id" name="service_id" data-price="'.$item['PRICE'].'" value="'.$service_id.'" '.checked($service_id, $service_id_select,false).'/>'. wc_price($item['PRICE']) .' - '.$service_name.'</label>';
                            continue;
                        }
                    }
                }
                echo '</div>';
            }
            $shipping = ob_get_clean();

            wp_send_json_success(array(
                'fragments' => array(
                    '.list_service' =>  $shipping
                )
            ));
            die();
        }
        function webhook_func(){
            /*$log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
                json_encode($_POST).PHP_EOL.
                json_encode($_GET).PHP_EOL.
                "-------------------------".PHP_EOL;

            file_put_contents( dirname(__FILE__) . '/log.txt', $log, FILE_APPEND);*/
        }


        function vtpost_login_daily_func(){
            global $vtpost_user;
            $username = (isset($vtpost_user['username']) && $vtpost_user['username']) ? esc_attr(wp_slash($vtpost_user['username'])) : '';
            $userpass = (isset($vtpost_user['userpass']) && $vtpost_user['userpass']) ? esc_attr(wp_slash($vtpost_user['userpass'])) : '';
            if($username && $userpass) {
                $data = array(
                    "USERNAME" => $username,
                    "PASSWORD" => $userpass,
                    "SOURCE" => 0
                );
                $this->api_vtpost_login($data);
            }
        }
    }

    function VTPost(){
        return ViettelPostShipping::instance();
    }

    VTPost();

}

add_filter( 'woocommerce_shipping_methods', 'add_vtpost_shipping_method' );
function add_vtpost_shipping_method($methods){
    $methods['VTPost_shipping_method'] = 'WC_VTPost_Shipping_Method';
    return $methods;
}

add_action( 'woocommerce_shipping_init', 'vtpost_shipping_method_init' );
function vtpost_shipping_method_init(){
    if ( ! class_exists( 'WC_VTPost_Shipping_Method' ) ) {
        class WC_VTPost_Shipping_Method extends WC_Shipping_Method {
            public $vtpost_mess = '';
            /**
             * Constructor for your shipping class
             *
             * @access public
             * @return void
             */
            public function __construct() {

                $this->id                 = 'vtpost_shipping_method';
                $this->method_title       = __( 'ViettelPost', 'devvn-vnshipping' );
                $this->method_description = sprintf( __('Tính phí vận chuyển và đồng bộ đơn hàng với ViettelPost. Cài đặt API <a href="%s">tại đây</a>', 'devvn-vnshipping'), admin_url('admin.php?page=devvn-vietnam-shipping&tab=viettelpost') );

                $this->init();

                $this->enabled            = $this->settings['enabled'];

            }

            /**
             * Init your settings
             *
             * @access public
             * @return void
             */
            function init() {
                // Load the settings API
                $this->init_form_fields();
                $this->init_settings();

                // Save settings in admin if you have any defined
                add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
            }

            function init_form_fields() {
                $this->form_fields = array(
                    'enabled' => array(
                        'title'     => __( 'Kích hoạt', 'devvn-vnshipping' ),
                        'type'      => 'checkbox',
                        'label'     => __( 'Kích hoạt tính phí vận chuyển bằng ViettelPost', 'devvn-vnshipping' ),
                        'default'   => 'yes',
                    )
                );
            } // End init_form_fields()

            /**
             * calculate_shipping function.
             *
             * @access public
             * @param mixed $package
             * @return void
             */
            public function calculate_shipping( $package = array() ) {
                $payment_methob = WC()->session->get('chosen_payment_method');
                $shipping = VTPost()->findAvailableShipping($package, '', $payment_methob);
                if($shipping && !empty($shipping)){
                    foreach($shipping as $rate){
                        $service_id = isset($rate['service_id']) ? $rate['service_id'] : '';
                        $hubid = isset($rate['hubid']) ? $rate['hubid'] : '';
                        $service_name = isset($rate['service_name']) ? $rate['service_name'] : '';
                        foreach($rate as $item){
                            if(isset($item['SERVICE_CODE']) && $item['SERVICE_CODE'] == 'ALL'){
                                $rate = array(
                                    'id' => $this->id . '_' . $service_id,
                                    'label' => $service_name,
                                    'cost' => $item['PRICE'],
                                    'calc_tax' => 'per_item',
                                    'meta_data' => array(
                                        'HubID' => $hubid,
                                        'ServiceID' => $service_id,
                                        'type' => 'VTPost'
                                    )
                                );
                                $this->add_rate($rate);
                                continue;
                            }
                        }
                    }
                }
            }

            function devvn_no_shipping_cart(){
                return $this->vtpost_mess;
            }
        }
    }
}