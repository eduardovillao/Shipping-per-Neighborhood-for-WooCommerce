<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WSN_Shipping_Extras {

    /**
     * Constructor class
     *
     * @since 1.0
     * 
     * @access public
     * @return void
     */
    public function __construct() {

        // custom template
        add_filter( 'woocommerce_locate_template', [ $this, 'get_custom_template' ], 10, 3 );

        // filter default package
        add_filter( 'woocommerce_cart_shipping_packages', [ $this, 'custom_package' ], 10, 1 );

        // save option cart page
        add_action( 'woocommerce_calculated_shipping', [ $this, 'save_calculate_cart' ] );

        // save option checkout page
        add_action( 'woocommerce_checkout_update_order_review', [ $this, 'pass_data_shipping' ], 10, 1 );

        // new checkout fields
        add_filter( 'woocommerce_billing_fields', [ $this, 'checkout_billing_neighborhood_fields' ], 60 );
        add_filter( 'woocommerce_shipping_fields', [ $this, 'checkout_shipping_neighborhood_fields' ], 60 );

        // custom checkout field option group
        add_filter('woocommerce_form_field_select', [ $this, 'chekcout_select_option_group' ], 10, 4);
    }

    /**
     * Load custom template
     *
     * Filter for get custom template (shipping-calculator.php) from plugin.
     *
     * @since 1.0
     * @return string new template
     */
    public function get_custom_template( $template, $template_name, $template_path ) {

        if ( 'shipping-calculator.php' === basename( $template ) ) {

            $template = trailingslashit( WSN_PLUGIN_PATH ) . 'woocommerce/cart/shipping-calculator.php';
        }

        return $template;
    }

    /**
     * Create custom package
     *
     * Create new key/value ( destination => neighborhood ) to default package.
     *
     * @since 1.0
     * @return array new package
     */
    function custom_package( $shipping_packages ) {

        $neighborhood = WSN_Get_Fields::get_neighborhood_option();
        $shipping_packages[0]['destination']['neighborhood'] = $neighborhood;

        return $shipping_packages;
    }

    /**
     * Save neighborhood - cart page
     *
     * Save neighborhood in cart page after user $_POST. Update user meta if is logged in.
     *
     * @since 1.0
     * @return void
     */
    public function save_calculate_cart() {

        if( isset( $_POST['calc-shipping-neighborhood'] ) ) {

            $neighborhood = sanitize_text_field( $_POST['calc-shipping-neighborhood'] );
            
            WC()->session->set( 'shipping_teste', $neighborhood );
            WC()->session->set( 'billing_teste', $neighborhood );
        }
    }

    /**
     * Save neighborhood - chekout page
     *
     * Save neighborhood in checkout page after user $_POST. Update user meta if is logged in.
     *
     * @since 1.0
     * @return void
     */
    function pass_data_shipping( $posted_data ) {
        
        parse_str( $posted_data, $neighborhood );
        $billing_neighborhood = $neighborhood['billing_neighborhood'];
        $shipping_neighborhood = $neighborhood['shipping_neighborhood'];

        // update session meta
        WC()->session->set( 'billing_teste', $billing_neighborhood );
        WC()->session->set( 'shipping_teste', $shipping_neighborhood );
    }

    /**
	 * Checkout billing fields
     * 
     * New checkout billing fields/options.
	 *
	 * @param array $fields Default fields.
	 *
     * @since 1.0
	 * @return array
	 */
	public function checkout_billing_neighborhood_fields( $fields ) {

        if( !WSN_Get_Fields::get_global_cities() ) {
            return $fields;
        }

        if ( isset( $fields['billing_neighborhood'] ) ) {

            unset( $fields['billing_neighborhood'] );
        }

        $fields['billing_neighborhood'] = array(
            'label' => __( 'Neighborhood', 'shipping-per-neighborhood-for-woocommerce' ),
            'data-control' => 'optiongroup',
            'type' => 'select',
            'required' => true,
            'class' => array( 'form-row-wide', 'address-field' ),
            'clear' => true,
            'priority' => 65,
            'options' => array( 'optiongroup' ),
        );

        if ( isset( $fields['billing_city'] ) ) {
            
            unset( $fields['billing_city'] );
        }

        $fields['billing_city'] = array(
            'label' => __( 'City', 'shipping-per-neighborhood-for-woocommerce' ),
            'type' => 'select',
            'required' => true,
            'class' => array( 'form-row-wide', 'address-field' ),
            'label_class' => array(),
            'input_class' => array(),
            'default' => '',
            'return' => false,
            'clear' => true,
            'priority' => 70,
            'options' => [ '' => __( 'Select City', 'shipping-per-neighborhood-for-woocommerce' ) ] + WSN_Get_Fields::get_global_cities(),
        );

        return $fields;
    }

    /**
	 * New checkout shipping fields
     * 
     * New checkout billing fields/options.
	 *
	 * @param  array $fields Default fields.
	 *
     * @since 1.0
	 * @return array
	 */
	public function checkout_shipping_neighborhood_fields( $fields ) {

        if( !WSN_Get_Fields::get_global_cities() ) {
            return $fields;
        }

        if ( isset( $fields['shipping_neighborhood'] ) ) {
            
            unset( $fields['shipping_neighborhood'] );
        }

        $fields['shipping_neighborhood'] = array(
            'label' => __( 'Shippping Neighborhood', 'shipping-per-neighborhood-for-woocommerce' ),
            'data-control' => 'optiongroup',
            'type' => 'select',
            'required' => true,
            'class' => array( 'form-row-wide', 'address-field' ),
            'clear' => true,
            'priority' => 65,
            'options' => array( 'optiongroup' ),
        );

        if ( isset( $fields['shipping_city'] ) ) {
            
            unset( $fields['shipping_city'] );
        }
        
        $fields['shipping_city'] = array(
            'label' => __( 'Shipping City', 'shipping-per-neighborhood-for-woocommerce' ),
            'type' => 'select',
            'required' => true,
            'class' => array( 'form-row-wide', 'address-field' ),
            'label_class' => array(),
            'input_class' => array(),
            'default' => '',
            'return' => false,
            'clear' => true,
            'priority' => 70,
            'options' => [ '' => __( 'Select City', 'shipping-per-neighborhood-for-woocommerce' ) ] + WSN_Get_Fields::get_global_cities(),
        );

        return $fields;
    }

    /**
	 * Options group fields - neighborhood
     * 
     * Filter default form field to support option group.
	 *
     * @since 1.0
	 * @return array
	 */
    public function chekcout_select_option_group( $html, $unused, $args, $value ) {

        if ( isset( $args['data-control'] ) && $args['data-control'] == 'optiongroup' ) {
            
            $options = '';
            $options .= '<option value="">'.__( 'Select Neighborhood', 'shipping-per-neighborhood-for-woocommerce' ).'</option>';

            foreach( WSN_Get_Fields::get_global_cities_and_neighborhoods() as $group => $option ) {
                $options .= '<optgroup label="'.esc_attr( $group ).'">';
                if( is_array( $option ) ) {
                    foreach( $option as $tb ) {
                        $options .= '<option value="'.esc_attr( $tb ).'" '.selected( WSN_Get_Fields::get_neighborhood_option(), $tb, false ).'>'.esc_html( $tb ).'</option>';
                    }
                }
                $options .= '</optgroup>';
            }        

            return preg_replace('/(?:<select[^>]+>)\\K(.*)(?:<\\/option>)/s', $options, $html);
        }

        return $html;
    }
}

new WSN_Shipping_Extras;