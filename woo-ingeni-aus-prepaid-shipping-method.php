<?php 
/*
Plugin Name: Woo Ingeni AusPost Prepaid Satchels Shipping Method
Plugin URI: http://ingeni.net
Description: Woo Ingeni AusPost Prepaid Satchels Shipping Method
Version: 2021.01
Author: Bruce McKinnon
Author URI: http://ingeni.net

v2021.01 - Updated satchel volumes to match new size AusPost 2021 satchels
*/


/**
 * Check if WooCommerce is active
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function aus_prepaid_satchels_shipping_method_init() {
		if ( ! class_exists( 'WC_Ingeni_Aus_Prepaid_Statchels_Shipping_Method' ) ) {
      require_once 'class-ingeni-aus-prepaid-shipping-method.php';
      $obj_ingeni_woo = new WC_Ingeni_Aus_Prepaid_Statchels_Shipping_Method();
    }
  }
  add_action( 'woocommerce_shipping_init', 'aus_prepaid_satchels_shipping_method_init' );

  function add_aus_prepaid_satchels_shipping_method( $methods ) {
    $methods['ingeni_aus_prepaid_satchels_shipping_method'] = 'WC_Ingeni_Aus_Prepaid_Statchels_Shipping_Method';
    return $methods;
  }
  add_filter( 'woocommerce_shipping_methods', 'add_aus_prepaid_satchels_shipping_method' );
}


function ingeni_update_aus_prepaid_shipping_method() {
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/ingeni-update-aus-prepaid-shipping-method',
		__FILE__,
		'ingeni-update-aus-prepaid-shipping-method'
	);
}
add_action( 'init', 'ingeni_update_aus_prepaid_shipping_method' );