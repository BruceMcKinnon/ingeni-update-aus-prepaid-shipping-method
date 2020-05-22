<?php

// 
// https://gist.github.com/mikejolley/6713608
// https://code.tutsplus.com/tutorials/create-a-custom-shipping-method-for-woocommerce--cms-26098
//
class WC_Ingeni_Aus_Prepaid_Statchels_Shipping_Method extends WC_Shipping_Method {

  public function __construct() {
		//$this->instance_id = absint( $instance_id );
		$this->id = 'ingeni_aus_prepaid_satchels_shipping_method';
	  $this->method_title = __( 'Aus Prepaid Satchels', 'woocommerce' );
		$this->method_description = __( 'Shipping Method for AusPost prepaid satchels' ); // Description shown in admin

		// Define user set variables
		if ( !isset( $this->settings['enabled'] ) ) {
			$this->settings['enabled'] = 'no';
		}
		$this->enabled = $this->settings['enabled'];

    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Aus Prepaid Satchels', 'woocommerce' );
/*
		$this->supports  = array(
 			'shipping-zones',
  		'instance-settings',
  		'instance-settings-modal',
 		);
		*/
		$this->init();

	}

	function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		
		// Save settings in admin if you have any defined
		//add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_options'));
	}


  	public function init_form_fields(){

			// Get the shipping classes
			$shipping_classes = get_terms( array('taxonomy' => 'product_shipping_class', 'hide_empty' => false ) );
			$ship_class_values = array();
			foreach($shipping_classes as $ship) {
				$ship_class_values[ $ship->term_taxonomy_id ] =  $ship->description;
			}

//fb_log('shipping classs: '.print_r($shipping_classes,true));

  		$this->form_fields = array(
		    'enabled' => array(
		      'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
		      'type' 			=> 'checkbox',
		      'label' 		=> __( 'Enable Aus Prepaid Satchels Shipping', 'woocommerce' ),
		      'default' 		=> 'yes'
		    ),
		    'title' => array(
		      'title' 		=> __( 'Method Title', 'woocommerce' ),
		      'type' 			=> 'text',
		      'description' 	=> __( 'Ship using pre-paid Australia Post satchels.', 'woocommerce' ),
		      'default'		=> __( 'Aus Prepaid Satchels Shipping', 'woocommerce' ),
				),
		    'avail_classes' => array(
		      'title' 		=> __( 'Allow for these Shipping Classes', 'woocommerce' ),
		      'type' 			=> 'multiselect',
		      'description' 	=> __( 'Limit to selected Shipping Classes.', 'woocommerce' ),
		      'options' => $ship_class_values
				),
				'cost_small' => array(
					'title' => __( 'Small', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Small satchel charge', 'woocommerce' ),
					'default' => '9.2'
				),
				'cost_medium' => array(
					'title' => __( 'Medium', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Medium satchel charge', 'woocommerce' ),
					'default' => '12.45'
				),
				'cost_large' => array(
					'title' => __( 'Large', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Large satchel charge', 'woocommerce' ),
					'default' => '15.7'
				),
				'cost_xlarge' => array(
					'title' => __( 'Extra-Large', 'woocommerce' ),
					'type' => 'text',
					'description' => __( 'Extra Large satchel charge', 'woocommerce' ),
					'default' => '18.95'
				),
			);
  	}





  	public function is_available( $package ){
			if ( $this->settings['enabled'] != 'yes' ) {
				return false;
			}
  		foreach ( $package['contents'] as $item_id => $values ) {
	      $_product = $values['data'];
	      $weight =	$_product->get_weight();
	      if( $weight > 5 ){
//fb_log('too heavy!!! = '.$weight);
	      	return false;
	      }
	  	}

	  	return true;
  	}


  	public function calculate_shipping( $package = array() ){
			try {

				if ( $this->settings['enabled'] == 'no' ) {
					return;
				}

				// AusPost pre-paid satchels
				$satchel_small_cost = $this->settings['cost_small'];
				$satchel_medium_cost = $this->settings['cost_medium'];
				$satchel_large_cost = $this->settings['cost_large'];
				$satchel_xlarge_cost = $this->settings['cost_xlarge'];

				// Volumn in cubic cm
				$satchel_small_volume = 750;
				$satchel_medium_volume = 940; 
				$satchel_large_volume = 1110;
				$satchel_xlarge_volume = 2010;

				// Size max weights
				$satchel_max_weight = 5;

				// Allowed shipping classes
				$permitted_shipping_classes = $this->settings['avail_classes'];
//fb_log('permitted: '.print_r($permitted_shipping_classes,true));

//fb_log(print_r($package,true));		

				//get the total weight and dimensions
				$weight = 0;
				$dimensions = 0;
				$shipping_class_ok = true;
				foreach ( $package['contents'] as $item_id => $values ) {
					$_product  = $values['data'];
//fb_log(print_r($_product,true));

					$shipping_class_id = $_product->shipping_class_id; // Shipping class ID
//fb_log('shipping class: ['.$shipping_class_id.'] ');

					// Check that this product is in the listy of permitted shipping classes
					if ( is_array($permitted_shipping_classes) ) {
						if ( !in_array( $shipping_class_id, $permitted_shipping_classes) ) {
							$shipping_class_ok = false;
//fb_log('shipping class OK');
						}
					}

//fb_log($_product->get_title().'  weight:'.$weight.' prod weight:'.$_product->get_weight().' qty:'.$values['quantity']);
//fb_log($_product->get_title().'  prod length:'.$_product->length.' width:'.$_product->width );
					$weight =	$weight + ($_product->get_weight() * $values['quantity']);
					$dimensions = $dimensions + (($_product->length * $values['quantity']) * $_product->width * $_product->height);
				}

//fb_log('weight:'.$weight.' volume:'.$dimensions);
				
				//calculate the cost according to the volume
				$cost = 0;
				$satchel_name = "";
				if ( ( $weight < $satchel_max_weight ) && ( $shipping_class_ok ) ) {
					if ($dimensions < $satchel_small_volume) {
						$cost = $satchel_small_cost;
						$satchel_name = "Small";
					} elseif ($dimensions < $satchel_medium_volume) {
						$cost = $satchel_medium_cost;
						$satchel_name = "Medium";
					} elseif ($dimensions < $satchel_large_volume) {
						$cost = $satchel_large_cost;
						$satchel_name = "Large";
					} elseif ($dimensions < $satchel_xlarge_volume) {
						$cost = $satchel_xlarge_cost;
						$satchel_name = "Extra large";
					}
				}
//fb_log('satchel_name'.	$satchel_name);
				if ( $satchel_name != "" ) {
					// send the final rate to the user. 
					$this->add_rate( array(
						'id' 	=> $this->id,
						'label' => $this->title . ' '.$satchel_name,
						'cost' 	=> $cost
					));
				}
			} catch (Exception $ex) {
				fb_log( 'calculate_shipping() satchel_name' .	$satchel_name . ' : ' . $ex->getMessage() );
			}
  	}



  function fb_log($msg) {
    $upload_dir = wp_upload_dir();
    $logFile = $upload_dir['basedir'] . '/' . 'fb_log.txt';
    date_default_timezone_set('Australia/Sydney');

    // Now write out to the file
    $log_handle = fopen($logFile, "a");
    if ($log_handle !== false) {
      fwrite($log_handle, date("H:i:s").": ".$msg."\r\n");
      fclose($log_handle);
    }
  }



}
