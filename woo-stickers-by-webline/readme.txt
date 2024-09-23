=== WOO Stickers by Webline ===
Contributors: weblineindia
Tags: woocommerce stickers, woocommerce products stickers, product stickers, category stickers, product badge, woocommerce product badge, custom product badge, sitcker animation, scheduled sticker
Requires at least: 3.5
Tested up to: 6.6
Stable tag: 1.2.3
License: GPLv2 or later

Enhance your buyer's shopping experience by adding various stickers to your products in your WooCommerce Shop. Various stickers are available like stickers for New, On Sale, Soldout Products, Category Stickers and you can use your custom stickers.

== Description ==

Enhance your buyer's shopping experience by adding various stickers to your products in your WooCommerce Shop. Various stickers are available like stickers for New, On Sale, Soldout Products, Category Stickers and you can use your custom stickers.

Add various stickers to your products easily from admin panel without any extra efforts or any knowledge of programming.

= Key Features =
- Stickers for New, On Sale, Soldout Products and Category Stickers. 
- Admin can even upload and use their Custom Stickers.
- Admin can define number of days to define product as new.
- Admin can configure different style of stickers.
- Admin can enable/disable this sticker feature.
- Admin can configure stickers for Product List as well for Product Detail page.
- Admin can configure/override stickers at category and product level.
- Admin can choose Image/Text as a sticker option.
- Admin can configure custom sticker group for products and also override their options at Category / Product level.
- Text type stickers are configurable with color combination.
- Admin can add custom CSS from settings.

= Premium Features =
- Admin can add custom settings to rotate the stickers.
- Admin can add animation to the stickers.
- Rotate and animation settings will work seamlessly for regular stickers.
- Admin can add scheduled stickers for particular period of time.
- Admin can upload custom image or text for scheduled sticker.

== NOTE == 

This plugin is an Open Source Software and we would be happy to have people contribute to our plugin. Please contact us here to talk to our <a href="https://www.weblineindia.com/contact-us.html">software development team</a>, if you would like to contribute to this plugin and help make it better. 

If you like this plugin then please rate our plugin to help us spread the word.


== Installation ==

1. Upload 'woo-stickers-by-webline' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Done!

== Screenshots ==

1. Stickers on listing page.
2. The Round sticker of Sold product on detail page.
3. The Text sticker of New and On Sale product on detail page.
4. The Text sticker of product setup as custom type on detail page.
5. The Category Sticker on frontend.
6. WOO Stickers menu in admin side under Settings.
7. General Configuration of WooStickers Plugin.
8. New Product Configuration of WooStickers Plugin.
9. Sale Configuration of WooStickers Plugin.
10. Sale Configuration with default image option of WooStickers Plugin.
11. Sold Configuration of WooStickers Plugin.
12. Custom Sticker Configuration of WooStickers Plugin.
13. Woo Stickers Configuration on Category level.
14. Woo Stickers Configuration on Product level.
15. Category Stickers Configuration.


== Frequently Asked Questions ==

= Sticker image is not displayed in product detail page =

First verify if you have enabled this option in the General Configuration -> Enable Sticker On Product Details Page as "YES"

Also check appropriate settings e.g if this issue persist for "NEW Sticker" then go to New Sticker configuration and Enable Product Sticker as "YES"

If you still find this problem then override the CSS class to match your Theme style and increase the "z-index" amount.

We have tested plugin in various standard themes of WordPress. In few of the themes where the theme structure is not proper, in such cases you will have to override specific class of your Theme style and set margin according to that.

If you still need help, feel free to contact our <a href="https://www.weblineindia.com/wordpress-development.html">WordPress developers</a> anytime. 

= Custom sticker image is repeating. =

Our plugin supports default image dimension of 54 X 54px for custom sticker images. If the dimension of your custom stickers is not same size size then this issue will occur. To solve this issue, either you have to override the class "custom_sticker_image" as per your website's theme style or you can create custom image according to our standard dimensions.

= Sales badge from the default theme duplicate with the plugin. =

If you notice the sales badge from the default theme or WooCommerce appearing repeatedly, it may require adjustment through CSS. You can resolve this by adding the following CSS rule:
**.classname-of-sale {display: none;}**
This will hide the duplicate sales badge.

= Sold badge from the default theme duplicate with the plugin. =

If you notice the Sold badge from the default theme or WooCommerce appearing repeatedly, it may require adjustment through CSS. You can resolve this by adding the following CSS rule:

`.classname-of-sold {
    display: none;
}`

This will hide the duplicate Sold badge.

== Changelog ==

= 1.2.3 =

Release Date: Sept 11, 2024

* Fix: Checked compatibility with WordPress version 6.6.2

= 1.2.2 =

Release Date: July 22, 2024

* Fix: Minor bug fixes.
* Fix: Checked compatibility with WordPress version 6.6

= 1.2.1 =

Release Date: July 09, 2024

* Enhancement: Introduced options for Sticker rotation and animation.
* Enhancement: Introduced options for Scheduled Sticker.
* Fix: Checked compatibility with WordPress version 6.5.5

= 1.2.0 =

Release Date: June 21, 2024

* Fix: Checked compatibility with WordPress version 6.5.4

= 1.1.9 =

Release Date: March 21, 2024

* Enhancement: Minor UI optimization for Information Banner.

= 1.1.8 =

Release Date: March 06, 2024

* Fix: Checked compatibility with WordPress version 6.4.3
* Fix: Resolved issue with option resetting after adding a category.
* Fix: Minor style improvements/fixes.
* Enhancement: Introduced options for Sticker position, including Top, Left, and Right.
* Enhancement: Added Height/Width customization for image type stickers.
* Enhancement: Introduced padding options for text type stickers, allowing for better alignment and spacing.
* Enhancement: Extended support for Grouped Products and External/Affiliate Product Types.
* Enhancement: Partially integrated sticker support for block editor themes.

= 1.1.7 =

Release Date: Dec 26, 2023

* Fix: Checked compatibility with WordPress version 6.4.2
* Fix: Minor style fixes

= 1.1.6 =

Release Date: Jan 31, 2023

* Fix: Minor style fixes
* Fix: Checked compatibility with WordPress version 6.1

= 1.1.5 =

Release Date: June 18, 2020

* Enhancement: Added new category sticker options.
* Enhancement: Added custom CSS option.

= 1.1.4 =

Release Date: June 03, 2020

* Enhancement: Added text type options on each stickers so now easily manage text on sticker.
* Enhancement: Added new custom stickers options.

= 1.1.3 =

Release Date: May 25, 2020

* Fix: get_woocommerce_term_meta is deprecated since version 3.6! Use get_term_meta instead

= 1.1.2 =

Release Date: January 02, 2020

* Enhancement: Added stickers options on Product level.
* Enhancement: Added stickers options on Category level.
* Fix: Checked compatibility with WooCommerce version 3.8.1 and WordPress version 5.3

= 1.1.1 =

Release Date: Dec 30, 2017

* Fix: Sold out sticker issue for variable product.
* Fix: Minor bug fixes.
* Fix: Checked compatibility with WooCommerce version 3.2.6 and WordPress version 4.9.1 

= 1.1.0 =

Release Date: May 06, 2017

* Fix: WooCommerce 3.x compatibility fixes.
* Fix: Resolved Sticker shows above title issue.
* Fix: Minor other bug fixes.

= 1.0.4 =

Release Date: March 05, 2015

* Fix: Soldout product sticker not display on category page or listing while product is on sale and out of stock. 

= 1.0.3 =

Release Date: March 04, 2015

* Enhancement: Setting link on Plugins page.
* Enhancement: Added field for Product Sticker Position and Custom sticker upload for new, sale and soldout product.
* Enhancement: Shorten tab names.
* Fix: Override the default behavior of woocommerce badge.
* Fix: Setting options updated.
* Fix: Uninstall hook option delete.
* Fix: Field description updated.
* Fix: New product default value. 

= 1.0.2 =

Release Date: November 29, 2014

* Enhancement: Added field to consider product as new.

= 1.0.1 =
Release Date: November 26, 2014

* Initial release
 