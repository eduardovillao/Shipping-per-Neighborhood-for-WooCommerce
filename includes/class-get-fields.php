<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WSN_Get_Fields {

    /**
     * Get global cities
     *
     * Get global sities for use in select.
     *
     * @since 1.0
     * @access public
	 * @static
     * 
     * @return array
     */
    public static function get_global_cities() {

        $cities = get_option( 'wsn_global_cities' );

        if( empty( $cities ) ) {
            return;
        }

        $cities = self::clean_shipping_options( $cities );
        $list_cities = [];
        foreach( $cities as $k => $v ) {

            if( is_array( $v ) ) {
                foreach( $v as $v2 ) {

                    $list_cities[$v2] = $v2;
                }
            }
        }

        return array_unique( $list_cities );
    }

    /**
     * Get neighborhood options
     *
     * Get seted neighborhood options for use in package and calculate shipping.
     *
     * @since 1.0
     * @access public
	 * @static
     * 
     * @return string
     */
    public static function get_neighborhood_option() {

        // base for options
        $logged_neighborhood = '';
        $session_neighborhood = '';
    
        // if is user is logged in
        if( is_user_logged_in() ) {
    
            // profile - user meta opitons
            $current_user = wp_get_current_user();
            $shipping_option = get_user_meta( $current_user->ID, 'billing_teste', true );
            $billing_option = get_user_meta( $current_user->ID, 'shiping_teste', true );
            $logged_neighborhood = empty( $shipping_option ) ? $billing_option : $shipping_option;
    
            // session options
            $session_shipping_option = WC()->session->get( 'shipping_teste' );
            $session_billing_option = WC()->session->get( 'billing_teste' );
            $session_neighborhood = empty( $session_shipping_option ) ? $session_billing_option : $session_shipping_option;
        }
        else {
    
            // sesstion options
            $session_shipping_option = WC()->session->get( 'shipping_teste' );
            $session_billing_option = WC()->session->get( 'billing_teste' );
            $session_neighborhood = empty( $session_shipping_option ) ? $session_billing_option : $session_shipping_option;
        }
    
        return empty( $session_neighborhood ) ? $logged_neighborhood : $session_neighborhood;
    }

    /**
     * Get global cities and neighborhoods
     *
     * Get global sities for use in select.
     *
     * @since 1.0
     * @access public
	 * @static
     * 
     * @return array
     */
    public static function get_global_cities_and_neighborhoods() {

        $cities = get_option( 'wsn_global_cities' );

        if( empty( $cities ) ) {
            return;
        }
        
        $cities = self::clean_shipping_options( $cities );
        
        $neighborhoods = get_option( 'wsn_global_neighborhoods' );
        $neighborhoods = self::clean_shipping_options( $neighborhoods );
        
        $options = [];
        foreach( $cities as $k => $v ) {
    
            if( is_array( $v ) ) {
                foreach( $v as $tk => $tv ) {
    
                    $options[$tv][] = $neighborhoods[$k][$tk];
                    asort( $options[$tv] );
                }
            }
        }

        return $options;
    }

    /**
     * Clean shipping options
     * 
     * Clean shipping options based on shipping method instances enabled.
     * 
     * @since 1.2
     *
     * @return void
     */
    public static function clean_shipping_options( $options ) {

        $zones = WC_Shipping_Zones::get_zones();
        $methods_by_zone = [];
        $current_instances = [];
        foreach( $zones as $id => $method ) {

            $methods_by_zone = array_column( $method['shipping_methods'], 'method_title', 'instance_id' );
            $plugin_method = __( 'Shipping per Neighborhood for WooCommerce', 'shipping-per-neighborhood-for-woocommerce' );

            if( in_array( $plugin_method, $methods_by_zone ) ) {

                $current_instances[] = array_search( $plugin_method, $methods_by_zone );
            }
        }

        $filtered_options = [];
        foreach( $current_instances as $k => $v ) {

            if( isset( $options[$v] ) ) {
                
                $filtered_options[$v] = $options[$v];
            }
        }

        return $filtered_options;
    }
}

/**
 * TODO: Make array with instance_id like index and all cities and neighborhoods inside to manage in all plugin.
 */