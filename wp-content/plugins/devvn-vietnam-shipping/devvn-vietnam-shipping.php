<?php
/*
 * Plugin Name: Master Shipping by DevVN
 * Plugin URI: https://levantoan.com/san-pham/plugin-ket-noi-viettelpost-voi-woocommerce/
 * Version: 1.0.0
 * Description: Thay đổi giao diện trang checkout cho phù hợp với Việt Nam. Thêm tỉnh thành; quận huyện; xã phường vào trang checkout. Tính phí vận chuyển và đăng đơn lên hệ thống ViettelPost qua API
 * Author: Le Van Toan
 * Author URI: http://levantoan.com
 * Text Domain: devvn-vnshipping
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 3.4.3
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if (
    in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
    && !in_array( 'woo-vietnam-checkout/devvn-woo-address-selectbox.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
) {

include 'cities/tinh_thanhpho.php';

register_activation_hook   ( __FILE__, array( 'Woo_Address_Selectbox_Class', 'on_activation' ) );
register_deactivation_hook ( __FILE__, array( 'Woo_Address_Selectbox_Class', 'on_deactivation' ) );
register_uninstall_hook    ( __FILE__, array( 'Woo_Address_Selectbox_Class', 'on_uninstall' ) );

load_textdomain('devvn-vnshipping', dirname(__FILE__) . '/languages/devvn-vnshipping-' . get_locale() . '.mo');

class Woo_Address_Selectbox_Class
{
    protected static $instance;

	protected $_version = '1.0.0';

	public $_optionName = 'devvn_woo_district';
	public $_optionGroup = 'devvn-district-options-group';
	public $_defaultOptions = array(
	    'active_village'	            =>	'',
        'required_village'	            =>	'',
        'to_vnd'	                    =>	'',
        'remove_methob_title'	        =>	'',
        'freeship_remove_other_methob'  =>  '',
        'khoiluong_quydoi'              =>  '6000',
        'tinhthanh_default'             =>  '01',
        'active_vnd2usd'                =>  0,
        'vnd_usd_rate'                  =>  '22745',
        'vnd2usd_currency'              =>  'USD',
        
        'alepay_support'                =>  0,
        'enable_postcode'               =>  0,
	);

    public $_licenseName = 'devvn_license_option';
    public $_licenseGroup = 'devvn-license-options-group';
    public $_licenseDefaultOptions = array(
        'license_key'   =>  ''
    );

    public static function init(){
        is_null( self::$instance ) AND self::$instance = new self;
        return self::$instance;
    }

	public function __construct(){

        $this->define_constants();

    	add_filter( 'woocommerce_checkout_fields' , array($this, 'custom_override_checkout_fields'), 999999 );
    	add_filter( 'woocommerce_states', array($this, 'vietnam_cities_woocommerce'), 99999 );

    	add_action( 'wp_enqueue_scripts', array($this, 'devvn_enqueue_UseAjaxInWp') );
    	add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

    	add_action( 'wp_ajax_load_diagioihanhchinh', array($this, 'load_diagioihanhchinh_func') );
		add_action( 'wp_ajax_nopriv_load_diagioihanhchinh', array($this, 'load_diagioihanhchinh_func') );

		add_filter('woocommerce_localisation_address_formats', array($this, 'devvn_woocommerce_localisation_address_formats'), 99999 );
		add_filter('woocommerce_order_formatted_billing_address', array($this, 'devvn_woocommerce_order_formatted_billing_address'), 10, 2);

		add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'devvn_after_shipping_address'), 10, 1 );
		add_filter('woocommerce_order_formatted_shipping_address', array($this, 'devvn_woocommerce_order_formatted_shipping_address'), 10, 2);

		add_filter('woocommerce_order_details_after_customer_details', array($this, 'devvn_woocommerce_order_details_after_customer_details'), 10);

		//my account
		add_filter('woocommerce_my_account_my_address_formatted_address', array($this, 'devvn_woocommerce_my_account_my_address_formatted_address'),10,3);
        add_filter( 'woocommerce_default_address_fields' , array($this, 'devvn_custom_override_default_address_fields'), 99999 );
        add_filter('woocommerce_get_country_locale', array($this, 'devvn_woocommerce_get_country_locale'), 99999);

		//More action
        add_filter( 'default_checkout_billing_country', array($this, 'change_default_checkout_country'), 9999 );
        //add_filter( 'default_checkout_billing_state', array($this, 'change_default_checkout_state'), 99 );

		//Options
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_mysettings') );
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ) );

        add_filter( 'woocommerce_package_rates', array($this, 'devvn_hide_shipping_when_shipdisable'), 100 );

		include_once( 'includes/functions-admin.php' );
        include_once( 'includes/apps.php' );

        //admin order address, form billing
        add_filter('woocommerce_admin_billing_fields', array($this, 'devvn_woocommerce_admin_billing_fields'), 99);
        add_filter('woocommerce_admin_shipping_fields', array($this, 'devvn_woocommerce_admin_shipping_fields'), 99);

        add_filter('woocommerce_form_field_select', array($this, 'devvn_woocommerce_form_field_select'), 10, 4);

        add_filter('woocommerce_shipping_calculator_enable_postcode','__return_false');

        add_filter('woocommerce_get_order_address', array($this, 'devvn_woocommerce_get_order_address'), 99, 2);  //API V1
        add_filter('woocommerce_rest_prepare_shop_order_object', array($this, 'devvn_woocommerce_rest_prepare_shop_order_object'), 99, 3);//API V2

        add_action('woocommerce_checkout_update_order_review', array($this, 'devvn_update_checkout_func'), 10);

        /*add_action( 'admin_notices', array($this, 'admin_notices') );
        if( is_admin() ) {
            add_action('in_plugin_update_message-' . DEVVN_DWAS_BASENAME, array($this,'devvn_modify_plugin_update_message'), 10, 2 );
        }*/

        //include_once ('includes/updates.php');

        //VTPost
        if(file_exists(DEVVN_DWAS_PLUGIN_DIR . 'includes/viettelpost/class-viettelpost-api.php')){
            include DEVVN_DWAS_PLUGIN_DIR . 'includes/viettelpost/class-viettelpost-api.php';
        }

    }

    public function define_constants(){
        if (!defined('DEVVN_DWAS_VERSION_NUM'))
            define('DEVVN_DWAS_VERSION_NUM', $this->_version);
        if (!defined('DEVVN_DWAS_URL'))
            define('DEVVN_DWAS_URL', plugin_dir_url(__FILE__));
        if (!defined('DEVVN_DWAS_BASENAME'))
            define('DEVVN_DWAS_BASENAME', plugin_basename(__FILE__));
        if (!defined('DEVVN_DWAS_PLUGIN_DIR'))
            define('DEVVN_DWAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
        if (!defined('DEVVN_AUTHOR_URL'))
            define('DEVVN_AUTHOR_URL', 'https://levantoan.com');
        if (!defined('DEVVN_AUTHOR_FACE'))
            define('DEVVN_AUTHOR_FACE', 'https://www.facebook.com/levantoan.wp');
        if (!defined('DEVVN_AUTHOR_MESSENGER'))
            define('DEVVN_AUTHOR_MESSENGER', 'http://m.me/levantoan.wp');
    }

    public static function on_activation(){
        if ( ! current_user_can( 'activate_plugins' ) )
            return false;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "activate-plugin_{$plugin}" );

    }

    public static function on_deactivation(){
        if ( ! current_user_can( 'activate_plugins' ) )
            return false;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );

    }

    public static function on_uninstall(){
        if ( ! current_user_can( 'activate_plugins' ) )
            return false;
    }

	function admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('Woocommerce Master Shipping','devvn-vnshipping'),
            __('Master Shipping','devvn-vnshipping'),
            'manage_woocommerce',
            'devvn-vietnam-shipping',
            array(
                $this,
                'devvn_district_setting'
            )
        );
	}

	function register_mysettings() {
		register_setting( $this->_optionGroup, $this->_optionName );
		register_setting( $this->_licenseGroup, $this->_licenseName );
	}

	function  devvn_district_setting() {
		include 'includes/options-page.php';
	}

	function vietnam_cities_woocommerce( $states ) {
		global $tinh_thanhpho;
	  	$states['VN'] = $tinh_thanhpho;
	  	return $states;
	}

    function custom_override_checkout_fields( $fields ) {
        global $tinh_thanhpho;

        if(!$this->get_options('alepay_support')) {
            //Billing
            $fields['billing']['billing_last_name'] = array(
                'label' => __('Full name', 'devvn-vnshipping'),
                'placeholder' => _x('Type Full name', 'placeholder', 'devvn-vnshipping'),
                'required' => true,
                'class' => array('form-row-wide'),
                'clear' => true,
                'priority' => 10
            );
        }
        if(isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone']['class'] = array('form-row-first4');
            $fields['billing']['billing_phone']['placeholder'] = __('Type your phone', 'devvn-vnshipping');
        }
        if(isset($fields['billing']['billing_email'])) {
            $fields['billing']['billing_email']['class'] = array('form-row-last');
            $fields['billing']['billing_email']['placeholder'] = __('Type your email', 'devvn-vnshipping');
        }
        $fields['billing']['billing_state'] = array(
            'label'			=> __('Province/City', 'devvn-vnshipping'),
            'required' 		=> true,
            'type'			=> 'select',
            'class'    		=> array( 'form-row-first5', 'address-field', 'update_totals_on_change' ),
            'placeholder'	=> _x('Select Province/City', 'placeholder', 'devvn-vnshipping'),
            'options'   	=> array( '' => __( 'Select Province/City', 'devvn-vnshipping' ) ) + $tinh_thanhpho,
            'priority'  =>  30
        );
        $fields['billing']['billing_city'] = array(
            'label'		=> __('District', 'devvn-vnshipping'),
            'required' 	=> true,
            'type'		=>	'select',
            'class'    	=> array( 'form-row-last', 'address-field', 'update_totals_on_change' ),
            'placeholder'	=>	_x('Select District', 'placeholder', 'devvn-vnshipping'),
            'options'   => array(
                ''	=> ''
            ),
            'priority'  =>  40
        );
        if(!$this->get_options()) {
            $fields['billing']['billing_address_2'] = array(
                'label' => __('Commune/Ward/Town', 'devvn-vnshipping'),
                'required' => true,
                'type' => 'select',
                'class' => array('form-row-firstp', 'address-field', 'update_totals_on_change'),
                'placeholder' => _x('Select Commune/Ward/Town', 'placeholder', 'devvn-vnshipping'),
                'options' => array(
                    '' => ''
                ),
                'priority'  =>  50
            );
            if ($this->get_options('required_village')) {
                $fields['billing']['billing_address_2']['required'] = false;
            }
        }
        $fields['billing']['billing_address_1']['placeholder'] = _x('Ex: No. 20, 90 Alley', 'placeholder', 'devvn-vnshipping');
        $fields['billing']['billing_address_1']['class'] = array('form-row-last');

        $fields['billing']['billing_address_1']['priority']  = 60;
        if(isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone']['priority'] = 20;
        }
        if(isset($fields['billing']['billing_email'])) {
            $fields['billing']['billing_email']['priority'] = 21;
        }
        if(!$this->get_options('alepay_support')) {
            unset($fields['billing']['billing_country']);
            unset($fields['billing']['billing_first_name']);
        }
        unset($fields['billing']['billing_company']);

        //Shipping
        if(!$this->get_options('alepay_support')) {
            $fields['shipping']['shipping_last_name'] = array(
                'label' => __('Full name', 'devvn-vnshipping'),
                'placeholder' => _x('Type Full name', 'placeholder', 'devvn-vnshipping'),
                'required' => true,
                'class' => array('form-row-first1'),
                'clear' => true,
                'priority' => 10
            );
        }
//        $fields['shipping']['shipping_phone'] = array(
//            'label' => __('Phone', 'devvn-vnshipping'),
//            'placeholder' => _x('Phone', 'placeholder', 'devvn-vnshipping'),
//            'required' => false,
//            'class' => array('form-row-last'),
//            'clear' => true,
//            'priority'  =>  20
//        );
//        if($this->get_options('alepay_support')) {
//            $fields['shipping']['shipping_phone']['class'] = array('form-row-wide');
//        }
        $fields['shipping']['shipping_state'] = array(
            'label'		=> __('Province/City', 'devvn-vnshipping'),
            'required' 	=> true,
            'type'		=>	'select',
            'class'    	=> array( 'form-row-first2', 'address-field', 'update_totals_on_change' ),
            'placeholder'	=>	_x('Select Province/City', 'placeholder', 'devvn-vnshipping'),
            'options'   => array( '' => __( 'Select Province/City', 'devvn-vnshipping' ) ) + $tinh_thanhpho,
            'priority'  =>  30
        );
        $fields['shipping']['shipping_city'] = array(
            'label'		=> __('District', 'devvn-vnshipping'),
            'required' 	=> true,
            'type'		=>	'select',
            'class'    	=> array( 'form-row-last', 'address-field', 'update_totals_on_change' ),
            'placeholder'	=>	_x('Select District', 'placeholder', 'devvn-vnshipping'),
            'options'   => array(
                ''	=> '',
            ),
            'priority'  =>  40
        );
        if(!$this->get_options()) {
            $fields['shipping']['shipping_address_2'] = array(
                'label' => __('Commune/Ward/Town', 'devvn-vnshipping'),
                'required' => true,
                'type' => 'select',
                'class' => array('form-row-first3', 'address-field', 'update_totals_on_change'),
                'placeholder' => _x('Select Commune/Ward/Town', 'placeholder', 'devvn-vnshipping'),
                'options' => array(
                    '' => '',
                ),
                'priority'  =>  50
            );
            if ($this->get_options('required_village')) {
                $fields['shipping']['shipping_address_2']['required'] = false;
            }
        }
        $fields['shipping']['shipping_address_1']['placeholder'] = _x('Ex: No. 20, 90 Alley', 'placeholder', 'devvn-vnshipping');
        $fields['shipping']['shipping_address_1']['class'] = array('form-row-last1');
        $fields['shipping']['shipping_address_1']['priority'] = 60;
        if(!$this->get_options('alepay_support')) {
            unset($fields['shipping']['shipping_country']);
            unset($fields['shipping']['shipping_first_name']);
        }
        unset($fields['shipping']['shipping_company']);

        uasort( $fields['billing'], array( $this, 'sort_fields_by_order' ) );
        uasort( $fields['shipping'], array( $this, 'sort_fields_by_order' ) );

        return $fields;
    }

    function sort_fields_by_order($a, $b){
        if(!isset($b['priority']) || !isset($a['priority']) || $a['priority'] == $b['priority']){
            return 0;
        }
        return ($a['priority'] < $b['priority']) ? -1 : 1;
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
	            $results = array_merge($results, $this->search_in_array($subarray, $key, $value));
	        }
	    }

	    return $results;
	}

    function search_in_array_value($array = array(), $value = '', $sub_arg = 'hub_district'){
        $results = array();
        if(is_array($array) && !empty($array)) {
            foreach ($array as $k=>$subarray) {
                $subarray_arg = isset($subarray[$sub_arg]) ? $subarray[$sub_arg] : $subarray;
                if(in_array($value, $subarray_arg)){
                    $results[] = $k;
                }
            }
        }
        return $results;
    }

	function devvn_enqueue_UseAjaxInWp() {
		if(is_checkout()|| is_page(get_option( 'woocommerce_edit_address_page_id' ))){
            wp_enqueue_style( 'dwas_styles', plugins_url( '/assets/css/devvn_dwas_style.css', __FILE__ ), array(), $this->_version, 'all' );
			wp_enqueue_script( 'devvn_tinhthanhpho', plugins_url('assets/js/devvn_tinhthanh.js', __FILE__), array('jquery','select2'), $this->_version, true);
			$php_array = array(
				'admin_ajax'		=>	admin_url( 'admin-ajax.php'),
				'home_url'			=>	home_url(),
                'formatNoMatches'   =>  __('No value', 'devvn-vnshipping')
			);
			wp_localize_script( 'devvn_tinhthanhpho', 'devvn_array', $php_array );
		}
	}

	function load_diagioihanhchinh_func() {
		$matp = isset($_POST['matp']) ? wc_clean(wp_unslash($_POST['matp'])) : '';
		$maqh = isset($_POST['maqh']) ? intval($_POST['maqh']) : '';
		if($matp){
			$result = $this->get_list_district($matp);
			wp_send_json_success($result);
		}
		if($maqh){
			$result = $this->get_list_village($maqh);
			wp_send_json_success($result);
		}
		wp_send_json_error();
		die();
	}
	function devvn_get_name_location($arg = array(), $id = '', $key = ''){
		if(is_array($arg) && !empty($arg)){
			$nameQuan = $this->search_in_array($arg,$key,$id);
			$nameQuan = isset($nameQuan[0]['name'])?$nameQuan[0]['name']:'';
			return $nameQuan;
		}
		return false;
	}

	function get_name_city($id = ''){
		global $tinh_thanhpho;
        if(is_numeric($id)) {
            $id_tinh = sprintf("%02d", intval($id));
            if(!is_array($tinh_thanhpho) || empty($tinh_thanhpho)){
                include 'cities/tinh_thanhpho_old.php';
            }
        }else{
            $id_tinh = wc_clean(wp_unslash($id));
        }
		$tinh_thanhpho_name = (isset($tinh_thanhpho[$id_tinh])) ? $tinh_thanhpho[$id_tinh] : '';
		return $tinh_thanhpho_name;
	}

	function get_name_district($id = ''){
		include 'cities/quan_huyen.php';
		$id_quan = sprintf("%03d", intval($id));
		if(is_array($quan_huyen) && !empty($quan_huyen)){
			$nameQuan = $this->search_in_array($quan_huyen,'maqh',$id_quan);
			$nameQuan = isset($nameQuan[0]['name'])?$nameQuan[0]['name']:'';
			return $nameQuan;
		}
		return false;
	}

	function get_name_village($id = ''){
		include 'cities/xa_phuong_thitran.php';
		$id_xa = sprintf("%05d", intval($id));
		if(is_array($xa_phuong_thitran) && !empty($xa_phuong_thitran)){
			$name = $this->search_in_array($xa_phuong_thitran,'xaid',$id_xa);
			$name = isset($name[0]['name'])?$name[0]['name']:'';
			return $name;
		}
		return false;
	}

	function devvn_woocommerce_localisation_address_formats($arg){
		unset($arg['default']);
		unset($arg['VN']);
		$arg['default'] = "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{country}";
		$arg['VN'] = "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{country}";
		return $arg;
	}

	function devvn_woocommerce_order_formatted_billing_address($eArg,$eThis){

        if($this->check_woo_version()){
            $orderID = $eThis->get_id();
        }else {
            $orderID = $eThis->id;
        }

		$nameTinh = $this->get_name_city(get_post_meta( $orderID, '_billing_state', true ));
		$nameQuan = $this->get_name_district(get_post_meta( $orderID, '_billing_city', true ));
		$nameXa = $this->get_name_village(get_post_meta( $orderID, '_billing_address_2', true ));

		unset($eArg['state']);
		unset($eArg['city']);
		unset($eArg['address_2']);

		$eArg['state'] = $nameTinh;
		$eArg['city'] = $nameQuan;
		$eArg['address_2'] = $nameXa;

		return $eArg;
	}

	function devvn_woocommerce_order_formatted_shipping_address($eArg,$eThis){

        if($this->check_woo_version()){
            $orderID = $eThis->get_id();
        }else {
            $orderID = $eThis->id;
        }

		$nameTinh = $this->get_name_city(get_post_meta( $orderID, '_shipping_state', true ));
		$nameQuan = $this->get_name_district(get_post_meta( $orderID, '_shipping_city', true ));
		$nameXa = $this->get_name_village(get_post_meta( $orderID, '_shipping_address_2', true ));

		unset($eArg['state']);
		unset($eArg['city']);
		unset($eArg['address_2']);

		$eArg['state'] = $nameTinh;
		$eArg['city'] = $nameQuan;
		$eArg['address_2'] = $nameXa;

		return $eArg;
	}

	function devvn_woocommerce_my_account_my_address_formatted_address($args, $customer_id, $name){

		$nameTinh = $this->get_name_city(get_user_meta( $customer_id, $name.'_state', true ));
		$nameQuan = $this->get_name_district(get_user_meta( $customer_id, $name.'_city', true ));
		$nameXa = $this->get_name_village(get_user_meta( $customer_id, $name.'_address_2', true ));

		unset($args['address_2']);
		unset($args['city']);
		unset($args['state']);

		$args['state'] = $nameTinh;
		$args['city'] = $nameQuan;
		$args['address_2'] = $nameXa;

		return $args;
	}

	function get_list_district($matp = ''){
		if(!$matp) return false;
		if(is_numeric($matp)) {
            include 'cities/quan_huyen_old.php';
            $matp = sprintf("%02d", intval($matp));
        }else{
            include 'cities/quan_huyen.php';
            $matp = wc_clean(wp_unslash($matp));
        }
		$result = $this->search_in_array($quan_huyen,'matp',$matp);
		return $result;
	}

	function get_list_district_select($matp = ''){
        $district_select  = array();
        $district_select_array = $this->get_list_district($matp);
        if($district_select_array && is_array($district_select_array)){
            foreach ($district_select_array as $district){
                $district_select[$district['maqh']] = $district['name'];
            }
        }
        return $district_select;
    }

	function get_list_village($maqh = ''){
		if(!$maqh) return false;
		include 'cities/xa_phuong_thitran.php';
		$id_xa = sprintf("%05d", intval($maqh));
		$result = $this->search_in_array($xa_phuong_thitran,'maqh',$id_xa);
		return $result;
	}

    function get_list_village_select($maqh = ''){
        $village_select  = array();
        $village_select_array = $this->get_list_village($maqh);
        if($village_select_array && is_array($village_select_array)){
            foreach ($village_select_array as $village){
                $village_select[$village['xaid']] = $village['name'];
            }
        }
        return $village_select;
    }

	function devvn_after_shipping_address($order){
	    if($this->check_woo_version()){
            $orderID = $order->get_id();
        }else {
            $orderID = $order->id;
        }
	    echo '<p><strong>'.__('Phone number of the recipient', 'devvn-vnshipping').':</strong> <br>' . get_post_meta( $orderID, '_shipping_phone', true ) . '</p>';
	}

	function devvn_woocommerce_order_details_after_customer_details($order){
		ob_start();
        if($this->check_woo_version()){
            $orderID = $order->get_id();
        }else {
            $orderID = $order->id;
        }
        $sdtnguoinhan = get_post_meta( $orderID, '_shipping_phone', true );
		if ( $sdtnguoinhan ) : ?>
			<tr>
				<th><?php _e( 'Shipping Phone:', 'devvn-vnshipping' ); ?></th>
				<td><?php echo esc_html( $sdtnguoinhan ); ?></td>
			</tr>
		<?php endif;
		echo ob_get_clean();
	}

	public function get_options($option = 'active_village'){
		$flra_options = wp_parse_args(get_option($this->_optionName),$this->_defaultOptions);
		return isset($flra_options[$option])?$flra_options[$option]:false;
	}

	public function get_license_options($option = 'license_key'){
		$license_options = wp_parse_args(get_option($this->_licenseName),$this->_licesneDefaultOptions);
		return isset($license_options[$option])?$license_options[$option]:false;
	}

	public function admin_enqueue_scripts() {
		wp_register_script( 'woocommerce_district_shipping_rate_rows', plugins_url( '/assets/js/admin-district-shipping.js', __FILE__ ), array( 'jquery', 'wp-util' ), $this->_version, true );
		wp_localize_script( 'woocommerce_district_shipping_rate_rows', 'woocommerce_district_shipping_rate_rows', array(
			'i18n' => array(
				'delete_rates' => __( 'Delete the selected boxes?', 'woocommerce-table-rate-shipping' ),
			),
			'delete_box_nonce' => wp_create_nonce( "delete-box" ),
		) );
        wp_enqueue_style( 'magnific-popup', plugins_url( '/assets/css/magnific-popup.css', __FILE__ ), array(), $this->_version, 'all' );
        wp_enqueue_script( 'magnific-popup', plugins_url( '/assets/js/jquery.magnific-popup.min.js', __FILE__ ), array( 'jquery'), $this->_version, true );
        wp_enqueue_script( 'woocommerce_district_admin_order', plugins_url( '/assets/js/admin-district-admin-order.js', __FILE__ ), array( 'jquery', 'select2', 'wp-util', 'magnific-popup'), $this->_version, true );
        wp_localize_script( 'woocommerce_district_admin_order', 'woocommerce_district_admin', array(
            'ajaxurl'   =>  admin_url('admin-ajax.php'),
            'formatNoMatches'   =>  __('No value', 'devvn-vnshipping')
        ) );
        wp_enqueue_style( 'woocommerce_district_shipping_styles', plugins_url( '/assets/css/admin.css', __FILE__ ), array(), $this->_version, 'all' );
	}
	
	/*Check version*/
	function devvn_district_zone_shipping_check_woo_version( $minimum_required = "2.6" ) {
		$woocommerce = WC();
		$version = $woocommerce->version;
		$active = version_compare( $version, $minimum_required, "ge" );
		return( $active );
	}
	/*filter woocommerce_shipping_methods*/
	
	function dwas_sort_desc_array($input = array(), $keysort = 'dk'){
        $sort = array();
        if($input && is_array($input)) {
            foreach ($input as $k => $v) {
                $sort[$keysort][$k] = $v[$keysort];
            }
            array_multisort($sort[$keysort], SORT_DESC, $input);
        }
        return $input;
    }
	function dwas_sort_asc_array($input = array(), $keysort = 'dk'){
        $sort = array();
        if($input && is_array($input)) {
            foreach ($input as $k => $v) {
                $sort[$keysort][$k] = $v[$keysort];
            }
            array_multisort($sort[$keysort], SORT_ASC, $input);
        }
        return $input;
    }
    function dwas_format_key_array($input = array()){
        $output = array();
        if($input && is_array($input)) {
            foreach ($input as $k => $v) {
                $output[] = $v;
            }
        }
        return $output;
    }
    function dwas_search_bigger_in_array($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && ($array[$key] <= $value) ) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->dwas_search_bigger_in_array($subarray, $key, $value));
            }
        }

        return $results;
    }
    function dwas_search_bigger_in_array_weight($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && ($array[$key] >= $value) ) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->dwas_search_bigger_in_array_weight($subarray, $key, $value));
            }
        }

        return $results;
    }
    public static function plugin_action_links( $links ) {
        $action_links = array(
            'settings' => '<a href="' . admin_url( 'admin.php?page=devvn-vietnam-shipping' ) . '" title="' . esc_attr( __( 'Settings', 'devvn-vnshipping' ) ) . '">' . __( 'Settings', 'devvn-vnshipping' ) . '</a>',
        );

        return array_merge( $action_links, $links );
    }
    public function check_woo_version($version = '3.0.0'){
        if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, $version, '>=' ) ) {
            return true;
        }
        return false;
    }
    function change_default_checkout_country() {
        return 'VN';
    }
    function devvn_woocommerce_get_country_locale($args){
        $field_s = array(
            'state' => array(
                'label'        => __('Province/City', 'devvn-vnshipping'),
                'priority'     => 41,
            ),
            'city' => array(
                'priority'     => 42,
            ),
            'address_1' => array(
                'priority'     => 44,
            ),
        );
        if(!$this->get_options()) {
            $field_s['address_2'] = array(
                'hidden'   => false,
                'priority'     => 43,
            );
        }
        $args['VN'] = $field_s;
        return $args;
    }
    function change_default_checkout_state() {
        $state = $this->get_options('tinhthanh_default');
        return ($state)?$state:'01';
    }
    function devvn_hide_shipping_when_shipdisable( $rates ) {
        $shipdisable = array();
        foreach ( $rates as $rate_id => $rate ) {
            if ( 'shipdisable' === $rate->id) {
                $shipdisable[ $rate_id ] = $rate;
                break;
            }
        }
        return ! empty( $shipdisable ) ? $shipdisable : $rates;
    }

    function devvn_custom_override_default_address_fields( $address_fields ) {
        if(!$this->get_options('alepay_support')) {
            unset($address_fields['first_name']);
            $address_fields['last_name'] = array(
                'label' => __('Full name', 'devvn-vnshipping'),
                'placeholder' => _x('Type Full name', 'placeholder', 'devvn-vnshipping'),
                'required' => true,
                'class' => array('form-row-wide'),
                'clear' => true
            );
        }
        if(!$this->get_options('enable_postcode')) {
            unset($address_fields['postcode']);
        }
        $address_fields['city'] = array(
            'label'        => __('District', 'devvn-vnshipping'),
            'type'		=>	'select',
            'required' => true,
            'class' => array('form-row-wide'),
            'placeholder'	=>	_x('Select District', 'placeholder', 'devvn-vnshipping'),
            'options'   => array(
                ''	=> ''
            ),
        );
        if(!$this->get_options()) {
            $address_fields['address_2'] = array(
                'label' => __('Commune/Ward/Town', 'devvn-vnshipping'),
                'type' => 'select',
                'class' => array('form-row-wide'),
                'placeholder' => _x('Select Commune/Ward/Town', 'placeholder', 'devvn-vnshipping'),
                'options' => array(
                    '' => ''
                ),
            );
        }else{
            unset($address_fields['address_2']);
        }
        $address_fields['address_1']['class'] = array('form-row-wide');
        return $address_fields;
    }
    function devvn_woocommerce_admin_billing_fields($billing_fields){
        global $thepostid, $post;
        $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
        $city = get_post_meta( $thepostid, '_billing_state', true );
        $district = get_post_meta( $thepostid, '_billing_city', true );
        $billing_fields = array(
            'first_name' => array(
                'label' => __( 'First name', 'woocommerce' ),
                'show'  => false,
            ),
            'last_name' => array(
                'label' => __( 'Last name', 'woocommerce' ),
                'show'  => false,
            ),
            'company' => array(
                'label' => __( 'Company', 'woocommerce' ),
                'show'  => false,
            ),
            'country' => array(
                'label'   => __( 'Country', 'woocommerce' ),
                'show'    => false,
                'class'   => 'js_field-country select short',
                'type'    => 'select',
                'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries(),
            ),
            'state' => array(
                'label' => __( 'Tỉnh/thành phố', 'woocommerce' ),
                'class'   => 'js_field-state select short',
                'show'  => false,
            ),
            'city' => array(
                'label' => __( 'Quận/huyện', 'woocommerce' ),
                'class'   => 'js_field-city select short',
                'type'      =>  'select',
                'show'  => false,
                'options' => array( '' => __( 'Chọn quận/huyện&hellip;', 'woocommerce' ) ) + $this->get_list_district_select($city),
            ),
            'address_2' => array(
                'label' => __( 'Xã/phường/thị trấn', 'woocommerce' ),
                'show'  => false,
                'class'   => 'js_field-address_2 select short',
                'type'      =>  'select',
                'options' => array( '' => __( 'Chọn xã/phường/thị trấn&hellip;', 'woocommerce' ) ) + $this->get_list_village_select($district),
            ),
            'address_1' => array(
                'label' => __( 'Address line 1', 'woocommerce' ),
                'show'  => false,
            ),
            'email' => array(
                'label' => __( 'Email address', 'woocommerce' ),
            ),
            'phone' => array(
                'label' => __( 'Phone', 'woocommerce' ),
            )
        );
        if($this->get_options()) {
            unset($billing_fields['address_2']);
        }
        return $billing_fields;
    }
    function devvn_woocommerce_admin_shipping_fields($shipping_fields){
        global $thepostid, $post;
        $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
        $city = get_post_meta( $thepostid, '_shipping_state', true );
        $district = get_post_meta( $thepostid, '_shipping_city', true );
        $billing_fields = array(
            'first_name' => array(
                'label' => __( 'First name', 'woocommerce' ),
                'show'  => false,
            ),
            'last_name' => array(
                'label' => __( 'Last name', 'woocommerce' ),
                'show'  => false,
            ),
            'company' => array(
                'label' => __( 'Company', 'woocommerce' ),
                'show'  => false,
            ),
            'country' => array(
                'label'   => __( 'Country', 'woocommerce' ),
                'show'    => false,
                'type'    => 'select',
                'class'   => 'js_field-country select short',
                'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries(),
            ),
            'state' => array(
                'label' => __( 'Tỉnh/thành phố', 'woocommerce' ),
                'class'   => 'js_field-state select short',
                'show'  => false,
            ),
            'city' => array(
                'label' => __( 'Quận/huyện', 'woocommerce' ),
                'class'   => 'js_field-city select short',
                'type'      =>  'select',
                'show'  => false,
                'options' => array( '' => __( 'Chọn quận/huyện&hellip;', 'woocommerce' ) ) + $this->get_list_district_select($city),
            ),
            'address_2' => array(
                'label' => __('Xã/phường/thị trấn', 'woocommerce'),
                'show' => false,
                'class' => 'js_field-address_2 select short',
                'type' => 'select',
                'options' => array('' => __('Chọn xã/phường/thị trấn&hellip;', 'woocommerce')) + $this->get_list_village_select($district),
            ),
            'address_1' => array(
                'label' => __( 'Address line 1', 'woocommerce' ),
                'show'  => false,
            ),
        );
        if($this->get_options()) {
            unset($billing_fields['address_2']);
        }
        return $billing_fields;
    }
    function devvn_woocommerce_form_field_select($field, $key, $args, $value){
        if(in_array($key, array('billing_city','shipping_city','billing_address_2','shipping_address_2'))) {
            if(in_array($key, array('billing_city','shipping_city'))) {
                if(!is_checkout() && is_user_logged_in()){
                    if('billing_city' === $key) {
                        $state = wc_get_post_data_by_key('billing_state', get_user_meta(get_current_user_id(), 'billing_state', true));
                    }else{
                        $state = wc_get_post_data_by_key('shipping_state', get_user_meta(get_current_user_id(), 'shipping_state', true));
                    }
                }else {
                    $state = WC()->checkout->get_value('billing_city' === $key ? 'billing_state' : 'shipping_state');
                }
                $city = array('' => ($args['placeholder']) ? $args['placeholder'] : __('Choose an option', 'woocommerce')) + $this->get_list_district_select($state);
                $args['options'] = $city;
            }elseif(in_array($key, array('billing_address_2','shipping_address_2'))) {
                if(!is_checkout() && is_user_logged_in()){
                    if('billing_address_2' === $key) {
                        $city = wc_get_post_data_by_key('billing_city', get_user_meta(get_current_user_id(), 'billing_city', true));
                    }else{
                        $city = wc_get_post_data_by_key('shipping_city', get_user_meta(get_current_user_id(), 'shipping_city', true));
                    }
                }else {
                    $city = WC()->checkout->get_value('billing_address_2' === $key ? 'billing_city' : 'shipping_city');
                }
                $village = array('' => ($args['placeholder']) ? $args['placeholder'] : __('Choose an option', 'woocommerce')) + $this->get_list_village_select($city);
                $args['options'] = $village;
            }

            if ($args['required']) {
                $args['class'][] = 'validate-required';
                $required = ' <abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
            } else {
                $required = '';
            }

            if (is_string($args['label_class'])) {
                $args['label_class'] = array($args['label_class']);
            }

            // Custom attribute handling.
            $custom_attributes = array();
            $args['custom_attributes'] = array_filter((array)$args['custom_attributes'], 'strlen');

            if ($args['maxlength']) {
                $args['custom_attributes']['maxlength'] = absint($args['maxlength']);
            }

            if (!empty($args['autocomplete'])) {
                $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
            }

            if (true === $args['autofocus']) {
                $args['custom_attributes']['autofocus'] = 'autofocus';
            }

            if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
                foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
                    $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
                }
            }

            if (!empty($args['validate'])) {
                foreach ($args['validate'] as $validate) {
                    $args['class'][] = 'validate-' . $validate;
                }
            }

            $label_id = $args['id'];
            $sort = $args['priority'] ? $args['priority'] : '';
            $field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</p>';

            $options = $field = '';

            if (!empty($args['options'])) {
                foreach ($args['options'] as $option_key => $option_text) {
                    if ('' === $option_key) {
                        // If we have a blank option, select2 needs a placeholder.
                        if (empty($args['placeholder'])) {
                            $args['placeholder'] = $option_text ? $option_text : __('Choose an option', 'woocommerce');
                        }
                        $custom_attributes[] = 'data-allow_clear="true"';
                    }
                    $options .= '<option value="' . esc_attr($option_key) . '" ' . selected($value, $option_key, false) . '>' . esc_attr($option_text) . '</option>';
                }

                $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '">
                    ' . $options . '
                </select>';
            }

            if (!empty($field)) {
                $field_html = '';

                if ($args['label'] && 'checkbox' != $args['type']) {
                    $field_html .= '<label for="' . esc_attr($label_id) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
                }

                $field_html .= $field;

                if ($args['description']) {
                    $field_html .= '<span class="description">' . esc_html($args['description']) . '</span>';
                }

                $container_class = esc_attr(implode(' ', $args['class']));
                $container_id = esc_attr($args['id']) . '_field';
                $field = sprintf($field_container, $container_class, $container_id, $field_html);
            }
            return $field;
        }
        return $field;
    }
    function convert_weight_to_kg( $weight ) {
        switch(get_option( 'woocommerce_weight_unit' )){
            case 'g':
                $weight = $weight * 0.001;
                break;
            case 'lbs':
                $weight = $weight * 0.45359237;
                break;
            case 'oz':
                $weight = $weight * 0.02834952;
                break;
        }
        return $weight; //return kg
    }
    function convert_dimension_to_cm( $dimension ) {
        switch(get_option( 'woocommerce_dimension_unit' )){
            case 'm':
                $dimension = $dimension * 100;
                break;
            case 'mm':
                $dimension = $dimension * 0.1;
                break;
            case 'in':
                $dimension = $dimension * 2.54;
            case 'yd':
                $dimension = $dimension * 91.44;
                break;
        }
        return $dimension; //return cm
    }
    function devvn_woocommerce_get_order_address($value, $type){
        if($type == 'billing' || $type == 'shipping'){
            if(isset($value['state']) && $value['state']){
                $state = $value['state'];
                $value['state'] = $this->get_name_city($state);
            }
            if(isset($value['city']) && $value['city']){
                $city = $value['city'];
                $value['city'] = $this->get_name_district($city);
            }
            if(isset($value['address_2']) && $value['address_2']){
                $address_2 = $value['address_2'];
                $value['address_2'] = $this->get_name_village($address_2);
            }
        }
        return $value;
    }
    function devvn_woocommerce_rest_prepare_shop_order_object($response, $order, $request){
        if( empty( $response->data ) ) {
            return $response;
        }

        $fields = array(
            'billing',
            'shipping'
        );

        foreach($fields as $field){
            if(isset($response->data[$field]['state']) && $response->data[$field]['state']){
                $state = $response->data[$field]['state'];
                $response->data[$field]['state'] = $this->get_name_city($state);
            }

            if(isset($response->data[$field]['city']) && $response->data[$field]['city']){
                $city = $response->data[$field]['city'];
                $response->data[$field]['city'] = $this->get_name_district($city);
            }

            if(isset($response->data[$field]['address_2']) && $response->data[$field]['address_2']){
                $address_2 = $response->data[$field]['address_2'];
                $response->data[$field]['address_2'] = $this->get_name_village($address_2);
            }
        }

        return $response;
    }

    function admin_notices(){
        $class = 'notice notice-error';
        $license_key = $this->get_options('license_key');
        if(!$license_key) {
            printf('<div class="%1$s"><p>Hãy điền <strong>License Key</strong> để tự động cập nhật khi có phiên bản mới. <a href="%2$s">Thêm tại đây</a></p></div>', esc_attr($class), esc_url(admin_url('admin.php?page=devvn-vietnam-shipping')));
        }
    }

    function devvn_modify_plugin_update_message( $plugin_data, $response ) {
        $license_key = sanitize_text_field($this->get_options('license_key'));
        if( $license_key && isset($plugin_data['package']) && $plugin_data['package']) return;
        $PluginURI = isset($plugin_data['PluginURI']) ? $plugin_data['PluginURI'] : '';
        echo '<br />' . sprintf( __('<strong>Mua bản quyền để được tự động update. <a href="%s" target="_blank">Xem thêm thông tin mua bản quyền</a></strong> hoặc liên hệ mua trực tiếp qua <a href="%s" target="_blank">facebook</a>', 'devvn-vnshipping'), $PluginURI, DEVVN_AUTHOR_MESSENGER);
    }

    function convert_weight_to_gram( $weight ) {
        switch(get_option( 'woocommerce_weight_unit' )){
            case 'kg':
                $weight = $weight * 1000;
                break;
            case 'lbs':
                $weight = $weight * 453.59237;
                break;
            case 'oz':
                $weight = $weight * 28.34952;
                break;
        }
        return $weight; //return gram
    }

    function get_cart_contents_weight( $package = array() ) {
        $weight = 0;
        if(isset($package['contents']) && !empty($package['contents'])) {
            foreach ($package['contents'] as $cart_item_key => $values) {
                $weight += (float)$values['data']->get_weight() * $values['quantity'];
            }
            $weight = $this->convert_weight_to_gram($weight);
        }
        return apply_filters( 'wc_devvn_cart_contents_weight', $weight );
    }

    function get_cart_quantity_package( $package = array() ) {
        $quantity = 0;
        if(isset($package['contents']) && !empty($package['contents'])) {
            foreach ($package['contents'] as $cart_item_key => $values) {
                $quantity += $values['quantity'];
            }
        }
        return apply_filters( 'wc_devvn_cart_quantity_package', $quantity, $package );
    }
    function get_customer_address_shipping($orderid = ''){

        if(!$orderid) return false;

        $customer_address = array();
        $order = wc_get_order($orderid);

        if($order && !is_wp_error($order)) {
            $billing_phone = wc_clean($order->get_billing_phone());
            $billing_ward = $order->get_billing_address_2();
            $billing_district = $order->get_billing_city();
            $billing_province = $order->get_billing_state();
            $billing_address = $order->get_billing_address_1();
            $billing_fullname = $order->get_formatted_billing_full_name();
            $billing_email = $order->get_billing_email();
            $shipping_phone = wc_clean(get_post_meta($order->get_id(), '_shipping_phone', true));
            if (!$shipping_phone) $shipping_phone = $billing_phone;

            if (!wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $order->get_formatted_shipping_address()) :
                $customer_address['name'] = $order->get_formatted_shipping_full_name();
                $customer_address['address'] = $order->get_shipping_address_1();
                $customer_address['province'] = $order->get_shipping_state();
                $customer_address['disrict'] = $order->get_shipping_city();
                $customer_address['ward'] = $order->get_shipping_address_2();
                $customer_address['email'] = $billing_email;
                $customer_address['phone'] = $shipping_phone;
            else:
                $customer_address['name'] = $billing_fullname;
                $customer_address['address'] = $billing_address;
                $customer_address['province'] = $billing_province;
                $customer_address['disrict'] = $billing_district;
                $customer_address['ward'] = $billing_ward;
                $customer_address['email'] = $billing_email;
                $customer_address['phone'] = $billing_phone;
            endif;
        }
        return $customer_address;
    }

    function get_order_weight($orderid = '', $field = 'weight'){
        if(!$orderid) return false;
        $all_weight = 0;
        $all_width = 0;
        $all_height = 0;
        $all_length = 0;
        $product_list = $this->get_product_args($orderid);
        if($product_list && !is_wp_error($product_list) && !empty($product_list)):
            foreach($product_list as $product):
                $all_weight += (float) ($product['quantity'] * $product['weight']);
                $all_width += (float) ($product['quantity'] * $product['width']);
                $all_height += (float) ($product['quantity'] * $product['height']);
                $all_length += (float) ($product['quantity'] * $product['length']);
            endforeach;
        endif;
        if($field == 'length') return $all_length;
        if($field == 'width') return $all_width;
        if($field == 'height') return $all_height;
        return $all_weight;
    }

    function get_product_args($orderid = ''){
        if(!$orderid) return false;
        $orderThis = wc_get_order($orderid);
        if($orderThis && !is_wp_error($orderThis)) {
            $products = array();
            $order_items = $orderThis->get_items();
            if ($order_items && !empty($order_items)) {
                $key = 0;
                $variations = array();
                foreach ($order_items as $item) {
                    $product = $item->get_product();
                    $subtitle = array();
                    if (is_array($item->get_meta_data())) {
                        foreach ($item->get_meta_data() as $meta) {
                            if (taxonomy_is_product_attribute($meta->key)) {
                                $term = get_term_by('slug', $meta->value, $meta->key);
                                $variations[$meta->key] = $term ? $term->name : $meta->value;
                            } elseif (meta_is_product_attribute($meta->key, $meta->value, $item['product_id'])) {
                                $variations[$meta->key] = $meta->value;
                            }
                        }
                        if ($variations && is_array($variations)) {
                            foreach ($variations as $k => $v) {
                                $subtitle[] = wc_attribute_label($k, $product) . '-' . $v;
                            }
                        }
                    }

                    if ($subtitle) {
                        $name_prod = sanitize_text_field($item['name']) . ' | ' . implode(" | ", $subtitle);
                    } else {
                        $name_prod = sanitize_text_field($item['name']);
                    }

                    $products[$key]['name'] = $name_prod;
                    $products[$key]['weight'] = (float)$product->get_weight();
                    $products[$key]['width'] = (float) $product->get_width();
                    $products[$key]['height'] = (float) $product->get_height();
                    $products[$key]['length'] = (float) $product->get_length();
                    $products[$key]['price'] = (float)$orderThis->get_item_subtotal($item, false, true);
                    $products[$key]['quantity'] = (float)$item->get_quantity();

                    $key++;
                }
            }
            return $products;
        }
        return false;
    }

    function order_get_total($order){
        $order_sub_total = $order->get_subtotal();
        $order_discount_total = $order->get_discount_total();
        if($order_discount_total){
            return $order_sub_total - $order_discount_total;
        }else{
            return $order_sub_total;
        }
    }

    function devvn_update_checkout_func($array)
    {
        delete_option( '_transient_shipping-transient-version' );
        return;
    }
}


function vn_shipping(){
    return Woo_Address_Selectbox_Class::init();
}
vn_shipping();

function devvn_round_up($value, $step)
{
    if(intval($value) == $value) return $value;
    $value_int = intval($value);
    $value_float = $value - $value_int;
    if($step == 0.5 && $value_float <= 0.5){
        $output = $value_int + 0.5;
    }elseif($step == 1 || ($step == 0.5 && $value_float > 0.5)){
        $output = $value_int + 1;
    }
    return $output;
}

}else{
    if(in_array( 'woo-vietnam-checkout/devvn-woo-address-selectbox.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )){
        function devvn_vnshipping_active_pro() {
            ?>
            <div class="notice notice-error">
                <p><?php _e( 'Ngừng kích hoạt plugin "Woocommerce Vietnam Checkout" để có thể chạy được plugin "Devvn Master Shipping"', 'devvn-vnshipping' ); ?></p>
            </div>
            <?php
        }
        add_action( 'admin_notices', 'devvn_vnshipping_active_pro' );
    }
}