<?php
/*
 * Plugin Name: Bubble Products
 * Plugin URI: https://adaptis.io/
 * Description: Bubble Products is a plugin for WordPress allows you export information
 * Version: 1.0
 * Author: Adaptis Team
 * Author URI: https://adaptis.io/
 */
if (!defined('ABSPATH')) {
    exit('Direct\'s not allowed');
}
define('CBE_BUBBLE_FILE', __FILE__);
define('CBE_BUBBLE_PLUGIN_DIR', dirname(__FILE__));
define('CBE_BUBBLE_PLUGIN_URL', plugins_url('', __FILE__));
if ( is_admin() ) {
    require_once CBE_BUBBLE_PLUGIN_DIR . '/functions.php';
    require_once CBE_BUBBLE_PLUGIN_DIR . '/admin/init.php';

}