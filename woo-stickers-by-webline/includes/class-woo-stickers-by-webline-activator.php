<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.weblineindia.com
 * @since      1.0.0
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/includes
 * @author     Weblineindia <info@weblineindia.com>
 */
class Woo_Stickers_By_Webline_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		update_option(WS_OPTION_NAME, WS_VERSION);
	}

}
