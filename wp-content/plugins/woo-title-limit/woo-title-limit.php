<?php
/**
* Plugin Name: Woo Title Limit
* Plugin URI: http://www.dimitri-wolf.de/woo-title-limit.html
* Description: Allow to set a limit for WooCommerce product titles in the shop view
* Version: 1.4.4
* Author: Dima W.
* Text Domain: woo-title-limit
* Domain Path: /languages
* Author URI: https://www.fraggi.de
* License: GPL2
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once(ABSPATH .'wp-includes/option.php');
if  ( ! defined( 'ABSPATH')) exit;

class woo_title_limit{
    private $options;
    protected $pluginPath;
    protected $pluginUrl;
    protected $pluginWidgetStyle = "woo-title-limit-widget";
    protected $pluginStyle = "woo-title-limit";
    private $pluginVersion = "1.4.4";

    /**
     * woo_title_limit constructor.
     */
    function __construct(){
        register_activation_hook( __FILE__, array( $this, 'wtl_activation_run' ) );
        add_action('admin_init', array($this,'wtl_options'));
        add_action('admin_menu', array($this,'wtl_admin_page'));
        add_filter( 'the_title', array($this,'wtl_shorten_my_product_title'), 10, 2 );
        add_action('init',array($this,'wtl_language'));
        add_action('wp_enqueue_scripts', array($this,'wtl_scripts'));
        //add_action( 'admin_enqueue_scripts', array($this,'wtl_admin_scripts'));


        // Set Plugin Path
        $this->pluginPath = dirname( plugin_basename( __FILE__ ));
        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/woo-title-limit';

        $this->options = $this->get_wtl_options();
    }

    /**
     * return mixed
     */
    public function get_wtl_options(){
        return get_option('wtl_opt');
    }

    /**
     * return mixed
     */
    public function get_pluginPath(){
        return $this->pluginPath;
    }

    /**
     * return string
     */
    public function get_pluginStyle(){
        return $this->pluginStyle;
    }
    /**
     * return string
     */
    public function get_pluginWidgetStyle(){
        return $this->pluginWidgetStyle;
    }

    /**
     * return string
     */
    public function get_pluginUrl(){
        return $this->pluginUrl;
    }

    public function set_option($option, $value){
        $v = $this->get_wtl_options();
        $v[$option] = $value;
        update_option('wtl_opt', $v);
    }
    public function wtl_activation_run(){
            $this->set_option('wtl_count_shop', 100);
            $this->set_option('wtl_count_category', 100);
            $this->set_option('wtl_count_product', 100);
            $this->set_option('wtl_count_home', 100);
            $this->set_option('wtl_checkbox_etc_shop', 0);
            $this->set_option('wtl_checkbox_etc_category', 0);
            $this->set_option('wtl_checkbox_etc_product', 0);
            $this->set_option('wtl_checkbox_widgets', 0);
            $this->set_option('wtl_checkbox_wordcutter', 0);
    }
    /**
     *
     */
    public function wtl_language() {
        load_plugin_textdomain('woo-title-limit', false, $this->get_pluginPath() . '/languages/');
    }
    /**
     * enqueue frontend scripts/css
     */
    public function wtl_scripts(){
        $options = $this->get_wtl_options();
        if(isset($options['wtl_checkbox_widgets'])){
            if($options['wtl_checkbox_widgets'] == 1) {
                wp_enqueue_style($this->get_pluginStyle(), $this->get_pluginUrl() . '/css/' . $this->get_pluginWidgetStyle() . '.css', '', $this->pluginVersion, '');
            }
        }
    }

    /**
     * enqueue admin scripts/css
     * TODO: upcoming version
     */
    /*public function wtl_admin_scripts(){
        wp_enqueue_script( 'wtl_help-js', $this->get_pluginUrl() . '/js/' . 'help.js', false );
        wp_enqueue_style($this->get_pluginStyle(), $this->get_pluginUrl() . '/css/' . $this->get_pluginStyle().'.css','', $this->pluginVersion,'');
    }*/

    /**
     *
     */
    function wtl_admin_page(){
        add_submenu_page(
            'options-general.php',
            'Woo Title Limit',
            'Woo Title Limit',
            'manage_options',
            'wtl',
            array($this,'wtl_output')
        );
    }

    /**
     *
     */
    public function wtl_options(){
        register_setting('wtl_options_group', 'wtl_opt');
        $x1 = __('Character limit shop page', 'woo-title-limit');
        $x2 = __('Character limit', 'woo-title-limit');
        $x3 = __('Character limit category page', 'woo-title-limit');
        $x4 = __('Character limit product page', 'woo-title-limit');
        $x9 = __('Character limit home page', 'woo-title-limit');
        $x5 = __('Add "..." to title', 'woo-title-limit');
        $x6 = __('Extra settings', 'woo-title-limit');
        $x7 = __('Limit product title for widgets automatically? (beta)', 'woo-title-limit');
        $x8 = __('Dont break words in title?', 'woo-title-limit');

        // section for settings on shop page
        add_settings_section(
            'wtl_shop_page',
            $x1,
            array($this,'wtl_shop_page_render'),
            'wtl_options_group'
        );
        // section for settings on category page
        add_settings_section(
            'wtl_category_page',
            $x3,
            array($this,'wtl_category_page_render'),
            'wtl_options_group'
        );
        // section for settings on product page
        add_settings_section(
            'wtl_product_page',
            $x4,
            array($this,'wtl_product_page_render'),
            'wtl_options_group'
        );
        // section for settings on product page
        add_settings_section(
            'wtl_home_page',
            $x9,
            array($this,'wtl_home_page_render'),
            'wtl_options_group'
        );
        // section for extra settings
        add_settings_section(
            'wtl_extra',
            $x6,
            array($this,'wtl_extra_render'),
            'wtl_options_group'
        );

        // shop page textarea field for character limit
        add_settings_field(
            'wtl_count_shop',
            $x2,
            array($this,'wtl_field_render_count_shop'),
            'wtl_options_group',
            'wtl_shop_page',
            array('id' => 'wtl_count_shop')
        );
        // shop page checkbox for "..."
        add_settings_field(
            'wtl_checkbox_etc_shop',
            $x5,
            array($this,'wtl_field_render_title_shop'),
            'wtl_options_group',
            'wtl_shop_page'
        );

        // category page textarea field for character limit
        add_settings_field(
            'wtl_count_category',
            $x2,
            array($this,'wtl_field_render_count_category'),
            'wtl_options_group',
            'wtl_category_page',
            array('id' => 'wtl_count_category')
        );
        // category page checkbox for "..."
        add_settings_field(
            'wtl_checkbox_etc_category',
            $x5,
            array($this,'wtl_field_render_title_category'),
            'wtl_options_group',
            'wtl_category_page'
        );

        // product page textarea field for character limit
        add_settings_field(
            'wtl_count_product',
            $x2,
            array($this,'wtl_field_render_count_product'),
            'wtl_options_group',
            'wtl_product_page',
            array('id' => 'wtl_count_product')
        );
        // product page checkbox for "..."
        add_settings_field(
            'wtl_checkbox_etc_product',
            $x5,
            array($this,'wtl_field_render_title_product'),
            'wtl_options_group',
            'wtl_product_page'
        );

        // product page checkbox for widget area
        add_settings_field(
            'wtl_widgets_limit',
            $x7,
            array($this,'wtl_field_render_widgets'),
            'wtl_options_group',
            'wtl_extra'
        );


        // home page textarea field for character limit
        add_settings_field(
            'wtl_count_home',
            $x2,
            array($this,'wtl_field_render_count_home'),
            'wtl_options_group',
            'wtl_home_page',
            array('id' => 'wtl_count_home')
        );
        // home page checkbox for "..."
        add_settings_field(
            'wtl_checkbox_etc_home',
            $x5,
            array($this,'wtl_field_render_title_home'),
            'wtl_options_group',
            'wtl_home_page'
        );


        add_settings_field(
            'wtl_extra_limit',
            $x8,
            array($this,'wtl_field_render_extra'),
            'wtl_options_group',
            'wtl_extra'
        );

    }

    /**
     *
     */
    public function wtl_output(){
        ?>
        <form action="options.php" method='post'>
            <h2>Woo Title Limit</h2>
            <?php
            if(is_plugin_inactive('woocommerce/woocommerce.php')){
                echo '<span style="background-color: red; font-weight: bold;">';
                _e('Notice: WooCommerce isnt active or isnt installed.', 'woo-title-limit');
                echo '</span>';
            }
            settings_fields('wtl_options_group');
            do_settings_sections('wtl_options_group');
            submit_button();
            ?>
        </form>
        <?php

    }

    // shop page render
    public function wtl_shop_page_render(){
        ?>
        <p><?php _e('Set the limit for the product titles (amount of maximum characters).', 'woo-title-limit'); ?></p>
        <?php
    }
    // category page render
    public function wtl_category_page_render(){
        ?>
        <p><?php _e('Set the limit for the product titles (amount of maximum characters).', 'woo-title-limit'); ?></p>
        <?php
    }
    // product page render
    public function wtl_product_page_render(){
        ?>
        <p><?php _e('Set the limit for the product titles (amount of maximum characters).', 'woo-title-limit'); ?></p>
        <?php
    }
    // shop page render
    public function wtl_home_page_render(){
        ?>
        <p><?php _e('Set the limit for the product titles (amount of maximum characters).', 'woo-title-limit'); ?></p>
        <?php
    }
    // extra area render
    public function wtl_extra_render(){
        ?>
        <p><?php _e('Some more settings for product titles.', 'woo-title-limit'); ?></p>
        <?php
    }
    // shop page section create the textfield for the title limit
    public function wtl_field_render_count_shop($args){
        $option = $this->get_wtl_options();
        ?>
        <input id="<?php echo $args['id']; ?>" type="number" name="wtl_opt[<?php echo $args['id']; ?>]" value="<?php echo $option[$args['id']]; ?>" title="Title" min="0" required>
        <?php
    }
    // shop page section create the checkbox for "..."
    public function wtl_field_render_title_shop($args){
        $option = $this->get_wtl_options();
        $html = '<input type="checkbox" id="wtl_checkbox_etc_shop" name="wtl_opt[wtl_checkbox_etc_shop]" value="1"' . checked( 1, isset($option['wtl_checkbox_etc_shop'])?$option['wtl_checkbox_etc_shop']:"0", false ) . '/>';

        echo $html;
    }
    // category page section create the textfield for the title limit
    public function wtl_field_render_count_category($args){
        $option = $this->get_wtl_options();
        ?>
        <input type="number" name="wtl_opt[<?php echo $args['id']; ?>]" value="<?php echo $option[$args['id']]; ?>" min="0" required>
        <?php
    }
    // category page section create the checkbox for "..."
    public function wtl_field_render_title_category($args){
        $option = $this->get_wtl_options();
        $html = '<input type="checkbox" id="wtl_checkbox_etc_category" name="wtl_opt[wtl_checkbox_etc_category]" value="1"' . checked( 1, isset($option['wtl_checkbox_etc_category'])?$option['wtl_checkbox_etc_category']:"0", false ) . '/>';

        echo $html;
    }
    // category page section create the textfield for the title limit
    public function wtl_field_render_count_product($args){
        $option = $this->get_wtl_options();
        ?>
        <input type="number" name="wtl_opt[<?php echo $args['id']; ?>]" value="<?php echo $option[$args['id']]; ?>" min="0" required>
        <?php
    }
    // category page section create the checkbox for "..."
    public function wtl_field_render_title_product($args){
        $option = $this->get_wtl_options();
        $html = '<input type="checkbox" id="wtl_checkbox_etc_product" name="wtl_opt[wtl_checkbox_etc_product]" value="1"' . checked( 1, isset($option['wtl_checkbox_etc_product'])?$option['wtl_checkbox_etc_product']:"0", false ) . '/>';

        echo $html;
    }


    // home page section create the textfield for the title limit
    public function wtl_field_render_count_home($args){
        $option = $this->get_wtl_options();
        ?>
        <input type="number" name="wtl_opt[<?php echo $args['id']; ?>]" value="<?php echo $option[$args['id']]; ?>" min="0" required>
        <?php
    }
    // home page section create the checkbox for "..."
    public function wtl_field_render_title_home($args){
        $option = $this->get_wtl_options();
        $html = '<input type="checkbox" id="wtl_checkbox_etc_home" name="wtl_opt[wtl_checkbox_etc_home]" value="1"' . checked( 1, isset($option['wtl_checkbox_etc_home'])?$option['wtl_checkbox_etc_home']:"0", false ) . '/>';

        echo $html;
    }


// extra section create the checkbox activation of the widget setting + word cutter
    public function wtl_field_render_extra($args){
        $option = $this->get_wtl_options();
        $html = '</br><input type="checkbox" id="wtl_checkbox_wordcutter" name="wtl_opt[wtl_checkbox_wordcutter]" value="1"' . checked( 1, isset($option['wtl_checkbox_wordcutter'])?$option['wtl_checkbox_wordcutter']:"0", false ) . '/>';

        echo $html;
    }
    public function wtl_field_render_widgets($args){
        $option = $this->get_wtl_options();
        $html = '<input type="checkbox" id="wtl_checkbox_widgets" name="wtl_opt[wtl_checkbox_widgets]" value="1"' . checked( 1, isset($option['wtl_checkbox_widgets'])?$option['wtl_checkbox_widgets']:"0", false ) . '/>';

        echo $html;
    }

    /**
     * param $title
     * param $id
     * return mixed
     */
    public function wtl_shorten_my_product_title( $title, $id ) {
        $options = $this->get_wtl_options();
        $pos = 0;
        if(get_post_type( $id ) != 'product'){return $title;}
        if(is_plugin_active('woocommerce/woocommerce.php')){
            #category page
            if ( is_product_category() && get_post_type( $id ) === 'product' ) {
                if($options['wtl_checkbox_etc_category'] != 1 && $options['wtl_count_category'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_category']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos );
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_category'] );
                    }
                }else if($options['wtl_checkbox_etc_category'] == 1 && $options['wtl_count_category'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_category']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos ).'...';
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_category'] ).'...';
                    }
                }else{
                    return $title;
                }
            }

            #shop page
            else if ( is_shop() && get_post_type( $id ) === 'product' ) {
                if($options['wtl_checkbox_etc_shop'] != 1 && $options['wtl_count_shop'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_shop']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos );
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_shop'] );
                    }
                }else if($options['wtl_checkbox_etc_shop'] == 1 && $options['wtl_count_shop'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_shop']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos ).'...';
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_shop'] ).'...';
                    }
                }else{
                    return $title;
                }
            }
            #product page
            else if ( is_product() && get_post_type( $id ) === 'product' ) {
                if($options['wtl_checkbox_etc_product'] != 1 && $options['wtl_count_product'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_product']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos );
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_product'] );
                    }
                }else if($options['wtl_checkbox_etc_product'] == 1 && $options['wtl_count_product'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_product']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos ).'...';
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_product'] ).'...';
                    }
                }else{
                    return $title;
                }

            }#home page
            else if ( (is_home() || is_front_page()) && get_post_type( $id ) === 'product' ) {
                if($options['wtl_checkbox_etc_home'] != 1 && $options['wtl_count_home'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_home']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos );
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_home'] );
                    }
                }else if($options['wtl_checkbox_etc_home'] == 1 && $options['wtl_count_home'] < strlen($title)){
                    if ($options['wtl_checkbox_wordcutter'] == 1){
                        $pos = strpos($title, ' ', $options['wtl_count_home']);
                        if(!$pos){
                            return $title;
                        }else{
                            return substr( $title, 0, $pos ).'...';
                        }
                    }else{
                        return substr( $title, 0, $options['wtl_count_home'] ).'...';
                    }
                }else{
                    return $title;
                }
            }else{
                return $title;
            }
        }else {
            return $title;
        }
    }

}//woo_title_limit class end

$wtl = new woo_title_limit();
?>