=== Ingeni Australia Post PrePaid Satchel Shipping Method for Woo ===

Contributors: Bruce McKinnon
Tags: woocommerce, australi post, auspost
Requires at least: 4.8
Tested up to: 5.1.1
Stable tag: 2021.02

A custom shipping method for WooCommerce that allows calculating of shipping using domestic prepaid satchels from Australia Post.



== Description ==

* - Support domestic prepaid satchels from Australia Post.




== Installation ==

1. Upload the 'woo-ingeni-aus-prepaid-shipping-method' folder to the '/wp-content/plugins/' directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. Enable the shipping method via WooCommerce



== Frequently Asked Questions ==

Q - Can I set my own prices?

A - Yes - Go to WooCommerce > Settings > Shipping > Au Prepaid Satchels and set the prices for Small, Medium, Large and Extra Large satchels.


Q - Can I limit which products can be shipped using this method?

A - Yes. Via the settings page, select the Shipping Classes that are allowed for use with this plugin.





== Changelog ==

v2020.01 - Initial version.

v2021.01 - Updated satchel volumes to match new AusPost 2021 pre-paid parcel post satchel sizes.

v2022.01 - Updated add_aus_prepaid_satchels_shipping_method() to match class ID value.
         - Updated default satchel prices.
         - Fixed various errors in the calculate_shipping() function.
