<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !class_exists( 'WC_Shipping_Method' ) ) {
    return;
}

class WSN_Shipping_Method extends WC_Shipping_Method {

    /**
     * SVG to table
     *
     * @since 1.0
     * 
     * @access protected
     * @return string
     */
    protected $svg = '<svg class="wsn-table__remove" height="16pt" viewBox="0 0 512 512" width="16pt" xmlns="http://www.w3.org/2000/svg"><path d="m256 0c-141.164062 0-256 114.835938-256 256s114.835938 256 256 256 256-114.835938 256-256-114.835938-256-256-256zm0 0" fill="#f44336"/><path d="m350.273438 320.105469c8.339843 8.34375 8.339843 21.824219 0 30.167969-4.160157 4.160156-9.621094 6.25-15.085938 6.25-5.460938 0-10.921875-2.089844-15.082031-6.25l-64.105469-64.109376-64.105469 64.109376c-4.160156 4.160156-9.621093 6.25-15.082031 6.25-5.464844 0-10.925781-2.089844-15.085938-6.25-8.339843-8.34375-8.339843-21.824219 0-30.167969l64.109376-64.105469-64.109376-64.105469c-8.339843-8.34375-8.339843-21.824219 0-30.167969 8.34375-8.339843 21.824219-8.339843 30.167969 0l64.105469 64.109376 64.105469-64.109376c8.34375-8.339843 21.824219-8.339843 30.167969 0 8.339843 8.34375 8.339843 21.824219 0 30.167969l-64.109376 64.105469zm0 0" fill="#fafafa"/></svg>';

    /**
     * Constructor for your shipping class
     *
     * @since 1.0
     * 
     * @access public
     * @return void
     */
    public function __construct( $instance_id = 0 ) {

        $this->id = 'woo_shipping_per_neighborhood';
        $this->instance_id = absint( $instance_id );
        $this->method_title = __('Shipping per Neighborhood for WooCommerce', 'shipping-per-neighborhood-for-woocommerce');
        $this->method_description = __('Create your price list by neighborhood or zone.', 'shipping-per-neighborhood-for-woocommerce');

        $this->supports = array(
            'shipping-zones',
            'instance-settings',
        );

        $this->instance_form_fields = array(
			'wsn-enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'shipping-per-neighborhood-for-woocommerce' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable this shipping method', 'shipping-per-neighborhood-for-woocommerce' ),
				'default' 		=> 'yes',
			),
			'wsn-title' => array(
				'title' 		=> __( 'Method Title', 'shipping-per-neighborhood-for-woocommerce' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This title user sees during checkout.', 'shipping-per-neighborhood-for-woocommerce' ),
				'default'		=> __( 'Price per Neighborhood', 'shipping-per-neighborhood-for-woocommerce' ),
				'desc_tip'		=> true
            ),
            'wsn_repeater_city' => array(
                'title' 		=> 'wsn_repeater_city',
                'type' 			=> 'text',
                'label' 		=> '',
                'default' 		=> '',
            ),
            'wsn_repeater_neighborhood' => array(
                'title' 		=> 'wsn_repeater_neighborhood',
                'type' 			=> 'text',
                'label' 		=> '',
				'default' 		=> '',
            ),
            'wsn_repeater_neighborhood_price' => array(
                'title' 		=> 'wsn_repeater_neighborhood_price',
                'type' 			=> 'text',
                'label' 		=> '',
				'default' 		=> '',
            ),
        );

        $this->enabled = $this->get_option( 'wsn-enabled' );
        $this->title = $this->get_option( 'wsn-title' );
        
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
	 * Correios options page.
	 */
	public function admin_options() {

        // get instanced fields
        $fields = $this->get_instance_form_fields();

        $html = '';
        $fields_repeater = [
            'wsn_repeater_city',
            'wsn_repeater_neighborhood',
            'wsn_repeater_neighborhood_price'
        ];

        // generate fields to output
        foreach ( $fields as $k => $v ) {
            $type = $this->get_field_type( $v );

            if ( in_array( $v['title'], $fields_repeater ) ) {
                break;
            }
            elseif ( method_exists( $this, 'generate_' . $type . '_html' ) ) {
                $html .= $this->{'generate_' . $type . '_html'}( $k, $v );
            }
            else {
                $html .= $this->generate_text_html( $k, $v );
            }
        }

        echo '<table class="form-table">';
        echo $html;
        echo '</table>';

        // include table (repeater)
        include WSN_PLUGIN_PATH . '/templates/admin-options.php';
    }

    /**
     * Validate City
     * 
     * Validate city after $_POST
     * 
     * @since 1.0
     *
     * @access public
     * @param mixed $key, $value
     * @return array
     */
    public function validate_wsn_repeater_city_field( $key, $value ) {

        $this->save_global_options( $value, 'city' );
        return is_array( $value ) ? array_map( 'wc_clean', array_map( 'stripslashes', $value ) ) : '';
    }

    /**
     * Validate Neighborhood
     * 
     * Validate neighborhood after $_POST
     *
     * @since 1.0
     * 
     * @access public
     * @param mixed $key, $value
     * @return array
     */
    public function validate_wsn_repeater_neighborhood_field( $key, $value ) {

        $this->save_global_options( $value, 'neighborhood' );
        return is_array( $value ) ? array_map( 'wc_clean', array_map( 'stripslashes', $value ) ) : '';
    }

    /**
     * Validate Price
     * 
     * Validate price after $_POST
     *
     * @since 1.0
     * 
     * @access public
     * @param mixed $key, $value
     * @return array
     */
    public function validate_wsn_repeater_neighborhood_price_field( $key, $value ) {

       return is_array( $value ) ? array_map( 'wc_format_decimal', array_map( 'wc_clean', $value ) ) : '';
    }

    /**
     * Save global options
     * 
     * Save global options for plugin.
     *
     * @since 1.0
     * 
     * @access public
     * @param mixed $field_value, type
     * @return void
     */
    public function save_global_options( $field_value, $type ) {

        if( $type == 'city' ) {

            $cities = get_option( 'wsn_global_cities' );
            $cities[$this->instance_id] = $field_value;

            update_option( 'wsn_global_cities', $cities );
        }
        elseif( $type == 'neighborhood' ) {

            $neighborhoods = get_option( 'wsn_global_neighborhoods' );
            $neighborhoods[$this->instance_id] = $field_value;

            update_option( 'wsn_global_neighborhoods', $neighborhoods );
        }
    }

    /**
     * Get neighborhoods
     * 
     * Get neighborhood options.
     *
     * @since 1.0
     * 
     * @access public
     * @return array
     */
    public function get_neighborhoods() {

        $repeater_city = $this->get_option( 'wsn_repeater_city' );
        $repeater_neighborhood = $this->get_option( 'wsn_repeater_neighborhood' );
        $repeater_price = $this->get_option( 'wsn_repeater_neighborhood_price' );

        $options = [];
        foreach( $repeater_city as $k => $v) {

            if( ! array_key_exists( $v, $options ) ) {

                $options[$v] = [];
            }

            $options[$v] = $options[$v] + [ $repeater_neighborhood[$k] => $repeater_price[$k] ];
        }
        return $options;
    }

    /**
     * Calculate Shipping
     * 
     * calculate_shipping function.
     *
     * @since 1.0
     * 
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping( $package = array() ) {
        
        // Check if have neighborhood on $package
        if( empty( $package['destination']['neighborhood'] ) ) {
            return;
        }

        $city = $package['destination']['city'];
        $neighborhood = $package['destination']['neighborhood'];

        // get values from admin options
        $shipping_zones = $this->get_neighborhoods();
        
        // define cost if neighborhood existe in admin options
        if( array_key_exists( $neighborhood, $shipping_zones[ $city ] ) ) {

            $this->add_rate( array(
                'id' => $this->id . $this->instance_id,
                'label' => $neighborhood,
                'cost' => $shipping_zones[$city][$neighborhood]
            ) );
        }
    }
}