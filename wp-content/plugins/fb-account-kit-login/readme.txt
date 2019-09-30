=== Passwordless Login with SMS & Email - Facebook Account Kit ===
Contributors: Infosatech
Tags: login, passwordless login, facebook, account kit, register, no password, auto login
Requires at least: 4.0
Tested up to: 5.1
Stable tag: 1.0.10
Requires PHP: 5.6
Donate link: http://bit.ly/2I0Gj60
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

🔥 The easiest and secure solution for login or register to WordPress by using SMS or Email Verification without any password using Facebook's Secure Authentication.

== Description ==

The Facebook Account Kit Login plugin brings a lightweight, secure, flexible, free and easy way to configure Password Less Login to WordPress website. This plugin helps to easily login or register to WordPress by using SMS on Phone or WhatsApp or Email Verification without any password. You can customize every setting of this plugin in the admin dashboard.

> GDPR compliant: does not collect any user data or does not send any data to any 3rd party website

### Features

 * **Login with SMS** (Phone).
 * **Login with WhatsApp**.
 * **Login with Email**.
 * **WooCommerce Support.**
 * Totally **Free of Cost SMS Service**.
 * **Shortcode** Compatible.
 * Dedicated **Widget**.
 * **Compatible with Jetpack**
 * **Compatible with Custom Login URL**
 
### This is how it works:

* Instead of asking users for a username and password when they try to log in to your website, it simply asks them for their phone number or email.
* Account Kit servers send an SMS with a confirmation code to the phone number (or WhatsApp Account) or send an email with a confirmation link to the email address to continue the login.
* If users fail to receive the SMS code, it offers two other methods that people can choose from the Phone call or Facebook notification.
* The SDK verifies the SMS confirmation code or monitors the status of the confirmation email. Account Kit may also verify the phone number directly without sending an SMS code.
* After successful verification of that authentication this plugin creates the log in WordPress cookie, successfully authenticating the user if the user alredy exists. Otherwise it will create a new user which depends upon plugin settings.

For more information about Facebook Account Kit please [click here](https://developers.facebook.com/docs/accountkit/overview).

#### Plugin Demo

> For Demo: [Click Here](https://demo.sayandatta.com/login)

#### Compatibility

* This plugin is fully compatible with WordPress Version 4.0 and beyond and also compatible with any WordPress theme.

#### Support

* Community support via the [support forums](https://wordpress.org/support/plugin/fb-account-kit-login) at WordPress.org.

#### Contribute
* Active development of this plugin is handled [on GitHub](https://github.com/iamsayan/fb-account-kit-login/).
* Feel free to [fork the project on GitHub](https://github.com/iamsayan/fb-account-kit-login/) and submit your contributions via pull request.

Like Facebook Account Kit Login plugin? Consider leaving a [5 star review](https://wordpress.org/support/plugin/fb-account-kit-login/reviews/?rate=5#new-post).

== Installation ==

1. Visit 'Plugins > Add New'
1. Search for 'Facebook Account Kit Login' and install it.
1. Or you can upload the `fb-account-kit-login` folder to the `/wp-content/plugins/` directory manually.
1. Activate Facebook Account Kit Login from your Plugins page.
1. After activation go to 'Account Kit' Option from Dashboard Menu.
1. Configure settings according to your need and save changes.

== Frequently Asked Questions ==

= What is Facebook Account Kit? =

Facebook Account Kit is a quick and easy way to log in to new apps using just your email address or phone number without any password. It helps you avoid creating another new username and password for every app you want to try. In addition, Account Kit doesn't need a Facebook account for you to log in to an app. Even if you have a Facebook account, you won't have to share information directly from your Facebook Profile to log in to apps with Account Kit.

= Do I need a Facebook account to use Account Kit? =

No, you don't need a Facebook account to log in to apps with Account Kit.

= How much the SMS costs? =

Facebook provides it for free.

= Does Account Kit work in my country? =

Account Kit works with [233 country codes](https://developers.facebook.com/docs/accountkit/languagescountries/) and in over [48 languages](https://developers.facebook.com/docs/accountkit/languages).

= Is there any link between my Facebook Account? =

Facebook account and the account kit authentication is fully separated and there is no connection between your Facebook account.

= How to migrate from DIGITS plugin? =

Migration from DIGITS plugin is very easy. If the username of your user is their phone number which is created by DIGITS plugin, then you can migrate from DIGITS to this plugin. Suppose your have 5 users and their country codes are +91, +880, +1, +856 and +86. Then you need to just add this code snippets to the end of your active theme's functions.php file:

`add_filter( 'fbak/custom_phone_number_format', 'fbak_add_digit_phone_support' );

function fbak_add_digit_phone_support( $phone ) {
    return str_replace( array( '91', '1' ), '', $phone ); // country codes without + sign
}`

== Screenshots ==

1. Login Page
2. SMS Login Screen
3. Email Login Screen
4. Default Login Form
5. Connected via Phone or Email with an Account
6. General Settings
7. SMS Login Settings
8. Email Login Settings
9. Display Settings
10. WooCommerce Settings
11. WooCommerce My Account
12. WooCommerce Checkout Page
13. WooCommerce Login Form
14. WooCommerce Profile Section
15. Others Settings
16. How it Works

== Changelog ==

= 1.0.10 =
Release Date: April 13, 2019

* Improved: Added a loader icon in profile page.
* Imporved: Login Message not displayed.
* Improved: Use of `esc_url()` in authentication redirect url.
* Fixed: Jetpack Login JS issue.
* Fixed: All in One WP Security reCaptcha CSS issue.
* Fixed: Advanced noCaptcha & invisible Captcha CSS issue.
* Fixed: Login No Captcha reCAPTCHA CSS issue.
* Fixed: Undefined plugin version constant error.

= 1.0.9 =
Release Date: April 11, 2019

* Added: A filter `fbak/custom_phone_number_format` to customize the phone number format which was used by **DIGITS** plugin to create WordPress Accounts.
* Added: A message to the users that shows until facebook account kit will authenticate them.
* Tweak: This plugin now automatically regenerate permalinks if any changes made in account kit endpoint url.
* Tweak: Changed some plugin settings label.
* Fixed: Conflict with Bootstrap CSS Class.
* Fixed: Missing HTML Tags in Admin Notice.

= 1.0.8 =
Release Date: April 8, 2019

* Tweak: Reduced plugin size.
* Removed: Some JS Files.
* Fixed: Some JS errors.

= 1.0.7 =
Release Date: April 6, 2019

* Tweak: Added a notice if a user account is not linked with Facebook Account Kit.
* Fixed: Some JS errors in Plugin Settings Page.
* Fixed: Delete Account option in user profile does not really disconnect from Account Kit.
* Fixed: CSS issue in WooCommerce register form.

= 1.0.6 =
Release Date: April 2, 2019

* Added: Complete WooCommerce Support.
* Removed: Some unwanted codes.

= 1.0.5 =
Release Date: March 21, 2019

* Improved: Admin UI.

= 1.0.4 =
Release Date: March 15, 2019

* Added: Account Kit Login Widget.
* Added: `fbak-sms-login` class for SMS Login and `fbak-email-login` class for Email Login from Navigation Menu directly.
* Added: An option to disable Account Kit Login on WordPress Login Page.
* Tweak: Now Account Kit SDK Loaded asynchronously to improve page loading speed.

= 1.0.3 =
Release Date: February 11, 2019

* Added: An option to redirect on custom page after a successful login.
* Added: An settings to set custom error text if an unregistered user tries to log in to the website.
* Tweak: Now Administrator can link the phone to email to the other existing account from their account.
* Tweak: Now every login via this plugin will be treated with wp_login action.
* Added: Some filters for future releases.

= 1.0.2 =
Release Date: January 27, 2019

* Fixed: Shortcode Issue.

= 1.0.1 =
Release Date: January 24, 2019

* Fixed: Image Paths.
* Fixed: Localization strings.

= 1.0.0 =
Release Date: January 23, 2019

* Initial release.