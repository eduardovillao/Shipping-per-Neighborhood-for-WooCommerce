<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WSN_Manage_Essential_Fields {

    /**
     * Constructor for your shipping class
     *
     * @since 1.0
     * 
     * @access public
     * @return void
     */
    public function __construct() {

        // formated order address
        add_filter( 'woocommerce_formatted_address_replacements', [ $this, 'replace_formatted_address' ], 1, 2 );

        // order page fields
        add_filter( 'woocommerce_admin_billing_fields', [ $this, 'shop_order_billing_fields' ] );
        add_filter( 'woocommerce_admin_shipping_fields', [ $this, 'shop_order_shipping_fields' ] );
        add_filter( 'woocommerce_order_formatted_billing_address', [ $this, 'order_formatted_billing_address' ], 1, 2 );
        add_filter( 'woocommerce_order_formatted_shipping_address', [ $this, 'order_formatted_shipping_address' ], 1, 2 );

        // formatted address localisation
        add_filter( 'woocommerce_localisation_address_formats', [ $this, 'localisation_address_formats' ] );

        // user meta fileds
        add_filter( 'woocommerce_customer_meta_fields', [ $this, 'customer_meta_fields' ], 10, 1 );
        add_filter( 'woocommerce_user_column_billing_address', [ $this, 'user_column_billing_address' ], 1, 2 );
        add_filter( 'woocommerce_user_column_shipping_address', [ $this, 'user_column_shipping_address' ], 1, 2 );
    }

    /**
     * Custom address format
     * 
     * Custom address format with neighborhood in order page.
     *
     * @since 1.0
     * 
     * @param  array $replacements Default replacements.
	 * @param  array $args Arguments to replace.
     * 
     * @access public
     * @return array
     */
	public function replace_formatted_address( $replacements, $args ) {
		$args = wp_parse_args( $args, array(
			'neighborhood' => '',
		) );

		$replacements['{neighborhood}'] = $args['neighborhood'];

		return $replacements;
    }

    /**
     * Custom order billing fields
     * 
     * Add custom billing fields to order/order page.
     *
     * @since 1.0
     * 
     * @param array $data Default order billing fields.
     * 
     * @access public
     * @return array
     */
	public function shop_order_billing_fields( $data ) {

        if ( ! isset( $data['neighborhood'] ) ) {
            
            $data['neighborhood'] = array(
                'label' => __( 'Neighborhood', WSN_TEXT_DOMAIN ),
                'show'  => false,
            );
        }

		return $data;
	}

    /**
     * Custom order shipping fields
     * 
     * Add custom shipping fields to order/order page.
     *
     * @since 1.0
     * 
     * @param array $data Default order shipping fields.
     * 
     * @access public
     * @return array
     */
	public function shop_order_shipping_fields( $data ) {

        if ( ! isset( $data['neighborhood'] ) ) {
            
            $data['neighborhood'] = array(
                'label' => __( 'Neighborhood', WSN_TEXT_DOMAIN ),
                'show'  => false,
            );
        }

		return $data;
	} 

    /**
     * Formatted billing address
     * 
     * Add formatted billing address in order/order page.
     *
     * @since 1.0
     * 
     * @param  array  $address Default address.
	 * @param  object $order Order data.
     * 
     * @access public
     * @return array
     */
    public function order_formatted_billing_address( $address, $order ) {

        if ( ! is_array( $address ) ) {
			return $address;
        }
        
        $address['neighborhood'] = $order->get_meta( '_billing_neighborhood' );

		return $address;
	}

    /**
     * Formatted billing address
     * 
     * Add formatted billing address in order/order page.
     *
     * @since 1.0
     * 
     * @param  array  $address Default address.
	 * @param  object $order Order data.
     * 
     * @access public
     * @return array
     */
	public function order_formatted_shipping_address( $address, $order ) {

		if ( ! is_array( $address ) ) {
			return $address;
        }
        
        $address['neighborhood'] = $order->get_meta( '_shipping_neighborhood' );

		return $address;
    }

    /**
     * Formatted localisation address
     * 
     * Add formatted localisations address with neihborhood for cart/others.
     *
     * @since 1.0
     * 
     * @param array $formats Defaul formats.
     * 
     * @access public
     * @return array
     */
    public function localisation_address_formats( $formats ) {
        
		$formats['BR'] = "{name}\n{address_1}, {address_2}\n{neighborhood}\n{city}\n{state}\n{postcode}\n{country}";

		return $formats;
    }

    /**
     * Add customer meta fields
     * 
     * Add customer meta fields in profile page.
     *
     * @since 1.0
     * 
     * @param array $fields Defaul fields.
     * 
     * @access public
     * @return array
     */
    public function customer_meta_fields( $fields ) {

        if( ! array_key_exists( 'billing_neighborhood', $fields['billing']['fields'] ) ) {
    
            $fields['billing']['fields']['billing_neighborhood'] = array(
                'label'       => __( 'Neighborhood', WSN_TEXT_DOMAIN ),
                'description' => '',
            );
        }
    
        if( ! array_key_exists( 'shipping_neighborhood', $fields['shipping']['fields'] ) ) {
    
            $fields['shipping']['fields']['shipping_neighborhood'] = array(
                'label'       => __( 'Neighborhood', WSN_TEXT_DOMAIN ),
                'description' => '',
            );
        }
        
        return $fields;
    }

    /**
     * Customer billing column
     * 
     * Add value tu custom field in billing address column.
     *
     * @since 1.0
     * 
     * @param array $address Defaul address.
     * @param int $user_id current user id.
     * 
     * @access public
     * @return array
     */
    public function user_column_billing_address( $address, $user_id ) {

		$address['neighborhood'] = get_user_meta( $user_id, 'billing_neighborhood', true );

		return $address;
    }

    /**
     * Customer shipping column
     * 
     * Add value tu custom field in shipping address column.
     *
     * @since 1.0
     * 
     * @param array $address Defaul address.
     * @param int $user_id current user id.
     * 
     * @access public
     * @return array
     */
    public function user_column_shipping_address( $address, $user_id ) {

		$address['neighborhood'] = get_user_meta( $user_id, 'shipping_neighborhood', true );

		return $address;
    }
}

new WSN_Manage_Essential_Fields;