<?php
/**
 * Plugin Name: Shipping per Neighborhood for WooCommerce
 * Plugin URI: https://eduardovillao.me/wordpress-plugin/
 * Description: Add support to shipping method by neighborhood or custom zones. Easy and flexible.
 * Author: EduardoVillao.me
 * Author URI: https://eduardovillao.me/
 * Version: 1.2.5
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WSN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WSN_PLUGN_URL', plugin_dir_url( __FILE__ ) );
define( 'WSN_VERSION', '1.2.5' );

/**
 * Order on WhatsApp Class
 *
 * Class to initialize the plugin.
 *
 * @since 1.0
 */
final class WSN_Init {

	/**
	 * Minimum WooCommerce Version
	 *
	 * @since 1.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_WOO_VERSION = '4.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Minimum WP Version
	 *
	 * @since 1.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_WP_VERSION = '5.4';

	/**
	 * Instance
	 *
	 * @since 1.0
	 *
	 * @access private
	 * @static
	 *
	 * @var WSN_Init The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @static
	 *
	 * @return OWW_Init An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * Private method for prevent instance outsite the class.
	 * 
	 * @since 1.0
	 *
	 * @access private
	 */
	private function __construct() {

		// Check required configs (PHP and WP version)
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Check for required WP version
		if ( version_compare( $GLOBALS['wp_version'], self::MINIMUM_WP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_wp_version' ] );
			return;
        }

		// Init plugin
		add_action( 'plugins_loaded', [ $this, 'init' ] );
		
		// Check if woo is activated
		add_action( 'admin_notices', [ $this, 'check_woo_activated' ] );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin and all classes after WooCommerce and other plugins is loaded.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
	public function init() {

		// Include required class
		include_once WSN_PLUGIN_PATH .'/includes/class-shipping-per-neighborhood.php';
		include_once WSN_PLUGIN_PATH .'/includes/class-get-fields.php';
		include_once WSN_PLUGIN_PATH .'/includes/class-shipping-extras.php';

		// include if woocommerce-extra-checkout-fields-for-brazil its not installed
		if ( ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {

			include_once WSN_PLUGIN_PATH .'/includes/class-manage-essential-fields.php';
		}
        
        // Add shipping method
		add_filter( 'woocommerce_shipping_methods', [ $this, 'add_shipping_method' ] );
		
		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ] );

		// Register global option
		add_action( 'admin_init', [ $this, 'register_global_option' ] );

        // 1 Disable country
        //add_filter ('woocommerce_shipping_calculator_enable_country', '__return_false');
        // 2 Disable State
        //add_filter ('woocommerce_shipping_calculator_enable_state', '__return_false');
        // 3 Desativar cidade
        //add_filter ('woocommerce_shipping_calculator_enable_city', '__return_false');
        // 4 Desativar código postal
        //add_filter ('woocommerce_shipping_calculator_enable_postcode', '__return_false');
    }
    
    /**
	 * Add Shipping Method
	 *
	 * Add woo shippint per neiighborhood method.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
    public function add_shipping_method( $methods ) {
        
        $methods['woo_shipping_per_neighborhood'] = 'WSN_Shipping_Method';
        return $methods;
	}
	
	/**
	 * Admin Scritps
	 *
	 * Add admin scrtips to options.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
	public function add_admin_scripts() {

		wp_register_script( 'wsn-admin-options-js', WSN_PLUGN_URL .'assets/js/admin-options.js', array(), WSN_VERSION, true );
		wp_register_style( 'wsn-admin-options-css', WSN_PLUGN_URL .'assets/css/admin-options.css', array(), WSN_VERSION );

		wp_enqueue_script( 'wsn-admin-options-js' );
		wp_enqueue_style( 'wsn-admin-options-css' );
	}

	/**
     * Register option
     * 
     * Register global options for plugin.
     *
     * @since 1.0
     * 
     * @access public
     * @return void
     */
	public function register_global_option() {

		$args = array(
            'default' => array(),
        );
		register_setting( 'wsn_options', 'wsn_global_cities', $args );
		register_setting( 'wsn_options', 'wsn_global_neighborhoods', $args );
	}

	/**
	 * Admin notice - WooCommerce
	 *
	 * Warning when the site doesn't have WooCommerce activated.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
	public function check_woo_activated() {

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			$message = sprintf(
				esc_html__( '%1$s requires %2$s to be installed and activated.', 'shipping-per-neighborhood-for-woocommerce' ),
				'<strong>' . esc_html__( 'Shipping per Neighborhood for WooCommerce', 'shipping-per-neighborhood-for-woocommerce' ) . '</strong>',
				'<strong>WooCommerce</strong>'
			);

			printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
		}
	}

	/**
	 * Admin notice - PHP
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '%1$s requires %2$s version %3$s or greater.', 'shipping-per-neighborhood-for-woocommerce' ),
			'<strong>' . esc_html__( 'Shipping per Neighborhood for WooCommerce', 'shipping-per-neighborhood-for-woocommerce' ) . '</strong>',
			'<strong>PHP</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice - WP
	 *
	 * Warning when the site doesn't have a minimum required WP version.
	 *
	 * @since 1.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_wp_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		
		$message = sprintf(
			esc_html__( '%1$s requires %2$s version %3$s or greater.', 'shipping-per-neighborhood-for-woocommerce' ),
			'<strong>' . esc_html__( 'Shipping per Neighborhood for WooCommerce', 'shipping-per-neighborhood-for-woocommerce' ) . '</strong>',
			'<strong>WordPress</strong>',
			 self::MINIMUM_WP_VERSION
		);

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}
}

WSN_Init::instance();
