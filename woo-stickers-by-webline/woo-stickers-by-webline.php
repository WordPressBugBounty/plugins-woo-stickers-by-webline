<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.weblineindia.com
 * @since             1.0.0
 * @package           Woo_Stickers_By_Webline
 *
 * @wordpress-plugin
 * Plugin Name:       Stickers for WooCommerce
 * Plugin URI:        https://www.weblineindia.com
 * Description:       Product sticker extension to improve customer experience while shopping by providing stickers for New products, On Sale products, Soldout Products which is easily configure from admin panel without any extra developer efforts.
 * Version:           1.2.9
 * Author:            Weblineindia
 * Author URI:        https://www.weblineindia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-stickers-by-webline
 * Domain Path:       /languages
 * Requires at least: 5.9
 * Requires Plugins: woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check if WooCommerce is active.
 *
 * @since 1.2.9
 * @return bool
 */
function woo_stickers_by_webline_is_woocommerce_active() {
	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
	}

	return in_array( 'woocommerce/woocommerce.php', $active_plugins, true );
}

if ( woo_stickers_by_webline_is_woocommerce_active() ) {

    define ( 'WS_VERSION', '1.2.9' );
    define ( 'WS_OPTION_NAME', 'WS_settings' );
    define ( 'WS_PLUGIN_FILE', basename ( __FILE__ ) );
    define('WOSBW_DIR', plugin_dir_path(__FILE__));
    define('WOSBW_URL', plugin_dir_url(__FILE__));

    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-woo-stickers-by-webline-activator.php
     */
    function woo_stickers_by_webline_activate() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-stickers-by-webline-activator.php';
        Woo_Stickers_By_Webline_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-woo-stickers-by-webline-deactivator.php
     */
    function woo_stickers_by_webline_deactivate() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-stickers-by-webline-deactivator.php';
        Woo_Stickers_By_Webline_Deactivator::deactivate();
    }

    register_activation_hook( __FILE__, 'woo_stickers_by_webline_activate' );
    register_deactivation_hook( __FILE__, 'woo_stickers_by_webline_deactivate' );

    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-woo-stickers-by-webline.php';

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function woo_stickers_by_webline_run() {

        $plugin = new Woo_Stickers_By_Webline();
        $plugin->run();

    }
    woo_stickers_by_webline_run();
}