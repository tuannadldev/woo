<?php

/*
  Replace BAF and baf by your plugin prefix to prevent conflicts between plugins using this script.
 */

// Constants for the plugin name, folder and remote XML url
define('BWL_WPFM_NOTIFIER_PLUGIN_NAME', BWL_WPFM_PLUGIN_TITLE); // The plugin name
define('BWL_WPFM_NOTIFIER_PLUGIN_SHORT_NAME', 'Woo Product Faq'); // The plugin short name, only if needed to make the menu item fit. Remove this if not needed
define('BWL_WPFM_NOTIFIER_PLUGIN_FOLDER_NAME', 'woocommerce-product-faq-manager'); // The plugin folder name
define('BWL_WPFM_NOTIFIER_PLUGIN_FILE_NAME', 'woocommerce-product-faq-manager.php'); // The plugin file name
define('BWL_WPFM_NOTIFIER_PLUGIN_XML_FILE', 'http://projects.bluewindlab.net/wpplugin/notify/wpfm/notifier.xml'); // The remote notifier XML file containing the latest version of the plugin and changelog
define('BWL_WPFM_PLUGIN_NOTIFIER_CACHE_INTERVAL', 86400); // The time interval for the remote XML cache in the database (86400 seconds = 1 Day)
define('BWL_WPFM_PLUGIN_NOTIFIER_CODECANYON_USERNAME', 'xenioushk'); // Your Codecanyon username
//
// Adds an update notification to the WordPress Dashboard menu
function bwl_wpfm_update_plugin_notifier_menu() {
    if (function_exists('simplexml_load_string')) { // Stop if simplexml_load_string funtion isn't available
        $xml = bwl_wpfm_get_latest_plugin_version(BWL_WPFM_PLUGIN_NOTIFIER_CACHE_INTERVAL); // Get the latest remote XML file on our server
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . BWL_WPFM_NOTIFIER_PLUGIN_FOLDER_NAME . '/' . BWL_WPFM_NOTIFIER_PLUGIN_FILE_NAME); // Read plugin current version from the style.css

        if ((string) $xml->latest > (string) $plugin_data['Version']) { // Compare current plugin version with the remote XML version
            if (defined('BWL_WPFM_NOTIFIER_PLUGIN_SHORT_NAME')) {
                $menu_name = BWL_WPFM_NOTIFIER_PLUGIN_SHORT_NAME;
            } else {
                $menu_name = BWL_WPFM_NOTIFIER_PLUGIN_NAME;
            }
            add_dashboard_page(BWL_WPFM_NOTIFIER_PLUGIN_NAME . ' Plugin Updates', $menu_name . ' <span class="update-plugins count-1"><span class="update-count">New Updates</span></span>', 'administrator', 'baf-plugin-update-notifier', 'bwl_wpfm_update_notifier');
        }
    }
}

add_action('admin_menu', 'bwl_wpfm_update_plugin_notifier_menu');

// Adds an update notification to the WordPress 3.1+ Admin Bar
function bwl_wpfm_update_notifier_bar_menu() {
    if (function_exists('simplexml_load_string')) { // Stop if simplexml_load_string funtion isn't available
        global $wp_admin_bar, $wpdb;

        if (!is_super_admin() || !is_admin_bar_showing()) // Don't display notification in admin bar if it's disabled or the current user isn't an administrator
            return;

        $xml = bwl_wpfm_get_latest_plugin_version(BWL_WPFM_PLUGIN_NOTIFIER_CACHE_INTERVAL); // Get the latest remote XML file on our server
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . BWL_WPFM_NOTIFIER_PLUGIN_FOLDER_NAME . '/' . BWL_WPFM_NOTIFIER_PLUGIN_FILE_NAME); // Read plugin current version from the main plugin file

        if ((string) $xml->latest > (string) $plugin_data['Version']) { // Compare current plugin version with the remote XML version
            $wp_admin_bar->add_menu(array('id' => 'plugin_update_notifier', 'title' => '<span>' . BWL_WPFM_NOTIFIER_PLUGIN_NAME . ' <span id="ab-updates">New Updates</span></span>', 'href' => get_admin_url() . 'index.php?page=baf-plugin-update-notifier'));
        }
    }
}

add_action('admin_bar_menu', 'bwl_wpfm_update_notifier_bar_menu', 1000);

// The notifier page
function bwl_wpfm_update_notifier() {
    $xml = bwl_wpfm_get_latest_plugin_version(BWL_WPFM_PLUGIN_NOTIFIER_CACHE_INTERVAL); // Get the latest remote XML file on our server
    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . BWL_WPFM_NOTIFIER_PLUGIN_FOLDER_NAME . '/' . BWL_WPFM_NOTIFIER_PLUGIN_FILE_NAME); // Read plugin current version from the main plugin file 
    ?>

    <style>
        .update-nag { display: none; }
        #instructions {max-width: 670px;}
        h3.title {margin: 30px 0 0 0; padding: 30px 0 0 0; border-top: 1px solid #ddd;}
        .new_version_box{
            background: #d54e21;
            padding: 0 5px;
            font-weight: bold;
            color: #FFFFFF;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            -o-border-radius: 4px;
            border-radius: 4px;
            
        }
    </style>

    <div class="wrap">

        <div id="icon-tools" class="icon32"></div>
        <h2><?php echo BWL_WPFM_NOTIFIER_PLUGIN_NAME ?> Plugin Updates</h2>
        <div id="message" class="updated below-h2"><p><strong>There is a new version of the <?php echo BWL_WPFM_NOTIFIER_PLUGIN_NAME; ?> plugin available.</strong> You have version <?php echo $plugin_data['Version']; ?> installed. Update to version <span class="new_version_box"><?php echo $xml->latest; ?></span></p></div>

        <div id="instructions">
            <h3>Update Download and Instructions</h3>
            <p><strong>Please note:</strong> make a <strong>backup</strong> of the Plugin inside your WordPress installation folder <strong>/wp-content/plugins/<?php echo BWL_WPFM_NOTIFIER_PLUGIN_FOLDER_NAME; ?>/</strong></p>
            <p>1. To update the Plugin, login to <strong><a href="http://www.codecanyon.net/?ref=<?php echo BWL_WPFM_PLUGIN_NOTIFIER_CODECANYON_USERNAME; ?>" target="_blank">CodeCanyon</a></strong>, 
                head over to your <strong><a href="http://codecanyon.net/downloads/?ref=<?php echo BWL_WPFM_PLUGIN_NOTIFIER_CODECANYON_USERNAME; ?>" target="_blank">downloads</a></strong> section and re-download the plugin like you did when you bought it.</p>
            <p>2. Extract the zip's contents, look for the extracted plugin folder, and after you have all the new files upload them using FTP to the <strong>/wp-content/plugins/<?php echo BWL_WPFM_NOTIFIER_PLUGIN_FOLDER_NAME; ?>/</strong> folder overwriting the old ones (this is why it's important to backup any changes you've made to the plugin files).</p>
        </div>

        <h3 class="title">Changelog</h3>
        <?php echo $xml->changelog; ?>
        
        <h3 class="title">Need Support?</h3>
        
        <p>If you face any kind of issue regarding my product then please contact with me from my <a href="http://codecanyon.net/user/xenioushk/?ref=<?php echo BWL_WPFM_PLUGIN_NOTIFIER_CODECANYON_USERNAME; ?>" target="_blank">profile page</a>. After getting your email, I usually reply within 24-48 hours. But, during week-end(Sunday UTC+6 ), it can take a bit more time to reply. </p>

    </div>

<?php
}

// Get the remote XML file contents and return its data (Version and Changelog)
// Uses the cached version if available and inside the time interval defined
function bwl_wpfm_get_latest_plugin_version($interval) {
    $notifier_file_url = BWL_WPFM_NOTIFIER_PLUGIN_XML_FILE;
    $db_cache_field = 'wpfm-notifier-cache';
    $db_cache_field_last_updated = 'wpfm-notifier-cache-last-updated';
    $last = get_option($db_cache_field_last_updated);
    $now = time();
    // check the cache
    if (!$last || ( ( $now - $last ) > $interval )) {
        // cache doesn't exist, or is old, so refresh it
        if (function_exists('curl_init')) { // if cURL is available, use it...
            $ch = curl_init($notifier_file_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $cache = curl_exec($ch);
            curl_close($ch);
        } else {
            $cache = file_get_contents($notifier_file_url); // ...if not, use the common file_get_contents()
        }

        if ($cache) {
            // we got good results	
            update_option($db_cache_field, $cache);
            update_option($db_cache_field_last_updated, time());
        }
        // read from the cache file
        $notifier_data = get_option($db_cache_field);
    } else {
        // cache file is fresh enough, so read from it
        $notifier_data = get_option($db_cache_field);
    }

    // Let's see if the $xml data was returned as we expected it to.
    // If it didn't, use the default 1.0 as the latest version so that we don't have problems when the remote server hosting the XML file is down
    if (strpos((string) $notifier_data, '<notifier>') === false) {
        $notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog></notifier>';
    }

    // Load the remote XML data into a variable and return it
    $xml = simplexml_load_string($notifier_data);

    return $xml;
}
