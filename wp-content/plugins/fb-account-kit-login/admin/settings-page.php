<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Facebook Account Kit Login
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */
?>

<div class="wrap">
    <div class="head-wrap">
        <h1 class="title">Facebook Account Kit Login<span class="title-count"><?php echo FBAK_PLUGIN_VERSION ?></span></h1>
        <div><?php _e( 'It helps to easily login or register to wordpress by using SMS or Email Verification without any password.', 'fb-account-kit-login' ); ?></div><hr>
        <div class="top-sharebar">
            <a class="share-btn rate-btn" href="https://wordpress.org/support/plugin/fb-account-kit-login/reviews/?filter=5#new-post" target="_blank" title="<?php _e( 'Please rate 5 stars if you like', 'fb-account-kit-login' ); ?> Facebook Account Kit Login"><span class="dashicons dashicons-star-filled"></span> <?php _e( 'Rate 5 stars', 'fb-account-kit-login' ); ?></a>
            <a class="share-btn twitter" href="https://twitter.com/home?status=Check%20out%20Facebook%20Account%20Kit,%20a%20%23WordPress%20%23plugin%20that%20helps%20to%20easily%20login%20or%20register%20to%20WordPress%20website%20by%20using%20SMS%20or%20Email%20Verification%20without%20any%20password.%20https%3A//wordpress.org/plugins/fb-account-kit-login/%20via%20%40im_sayaan" target="_blank"><span class="dashicons dashicons-twitter"></span> <?php _e( 'Tweet about Facebook Account Kit Login', 'fb-account-kit-login' ); ?></a>
        </div>
    </div>
    <div id="nav-container" class="nav-tab-wrapper">
        <a href="#general" class="nav-tab active" id="btn1"><span class="dashicons dashicons-admin-generic" style="padding-top: 2px;"></span> <?php _e( 'General', 'fb-account-kit-login' ); ?></a>
        <a href="#sms" class="nav-tab" id="btn2"><span class="dashicons dashicons-admin-comments" style="padding-top: 2px;"></span> <?php _e( 'SMS Login', 'fb-account-kit-login' ); ?></a>
        <a href="#email" class="nav-tab" id="btn3"><span class="dashicons dashicons-email" style="padding-top: 2px;"></span> <?php _e( 'Email Login', 'fb-account-kit-login' ); ?></a>
        <a href="#display" class="nav-tab" id="btn4"><span class="dashicons dashicons-visibility" style="padding-top: 2px;"></span> <?php _e( 'Display', 'fb-account-kit-login' ); ?></a>
        <?php if( class_exists( 'WooCommerce' ) ) { ?><a href="#woocommerce" class="nav-tab" id="btn5"><span class="dashicons dashicons-cart" style="padding-top: 2px;"></span> <?php _e( 'WooCommerce', 'fb-account-kit-login' ); ?></a><?php } ?>
        <a href="#others" class="nav-tab" id="btn6"><span class="dashicons dashicons-screenoptions" style="padding-top: 2px;"></span> <?php _e( 'Others', 'fb-account-kit-login' ); ?></a>
        <a href="#shortcode" class="nav-tab" id="btn7"><span class="dashicons dashicons-editor-code" style="padding-top: 2px;"></span> <?php _e( 'Shortcode', 'fb-account-kit-login' ); ?></a>
    </div>
    <script>
        var header = document.getElementById("nav-container");
        var btns = header.getElementsByClassName("nav-tab");
        for (var i = 0; i < btns.length; i++) {
            btns[i].addEventListener("click", function() {
            var current = document.getElementsByClassName("active");
            current[0].className = current[0].className.replace(" active", "");
            this.className += " active";
            });
        }
    </script>
    <div id="poststuff" style="padding-top: 0;">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <form id="main-form" method="post" action="options.php">
                <?php settings_fields('fbak_plugin_settings_fields'); ?>
			        <div id="fbak-general" class="postbox">
				        <h3 class="hndle fbak-hndle">
                            <span class="fbak-heading">
                                <?php _e( 'General Options', 'fb-account-kit-login' ); ?>
                            </span>
                        </h3>
				        <div class="inside fbak-inside">
                            <?php do_settings_sections('fbak_plugin_general_option'); ?>
                            <?php submit_button( __( 'Save Settings', 'fb-account-kit-login' ), 'primary save-settings', 'submit-general' ); ?>
                        </div>
                    </div>
                    <div id="fbak-sms" class="postbox" style="display: none;">
				        <h3 class="hndle fbak-hndle">
                            <span class="fbak-heading">
                                <?php _e( 'SMS Login', 'fb-account-kit-login' ); ?>
                            </span>
                        </h3>
				        <div class="inside fbak-inside">
                            <?php do_settings_sections('fbak_plugin_sms_option'); ?>
                            <br><b><?php _e( 'Note:', 'fb-account-kit-login' ); ?></b> <i><?php _e( 'You can use <code>fbak-sms-login</code> CSS Class as Navigation ( Appearence > Menus > CSS Classes ) Button Class & <code>#</code> as URL.', 'fb-account-kit-login' ); ?></i>
                            <?php submit_button( __( 'Save Settings', 'fb-account-kit-login' ), 'primary save-settings', 'submit-sms' ); ?>
                        </div>
                    </div>
                    <div id="fbak-email" class="postbox" style="display: none;">
				        <h3 class="hndle fbak-hndle">
                            <span class="fbak-heading">
                                <?php _e( 'Email Login', 'fb-account-kit-login' ); ?>
                            </span>
                        </h3>
				        <div class="inside fbak-inside">
                            <?php do_settings_sections('fbak_plugin_email_option'); ?>
                            <br><b><?php _e( 'Note:', 'fb-account-kit-login' ); ?></b> <i><?php _e( 'You can use <code>fbak-email-login</code> CSS Class as Navigation ( Appearence > Menus > CSS Classes ) Button Class & <code>#</code> as URL.', 'fb-account-kit-login' ); ?></i>
                            <?php submit_button( __( 'Save Settings', 'fb-account-kit-login' ), 'primary save-settings', 'submit-email' ); ?>
                        </div>
                    </div>
                    <div id="fbak-display" class="postbox" style="display: none;">
				        <h3 class="hndle fbak-hndle">
                            <span class="fbak-heading">
                                <?php _e( 'Display Options', 'fb-account-kit-login' ); ?>
                            </span>
                        </h3>
				        <div class="inside fbak-inside">
                            <?php do_settings_sections('fbak_plugin_display_option'); ?>
                            <?php submit_button( __( 'Save Settings', 'fb-account-kit-login' ), 'primary save-settings', 'submit-display' ); ?>
                        </div>
                    </div>
                    <?php if( class_exists( 'WooCommerce' ) ) { ?>
                    <div id="fbak-woo" class="postbox" style="display: none;">
				        <h3 class="hndle fbak-hndle">
                            <span class="fbak-heading">
                                <?php _e( 'WooCommerce Options', 'fb-account-kit-login' ); ?>
                            </span>
                        </h3>
				        <div class="inside fbak-inside">
                            <?php do_settings_sections('fbak_plugin_woo_option'); ?>
                            <?php submit_button( __( 'Save Settings', 'fb-account-kit-login' ), 'primary save-settings', 'submit-woo' ); ?>
                        </div>
                    </div>
                    <?php } ?>
                    <div id="fbak-misc" class="postbox" style="display: none;">
				        <h3 class="hndle fbak-hndle">
                            <span class="fbak-heading">
                                <?php _e( 'Miscellaneous Options', 'fb-account-kit-login' ); ?>
                            </span>
                        </h3>
				        <div class="inside fbak-inside">
                            <?php do_settings_sections('fbak_plugin_misc_option'); ?>
                            <?php submit_button( __( 'Save Settings', 'fb-account-kit-login' ), 'primary save-settings', 'submit-misc' ); ?>
                        </div>
                    </div>
                    <div id="fbak-shortcode" class="postbox" style="display: none;">
				        <h3 class="hndle fbak-hndle">
                            <span class="fbak-heading">
                                <?php _e( 'Shortcode Info', 'fb-account-kit-login' ); ?>
                            </span>
                        </h3>
				        <div class="inside fbak-inside" style="padding-bottom: 15px;">
                            <p><?php printf( __( 'You can insert the login buttons manually in any page or post or template by simply using the shortcode %1$s. To enter the shortcode directly into templates using PHP, enter %2$s', 'fb-account-kit-login' ), '<code>[fbak-account-kit]</code>', '<code>echo do_shortcode(&#39;[fbak-account-kit]&#39;);</code>' ); ?></strong></p>
                            <p><?php _e( 'You can also use the options or attributes below to override the default settings.', 'fb-account-kit-login' ); ?></p>
                            <li><strong>sms_login</strong> - <?php _e( 'can be', 'fb-account-kit-login' ); ?> <strong>yes/no</strong></li>
                            <li><strong>email_login</strong> - <?php _e( 'can be', 'fb-account-kit-login' ); ?> <strong>yes/no</strong></li>
                            <li><strong>sms_class</strong> - <?php _e( 'set the sms button class, defaults to', 'fb-account-kit-login' ); ?> <strong>button btn</strong></li>
                            <li><strong>email_class</strong> - <?php _e( 'set the email button class, defaults to', 'fb-account-kit-login' ); ?> <strong>button btn</strong></li>
                            <li><strong>sms_label</strong> - <?php _e( 'set the sms button text, defaults to <strong>Login with SMS</strong>', 'fb-account-kit-login' ); ?></li>
                            <li><strong>email_label</strong> - <?php _e( 'set the email button text, defaults to <strong>Login with Email</strong>', 'fb-account-kit-login' ); ?></li>
                            <li><strong>description</strong> - <?php _e( 'set the description text to show on login page.', 'fb-account-kit-login' ); ?></li>
                        </div>
                    </div>
                    <div class="coffee-box">
                        <div class="coffee-amt-wrap">
                            <p><select class="coffee-amt">
                                <option value="5usd">$5</option>
                                <option value="6usd">$6</option>
                                <option value="7usd">$7</option>
                                <option value="8usd">$8</option>
                                <option value="9usd">$9</option>
                                <option value="10usd" selected="selected">$10</option>
                                <option value="11usd">$11</option>
                                <option value="12usd">$12</option>
                                <option value="13usd">$13</option>
                                <option value="14usd">$14</option>
                                <option value="15usd">$15</option>
                                <option value=""><?php _e( 'Custom', 'fb-account-kit-login' ); ?></option>
                            </select></p>
                            <a class="button button-primary buy-coffee-btn" style="margin-left: 2px;" href="https://www.paypal.me/iamsayan/10usd" data-link="https://www.paypal.me/iamsayan/" target="_blank"><?php _e( 'Buy me a coffee!', 'fb-account-kit-login' ); ?></a>
                        </div>
                        <span class="coffee-heading"><?php _e( 'Buy me a coffee!', 'fb-account-kit-login' ); ?></span>
                        <p style="text-align: justify;"><?php printf( __( 'Thank you for using %s. If you found the plugin useful buy me a coffee! Your donation will motivate and make me happy for all the efforts. You can donate via PayPal.', 'fb-account-kit-login' ), '<strong>Facebook Account Kit Login v' . FBAK_PLUGIN_VERSION . '</strong>' ); ?></strong></p>
                        <p style="text-align: justify; font-size: 12px; font-style: italic;">Developed with <span style="color:#e25555;">♥</span> by <a href="https://sayandatta.com" target="_blank" style="font-weight: 500;">Sayan Datta</a> | <a href="https://github.com/iamsayan/fb-account-kit-login" target="_blank" style="font-weight: 500;">GitHub</a> | <a href="https://wordpress.org/support/plugin/fb-account-kit-login" target="_blank" style="font-weight: 500;">Support</a> | <a href="https://translate.wordpress.org/projects/wp-plugins/fb-account-kit-login/" target="_blank" style="font-weight: 500;">Translate</a> | <a href="https://wordpress.org/support/plugin/fb-account-kit-login/reviews/?rate=5#new-post" target="_blank" style="font-weight: 500;">Rate it</a> (<span style="color:#ffa000;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>) on WordPress.org, if you like this plugin.</p>
                    </div>
                    <div id="progressMessage" class="progressModal" style="display:none;">
                        <?php _e( 'Please wait...', 'fb-account-kit-login' ); ?>
                    </div>
                    <div id="saveMessage" class="successModal" style="display:none;">
                        <p class="fbak-success-msg">
                            <?php _e( 'Settings Saved Successfully!', 'fb-account-kit-login' ); ?>
                        </p>
                    </div>
                </form>
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $('#main-form').submit(function() {
                            $('#progressMessage').show();
                            $(".save-settings").addClass("disabled");
                            $(".save-settings").val("<?php _e( 'Saving...', 'fb-account-kit-login' ); ?>");
                            $(this).ajaxSubmit({
                                success: function() {
                                    $('#progressMessage').fadeOut();
                                    $('#saveMessage').show().delay(4000).fadeOut();
                                    $(".save-settings").removeClass("disabled");
                                    $(".save-settings").val("<?php _e( 'Save Settings', 'fb-account-kit-login' ); ?>");
                                    if ($('#changetrigger').val() == 'yes') {
                                        $.ajax({
                                            type: "POST",
                                            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                                            dataType: "json",
                                            data: {
                                                action: "fbak_trigger_flush_rewrite_rules",
                                            }
                                        });
                                    }
                                }
                            });
                            return false;
                        });
                        $("#fbak-woo-ep").change(function() {
					        $('#changetrigger').val('yes');
                        });
                    });
                </script>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h3 class="hndle fbak-hndle" style="text-align: center;"><?php _e( 'My Other Plugins!', 'fb-account-kit-login' ); ?></h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-clock"></span>
                            <label>
                                <strong><a href="https://wordpress.org/plugins/wp-last-modified-info/" target="_blank">WP Last Modified Info</a>: </strong>
                                <?php _e( 'Display last update date and time on frontend with \'dateModified\' Schema Markup.', 'fb-account-kit-login' ); ?>
                            </label>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-admin-comments"></span>
                            <label>
                                <strong><a href="https://wordpress.org/plugins/ultimate-facebook-comments/" target="_blank">Ultimate Facebook Comments</a>: </strong>
                                <?php _e( 'Ultimate Facebook Comments Solution with instant email notification.', 'fb-account-kit-login' ); ?>
                            </label>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-admin-links"></span>
                            <label>
                                <strong><a href="https://wordpress.org/plugins/change-wp-page-permalinks/" target="_blank">WP Page Permalink Extension</a>: </strong>
                                <?php _e( 'Add any page extension like .html, .php, .aspx, .htm, .asp, .shtml only to pages.', 'fb-account-kit-login' ); ?>
                            </label>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-megaphone"></span>
                            <label>
                                <strong><a href="https://wordpress.org/plugins/simple-posts-ticker/" target="_blank">Simple Posts Ticker</a>: </strong>
                                <?php _e( 'Simple Posts Ticker is a small tool that shows your most recent posts in a marquee style.', 'fb-account-kit-login' ); ?>
                            </label>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <label>
                                <strong><a href="https://wordpress.org/plugins/remove-wp-meta-tags/" target="_blank">Easy Header Footer</a>: </strong>
                                <?php _e( 'Add custom code and remove the unwanted meta tags, links from the source code and many more.', 'fb-account-kit-login' ); ?>
                            </label>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-update"></span>
                            <label>
                                <strong><a href="https://wordpress.org/plugins/wp-auto-republish/" target="_blank">WP Auto Republish</a>: </strong>
                                <?php _e( 'Automatically republish you old evergreen content to grab better SEO.', 'fb-account-kit-login' ); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </diV>
        </div>
    </div>
</div>