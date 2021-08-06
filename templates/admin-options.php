<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$repeater_city = $this->get_option( 'wsn_repeater_city' );
$repeater_neighborhood = $this->get_option( 'wsn_repeater_neighborhood' );
$repeater_price = $this->get_option( 'wsn_repeater_neighborhood_price' );

if( !empty( $repeater_city ) && !empty( $repeater_neighborhood ) && !empty( $repeater_price ) ) {

    ?>
    <h2><?php esc_html_e('Pricing table', WSN_TEXT_DOMAIN); ?></h2>
    <table class="wp-list-table widefat fixed striped wsn-table">
        <thead>
            <tr>
                <th><?php esc_html_e('City', WSN_TEXT_DOMAIN); ?></th>
                <th><?php esc_html_e('Neigborhood', WSN_TEXT_DOMAIN); ?></th>
                <th><?php esc_html_e('Price', WSN_TEXT_DOMAIN); ?></th>
                <th class="wsn-table__header-action"><?php esc_html_e('Action', WSN_TEXT_DOMAIN); ?></th>
            </tr>
        </thead>
        <tbody class="wsn-table__body">
            <?php foreach( $repeater_city as $k => $v ) :
            ?>
                <tr class="wsn-table__row" data-index="<?php echo esc_html( $k ); ?>">
                    <td> <input type="text" 
                            name="woocommerce_woo_shipping_per_neighborhood_<?php echo esc_html( $fields['wsn_repeater_city']['title'].'['.$k.'].' ); ?>" 
                            class="regular-text"
                            data-index="<?php echo esc_html( $k ); ?>" 
                            value="<?php echo esc_html( $repeater_city[$k] ); ?>"> 
                    </td>
                    <td> <input type="text" 
                            name="woocommerce_woo_shipping_per_neighborhood_<?php echo esc_html( $fields['wsn_repeater_neighborhood']['title'].'['.$k.'].' ); ?>" 
                            class="regular-text"
                            data-index="<?php echo esc_html( $k ); ?>"  
                            value="<?php echo esc_html( $repeater_neighborhood[$k] ); ?>">
                    </td>
                    <td> <input type="number" 
                            name="woocommerce_woo_shipping_per_neighborhood_<?php echo esc_html( $fields['wsn_repeater_neighborhood_price']['title'].'['.$k.'].' ); ?>" 
                            class="regular-text"
                            data-index="<?php echo esc_html( $k ); ?>" 
                            value="<?php echo esc_html( $repeater_price[$k] ); ?>"> 
                    </td>
                    <td class="wsn-table__action"><?php echo $this->svg; ?></td>
                </tr>
            <?php
            endforeach; ?>
        </tbody>
    </table>

    <a class="button button-secondary wsn-table__add"> <?php esc_html_e('Add option', WSN_TEXT_DOMAIN); ?> </a>
    <?php
} else {
    ?>
    <h2><?php esc_html_e('Pricing table', WSN_TEXT_DOMAIN); ?></h2>
    <table class="wp-list-table widefat fixed striped wsn-table">
        <thead>
            <tr>
                <th><?php esc_html_e('City', WSN_TEXT_DOMAIN); ?></th>
                <th><?php esc_html_e('Neigborhood', WSN_TEXT_DOMAIN); ?></th>
                <th><?php esc_html_e('Price', WSN_TEXT_DOMAIN); ?></th>
                <th class="wsn-table__header-action"><?php esc_html_e('Action', WSN_TEXT_DOMAIN); ?></th>
            </tr>
        </thead>
        <tbody class="wsn-table__body">
            <tr class="wsn-table__row" data-index="0">
                <td> <input type="text" 
                        name="woocommerce_woo_shipping_per_neighborhood_<?php echo esc_html( $fields['wsn_repeater_city']['title'] ); ?>[0]" 
                        class="regular-text"
                        data-index="0" 
                        value=""> 
                </td>
                <td> <input type="text" 
                        name="woocommerce_woo_shipping_per_neighborhood_<?php echo esc_html( $fields['wsn_repeater_neighborhood']['title'] ); ?>[0]" 
                        class="regular-text"
                        data-index="0"  
                        value="">
                </td>
                <td> <input type="number" 
                        name="woocommerce_woo_shipping_per_neighborhood_<?php echo esc_html( $fields['wsn_repeater_neighborhood_price']['title'] ); ?>[0]" 
                        class="regular-text"
                        data-index="0" 
                        value=""> 
                </td>
                <td class="wsn-table__action"><?php echo $this->svg; ?></td>
            </tr>
        </tbody>
    </table>

    <a class="button button-secondary wsn-table__add"> <?php esc_html_e('Add option', WSN_TEXT_DOMAIN); ?> </a>
    <?php
}