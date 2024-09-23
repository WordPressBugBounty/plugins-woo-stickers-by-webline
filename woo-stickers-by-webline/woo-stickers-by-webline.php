<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.weblineindia.com
 * @since             1.0.0
 * @package           Woo_Stickers_By_Webline
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Stickers by Webline
 * Plugin URI:        http://www.weblineindia.com
 * Description:       Product sticker extension to improve customer experience while shopping by providing stickers for New products, On Sale products, Soldout Products which is easily configure from admin panel without any extra developer efforts.
 * Version:           1.2.3
 * Author:            Weblineindia
 * Author URI:        http://www.weblineindia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-stickers-by-webline
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check if WooCommerce is active
 */

if (in_array ( 'woocommerce/woocommerce.php', apply_filters ( 'active_plugins', get_option ( 'active_plugins' ) ) )) {

define ( 'WS_VERSION', '1.2.3' );
define ( 'WS_OPTION_NAME', 'WS_settings' );
define ( 'WS_PLUGIN_FILE', basename ( __FILE__ ) );
define('WOSBW_DIR', plugin_dir_path(__FILE__));
define('WOSBW_URL', plugin_dir_url(__FILE__));


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-stickers-by-webline-activator.php
 */
function activate_woo_stickers_by_webline() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-stickers-by-webline-activator.php';
	Woo_Stickers_By_Webline_Activator::activate();
	update_option('wosbw_activation_date', time());
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-stickers-by-webline-deactivator.php
 */
function deactivate_woo_stickers_by_webline() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-stickers-by-webline-deactivator.php';
    if ( ! wp_next_scheduled( 'cron_job_hook' ) ) {
        wp_schedule_event( time(), 'every_day', 'cron_job_hook' );
    }
	Woo_Stickers_By_Webline_Deactivator::deactivate();
}

/* Check update hook Start */
function update_woo_stickers_by_webline($transient)
{
    if (empty($transient->checked)) {
        return $transient;
    }
    $plugin_folder = plugin_basename(__FILE__);
    if (isset($transient->checked[$plugin_folder])) {
        update_option('wosbw_activation_date', time());
    }
    return $transient;
}   
/* Check update hook End */

register_activation_hook( __FILE__, 'activate_woo_stickers_by_webline' );
register_deactivation_hook( __FILE__, 'deactivate_woo_stickers_by_webline' );
add_filter('pre_set_site_transient_update_plugins', 'update_woo_stickers_by_webline');

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
function run_woo_stickers_by_webline() {

	$plugin = new Woo_Stickers_By_Webline();
	$plugin->run();

}
run_woo_stickers_by_webline();

add_action('wp_ajax_get_post_title_wosbw', 'get_post_title_wosbw_from_permalink');
add_action('wp_ajax_nopriv_get_post_title_wosbw', 'get_post_title_wosbw_from_permalink');
function get_post_title_wosbw_from_permalink() {
    $permalink = get_option('wosbw_selected_page');
    $post_id = url_to_postid($permalink);
    $post_title = get_the_title($post_id);
    echo esc_html($post_title);
    exit;
}

add_action('wp_ajax_search_posts_pages', 'wosbw_search_posts_pages_callback');
function wosbw_search_posts_pages_callback()
{
    $results = array();
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    
    if (!$nonce || !wp_verify_nonce($nonce, 'search_posts_nonce')) {
        $term = sanitize_text_field($_GET['term']);
        $posts = get_posts(
            array(
                'post_type' => 'any',
                'posts_per_page' => -1,
                's' => $term,
                'post_status' => 'publish',
                'fields' => 'ids',
            )
        );

        foreach ($posts as $post_id) {
            $post_title = get_the_title($post_id);
            $post_permalink = get_permalink($post_id);
            $results[] = array(
                'label' => $post_title,
                'permalink' => $post_permalink, 
            );
        }
    }
    wp_send_json($results);
}

function wosbw_validate_upgrade_option($parsedData) {
    $wosbw_upgrade_option = isset($parsedData['wosbw_upgrade_option']) ? $parsedData['wosbw_upgrade_option'] : '';
    $selected_pages = isset($parsedData['selected_post_permalink_wosbw']) ? $parsedData['selected_post_permalink_wosbw'] : '';
    $wosbw_saved_keyword = isset($parsedData['wosbw_saved_keyword']) ? $parsedData['wosbw_saved_keyword'] : '';

    if ($wosbw_upgrade_option === 'backlink' && $selected_pages) {		
    
        $parts = explode('|', $wosbw_saved_keyword);		
        $searchText = $parts[1];
        $searchHref = $parts[0];

        $pageContent = getPageContentWosbw($selected_pages);

        if (checkContentWosbw($pageContent, $searchText, $searchHref)) {
            return '1';
        } else {
            $wosbw_saved_keyword = isset($parsedData['wosbw_saved_keyword']) ? $parsedData['wosbw_saved_keyword'] : '';
            $parts = explode('|', $wosbw_saved_keyword);
            if(get_option( 'wosbw_premium_access_allowed' ) != 0){
				update_option('wosbw_premium_access_allowed', 0);
				wosbw_get_json_response('Revoked');
			}
            update_option('wosbw_upgrade_option', $parsedData['wosbw_upgrade_option']);
            update_option('wosbw_selected_page', $parsedData['selected_post_permalink_wosbw']);
            update_option('wosbw_selected_page_name', $parsedData['select_posts_input_wosbw']);
            update_option('wosbw_saved_keyword', '<a href="' . $parts[0] . '">' . $parts[1] . '</a>');
            return 'The specified text and href are NOT present in the page source.';
        }	
    }
    else{
        return '0';
    }
}

function getPageContentWosbw($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function checkContentWosbw($content, $text, $href) {
	$isCommented = function($content, $position) {
        $before = strrpos(substr($content, 0, $position), '<!--');
        if ($before === false) {
            return false; 
        }
        $after = strpos($content, '-->', $before);
        if ($after === false || $after < $position) {
            return false; 
        }
        return true;
    };

    $textPositionWosbw = strpos($content, '>'.$text.'<');
    $hrefPosition1Wosbw = strpos($content, 'href="' . $href . '"');
    $hrefPosition2Wosbw = strpos($content, "href='" . $href . "'");

    $textFoundWosbw = $textPositionWosbw !== false && !$isCommented($content, $textPositionWosbw);
    $hrefFoundWosbw = ($hrefPosition1Wosbw !== false && !$isCommented($content, $hrefPosition1Wosbw)) || ($hrefPosition2Wosbw !== false && !$isCommented($content, $hrefPosition2Wosbw));

    return $textFoundWosbw && $hrefFoundWosbw;
}

add_action('wp_ajax_wosbw_save_upgrade_option', 'wosbw_save_upgrade_option');
add_action('wp_ajax_no_priv_wosbw_save_upgrade_option', 'wosbw_save_upgrade_option');

function wosbw_save_upgrade_option() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wosbw_save_upgrade_option_nonce')) {
        $formData = isset($_POST['formData']) ? $_POST['formData'] : '';
        $formData = filter_var($formData, FILTER_SANITIZE_STRING);
        parse_str($formData, $parsedData);

        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';

        $post_types = get_post_types(['public' => true], 'names');
        $args = array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'title' => $title
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post = $query->post;
            }
            wp_reset_postdata();
        }

        if (!$post) {
            wp_send_json_error(array('valid' => false, 'message' => 'The selected post/page does not exist or is invalid.'));
            die();
        }

        $validation_result = wosbw_validate_upgrade_option($parsedData);
        if ($validation_result !== '1') {
            wp_send_json_error(array('valid' => true, 'message' => $validation_result));
            die();
        }

        $wosbw_saved_keyword = isset($parsedData['wosbw_saved_keyword']) ? $parsedData['wosbw_saved_keyword'] : '';
        $parts = explode('|', $wosbw_saved_keyword);

        update_option('wosbw_upgrade_option', $parsedData['wosbw_upgrade_option']);
        update_option('wosbw_selected_page', $parsedData['selected_post_permalink_wosbw']);
        update_option('wosbw_selected_page_name', $parsedData['select_posts_input_wosbw']);
        update_option('wosbw_saved_keyword', '<a href="' . $parts[0] . '">' . $parts[1] . '</a>');
        if(get_option( 'wosbw_premium_access_allowed' ) != 1){
            update_option('wosbw_premium_access_allowed', 1);
            wosbw_get_json_response('Granted');
        }

        $response_data = array(
            'success' => true,
            'valid' => true,
            'message' => 'Settings Updated',
        );
        wp_send_json_success($response_data);
    } else {
        wp_send_json_error(array('message' => 'Nonce verification failed.'));
    }
    die();
}

if ( is_admin() ) {
    if( ! function_exists('get_plugin_data') ){
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    $plugin_name = get_plugin_data( __FILE__ )['Name'];
    define('PLUGIN_NAME_WOSBW', $plugin_name);
}

// Define custom time intervals
function custom_cron_intervals_wosbw($schedules) {
    $schedules['every_day'] = array(
        'interval' => 86400,
        'display' => __('Every 1 Day')
    );
    return $schedules;
}
add_filter('cron_schedules', 'custom_cron_intervals_wosbw');

if(get_option( 'wosbw_premium_access_allowed' ) == 1){
    add_action( 'cron_job_hook', 'wosbw_cron_job_function' );
}

function wosbw_cron_job_function(){
    $upgrade_option =  get_option( 'wosbw_upgrade_option' );
    $selected_pages = get_option( 'wosbw_selected_page' );

    if ($upgrade_option === 'backlink' && !empty($selected_pages)) {
        
        $wosbw_saved_keyword = get_option( 'wosbw_saved_keyword' );
        preg_match('/"([^"]+)"/', $wosbw_saved_keyword, $match1);
        if (isset($match1[1])) {
            $searchHref = $match1[1];
        }
        preg_match('/>(.*?)</', $wosbw_saved_keyword, $match2);
        if (isset($match2[1])) {
            $searchText = $match2[1];
            
        }
        $pageContent = getPageContentWosbw($selected_pages);

        if (checkContentWosbw($pageContent, $searchText, $searchHref) == false) {
            update_option('wosbw_premium_access_allowed', 0);
            wosbw_get_json_response('Revoked');
        }
    }
}

function wosbw_get_json_response($premium_plan_status){

    $saved_keyword = get_option('wosbw_saved_keyword');	

    $keyword_url = '';
    $keyword_name = '';		

    preg_match('/<a\s+href="([^"]+)">([^<]+)<\/a>/', $saved_keyword, $matches);

    if (!empty($matches)) {
        $keyword_url = isset($matches[1]) ? $matches[1] : '';
        $keyword_name = isset($matches[2]) ? $matches[2] : '';
    }

    $data = array(
        'admin_email' => get_option('admin_email'),
        'plugin_name' => PLUGIN_NAME_WOSBW,
        'site_url' => home_url(),
        'page_name' => get_option('wosbw_selected_page_name'),
        'page_url' => get_option('wosbw_selected_page'),
        'keyword_url' => $keyword_url,
        'keyword_name' => $keyword_name,
        'premium_plan_status' => $premium_plan_status
    );

    // URL of the remote PHP script
    $url = 'https://cdkqydurfsivjzt35o6wfs54c40hxjip.lambda-url.ap-south-1.on.aws/';

    $json_data = json_encode($data);

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-WLIKey: cc196df4-1328-4fe0-be5d-527285c41c62',
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    // Execute cURL session and capture the response
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);
    
}
}