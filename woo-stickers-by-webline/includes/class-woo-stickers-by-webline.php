<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.weblineindia.com
 * @since      1.0.0
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/includes
 * @author     Weblineindia <info@weblineindia.com>
 */
class Woo_Stickers_By_Webline {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_Stickers_By_Webline_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'woo-stickers-by-webline';
		$this->version = WS_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Future use for Upgrade of plugin        
        if(version_compare(get_option(WS_OPTION_NAME), '1.0.2') == '-1') {
            $this->upgradeTo102();
        }
        if(version_compare(get_option(WS_OPTION_NAME), '1.0.3') == '-1') {
        	$this->upgradeTo103();
        }
        if(version_compare(get_option(WS_OPTION_NAME), '1.0.4') == '-1') {
        	$this->upgradeTo104();
        }
        if(version_compare(get_option(WS_OPTION_NAME), '1.1.0') == '-1') {
        	$this->upgradeTo110();
        }
        if(version_compare(get_option(WS_OPTION_NAME), '1.1.1') == '-1') {
        	$this->upgradeTo111();
        }
        
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Stickers_By_Webline_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Stickers_By_Webline_i18n. Defines internationalization functionality.
	 * - Woo_Stickers_By_Webline_Admin. Defines all hooks for the admin area.
	 * - Woo_Stickers_By_Webline_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-stickers-by-webline-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-stickers-by-webline-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-stickers-by-webline-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-stickers-by-webline-public.php';

		$this->loader = new Woo_Stickers_By_Webline_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Stickers_By_Webline_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woo_Stickers_By_Webline_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woo_Stickers_By_Webline_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menus' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_general_settings');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_new_product_settings');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_sale_product_settings');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_sold_product_settings');
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_cust_product_settings');
		
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'add_settings_link', 10, 2);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woo_Stickers_By_Webline_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		//action to show sold out product badge on product listing page
		$this->loader->add_action( 'woocommerce_before_shop_loop_item', $plugin_public, 'show_product_soldout_badge', 1 );

		//action to show sold out product badge on product detail page
		$this->loader->add_action('woocommerce_before_single_product_summary', $plugin_public, 'show_product_soldout_badge', 1 );

		//action to show new product badge on product listing page
		$this->loader->add_action( 'woocommerce_before_shop_loop_item', $plugin_public, 'show_product_new_badge', 1 );

		//action to show new product badge on product detail page
		$this->loader->add_action('woocommerce_before_single_product_summary', $plugin_public, 'show_product_new_badge', 1 );
		
		//action to show all product custom badge on product listing page
		$this->loader->add_action( 'woocommerce_before_shop_loop_item', $plugin_public, 'show_product_cust_badge', 2 );

		//action to show all product custom badge on product detail page
		$this->loader->add_action('woocommerce_before_single_product_summary', $plugin_public, 'show_product_cust_badge', 2 );

		if(wp_is_block_theme() == false){
			//Filter to show sales badge
			$this->loader->add_filter('woocommerce_sale_flash', $plugin_public, 'get_show_product_sale_badge', 21, 3 );
		}else{
			//action to show all product sale badge on product listing page
			$this->loader->add_action('woocommerce_before_shop_loop_item', $plugin_public, 'custom_woocommerce_sale_flash', 2 );

			//action to show all product sale badge on product detail page
			$this->loader->add_action('woocommerce_before_single_product_summary', $plugin_public, 'custom_woocommerce_sale_flash', 2 );
		}

		//Filter to show category badge
		$this->loader->add_filter('woocommerce_before_subcategory', $plugin_public, 'show_category_badge', 10, 1 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Stickers_By_Webline_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Store plugin version in options.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function update_version($version = '') {
    	
    	if($version == '') {
    		$version = WS_VERSION;
    	}

    	update_option(WS_OPTION_NAME, $version);
    }


    /**
	 * Upgrade plugin to 1.0.2.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    public function upgradeTo102() {
    	
    	$new_product_settings = ( array ) get_option ( 'new_product_settings' );
    	
    	$new_product_settings['new_product_sticker_days'] = '10';
    	
    	update_option('new_product_settings', $new_product_settings);
    	
    	$this->update_version('1.0.2');
    	
    }
    
    /**
	 * Upgrade plugin to 1.0.3.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    public function upgradeTo103() {
    	 
    	$new_product_settings = ( array ) get_option ( 'new_product_settings' );    	     	
    	$new_product_settings['new_product_position'] = 'left';
    	$new_product_settings['new_product_custom_sticker'] = '';    	 
    	update_option('new_product_settings', $new_product_settings);
    	 
    	$sale_product_settings = ( array ) get_option ( 'sale_product_settings' );
    	$sale_product_settings['sale_product_position'] = 'right';
    	$sale_product_settings['sale_product_custom_sticker'] = '';
    	update_option('sale_product_settings', $sale_product_settings);
    	
    	$sold_product_settings = ( array ) get_option ( 'sold_product_settings' );
    	$sold_product_settings['sold_product_position'] = 'left';
    	$sold_product_settings['sold_product_custom_sticker'] = '';
    	update_option('sold_product_settings', $sold_product_settings);
    	
    	$this->update_version('1.0.3');
    	 
    }
    
    /**
	 * Upgrade plugin to 1.0.4.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    public function upgradeTo104() {

    	$new_product_settings = ( array ) get_option ( 'new_product_settings' );    	     	
    	$new_product_settings['new_product_position'] = 'left';
    	$new_product_settings['new_product_option'] = '';
    	$new_product_settings['new_product_custom_text'] = '';
    	$new_product_settings['enable_new_product_style'] = '';
    	$new_product_settings['new_product_custom_text_fontcolor'] = '';
    	$new_product_settings['new_product_custom_text_backcolor'] = '';
    	$new_product_settings['new_product_custom_sticker'] = '';    	 
    	update_option('new_product_settings', $new_product_settings);
    	 
    	$sale_product_settings = ( array ) get_option ( 'sale_product_settings' );
    	$sale_product_settings['sale_product_position'] = 'right';
    	$sale_product_settings['sale_product_option'] = '';
    	$sale_product_settings['sale_product_custom_text'] = '';
    	$sale_product_settings['enable_sale_product_style'] = '';
    	$sale_product_settings['sale_product_custom_text_fontcolor'] = '';
    	$sale_product_settings['sale_product_custom_text_backcolor'] = '';
    	$sale_product_settings['sale_product_custom_sticker'] = '';
    	update_option('sale_product_settings', $sale_product_settings);
    	
    	$sold_product_settings = ( array ) get_option ( 'sold_product_settings' );
    	$sold_product_settings['sold_product_position'] = 'left';
    	$sold_product_settings['sold_product_option'] = '';
    	$sold_product_settings['sold_product_custom_text'] = '';
    	$sold_product_settings['enable_sold_product_style'] = '';
    	$sold_product_settings['sold_product_custom_text_fontcolor'] = '';
    	$sold_product_settings['sold_product_custom_text_backcolor'] = '';
    	$sold_product_settings['sold_product_custom_sticker'] = '';
    	update_option('sold_product_settings', $sold_product_settings);

    	$cust_product_settings = ( array ) get_option ( 'cust_product_settings' );
    	$cust_product_settings['cust_product_position'] = 'left';
    	$cust_product_settings['cust_product_option'] = '';
    	$cust_product_settings['cust_product_custom_text'] = '';
    	$cust_product_settings['enable_cust_product_style'] = '';
    	$cust_product_settings['cust_product_custom_text_fontcolor'] = '';
    	$cust_product_settings['cust_product_custom_text_backcolor'] = '';
    	$cust_product_settings['cust_product_custom_sticker'] = '';
    	update_option('cust_product_settings', $cust_product_settings);

    	$this->update_version('1.0.4');
    	 
    }

    /**
	 * Upgrade plugin to 1.1.0.
	 *
	 * @since    1.1.0
	 * @access   private
	 */
    public function upgradeTo110() {	
    	$this->update_version('1.1.0');
    	 
    }

    /**
	 * Upgrade plugin to 1.1.1.
	 *
	 * @since    1.1.1
	 * @access   private
	 */
    public function upgradeTo111() {	
    	$this->update_version('1.1.1');
    	 
    }
}
