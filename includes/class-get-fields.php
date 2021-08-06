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
        $neighborhoods = get_option( 'wsn_global_neighborhoods' );
        $options = [];
        foreach( $cities as $k => $v ) {
    
            if( is_array( $v ) ) {
    
                foreach( $v as $tk => $tv ) {
    
                    $options[$tv][] = $neighborhoods[$k][$tk];
                }
            }
        }
        
        return $options;
    }
}