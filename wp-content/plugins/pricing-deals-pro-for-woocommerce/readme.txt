=== Pricing Deals Pro for WooCommerce ===
Contributors: varktech
Donate link: http://www.varktech.com/woocommerce/pricing-deals-pro-for-woocommerce/
Requires at least: 3.3
Tested up to: 5.1
Stable tag: 2.0.0.8
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*
Copyright 2017 AardvarkPC Services NZ, all rights reserved.  See license.txt for more details.
*

**Set up and manage incredibly flexible Pricing Deals and Marketing Promotions for your store - BOGO (Buy One Get One) Deals, Quantity bulk discounts, General Sales and Discounting, Group Pricing and More!**  

Please go to the following URL for all tutorials, documentation, etc.
http://www.varktech.com/woocommerce/pricing-deals-pro-for-woocommerce/

== Installation ==

First Download and install the Free Version from WordPress.Org

    - Use the built-in WordPress plugin installer to download, install and activate the Free version of the plugin hosted at wordpress.org/extend/plugins/maximum-purchase-for-woocommerce

Then Download and install the Pro Version from www.varktech.com

    - Download the zipped Pro Version of the plugin from Downloads ("http://www.varktech.com/products-page/download-manager/") using your Session Id or Name and Email from Purchase email issued at Checkout.
    - Upload and activate the zipped Pro Version of the plugin file to your site through the 'Plugins' menu in WordPress. 
    - Please Note: **Both the Free and Pro versions must be installed and active**
    
**WooCommerce 2.0 or above is needed to run this plugin successfully.**



= Plugin Requirements =

*   WooCommerce 2.0+
*   WordPress 3.3+
*   PHP 5+
*   Purchasing Deals for WooCommerce (free) - must be installed and active!


== Changelog ==

= 2.0.0.8 - 2019-04-09 =
* Fix - Groups selection function repaired.
* Fix - Memberships selection function repaired. 

= 2.0.0.5 - 2019-01-22 =
* Fix - On the Pricing Deals Settings page, in Coupon Discount mode, the "Cart Cross-Rule Limits" settings were having an issue, now repaired.
* Enhancement - For Pricing Deals Settings page -- Show Discount as Unit Price Discount or Coupon Discount 
           	If "Coupon Discount" selected, the 
           	** single automatic coupon name showing the discounting **  
           	can be custom named in the new "Automatic Coupon Name" field directly below 
* Enhancement - If rule discount is activated by a WOO coupon ("Discount Coupon Code"), the WOO coupon is now
           	automatically created as well. 
* Enhancement - In the front end, if rule discount is activated by a WOO coupon , the WOO coupon code shows
           	in a "coupon code ... 00" cart totals line.
           	There is now a switch to prevent the "...00" line showing in the front end.
           	On the Pricing Deals Settings page, look for:
		-- If rule Activated by Coupon Code - show the "coupon code ... 00"    Cart totals line"
		--- To prevent this line from showing in the front end, set to 'No'
* Enhancement - Allow additional product types from additional plugins

         	If added PRODUCT type from additional Plugins needed
                   Find all the Product types needed in your additional plugins, by searching for: "register_post_type".
                   In the "return" statement below, string them together as the example suggests
           
        	//add the 'add_filter...' statement to your theme/child-theme functions.php file 
        	add_filter( 'vtprd_use_additional_product_type', function() { return (array('product-type1','product-type2')); } ); 
          
                   THIS FILTER will add your added PRODUCT type to BOTH the PRODUCT selector AND the Pricing Deal Category selector
                   - so if you want a group of products to be included in a rule, you can either list them in the PRODUCT selector,
                   or make sure they participate in a Pricing Deal Category, which is then selected in your desired rule.

= 2.0.0.2 - 2018-11-11 =
* Fix - Code changes to support PHP 7.2


= 2.0.0.0 - 2018-07-01 =
* Enhancement - RULE SCREEN REWRITE - Rewrite of the way to choose a Category, Product, etc in a rule - now using Select2.
          	Added Group Choice by Customer (email and name), Brands and Groups. 
          	Each group choice will have an include/exclude option.

          	If using a BRANDS plugin not in the supported list, add support by doing the following:
          	//add the 'add_filter...' statement to your theme/child-theme functions.php file 
          	//change [brands plugin taxonomy] to the brands taxonomy of your brands plugin   
          	add_filter( 'vtprd_add_brands_taxonomy', function() { return  'brands plugin taxonomy'; } );
* Enhancement - New "Apply Discount to Equal or Lesser Value Item"
                      //--------------------------------------------------------------------                
                      // Discount Equal or Lesser Value Item first
                      //- Discount the item(s) in the GET Group of equal or lesser value to the most expensive item in the BUY Group    
                      //--------------------------------------------------------------------   
* Enhancement - New 'Copy to Support'button, for 1-click copying of rule for support emails 
* Enhancement - Removed Include/Exclude Box on Product screen.  All existing include/exclude converted into new rule structures.
          	Report documeting all original include/exclude settings available:
          	Ex: http://[your website name]/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_show_help_page&doThis=reportInclExclV2.0.0  
* Fix - Compatible again with Gravity Forms plugin.  
		Gravity Forms plugin (v2.3.2+)  interferes with the wordpress is-admin() conditional, 
			making Gravity Forms incompatible with Pricing Deals.  
		Replaced is-admin() conditional with REQUEST_URI test, where required for compatibility.  

= 1.1.8.1 - 2017-11-04 =
* Enhancement - Clone Rule Button now available on each rule.  Clicking on the button makes a rule copy, and the copy is placed in pending status.
* Fix - Currency-based rule repeat bug fixed 
* Fix - Catlog rule discount display now shows original list price as old price crossed out, even if a woo sale is price has bee overridden.
 

= 1.1.8.0 - 2017-10-21 =
* Enhancement - Bulk Processing in a single rule!  Use the Bulk option for simple deals as well! 
* Enhancement - CART Discount reporting in Order History (very basic formatting, will improve soon)
          	//to TURN OFF this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file  
          	add_filter( 'vtprd_do_not_show_order_history_report', function() { return  TRUE; } );  
* Fix - at update time, had a rare issue when an active rule had auto add for free.
* Fix - for the 'get variations' button in Rule UI product filter 


= 1.1.7.2 - 2017-10-10 =
* Enhancement - Prevent Pricing Deals and Pricing Deals Pro from background auto-updating.  These plugins must always
		These plugins must be updated *only* by and an admin click in response to an update nag ! 
* Enhancement - Show WOO sales badge if shop product discounted by CATALOG rule discount
          	//to TURN OFF this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file  
          	add_filter( 'vtprd_show_catalog_deal_sale_badge', function() { return  FALSE; } );  
* Fix - by Variation Name across Products now also applies to CATALOG rules
* Enhancement - Allow multiple coupons in coupon mode
          	//to TURN ON this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file
          	add_filter( 'vtprd_allow_multiple_coupons_in_coupon_mode', function() { return TRUE; } ); 
* Enhancement - By Role now tests against all roles user participates in (primary and secondary) 
* Fix - by Variation Name across Products in the Get Group now saving name correctly.
* Enhancement - New Filter to Allow full discount reporting on customer emails for Units discounting
          	//to TURN ON this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file 
          	add_filter( 'vtprd_always_show_email_discount_table', function() { return TRUE; } );
* Fix - VTPRD_PURCHASE_LOG definition changed, 2 columns now Longtext.
* Fix - Various Woocommerce 3.0, 3.12 and 3.2 log warnings resolved. 
* Enhancement - Limit Cart Rule Discounting to a Single Rule or Rule type
          	new setting: - Pricing Deal Settings Page => 'Cart Cross-Rule Limits'
* Enhancement - New Filter vtprd_cumulativeRulePricing_custom_criteria.  Allows custom control of rule interaction.
          	Using this filter, create your own custom function to manage Rule interaction
          	(folow the example for using the 'vtprd_additional_inpop_include_criteria' in the PRO version apply-rules.php)


= 1.1.7.1 - 2017-05-26 =
* Enhancement - Filter to allow page Refresh of the CART page after an AJAX update 
   		Valid Values for FILTER:
  		    CouponOnly - only send JS on Cart Page when an existing rule is actuated by a Coupon
  		    Never - never send the JS on Cart Page [DEFAULT] 
 		    Always - always on Cart Page 
      
            	//Be sure to clear the cache and start a fresh browser session when testing this...
            	function js_trigger_cart_page_reload() {
           		 return 'Never';  //valid values: 'CouponOnly' / 'Never' / 'Always'
          	}
          	add_filter('vtprd_js_trigger_cart_page_reload', 'js_trigger_cart_page_reload', 10);

          	//Alternative: same solution with less code, no additional function:
          	add_filter( 'vtprd_js_trigger_cart_page_reload', function() { return  'Never'; } );  //valid values: 'CouponOnly' / 'Never' / 'Always'


* Enhancement - FOR Cart rule with 'Buy amount applies to' = EAch, and discoung group same as buy group,
		process EACH product matching the choice criteria INDIVIDUALLY.
		*NOTE* if 'Buy Group RULE USAGE COUNT' = apply rule once per cart, the rule will be applied
		**once per product**
* Fix - Remove warnings on coupon use
* Fix - IF auto add to cart granted and user logs in, correct auto added product count will be maintained.

= 1.1.7   - 2017-03-14 =
* Enhancement - Changes required to be compatable with WOO 3.0+

= 1.1.6.7 - 2016-07-18 =
* Enhancement - Improved "Cheapest" Deals: 
		Cheapest in the Get Product Group Filter, applies to Complete Group or ENTIRE cart!
		Cheapest option now in the Rule's Blueprint area - **please hover** to read the how-to.


= 1.1.6.3 - 2016-07-03 =
* Fix - repair a rare auto add for free bug

= 1.1.5 - 2016-05-29 =
* Enhancement - Licensing Registration

= 1.1.1.2 - 2015-11-07 =
* Fix - Coupon discount mini-cart intermittent display issue on 1st time 


= 1.1.1.1 - 2015-09-28 =
* Fix - Autoadd Free item for product not yet in cart. 

= 1.1.1 - 2015-09-26 =
* Enhancement - Compatibility with woocommerce-measurement-price-calculator now available. 

= 1.1.0.9 - 2015-07-31 =
* Fix - Other rule discounts = no
* Fix - improve efficiency for Rule Discounts activated by Coupon

= 1.1.0.8 - 2015-07-25 =
* Fix - Wp-admin Rule editing - if advanced field in error and basic rule showing, 
	switch to advanced rule in update process to expose errored field. 
* Fix - fix to user tax exempt status - saved to user updated, not user making the update!
* Enhancement - New Advanced Rule Option - Rule Discount applies only 
			when a specific Coupon Code is redeemed for the cart:
		- Coupon code is entered in the Pricing Deals Rule in the Discount box area (opotional!)
		- The rule discount will not activate in the Cart for a client purchase, 
			until the correct coupon code is presented.
		- Best to use a coupon set to 'Cart Discount' and 'coupon amount' = 0.

= 1.1.0.6 - 2015-07-07 =
* Fix - Auto add free item function. 
* Enhancement - Auto add free item function:
		- Can now add multiple free items using the Get Group Amount count.
		- New Filter ==> $0 Price shown as 'Free' unless overridden by filter:
			add_filter('vtprd_show_zero_price_as_free',FALSE); 
			(in your theme's functions.php file)

= 1.1 - 2015-04-19 =
* Enhancement - In the Buy Group Filter, added Logged-in Role to Single product and single product with variations:
	By Single Product with Variations   (+ Logged-in Role) 
	By Single Product    (+ Logged-in Role)          

= 1.0.6.2 - 2015-04-10 =
* Fix - Cart issue if only Catalog discount used, now fixed.

= 1.0.6.1 - 2015-04-09 =
* Fix - Balance out discount if $$ discount greater than item value

= 1.0.6.0 - 2014-12-11 =
* Fix - Short msg stripslashes fix

= 1.0.5.9 - 2014-09-04 =
* Fix - Rare Discount by each counting issue  - matches Free v1.0.8.7

= 1.0.5.8 - 2014-08-16 =
* Fix - Rare variation categories list issue  - matches Free v1.0.8.6
* Enhancement - Variation Attributes

= 1.0.5.7 - 2014-08-6 =
* Enhancement - Pick up User Login and apply to Cart realtime  - matches Free v1.0.8.4
* Enhancement - Upgraded discount exclusion for pricing tiers, when "Discount Applies to ALL" 
* Enhancement - Pick up admin changes to Catalog rules realtime

= 1.0.5.6 - 2014-07-30 =
* Fix - Auto Insert free product name in discount reporting - matches Free v1.0.8.2 

= 1.0.5.5 - 2014-07-27 =
* Fix - Refactored "Discount This" limits
	If 'Buy Something, Discount This Item' is selected,
	Get Group Amount is now *an absolute amount* of units/$$ applied to
	working with the Get Group Repeat amount 

= 1.0.5.4 - 2014-06-30 =
* Fix - Inclusion List
* Enhancement - math improvement for group pricing

= 1.0.5.3 - 2014-06-19 =
* Enhancement - VAT pricing - include Woo wildcard in suffix text
* Enhancement - Taxation messaging as needed in checkout
* Fix - PHP floating point rounding

= 1.0.5.2 - 2014-06-05 =
* Fix - post-purchase processing

= 1.0.5.1 - 2014-05-29 =
* Fix - Package Pricing
* Fix - group pricing rounding issue

= 1.0.5 - 2014-05-07 =
* Enhancement - New apply_filters for additional population criteria in core/vtprd-apply-rules.php => usage example at bottom of file
* Fix -VAT inclusive for Cart totals
* Fix -Warnings fix
* Fix -$product_variations_list

= 1.0.5 - 2014-5-08=
* Fix -VAT inclusive for Cart totals
* Fix -Warnings fix
* Enhancement - hook added for additional population logic - filter "vtprd_additional_inpop_include_criteria"
* Fix -$product_variations_list fix


= 1.0.4 - 2014-05-01 =
* Fix - lifetime counter educated to count all products as a single iteration, if discount is applied to the group.
* Fix - apply skip sale logic fix

= 1.0.3 - 2014-04-26 =
* Fix - warnings for lifetime address info
* Fix - Get group repeat logic
* Enhancement - e_notices made switchable, based on 'Test Debugging Mode Turned On' settings switch


= 1.0.2 - 2014-04-14 =
* Fix - warnings on UI update error

= 1.0.1 - 2014-03-31 =
* Fix - warning on install in front end if no rule
* Fix - removed red notices to change host timezone on install
* Fix - removed deprecated WOO hook
* Fix - BOGO discount this fix
* Enhancement - reformatted the rule screen, hover help now applies to Label, rather than data field 

= 1.0 - 2014-03-15 =
* Initial Public Release

=====================
== Terms and Conditions ==
=====================

Order Acceptance
----------------

Order acceptance will take place on the dispatch of the products ordered.
Non-acceptance of an order by VarkTech.com may be a result of one of the following:

    *Our inability to obtain authorization for your payment.
    *The identification of a pricing or product description error.

 

Product Delivery
----------------

All purchases on this website regard software and support, and there is no physical product shipped.   All software is downloadable, and is can be downloaded following receipt of the order confirmation email.  Once you have made a purchase, you will be able to download your plugins from http://www.varktech.com/download-pro-plugins/, using your Session Id or Name and Email from Checkout.


Licensing
---------

Purchase of a single software plugin grants the purchaser a single website license without time limit, for both a production website and a development version of the same website only.  This license also grants the purchaser access to new versions of the software, as they become available.


Support and Refund Policy
-------------------------

We do not issue refunds once the order is accomplished.  Varktech will work with the customer to try to solve any compatibility issues with other major WordPress plugins, but VarkTech is not responsible for compatibility issues with 3rd party plugins.  Any issues of theme compatibility fall outside the Support scope