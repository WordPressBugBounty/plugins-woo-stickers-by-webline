<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.weblineindia.com
 * @since      1.0.0
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/public
 * @author     Weblineindia <info@weblineindia.com>
 */

class Woo_Stickers_By_Webline_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $general_settings_key = 'general_settings';
	private $new_product_settings_key = 'new_product_settings';
	private $sale_product_settings_key = 'sale_product_settings';
	private $sold_product_settings_key = 'sold_product_settings';
	private $cust_product_settings_key = 'cust_product_settings';
	private $general_settings = array();
	private $new_product_settings = array();
	private $sale_product_settings = array();
	private $sold_product_settings = array();
	private $cust_product_settings = array();

	/**
	 * The Sold Out flag Identify product as sold.
	 *
	 * @since    1.1.2
	 * @access   private
	 * @var      string    $sold_out    The Sold Out flag Identify product as sold.
	 */
	private $sold_out = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->general_settings = ( array ) get_option ( $this->general_settings_key );
		$this->new_product_settings = ( array ) get_option ( $this->new_product_settings_key );
		$this->sale_product_settings = ( array ) get_option ( $this->sale_product_settings_key );
		$this->sold_product_settings = ( array ) get_option ( $this->sold_product_settings_key );
		$this->cust_product_settings = ( array ) get_option ( $this->cust_product_settings_key );

		// Merge with defaults
		$this->general_settings = array_merge ( array (
				'enable_sticker' => 'no',
				'enable_sticker_list' => 'no',
				'enable_sticker_detail' => 'no',
				'custom_css' => ''
		), $this->general_settings );
		
		$this->new_product_settings = array_merge ( array (
				'enable_new_product_sticker' => 'no',
				'new_product_sticker_days' => '10',
				'new_product_position' => 'left',
				'new_product_option' => '',
				'new_product_custom_text' => '',
				'enable_new_product_style' => 'ribbon',
				'new_product_custom_text_fontcolor' => '#ffffff',
				'new_product_custom_text_backcolor' => '#000000',
				'new_product_custom_sticker' => '',
				'enable_new_schedule_product_style' => '',
		), $this->new_product_settings );
		
		$this->sale_product_settings = array_merge ( array (
				'enable_sale_product_sticker' => 'no',
				'sale_product_position' => 'right',
				'sale_product_option' => '',
				'sale_product_custom_text' => '',
				'enable_sale_product_style' => 'ribbon',
				'sale_product_custom_text_fontcolor' => '#ffffff',
				'sale_product_custom_text_backcolor' => '#000000',
				'sale_product_custom_sticker' => '' ,
				'enable_sale_schedule_product_style' => '' ,
		), $this->sale_product_settings );
		
		$this->sold_product_settings = array_merge ( array (
				'enable_sold_product_sticker' => 'no',
				'sold_product_position' => 'left',
				'sold_product_option' => '',
				'sold_product_custom_text' => '',
				'enable_sold_product_style' => 'ribbon',
				'sold_product_custom_text_fontcolor' => '#ffffff',
				'sold_product_custom_text_backcolor' => '#000000',
				'sold_product_custom_sticker' => '',
				'enable_sold_schedule_product_style' => '' ,

		), $this->sold_product_settings );

		$this->cust_product_settings = array_merge ( array (
				'enable_cust_product_sticker' => 'no',
				'cust_product_position' => 'left',
				'cust_product_option' => '',
				'cust_product_custom_text' => '',
				'enable_cust_product_style' => 'ribbon',
				'enable_cust_schedule_product_style' => 'ribbon',
				'cust_product_custom_text_fontcolor' => '#ffffff',
				'cust_product_custom_text_backcolor' => '#000000',
				'cust_product_custom_sticker' => '',
				'enable_cust_schedule_product_style' => '' ,
		), $this->cust_product_settings );

		//Check if custom css exists & action to load custom css on frontend head
		if( !empty( $this->general_settings['custom_css'] ) ) {
			add_action( 'wp_head', array( $this, 'load_custom_css' ), 99 );			
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Stickers_By_Webline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Stickers_By_Webline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-stickers-by-webline-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Stickers_By_Webline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Stickers_By_Webline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-stickers-by-webline-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Override New product stickers options level
	 */
	public function override_np_sticker_level_settings( $settings ) {

		global $post;

		$id = !empty( $post->ID ) ? $post->ID : '';

		//If empty then return AS received
		if( empty( $id ) ) return $settings;

		$enable_np_sticker 	= get_post_meta( $id, '_enable_np_sticker', true );
		if( $enable_np_sticker == 'yes' ) {
			$settings['enable_new_product_sticker'] = 'yes';
			$settings['new_product_sticker_days'] = !empty( $np_no_of_days ) ? $np_no_of_days : "10";

			$np_sticker_pos = get_post_meta( $id, '_np_sticker_pos', true );
			$settings['new_product_position'] = !empty( $np_sticker_pos ) ? $np_sticker_pos : "";

			$new_product_sticker_left_right = get_post_meta( $id, 'np_sticker_left_right', true );
			$settings['new_product_sticker_left_right'] = !empty( $new_product_sticker_left_right ) ? $new_product_sticker_left_right : "";

			$new_product_top = get_post_meta( $id, 'np_sticker_top', true );
			$settings['new_product_sticker_top'] = !empty( $new_product_top ) ? $new_product_top : "";
			
			$np_sticker_rotate = get_post_meta( $id, 'np_sticker_rotate', true );
			$settings['new_product_sticker_rotate'] = !empty( $np_sticker_rotate ) ? $np_sticker_rotate : "";

			$np_sticker_animation_type = get_post_meta( $id, 'np_sticker_animation_type', true );
			$settings['new_product_sticker_animation_type'] = !empty( $np_sticker_animation_type ) ? $np_sticker_animation_type : "";

			$np_sticker_animation_direction = get_post_meta( $id, 'np_sticker_animation_direction', true );
			$settings['new_product_sticker_animation_direction'] = !empty( $np_sticker_animation_direction ) ? $np_sticker_animation_direction : "";

			$np_sticker_animation_scale = get_post_meta( $id, 'np_sticker_animation_scale', true );
			$settings['new_product_sticker_animation_scale'] = !empty( $np_sticker_animation_scale ) ? $np_sticker_animation_scale : "";

			$np_sticker_animation_iteration_count = get_post_meta( $id, 'np_sticker_animation_iteration_count', true );
			$settings['new_product_sticker_animation_iteration_count'] = !empty( $np_sticker_animation_iteration_count ) ? $np_sticker_animation_iteration_count : "";
			
			$np_sticker_animation_delay = get_post_meta( $id, 'np_sticker_animation_delay', true );
			$settings['new_product_sticker_animation_delay'] = !empty( $np_sticker_animation_delay ) ? $np_sticker_animation_delay : "";
			
			$np_product_option = get_post_meta( $id, '_np_product_option', true );
			if( !empty( $np_product_option ) ) $settings['new_product_option'] = $np_product_option;
			
			if($np_product_option == 'text') {

				$np_product_custom_text = get_post_meta( $id, '_np_product_custom_text', true );
				$settings['new_product_custom_text'] = !empty( $np_product_custom_text ) ? $np_product_custom_text : "";

				$np_sticker_type = get_post_meta( $id, '_np_sticker_type', true );
				$settings['enable_new_product_style'] = !empty( $np_sticker_type ) ? $np_sticker_type : "";

				$np_product_custom_text_fontcolor = get_post_meta( $id, '_np_product_custom_text_fontcolor', true );
				$settings['new_product_custom_text_fontcolor'] = !empty( $np_product_custom_text_fontcolor ) ? $np_product_custom_text_fontcolor : "";

				$np_product_custom_text_backcolor = get_post_meta( $id, '_np_product_custom_text_backcolor', true );
				$settings['new_product_custom_text_backcolor'] = !empty( $np_product_custom_text_backcolor ) ? $np_product_custom_text_backcolor : "";

				$np_product_custom_text_padding_top = get_post_meta( $id, '_np_product_custom_text_padding_top', true );
				$settings['new_product_text_padding_top'] = !empty( $np_product_custom_text_padding_top ) ? $np_product_custom_text_padding_top : "";

				$np_product_custom_text_padding_right = get_post_meta( $id, '_np_product_custom_text_padding_right', true );
				$settings['new_product_text_padding_right'] = !empty( $np_product_custom_text_padding_right ) ? $np_product_custom_text_padding_right : "";
				
				$np_product_custom_text_padding_bottom = get_post_meta( $id, '_np_product_custom_text_padding_bottom', true );
				$settings['new_product_text_padding_bottom'] = !empty( $np_product_custom_text_padding_bottom ) ? $np_product_custom_text_padding_bottom : "";

				$np_product_custom_text_padding_left = get_post_meta( $id, '_np_product_custom_text_padding_left', true );
				$settings['new_product_text_padding_left'] = !empty( $np_product_custom_text_padding_left ) ? $np_product_custom_text_padding_left : "";
				
			} else if($np_product_option == 'image') {

				$new_product_sticker_image_width = get_post_meta( $id, 'np_sticker_image_width', true );
				$settings['new_product_sticker_image_width'] = !empty( $new_product_sticker_image_width ) ? $new_product_sticker_image_width : "";

				$new_product_sticker_image_height = get_post_meta( $id, 'np_sticker_image_height', true );
				$settings['new_product_sticker_image_height'] = !empty( $new_product_sticker_image_height ) ? $new_product_sticker_image_height : "";

				$np_sticker_custom_id = get_post_meta( $id, '_np_sticker_custom_id', true );
				$settings['new_product_custom_sticker'] = !empty( $np_sticker_custom_id ) ? wp_get_attachment_thumb_url($np_sticker_custom_id) : "";

			}

			$enable_new_product_schedule_sticker = get_post_meta( $id, '_enable_np_product_schedule_sticker', true );
			$settings['enable_new_product_schedule_sticker'] = !empty( $enable_new_product_schedule_sticker ) ? $enable_new_product_schedule_sticker : "";

			$new_product_schedule_start_sticker_date_time = get_post_meta( $id, '_np_product_schedule_start_sticker_date_time', true );
			$settings['new_product_schedule_start_sticker_date_time'] = !empty( $new_product_schedule_start_sticker_date_time ) ? $new_product_schedule_start_sticker_date_time : "";

			$new_product_schedule_end_sticker_date_time = get_post_meta( $id, '_np_product_schedule_end_sticker_date_time', true );
			$settings['new_product_schedule_end_sticker_date_time'] = !empty( $new_product_schedule_end_sticker_date_time ) ? $new_product_schedule_end_sticker_date_time : "";

			$new_product_schedule_sticker_option = get_post_meta( $id, '_np_product_schedule_option', true );
			$settings['new_product_schedule_sticker_option'] = $new_product_schedule_sticker_option;

			if($enable_new_product_schedule_sticker == "yes"){
				if($new_product_schedule_sticker_option == 'text_schedule') {

					$new_product_schedule_custom_text = get_post_meta( $id, '_np_schedule_product_custom_text', true );
					$settings['new_product_schedule_custom_text'] = !empty( $new_product_schedule_custom_text ) ? $new_product_schedule_custom_text : "";
					
					$enable_new_schedule_product_style = get_post_meta( $id, '_np_schedule_sticker_type', true );
					if( !empty( $enable_new_schedule_product_style ) ) $settings['enable_new_schedule_product_style'] = $enable_new_schedule_product_style;
					$settings['enable_new_schedule_product_style'] = !empty( $enable_new_schedule_product_style ) ? $enable_new_schedule_product_style : "";
					
					$new_product_schedule_custom_text_fontcolor = get_post_meta( $id, '_np_schedule_product_custom_text_fontcolor', true );
					$settings['new_product_schedule_custom_text_fontcolor'] = !empty( $new_product_schedule_custom_text_fontcolor ) ? $new_product_schedule_custom_text_fontcolor : "";
					
					$new_product_schedule_custom_text_backcolor = get_post_meta( $id, '_np_schedule_product_custom_text_backcolor', true );
					$settings['new_product_schedule_custom_text_backcolor'] = !empty( $new_product_schedule_custom_text_backcolor ) ? $new_product_schedule_custom_text_backcolor : "";

					$new_product_schedule_text_padding_top = get_post_meta( $id, '_np_schedule_product_custom_text_padding_top', true );
					$settings['new_product_schedule_text_padding_top'] = !empty( $new_product_schedule_text_padding_top ) ? $new_product_schedule_text_padding_top : "";

					$new_product_schedule_text_padding_right = get_post_meta( $id, '_np_product_schedule_custom_text_padding_right', true );
					$settings['new_product_schedule_text_padding_right'] = !empty( $new_product_schedule_text_padding_right ) ? $new_product_schedule_text_padding_right : "";

					$new_product_schedule_text_padding_bottom = get_post_meta( $id, '_np_product_schedule_custom_text_padding_bottom', true );
					$settings['new_product_schedule_text_padding_bottom'] = !empty( $new_product_schedule_text_padding_bottom ) ? $new_product_schedule_text_padding_bottom : "";

					$new_product_schedule_text_padding_left = get_post_meta( $id, '_np_product_schedule_custom_text_padding_left', true );
					$settings['new_product_schedule_text_padding_left'] = !empty( $new_product_schedule_text_padding_left ) ? $new_product_schedule_text_padding_left : "";

				}elseif($new_product_schedule_sticker_option == 'image_schedule'){

					$new_product_schedule_sticker_image_width = get_post_meta( $id, 'np_schedule_sticker_image_width', true );
					$settings['new_product_schedule_sticker_image_width'] = $new_product_schedule_sticker_image_width;
					$settings['new_product_schedule_sticker_image_width'] = !empty( $new_product_schedule_sticker_image_width ) ? $new_product_schedule_sticker_image_width : "";

					$new_product_schedule_sticker_image_height = get_post_meta( $id, 'np_schedule_sticker_image_height', true );
					$settings['new_product_schedule_sticker_image_height'] = $new_product_schedule_sticker_image_height;
					$settings['new_product_schedule_sticker_image_height'] = !empty( $new_product_schedule_sticker_image_height ) ? $new_product_schedule_sticker_image_height : "";

					$new_product_schedule_custom_sticker = get_post_meta( $id, '_np_schedule_sticker_custom_id', true );
					$settings['new_product_schedule_custom_sticker'] = !empty( $new_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url($new_product_schedule_custom_sticker) : "";

				}
			}

			return $settings;
		} elseif ( $enable_np_sticker == 'no' ) {
			$settings['enable_new_product_sticker'] = 'no';
			return $settings;
		}

		// Get categories
		$terms = get_the_terms( $id, 'product_cat' );
		if( !empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$enable_np_sticker = get_term_meta( $term->term_id, 'enable_np_sticker', true );
				if( !empty( $enable_np_sticker ) ) {
					if( $enable_np_sticker == 'yes' ) {
						$settings['enable_new_product_sticker'] = 'yes';
						$np_no_of_days 	= get_term_meta( $term->term_id, 'np_no_of_days', true );
						$settings['new_product_sticker_days'] = !empty( $np_no_of_days ) ? $np_no_of_days : "10";
						$np_sticker_pos = get_term_meta( $term->term_id, 'np_sticker_pos', true );
						$settings['new_product_position'] = !empty( $np_sticker_pos ) ? $np_sticker_pos : "";

						$new_product_sticker_left_right = get_term_meta( $term->term_id, 'np_sticker_left_right', true );
						$settings['new_product_sticker_left_right'] = !empty( $new_product_sticker_left_right ) ? $new_product_sticker_left_right : "";

						$new_product_top = get_term_meta( $term->term_id, 'np_sticker_top', true );
						$settings['new_product_sticker_top'] = !empty( $new_product_top ) ? $new_product_top : "";

						$np_sticker_rotate = get_term_meta( $term->term_id, 'np_sticker_rotate', true );
						$settings['new_product_sticker_rotate'] = !empty( $np_sticker_rotate ) ? $np_sticker_rotate : "";

						$np_sticker_animation_type = get_term_meta( $term->term_id, 'np_sticker_category_animation_type', true );
						$settings['new_product_sticker_animation_type'] = !empty( $np_sticker_animation_type ) ? $np_sticker_animation_type : "";

						$np_sticker_animation_direction = get_term_meta( $term->term_id, 'np_sticker_category_animation_direction', true );
						$settings['new_product_sticker_animation_direction'] = !empty( $np_sticker_animation_direction ) ? $np_sticker_animation_direction : "";

						$np_sticker_animation_scale = get_term_meta( $term->term_id, 'np_sticker_category_animation_scale', true );
						$settings['new_product_sticker_animation_scale'] = !empty( $np_sticker_animation_scale ) ? $np_sticker_animation_scale : "";

						$np_sticker_animation_iteration_count = get_term_meta( $term->term_id, 'np_sticker_category_animation_iteration_count', true );
						$settings['new_product_sticker_animation_iteration_count'] = !empty( $np_sticker_animation_iteration_count ) ? $np_sticker_animation_iteration_count : "";

						$np_sticker_animation_delay = get_term_meta( $term->term_id, 'np_sticker_category_animation_type_delay', true );
						$settings['new_product_sticker_animation_delay'] = !empty( $np_sticker_animation_delay ) ? $np_sticker_animation_delay : "";

						$np_product_option = get_term_meta( $term->term_id, 'np_product_option', true );
						if( !empty( $np_product_option ) ) $settings['new_product_option'] = $np_product_option;
						if($np_product_option == 'text') {

							$np_product_custom_text = get_term_meta( $term->term_id, 'np_product_custom_text', true );
							$settings['new_product_custom_text'] = !empty( $np_product_custom_text ) ? $np_product_custom_text : "";
							$np_sticker_type = get_term_meta( $term->term_id, 'np_sticker_type', true );
							$settings['enable_new_product_style'] = !empty( $np_sticker_type ) ? $np_sticker_type : "";
							$np_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'np_product_custom_text_fontcolor', true );
							$settings['new_product_custom_text_fontcolor'] = !empty( $np_product_custom_text_fontcolor ) ? $np_product_custom_text_fontcolor : "";
							$np_product_custom_text_backcolor = get_term_meta( $term->term_id, 'np_product_custom_text_backcolor', true );
							$settings['new_product_custom_text_backcolor'] = !empty( $np_product_custom_text_backcolor ) ? $np_product_custom_text_backcolor : "";

							$np_product_custom_text_padding_top = get_term_meta( $term->term_id, 'np_product_custom_text_padding_top', true );
							$settings['new_product_text_padding_top'] = !empty( $np_product_custom_text_padding_top ) ? $np_product_custom_text_padding_top : "";

							$np_product_custom_text_padding_right = get_term_meta( $term->term_id, 'np_product_custom_text_padding_right', true );
							$settings['new_product_text_padding_right'] = !empty( $np_product_custom_text_padding_right ) ? $np_product_custom_text_padding_right : "";

							$np_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'np_product_custom_text_padding_bottom', true );
							$settings['new_product_text_padding_bottom'] = !empty( $np_product_custom_text_padding_bottom ) ? $np_product_custom_text_padding_bottom : "";

							$np_product_custom_text_padding_left = get_term_meta( $term->term_id, 'np_product_custom_text_padding_left', true );
							$settings['new_product_text_padding_left'] = !empty( $np_product_custom_text_padding_left ) ? $np_product_custom_text_padding_left : "";
							
						} else if($np_product_option == 'image') {

							$new_product_sticker_image_width = get_term_meta( $term->term_id, 'np_sticker_image_width', true );
							$settings['new_product_sticker_image_width'] = !empty( $new_product_sticker_image_width ) ? $new_product_sticker_image_width : "";

							$new_product_sticker_image_height = get_term_meta( $term->term_id, 'np_sticker_image_height', true );
							$settings['new_product_sticker_image_height'] = !empty( $new_product_sticker_image_height ) ? $new_product_sticker_image_height : "";

							$np_sticker_custom_id = get_term_meta( $term->term_id, 'np_sticker_custom_id', true );
							$settings['new_product_custom_sticker'] = !empty( $np_sticker_custom_id ) ? wp_get_attachment_thumb_url($np_sticker_custom_id) : "";
						}

						$enable_new_product_schedule_sticker = get_term_meta( $term->term_id, 'enable_np_product_schedule_sticker_category', true );
						$settings['enable_new_product_schedule_sticker'] = !empty( $enable_new_product_schedule_sticker ) ? $enable_new_product_schedule_sticker : "";

						$new_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'np_product_schedule_start_sticker_date_time', true );
						$settings['new_product_schedule_start_sticker_date_time'] = !empty( $new_product_schedule_start_sticker_date_time ) ? $new_product_schedule_start_sticker_date_time : "";

						$new_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'np_product_schedule_end_sticker_date_time', true );
						$settings['new_product_schedule_end_sticker_date_time'] = !empty( $new_product_schedule_end_sticker_date_time ) ? $new_product_schedule_end_sticker_date_time : "";

						$new_product_schedule_sticker_option = get_term_meta( $term->term_id, 'np_product_schedule_option', true );
						$settings['new_product_schedule_sticker_option'] = $new_product_schedule_sticker_option;

						if($new_product_schedule_sticker_option == 'text_schedule') {

							$new_product_schedule_custom_text = get_term_meta( $term->term_id, 'np_product_schedule_custom_text', true );
							$settings['new_product_schedule_custom_text'] = !empty( $new_product_schedule_custom_text ) ? $new_product_schedule_custom_text : "";
							$np_sticker_type = get_term_meta( $term->term_id, 'np_schedule_sticker_type', true );
							$settings['enable_new_schedule_product_style'] = !empty( $enable_new_schedule_product_style ) ? $enable_new_schedule_product_style : "";
							$new_product_schedule_custom_text_fontcolor = get_term_meta( $term->term_id, 'np_schedule_product_custom_text_fontcolor', true );
							$settings['new_product_schedule_custom_text_fontcolor'] = !empty( $new_product_schedule_custom_text_fontcolor ) ? $new_product_schedule_custom_text_fontcolor : "";
							$new_product_schedule_custom_text_backcolor = get_term_meta( $term->term_id, 'np_schedule_product_custom_text_backcolor', true );
							$settings['new_product_schedule_custom_text_backcolor'] = !empty( $new_product_schedule_custom_text_backcolor ) ? $new_product_schedule_custom_text_backcolor : "";

							$new_product_schedule_text_padding_top = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_top', true );
							$settings['new_product_schedule_text_padding_top'] = !empty( $new_product_schedule_text_padding_top ) ? $new_product_schedule_text_padding_top : "";

							$new_product_schedule_text_padding_right = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_right', true );
							$settings['new_product_schedule_text_padding_right'] = !empty( $new_product_schedule_text_padding_right ) ? $new_product_schedule_text_padding_right : "";

							$new_product_schedule_text_padding_bottom = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_bottom', true );
							$settings['new_product_schedule_text_padding_bottom'] = $new_product_schedule_text_padding_bottom;

							$new_product_schedule_text_padding_left = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_left', true );
							$settings['new_product_schedule_text_padding_left'] = $new_product_schedule_text_padding_left;

						} else if($new_product_schedule_sticker_option == 'image_schedule') {

							$new_product_schedule_sticker_image_width = get_term_meta( $term->term_id, 'np_schedule_sticker_image_width', true );
							$settings['new_product_schedule_sticker_image_width'] = $new_product_schedule_sticker_image_width;

							$new_product_schedule_sticker_image_height = get_term_meta( $term->term_id, 'np_schedule_sticker_image_height', true );
							$settings['new_product_schedule_sticker_image_height'] = $new_product_schedule_sticker_image_height;

							$new_product_schedule_custom_sticker = get_term_meta( $term->term_id, 'np_schedule_sticker_custom_id', true );
							$settings['new_product_schedule_custom_sticker'] = !empty( $new_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url($new_product_schedule_custom_sticker) : "";
						}
						
					} elseif ( $enable_np_sticker == 'no' ) {
						$settings['enable_new_product_sticker'] = 'no';
					}
					break;
				}
			}
		}

		return $settings;
	}

	/**
	 * Override sale product stickers options level
	 */
	public function override_pos_sticker_level_settings( $settings ) {

		global $post;

		$id = !empty( $post->ID ) ? $post->ID : '';

		//If empty then return AS received
		if( empty( $id ) ) return $settings;

		$enable_pos_sticker = get_post_meta( $id, '_enable_pos_sticker', true );
		if( $enable_pos_sticker == 'yes' ) {
			$settings['enable_sale_product_sticker'] = 'yes';

			$pos_sticker_pos = get_post_meta( $id, '_pos_sticker_pos', true );
			$settings['sale_product_position'] = !empty( $pos_sticker_pos ) ? $pos_sticker_pos : "";

			$pos_product_top = get_post_meta( $id, 'pos_sticker_top', true );
			$settings['sale_product_sticker_top'] = !empty( $pos_product_top ) ? $pos_product_top : "";

			$pos_product_sticker_left_right = get_post_meta( $id, 'pos_sticker_left_right', true );
			$settings['sale_product_sticker_left_right'] = !empty( $pos_product_sticker_left_right ) ? $pos_product_sticker_left_right : "";

			$pos_sticker_rotate = get_post_meta( $id, 'pos_sticker_rotate', true );
			$settings['sale_product_sticker_rotate'] = !empty( $pos_sticker_rotate ) ? $pos_sticker_rotate : "";

			$pos_sticker_animation_type = get_post_meta( $id, 'pos_sticker_animation_type', true );
			$settings['sale_product_sticker_animation_type'] = !empty( $pos_sticker_animation_type ) ? $pos_sticker_animation_type : "";

			$pos_sticker_animation_direction = get_post_meta( $id, 'pos_sticker_animation_direction', true );
			$settings['sale_product_sticker_animation_direction'] = !empty( $pos_sticker_animation_direction ) ? $pos_sticker_animation_direction : "";

			$pos_sticker_animation_scale = get_post_meta( $id, 'pos_sticker_animation_scale', true );
			$settings['sale_product_sticker_animation_scale'] = !empty( $pos_sticker_animation_scale ) ? $pos_sticker_animation_scale : "";

			$pos_sticker_animation_iteration_count = get_post_meta( $id, 'pos_sticker_animation_iteration_count', true );
			$settings['sale_product_sticker_animation_iteration_count'] = !empty( $pos_sticker_animation_iteration_count ) ? $pos_sticker_animation_iteration_count : "";

			$pos_sticker_animation_delay = get_post_meta( $id, 'pos_sticker_animation_delay', true );
			$settings['sale_product_sticker_animation_delay'] = !empty( $pos_sticker_animation_delay ) ? $pos_sticker_animation_delay : "";
			
			$pos_product_option = get_post_meta( $id, '_pos_product_option', true );
			if( !empty( $pos_product_option ) ) $settings['sale_product_option'] = $pos_product_option;
			if($pos_product_option == 'text') {
				
				$pos_product_custom_text = get_post_meta( $id, '_pos_product_custom_text', true );
				$settings['pos_product_custom_text'] = !empty( $pos_product_custom_text ) ? $pos_product_custom_text : "";

				$pos_sticker_type = get_post_meta( $id, '_pos_sticker_type', true );
				$settings['enable_sale_product_style'] = !empty( $pos_sticker_type ) ? $pos_sticker_type : "";

				$pos_product_custom_text_fontcolor = get_post_meta( $id, '_pos_product_custom_text_fontcolor', true );
				$settings['sale_product_custom_text_fontcolor'] = !empty( $pos_product_custom_text_fontcolor ) ? $pos_product_custom_text_fontcolor : "";

				$pos_product_custom_text_backcolor = get_post_meta( $id, '_pos_product_custom_text_backcolor', true );
				$settings['sale_product_custom_text_backcolor'] = !empty( $pos_product_custom_text_backcolor ) ? $pos_product_custom_text_backcolor : "";

				$pos_product_custom_text_padding_top = get_post_meta( $id, '_pos_product_custom_text_padding_top', true );
				$settings['sale_product_text_padding_top'] = !empty( $pos_product_custom_text_padding_top ) ? $pos_product_custom_text_padding_top : "";

				$pos_product_custom_text_padding_right = get_post_meta( $id, '_pos_product_custom_text_padding_right', true );
				$settings['sale_product_text_padding_right'] = !empty( $pos_product_custom_text_padding_right ) ? $pos_product_custom_text_padding_right : "";

				$pos_product_custom_text_padding_bottom = get_post_meta( $id, '_pos_product_custom_text_padding_bottom', true );
				$settings['sale_product_text_padding_bottom'] = !empty( $pos_product_custom_text_padding_bottom ) ? $pos_product_custom_text_padding_bottom : "";

				$pos_product_custom_text_padding_left = get_post_meta( $id, '_pos_product_custom_text_padding_left', true );
				$settings['sale_product_text_padding_left'] = !empty( $pos_product_custom_text_padding_left ) ? $pos_product_custom_text_padding_left : "";

			} else if($pos_product_option == 'image') {
				$pos_sticker_custom_id = get_post_meta( $id, '_pos_sticker_custom_id', true );
				$settings['sale_product_custom_sticker'] = !empty( $pos_sticker_custom_id ) ? wp_get_attachment_thumb_url($pos_sticker_custom_id) : "";

				$pos_product_sticker_image_width = get_post_meta( $id, 'pos_sticker_image_width', true );
				$settings['sale_product_sticker_image_width'] = !empty( $pos_product_sticker_image_width ) ? $pos_product_sticker_image_width : "";

				$pos_product_sticker_image_height = get_post_meta( $id, 'pos_sticker_image_height', true );
				$settings['sale_product_sticker_image_height'] = !empty( $pos_product_sticker_image_height ) ? $pos_product_sticker_image_height : "";

			}

			$enable_sale_product_schedule_sticker = get_post_meta( $id, '_enable_pos_product_schedule_sticker', true );
			$settings['enable_sale_product_schedule_sticker'] = $enable_sale_product_schedule_sticker;

			$sale_product_schedule_start_sticker_date_time = get_post_meta( $id, '_pos_product_schedule_start_sticker_date_time', true );
			$settings['sale_product_schedule_start_sticker_date_time'] = !empty( $sale_product_schedule_start_sticker_date_time ) ? $sale_product_schedule_start_sticker_date_time : "";

			$sale_product_schedule_end_sticker_date_time = get_post_meta( $id, '_pos_product_schedule_end_sticker_date_time', true );
			$settings['sale_product_schedule_end_sticker_date_time'] = !empty( $sale_product_schedule_end_sticker_date_time ) ? $sale_product_schedule_end_sticker_date_time : "";

			$sale_product_schedule_sticker_option = get_post_meta( $id, '_pos_product_schedule_option', true );
			$settings['sale_product_schedule_sticker_option'] = $sale_product_schedule_sticker_option;

			if($enable_sale_product_schedule_sticker == "yes"){
				if($sale_product_schedule_sticker_option == 'text_schedule') {

					$sale_product_schedule_custom_text = get_post_meta( $id, '_pos_schedule_product_custom_text', true );

					$settings['sale_product_schedule_custom_text'] = !empty( $sale_product_schedule_custom_text ) ? $sale_product_schedule_custom_text : "";
					$settings['enable_sale_schedule_product_style'] = !empty( $enable_sale_schedule_product_style ) ? $enable_sale_schedule_product_style : "";

					$sale_product_schedule_custom_text_fontcolor = get_post_meta( $id, '_pos_schedule_product_custom_text_fontcolor', true );
					$settings['sale_product_schedule_custom_text_fontcolor'] = !empty( $sale_product_schedule_custom_text_fontcolor ) ? $sale_product_schedule_custom_text_fontcolor : "";

					$sale_product_schedule_custom_text_backcolor = get_post_meta( $id, '_pos_schedule_product_custom_text_backcolor', true );
					$settings['sale_product_schedule_custom_text_backcolor'] = !empty( $sale_product_schedule_custom_text_backcolor ) ? $sale_product_schedule_custom_text_backcolor : "";

					$sale_product_schedule_text_padding_top = get_post_meta( $id, '_pos_schedule_product_custom_text_padding_top', true );
					$settings['sale_product_schedule_text_padding_top'] = !empty( $sale_product_schedule_text_padding_top ) ? $sale_product_schedule_text_padding_top : "";

					$sale_product_schedule_text_padding_right = get_post_meta( $id, '_pos_product_schedule_custom_text_padding_right', true );
					$settings['sale_product_schedule_text_padding_right'] = !empty( $sale_product_schedule_text_padding_right ) ? $sale_product_schedule_text_padding_right : "";
				
					$sale_product_schedule_text_padding_bottom = get_post_meta( $id, '_pos_product_schedule_custom_text_padding_bottom', true );
					$settings['sale_product_schedule_text_padding_bottom'] = !empty( $sale_product_schedule_text_padding_bottom ) ? $sale_product_schedule_text_padding_bottom : "";
					
					$sale_product_schedule_text_padding_left = get_post_meta( $id, '_pos_product_schedule_custom_text_padding_left', true );
					$settings['sale_product_schedule_text_padding_left'] = !empty( $sale_product_schedule_text_padding_left ) ? $sale_product_schedule_text_padding_left : "";

				}elseif($sale_product_schedule_sticker_option == 'image_schedule'){

					$sale_product_schedule_sticker_image_width = get_post_meta( $id, 'pos_schedule_sticker_image_width', true );
					$settings['sale_product_schedule_sticker_image_width'] = !empty( $sale_product_schedule_sticker_image_width ) ? $sale_product_schedule_sticker_image_width : "";

					$sale_product_schedule_sticker_image_height = get_post_meta( $id, 'pos_schedule_sticker_image_height', true );
					$settings['sale_product_schedule_sticker_image_height'] = !empty( $sale_product_schedule_sticker_image_height ) ? $sale_product_schedule_sticker_image_height : "";

					$sale_product_schedule_custom_sticker = get_post_meta( $id, '_pos_schedule_sticker_custom_id', true );
					$settings['sale_product_schedule_custom_sticker'] = !empty( $sale_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url($sale_product_schedule_custom_sticker) : "";

				}
			}

			return $settings;
		} elseif ( $enable_pos_sticker == 'no' ) {
			$settings['enable_sale_product_sticker'] = 'no';
			return $settings;
		}

		// Get categories
		$terms = get_the_terms( $id, 'product_cat' );
		if( !empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$enable_pos_sticker = get_term_meta( $term->term_id, 'enable_pos_sticker', true );
				if( !empty( $enable_pos_sticker ) ) {
					if( $enable_pos_sticker == 'yes' ) {
						$settings['enable_sale_product_sticker'] = 'yes';
						$pos_sticker_pos = get_term_meta( $term->term_id, 'pos_sticker_pos', true );
						$settings['sale_product_position'] = !empty( $pos_sticker_pos ) ? $pos_sticker_pos : "";

						$pos_product_sticker_left_right = get_term_meta( $term->term_id, 'pos_sticker_left_right', true );
						$settings['sale_product_sticker_left_right'] = $pos_product_sticker_left_right;

						$pos_product_top = get_term_meta( $term->term_id, 'pos_sticker_top', true );
						$settings['sale_product_sticker_top'] = !empty( $pos_product_top ) ? $pos_product_top : "";
						
						$pos_sticker_rotate = get_term_meta( $term->term_id, 'pos_sticker_rotate', true );
						$settings['sale_product_sticker_rotate'] = !empty( $pos_sticker_rotate ) ? $pos_sticker_rotate : "";

						$pos_sticker_animation_type = get_term_meta( $term->term_id, 'pos_sticker_category_animation_type', true );
						$settings['sale_product_sticker_animation_type'] = !empty( $pos_sticker_animation_type ) ? $pos_sticker_animation_type : "";

						$pos_sticker_animation_direction = get_term_meta( $term->term_id, 'pos_sticker_category_animation_direction', true );
						$settings['sale_product_sticker_animation_direction'] = !empty( $pos_sticker_animation_direction ) ? $pos_sticker_animation_direction : "";

						$pos_sticker_animation_scale = get_term_meta( $term->term_id, 'pos_sticker_category_animation_scale', true );
						$settings['sale_product_sticker_animation_scale'] = !empty( $pos_sticker_animation_scale ) ? $pos_sticker_animation_scale : "";

						$pos_sticker_animation_iteration_count = get_term_meta( $term->term_id, 'pos_sticker_category_animation_iteration_count', true );
						$settings['sale_product_sticker_animation_iteration_count'] = !empty( $pos_sticker_animation_iteration_count ) ? $pos_sticker_animation_iteration_count : "";

						$pos_sticker_animation_delay = get_term_meta( $term->term_id, 'pos_sticker_category_animation_type_delay', true );
						$settings['sale_product_sticker_animation_delay'] = !empty( $pos_sticker_animation_delay ) ? $pos_sticker_animation_delay : "";
						
						$pos_product_option = get_term_meta( $term->term_id, 'pos_product_option', true );
						if( !empty( $pos_product_option ) ) $settings['sale_product_option'] = $pos_product_option;
						if($pos_product_option == 'text') {
							$pos_product_custom_text = get_term_meta( $term->term_id, 'pos_product_custom_text', true );
							$settings['sale_product_custom_text'] = !empty( $pos_product_custom_text ) ? $pos_product_custom_text : "";

							$pos_sticker_type = get_term_meta( $term->term_id, 'pos_sticker_type', true );
							$settings['enable_sale_product_style'] = !empty( $pos_sticker_type ) ? $pos_sticker_type : "";

							$pos_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'pos_product_custom_text_fontcolor', true );
							$settings['sale_product_custom_text_fontcolor'] = !empty( $pos_product_custom_text_fontcolor ) ? $pos_product_custom_text_fontcolor : "";

							$pos_product_custom_text_backcolor = get_term_meta( $term->term_id, 'pos_product_custom_text_backcolor', true );
							$settings['sale_product_custom_text_backcolor'] = !empty( $pos_product_custom_text_backcolor ) ? $pos_product_custom_text_backcolor : "";


							$pos_product_custom_text_padding_top = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_top', true );
							$settings['sale_product_text_padding_top'] = !empty( $pos_product_custom_text_padding_top ) ? $pos_product_custom_text_padding_top : "";

							$pos_product_custom_text_padding_right = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_right', true );
							$settings['sale_product_text_padding_right'] = !empty( $pos_product_custom_text_padding_right ) ? $pos_product_custom_text_padding_right : "";

							$pos_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_bottom', true );
							$settings['sale_product_text_padding_bottom'] = !empty( $pos_product_custom_text_padding_bottom ) ? $pos_product_custom_text_padding_bottom : "";

							$pos_product_custom_text_padding_left = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_left', true );
							$settings['sale_product_text_padding_left'] = !empty( $pos_product_custom_text_padding_left ) ? $pos_product_custom_text_padding_left : "";

						} else if($pos_product_option == 'image') {
							
							$pos_product_sticker_image_width = get_term_meta( $term->term_id, 'pos_sticker_image_width', true );
							$settings['sale_product_sticker_image_width'] = !empty( $pos_product_sticker_image_width ) ? $pos_product_sticker_image_width : "";

							$pos_product_sticker_image_height = get_term_meta( $term->term_id, 'pos_sticker_image_height', true );
							$settings['sale_product_sticker_image_height'] = !empty( $pos_product_sticker_image_height ) ? $pos_product_sticker_image_height : "";

							$pos_sticker_custom_id = get_term_meta( $term->term_id, 'pos_sticker_custom_id', true );							
							$settings['sale_product_custom_sticker'] = !empty( $pos_sticker_custom_id ) ? wp_get_attachment_thumb_url($pos_sticker_custom_id) : "";
						}

						$enable_sale_product_schedule_sticker = get_term_meta( $term->term_id, 'enable_pos_product_schedule_sticker_category', true );
						$settings['enable_sale_product_schedule_sticker'] = $enable_sale_product_schedule_sticker;

						$sale_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'pos_product_schedule_start_sticker_date_time', true );
						$settings['sale_product_schedule_start_sticker_date_time'] = !empty( $sale_product_schedule_start_sticker_date_time ) ? $sale_product_schedule_start_sticker_date_time : "";

						$sale_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'pos_product_schedule_end_sticker_date_time', true );
						$settings['sale_product_schedule_end_sticker_date_time'] = !empty( $sale_product_schedule_end_sticker_date_time ) ? $sale_product_schedule_end_sticker_date_time : "";

						$sale_product_schedule_sticker_option = get_term_meta( $term->term_id, 'pos_product_schedule_option', true );
						$settings['sale_product_schedule_sticker_option'] = $sale_product_schedule_sticker_option;

						if($sale_product_schedule_sticker_option == 'text_schedule') {

							$sale_product_schedule_custom_text = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text', true );
							$settings['sale_product_schedule_custom_text'] = !empty( $sale_product_schedule_custom_text ) ? $sale_product_schedule_custom_text : "";
							
							$pos_sticker_type = get_term_meta( $term->term_id, 'pos_schedule_sticker_type', true );
							$settings['enable_sale_schedule_product_style'] = !empty( $pos_sticker_type ) ? $pos_sticker_type : "";

							$sale_product_schedule_custom_text_fontcolor = get_term_meta( $term->term_id, 'pos_schedule_product_custom_text_fontcolor', true );
							$settings['sale_product_schedule_custom_text_fontcolor'] = !empty( $sale_product_schedule_custom_text_fontcolor ) ? $sale_product_schedule_custom_text_fontcolor : "";

							$sale_product_schedule_custom_text_backcolor = get_term_meta( $term->term_id, 'pos_schedule_product_custom_text_backcolor', true );
							$settings['sale_product_schedule_custom_text_backcolor'] = !empty( $sale_product_schedule_custom_text ) ? $sale_product_schedule_custom_text : "";

							$sale_product_schedule_text_padding_top = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_top', true );
							$settings['sale_product_schedule_text_padding_top'] = !empty( $sale_product_schedule_text_padding_top ) ? $sale_product_schedule_text_padding_top : "";

							$sale_product_schedule_text_padding_right = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_right', true );
							$settings['sale_product_schedule_text_padding_right'] = !empty( $sale_product_schedule_text_padding_right ) ? $sale_product_schedule_text_padding_right : "";

							$sale_product_schedule_text_padding_bottom = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_bottom', true );
							$settings['sale_product_schedule_text_padding_bottom'] = !empty( $sale_product_schedule_text_padding_bottom ) ? $sale_product_schedule_text_padding_bottom : "";

							$sale_product_schedule_text_padding_left = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_left', true );
							$settings['sale_product_schedule_text_padding_left'] = !empty( $sale_product_schedule_text_padding_left ) ? $sale_product_schedule_text_padding_left : "";

						} else if($sale_product_schedule_sticker_option == 'image_schedule') {

							$sale_product_schedule_sticker_image_width = get_term_meta( $term->term_id, 'pos_schedule_sticker_image_width', true );
							$settings['sale_product_schedule_sticker_image_width'] = !empty( $sale_product_schedule_sticker_image_width ) ? $sale_product_schedule_sticker_image_width : "";

							$sale_product_schedule_sticker_image_height = get_term_meta( $term->term_id, 'pos_schedule_sticker_image_height', true );
							$settings['sale_product_schedule_sticker_image_height'] = !empty( $sale_product_schedule_sticker_image_height ) ? $sale_product_schedule_sticker_image_height : "";

							$sale_product_schedule_custom_sticker = get_term_meta( $term->term_id, 'pos_schedule_sticker_custom_id', true );
							$settings['sale_product_schedule_custom_sticker'] = !empty( $sale_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url($sale_product_schedule_custom_sticker) : "";
						}

					} elseif ( $enable_pos_sticker == 'no' ) {
						$settings['enable_sale_product_sticker'] = 'no';
					}
					break;
				}
			}
		}

		return $settings;
	}

	/**
	 * Override soldout product stickers options level
	 */
	public function override_sop_sticker_level_settings( $settings ) {

		global $post;

		$id = !empty( $post->ID ) ? $post->ID : '';

		//If empty then return AS received
		if( empty( $id ) ) return $settings;

		$enable_sop_sticker = get_post_meta( $id, '_enable_sop_sticker', true );
		if( $enable_sop_sticker == 'yes' ) {
			$settings['enable_sold_product_sticker'] = 'yes';

			$sop_sticker_pos = get_post_meta( $id, '_sop_sticker_pos', true );
			$settings['sold_product_position'] = !empty( $sop_sticker_pos ) ? $sop_sticker_pos : "";

			$sop_product_sticker_left_right = get_post_meta( $id, 'sop_sticker_left_right', true );
			$settings['sold_product_sticker_left_right'] = !empty( $sop_product_sticker_left_right ) ? $sop_product_sticker_left_right : "";

			$sop_product_top = get_post_meta( $id, 'sop_sticker_top', true );
			$settings['sold_product_sticker_top'] = !empty( $sop_product_top ) ? $sop_product_top : "";

			$sop_sticker_rotate = get_post_meta( $id, 'sop_sticker_rotate', true );
			$settings['sold_product_sticker_rotate'] = !empty( $sop_sticker_rotate ) ? $sop_sticker_rotate : "";

			$sop_sticker_animation_type = get_post_meta( $id, 'sop_sticker_animation_type', true );
			$settings['sold_product_sticker_animation_type'] = !empty( $sop_sticker_animation_type ) ? $sop_sticker_animation_type : "";

			$sop_sticker_animation_direction = get_post_meta( $id, 'sop_sticker_animation_direction', true );
			$settings['sold_product_sticker_animation_direction'] = !empty( $sop_sticker_animation_direction ) ? $sop_sticker_animation_direction : "";

			$sop_sticker_animation_scale = get_post_meta( $id, 'sop_sticker_animation_scale', true );
			$settings['sold_product_sticker_animation_scale'] = !empty( $sop_sticker_animation_scale ) ? $sop_sticker_animation_scale : "";

			$sop_sticker_animation_iteration_count = get_post_meta( $id, 'sop_sticker_animation_iteration_count', true );
			$settings['sold_product_sticker_animation_iteration_count'] = !empty( $sop_sticker_animation_iteration_count ) ? $sop_sticker_animation_iteration_count : "";

			$sop_sticker_animation_delay = get_post_meta( $id, 'sop_sticker_animation_delay', true );
			$settings['sold_product_sticker_animation_delay'] = !empty( $sop_sticker_animation_delay ) ? $sop_sticker_animation_delay : "";
			
			$sop_product_option = get_post_meta( $id, '_sop_product_option', true );
			if( !empty( $sop_product_option ) ) $settings['sold_product_option'] = $sop_product_option;
			if($sop_product_option == 'text') {

				$sop_product_custom_text = get_post_meta( $id, '_sop_product_custom_text', true );
				$settings['sold_product_custom_text'] = !empty( $sop_product_custom_text ) ? $sop_product_custom_text : "";

				$sop_sticker_type = get_post_meta( $id, '_sop_sticker_type', true );
				$settings['enable_sold_product_style'] = !empty( $sop_sticker_type ) ? $sop_sticker_type : "";

				$sop_product_custom_text_fontcolor = get_post_meta( $id, '_sop_product_custom_text_fontcolor', true );
				$settings['sold_product_custom_text_fontcolor'] = !empty( $sop_product_custom_text_fontcolor ) ? $sop_product_custom_text_fontcolor : "";

				$sop_product_custom_text_backcolor = get_post_meta( $id, '_sop_product_custom_text_backcolor', true );
				$settings['sold_product_custom_text_backcolor'] = !empty( $sop_product_custom_text_backcolor ) ? $sop_product_custom_text_backcolor : "";

				$sop_product_custom_text_padding_top = get_post_meta( $id, '_sop_product_custom_text_padding_top', true );
				$settings['sold_product_text_padding_top'] = !empty( $sop_product_custom_text_padding_top ) ? $sop_product_custom_text_padding_top : "";

				$sop_product_custom_text_padding_right = get_post_meta( $id, '_sop_product_custom_text_padding_right', true );
				$settings['sold_product_text_padding_right'] = !empty( $sop_product_custom_text_padding_right ) ? $sop_product_custom_text_padding_right : "";

				$sop_product_custom_text_padding_bottom = get_post_meta( $id, '_sop_product_custom_text_padding_bottom', true );
				$settings['sold_product_text_padding_bottom'] = !empty( $sop_product_custom_text_padding_bottom ) ? $sop_product_custom_text_padding_bottom : "";

				$sop_product_custom_text_padding_left = get_post_meta( $id, '_sop_product_custom_text_padding_left', true );
				$settings['sold_product_text_padding_left'] = !empty( $sop_product_custom_text_padding_left ) ? $sop_product_custom_text_padding_left : "";
				
			} else if($sop_product_option == 'image') {
				$sop_sticker_custom_id = get_post_meta( $id, '_sop_sticker_custom_id', true );
				$settings['sold_product_custom_sticker'] = !empty( $sop_sticker_custom_id ) ? wp_get_attachment_thumb_url($sop_sticker_custom_id) : "";

				$sop_product_sticker_image_width = get_post_meta( $id, 'sop_sticker_image_width', true );
				$settings['sold_product_sticker_image_width'] = !empty( $sop_product_sticker_image_width ) ? $sop_product_sticker_image_width : "";

				$sop_product_sticker_image_height = get_post_meta( $id, 'sop_sticker_image_height', true );
				$settings['sold_product_sticker_image_height'] = !empty( $sop_product_sticker_image_height ) ? $sop_product_sticker_image_height : "";

			}

			$enable_sold_product_schedule_sticker = get_post_meta( $id, '_enable_sop_product_schedule_sticker', true );
			$settings['enable_sold_product_schedule_sticker'] = $enable_sold_product_schedule_sticker;

			$sold_product_schedule_start_sticker_date_time = get_post_meta( $id, '_sop_product_schedule_start_sticker_date_time', true );
			$settings['sold_product_schedule_start_sticker_date_time'] = !empty( $sold_product_schedule_start_sticker_date_time ) ? $sold_product_schedule_start_sticker_date_time : "";

			$sold_product_schedule_end_sticker_date_time = get_post_meta( $id, '_sop_product_schedule_end_sticker_date_time', true );
			$settings['sold_product_schedule_end_sticker_date_time'] = !empty( $sold_product_schedule_end_sticker_date_time ) ? $sold_product_schedule_end_sticker_date_time : "";

			$sold_product_schedule_sticker_option = get_post_meta( $id, '_sop_product_schedule_option', true );
			$settings['sold_product_schedule_sticker_option'] = !empty( $sold_product_schedule_sticker_option ) ? $sold_product_schedule_sticker_option : "";

			if($enable_sold_product_schedule_sticker == "yes"){
				if($sold_product_schedule_sticker_option == 'text_schedule') {

					$sold_product_schedule_custom_text = get_post_meta( $id, '_sop_schedule_product_custom_text', true );
					$settings['sold_product_schedule_custom_text'] = !empty( $sold_product_schedule_custom_text ) ? $sold_product_schedule_custom_text : "";
					
					$enable_sold_schedule_product_style = get_post_meta( $id, '_sop_schedule_sticker_type', true );
					$settings['enable_sold_schedule_product_style'] = !empty( $enable_sold_schedule_product_style ) ? $enable_sold_schedule_product_style : "";

					$sold_product_schedule_custom_text_fontcolor = get_post_meta( $id, '_sop_schedule_product_custom_text_fontcolor', true );
					$settings['sold_product_schedule_custom_text_fontcolor'] = !empty( $sold_product_schedule_custom_text_fontcolor ) ? $sold_product_schedule_custom_text_fontcolor : "";

					$sold_product_schedule_custom_text_backcolor = get_post_meta( $id, '_sop_schedule_product_custom_text_backcolor', true );
					$settings['sold_product_schedule_custom_text_backcolor'] = !empty( $sold_product_schedule_custom_text_backcolor ) ? $sold_product_schedule_custom_text_backcolor : "";

					$sold_product_schedule_text_padding_top = get_post_meta( $id, '_sop_schedule_product_custom_text_padding_top', true );
					$settings['sold_product_schedule_text_padding_top'] = !empty( $sold_product_schedule_text_padding_top ) ? $sold_product_schedule_text_padding_top : "";

					$sold_product_schedule_text_padding_right = get_post_meta( $id, '_sop_product_schedule_custom_text_padding_right', true );
					$settings['sold_product_schedule_text_padding_right'] = !empty( $sold_product_schedule_text_padding_right ) ? $sold_product_schedule_text_padding_right : "";

					$sold_product_schedule_text_padding_bottom = get_post_meta( $id, '_sop_product_schedule_custom_text_padding_bottom', true );
					$settings['sold_product_schedule_text_padding_bottom'] = !empty( $sold_product_schedule_text_padding_bottom ) ? $sold_product_schedule_text_padding_bottom : "";

					$sold_product_schedule_text_padding_left = get_post_meta( $id, '_sop_product_schedule_custom_text_padding_left', true );
					$settings['sold_product_schedule_text_padding_left'] = !empty( $sold_product_schedule_text_padding_left ) ? $sold_product_schedule_text_padding_left : "";

				}elseif($sold_product_schedule_sticker_option == 'image_schedule'){

					$sold_product_schedule_sticker_image_width = get_post_meta( $id, 'sop_schedule_sticker_image_width', true );
					$settings['sold_product_schedule_sticker_image_width'] = !empty( $sold_product_schedule_sticker_image_width ) ? $sold_product_schedule_sticker_image_width : "";

					$sold_product_schedule_sticker_image_height = get_post_meta( $id, 'sop_schedule_sticker_image_height', true );
					$settings['sold_product_schedule_sticker_image_height'] = !empty( $sold_product_schedule_sticker_image_height ) ? $sold_product_schedule_sticker_image_height : "";

					$sold_product_schedule_custom_sticker = get_post_meta( $id, '_sop_schedule_sticker_custom_id', true );
					$settings['sold_product_schedule_custom_sticker'] = !empty( $sold_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url($sold_product_schedule_custom_sticker) : "";

				}
			}

			return $settings;
		} elseif ( $enable_sop_sticker == 'no' ) {
			$settings['enable_sold_product_sticker'] = 'no';
			return $settings;
		}

		// Get categories
		$terms = get_the_terms( $id, 'product_cat' );
		if( !empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$enable_sop_sticker = get_term_meta( $term->term_id, 'enable_sop_sticker', true );
				if( !empty( $enable_sop_sticker ) ) {
					if( $enable_sop_sticker == 'yes' ) {
						$settings['enable_sold_product_sticker'] = 'yes';
						$sop_sticker_pos = get_term_meta( $term->term_id, 'sop_sticker_pos', true );
						$settings['sold_product_position'] = !empty( $sop_sticker_pos ) ? $sop_sticker_pos : "";

						$sop_product_sticker_left_right = get_term_meta( $term->term_id, 'sop_sticker_left_right', true );
						$settings['sold_product_sticker_left_right'] = !empty( $sop_product_sticker_left_right ) ? $sop_product_sticker_left_right : "";

						$sop_product_top = get_term_meta( $term->term_id, 'sop_sticker_top', true );
						$settings['sold_product_sticker_top'] = !empty( $sop_product_top ) ? $sop_product_top : "";

						$sop_sticker_rotate = get_term_meta( $term->term_id, 'sop_sticker_rotate', true );
						$settings['sold_product_sticker_rotate'] = !empty( $sop_sticker_rotate ) ? $sop_sticker_rotate : "";

						$sop_sticker_animation_type = get_term_meta( $term->term_id, 'sop_sticker_category_animation_type', true );
						$settings['sold_product_sticker_animation_type'] = !empty( $sop_sticker_animation_type ) ? $sop_sticker_animation_type : "";

						$sop_sticker_animation_direction = get_term_meta( $term->term_id, 'sop_sticker_category_animation_direction', true );
						$settings['sold_product_sticker_animation_direction'] = !empty( $sop_sticker_animation_direction ) ? $sop_sticker_animation_direction : "";

						$sop_sticker_pos = get_term_meta( $term->term_id, 'sop_sticker_category_animation_scale', true );
						$settings['sold_product_sticker_animation_scale'] = !empty( $sop_sticker_pos ) ? $sop_sticker_pos : "";

						$sop_sticker_animation_iteration_count = get_term_meta( $term->term_id, 'sop_sticker_category_animation_iteration_count', true );
						$settings['sold_product_sticker_animation_iteration_count'] = !empty( $sop_sticker_animation_iteration_count ) ? $sop_sticker_animation_iteration_count : "";

						$sop_sticker_animation_delay = get_term_meta( $term->term_id, 'sop_sticker_category_animation_type_delay', true );
						$settings['sold_product_sticker_animation_delay'] = !empty( $sop_sticker_animation_delay ) ? $sop_sticker_animation_delay : "";

						$sop_product_option = get_term_meta( $term->term_id, 'sop_product_option', true );
						if( !empty( $sop_product_option ) ) $settings['sold_product_option'] = $sop_product_option;
						
						if($sop_product_option == 'text') {
						
							$sop_product_custom_text = get_term_meta( $term->term_id, 'sop_product_custom_text', true );
							$settings['sold_product_custom_text'] = !empty( $sop_product_custom_text ) ? $sop_product_custom_text : "";

							$sop_sticker_type = get_term_meta( $term->term_id, 'sop_sticker_type', true );
							$settings['enable_sold_product_style'] = !empty( $sop_sticker_type ) ? $sop_sticker_type : "";

							$sop_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'sop_product_custom_text_fontcolor', true );
							$settings['sold_product_custom_text_fontcolor'] = !empty( $sop_product_custom_text_fontcolor ) ? $sop_product_custom_text_fontcolor : "";

							$sop_product_custom_text_backcolor = get_term_meta( $term->term_id, 'sop_product_custom_text_backcolor', true );
							$settings['sold_product_custom_text_backcolor'] = !empty( $sop_product_custom_text_backcolor ) ? $sop_product_custom_text_backcolor : "";

							$sop_product_custom_text_padding_top = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_top', true );
							$settings['sold_product_text_padding_top'] = !empty( $sop_product_custom_text_padding_top ) ? $sop_product_custom_text_padding_top : "";

							$sop_product_custom_text_padding_right = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_right', true );
							$settings['sold_product_text_padding_right'] = !empty( $sop_product_custom_text_padding_right ) ? $sop_product_custom_text_padding_right : "";

							$sop_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_bottom', true );
							$settings['sold_product_text_padding_bottom'] = !empty( $sop_product_custom_text_padding_bottom ) ? $sop_product_custom_text_padding_bottom : "";

							$sop_product_custom_text_padding_left = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_left', true );
							$settings['sold_product_text_padding_left'] = !empty( $sop_product_custom_text_padding_left ) ? $sop_product_custom_text_padding_left : "";

						} else if($sop_product_option == 'image') {

							$sop_product_sticker_image_width = get_term_meta( $term->term_id, 'sop_sticker_image_width', true );
							$settings['sold_product_sticker_image_width'] = !empty( $sop_product_sticker_image_width ) ? $sop_product_sticker_image_width : "";

							$sop_product_sticker_image_height = get_term_meta( $term->term_id, 'sop_sticker_image_height', true );
							$settings['sold_product_sticker_image_height'] = !empty( $sop_product_sticker_image_height ) ? $sop_product_sticker_image_height : "";

							$sop_sticker_custom_id = get_term_meta( $term->term_id, 'sop_sticker_custom_id', true );
							$settings['sold_product_custom_sticker'] = !empty( $sop_sticker_custom_id ) ?  wp_get_attachment_thumb_url( $sop_sticker_custom_id ) : "";

						}

						$enable_sold_product_schedule_sticker = get_term_meta( $term->term_id, 'enable_sop_product_schedule_sticker_category', true );
						$settings['enable_sold_product_schedule_sticker'] = $enable_sold_product_schedule_sticker;

						$sold_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'sop_product_schedule_start_sticker_date_time', true );
						$settings['sold_product_schedule_start_sticker_date_time'] = $sold_product_schedule_start_sticker_date_time;

						$sold_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'sop_product_schedule_end_sticker_date_time', true );
						$settings['sold_product_schedule_end_sticker_date_time'] = $sold_product_schedule_end_sticker_date_time;

						$sold_product_schedule_sticker_option = get_term_meta( $term->term_id, 'sop_product_schedule_option', true );
						$settings['sold_product_schedule_sticker_option'] = $sold_product_schedule_sticker_option;

						if($sold_product_schedule_sticker_option == 'text_schedule') {

							$sold_product_schedule_custom_text = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text', true );
							$settings['sold_product_schedule_custom_text'] = !empty( $sold_product_schedule_custom_text ) ? $sold_product_schedule_custom_text : "";
							
							$sop_sticker_type = get_term_meta( $term->term_id, 'sop_schedule_sticker_type', true );
							$settings['enable_sold_schedule_product_style'] = !empty( $sop_sticker_type ) ? $sop_sticker_type : "";

							$sold_product_schedule_custom_text_fontcolor = get_term_meta( $term->term_id, 'sop_schedule_product_custom_text_fontcolor', true );
							$settings['sold_product_schedule_custom_text_fontcolor'] = !empty( $sold_product_schedule_custom_text_fontcolor ) ? $sold_product_schedule_custom_text_fontcolor : "";

							$sold_product_schedule_custom_text_backcolor = get_term_meta( $term->term_id, 'sop_schedule_product_custom_text_backcolor', true );
							$settings['sold_product_schedule_custom_text_backcolor'] = !empty( $sold_product_schedule_custom_text ) ? $sold_product_schedule_custom_text : "";

							$sold_product_schedule_text_padding_top = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_top', true );
							$settings['sold_product_schedule_text_padding_top'] = !empty( $sold_product_schedule_text_padding_top ) ? $sold_product_schedule_text_padding_top : "";

							$sold_product_schedule_text_padding_right = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_right', true );
							$settings['sold_product_schedule_text_padding_right'] = !empty( $sold_product_schedule_text_padding_right ) ? $sold_product_schedule_text_padding_right : "";

							$sold_product_schedule_text_padding_bottom = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_bottom', true );
							$settings['sold_product_schedule_text_padding_bottom'] = !empty( $sold_product_schedule_text_padding_bottom ) ? $sold_product_schedule_text_padding_bottom : "";

							$sold_product_schedule_text_padding_left = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_left', true );
							$settings['sold_product_schedule_text_padding_left'] = !empty( $sold_product_schedule_text_padding_left ) ? $sold_product_schedule_text_padding_left : "";

						} else if($sold_product_schedule_sticker_option == 'image_schedule') {

							$sold_product_schedule_sticker_image_width = get_term_meta( $term->term_id, 'sop_schedule_sticker_image_width', true );
							$settings['sold_product_schedule_sticker_image_width'] = !empty( $sold_product_schedule_sticker_image_width ) ? $sold_product_schedule_sticker_image_width : "";

							$sold_product_schedule_sticker_image_height = get_term_meta( $term->term_id, 'sop_schedule_sticker_image_height', true );
							$settings['sold_product_schedule_sticker_image_height'] = !empty( $sold_product_schedule_sticker_image_height ) ? $sold_product_schedule_sticker_image_height : "";

							$sold_product_schedule_custom_sticker = get_term_meta( $term->term_id, 'sop_schedule_sticker_custom_id', true );
							$settings['sold_product_schedule_custom_sticker'] = !empty( $sold_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url($sold_product_schedule_custom_sticker) : "";

						}

					} elseif ( $enable_sop_sticker == 'no' ) {
						$settings['enable_sold_product_sticker'] = 'no';
					}
					break;
				}
			}
		}

		return $settings;
	}

	/**
	 * Override Custom Product Sticker options level
	 */
	public function override_cust_sticker_level_settings( $settings ) {

		global $post;

		$id = !empty( $post->ID ) ? $post->ID : '';

		//If empty then return AS received
		if( empty( $id ) ) return $settings;

		$enable_cust_sticker 	= get_post_meta( $id, '_enable_cust_sticker', true );
		if( $enable_cust_sticker == 'yes' ) {

			$settings['enable_cust_product_sticker'] = 'yes';

			$cust_sticker_pos 	= get_post_meta( $id, '_cust_sticker_pos', true );
			$settings['cust_product_position'] = !empty( $cust_sticker_pos ) ? $cust_sticker_pos : "";

			$cust_product_sticker_left_right 	= get_post_meta( $id, 'cust_sticker_left_right', true );
			$settings['cust_sticker_left_right'] = !empty( $cust_product_sticker_left_right ) ? $cust_product_sticker_left_right : "";

			$cust_product_top 	= get_post_meta( $id, 'cust_sticker_top', true );
			$settings['cust_product_sticker_top'] = !empty( $cust_product_top ) ? $cust_sticker_pos : "";
			
			$cust_sticker_rotate = get_post_meta( $id, 'cust_sticker_rotate', true );
			$settings['cust_product_sticker_rotate'] = !empty( $cust_sticker_rotate ) ? $cust_sticker_rotate : "";

			$cust_sticker_animation_type = get_post_meta( $id, 'cust_sticker_animation_type', true );
			$settings['cust_product_sticker_animation_type'] = !empty( $cust_sticker_animation_type ) ? $cust_sticker_animation_type : "";

			$cust_sticker_animation_direction = get_post_meta( $id, 'cust_sticker_animation_direction', true );
			$settings['cust_product_sticker_animation_direction'] = !empty( $cust_sticker_animation_direction ) ? $cust_sticker_animation_direction : "";

			$cust_sticker_animation_scale = get_post_meta( $id, 'cust_sticker_animation_scale', true );
			$settings['cust_product_sticker_animation_scale'] = !empty( $cust_sticker_animation_scale ) ? $cust_sticker_animation_scale : "";

			$cust_sticker_animation_iteration_count = get_post_meta( $id, 'cust_sticker_animation_iteration_count', true );
			$settings['cust_product_sticker_animation_iteration_count'] = !empty( $cust_sticker_animation_iteration_count ) ? $cust_sticker_animation_iteration_count : "";

			$cust_sticker_animation_delay = get_post_meta( $id, 'cust_sticker_animation_delay', true );
			$settings['cust_product_sticker_animation_delay'] = !empty( $cust_sticker_animation_delay ) ? $cust_sticker_animation_delay : "";

			$cust_product_option = get_post_meta( $id, '_cust_product_option', true );
			if( !empty( $cust_product_option ) ) $settings['cust_product_option'] = $cust_product_option;

			if($cust_product_option == 'text') {

				$cust_product_custom_text = get_post_meta( $id, '_cust_product_custom_text', true );
				$settings['cust_product_custom_text'] = !empty( $cust_product_custom_text ) ? $cust_product_custom_text : "";

				$cust_sticker_type = get_post_meta( $id, '_cust_sticker_type', true );
				$settings['enable_cust_product_style'] = !empty( $cust_sticker_type ) ? $cust_sticker_type : "";

				$cust_product_custom_text_fontcolor = get_post_meta( $id, '_cust_product_custom_text_fontcolor', true );
				$settings['cust_product_custom_text_fontcolor'] = !empty( $cust_product_custom_text_fontcolor ) ? $cust_product_custom_text_fontcolor : "";

				$cust_product_custom_text_backcolor = get_post_meta( $id, '_cust_product_custom_text_backcolor', true );
				$settings['cust_product_custom_text_backcolor'] = !empty( $cust_product_custom_text_backcolor ) ? $cust_product_custom_text_backcolor : "";

				$cust_product_custom_text_padding_top = get_post_meta( $id, '_cust_product_custom_text_padding_top', true );
				$settings['cust_product_text_padding_top'] = !empty( $cust_product_custom_text_padding_top ) ? $cust_product_custom_text_padding_top : "";

				$cust_product_custom_text_padding_right = get_post_meta( $id, '_cust_product_custom_text_padding_right', true );
				$settings['cust_product_text_padding_right'] = !empty( $cust_product_custom_text_padding_right ) ? $cust_product_custom_text_padding_right : "";

				$cust_product_custom_text_padding_bottom = get_post_meta( $id, '_cust_product_custom_text_padding_bottom', true );
				$settings['cust_product_text_padding_bottom'] = !empty( $cust_product_custom_text_padding_bottom ) ? $cust_product_custom_text_padding_bottom : "";

				$cust_product_custom_text_padding_left = get_post_meta( $id, '_cust_product_custom_text_padding_left', true );
				$settings['cust_product_text_padding_left'] = !empty( $cust_product_custom_text_padding_left ) ? $cust_product_custom_text_padding_left : "";

			} else if($cust_product_option == 'image') {

				$cust_sticker_custom_id = get_post_meta( $id, '_cust_sticker_custom_id', true );
				$settings['cust_product_custom_sticker'] = !empty( $cust_sticker_custom_id ) ? wp_get_attachment_thumb_url( $cust_sticker_custom_id ) : "";

				$cust_product_sticker_image_width = get_post_meta( $id, 'cust_sticker_image_width', true );
				$settings['cust_product_sticker_image_width'] = !empty( $cust_product_sticker_image_width ) ? $cust_product_sticker_image_width : "";

				$cust_product_sticker_image_height = get_post_meta( $id, 'cust_sticker_image_height', true );
				$settings['cust_product_sticker_image_height'] = !empty( $cust_product_sticker_image_height ) ? $cust_product_sticker_image_height : "";
				
			}

			$enable_cust_product_schedule_sticker = get_post_meta( $id, '_enable_cust_product_schedule_sticker', true );
			$settings['enable_cust_product_schedule_sticker'] = $enable_cust_product_schedule_sticker;

			$cust_product_schedule_start_sticker_date_time = get_post_meta( $id, '_cust_product_schedule_start_sticker_date_time', true );
			$settings['cust_product_schedule_start_sticker_date_time'] = !empty( $cust_product_schedule_start_sticker_date_time ) ? $cust_product_schedule_start_sticker_date_time : "";

			$cust_product_schedule_end_sticker_date_time = get_post_meta( $id, '_cust_product_schedule_end_sticker_date_time', true );
			$settings['cust_product_schedule_end_sticker_date_time'] = !empty( $cust_product_schedule_end_sticker_date_time ) ? $cust_product_schedule_end_sticker_date_time : "";

			$cust_product_schedule_sticker_option = get_post_meta( $id, '_cust_product_schedule_option', true );
			$settings['cust_product_schedule_sticker_option'] = $cust_product_schedule_sticker_option;

			if($enable_cust_product_schedule_sticker == "yes"){
				if($cust_product_schedule_sticker_option == 'text_schedule') {

					$cust_product_schedule_custom_text = get_post_meta( $id, '_cust_schedule_product_custom_text', true );
					$settings['cust_product_schedule_custom_text'] = !empty( $cust_product_schedule_custom_text ) ? $cust_product_schedule_custom_text : "";
					
					$enable_cust_schedule_product_style = get_post_meta( $id, '_cust_schedule_sticker_type', true );
					$settings['enable_cust_schedule_product_style'] = !empty( $enable_cust_schedule_product_style ) ? $enable_cust_schedule_product_style : "";

					$cust_product_schedule_custom_text_fontcolor = get_post_meta( $id, '_cust_schedule_product_custom_text_fontcolor', true );
					$settings['cust_product_schedule_custom_text_fontcolor'] = !empty( $cust_product_schedule_custom_text_fontcolor ) ? $cust_product_schedule_custom_text_fontcolor : "";

					$cust_product_schedule_custom_text_backcolor = get_post_meta( $id, '_cust_schedule_product_custom_text_backcolor', true );
					$settings['cust_product_schedule_custom_text_backcolor'] = !empty( $cust_product_schedule_custom_text_backcolor ) ? $cust_product_schedule_custom_text_backcolor : "";

					$cust_product_schedule_text_padding_top = get_post_meta( $id, '_cust_schedule_product_custom_text_padding_top', true );
					$settings['cust_product_schedule_text_padding_top'] = !empty( $cust_product_schedule_text_padding_top ) ? $cust_product_schedule_text_padding_top : "";

					$cust_product_schedule_text_padding_right = get_post_meta( $id, '_cust_product_schedule_custom_text_padding_right', true );
					$settings['cust_product_schedule_text_padding_right'] = !empty( $cust_product_schedule_text_padding_right ) ? $cust_product_schedule_text_padding_right : "";

					$cust_product_schedule_text_padding_bottom = get_post_meta( $id, '_cust_product_schedule_custom_text_padding_bottom', true );
					$settings['cust_product_schedule_text_padding_bottom'] = !empty( $cust_product_schedule_text_padding_bottom ) ? $cust_product_schedule_text_padding_bottom : "";

					$cust_product_schedule_text_padding_left = get_post_meta( $id, '_cust_product_schedule_custom_text_padding_left', true );
					$settings['cust_product_schedule_text_padding_left'] = !empty( $cust_product_schedule_text_padding_left ) ? $cust_product_schedule_text_padding_left : "";

				}elseif($cust_product_schedule_sticker_option == 'image_schedule'){

					$cust_product_schedule_sticker_image_width = get_post_meta( $id, 'cust_schedule_sticker_image_width', true );
					$settings['cust_product_schedule_sticker_image_width'] = !empty( $cust_product_schedule_sticker_image_width ) ? $cust_product_schedule_sticker_image_width : "";

					$cust_product_schedule_sticker_image_height = get_post_meta( $id, 'cust_schedule_sticker_image_height', true );
					$settings['cust_product_schedule_sticker_image_height'] = !empty( $cust_product_schedule_sticker_image_height ) ? $cust_product_schedule_sticker_image_height : "";

					$cust_product_schedule_custom_sticker = get_post_meta( $id, '_cust_schedule_sticker_custom_id', true );
					$settings['cust_product_schedule_custom_sticker'] = !empty( $cust_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url( $cust_product_schedule_custom_sticker ) : "";
				}
			}
			
			return $settings;
		} elseif ( $enable_cust_sticker == 'no' ) {
			$settings['enable_cust_product_sticker'] = 'no';
			return $settings;
		}

		// Get categories
		$terms = get_the_terms( $id, 'product_cat' );
		if( !empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$enable_cust_sticker = get_term_meta( $term->term_id, 'enable_cust_sticker', true );
				if( !empty( $enable_cust_sticker ) ) {
					if( $enable_cust_sticker == 'yes' ) {
						$settings['enable_cust_product_sticker'] = 'yes';

						$cust_sticker_pos = get_term_meta( $term->term_id, 'cust_sticker_pos', true );
						$settings['cust_product_position'] = !empty( $cust_sticker_pos ) ? $cust_sticker_pos : "";

						$cust_product_sticker_left_right = get_term_meta( $term->term_id, 'cust_sticker_left_right', true );
						$settings['cust_sticker_left_right'] = !empty( $cust_product_sticker_left_right ) ? $cust_product_sticker_left_right : "";

						$cust_product_top = get_term_meta( $term->term_id, 'cust_sticker_top', true );
						$settings['cust_product_sticker_top'] = !empty( $cust_sticker_pos ) ? $cust_sticker_pos : "";

						$cust_sticker_rotate = get_term_meta( $term->term_id, 'cust_sticker_rotate', true );
						$settings['cust_product_sticker_rotate'] = !empty( $cust_sticker_rotate ) ? $cust_sticker_rotate : "";

						$cust_sticker_animation_type = get_term_meta( $term->term_id, 'cust_sticker_category_animation_type', true );
						$settings['cust_product_sticker_animation_type'] = !empty( $cust_sticker_animation_type ) ? $cust_sticker_animation_type : "";

						$cust_sticker_animation_direction = get_term_meta( $term->term_id, 'cust_sticker_category_animation_direction', true );
						$settings['cust_product_sticker_animation_direction'] = !empty( $cust_sticker_animation_direction ) ? $cust_sticker_animation_direction : "";

						$cust_sticker_animation_scale = get_term_meta( $term->term_id, 'cust_sticker_category_animation_scale', true );
						$settings['cust_product_sticker_animation_scale'] = !empty( $cust_sticker_animation_scale ) ? $cust_sticker_animation_scale : "";

						$cust_sticker_animation_iteration_count = get_term_meta( $term->term_id, 'cust_sticker_category_animation_iteration_count', true );
						$settings['cust_product_sticker_animation_iteration_count'] = !empty( $cust_sticker_animation_iteration_count ) ? $cust_sticker_animation_iteration_count : "";

						$cust_sticker_animation_delay = get_term_meta( $term->term_id, 'cust_sticker_category_animation_type_delay', true );
						$settings['cust_product_sticker_animation_delay'] = !empty( $cust_sticker_animation_delay ) ? $cust_sticker_animation_delay : "";

						$cust_product_option = get_term_meta( $term->term_id, 'cust_product_option', true );
						if( !empty( $cust_product_option ) ) $settings['cust_product_option'] = $cust_product_option;
						if($cust_product_option == 'text') {
							
							$cust_product_custom_text = get_term_meta( $term->term_id, 'cust_product_custom_text', true );
							$settings['cust_product_custom_text'] = !empty( $cust_product_custom_text ) ? $cust_product_custom_text : "";

							$cust_sticker_type = get_term_meta( $term->term_id, 'cust_sticker_type', true );
							$settings['enable_cust_product_style'] = !empty( $cust_sticker_type ) ? $cust_sticker_type : "";

							$cust_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'cust_product_custom_text_fontcolor', true );
							$settings['cust_product_custom_text_fontcolor'] = !empty( $cust_product_custom_text_fontcolor ) ? $cust_product_custom_text_fontcolor : "";

							$cust_product_custom_text_backcolor = get_term_meta( $term->term_id, 'cust_product_custom_text_backcolor', true );
							$settings['cust_product_custom_text_backcolor'] = !empty( $cust_product_custom_text_backcolor ) ? $cust_product_custom_text_backcolor : "";

							$cust_product_custom_text_padding_top = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_top', true );
							$settings['cust_product_text_padding_top'] = !empty( $cust_product_custom_text_padding_top ) ? $cust_product_custom_text_padding_top : "";

							$cust_product_custom_text_padding_right = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_right', true );
							$settings['cust_product_text_padding_right'] = !empty( $cust_product_custom_text_padding_right ) ? $cust_product_custom_text_padding_right : "";

							$cust_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_bottom', true );
							$settings['cust_product_text_padding_bottom'] = !empty( $cust_product_custom_text_padding_bottom ) ? $cust_product_custom_text_padding_bottom : "";

							$cust_product_custom_text_padding_left = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_left', true );
							$settings['cust_product_text_padding_left'] = !empty( $cust_product_custom_text_padding_left ) ? $cust_product_custom_text_padding_left : "";

						} else if($cust_product_option == 'image') {

							$cust_product_sticker_image_width = get_term_meta( $term->term_id, 'cust_sticker_image_width', true );
							$settings['cust_product_sticker_image_width'] = !empty( $cust_product_sticker_image_width ) ? $cust_product_sticker_image_width : "";

							$cust_product_sticker_image_height = get_term_meta( $term->term_id, 'cust_sticker_image_height', true );
							$settings['cust_product_sticker_image_height'] = !empty( $cust_product_sticker_image_height ) ? $cust_product_sticker_image_height : "";

							$cust_sticker_custom_id = get_term_meta( $term->term_id, 'cust_sticker_custom_id', true );
							$settings['cust_product_custom_sticker'] = !empty( $cust_sticker_custom_id ) ? wp_get_attachment_thumb_url( $cust_sticker_custom_id ) : "";
						}

						$enable_cust_product_schedule_sticker = get_term_meta( $term->term_id, 'enable_cust_product_schedule_sticker_category', true );
						$settings['enable_cust_product_schedule_sticker'] = $enable_cust_product_schedule_sticker;

						$cust_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'cust_product_schedule_start_sticker_date_time', true );
						$settings['cust_product_schedule_start_sticker_date_time'] = !empty( $cust_product_schedule_start_sticker_date_time ) ? $cust_product_schedule_start_sticker_date_time : "";

						$cust_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'cust_product_schedule_end_sticker_date_time', true );
						$settings['cust_product_schedule_end_sticker_date_time'] = !empty( $cust_product_schedule_end_sticker_date_time ) ? $cust_product_schedule_end_sticker_date_time : "";

						$cust_product_schedule_sticker_option = get_term_meta( $term->term_id, 'cust_product_schedule_option', true );
						$settings['cust_product_schedule_sticker_option'] = $cust_product_schedule_sticker_option;

						if($cust_product_schedule_sticker_option == 'text_schedule') {

							$cust_product_schedule_custom_text = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text', true );
							$settings['cust_product_schedule_custom_text'] = !empty( $cust_product_schedule_custom_text ) ? $cust_product_schedule_custom_text : "";

							$cust_sticker_type = get_term_meta( $term->term_id, 'cust_schedule_sticker_type', true );
							$settings['enable_cust_schedule_product_style'] = !empty( $cust_sticker_type ) ? $cust_sticker_type : "";

							$cust_product_schedule_custom_text_fontcolor = get_term_meta( $term->term_id, 'cust_schedule_product_custom_text_fontcolor', true );
							$settings['cust_product_schedule_custom_text_fontcolor'] = !empty( $cust_product_schedule_custom_text_fontcolor ) ? $cust_product_schedule_custom_text_fontcolor : "";

							$cust_product_schedule_custom_text_backcolor = get_term_meta( $term->term_id, 'cust_schedule_product_custom_text_backcolor', true );
							$settings['cust_product_schedule_custom_text_backcolor'] = !empty( $cust_product_schedule_custom_text ) ? $cust_product_schedule_custom_text : "";

							$cust_product_schedule_text_padding_top = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_top', true );
							$settings['cust_product_schedule_text_padding_top'] = !empty( $cust_product_schedule_text_padding_top ) ? $cust_product_schedule_text_padding_top : "";

							$cust_product_schedule_text_padding_right = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_right', true );
							$settings['cust_product_schedule_text_padding_right'] = !empty( $cust_product_schedule_text_padding_right ) ? $cust_product_schedule_text_padding_right : "";

							$cust_product_schedule_text_padding_bottom = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_bottom', true );
							$settings['cust_product_schedule_text_padding_bottom'] = !empty( $cust_product_schedule_text_padding_bottom ) ? $cust_product_schedule_text_padding_bottom : "";

							$cust_product_schedule_text_padding_left = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_left', true );
							$settings['cust_product_schedule_text_padding_left'] = !empty( $cust_product_schedule_text_padding_left ) ? $cust_product_schedule_text_padding_left : "";

						} else if($cust_product_schedule_sticker_option == 'image_schedule') {

							$cust_product_schedule_sticker_image_width = get_term_meta( $term->term_id, 'cust_schedule_sticker_image_width', true );
							$settings['cust_product_schedule_sticker_image_width'] = !empty( $cust_product_schedule_sticker_image_width ) ? $cust_product_schedule_sticker_image_width : "";

							$cust_product_schedule_sticker_image_height = get_term_meta( $term->term_id, 'cust_schedule_sticker_image_height', true );
							$settings['cust_product_schedule_sticker_image_height'] = !empty( $cust_product_schedule_sticker_image_height ) ? $cust_product_schedule_sticker_image_height : "";

							$cust_product_schedule_custom_sticker = get_term_meta( $term->term_id, 'cust_schedule_sticker_custom_id', true );
							$settings['cust_product_schedule_custom_sticker'] = !empty( $cust_product_schedule_custom_sticker ) ? wp_get_attachment_thumb_url( $cust_product_schedule_custom_sticker ) : "";
						}

					} elseif ( $enable_cust_sticker == 'no' ) {
						$settings['enable_cust_product_sticker'] = 'no';
					}
					break;
				}
			}
		}

		return $settings;
	}

	/**
	 * Call back function for show new product badge.
	 *
	 * @return void
	 * @param No arguments passed
	 * @author Weblineindia
	 * @since    1.0.0
	 */
	public function show_product_new_badge() {

		//Override sticker options
		$new_product_settings = $this->override_np_sticker_level_settings( $this->new_product_settings );

		if ( ! $this->sold_out && $this->general_settings['enable_sticker'] == "yes" && $new_product_settings['enable_new_product_sticker'] == "yes") 		{

			if((!is_product() && $this->general_settings['enable_sticker_list'] == "yes" ) || (is_product() && $this->general_settings['enable_sticker_detail'] == "yes"))
			{
				$postdate = get_the_time ( 'Y-m-d' );
				$postdatestamp = strtotime ( $postdate );				
				$newness = (($new_product_settings['new_product_sticker_days']=="") ? 10 : trim($new_product_settings['new_product_sticker_days']));		
				$classPosition=(($new_product_settings['new_product_position']=='left')? ((is_product())? " pos_left_detail " : " pos_left " ) : ((is_product())? " pos_right_detail " : " pos_right "));
				$classType = (($new_product_settings['enable_new_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');
				$classTypeSch = (($new_product_settings['enable_new_schedule_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');

				$new_product_top = isset($new_product_settings['new_product_sticker_top']) && $new_product_settings['new_product_sticker_top'] !== '' ? absint($new_product_settings['new_product_sticker_top']) . 'px' : '';
				$new_product_top = !empty($new_product_top) ? "top: $new_product_top;" : "";				

				$new_product_sticker_left_right = isset($new_product_settings['new_product_sticker_left_right']) && $new_product_settings['new_product_sticker_left_right'] !== '' ? absint($new_product_settings['new_product_sticker_left_right']) . 'px' : '';
				if($new_product_settings['new_product_position']=='left'){
					$new_product_sticker_left_right = !empty($new_product_sticker_left_right) ? "left: $new_product_sticker_left_right;" : "";	
				}else {
					$new_product_sticker_left_right = !empty($new_product_sticker_left_right) ? "right: $new_product_sticker_left_right;" : "";	
				}

				$new_product_sticker_image_width = isset($new_product_settings['new_product_sticker_image_width']) && $new_product_settings['new_product_sticker_image_width'] !== '' ? absint($new_product_settings['new_product_sticker_image_width']) . 'px' : '';
				$new_product_sticker_image_height = isset($new_product_settings['new_product_sticker_image_height']) && $new_product_settings['new_product_sticker_image_width'] !== '' ? absint($new_product_settings['new_product_sticker_image_height']) . 'px' : '';
				$new_product_sticker_image_width = !empty($new_product_sticker_image_width) ? "width: $new_product_sticker_image_width;" : "";	
				$new_product_sticker_image_height = !empty($new_product_sticker_image_height) ? "height: $new_product_sticker_image_height;" : "";	

				$new_product_text_padding_top = isset($new_product_settings['new_product_text_padding_top']) && $new_product_settings['new_product_text_padding_top'] !== '' ? absint($new_product_settings['new_product_text_padding_top']) . 'px' : '';
				$new_product_text_padding_right = isset($new_product_settings['new_product_text_padding_right']) && $new_product_settings['new_product_text_padding_right'] !== '' ? absint($new_product_settings['new_product_text_padding_right']) . 'px' : '';
				$new_product_text_padding_bottom = isset($new_product_settings['new_product_text_padding_bottom']) && $new_product_settings['new_product_text_padding_bottom'] !== '' ? absint($new_product_settings['new_product_text_padding_bottom']) . 'px' : '';
				$new_product_text_padding_left = isset($new_product_settings['new_product_text_padding_left']) && $new_product_settings['new_product_text_padding_left'] !== '' ? absint($new_product_settings['new_product_text_padding_left']) . 'px' : '';

				$new_product_text_padding_top = !empty($new_product_text_padding_top) ? "padding-top: $new_product_text_padding_top;" : "";	
				$new_product_text_padding_right = !empty($new_product_text_padding_right) ? "padding-right: $new_product_text_padding_right;" : "";	
				$new_product_text_padding_bottom = !empty($new_product_text_padding_bottom) ? "padding-bottom: $new_product_text_padding_bottom;" : "";	
				$new_product_text_padding_left = !empty($new_product_text_padding_left) ? "padding-left: $new_product_text_padding_left;" : "";
				
				$new_product_sticker_rotate = isset($new_product_settings['new_product_sticker_rotate']) && $new_product_settings['new_product_sticker_rotate'] !== '' ? absint($new_product_settings['new_product_sticker_rotate']) . 'deg' : '';
				$new_product_sticker_rotate = !empty($new_product_sticker_rotate) ? "rotate: $new_product_sticker_rotate;" : "";

				$new_product_sticker_animation_scale = isset($new_product_settings['new_product_sticker_animation_scale']) && $new_product_settings['new_product_sticker_animation_scale'] !== '' ? ($new_product_settings['new_product_sticker_animation_scale']) : '';
				$new_product_sticker_animation_scale = !empty($new_product_sticker_animation_scale) ? "$new_product_sticker_animation_scale" : "1.2";

				$new_product_sticker_animation_rotate = isset($new_product_settings['new_product_sticker_animation_rotate']) && $new_product_settings['new_product_sticker_animation_rotate'] !== '' ? ($new_product_settings['new_product_sticker_animation_rotate']) : '';
				$new_product_sticker_animation_rotate = !empty($new_product_sticker_animation_rotate) ? "$new_product_sticker_animation_rotate" . "deg" : "";

				$new_product_sticker_animation_iteration_count = isset($new_product_settings['new_product_sticker_animation_iteration_count']) && $new_product_settings['new_product_sticker_animation_iteration_count'] !== '' ? ($new_product_settings['new_product_sticker_animation_iteration_count']) : '';
				$new_product_sticker_animation_iteration_count = !empty($new_product_sticker_animation_iteration_count) ? "$new_product_sticker_animation_iteration_count" : "2";

				$new_product_sticker_animation_delay = isset($new_product_settings['new_product_sticker_animation_delay']) && $new_product_settings['new_product_sticker_animation_delay'] !== '' ? ($new_product_settings['new_product_sticker_animation_delay']) : '';
				$new_product_sticker_animation_delay = !empty($new_product_sticker_animation_delay) ? "$new_product_sticker_animation_delay" .'s' : "2s";

				$new_product_sticker_animation_direction = isset($new_product_settings['new_product_sticker_animation_direction']) && $new_product_settings['new_product_sticker_animation_direction'] !== '' ? ($new_product_settings['new_product_sticker_animation_direction']) : '';
				$new_product_sticker_animation_direction = !empty($new_product_sticker_animation_direction) ? "$new_product_sticker_animation_direction" : "";

				$new_product_sticker_animation_type = isset($new_product_settings['new_product_sticker_animation_type']) && $new_product_settings['new_product_sticker_animation_type'] !== '' ? ($new_product_settings['new_product_sticker_animation_type']) : '';
				$new_product_sticker_animation_type = !empty($new_product_sticker_animation_type) ? "$new_product_sticker_animation_type" : "";

				$enable_new_product_schedule_sticker = isset($new_product_settings['enable_new_product_schedule_sticker']) && $new_product_settings['enable_new_product_schedule_sticker'] !== '' ? ($new_product_settings['enable_new_product_schedule_sticker']) : '';

				$new_product_schedule_start_sticker_date_time = isset($new_product_settings['new_product_schedule_start_sticker_date_time']) && $new_product_settings['new_product_schedule_start_sticker_date_time'] !== '' ? ($new_product_settings['new_product_schedule_start_sticker_date_time']) : '';
				$date_start = new DateTime($new_product_schedule_start_sticker_date_time);
				$timestamp_start = $date_start->getTimestamp();

				$new_product_schedule_end_sticker_date_time = isset($new_product_settings['new_product_schedule_end_sticker_date_time']) && $new_product_settings['new_product_schedule_end_sticker_date_time'] !== '' ? ($new_product_settings['new_product_schedule_end_sticker_date_time']) : '';
				$date_end = new DateTime($new_product_schedule_end_sticker_date_time);
				$timestamp_end = $date_end->getTimestamp();

				$current_timestamp = current_time('timestamp');

				$new_product_schedule_sticker_image_width = isset($new_product_settings['new_product_schedule_sticker_image_width']) && $new_product_settings['new_product_schedule_sticker_image_width'] !== '' ? absint($new_product_settings['new_product_schedule_sticker_image_width']) . 'px' : '';
				$new_product_schedule_sticker_image_width = !empty($new_product_schedule_sticker_image_width) ? "width: $new_product_schedule_sticker_image_width;" : "";	

				$new_product_schedule_sticker_image_height = isset($new_product_settings['new_product_schedule_sticker_image_height']) && $new_product_settings['new_product_schedule_sticker_image_height'] !== '' ? absint($new_product_settings['new_product_schedule_sticker_image_height']) . 'px' : '';
				$new_product_schedule_sticker_image_height = !empty($new_product_schedule_sticker_image_height) ? "height: $new_product_schedule_sticker_image_height;" : "";	

				$new_product_schedule_text_padding_top = isset($new_product_settings['new_product_schedule_text_padding_top']) && $new_product_settings['new_product_schedule_text_padding_top'] !== '' ? absint($new_product_settings['new_product_schedule_text_padding_top']) . 'px' : '';
				$new_product_schedule_text_padding_top = !empty($new_product_schedule_text_padding_top) ? "padding-top: $new_product_schedule_text_padding_top;" : "";	

				$new_product_schedule_text_padding_right = isset($new_product_settings['new_product_schedule_text_padding_right']) && $new_product_settings['new_product_schedule_text_padding_right'] !== '' ? absint($new_product_settings['new_product_schedule_text_padding_right']) . 'px' : '';
				$new_product_schedule_text_padding_right = !empty($new_product_schedule_text_padding_right) ? "padding-right: $new_product_schedule_text_padding_right;" : "";	

				$new_product_schedule_text_padding_bottom = isset($new_product_settings['new_product_schedule_text_padding_bottom']) && $new_product_settings['new_product_schedule_text_padding_bottom'] !== '' ? absint($new_product_settings['new_product_schedule_text_padding_bottom']) . 'px' : '';
				$new_product_schedule_text_padding_bottom = !empty($new_product_schedule_text_padding_bottom) ? "padding-bottom: $new_product_schedule_text_padding_bottom;" : "";	

				$new_product_schedule_text_padding_left = isset($new_product_settings['new_product_schedule_text_padding_left']) && $new_product_settings['new_product_schedule_text_padding_left'] !== '' ? absint($new_product_settings['new_product_schedule_text_padding_left']) . 'px' : '';
				$new_product_schedule_text_padding_left = !empty($new_product_schedule_text_padding_left) ? "padding-left: $new_product_schedule_text_padding_left;" : "";				

				if ($enable_new_product_schedule_sticker == "yes" && (($timestamp_start <= $current_timestamp) && ($timestamp_end >= $current_timestamp))) {

					if($new_product_settings['new_product_schedule_sticker_option'] == "text_schedule") {
						if(!empty($new_product_settings['new_product_schedule_custom_text'])){
							$class = "woosticker woosticker_new custom_sticker_text";
							echo '<span class="' . $class . $classPosition . $classTypeSch . '" style="
								background-color:' . esc_attr($new_product_settings["new_product_schedule_custom_text_backcolor"]) . '; 
								color:' . esc_attr($new_product_settings["new_product_schedule_custom_text_fontcolor"]) . ';'
								. $new_product_schedule_text_padding_top 
								. $new_product_schedule_text_padding_right 
								. $new_product_schedule_text_padding_bottom 
								. $new_product_schedule_text_padding_left 
								. $new_product_top 
								. $new_product_sticker_left_right .'">'
								. esc_attr($new_product_settings["new_product_schedule_custom_text"]) .'</span>';
					
						}else{
							$class = (
								($new_product_settings['enable_new_schedule_product_style'] == "ribbon") ?
									($new_product_settings['new_product_position'] == 'left' ?
										" woosticker woosticker_new new_ribbon_left" :
										" woosticker woosticker_new new_ribbon_right") :
									($new_product_settings['new_product_position'] == 'left' ?
										" woosticker woosticker_new new_round_left" :
										" woosticker woosticker_new new_round_right")
							);
							
								echo '<span class="'. $class . $classPosition. '"  style="' 
									. $new_product_top 
									.  $new_product_sticker_left_right 
									. $new_product_schedule_sticker_image_width 
									. $new_product_schedule_sticker_image_height .'">'
									. __ ( 'New', 'woocommerce-new-badge' ) . '</span>';
						}
					
					} else if($new_product_settings['new_product_schedule_sticker_option'] == "image_schedule") {
						if($new_product_settings['new_product_schedule_custom_sticker']!='') {
							$class = "woosticker woosticker_new custom_sticker_image";
							echo '<span class="' . $class . $classPosition . $classType . '" style="
								background-image:url(' . esc_url($new_product_settings['new_product_schedule_custom_sticker']) . '); ' 
								. $new_product_top
								. $new_product_sticker_left_right
								. $new_product_schedule_sticker_image_width
								. $new_product_schedule_sticker_image_height . '"></span>';
						} else {
							$class=(($new_product_settings['new_product_schedule_custom_sticker'] =='') ? 
							(($new_product_settings['enable_new_schedule_product_style'] == "ribbon") ? 
							(($new_product_settings['new_product_position']=='left') ?
								" woosticker woosticker_new new_ribbon_left ":" woosticker woosticker_new new_ribbon_right ") : 
									(($new_product_settings['new_product_position']=='left') ?
										" woosticker woosticker_new new_round_left ":" woosticker woosticker_new new_round_right ")):"woosticker woosticker_new custom_sticker_image");
								echo '<span class="'. $class . $classPosition. '"  style="'
									. $new_product_top 
									.  $new_product_sticker_left_right 
									. $new_product_schedule_sticker_image_width
									. $new_product_schedule_sticker_image_height . '">' 
									. __ ( 'New', 'woocommerce-new-badge' ) . '</span>';
					
						}
					}

				}else{
					if ((time () - (60 * 60 * 24 * $newness)) < $postdatestamp) {
						// If the product was published within the newness time frame display the new badge 
						$animation_name_new = 'new_product_sticker_animation_' . get_the_ID();
						if($new_product_settings['new_product_option'] == "text") {
							if(!empty($new_product_settings['new_product_custom_text'])){
								$class = "woosticker woosticker_new custom_sticker_text";
								echo '<span class="' . $class . $classPosition . $classType . '" style="
									background-color:' . esc_attr($new_product_settings["new_product_custom_text_backcolor"]) . '; 
									color:' . esc_attr($new_product_settings["new_product_custom_text_fontcolor"]) . ';'
									. $new_product_text_padding_top 
									. $new_product_text_padding_right 
									. $new_product_text_padding_bottom 
									. $new_product_text_padding_left 
									. $new_product_top 
									. $new_product_sticker_left_right 
									. $new_product_sticker_rotate 
									. "animation-name: $animation_name_new;"
									. "animation-duration: $new_product_sticker_animation_delay;"
									. "animation-iteration-count: $new_product_sticker_animation_iteration_count;"
									. "animation-direction: $new_product_sticker_animation_direction;" .'">'
									. esc_attr($new_product_settings["new_product_custom_text"]) .'</span>';
	
							}else{
								$class = (
									($new_product_settings['enable_new_product_style'] == "ribbon") ?
										($new_product_settings['new_product_position'] == 'left' ?
											" woosticker woosticker_new new_ribbon_left" :
											" woosticker woosticker_new new_ribbon_right") :
										($new_product_settings['new_product_position'] == 'left' ?
											" woosticker woosticker_new new_round_left" :
											" woosticker woosticker_new new_round_right")
								);
									echo '<span class="'. $class . $classPosition. '"  style="' 
										. $new_product_top 
										.  $new_product_sticker_left_right 
										. $new_product_sticker_image_width 
										. $new_product_sticker_image_height 
										. $new_product_sticker_rotate 
										. "animation-name: $animation_name_new;"
										. "animation-duration: $new_product_sticker_animation_delay;"
										. "animation-iteration-count: $new_product_sticker_animation_iteration_count;"
										. "animation-direction: $new_product_sticker_animation_direction;" .'">'
										. __ ( 'New', 'woocommerce-new-badge' ) . '</span>';
							}
	
						} else if($new_product_settings['new_product_option'] == "image") {
							if($new_product_settings['new_product_custom_sticker']!='') {
								$class = "woosticker woosticker_new custom_sticker_image";
								echo '<span class="' . $class . $classPosition . $classType . '" style="
									background-image:url(' . esc_url($new_product_settings['new_product_custom_sticker']) . '); ' 
									. $new_product_top
									. $new_product_sticker_left_right
									. $new_product_sticker_image_width
									. $new_product_sticker_image_height 
									. $new_product_sticker_rotate 
									. "animation-name: $animation_name_new;"
									. "animation-duration: $new_product_sticker_animation_delay;"
									. "animation-iteration-count: $new_product_sticker_animation_iteration_count;"
									. "animation-direction: $new_product_sticker_animation_direction;" 
									. '"></span>';
							} else {
								$class=(($new_product_settings['new_product_custom_sticker'] =='') ? 
								(($new_product_settings['enable_new_product_style'] == "ribbon") ? 
								(($new_product_settings['new_product_position']=='left') ?
									" woosticker woosticker_new new_ribbon_left ":" woosticker woosticker_new new_ribbon_right ") : 
										(($new_product_settings['new_product_position']=='left') ?
											" woosticker woosticker_new new_round_left ":" woosticker woosticker_new new_round_right ")):"woosticker woosticker_new custom_sticker_image");
									echo '<span class="'. $class . $classPosition. '"  style="'
										. $new_product_top 
										.  $new_product_sticker_left_right 
										. $new_product_sticker_image_width
										. $new_product_sticker_image_height 
										. $new_product_sticker_rotate 
										. "animation-name: $animation_name_new;"
										. "animation-duration: $new_product_sticker_animation_delay;"
										. "animation-iteration-count: $new_product_sticker_animation_iteration_count;"
										. "animation-direction: $new_product_sticker_animation_direction;" . '">' 
										. __ ( 'New', 'woocommerce-new-badge' ) . '</span>';
	
							}
						} else {
							$class=(($new_product_settings['new_product_custom_sticker'] =='') ? 
								(($new_product_settings['enable_new_product_style'] == "ribbon") ? 
								(($new_product_settings['new_product_position']=='left') ?
									" woosticker woosticker_new new_ribbon_left ":" woosticker woosticker_new new_ribbon_right ") : 
										(($new_product_settings['new_product_position']=='left') ?
											" woosticker woosticker_new new_round_left ":" woosticker woosticker_new new_round_right ")):"woosticker woosticker_new custom_sticker_image");
							echo '<span class="'. $class . $classPosition. '"  style="' 
									. $new_product_top 
									.  $new_product_sticker_left_right 
									. $new_product_sticker_rotate 
									. "animation-name: $animation_name_new;"
									. "animation-duration: $new_product_sticker_animation_delay;"
									. "animation-iteration-count: $new_product_sticker_animation_iteration_count;"
									. "animation-direction: $new_product_sticker_animation_direction;".'">' 
									. __ ( 'New', 'woocommerce-new-badge' ) . '</span>';
						}
	
						?>
							<style>
								<?php if($new_product_sticker_animation_type == 'zoominout'){ ?>
									@keyframes <?php echo $animation_name_new; ?> {
										0% {
											transform: scale(<?php echo $new_product_sticker_animation_scale ?>) rotate(0deg) translate(0, 0);
										}
									}
								<?php } elseif($new_product_sticker_animation_type == 'spin'){?>
									@keyframes <?php echo $animation_name_new; ?> {
										100% {
											transform: rotate(360deg) translate(0, 0) ;
										}
									}
								<?php } elseif($new_product_sticker_animation_type == 'swing'){?>
									@keyframes <?php echo $animation_name_new; ?> {
										0% {
											transform: rotate(0deg);
										}
										50% {
											transform: rotate(20deg);
										}                              
										100% {
											transform: rotate(-20deg);
										}
								<?php } elseif($new_product_sticker_animation_type == 'updown'){?>
									@keyframes <?php echo $animation_name_new; ?> {
										0%   {
											top:0px;
										}
										50%  {
											top:50px;
										}
										100%  {
											top:0px;
										}
								<?php } elseif($new_product_sticker_animation_type == 'leftright'){?>
									@keyframes <?php echo $animation_name_new; ?> {
										0%   {
											left:0px;
											right: auto;
										}
										50%  {
											left:200px;
											right: auto;
										}
										100%  {
											left:0px;
											right: auto;
										}
								<?php } ?>
							</style>
						<?php
					}

				}
			}
		}
	}
	

	/**
	 * Function to get sale product badge.
	 *
	 * @return string
	 * @param string $span_class_onsale_sale_woocommerce_span The span class onsale sale woocommerce span.
	 * @param string $post The post.
	 * @param string $product The product.
	 * @author Weblineindia
	 * @since    1.1.8
	 */
	public function get_show_product_sale_badge($span_class_onsale_sale_woocommerce_span, $post, $product ) {

		//Override sticker options
		$sale_product_settings = $this->override_pos_sticker_level_settings( $this->sale_product_settings );

		if ($this->general_settings['enable_sticker'] == "yes" && $sale_product_settings['enable_sale_product_sticker'] == "yes") {

			if((!is_product() && $this->general_settings['enable_sticker_list'] == "yes" ) || (is_product() && $this->general_settings['enable_sticker_detail'] == "yes"))
			{
				global $product;

				$classSalePosition=(($sale_product_settings['sale_product_position']=='left') ? ((is_product())? " pos_left_detail " : " pos_left " ) : ((is_product())? " pos_right_detail " : " pos_right "));				
				
				$classSaleType = (($sale_product_settings['enable_sale_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');
				$classSaleTypeSch = (($sale_product_settings['enable_sale_schedule_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');

				$sale_product_sticker_top = isset($sale_product_settings['sale_product_sticker_top']) && $sale_product_settings['sale_product_sticker_top'] !== '' ? absint($sale_product_settings['sale_product_sticker_top']) . 'px' : '';
				$sale_product_sticker_top = !empty($sale_product_sticker_top) ? "top: $sale_product_sticker_top;" : "";	

				$sale_product_sticker_left_right = isset($sale_product_settings['sale_product_sticker_left_right']) && $sale_product_settings['sale_product_sticker_left_right'] !== '' ? absint($sale_product_settings['sale_product_sticker_left_right']) . 'px' : '';
				if($sale_product_settings['sale_product_position']=='left'){
					$sale_product_sticker_left_right = !empty($sale_product_sticker_left_right) ? "left: $sale_product_sticker_left_right;" : "";	
				}else {
					$sale_product_sticker_left_right = !empty($sale_product_sticker_left_right) ? "right: $sale_product_sticker_left_right;" : "";	
				}

				$sale_product_sticker_image_width = isset($sale_product_settings['sale_product_sticker_image_width']) && $sale_product_settings['sale_product_sticker_image_width'] !== '' ? absint($sale_product_settings['sale_product_sticker_image_width']) . 'px' : '';
				$sale_product_sticker_image_height = isset($sale_product_settings['sale_product_sticker_image_height']) && $sale_product_settings['sale_product_sticker_image_height'] !== '' ? absint($sale_product_settings['sale_product_sticker_image_height']) . 'px' : '';

				$sale_product_sticker_image_width = !empty($sale_product_sticker_image_width) ? "width: $sale_product_sticker_image_width;" : "";	
				$sale_product_sticker_image_height = !empty($sale_product_sticker_image_height) ? "height: $sale_product_sticker_image_height;" : "";	

				$sale_product_text_padding_top = isset($sale_product_settings['sale_product_text_padding_top']) && $sale_product_settings['sale_product_text_padding_top'] !== '' ? absint($sale_product_settings['sale_product_text_padding_top']) . 'px' : '';
				$sale_product_text_padding_right = isset($sale_product_settings['sale_product_text_padding_right']) && $sale_product_settings['sale_product_text_padding_right'] !== '' ? absint($sale_product_settings['sale_product_text_padding_right']) . 'px' : '';
				$sale_product_text_padding_bottom = isset($sale_product_settings['sale_product_text_padding_bottom']) && $sale_product_settings['sale_product_text_padding_bottom'] !== '' ? absint($sale_product_settings['sale_product_text_padding_bottom']) . 'px' : '';
				$sale_product_text_padding_left = isset($sale_product_settings['sale_product_text_padding_left']) && $sale_product_settings['sale_product_text_padding_left'] !== '' ? absint($sale_product_settings['sale_product_text_padding_left']) . 'px' : '';

				$sale_product_text_padding_top = !empty($sale_product_text_padding_top) ? "padding-top: $sale_product_text_padding_top;" : "";	
				$sale_product_text_padding_right = !empty($sale_product_text_padding_right) ? "padding-right: $sale_product_text_padding_right;" : "";	
				$sale_product_text_padding_bottom = !empty($sale_product_text_padding_bottom) ? "padding-bottom: $sale_product_text_padding_bottom;" : "";	
				$sale_product_text_padding_left = !empty($sale_product_text_padding_left) ? "padding-left: $sale_product_text_padding_left;" : "";	

				$sale_product_sticker_rotate = isset($sale_product_settings['sale_product_sticker_rotate']) && $sale_product_settings['sale_product_sticker_rotate'] !== '' ? absint($sale_product_settings['sale_product_sticker_rotate']) . 'deg' : '';
				$sale_product_sticker_rotate = !empty($sale_product_sticker_rotate) ? "rotate: $sale_product_sticker_rotate;" : "";

				$sale_product_sticker_animation_scale = isset($sale_product_settings['sale_product_sticker_animation_scale']) && $sale_product_settings['sale_product_sticker_animation_scale'] !== '' ? ($sale_product_settings['sale_product_sticker_animation_scale']) : '';
				$sale_product_sticker_animation_scale = !empty($sale_product_sticker_animation_scale) ? "$sale_product_sticker_animation_scale" : "1.2";

				$sale_product_sticker_animation_iteration_count = isset($sale_product_settings['sale_product_sticker_animation_iteration_count']) && $sale_product_settings['sale_product_sticker_animation_iteration_count'] !== '' ? ($sale_product_settings['sale_product_sticker_animation_iteration_count']) : '';
				$sale_product_sticker_animation_iteration_count = !empty($sale_product_sticker_animation_iteration_count) ? "$sale_product_sticker_animation_iteration_count" : "2";

				$sale_product_sticker_animation_delay = isset($sale_product_settings['sale_product_sticker_animation_delay']) && $sale_product_settings['sale_product_sticker_animation_delay'] !== '' ? ($sale_product_settings['sale_product_sticker_animation_delay']) : '';
				$sale_product_sticker_animation_delay = !empty($sale_product_sticker_animation_delay) ? "$sale_product_sticker_animation_delay" .'s' : "2s";
				
				$sale_product_sticker_animation_direction = isset($sale_product_settings['sale_product_sticker_animation_direction']) && $sale_product_settings['sale_product_sticker_animation_direction'] !== '' ? ($sale_product_settings['sale_product_sticker_animation_direction']) : '';
				$sale_product_sticker_animation_direction = !empty($sale_product_sticker_animation_direction) ? "$sale_product_sticker_animation_direction" : "";

				$sale_product_sticker_animation_type = isset($sale_product_settings['sale_product_sticker_animation_type']) && $sale_product_settings['sale_product_sticker_animation_type'] !== '' ? ($sale_product_settings['sale_product_sticker_animation_type']) : '';
				$sale_product_sticker_animation_type = !empty($sale_product_sticker_animation_type) ? "$sale_product_sticker_animation_type" : "";
				
				$enable_sale_product_schedule_sticker = isset($sale_product_settings['enable_sale_product_schedule_sticker']) && $sale_product_settings['enable_sale_product_schedule_sticker'] !== '' ? ($sale_product_settings['enable_sale_product_schedule_sticker']) : '';

				$sale_product_schedule_start_sticker_date_time = isset($sale_product_settings['sale_product_schedule_start_sticker_date_time']) && $sale_product_settings['sale_product_schedule_start_sticker_date_time'] !== '' ? ($sale_product_settings['sale_product_schedule_start_sticker_date_time']) : '';
				$date_start = new DateTime($sale_product_schedule_start_sticker_date_time);
				$timestamp_start = $date_start->getTimestamp();

				$sale_product_schedule_end_sticker_date_time = isset($sale_product_settings['sale_product_schedule_end_sticker_date_time']) && $sale_product_settings['sale_product_schedule_end_sticker_date_time'] !== '' ? ($sale_product_settings['sale_product_schedule_end_sticker_date_time']) : '';
				$date_end = new DateTime($sale_product_schedule_end_sticker_date_time);
				$timestamp_end = $date_end->getTimestamp();

				$current_timestamp = current_time('timestamp');

				$sale_product_schedule_sticker_image_width = isset($sale_product_settings['sale_product_schedule_sticker_image_width']) && $sale_product_settings['sale_product_schedule_sticker_image_width'] !== '' ? absint($sale_product_settings['sale_product_schedule_sticker_image_width']) . 'px' : '';
				$sale_product_schedule_sticker_image_width = !empty($sale_product_schedule_sticker_image_width) ? "width: $sale_product_schedule_sticker_image_width;" : "";	

				$sale_product_schedule_sticker_image_height = isset($sale_product_settings['sale_product_schedule_sticker_image_height']) && $sale_product_settings['sale_product_schedule_sticker_image_height'] !== '' ? absint($sale_product_settings['sale_product_schedule_sticker_image_height']) . 'px' : '';
				$sale_product_schedule_sticker_image_height = !empty($sale_product_schedule_sticker_image_height) ? "height: $sale_product_schedule_sticker_image_height;" : "";	

				$sale_product_schedule_text_padding_top = isset($sale_product_settings['sale_product_schedule_text_padding_top']) && $sale_product_settings['sale_product_schedule_text_padding_top'] !== '' ? absint($sale_product_settings['sale_product_schedule_text_padding_top']) . 'px' : '';
				$sale_product_schedule_text_padding_top = !empty($sale_product_schedule_text_padding_top) ? "padding-top: $sale_product_schedule_text_padding_top;" : "";	

				$sale_product_schedule_text_padding_right = isset($sale_product_settings['sale_product_schedule_text_padding_right']) && $sale_product_settings['sale_product_schedule_text_padding_right'] !== '' ? absint($sale_product_settings['sale_product_schedule_text_padding_right']) . 'px' : '';
				$sale_product_schedule_text_padding_right = !empty($sale_product_schedule_text_padding_right) ? "padding-right: $sale_product_schedule_text_padding_right;" : "";	

				$sale_product_schedule_text_padding_bottom = isset($sale_product_settings['sale_product_schedule_text_padding_bottom']) && $sale_product_settings['sale_product_schedule_text_padding_bottom'] !== '' ? absint($sale_product_settings['sale_product_schedule_text_padding_bottom']) . 'px' : '';
				$sale_product_schedule_text_padding_bottom = !empty($sale_product_schedule_text_padding_bottom) ? "padding-bottom: $sale_product_schedule_text_padding_bottom;" : "";	

				$sale_product_schedule_text_padding_left = isset($sale_product_settings['sale_product_schedule_text_padding_left']) && $sale_product_settings['sale_product_schedule_text_padding_left'] !== '' ? absint($sale_product_settings['sale_product_schedule_text_padding_left']) . 'px' : '';
				$sale_product_schedule_text_padding_left = !empty($sale_product_schedule_text_padding_left) ? "padding-left: $sale_product_schedule_text_padding_left;" : "";	
				
				if ($enable_sale_product_schedule_sticker == "yes" && (($timestamp_start <= $current_timestamp) && ($timestamp_end >= $current_timestamp))) {

					if ( $product->is_in_stock ()  && $product->is_on_sale ()) {
						if($sale_product_settings['sale_product_schedule_sticker_option'] == "text_schedule" && !empty($sale_product_settings['sale_product_schedule_custom_text'])) {
	
							$classSale = "woosticker woosticker_sale custom_sticker_text";
	
							$span_class_onsale_sale_woocommerce_span = '<span class="'
									.$classSale . $classSalePosition . $classSaleTypeSch .'" 
									style="
										background-color:' . esc_attr($sale_product_settings["sale_product_schedule_custom_text_backcolor"]) . '; 
										color:' . esc_attr($sale_product_settings["sale_product_schedule_custom_text_fontcolor"]) . ';'
										. $sale_product_schedule_text_padding_top 
										. $sale_product_schedule_text_padding_right 
										. $sale_product_schedule_text_padding_bottom 
										. $sale_product_schedule_text_padding_left 
										. $sale_product_sticker_top 
										. $sale_product_sticker_left_right
										.'">'. esc_attr($sale_product_settings["sale_product_schedule_custom_text"]) 
										.'</span>';
	
						} else if($sale_product_settings['sale_product_schedule_sticker_option'] == "image_schedule") {
							if($sale_product_settings['sale_product_schedule_custom_sticker']!='') {
								
								$classSale = "woosticker woosticker_sale custom_sticker_image";
								$span_class_onsale_sale_woocommerce_span = '<span class="'
									. $classSale . $classSalePosition . $classSaleTypeSch .'" 
									style="
										background-image:url('.esc_url($sale_product_settings['sale_product_schedule_custom_sticker']).'); ' 
										. $sale_product_sticker_top 
										. $sale_product_sticker_left_right 
										. $sale_product_schedule_sticker_image_width 
										. $sale_product_schedule_sticker_image_height 
										.'"></span>';
							} else {
								$classSale = (($sale_product_settings['sale_product_custom_sticker']=='')?(($sale_product_settings['enable_sale_product_style'] == "ribbon") ? (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_ribbon_left ":" woosticker woosticker_sale onsale_ribbon_right ") : (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_round_left ":" woosticker woosticker_sale onsale_round_right ")):"woosticker woosticker_sale custom_sticker_image");
								$span_class_onsale_sale_woocommerce_span =  '<span class="' 
									. $classSale . $classSalePosition . '" 
									style = "' 
										. $sale_product_sticker_top 
										. $sale_product_sticker_left_right 
										. $sale_product_schedule_sticker_image_width 
										. $sale_product_schedule_sticker_image_height 
										. '"> '. __('Sale', 'woo-stickers-by-webline' ) .' </span>';
							}
						} else {
							$classSale = (($sale_product_settings['sale_product_custom_sticker']=='')?(($sale_product_settings['enable_sale_product_style'] == "ribbon") ? (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_ribbon_left ":" woosticker woosticker_sale onsale_ribbon_right ") : (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_round_left ":" woosticker woosticker_sale onsale_round_right ")):"woosticker woosticker_sale custom_sticker_image");
							$span_class_onsale_sale_woocommerce_span =  '<span class="' 
								. $classSale . $classSalePosition . '" 
								style="' 
									. $sale_product_sticker_top 
									.  $sale_product_sticker_left_right 
									.'"> '. __('Sale', 'woo-stickers-by-webline' ) .' </span>';
						}
					}
					else {
						$sold_product_settings = $this->override_sop_sticker_level_settings( $this->sold_product_settings );
						if($sold_product_settings['enable_sold_product_sticker']=="yes") {
							$span_class_onsale_sale_woocommerce_span='';
						}
					}
					
				}else{

					if ( $product->is_in_stock ()  && $product->is_on_sale ()) {
						$animation_name_sale = 'sale_product_sticker_animation_' . get_the_ID();
						if($sale_product_settings['sale_product_option'] == "text") {
	
							if(!empty($sale_product_settings['sale_product_custom_text'])){
								$classSale = "woosticker woosticker_sale custom_sticker_text";
	
								$span_class_onsale_sale_woocommerce_span = '<span class="'
									.$classSale . $classSalePosition . $classSaleType .'" 
									style="
										background-color:' . esc_attr($sale_product_settings["sale_product_custom_text_backcolor"]) . '; 
										color:' . esc_attr($sale_product_settings["sale_product_custom_text_fontcolor"]) . ';'
										. $sale_product_text_padding_top 
										. $sale_product_text_padding_right 
										. $sale_product_text_padding_bottom 
										. $sale_product_text_padding_left 
										. $sale_product_sticker_top 
										. $sale_product_sticker_left_right
										. $sale_product_sticker_rotate
										. "animation-name: $animation_name_sale;"
										. "animation-duration: $sale_product_sticker_animation_delay;"
										. "animation-iteration-count: $sale_product_sticker_animation_iteration_count;"
										. "animation-direction: $sale_product_sticker_animation_direction;"
										.'">'. esc_attr($sale_product_settings["sale_product_custom_text"]) 
										.'</span>';
								
							}else{
								$classSale = (($sale_product_settings['sale_product_custom_sticker']=='')?(($sale_product_settings['enable_sale_product_style'] == "ribbon") ? (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_ribbon_left ":" woosticker woosticker_sale onsale_ribbon_right ") : (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_round_left ":" woosticker woosticker_sale onsale_round_right ")):"woosticker woosticker_sale custom_sticker_image");
								$span_class_onsale_sale_woocommerce_span =  '<span class="' 
									. $classSale . $classSalePosition . '" 
									style = "' 
										. $sale_product_sticker_top 
										. $sale_product_sticker_left_right 
										. $sale_product_sticker_image_width 
										. $sale_product_sticker_image_height 
										. $sale_product_sticker_rotate
										. "animation-name: $animation_name_sale;"
										. "animation-duration: $sale_product_sticker_animation_delay;"
										. "animation-iteration-count: $sale_product_sticker_animation_iteration_count;"
										. "animation-direction: $sale_product_sticker_animation_direction;"
										. '"> '. __('Sale', 'woo-stickers-by-webline' ) .' </span>';
							}
	
						} else if($sale_product_settings['sale_product_option'] == "image") {
							if($sale_product_settings['sale_product_custom_sticker']!='') {
								$classSale = "woosticker woosticker_sale custom_sticker_image";
								$span_class_onsale_sale_woocommerce_span = '<span class="'
									. $classSale . $classSalePosition . $classSaleType .'" 
									style="
										background-image:url('.esc_url($sale_product_settings['sale_product_custom_sticker']).'); ' 
										. $sale_product_sticker_top 
										. $sale_product_sticker_left_right 
										. $sale_product_sticker_image_width 
										. $sale_product_sticker_image_height 
										. $sale_product_sticker_rotate
										. "animation-name: $animation_name_sale;"
										. "animation-duration: $sale_product_sticker_animation_delay;"
										. "animation-iteration-count: $sale_product_sticker_animation_iteration_count;"
										. "animation-direction: $sale_product_sticker_animation_direction;"
										.'"></span>';
							} else {
								$classSale = (($sale_product_settings['sale_product_custom_sticker']=='')?(($sale_product_settings['enable_sale_product_style'] == "ribbon") ? (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_ribbon_left ":" woosticker woosticker_sale onsale_ribbon_right ") : (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_round_left ":" woosticker woosticker_sale onsale_round_right ")):"woosticker woosticker_sale custom_sticker_image");
								$span_class_onsale_sale_woocommerce_span =  '<span class="' 
									. $classSale . $classSalePosition . '" 
									style = "' 
										. $sale_product_sticker_top 
										. $sale_product_sticker_left_right 
										. $sale_product_sticker_image_width 
										. $sale_product_sticker_image_height 
										. $sale_product_sticker_rotate
										. "animation-name: $animation_name_sale;"
										. "animation-duration: $sale_product_sticker_animation_delay;"
										. "animation-iteration-count: $sale_product_sticker_animation_iteration_count;"
										. "animation-direction: $sale_product_sticker_animation_direction;"
										. '"> '. __('Sale', 'woo-stickers-by-webline' ) .' </span>';
							}
						} else {
							$classSale = (($sale_product_settings['sale_product_custom_sticker']=='')?(($sale_product_settings['enable_sale_product_style'] == "ribbon") ? (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_ribbon_left ":" woosticker woosticker_sale onsale_ribbon_right ") : (($sale_product_settings['sale_product_position']=='left')?" woosticker woosticker_sale onsale_round_left ":" woosticker woosticker_sale onsale_round_right ")):"woosticker woosticker_sale custom_sticker_image");
							$span_class_onsale_sale_woocommerce_span =  '<span class="' 
								. $classSale . $classSalePosition . '" 
								style="' 
									. $sale_product_sticker_top 
									.  $sale_product_sticker_left_right 
									. $sale_product_sticker_rotate
									. "animation-name: $animation_name_sale;"
									. "animation-duration: $sale_product_sticker_animation_delay;"
									. "animation-iteration-count: $sale_product_sticker_animation_iteration_count;"
									. "animation-direction: $sale_product_sticker_animation_direction;"
									.'"> '. __('Sale', 'woo-stickers-by-webline' ) .' </span>';
						}
					}
					else {
						$sold_product_settings = $this->override_sop_sticker_level_settings( $this->sold_product_settings );
						if($sold_product_settings['enable_sold_product_sticker']=="yes") {
							$span_class_onsale_sale_woocommerce_span='';
						}
					}
				}
				?>
					<style>
						<?php if($sale_product_sticker_animation_type == 'zoominout'){ ?>
							@keyframes <?php echo $animation_name_sale; ?> {
								0% {
									transform: scale(<?php echo $sale_product_sticker_animation_scale ?>) rotate(0deg) translate(0, 0);
								}
							}
						<?php } elseif($sale_product_sticker_animation_type == 'spin'){?>
							@keyframes <?php echo $animation_name_sale; ?> {
								100% {
									transform: rotate(360deg) translate(0, 0) ;
								}
							}
						<?php } elseif($sale_product_sticker_animation_type == 'swing'){?>
							@keyframes <?php echo $animation_name_sale; ?> {
								0% {
									transform: rotate(0deg);
								}
								50% {
									transform: rotate(20deg);
								}                              
								100% {
									transform: rotate(-20deg);
								}
						<?php } elseif($sale_product_sticker_animation_type == 'updown'){?>
							@keyframes <?php echo $animation_name_sale; ?> {
								0%   {
									top:0px;
								}
								50%  {
									top:50px;
								}
								100%  {
									top:0px;
								}
						<?php } elseif($sale_product_sticker_animation_type == 'leftright'){?>
							@keyframes <?php echo $animation_name_sale; ?> {
								0%   {
									left:0px;
									right: auto;
								}
								50%  {
									left:200px;
									right: auto;
								}
								100%  {
									left:0px;
									right: auto;
								}
						<?php } ?>
					</style>
				<?php
			}
		}
		
		return $span_class_onsale_sale_woocommerce_span;
	}

	/**
	 * Call back function for show sold product badge on list.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 * @since    1.0.0
	 */
	public function show_product_soldout_badge()
	{	 

		//Override sticker options
		$sold_product_settings = $this->override_sop_sticker_level_settings( $this->sold_product_settings );

		$this->sold_out = false;//Initially set as not sold
		if ($this->general_settings['enable_sticker'] == "yes" && $sold_product_settings['enable_sold_product_sticker'] == "yes") {

			if((!is_product() && $this->general_settings['enable_sticker_list'] == "yes" ) || (is_product() && $this->general_settings['enable_sticker_detail'] == "yes"))	{
				
				global $product;
					
				$classSoldPosition=(($sold_product_settings['sold_product_position']=='left') ? ((is_product())? " pos_left_detail " : " pos_left " ) : ((is_product())? " pos_right_detail " : " pos_right "));	
				
				$classSoldType = (($sold_product_settings['enable_sold_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');
				$classSoldTypeSch = (($sold_product_settings['enable_sold_schedule_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');


				// New Changes Start
				$sold_product_sticker_top = isset($sold_product_settings['sold_product_sticker_top']) && $sold_product_settings['sold_product_sticker_top'] !== '' ? absint($sold_product_settings['sold_product_sticker_top']) . 'px' : '';
				$sold_product_sticker_top = !empty($sold_product_sticker_top) ? "top: $sold_product_sticker_top;" : "";	

				$sold_product_sticker_left_right = isset($sold_product_settings['sold_product_sticker_left_right']) && $sold_product_settings['sold_product_sticker_left_right'] !== '' ? absint($sold_product_settings['sold_product_sticker_left_right']) . 'px' : '';
				if($sold_product_settings['sold_product_position']=='left'){
					$sold_product_sticker_left_right = !empty($sold_product_sticker_left_right) ? "left: $sold_product_sticker_left_right;" : "";	
				}else {
					$sold_product_sticker_left_right = !empty($sold_product_sticker_left_right) ? "right: $sold_product_sticker_left_right;" : "";	
				}

				$sold_product_sticker_image_width = isset($sold_product_settings['sold_product_sticker_image_width']) && $sold_product_settings['sold_product_sticker_image_width'] !== '' ? absint($sold_product_settings['sold_product_sticker_image_width']) . 'px' : '';
				$sold_product_sticker_image_height = isset($sold_product_settings['sold_product_sticker_image_height']) && $sold_product_settings['sold_product_sticker_image_height'] !== '' ? absint($sold_product_settings['sold_product_sticker_image_height']) . 'px' : '';

				$sold_product_sticker_image_width = !empty($sold_product_sticker_image_width) ? "width: $sold_product_sticker_image_width;" : "";	
				$sold_product_sticker_image_height = !empty($sold_product_sticker_image_height) ? "height: $sold_product_sticker_image_height;" : "";	

				$sold_product_text_padding_top = isset($sold_product_settings['sold_product_text_padding_top']) && $sold_product_settings['sold_product_text_padding_top'] !== '' ? absint($sold_product_settings['sold_product_text_padding_top']) . 'px' : '';
				$sold_product_text_padding_right = isset($sold_product_settings['sold_product_text_padding_right']) && $sold_product_settings['sold_product_text_padding_right'] !== '' ? absint($sold_product_settings['sold_product_text_padding_right']) . 'px' : '';
				$sold_product_text_padding_bottom = isset($sold_product_settings['sold_product_text_padding_bottom']) && $sold_product_settings['sold_product_text_padding_bottom'] !== '' ? absint($sold_product_settings['sold_product_text_padding_bottom']) . 'px' : '';
				$sold_product_text_padding_left = isset($sold_product_settings['sold_product_text_padding_left']) && $sold_product_settings['sold_product_text_padding_left'] !== '' ? absint($sold_product_settings['sold_product_text_padding_left']) . 'px' : '';

				$sold_product_text_padding_top = !empty($sold_product_text_padding_top) ? "padding-top: $sold_product_text_padding_top;" : "";	
				$sold_product_text_padding_right = !empty($sold_product_text_padding_right) ? "padding-right: $sold_product_text_padding_right;" : "";	
				$sold_product_text_padding_bottom = !empty($sold_product_text_padding_bottom) ? "padding-bottom: $sold_product_text_padding_bottom;" : "";	
				$sold_product_text_padding_left = !empty($sold_product_text_padding_left) ? "padding-left: $sold_product_text_padding_left;" : "";	

				$sold_product_sticker_rotate = isset($sold_product_settings['sold_product_sticker_rotate']) && $sold_product_settings['sold_product_sticker_rotate'] !== '' ? absint($sold_product_settings['sold_product_sticker_rotate']) . 'deg' : '';
				$sold_product_sticker_rotate = !empty($sold_product_sticker_rotate) ? "rotate: $sold_product_sticker_rotate;" : "";

				$sold_product_sticker_animation_scale = isset($sold_product_settings['sold_product_sticker_animation_scale']) && $sold_product_settings['sold_product_sticker_animation_scale'] !== '' ? ($sold_product_settings['sold_product_sticker_animation_scale']) : '';
				$sold_product_sticker_animation_scale = !empty($sold_product_sticker_animation_scale) ? "$sold_product_sticker_animation_scale" : "1.2";

				$sold_product_sticker_animation_rotate = isset($sold_product_settings['sold_product_sticker_animation_rotate']) && $sold_product_settings['sold_product_sticker_animation_rotate'] !== '' ? ($sold_product_settings['sold_product_sticker_animation_rotate']) : '';
				$sold_product_sticker_animation_rotate = !empty($sold_product_sticker_animation_rotate) ? "$sold_product_sticker_animation_rotate" . "deg" : "";

				$sold_product_sticker_animation_iteration_count = isset($sold_product_settings['sold_product_sticker_animation_iteration_count']) && $sold_product_settings['sold_product_sticker_animation_iteration_count'] !== '' ? ($sold_product_settings['sold_product_sticker_animation_iteration_count']) : '';
				$sold_product_sticker_animation_iteration_count = !empty($sold_product_sticker_animation_iteration_count) ? "$sold_product_sticker_animation_iteration_count" : "2";

				$sold_product_sticker_animation_delay = isset($sold_product_settings['sold_product_sticker_animation_delay']) && $sold_product_settings['sold_product_sticker_animation_delay'] !== '' ? ($sold_product_settings['sold_product_sticker_animation_delay']) : '';
				$sold_product_sticker_animation_delay = !empty($sold_product_sticker_animation_delay) ? "$sold_product_sticker_animation_delay" .'s' : "2s";

				$sold_product_sticker_animation_direction = isset($sold_product_settings['sold_product_sticker_animation_direction']) && $sold_product_settings['sold_product_sticker_animation_direction'] !== '' ? ($sold_product_settings['sold_product_sticker_animation_direction']) : '';
				$sold_product_sticker_animation_direction = !empty($sold_product_sticker_animation_direction) ? "$sold_product_sticker_animation_direction" : "";

				$sold_product_sticker_animation_type = isset($sold_product_settings['sold_product_sticker_animation_type']) && $sold_product_settings['sold_product_sticker_animation_type'] !== '' ? ($sold_product_settings['sold_product_sticker_animation_type']) : '';
				$sold_product_sticker_animation_type = !empty($sold_product_sticker_animation_type) ? "$sold_product_sticker_animation_type" : "";

				$enable_sold_product_schedule_sticker = isset($sold_product_settings['enable_sold_product_schedule_sticker']) && $sold_product_settings['enable_sold_product_schedule_sticker'] !== '' ? ($sold_product_settings['enable_sold_product_schedule_sticker']) : '';

				$sold_product_schedule_start_sticker_date_time = isset($sold_product_settings['sold_product_schedule_start_sticker_date_time']) && $sold_product_settings['sold_product_schedule_start_sticker_date_time'] !== '' ? ($sold_product_settings['sold_product_schedule_start_sticker_date_time']) : '';
				$date_start = new DateTime($sold_product_schedule_start_sticker_date_time);
				$timestamp_start = $date_start->getTimestamp();

				$sold_product_schedule_end_sticker_date_time = isset($sold_product_settings['sold_product_schedule_end_sticker_date_time']) && $sold_product_settings['sold_product_schedule_end_sticker_date_time'] !== '' ? ($sold_product_settings['sold_product_schedule_end_sticker_date_time']) : '';
				$date_end = new DateTime($sold_product_schedule_end_sticker_date_time);
				$timestamp_end = $date_end->getTimestamp();

				$current_timestamp = current_time('timestamp');

				$sold_product_schedule_sticker_image_width = isset($sold_product_settings['sold_product_schedule_sticker_image_width']) && $sold_product_settings['sold_product_schedule_sticker_image_width'] !== '' ? absint($sold_product_settings['sold_product_schedule_sticker_image_width']) . 'px' : '';
				$sold_product_schedule_sticker_image_width = !empty($sold_product_schedule_sticker_image_width) ? "width: $sold_product_schedule_sticker_image_width;" : "";	

				$sold_product_schedule_sticker_image_height = isset($sold_product_settings['sold_product_schedule_sticker_image_height']) && $sold_product_settings['sold_product_schedule_sticker_image_height'] !== '' ? absint($sold_product_settings['sold_product_schedule_sticker_image_height']) . 'px' : '';
				$sold_product_schedule_sticker_image_height = !empty($sold_product_schedule_sticker_image_height) ? "height: $sold_product_schedule_sticker_image_height;" : "";	

				$sold_product_schedule_text_padding_top = isset($sold_product_settings['sold_product_schedule_text_padding_top']) && $sold_product_settings['sold_product_schedule_text_padding_top'] !== '' ? absint($sold_product_settings['sold_product_schedule_text_padding_top']) . 'px' : '';
				$sold_product_schedule_text_padding_top = !empty($sold_product_schedule_text_padding_top) ? "padding-top: $sold_product_schedule_text_padding_top;" : "";	

				$sold_product_schedule_text_padding_right = isset($sold_product_settings['sold_product_schedule_text_padding_right']) && $sold_product_settings['sold_product_schedule_text_padding_right'] !== '' ? absint($sold_product_settings['sold_product_schedule_text_padding_right']) . 'px' : '';
				$sold_product_schedule_text_padding_right = !empty($sold_product_schedule_text_padding_right) ? "padding-right: $sold_product_schedule_text_padding_right;" : "";	

				$sold_product_schedule_text_padding_bottom = isset($sold_product_settings['sold_product_schedule_text_padding_bottom']) && $sold_product_settings['sold_product_schedule_text_padding_bottom'] !== '' ? absint($sold_product_settings['sold_product_schedule_text_padding_bottom']) . 'px' : '';
				$sold_product_schedule_text_padding_bottom = !empty($sold_product_schedule_text_padding_bottom) ? "padding-bottom: $sold_product_schedule_text_padding_bottom;" : "";	

				$sold_product_schedule_text_padding_left = isset($sold_product_settings['sold_product_schedule_text_padding_left']) && $sold_product_settings['sold_product_schedule_text_padding_left'] !== '' ? absint($sold_product_settings['sold_product_schedule_text_padding_left']) . 'px' : '';
				$sold_product_schedule_text_padding_left = !empty($sold_product_schedule_text_padding_left) ? "padding-left: $sold_product_schedule_text_padding_left;" : "";
				
				// New Changes End

				if( $product->get_type('product_type') == 'variable' ) {

					$total_qty=0;
					
					$available_variations = $product->get_available_variations();
				   
					foreach ($available_variations as $variation) {

						if($variation['is_in_stock']==true){
							$total_qty++;
						}
						
					}

					if($total_qty==0){
						$animation_name_sold = 'new_product_sticker_animation_' . get_the_ID();
						
						if ($enable_sold_product_schedule_sticker == "yes" && (($timestamp_start <= $current_timestamp) && ($timestamp_end >= $current_timestamp))) {

							if($sold_product_settings['sold_product_schedule_sticker_option'] == "text_schedule" && !empty($sold_product_settings['sold_product_schedule_custom_text'])) { 
						
								$classSold = "woosticker woosticker_sold custom_sticker_text";
								echo '<span class="'
										.$classSold . $classSoldPosition . $classSoldTypeSch .'" 
										style="
											background-color:' . esc_attr($sold_product_settings["sold_product_schedule_custom_text_backcolor"]) . '; 
											color:' . esc_attr($sold_product_settings["sold_product_schedule_custom_text_fontcolor"]) . ';'
											. $sold_product_schedule_text_padding_top 
											. $sold_product_schedule_text_padding_right 
											. $sold_product_schedule_text_padding_bottom 
											. $sold_product_schedule_text_padding_left 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											.'">'. esc_attr($sold_product_settings["sold_product_schedule_custom_text"]) .'</span>';
							
							} else if($sold_product_settings['sold_product_schedule_sticker_option'] == "image_schedule") {
								if($sold_product_settings['sold_product_custom_sticker']!='') {
									$classSold = "woosticker woosticker_sold custom_sticker_image";
									echo '<span class="' 
										. $classSold . $classSoldPosition . $classSoldTypeSch .'" 
										style="
											background-image:url('.esc_url($sold_product_settings['sold_product_custom_sticker']).'); '
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_schedule_sticker_image_width 
											. $sold_product_schedule_sticker_image_height 
											.' "></span>';
								} else {
									$classSold = (($sold_product_settings['sold_product_custom_sticker']=='')?(($sold_product_settings['enable_sold_schedule_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
									echo '<span class="'
										.$classSold . $classSoldPosition .'"
										style="' 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_schedule_sticker_image_width 
											. $sold_product_schedule_sticker_image_height 
											.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
								}
							} else {
							$classSold = (($sold_product_settings['sold_product_custom_sticker']=='')?(($sold_product_settings['enable_sold_schedule_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
							echo '<span class="'
									.$classSold . $classSoldPosition .'" 
									style="' 
										. $sold_product_sticker_top 
										. $sold_product_sticker_left_right 
										. $sold_product_schedule_sticker_image_width 
										. $sold_product_schedule_sticker_image_height 
										.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
							}	
						}																																	
						elseif($sold_product_settings['enable_sold_product_sticker']=="yes") {
							if($sold_product_settings['sold_product_option'] == "text" && !empty($sold_product_settings['sold_product_custom_text'])) { 

								$classSold = "woosticker woosticker_sold custom_sticker_text";
								echo '<span class="'
										.$classSold . $classSoldPosition . $classSoldType .'" 
										style="
											background-color:' . esc_attr($sold_product_settings["sold_product_custom_text_backcolor"]) . '; 
											color:' . esc_attr($sold_product_settings["sold_product_custom_text_fontcolor"]) . ';'
											. $sold_product_text_padding_top 
											. $sold_product_text_padding_right 
											. $sold_product_text_padding_bottom 
											. $sold_product_text_padding_left 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_sticker_rotate 
											. "animation-name: $animation_name_sold;"
											. "animation-duration: $sold_product_sticker_animation_delay;"
											. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
											. "animation-direction: $sold_product_sticker_animation_direction;"
											.'">'. esc_attr($sold_product_settings["sold_product_custom_text"]) .'</span>';

							} else if($sold_product_settings['sold_product_option'] == "image") {
								if($sold_product_settings['sold_product_custom_sticker']!='') {
									$classSold = "woosticker woosticker_sold custom_sticker_image";
									echo '<span class="' 
										. $classSold . $classSoldPosition . $classSoldType .'" 
										style="
											background-image:url('.esc_url($sold_product_settings['sold_product_custom_sticker']).'); '
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_sticker_image_width 
											. $sold_product_sticker_image_height 
											. $sold_product_sticker_rotate 
											. "animation-name: $animation_name_sold;"
											. "animation-duration: $sold_product_sticker_animation_delay;"
											. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
											. "animation-direction: $sold_product_sticker_animation_direction;"
											.' "></span>';
								} else {
									$classSold = (($sold_product_settings['sold_product_custom_sticker']=='')?(($sold_product_settings['enable_sold_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
									echo '<span class="'
										.$classSold . $classSoldPosition .'"
										style="' 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_sticker_image_width 
											. $sold_product_sticker_image_height 
											. $sold_product_sticker_rotate 
											. "animation-name: $animation_name_sold;"
											. "animation-duration: $sold_product_sticker_animation_delay;"
											. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
											. "animation-direction: $sold_product_sticker_animation_direction;"
											.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
								}
							} else {
								$classSold = (($sold_product_settings['sold_product_custom_sticker']=='')?(($sold_product_settings['enable_sold_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
								echo '<span class="'
										.$classSold . $classSoldPosition .'" 
										style="' 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_sticker_image_width 
											. $sold_product_sticker_image_height 
											. $sold_product_sticker_rotate 
											. "animation-name: $animation_name_sold;"
											. "animation-duration: $sold_product_sticker_animation_delay;"
											. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
											. "animation-direction: $sold_product_sticker_animation_direction;"
											.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
							}
							$this->sold_out = true;//Set as SOLD OUT
						}
					}				

				}
				else {

					if (! $product->is_in_stock ()) {
						$animation_name_sold = 'sold_product_sticker_animation_' . get_the_ID();

						if ($enable_sold_product_schedule_sticker == "yes" && (($timestamp_start <= $current_timestamp) && ($timestamp_end >= $current_timestamp))) {

							if($sold_product_settings['sold_product_schedule_sticker_option'] == "text_schedule" && !empty($sold_product_settings['sold_product_schedule_custom_text'])) { 
						
								$classSold = "woosticker woosticker_sold custom_sticker_text";
								echo '<span class="'
										.$classSold . $classSoldPosition . $classSoldTypeSch .'" 
										style="
											background-color:' . esc_attr($sold_product_settings["sold_product_schedule_custom_text_backcolor"]) . '; 
											color:' . esc_attr($sold_product_settings["sold_product_schedule_custom_text_fontcolor"]) . ';'
											. $sold_product_schedule_text_padding_top 
											. $sold_product_schedule_text_padding_right 
											. $sold_product_schedule_text_padding_bottom 
											. $sold_product_schedule_text_padding_left 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											.'">'. esc_attr($sold_product_settings["sold_product_schedule_custom_text"]) .'</span>';
							
							} else if($sold_product_settings['sold_product_schedule_sticker_option'] == "image_schedule") {
								if($sold_product_settings['sold_product_schedule_custom_sticker']!='') {
									$classSold = "woosticker woosticker_sold custom_sticker_image";
									echo '<span class="' 
										. $classSold . $classSoldPosition . $classSoldTypeSch .'" 
										style="
											background-image:url('.esc_url($sold_product_settings['sold_product_schedule_custom_sticker']).'); '
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_schedule_sticker_image_width 
											. $sold_product_schedule_sticker_image_height 
											.' "></span>';
								} else {
									$classSold = (($sold_product_settings['sold_product_schedule_custom_sticker']=='')?(($sold_product_settings['enable_sold_schedule_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
									echo '<span class="'
										.$classSold . $classSoldPosition .'"
										style="' 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_schedule_sticker_image_width 
											. $sold_product_schedule_sticker_image_height 
											.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
								}
							} else {
							$classSold = (($sold_product_settings['sold_product_schedule_custom_sticker']=='')?(($sold_product_settings['enable_sold_schedule_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
							echo '<span class="'
									.$classSold . $classSoldPosition .'" 
									style="' 
										. $sold_product_sticker_top 
										. $sold_product_sticker_left_right 
										. $sold_product_schedule_sticker_image_width 
										. $sold_product_schedule_sticker_image_height 
										.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
							}	
						}	

						elseif($sold_product_settings['enable_sold_product_sticker']=="yes") {
							if($sold_product_settings['sold_product_option'] == "text") {

								if(!empty($sold_product_settings['sold_product_custom_text'])){

									$classSold = "woosticker woosticker_sold custom_sticker_text";		
									echo '<span class="'
										.$classSold . $classSoldPosition . $classSoldType .'" 
										style="
											background-color:' . esc_attr($sold_product_settings["sold_product_custom_text_backcolor"]) . '; 
											color:' . esc_attr($sold_product_settings["sold_product_custom_text_fontcolor"]) . ';'
											. $sold_product_text_padding_top 
											. $sold_product_text_padding_right 
											. $sold_product_text_padding_bottom 
											. $sold_product_text_padding_left 
											. $sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_sticker_rotate 
											. "animation-name: $animation_name_sold;"
											. "animation-duration: $sold_product_sticker_animation_delay;"
											. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
											. "animation-direction: $sold_product_sticker_animation_direction;"
											.'">'. esc_attr($sold_product_settings["sold_product_custom_text"]) .'</span>';

								}else{
									$classSold = (($sold_product_settings['sold_product_custom_sticker']=='')?(($sold_product_settings['enable_sold_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
									echo '<span class="'
											.$classSold . $classSoldPosition .'" 
											style="' 
												. $sold_product_sticker_top
												. $sold_product_sticker_left_right 
												. $sold_product_sticker_image_width 
												. $sold_product_sticker_image_height 
												. $sold_product_sticker_rotate 
												. "animation-name: $animation_name_sold;"
												. "animation-duration: $sold_product_sticker_animation_delay;"
												. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
												. "animation-direction: $sold_product_sticker_animation_direction;"
												.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
								}
							} else if($sold_product_settings['sold_product_option'] == "image") {
								if($sold_product_settings['sold_product_custom_sticker']!='') {
									$classSold = "woosticker woosticker_sold custom_sticker_image";
									echo '<span class="' 
											. $classSold . $classSoldPosition . $classSoldType .'" 
											style="
												background-image:url('.esc_url($sold_product_settings['sold_product_custom_sticker']).'); '
												. $sold_product_sticker_top 
												. $sold_product_sticker_left_right 
												. $sold_product_sticker_image_width 
												. $sold_product_sticker_image_height 
												. $sold_product_sticker_rotate 
												. "animation-name: $animation_name_sold;"
												. "animation-duration: $sold_product_sticker_animation_delay;"
												. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
												. "animation-direction: $sold_product_sticker_animation_direction;"
												.' "></span>';
								} else {
									$classSold = (($sold_product_settings['sold_product_custom_sticker']=='')?(($sold_product_settings['enable_sold_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
									echo '<span class="'
											.$classSold . $classSoldPosition .'" 
											style="' 
												.$sold_product_sticker_top 
												. $sold_product_sticker_left_right 
												. $sold_product_sticker_image_width 
												. $sold_product_sticker_image_height 
												. $sold_product_sticker_rotate 
												. "animation-name: $animation_name_sold;"
												. "animation-duration: $sold_product_sticker_animation_delay;"
												. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
												. "animation-direction: $sold_product_sticker_animation_direction;"
												.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
								}
							} else {
								$classSold = (($sold_product_settings['sold_product_custom_sticker']=='')?(($sold_product_settings['enable_sold_product_style'] == "ribbon") ? (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_ribbon_left ":" woosticker woosticker_sold soldout_ribbon_right ") : (($sold_product_settings['sold_product_position']=='left')?" woosticker woosticker_sold soldout_round_left ":" woosticker woosticker_sold soldout_round_right ")):"woosticker woosticker_sold custom_sticker_image");
								echo '<span class="'
											.$classSold 
											. $classSoldPosition .'" 
											style="' 
											.$sold_product_sticker_top 
											. $sold_product_sticker_left_right 
											. $sold_product_sticker_image_width 
											. $sold_product_sticker_image_height 
											. $sold_product_sticker_rotate 
											. "animation-name: $animation_name_sold;"
											. "animation-duration: $sold_product_sticker_animation_delay;"
											. "animation-iteration-count: $sold_product_sticker_animation_iteration_count;"
											. "animation-direction: $sold_product_sticker_animation_direction;"
											.'">'. __('Sold Out', 'woo-stickers-by-webline' ) .'</span>';
							}

							$this->sold_out = true;//Set as SOLD OUT
						}
					}
				}

				?>
					<style>
						<?php if($sold_product_sticker_animation_type == 'zoominout'){ ?>
							@keyframes <?php echo $animation_name_sold; ?> {
								0% {
									transform: scale(<?php echo $sold_product_sticker_animation_scale ?>) rotate(0deg) translate(0, 0);
								}
							}
						<?php } elseif($sold_product_sticker_animation_type == 'spin'){?>
							@keyframes <?php echo $animation_name_sold; ?> {
								100% {
									transform: rotate(360deg) translate(0, 0) ;
								}
							}
						<?php } elseif($sold_product_sticker_animation_type == 'swing'){?>
							@keyframes <?php echo $animation_name_sold; ?> {
								0% {
									transform: rotate(0deg);
								}
								50% {
									transform: rotate(20deg);
								}                              
								100% {
									transform: rotate(-20deg);
								}
						<?php } elseif($sold_product_sticker_animation_type == 'updown'){?>
							@keyframes <?php echo $animation_name_sold; ?> {
								0%   {
									top:0px;
								}
								50%  {
									top:50px;
								}
								100%  {
									top:0px;
								}
						<?php } elseif($sold_product_sticker_animation_type == 'leftright'){?>
							@keyframes <?php echo $animation_name_sold; ?> {
								0%   {
									left:0px;
									right: auto;
								}
								50%  {
									left:200px;
									right: auto;
								}
								100%  {
									left:0px;
									right: auto;
								}
						<?php } ?>
					</style>
				<?php
			}
		}
	}

	/**
	 * Call back function for show Custom Product Sticker badge.
	 *
	 * @return string
	 * @param string $span_class_onsale_sale_woocommerce_span The span class onsale sale woocommerce span.
	 * @param string $post The post.
	 * @param string $product The product.
	 * @author Weblineindia
	 * @since    1.0.0
	 */
	public function show_product_cust_badge( $span_class_custom_woocommerce_span ) {

		//Override sticker options
		$cust_product_settings = $this->override_cust_sticker_level_settings( $this->cust_product_settings );

		if ($this->general_settings['enable_sticker'] == "yes" && $cust_product_settings['enable_cust_product_sticker'] == "yes") {

			if((!is_product() && $this->general_settings['enable_sticker_list'] == "yes" ) || (is_product() && $this->general_settings['enable_sticker_detail'] == "yes"))
			{
				global $product;

				$classCustomPosition=(($cust_product_settings['cust_product_position']=='left') ? ((is_product())? " pos_left_detail " : " pos_left " ) : ((is_product())? " pos_right_detail " : " pos_right "));
				$classCustomType = (($cust_product_settings['enable_cust_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');	
				$classCustomTypeSch = (($cust_product_settings['enable_cust_schedule_product_style']=='ribbon') ? 'woosticker_ribbon' : 'woosticker_round');	

				$cust_product_sticker_top = isset($cust_product_settings['cust_product_sticker_top']) && $cust_product_settings['cust_product_sticker_top'] !== '' ? absint($cust_product_settings['cust_product_sticker_top']) . 'px' : '';
				$cust_product_sticker_top = !empty($cust_product_sticker_top) ? "top: $cust_product_sticker_top;" : "";	

				$cust_product_sticker_left_right = isset($cust_product_settings['cust_product_sticker_left_right']) && $cust_product_settings['cust_product_sticker_left_right'] !== '' ? absint($cust_product_settings['cust_product_sticker_left_right']) . 'px' : '';
				if($cust_product_settings['cust_product_position']=='left'){
					$cust_product_sticker_left_right = !empty($cust_product_sticker_left_right) ? "left: $cust_product_sticker_left_right;" : "";
				}else {
					$cust_product_sticker_left_right = !empty($cust_product_sticker_left_right) ? "right: $cust_product_sticker_left_right;" : "";
				}

				$cust_product_sticker_image_width = isset($cust_product_settings['cust_product_sticker_image_width']) && $cust_product_settings['cust_product_sticker_image_width'] !== '' ? absint($cust_product_settings['cust_product_sticker_image_width']) . 'px' : '';
				$cust_product_sticker_image_height = isset($cust_product_settings['cust_product_sticker_image_height']) && $cust_product_settings['cust_product_sticker_image_height'] !== '' ? absint($cust_product_settings['cust_product_sticker_image_height']) . 'px' : '';
				$cust_product_sticker_image_width = !empty($cust_product_sticker_image_width) ? "width: $cust_product_sticker_image_width;" : "";
				$cust_product_sticker_image_height = !empty($cust_product_sticker_image_height) ? "height: $cust_product_sticker_image_height;" : "";

				$cust_product_text_padding_top = isset($cust_product_settings['cust_product_text_padding_top']) && $cust_product_settings['cust_product_text_padding_top'] !== '' ? absint($cust_product_settings['cust_product_text_padding_top']) . 'px' : '';
				$cust_product_text_padding_right = isset($cust_product_settings['cust_product_text_padding_right']) && $cust_product_settings['cust_product_text_padding_right'] !== '' ? absint($cust_product_settings['cust_product_text_padding_right']) . 'px' : '';
				$cust_product_text_padding_bottom = isset($cust_product_settings['cust_product_text_padding_bottom']) && $cust_product_settings['cust_product_text_padding_bottom'] !== '' ? absint($cust_product_settings['cust_product_text_padding_bottom']) . 'px' : '';
				$cust_product_text_padding_left = isset($cust_product_settings['cust_product_text_padding_left']) && $cust_product_settings['cust_product_text_padding_left'] !== '' ? absint($cust_product_settings['cust_product_text_padding_left']) . 'px' : '';

				$cust_product_text_padding_top = !empty($cust_product_text_padding_top) ? "padding-top: $cust_product_text_padding_top;" : "";
				$cust_product_text_padding_right = !empty($cust_product_text_padding_right) ? "padding-right: $cust_product_text_padding_right;" : "";
				$cust_product_text_padding_bottom = !empty($cust_product_text_padding_bottom) ? "padding-bottom: $cust_product_text_padding_bottom;" : "";
				$cust_product_text_padding_left = !empty($cust_product_text_padding_left) ? "padding-left: $cust_product_text_padding_left;" : "";

				$cust_product_sticker_rotate = isset($cust_product_settings['cust_product_sticker_rotate']) && $cust_product_settings['cust_product_sticker_rotate'] !== '' ? absint($cust_product_settings['cust_product_sticker_rotate']) . 'deg' : '';
				$cust_product_sticker_rotate = !empty($cust_product_sticker_rotate) ? "rotate: $cust_product_sticker_rotate;" : "";

				$cust_product_sticker_animation_scale = isset($cust_product_settings['cust_product_sticker_animation_scale']) && $cust_product_settings['cust_product_sticker_animation_scale'] !== '' ? ($cust_product_settings['cust_product_sticker_animation_scale']) : '';
				$cust_product_sticker_animation_scale = !empty($cust_product_sticker_animation_scale) ? "$cust_product_sticker_animation_scale" : "1.2";

				$cust_product_sticker_animation_rotate = isset($cust_product_settings['cust_product_sticker_animation_rotate']) && $cust_product_settings['cust_product_sticker_animation_rotate'] !== '' ? ($cust_product_settings['cust_product_sticker_animation_rotate']) : '';
				$cust_product_sticker_animation_rotate = !empty($cust_product_sticker_animation_rotate) ? "$cust_product_sticker_animation_rotate" . "deg" : "";

				$cust_product_sticker_animation_iteration_count = isset($cust_product_settings['cust_product_sticker_animation_iteration_count']) && $cust_product_settings['cust_product_sticker_animation_iteration_count'] !== '' ? ($cust_product_settings['cust_product_sticker_animation_iteration_count']) : '';
				$cust_product_sticker_animation_iteration_count = !empty($cust_product_sticker_animation_iteration_count) ? "$cust_product_sticker_animation_iteration_count" : "2";

				$cust_product_sticker_animation_delay = isset($cust_product_settings['cust_product_sticker_animation_delay']) && $cust_product_settings['cust_product_sticker_animation_delay'] !== '' ? ($cust_product_settings['cust_product_sticker_animation_delay']) : '';
				$cust_product_sticker_animation_delay = !empty($cust_product_sticker_animation_delay) ? "$cust_product_sticker_animation_delay" .'s' : "2s";

				$cust_product_sticker_animation_direction = isset($cust_product_settings['cust_product_sticker_animation_direction']) && $cust_product_settings['cust_product_sticker_animation_direction'] !== '' ? ($cust_product_settings['cust_product_sticker_animation_direction']) : '';
				$cust_product_sticker_animation_direction = !empty($cust_product_sticker_animation_direction) ? "$cust_product_sticker_animation_direction" : "";

				$cust_product_sticker_animation_type = isset($cust_product_settings['cust_product_sticker_animation_type']) && $cust_product_settings['cust_product_sticker_animation_type'] !== '' ? ($cust_product_settings['cust_product_sticker_animation_type']) : '';
				$cust_product_sticker_animation_type = !empty($cust_product_sticker_animation_type) ? "$cust_product_sticker_animation_type" : "";

				$animation_name_custom = 'cust_product_sticker_animation_' . get_the_ID();

				$enable_cust_product_schedule_sticker = isset($cust_product_settings['enable_cust_product_schedule_sticker']) && $cust_product_settings['enable_cust_product_schedule_sticker'] !== '' ? ($cust_product_settings['enable_cust_product_schedule_sticker']) : '';

				$cust_product_schedule_start_sticker_date_time = isset($cust_product_settings['cust_product_schedule_start_sticker_date_time']) && $cust_product_settings['cust_product_schedule_start_sticker_date_time'] !== '' ? ($cust_product_settings['cust_product_schedule_start_sticker_date_time']) : '';
				$date_start = new DateTime($cust_product_schedule_start_sticker_date_time);
				$timestamp_start = $date_start->getTimestamp();

				$cust_product_schedule_end_sticker_date_time = isset($cust_product_settings['cust_product_schedule_end_sticker_date_time']) && $cust_product_settings['cust_product_schedule_end_sticker_date_time'] !== '' ? ($cust_product_settings['cust_product_schedule_end_sticker_date_time']) : '';
				$date_end = new DateTime($cust_product_schedule_end_sticker_date_time);
				$timestamp_end = $date_end->getTimestamp();

				$current_timestamp = current_time('timestamp');

				$cust_product_schedule_sticker_image_width = isset($cust_product_settings['cust_product_schedule_sticker_image_width']) && $cust_product_settings['cust_product_schedule_sticker_image_width'] !== '' ? absint($cust_product_settings['cust_product_schedule_sticker_image_width']) . 'px' : '';
				$cust_product_schedule_sticker_image_width = !empty($cust_product_schedule_sticker_image_width) ? "width: $cust_product_schedule_sticker_image_width;" : "";	

				$cust_product_schedule_sticker_image_height = isset($cust_product_settings['cust_product_schedule_sticker_image_height']) && $cust_product_settings['cust_product_schedule_sticker_image_height'] !== '' ? absint($cust_product_settings['cust_product_schedule_sticker_image_height']) . 'px' : '';
				$cust_product_schedule_sticker_image_height = !empty($cust_product_schedule_sticker_image_height) ? "height: $cust_product_schedule_sticker_image_height;" : "";	

				$cust_product_schedule_text_padding_top = isset($cust_product_settings['cust_product_schedule_text_padding_top']) && $cust_product_settings['cust_product_schedule_text_padding_top'] !== '' ? absint($cust_product_settings['cust_product_schedule_text_padding_top']) . 'px' : '';
				$cust_product_schedule_text_padding_top = !empty($cust_product_schedule_text_padding_top) ? "padding-top: $cust_product_schedule_text_padding_top;" : "";	

				$cust_product_schedule_text_padding_right = isset($cust_product_settings['cust_product_schedule_text_padding_right']) && $cust_product_settings['cust_product_schedule_text_padding_right'] !== '' ? absint($cust_product_settings['cust_product_schedule_text_padding_right']) . 'px' : '';
				$cust_product_schedule_text_padding_right = !empty($cust_product_schedule_text_padding_right) ? "padding-right: $cust_product_schedule_text_padding_right;" : "";	

				$cust_product_schedule_text_padding_bottom = isset($cust_product_settings['cust_product_schedule_text_padding_bottom']) && $cust_product_settings['cust_product_schedule_text_padding_bottom'] !== '' ? absint($cust_product_settings['cust_product_schedule_text_padding_bottom']) . 'px' : '';
				$cust_product_schedule_text_padding_bottom = !empty($cust_product_schedule_text_padding_bottom) ? "padding-bottom: $cust_product_schedule_text_padding_bottom;" : "";	

				$cust_product_schedule_text_padding_left = isset($cust_product_settings['cust_product_schedule_text_padding_left']) && $cust_product_settings['cust_product_schedule_text_padding_left'] !== '' ? absint($cust_product_settings['cust_product_schedule_text_padding_left']) . 'px' : '';
				$cust_product_schedule_text_padding_left = !empty($cust_product_schedule_text_padding_left) ? "padding-left: $cust_product_schedule_text_padding_left;" : "";

				if ($enable_cust_product_schedule_sticker == "yes" && (($timestamp_start <= $current_timestamp) && ($timestamp_end >= $current_timestamp))) {

					if($cust_product_settings['cust_product_schedule_sticker_option'] == "text_schedule" && $cust_product_settings['cust_product_schedule_custom_text']) {
						$classCustom = "woosticker woosticker_custom custom_sticker_text";	
						echo $span_class_custom_woocommerce_span = '<span class="'
																	.$classCustom . $classCustomPosition . $classCustomTypeSch . '" 
																	style="
																		background-color:' . esc_attr($cust_product_settings["cust_product_schedule_custom_text_backcolor"]) . ';
																		color:' . esc_attr($cust_product_settings["cust_product_schedule_custom_text_fontcolor"]) . '; ' 
																		. $cust_product_text_padding_top 
																		. $cust_product_text_padding_right 
																		. $cust_product_text_padding_bottom 
																		. $cust_product_text_padding_left 
																		. $cust_product_sticker_top 
																		. $cust_product_sticker_left_right 
																		.' "> '. esc_attr($cust_product_settings["cust_product_schedule_custom_text"]) .'</span>';
	
					} else if($cust_product_settings['cust_product_schedule_sticker_option'] == "image_schedule") {
						$classCustom = "woosticker woosticker_custom custom_sticker_image";
						echo $span_class_custom_woocommerce_span =  '<span class="' 
																		. $classCustom . $classCustomPosition . $classCustomTypeSch . '" 
																		style="
																			background-image:url('.esc_url($cust_product_settings['cust_product_schedule_custom_sticker']).');'
																			. $cust_product_sticker_top 
																			. $cust_product_sticker_left_right 
																			. $cust_product_sticker_image_width 
																			. $cust_product_sticker_image_height 
																			.'"></span>';
					}

				}
				else{

					if($cust_product_settings['cust_product_option'] == "text" && $cust_product_settings['cust_product_custom_text']) {
						$classCustom = "woosticker woosticker_custom custom_sticker_text";	
						echo $span_class_custom_woocommerce_span = '<span class="'
																	.$classCustom . $classCustomPosition . $classCustomType . '" 
																	style="
																		background-color:' . esc_attr($cust_product_settings["cust_product_custom_text_backcolor"]) . ';
																		color:' . esc_attr($cust_product_settings["cust_product_custom_text_fontcolor"]) . '; ' 
																		. $cust_product_text_padding_top 
																		. $cust_product_text_padding_right 
																		. $cust_product_text_padding_bottom 
																		. $cust_product_text_padding_left 
																		. $cust_product_sticker_top 
																		. $cust_product_sticker_left_right 
																		. $cust_product_sticker_rotate 
																		. "animation-name: $animation_name_custom;"
																		. "animation-duration: $cust_product_sticker_animation_delay;"
																		. "animation-iteration-count: $cust_product_sticker_animation_iteration_count;"
																		. "animation-direction: $cust_product_sticker_animation_direction;"
																		.' "> '. esc_attr($cust_product_settings["cust_product_custom_text"]) .'</span>';
	
					} else if($cust_product_settings['cust_product_option'] == "image") {
						$classCustom = "woosticker woosticker_custom custom_sticker_image";
						echo $span_class_custom_woocommerce_span =  '<span class="' 
																		. $classCustom . $classCustomPosition . $classCustomType . '" 
																		style="
																			background-image:url('.esc_url($cust_product_settings['cust_product_custom_sticker']).');'
																			. $cust_product_sticker_top 
																			. $cust_product_sticker_left_right 
																			. $cust_product_sticker_image_width 
																			. $cust_product_sticker_image_height 
																			. $cust_product_sticker_rotate 
																			. "animation-name: $animation_name_custom;"
																			. "animation-duration: $cust_product_sticker_animation_delay;"
																			. "animation-iteration-count: $cust_product_sticker_animation_iteration_count;"
																			. "animation-direction: $cust_product_sticker_animation_direction;"
																			.'"></span>';
					}

				}

				?>
					<style>
						<?php if($cust_product_sticker_animation_type == 'zoominout'){ ?>
							@keyframes <?php echo $animation_name_custom; ?> {
								0% {
									transform: scale(<?php echo $cust_product_sticker_animation_scale ?>) rotate(0deg) translate(0, 0);
								}
							}
						<?php } elseif($cust_product_sticker_animation_type == 'spin'){?>
							@keyframes <?php echo $animation_name_custom; ?> {
								100% {
									transform: rotate(360deg) translate(0, 0) ;
								}
							}
						<?php } elseif($cust_product_sticker_animation_type == 'swing'){?>
							@keyframes <?php echo $animation_name_custom; ?> {
								0% {
									transform: rotate(0deg);
								}
								50% {
									transform: rotate(20deg);
								}                              
								100% {
									transform: rotate(-20deg);
								}
						<?php } elseif($cust_product_sticker_animation_type == 'updown'){?>
							@keyframes <?php echo $animation_name_custom; ?> {
								0%   {
									top:0px;
								}
								50%  {
									top:50px;
								}
								100%  {
									top:0px;
								}
						<?php } elseif($cust_product_sticker_animation_type == 'leftright'){?>
							@keyframes <?php echo $animation_name_custom; ?> {
								0%   {
									left:0px;
									right: auto;
								}
								50%  {
									left:200px;
									right: auto;
								}
								100%  {
									left:0px;
									right: auto;
								}
						<?php } ?>
					</style>
				<?php
			}
		}
		return $span_class_custom_woocommerce_span;
	}

	/**
	 * Display category badge on bases of sticker settings.
	 *
	 * @author Weblineindia
	 * @since    1.1.5
	 */
	public function show_category_badge( $category ) {

		//Check if category exists and sticker enabled
		if( $this->general_settings['enable_sticker'] == "yes" && !empty( $category->term_id ) ) {

			//Get & category sticker enabled?
			$enable_category_sticker = get_term_meta( $category->term_id, 'enable_category_sticker', true );
			if( $enable_category_sticker == 'yes' ) {

				//Get category options
				$sticker_pos 	= get_term_meta( $category->term_id, 'category_sticker_pos', true );
				$sticker_option = get_term_meta( $category->term_id, 'category_sticker_option', true );
				$category_product_schedule_option = get_term_meta( $category->term_id, 'category_product_schedule_option', true );

				$sticker_text 	= get_term_meta( $category->term_id, 'category_sticker_text', true );
				$category_product_schedule_custom_text 	= get_term_meta( $category->term_id, 'category_product_schedule_custom_text', true );

				$sticker_type 	= get_term_meta( $category->term_id, 'category_sticker_type', true );
				$category_schedule_sticker_type 	= get_term_meta( $category->term_id, 'category_schedule_sticker_type', true );


				$sticker_text_fontcolor = get_term_meta( $category->term_id, 'category_sticker_text_fontcolor', true );
				$category_schedule_product_custom_text_fontcolor = get_term_meta( $category->term_id, 'category_schedule_product_custom_text_fontcolor', true );

				$sticker_text_backcolor = get_term_meta( $category->term_id, 'category_sticker_text_backcolor', true );
				$category_schedule_product_custom_text_backcolor = get_term_meta( $category->term_id, 'category_schedule_product_custom_text_backcolor', true );

				$sticker_image_id = get_term_meta( $category->term_id, 'category_sticker_image_id', true );
				$category_schedule_sticker_custom_id = get_term_meta( $category->term_id, 'category_schedule_sticker_custom_id', true );

				$sticker_image 	  = wp_get_attachment_image_src( $sticker_image_id, 'thumbnail' );
				$sticker_image_sch 	  = wp_get_attachment_image_src( $category_schedule_sticker_custom_id, 'thumbnail' );

				$sticker_class = 'woosticker category_sticker ';
				$sticker_class .= $sticker_pos == 'left' ? 'pos_left ' : 'pos_right ';
				$sticker_class .= $sticker_type == 'ribbon' ? 'woosticker_ribbon ' : 'woosticker_round ';

				$sticker_class_sch = 'woosticker category_sticker ';
				$sticker_class_sch .= $sticker_pos == 'left' ? 'pos_left ' : 'pos_right ';
				$sticker_class_sch .= $category_schedule_sticker_type == 'ribbon' ? 'woosticker_ribbon ' : 'woosticker_round ';

				
				$sticker_top = get_term_meta( $category->term_id, 'category_sticker_top', true );				
				if ( ! empty( $sticker_top ) ) {
					$sticker_top = "top:" . $sticker_top . "px;" ;
				}
				
				$sticker_left_right = get_term_meta( $category->term_id, 'category_sticker_left_right', true );
				if ( ! empty( $sticker_left_right ) ) {
					if($sticker_pos == "left"){
						$sticker_left_right = "left:" . $sticker_left_right . "px;" ;
					}else{
						$sticker_left_right = "right:" . $sticker_left_right . "px;" ;
					}
				}
				
				$sticker_image_width = get_term_meta( $category->term_id, 'category_sticker_image_width', true );
				if ( ! empty( $sticker_image_width ) ) {
					$sticker_image_width = "width:" . $sticker_image_width . "px;" ;
				}

				$sticker_image_height = get_term_meta( $category->term_id, 'category_sticker_image_width', true );
				if ( ! empty( $sticker_image_height ) ) {
					$sticker_image_height = "height:" . $sticker_image_height . "px;" ;
				}
				
				$category_sticker_text_padding_top = get_term_meta( $category->term_id, 'category_sticker_text_padding_top', true );
				if ( ! empty( $category_sticker_text_padding_top ) ) {
					$category_sticker_text_padding_top = "padding-top:" . $category_sticker_text_padding_top . "px;" ;
				}

				$category_sticker_text_padding_right = get_term_meta( $category->term_id, 'category_sticker_text_padding_right', true );
				if ( ! empty( $category_sticker_text_padding_right ) ) {
					$category_sticker_text_padding_right = "padding-right:" . $category_sticker_text_padding_right . "px;" ;
				}
				
				$category_sticker_text_padding_bottom = get_term_meta( $category->term_id, 'category_sticker_text_padding_bottom', true );
				if ( ! empty( $category_sticker_text_padding_bottom ) ) {
					$category_sticker_text_padding_bottom = "padding-bottom:" . $category_sticker_text_padding_bottom . "px;" ;
				}

				$category_sticker_text_padding_left = get_term_meta( $category->term_id, 'category_sticker_text_padding_left', true );

				if ( ! empty( $category_sticker_text_padding_left ) ) {
					$category_sticker_text_padding_left = "padding-left:" . $category_sticker_text_padding_left . "px;" ;
				}

				$category_sticker_sticker_rotate = get_term_meta( $category->term_id, 'category_sticker_sticker_rotate', true );
				$category_sticker_sticker_rotate = isset($category_sticker_sticker_rotate) && $category_sticker_sticker_rotate !== '' ? absint($category_sticker_sticker_rotate) . 'deg' : '';
				$category_sticker_sticker_rotate = !empty($category_sticker_sticker_rotate) ? "rotate: $category_sticker_sticker_rotate;" : "";

				$category_sticker_sticker_category_animation_type = get_term_meta( $category->term_id, 'category_sticker_sticker_category_animation_type', true );
				$category_sticker_sticker_category_animation_type = !empty($category_sticker_sticker_category_animation_type) ? $category_sticker_sticker_category_animation_type : "";

				$category_sticker_sticker_category_animation_scale = get_term_meta( $category->term_id, 'category_sticker_sticker_category_animation_scale', true );
				$category_sticker_sticker_category_animation_scale = isset($category_sticker_sticker_category_animation_scale) && $category_sticker_sticker_category_animation_scale !== '' ? ($category_sticker_sticker_category_animation_scale) : '';
				$category_sticker_sticker_category_animation_scale = !empty($category_sticker_sticker_category_animation_scale) ? "$category_sticker_sticker_category_animation_scale" : "1.2";

				$category_sticker_sticker_category_animation_direction = get_term_meta( $category->term_id, 'category_sticker_sticker_category_animation_direction', true );
				$category_sticker_sticker_category_animation_direction = !empty($category_sticker_sticker_category_animation_direction) ? $category_sticker_sticker_category_animation_direction : "";

				$category_sticker_sticker_category_animation_iteration_count = get_term_meta( $category->term_id, 'category_sticker_sticker_category_animation_iteration_count', true );
				$category_sticker_sticker_category_animation_iteration_count = !empty($category_sticker_sticker_category_animation_iteration_count) ? $category_sticker_sticker_category_animation_iteration_count : "3";

				$category_sticker_sticker_category_animation_type_delay = get_term_meta( $category->term_id, 'category_sticker_sticker_category_animation_type_delay', true );
				$category_sticker_sticker_category_animation_type_delay = isset($category_sticker_sticker_category_animation_type_delay) && $category_sticker_sticker_category_animation_type_delay !== '' ? absint($category_sticker_sticker_category_animation_type_delay).'s' : '2s';

				$category_sticker_animation = 'category_sticker_animation' . $category->term_id;

				$enable_category_product_schedule_sticker_category = get_term_meta( $category->term_id, 'enable_category_product_schedule_sticker_category', true );

				$category_schedule_sticker_image_width = get_term_meta( $category->term_id, 'category_schedule_sticker_image_width', true );
				if ( ! empty( $category_schedule_sticker_image_width ) ) {
					$category_schedule_sticker_image_width = "width:" . $category_schedule_sticker_image_width . "px;" ;
				}

				$category_schedule_sticker_image_height = get_term_meta( $category->term_id, 'category_schedule_sticker_image_height', true );
				if ( ! empty( $category_schedule_sticker_image_height ) ) {
					$category_schedule_sticker_image_height = "height:" . $category_schedule_sticker_image_height . "px;" ;
				}

				$category_product_schedule_custom_text_padding_top = get_term_meta( $category->term_id, 'category_product_schedule_custom_text_padding_top', true );
				if ( ! empty( $category_product_schedule_custom_text_padding_top ) ) {
					$category_product_schedule_custom_text_padding_top = "padding-top:" . $category_product_schedule_custom_text_padding_top . "px;" ;
				}

				$category_product_schedule_custom_text_padding_right = get_term_meta( $category->term_id, 'category_product_schedule_custom_text_padding_right', true );
				if ( ! empty( $category_product_schedule_custom_text_padding_right ) ) {
					$category_product_schedule_custom_text_padding_right = "padding-right:" . $category_product_schedule_custom_text_padding_right . "px;" ;
				}

				$category_product_schedule_custom_text_padding_bottom = get_term_meta( $category->term_id, 'category_product_schedule_custom_text_padding_bottom', true );
				if ( ! empty( $category_product_schedule_custom_text_padding_bottom ) ) {
					$category_product_schedule_custom_text_padding_bottom = "padding-bottom:" . $category_product_schedule_custom_text_padding_bottom . "px;" ;
				}

				$category_product_schedule_custom_text_padding_left = get_term_meta( $category->term_id, 'category_product_schedule_custom_text_padding_left', true );

				if ( ! empty( $category_product_schedule_custom_text_padding_left ) ) {
					$category_product_schedule_custom_text_padding_left = "padding-left:" . $category_product_schedule_custom_text_padding_left . "px;" ;
				}

				$category_product_schedule_start_sticker_date_time = get_term_meta( $category->term_id, 'category_product_schedule_start_sticker_date_time', true );
				$date_start_cat = new DateTime($category_product_schedule_start_sticker_date_time);
				$timestamp_start_cat = $date_start_cat->getTimestamp();


				$category_product_schedule_end_sticker_date_time = get_term_meta( $category->term_id, 'category_product_schedule_end_sticker_date_time', true );
				$date_end_cat = new DateTime($category_product_schedule_end_sticker_date_time);
				$timestamp_end_cat = $date_end_cat->getTimestamp();

				$current_timestamp = current_time('timestamp');

				if ($enable_category_product_schedule_sticker_category == "yes" && (($timestamp_start_cat <= $current_timestamp) && ($timestamp_end_cat >= $current_timestamp))) {

					//Check if sticker text exists
					if( $category_product_schedule_option == 'text_schedule' && !empty( $category_product_schedule_custom_text ) ) {
						echo '<span class="'
								. $sticker_class .'custom_sticker_text" style="background-color:'
								. esc_attr($category_schedule_product_custom_text_backcolor) .'; color:'
								. esc_attr($category_schedule_product_custom_text_fontcolor) .'; '
								. $sticker_top 
								. $sticker_left_right 
								. $category_product_schedule_custom_text_padding_top 
								. $category_product_schedule_custom_text_padding_right 
								. $category_product_schedule_custom_text_padding_bottom 
								. $category_product_schedule_custom_text_padding_left
								.'">'. esc_attr( $category_product_schedule_custom_text ) .'</span>';

					} elseif ( !empty( $sticker_image_sch[0] ) ) {//Check if sticker image exists

						echo '<span class="'
								. $sticker_class .'custom_sticker_image" style="background-image:url('. esc_url($sticker_image_sch[0]) .');'
								. $sticker_top 
								. $category_schedule_sticker_image_width 
								. $category_schedule_sticker_image_height
								. '"></span>';
					}

				}else{
					//Check if sticker text exists
					if( $sticker_option == 'text' && !empty( $sticker_text ) ) {
						echo '<span class="'
								. $sticker_class .'custom_sticker_text" style="background-color:'
								. esc_attr($sticker_text_backcolor) .'; color:'
								. esc_attr($sticker_text_fontcolor) .'; '
								. $sticker_top 
								. $sticker_left_right 
								. $category_sticker_text_padding_top 
								. $category_sticker_text_padding_right 
								. $category_sticker_text_padding_bottom 
								. $category_sticker_text_padding_left
								. $category_sticker_sticker_rotate 
								. "animation-name: $category_sticker_animation;"
								. "animation-duration: $category_sticker_sticker_category_animation_type_delay;"
								. "animation-iteration-count: $category_sticker_sticker_category_animation_iteration_count;"
								. "animation-direction: $category_sticker_sticker_category_animation_direction;"
								.'">'. esc_attr( $sticker_text ) .'</span>';

					} elseif ( !empty( $sticker_image[0] ) ) {//Check if sticker image exists

						echo '<span class="'
								. $sticker_class .'custom_sticker_image" style="background-image:url('. esc_url($sticker_image[0]) .');'
								. $sticker_top 
								. $sticker_image_width 
								. $sticker_image_height
								. $category_sticker_sticker_rotate 
								. "animation-name: $category_sticker_animation;"
								. "animation-duration: $category_sticker_sticker_category_animation_type_delay;"
								. "animation-iteration-count: $category_sticker_sticker_category_animation_iteration_count;"
								. "animation-direction: $category_sticker_sticker_category_animation_direction;"
								. '"></span>';
					}
				}

				?>
					<style>
						<?php if($category_sticker_sticker_category_animation_type == 'zoominout'){ ?>
							@keyframes <?php echo $category_sticker_animation; ?> {
								0% {
									transform: scale(<?php echo $category_sticker_sticker_category_animation_scale ?>) rotate(0deg) translate(0, 0);
								}
							}
						<?php } elseif($category_sticker_sticker_category_animation_type == 'spin'){?>
							@keyframes <?php echo $category_sticker_animation; ?> {
								100% {
									transform: rotate(360deg) translate(0, 0) ;
								}
							}
						<?php } elseif($category_sticker_sticker_category_animation_type == 'swing'){?>
							@keyframes <?php echo $category_sticker_animation; ?> {
								0% {
									transform: rotate(0deg);
								}
								50% {
									transform: rotate(20deg);
								}                              
								100% {
									transform: rotate(-20deg);
								}
						<?php } elseif($category_sticker_sticker_category_animation_type == 'updown'){?>
							@keyframes <?php echo $category_sticker_animation; ?> {
								0%   {
									top:0px;
								}
								50%  {
									top:50px;
								}
								100%  {
									top:0px;
								}
						<?php } elseif($category_sticker_sticker_category_animation_type == 'leftright'){?>
							@keyframes <?php echo $category_sticker_animation; ?> {
								0%   {
									left:0px;
									right: auto;
								}
								50%  {
									left:200px;
									right: auto;
								}
								100%  {
									left:0px;
									right: auto;
								}
						<?php } ?>
					</style>
				<?php
			}
		}
	}

	/**
	 * Load Custom CSS on frontend header
	 *
	 * @author Weblineindia
	 * @since    1.1.5
	 */
	public function load_custom_css() {

		//Check screen where custom css requred
		$display = false;
		if( is_shop() || is_product() || is_product_category() ) $display = true;

		//Check if load custom CSS where needed
		if( apply_filters( 'woosticker_display_custom_css', $display ) ) {
			echo '<style type="text/css">'. apply_filters( 'woosticker_load_custom_css', $this->general_settings['custom_css'] ) .'</style>';
		}
	}

	/**
	 * Call back function for show sale product badge.
	 *
	 * @return string
	 * @param string $span_class_onsale_sale_woocommerce_span The span class onsale sale woocommerce span.
	 * @param string $post The post.
	 * @param string $product The product.
	 * @author Weblineindia
	 * @since    1.0.0
	 */
	public function show_product_sale_badge($span_class_onsale_sale_woocommerce_span, $post, $product ) {
        return $this->get_show_product_sale_badge($span_class_onsale_sale_woocommerce_span, $post, $product);
    }

	/**
	 * Action function to Show sale product badge
	 *
	 * @author Weblineindia
	 * @since    1.1.8
	 */
    public function custom_woocommerce_sale_flash() {
        global $post, $product;
        echo $this->get_show_product_sale_badge('',$post, $product);
    }
}