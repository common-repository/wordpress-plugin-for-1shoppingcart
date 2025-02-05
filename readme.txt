=== Free Wordpress Plugin For 1ShoppingCart.com ===
Contributors: equalserving
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=H8KWRPTET2SK2&lc=US&item_name=Free%20Wordpress%20Plugin%20For%201ShoppingCart.com&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: 1ShoppingCart,1ShoppingCart.com,mcssl.com,mcssl
Requires at least: 3.0
Tested up to: 4.9.1
Stable tag: 0.9.2

Using shortcodes, you can easily display product details from your 1ShoppingCart.com product catalog on pages or posts within your Wordpress site.

== Description ==

Check the [DEMO](http://1shopdemo.equalserving.com/free) on our site.

Are you tired of having to enter your product details twice - once into 1ShoppingCart.com and then again in your Wordpress website? The Free Wordpress Plugin For 1ShoppingCart is the solution for you.

Using shortcodes, you can easily display product details from your 1ShoppingCart.com product catalog on pages or posts within your Wordpress site. All that needs to be entered on the page or post is the title and the shortcut code [es1sc_prodlist]. The shortcode [es1sc_prodlist] without any additional arguments will display your entire active product catalog. You can limit the list to specific products by adding the argument prd_ids to the shortcode such as -
[es1sc_prodlist prd_ids="8644152,8644145,8580674,8569588,8569508,8361626"].

The plugin uses the 1ShoppingCart.com API. Since you enter what products are on sale along with the sale price into the 1ShoppingCart.com interface, the API knows which products are on sale and displays the proper price.

Unfortunately, because Bundled Offers are not really products, the API does not support any bundled offers that are in the catalog. Bundled products would have to be maintained manually on the Wordpress site.

Looking for more robust features? Check out our [Premium version](http://www.equalserving.com/wordpress-plugin-for-1shoppingcart-com-v2/). 

== Installation ==

Upload the Free Wordpress Plugin For 1ShoppingCart.com plugin to your blog, Activate it, then enter your 1ShoppingCart.com API settings on the Settings -> 1SC Settings page.

That's it! You're done!

This plugin is provided for FREE by [EqualServing.Com](http://EqualServing.com "EqualServing.com").

== Frequently Asked Questions ==

= The product display is not very pleasing.  How can I easily improve it? =

Surround the shortcode in a div tag such as - 

&lt;div id="product_catalog"&gt;

&lt;h2&gt;Featured Products&lt;/h2&gt;

[es1sc_prodlist prd_ids="8644152,8644145,8580674,8569588,8569508,8361626"]

&lt;/div&gt;


or 


&lt;div id="product_catalog"&gt;

&lt;h2&gt;All Products&lt;/h2&gt;

[es1sc_prodlist]

&lt;/div&gt;



Then add the following to your theme's stylesheet.

&#35;product_catalog h2 {color: #FF4D00; font-size: 2em; line-height: 2.2em; margin: 5px 0; }

&#35;product_catalog div.product {border-bottom: 1px dotted #FF9900;clear: both;font-size: 12px; padding: 5px 0;}

&#35;product_catalog .product .product_name {text-align: left; ; display:block; font-weight: bold; font-size: 1.4em;}

&#35;product_catalog div.product img.product_image {float: left; padding: 5px 5px 5px 0; width: 125px; }

&#35;product_catalog div.product .product_details {text-align: right; display:block;}

&#35;product_catalog div.product .product_details .description {text-align: left; font-size: 1.2em; display: block;}

&#35;product_catalog div.product .price {font-weight: bold; display: block;}

&#35;product_catalog div.product .product_details .sku {text-align: left; font-size: 0.85em; display: block;}

&#35;product_catalog div.product .product_details .buy-now {display: block; padding: 0 0 8px 0;}


= Additional information needed? =

Please read additional information at [EqualServing.com](http://www.equalserving.com/2011/02/1shoppingcart-wordpress-plugin/ "EqualServing.com").
Or our at our [Equalserving.com Support Forum](https://equalserving.uservoice.com/ "EqualServing.UserVoice.com").

== Screenshots ==

1. 1SC Setting page. screenshot-1.png.
2. Example of the product listing on a page. screenshot-2.png.

== Changelog ==
= Version 0.9.2 =
* Tested with WP 4.9.1
= Version 0.9.1 =
* Refresh cache
= Version 0.9.0 =
* Added new field (MID) to the configuration administration panel. 
* Rename class to prevent conflict with WishList.
= Version 0.8.0 =
* Add troubleshooting item to help section to help admins configure the plugins settings correctly for use with the 1ShoppingCart.com API.
* Change method for accessing API server from cURL to Wordpress HTTP API. 
= Version 0.7.0 =
* Various changes to admin panel. Do not rely on 1SC for determination of sale items. Instead check sale and regular price fields.
= Version 0.6.2 =
* Add banner-772x250.png.
= Version 0.6.1 =
* Update error message.
= Version 0.6 =
* If the 1ShoppingCart.com API credentials are incorrect and error message will display in place of the shortcode. An email will also be sent to the administrator with the same error message. This versionalos corrects a rounding issues on some servers.
= Version 0.5 =
* number_format issue on some installations.
= Version 0.4 =
* Re-tag
= Version 0.3 =
* Fix typo in screenshot and allow a domain masked buy now url or one that uses the mcssl.com url.
= Version 0.2 =
* Fix typos and provide more help on the settings page.
= Version 0.1 =
* First release.
