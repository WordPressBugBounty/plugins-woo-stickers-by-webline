<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.weblineindia.com
 * @since      1.0.0
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/includes
 * @author     Weblineindia <info@weblineindia.com>
 */
class Woo_Stickers_By_Webline_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( 'general_settings' );
    	delete_option( 'new_product_settings' );
    	delete_option( 'sale_product_settings' );
    	delete_option( 'sold_product_settings' );
    	delete_option( 'cust_product_settings' );
	}

}
