<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.weblineindia.com
 * @since      1.0.0
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/admin
 * @author     Weblineindia <info@weblineindia.com>
 */
class Woo_Stickers_By_Webline_Admin {

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

	/**
	 * Additional Variables
	 */
	private $general_settings_key = 'general_settings';
	private $new_product_settings_key = 'new_product_settings';
	private $sale_product_settings_key = 'sale_product_settings';
	private $sold_product_settings_key = 'sold_product_settings';
	private $cust_product_settings_key = 'cust_product_settings';
	private $plugin_options_key = 'wli-stickers';
	private $plugin_settings_tabs = array ();
	private $general_settings = array();
	private $new_product_settings = array();
	private $sale_product_settings = array();
	private $sold_product_settings = array();
	private $cust_product_settings = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_settings ();
		$widget_ops = array (
				'classname' => 'wli_woo_stickers',
				'description' => __( "WLI Woocommerce Stickers", 'woo-stickers-by-webline' ) 
		);

		// Add form
		add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ), 11 );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 11 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'sticker_settings_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_sticker_panels' ) );
		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_sticker_option_fields'));
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_sticker_option_fields'));
		add_action( 'woocommerce_process_product_meta_grouped', array( $this, 'save_sticker_option_fields'));
		add_action( 'woocommerce_process_product_meta_external', array( $this, 'save_sticker_option_fields'));

		// Admin footer text.
		add_filter( 'admin_footer_text', array( $this, 'admin_footer' ), 1, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_custom_scripts' ) );

	}
	public function sticker_settings_tabs( $tabs ) {

		$tabs['woo_stickers'] = array(
			'label'    => __( 'Stickers', 'woo-stickers-by-webline' ),
			'target'   => 'woo_stickers_data',
			'class'    => array('show_if_virtual1'),
			'priority' => 99,
		);
		return $tabs;
	}

	public function product_sticker_panels() {

		global $post;

		$premium_access = get_option('wosbw_premium_access_allowed');

		//Get placeholder image
		$placeholder_img = wc_placeholder_img_src();

		//Get new product sticker
		$np_sticker_custom_id = get_post_meta( $post->ID, '_np_sticker_custom_id', true );
		if ( $np_sticker_custom_id ) {
			$np_image = wp_get_attachment_thumb_url( $np_sticker_custom_id );
		} else {
			$np_image = $placeholder_img;
		}

		$np_schedule_sticker_custom_id = get_post_meta( $post->ID, '_np_schedule_sticker_custom_id', true );
		if ( $np_schedule_sticker_custom_id ) {
			$np_schedule_image = wp_get_attachment_thumb_url( $np_schedule_sticker_custom_id );
		} else {
			$np_schedule_image = $placeholder_img;
		}

		//Get on sale product sticker
		$pos_sticker_custom_id = get_post_meta( $post->ID, '_pos_sticker_custom_id', true );
		if ( $pos_sticker_custom_id ) {
			$pos_image = wp_get_attachment_thumb_url( $pos_sticker_custom_id );
		} else {
			$pos_image = $placeholder_img;
		}

		$pos_schedule_sticker_custom_id = get_post_meta( $post->ID, '_pos_schedule_sticker_custom_id', true );
		if ( $pos_schedule_sticker_custom_id ) {
			$pos_schedule_image = wp_get_attachment_thumb_url( $pos_schedule_sticker_custom_id );
		} else {
			$pos_schedule_image = $placeholder_img;
		}

		//Get soldout product sticker
		$sop_sticker_custom_id = get_post_meta( $post->ID, '_sop_sticker_custom_id', true );
		if ( $sop_sticker_custom_id ) {
			$sop_image = wp_get_attachment_thumb_url( $sop_sticker_custom_id );
		} else {
			$sop_image = $placeholder_img;
		}

		$sop_schedule_sticker_custom_id = get_post_meta( $post->ID, '_sop_schedule_sticker_custom_id', true );
		if ( $sop_schedule_sticker_custom_id ) {
			$sop_schedule_image = wp_get_attachment_thumb_url( $sop_schedule_sticker_custom_id );
		} else {
			$sop_schedule_image = $placeholder_img;
		}

		//Get custom sticker for products
		$cust_sticker_custom_id = get_post_meta( $post->ID, '_cust_sticker_custom_id', true );
		if ( $cust_sticker_custom_id ) {
			$cust_image = wp_get_attachment_thumb_url( $cust_sticker_custom_id );
		} else {
			$cust_image = $placeholder_img;
		}
		
		$cust_schedule_sticker_custom_id = get_post_meta( $post->ID, '_cust_schedule_sticker_custom_id', true );
		if ( $cust_schedule_sticker_custom_id ) {
			$cust_schedule_image = wp_get_attachment_thumb_url( $cust_schedule_sticker_custom_id );
		} else {
			$cust_schedule_image = $placeholder_img;
		}

		echo '<div id="woo_stickers_data" class="panel woocommerce_options_panel hidden wsbw-sticker-options-wrap">';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="#wsbw_new_products"><?php _e( "New Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_products_sale"><?php _e( "Products On Sale", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_soldout_products"><?php _e( "Soldout Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_cust_products"><?php _e( "Custom Product Sticker", 'woo-stickers-by-webline' );?></a>
		</h2>
		<div id="wsbw_new_products" class="wsbw_tab_content">
			<?php
			$np_product_option = get_post_meta( $post->ID, '_np_product_option', true ); 
			$np_product_custom_text_fontcolor = get_post_meta( $post->ID, '_np_product_custom_text_fontcolor', true ); 
			$np_product_custom_text_backcolor = get_post_meta( $post->ID, '_np_product_custom_text_backcolor', true ); 
			$np_product_custom_text_padding_top = get_post_meta( $post->ID, '_np_product_custom_text_padding_top', true );
			$np_product_custom_text_padding_right = get_post_meta( $post->ID, '_np_product_custom_text_padding_right', true );
			$np_product_custom_text_padding_bottom = get_post_meta( $post->ID, '_np_product_custom_text_padding_bottom', true );
			$np_product_custom_text_padding_left = get_post_meta( $post->ID, '_np_product_custom_text_padding_left', true );
			
			if($np_product_option == "image" || $np_product_option == "") {
				$wliclass = 'wli_none';
			} else {
				$wliclass = 'wli_block';
			}

			$enable_np_product_schedule_sticker = get_post_meta( $post->ID, '_enable_np_product_schedule_sticker', true );
			$np_product_schedule_start_sticker_date_time = get_post_meta( $post->ID, '_np_product_schedule_start_sticker_date_time', true );
			$np_product_schedule_end_sticker_date_time = get_post_meta( $post->ID, '_np_product_schedule_end_sticker_date_time', true );
			$np_schedule_product_custom_text_fontcolor = get_post_meta( $post->ID, '_np_schedule_product_custom_text_fontcolor', true );
			$np_schedule_product_custom_text_backcolor = get_post_meta( $post->ID, '_np_schedule_product_custom_text_backcolor', true );
			$np_schedule_product_custom_text_padding_top = get_post_meta( $post->ID, '_np_schedule_product_custom_text_padding_top', true );
			$np_product_schedule_custom_text_padding_right = get_post_meta( $post->ID, '_np_product_schedule_custom_text_padding_right', true );
			$np_product_schedule_custom_text_padding_bottom = get_post_meta( $post->ID, '_np_product_schedule_custom_text_padding_bottom', true );
			$np_product_schedule_custom_text_padding_left = get_post_meta( $post->ID, '_np_product_schedule_custom_text_padding_left', true );

			$np_product_schedule_option = get_post_meta( $post->ID, '_np_product_schedule_option', true ); 
			if($np_product_schedule_option == "image_schedule" || $np_product_schedule_option == "") {
				$wliclass = 'wli_none';
			} else {
				$wliclass = 'wli_block';
			}

			$format = 'Y-m-d\TH:i'; 
			$current_timestamp = current_time('timestamp');
			$formatted_date_time = date($format, $current_timestamp);
			
			woocommerce_wp_select( array(
				'id'          => 'enable_np_sticker',
				'value'       => get_post_meta( $post->ID, '_enable_np_sticker', true ),
				'wrapper_class' => '',
				'label'       => __( 'Enable Sticker:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'yes' => __( 'Yes', 'woo-stickers-by-webline' ), 'no' => __( 'No', 'woo-stickers-by-webline' ) ),
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_no_of_days',
				'value'             => get_post_meta( $post->ID, '_np_no_of_days', true ),
				'label'             => __( 'Number of Days:', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify the No of days before to be display product as New, Leave empty or 0 if you want to take from global settings.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_select( array(
				'id'          => 'np_sticker_pos',
				'value'       => get_post_meta( $post->ID, '_np_sticker_pos', true ),
				'wrapper_class' => '',
				'label'       => __( 'Sticker Position:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'left' => __( 'Left', 'woo-stickers-by-webline' ), 'right' => __( 'Right', 'woo-stickers-by-webline' ) ),
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_top',
				'value'             => get_post_meta( $post->ID, 'np_sticker_top', true ),
				'label'             => __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'np_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			if(get_option('wosbw_premium_access_allowed') == 1){

				woocommerce_wp_text_input( array(
					'id'                => 'np_sticker_rotate',
					'value'             => get_post_meta( $post->ID, 'np_sticker_rotate', true ),
					'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true,
					'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
				) );

			}else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<input type="number" class="small-text file-input"  value="<?php echo get_post_meta( $post->ID, 'np_sticker_rotate', true); ?>"  disabled />
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}

			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'np_sticker_animation_type',
					'value'       => get_post_meta( $post->ID, 'np_sticker_animation_type', true ),
					'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
					'description' => __( 'Specify animation type.', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'desc_tip'    => true,
					'options'     => array(
						'none'    => __( 'none', 'woo-stickers-by-webline' ),
						'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
						'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
						'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
						'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
						'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
					)
				) );
			}
			else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Sticker Animation:', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<select class="small-text file-input" disabled>
								<option><?php echo get_post_meta( $post->ID, 'np_sticker_animation_type', true ) ?></option>
							</select>
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}

			if(get_option('wosbw_premium_access_allowed') == 1){
			?>
				<div id="zoominout-options-np-product" style="display: none;">
					<?php
						woocommerce_wp_text_input( array(
							'id'                => 'np_sticker_animation_scale',
							'value'             => get_post_meta( $post->ID, 'np_sticker_animation_scale', true ),
							'label'             => __( '', 'woo-stickers-by-webline' ),
							'class'  	        => 'wsbw-small-text',
							'description'       => __( 'Specify animation scale.', 'woo-stickers-by-webline' ),
							'desc_tip'			=> true,
							'placeholder'       => __( 'Scale', 'woo-stickers-by-webline' )
						) );
					?>
				</div>
			<?php

			woocommerce_wp_select( array(
				'id'                => 'np_sticker_animation_direction',
				'value'             => get_post_meta( $post->ID, 'np_sticker_animation_direction', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation direction', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'options'     => array(
					'normal'    => __( 'Normal', 'woo-stickers-by-webline' ),
					'reverse'    => __( 'Reverse', 'woo-stickers-by-webline' ),
					'alternate'  => __( 'Alternate', 'woo-stickers-by-webline' ),
					'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' )
				)
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_animation_iteration_count',
				'value'             => get_post_meta( $post->ID, 'np_sticker_animation_iteration_count', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'np_sticker_animation_delay', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );
			}

			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'enable_np_product_schedule_sticker',
					'value'         => get_post_meta($post->ID, '_enable_np_product_schedule_sticker', true ) ?: 'no', 
					'wrapper_class' => '',
					'label'       => __( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'options'     => array(
						'yes'    => __( 'Yes', 'woo-stickers-by-webline' ),
						'no'    => __( 'No', 'woo-stickers-by-webline' )
					)
				) );
			}
			else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<select class="small-text file-input" disabled>
								<option><?php echo get_post_meta( $post->ID, '_enable_np_product_schedule_sticker', true ) ?></option>
							</select>
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			if(get_option('wosbw_premium_access_allowed') == 1){
			?>

			<div class="form-field term-thumbnail-wrap">

				<label for="np_product_schedule_start_sticker_date_time"><?php _e( 'Schedule Sticker:', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_start_sticker_date_time" name="np_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($np_product_schedule_start_sticker_date_time) ? $np_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				 />
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify start date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<label for="np_product_schedule_end_sticker_date_time"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_end_sticker_date_time" name="np_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($np_product_schedule_end_sticker_date_time) ? $np_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>" 
				min="<?php echo $formatted_date_time; ?>" />
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify end date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<div class="woo_opt np_product_schedule_option">
					<label for="np_product_schedule_option"><?php _e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-p-schedule" id="image_schedule" value="image_schedule" <?php if($np_product_schedule_option == 'image_schedule' || $np_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $np_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-p-schedule" id="text_schedule" value="text_schedule" <?php if($np_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $np_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="np_product_schedule_option" class="wli_schedule_product_option_product" name="np_product_schedule_option" value="<?php if($np_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $np_product_schedule_option ); } ?>"/>
				</div>
			</div>

			<div class="custom_option custom_optimage_sch">
				<?php

					woocommerce_wp_text_input( array(
						'id'                => 'np_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'np_schedule_sticker_image_width', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );


					woocommerce_wp_text_input( array(
						'id'                => 'np_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'np_schedule_sticker_image_height', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );

				?>
			</div>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($np_product_schedule_option == 'image_schedule' || $np_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $np_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="np_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="np_schedule_sticker_custom_id" value="<?php echo absint( $np_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				

			</div>

			<?php

				woocommerce_wp_text_input( array(
					'id'                => 'np_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_np_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );

				woocommerce_wp_select( array(
					'id'          => 'np_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_np_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );

			?>

			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_np" <?php if($np_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="np_schedule_product_custom_text_fontcolor" value="<?php echo ($np_schedule_product_custom_text_fontcolor) ? esc_attr( $np_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_np"<?php if($np_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_schedule_product_custom_text_backcolor" class="wli_color_picker" name="np_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $np_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($np_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="np_schedule_product_custom_text_padding"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="np_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="np_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $np_schedule_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="np_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="np_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_right ); ?>"/>
					<input type="number" id="np_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="np_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="np_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="np_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<?php } ?>


			<div class="form-field term-thumbnail-wrap">
				<div class="woo_opt np_product_option">
					<label for="np_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($np_product_option == 'image' || $np_product_option == '') { echo "checked"; } ?> <?php checked( $np_product_option, 'image'); ?>/>
					<label for="image" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($np_product_option == 'text') { echo "checked"; } ?> <?php checked( $np_product_option, 'text'); ?>/>
					<label for="text" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="np_product_option" class="wli_product_option" name="np_product_option" value="<?php if($np_product_option == '') { echo "image"; } else { echo esc_attr( $np_product_option ); } ?>"/>
				</div>
			</div>

			

		    <?php
			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'np_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );
			

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'np_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'np_product_custom_text',
				'value'             => get_post_meta( $post->ID, '_np_product_custom_text', true ),
				'wrapper_class' 	=> 'custom_option custom_opttext ' . $wliclass,
				'label'             => __( 'Custom Sticker Text:', 'woo-stickers-by-webline' ),
			) );

			woocommerce_wp_select( array(
				'id'          => 'np_sticker_type',
				'value'       => get_post_meta( $post->ID, '_np_sticker_type', true ),
				'wrapper_class' => 'custom_option custom_opttext ' . $wliclass,
				'class'  	        => 'wsbw-small-text',
				'label'       => __( 'Custom Sticker Type:', 'woo-stickers-by-webline' ),
				'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
			) );

			?>

			<p class="form-field custom_option custom_opttext" <?php if($np_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_product_custom_text_fontcolor" class="wli_color_picker" name="np_product_custom_text_fontcolor" value="<?php echo ($np_product_custom_text_fontcolor) ? esc_attr( $np_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext"<?php if($np_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_product_custom_text_backcolor" class="wli_color_picker" name="np_product_custom_text_backcolor" value="<?php echo esc_attr( $np_product_custom_text_backcolor ); ?>"/>
			</p>

			<p class="form-field custom_option custom_opttext" <?php if($np_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="np_product_custom_text_padding"><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="np_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="np_product_custom_text_padding_top" value="<?php echo esc_attr( $np_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="np_product_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="np_product_custom_text_padding_right" value="<?php echo esc_attr( $np_product_custom_text_padding_right ); ?>"/>
					<input type="number" id="np_product_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="np_product_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="np_product_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="np_product_custom_text_padding_left" value="<?php echo esc_attr( $np_product_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($np_product_option == 'image' || $np_product_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $np_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="np_sticker_custom_id" class="wsbw_upload_img_id" name="np_sticker_custom_id" value="<?php echo absint( $np_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>
		</div>
		<div id="wsbw_products_sale" class="wsbw_tab_content" style="display: none;">
			<?php $pos_product_option = get_post_meta( $post->ID, '_pos_product_option', true ); 
			$pos_product_custom_text_fontcolor = get_post_meta( $post->ID, '_pos_product_custom_text_fontcolor', true ); 
			$pos_product_custom_text_backcolor = get_post_meta( $post->ID, '_pos_product_custom_text_backcolor', true );
			$pos_product_custom_text_padding_top = get_post_meta( $post->ID, '_pos_product_custom_text_padding_top', true );
			$pos_product_custom_text_padding_right = get_post_meta( $post->ID, '_pos_product_custom_text_padding_right', true );
			$pos_product_custom_text_padding_bottom = get_post_meta( $post->ID, '_pos_product_custom_text_padding_bottom', true );
			$pos_product_custom_text_padding_left = get_post_meta( $post->ID, '_pos_product_custom_text_padding_left', true );

			if($pos_product_option == "image" || $pos_product_option == "") {
				$wliclassSale = 'wli_none';
			} else {
				$wliclassSale = 'wli_block';
			}

			$enable_pos_product_schedule_sticker = get_post_meta( $post->ID, '_enable_pos_product_schedule_sticker', true );
			$pos_product_schedule_start_sticker_date_time = get_post_meta( $post->ID, '_pos_product_schedule_start_sticker_date_time', true );
			$pos_product_schedule_end_sticker_date_time = get_post_meta( $post->ID, '_pos_product_schedule_end_sticker_date_time', true );
			$pos_schedule_product_custom_text_fontcolor = get_post_meta( $post->ID, '_pos_schedule_product_custom_text_fontcolor', true );
			$pos_schedule_product_custom_text_backcolor = get_post_meta( $post->ID, '_pos_schedule_product_custom_text_backcolor', true );
			$pos_schedule_product_custom_text_padding_top = get_post_meta( $post->ID, '_pos_schedule_product_custom_text_padding_top', true );
			$pos_product_schedule_custom_text_padding_right = get_post_meta( $post->ID, '_pos_product_schedule_custom_text_padding_right', true );
			$pos_product_schedule_custom_text_padding_bottom = get_post_meta( $post->ID, '_pos_product_schedule_custom_text_padding_bottom', true );
			$pos_product_schedule_custom_text_padding_left = get_post_meta( $post->ID, '_pos_product_schedule_custom_text_padding_left', true );

			$pos_product_schedule_option = get_post_meta( $post->ID, '_pos_product_schedule_option', true ); 
			if($pos_product_schedule_option == "image_schedule" || $pos_product_schedule_option == "") {
				$wliclass = 'wli_none';
			} else {
				$wliclass = 'wli_block';
			}
			
			woocommerce_wp_select( array(
				'id'          => 'enable_pos_sticker',
				'value'       => get_post_meta( $post->ID, '_enable_pos_sticker', true ),
				'wrapper_class' => '',
				'label'       => __( 'Enable Sticker:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'yes' => __( 'Yes', 'woo-stickers-by-webline' ), 'no' => __( 'No', 'woo-stickers-by-webline' ) ),
			) );

			woocommerce_wp_select( array(
				'id'          => 'pos_sticker_pos',
				'value'       => get_post_meta( $post->ID, '_pos_sticker_pos', true ),
				'wrapper_class' => '',
				'label'       => __( 'Sticker Position:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'left' => __( 'Left', 'woo-stickers-by-webline' ), 'right' => __( 'Right', 'woo-stickers-by-webline' ) ),
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_top',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_top', true ),
				'label'             => __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			if(get_option('wosbw_premium_access_allowed') == 1) {
				woocommerce_wp_text_input( array(
					'id'                => 'pos_sticker_rotate',
					'value'             => get_post_meta( $post->ID, 'pos_sticker_rotate', true ),
					'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true,
					'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
				) );
			}else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<input type="number" class="small-text file-input" value="<?php echo get_post_meta( $post->ID, 'pos_sticker_rotate', true ); ?>" disabled />
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			
			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'pos_sticker_animation_type',
					'value'       => get_post_meta( $post->ID, 'pos_sticker_animation_type', true ),
					'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
					'description' => __( 'Specify animation type.', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'desc_tip'    => true,
					'options'     => array(
						'none'    => __( 'none', 'woo-stickers-by-webline' ),
						'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
						'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
						'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
						'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
						'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
					)
				) );
			}else{
			?>
				<div class="custom-p-field">
					<label><?php _e( 'Sticker Animation:', 'woo-stickers-by-webline' ); ?></label>
					<div class="wosbw-pro-ribbon-banner">
					<select class="small-text file-input" disabled>
						<option><?php echo get_post_meta( $post->ID, 'pos_sticker_animation_type', true ) ?></option>
					</select>
						<div class="ribbon">
							<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
								<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
								<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
								<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
								<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
								<defs>
								<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
								<stop stop-color="#FDAB00"/>
								<stop offset="1" stop-color="#CD8F0D"/>
								</linearGradient>
								<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
								<stop stop-color="#FDAB00"/>
								<stop offset="1" stop-color="#CD8F0D"/>
								</linearGradient>
								</defs>
							</svg>
						</div>
						<div class="learn-more">
							<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
						</div>
					</div>
				</div>
			<?php
			}

			if(get_option('wosbw_premium_access_allowed') == 1){

			?>
				<div id="zoominout-options-pos-product" style="display: none;">
					<?php
						woocommerce_wp_text_input( array(
							'id'                => 'pos_sticker_animation_scale',
							'value'             => get_post_meta( $post->ID, 'pos_sticker_animation_scale', true ),
							'label'             => __( '', 'woo-stickers-by-webline' ),
							'class'  	        => 'wsbw-small-text',
							'description'       => __( 'Specify animation scale.', 'woo-stickers-by-webline' ),
							'desc_tip'			=> true,
							'placeholder'       => __( 'Scale', 'woo-stickers-by-webline' )
						) );
					?>
				</div>
			<?php
			
			woocommerce_wp_select( array(
				'id'                => 'pos_sticker_animation_direction',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_animation_direction', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation direction', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'options'     => array(
					'normal'    => __( 'Normal', 'woo-stickers-by-webline' ),
					'reverse'    => __( 'Reverse', 'woo-stickers-by-webline' ),
					'alternate'  => __( 'Alternate', 'woo-stickers-by-webline' ),
					'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' )
				)
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_animation_iteration_count',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_animation_iteration_count', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_animation_delay', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );

			}

			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'enable_pos_product_schedule_sticker',
					'value'         => get_post_meta($post->ID, '_enable_pos_product_schedule_sticker', true ) ?: 'no', 
					'wrapper_class' => '',
					'label'       => __( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'options'     => array(
						'yes'    => __( 'Yes', 'woo-stickers-by-webline' ),
						'no'    => __( 'No', 'woo-stickers-by-webline' )
					)
				) );
			}else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
						<select class="small-text file-input" disabled>
							<option><?php echo get_post_meta( $post->ID, '_enable_pos_product_schedule_sticker', true ) ?></option>
						</select>
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			if(get_option('wosbw_premium_access_allowed') == 1){

			?>

			<div class="form-field term-thumbnail-wrap">

				<label for="pos_product_schedule_start_sticker_date_time"><?php _e( 'Schedule Sticker:', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_start_sticker_date_time" name="pos_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($pos_product_schedule_start_sticker_date_time) ? $pos_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				 />
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify start date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<label for="pos_product_schedule_end_sticker_date_time"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_end_sticker_date_time" name="pos_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($pos_product_schedule_end_sticker_date_time) ? $pos_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>" 
				min="<?php echo $formatted_date_time; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify end date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<div class="woo_opt pos_product_schedule_option">
					<label for="pos_product_schedule_option"><?php _e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-p-schedule" id="image_schedule_pos" value="image_schedule" <?php if($pos_product_schedule_option == 'image_schedule' || $pos_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $pos_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-p-schedule" id="text_schedule_pos" value="text_schedule" <?php if($pos_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $pos_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="pos_product_schedule_option" class="wli_schedule_product_option_product" name="pos_product_schedule_option" value="<?php if($pos_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $pos_product_schedule_option ); } ?>"/>
				</div>
			</div>

			<div class="custom_option custom_optimage_sch">
				<?php

					woocommerce_wp_text_input( array(
						'id'                => 'pos_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'pos_schedule_sticker_image_width', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );


					woocommerce_wp_text_input( array(
						'id'                => 'pos_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'pos_schedule_sticker_image_height', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );

				?>
			</div>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($pos_product_schedule_option == 'image_schedule' || $pos_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $pos_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="pos_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="pos_schedule_sticker_custom_id" value="<?php echo absint( $pos_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_pos"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_pos"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				

			</div>

			<?php

				woocommerce_wp_text_input( array(
					'id'                => 'pos_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_pos_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );

				woocommerce_wp_select( array(
					'id'          => 'pos_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_pos_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );

			?>

			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_pos" <?php if($pos_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_fontcolor" value="<?php echo ($pos_schedule_product_custom_text_fontcolor) ? esc_attr( $pos_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_pos"<?php if($pos_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_schedule_product_custom_text_backcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $pos_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($pos_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="pos_schedule_product_custom_text_padding"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="pos_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="pos_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $pos_schedule_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="pos_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="pos_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_right ); ?>"/>
					<input type="number" id="pos_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="pos_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="pos_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="pos_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
		<?php } ?>


			<div class="form-field term-thumbnail-wrap">
				<div class="woo_opt pos_product_option">
					<label><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="image1" value="image" <?php if($pos_product_option == 'image' || $pos_product_option == '') { echo "checked"; } ?> <?php checked( $pos_product_option, 'image'); ?>/>
					<label for="image1" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="text1" value="text" <?php if($pos_product_option == 'text') { echo "checked"; } ?> <?php checked( $pos_product_option, 'text'); ?>/>
					<label for="text1" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="pos_product_option" class="wli_product_option" name="pos_product_option" value="<?php if($pos_product_option == '') { echo "image"; } else { echo esc_attr( $pos_product_option ); } ?>"/>
				</div>
			</div>
			

		    <?php
			
			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'pos_product_custom_text',
				'value'             => get_post_meta( $post->ID, '_pos_product_custom_text', true ),
				'wrapper_class' => 'custom_option custom_opttext ' . $wliclassSale,
				'label'             => __( 'Custom Sticker Text:', 'woo-stickers-by-webline' ),
			) );

			woocommerce_wp_select( array(
				'id'          => 'pos_sticker_type',
				'value'       => get_post_meta( $post->ID, '_pos_sticker_type', true ),
				'wrapper_class' => 'custom_option custom_opttext ' . $wliclassSale,
				'label'       => __( 'Custom Sticker Type:', 'woo-stickers-by-webline' ),
				'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
			) );

			?>
			<p class="form-field custom_option custom_opttext" <?php if($pos_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_product_custom_text_fontcolor" class="wli_color_picker" name="pos_product_custom_text_fontcolor" value="<?php echo ($pos_product_custom_text_fontcolor) ? esc_attr( $pos_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext" <?php if($pos_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_product_custom_text_backcolor" class="wli_color_picker" name="pos_product_custom_text_backcolor" value="<?php echo esc_attr( $pos_product_custom_text_backcolor ); ?>"/>
			</p>

			<p class="form-field custom_option custom_opttext" <?php if($pos_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_product_custom_text_padding"><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
				<input type="number" id="pos_product_custom_text_padding_top" class="wsbw-small-text" placeholder="Top" name="pos_product_custom_text_padding_top" value="<?php echo esc_attr( $pos_product_custom_text_padding_top ); ?>"/>
				<input type="number" id="pos_product_custom_text_padding_right" class="wsbw-small-text" placeholder="Right" name="pos_product_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_custom_text_padding_right ); ?>"/>
				<input type="number" id="pos_product_custom_text_padding_bottom" class="wsbw-small-text"  placeholder="Bottom" name="pos_product_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_custom_text_padding_bottom ); ?>"/>
				<input type="number" id="pos_product_custom_text_padding_left" class="wsbw-small-text" placeholder="Left" name="pos_product_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_custom_text_padding_left ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($pos_product_option == 'image' || $pos_product_option == '') { echo 'style="display:block"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $pos_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="pos_sticker_custom_id" class="wsbw_upload_img_id" name="pos_sticker_custom_id" value="<?php echo absint( $pos_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>
		</div>
		<div id="wsbw_soldout_products" class="wsbw_tab_content" style="display: none;">
			<?php $sop_product_option = get_post_meta( $post->ID, '_sop_product_option', true ); 
			$sop_product_custom_text_fontcolor = get_post_meta( $post->ID, '_sop_product_custom_text_fontcolor', true ); 
			$sop_product_custom_text_backcolor = get_post_meta( $post->ID, '_sop_product_custom_text_backcolor', true );
			$sop_product_custom_text_padding_top = get_post_meta( $post->ID, '_sop_product_custom_text_padding_top', true );
			$sop_product_custom_text_padding_right = get_post_meta( $post->ID, '_sop_product_custom_text_padding_right', true );
			$sop_product_custom_text_padding_bottom = get_post_meta( $post->ID, '_sop_product_custom_text_padding_bottom', true );
			$sop_product_custom_text_padding_left = get_post_meta( $post->ID, '_sop_product_custom_text_padding_left', true );
			if($sop_product_option == "image" || $sop_product_option == "") {
				$wliclassSold = 'wli_none';
			} else {
				$wliclassSold = 'wli_block';
			}

			$enable_sop_product_schedule_sticker = get_post_meta( $post->ID, '_enable_sop_product_schedule_sticker', true );
			$sop_product_schedule_start_sticker_date_time = get_post_meta( $post->ID, '_sop_product_schedule_start_sticker_date_time', true );
			$sop_product_schedule_end_sticker_date_time = get_post_meta( $post->ID, '_sop_product_schedule_end_sticker_date_time', true );
			$sop_schedule_product_custom_text_fontcolor = get_post_meta( $post->ID, '_sop_schedule_product_custom_text_fontcolor', true );
			$sop_schedule_product_custom_text_backcolor = get_post_meta( $post->ID, '_sop_schedule_product_custom_text_backcolor', true );
			$sop_schedule_product_custom_text_padding_top = get_post_meta( $post->ID, '_sop_schedule_product_custom_text_padding_top', true );
			$sop_product_schedule_custom_text_padding_right = get_post_meta( $post->ID, '_sop_product_schedule_custom_text_padding_right', true );
			$sop_product_schedule_custom_text_padding_bottom = get_post_meta( $post->ID, '_sop_product_schedule_custom_text_padding_bottom', true );
			$sop_product_schedule_custom_text_padding_left = get_post_meta( $post->ID, '_sop_product_schedule_custom_text_padding_left', true );

			$sop_product_schedule_option = get_post_meta( $post->ID, '_sop_product_schedule_option', true ); 
			if($sop_product_schedule_option == "image_schedule" || $sop_product_schedule_option == "") {
				$wliclass = 'wli_none';
			} else {
				$wliclass = 'wli_block';
			}
			
			woocommerce_wp_select( array(
				'id'          => 'enable_sop_sticker',
				'value'       => get_post_meta( $post->ID, '_enable_sop_sticker', true ),
				'wrapper_class' => '',
				'label'       => __( 'Enable Sticker:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'yes' => __( 'Yes', 'woo-stickers-by-webline' ), 'no' => __( 'No', 'woo-stickers-by-webline' ) ),
			) );

			woocommerce_wp_select( array(
				'id'          => 'sop_sticker_pos',
				'value'       => get_post_meta( $post->ID, '_sop_sticker_pos', true ),
				'wrapper_class' => '',
				'label'       => __( 'Sticker Position:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'left' => __( 'Left', 'woo-stickers-by-webline' ), 'right' => __( 'Right', 'woo-stickers-by-webline' ) ),
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_top',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_top', true ),
				'label'             => __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_text_input( array(
					'id'                => 'sop_sticker_rotate',
					'value'             => get_post_meta( $post->ID, 'sop_sticker_rotate', true ),
					'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true,
					'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
				) );
			}else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<input type="number" class="small-text file-input" id="" name="" value="<?php echo get_post_meta( $post->ID, 'sop_sticker_rotate', true ); ?>"  disabled />
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			
			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'sop_sticker_animation_type',
					'value'       => get_post_meta( $post->ID, 'sop_sticker_animation_type', true ),
					'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
					'description' => __( 'Specify animation type.', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'desc_tip'    => true,
					'options'     => array(
						'none'    => __( 'none', 'woo-stickers-by-webline' ),
						'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
						'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
						'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
						'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
						'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
					)
				) );
			}
			else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Sticker Animation:', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
						<select class="small-text file-input" disabled>
							<option><?php echo get_post_meta( $post->ID, 'sop_sticker_animation_type', true ) ?></option>
						</select>
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			if(get_option('wosbw_premium_access_allowed') == 1){
			?>
				<div id="zoominout-options-sop-product" style="display: none;">
					<?php
						woocommerce_wp_text_input( array(
							'id'                => 'sop_sticker_animation_scale',
							'value'             => get_post_meta( $post->ID, 'sop_sticker_animation_scale', true ),
							'label'             => __( '', 'woo-stickers-by-webline' ),
							'class'  	        => 'wsbw-small-text',
							'description'       => __( 'Specify animation scale.', 'woo-stickers-by-webline' ),
							'desc_tip'			=> true,
							'placeholder'       => __( 'Scale', 'woo-stickers-by-webline' )
						) );
					?>
				</div>
			<?php
			
			woocommerce_wp_select( array(
				'id'                => 'sop_sticker_animation_direction',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_animation_direction', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation direction', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'options'     => array(
					'normal'    => __( 'Normal', 'woo-stickers-by-webline' ),
					'reverse'    => __( 'Reverse', 'woo-stickers-by-webline' ),
					'alternate'  => __( 'Alternate', 'woo-stickers-by-webline' ),
					'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' )
				)
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_animation_iteration_count',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_animation_iteration_count', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_animation_delay', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );
			}
			
			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'enable_sop_product_schedule_sticker',
					'value'         => get_post_meta($post->ID, '_enable_sop_product_schedule_sticker', true ) ?: 'no', 
					'wrapper_class' => '',
					'label'       => __( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'options'     => array(
						'yes'    => __( 'Yes', 'woo-stickers-by-webline' ),
						'no'    => __( 'No', 'woo-stickers-by-webline' )
					)
				) );
			}else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
						<select class="small-text file-input" disabled>
							<option><?php echo get_post_meta( $post->ID, '_enable_sop_product_schedule_sticker', true ) ?></option>
						</select>
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			if(get_option('wosbw_premium_access_allowed') == 1){
			
			?>
			
			<div class="form-field term-thumbnail-wrap">
			
				<label for="sop_product_schedule_start_sticker_date_time"><?php _e( 'Schedule Sticker:', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_start_sticker_date_time" name="sop_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($sop_product_schedule_start_sticker_date_time) ? $sop_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify start date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<label for="sop_product_schedule_end_sticker_date_time"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_end_sticker_date_time" name="sop_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($sop_product_schedule_end_sticker_date_time) ? $sop_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>"
				min="<?php echo $formatted_date_time; ?>" />
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify end date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<div class="woo_opt sop_product_schedule_option">
					<label for="sop_product_schedule_option"><?php _e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-p-schedule" id="image_schedule_sop" value="image_schedule" <?php if($sop_product_schedule_option == 'image_schedule' || $sop_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $sop_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-p-schedule" id="text_schedule_sop" value="text_schedule" <?php if($sop_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $sop_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="sop_product_schedule_option" class="wli_schedule_product_option_product" name="sop_product_schedule_option" value="<?php if($sop_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $sop_product_schedule_option ); } ?>"/>
				</div>
			</div>
			
			<div class="custom_option custom_optimage_sch">
				<?php
			
					woocommerce_wp_text_input( array(
						'id'                => 'sop_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'sop_schedule_sticker_image_width', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
			
					woocommerce_wp_text_input( array(
						'id'                => 'sop_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'sop_schedule_sticker_image_height', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
				?>
			</div>
			
			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($sop_product_schedule_option == 'image_schedule' || $sop_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $sop_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="sop_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="sop_schedule_sticker_custom_id" value="<?php echo absint( $sop_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_sop"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_sop"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				
			
			</div>
			
			<?php
			
				woocommerce_wp_text_input( array(
					'id'                => 'sop_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_sop_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );
			
				woocommerce_wp_select( array(
					'id'          => 'sop_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_sop_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );
			
			?>
			
			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_sop" <?php if($sop_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_fontcolor" value="<?php echo ($sop_schedule_product_custom_text_fontcolor) ? esc_attr( $sop_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_sop"<?php if($sop_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_schedule_product_custom_text_backcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $sop_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($sop_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="sop_schedule_product_custom_text_padding"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="sop_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="sop_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $sop_schedule_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="sop_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="sop_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_right ); ?>"/>
					<input type="number" id="sop_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="sop_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="sop_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="sop_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<?php } ?>


			<div class="form-field term-thumbnail-wrap">
				<div class="woo_opt sop_product_option">
					<label for="sop_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="image2" value="image" <?php if($sop_product_option == 'image' || $sop_product_option == '') { echo 'checked="checked"'; } ?> <?php checked( $sop_product_option, 'image'); ?>/>
					<label for="image2" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="text2" value="text" <?php if($sop_product_option == 'text') { echo "checked"; } ?> <?php checked( $sop_product_option, 'text'); ?>/>
					<label for="text2" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="sop_product_option" class="wli_product_option" name="sop_product_option" value="<?php if($sop_product_option == '') { echo "image"; } else { echo esc_attr( $sop_product_option ); } ?>"/>
				</div>
			</div>

		    <?php
			
			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'sop_product_custom_text',
				'value'             => get_post_meta( $post->ID, '_sop_product_custom_text', true ),
				'wrapper_class' 	=> 'custom_option custom_opttext ' . $wliclass,
				'label'             => __( 'Custom Sticker Text:', 'woo-stickers-by-webline' ),
			) );

			woocommerce_wp_select( array(
				'id'          => 'sop_sticker_type',
				'value'       => get_post_meta( $post->ID, '_sop_sticker_type', true ),
				'wrapper_class' => 'custom_option custom_opttext ' . $wliclassSold,
				'label'       => __( 'Custom Sticker Type:', 'woo-stickers-by-webline' ),
				'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
			) );

			?>
			<p class="form-field custom_option custom_opttext" <?php if($sop_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_product_custom_text_fontcolor" class="wli_color_picker" name="sop_product_custom_text_fontcolor" value="<?php echo ($sop_product_custom_text_fontcolor) ? esc_attr( $sop_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext" <?php if($sop_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_product_custom_text_backcolor" class="wli_color_picker" name="sop_product_custom_text_backcolor" value="<?php echo esc_attr( $sop_product_custom_text_backcolor ); ?>"/>
			</p>

			<p class="form-field custom_option custom_opttext" <?php if($sop_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_product_custom_text_padding"><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
				<input type="number" id="sop_product_custom_text_padding_top" class="wsbw-small-text" placeholder="Top" name="sop_product_custom_text_padding_top" value="<?php echo esc_attr( $sop_product_custom_text_padding_top ); ?>"/>
				<input type="number" id="sop_product_custom_text_padding_right" class="wsbw-small-text" placeholder="Right" name="sop_product_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_custom_text_padding_right ); ?>"/>
				<input type="number" id="sop_product_custom_text_padding_bottom" class="wsbw-small-text"  placeholder="Bottom" name="sop_product_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_custom_text_padding_bottom ); ?>"/>
				<input type="number" id="sop_product_custom_text_padding_left" class="wsbw-small-text" placeholder="Left" name="sop_product_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_custom_text_padding_left ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($sop_product_option == 'image' || $sop_product_option == '') { echo 'style="display:block"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $sop_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="sop_sticker_custom_id" class="wsbw_upload_img_id" name="sop_sticker_custom_id" value="<?php echo absint( $sop_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>
		</div>
		<div id="wsbw_cust_products" class="wsbw_tab_content" style="display: none;">
			<?php $cust_product_option = get_post_meta( $post->ID, '_cust_product_option', true ); 
			$cust_product_custom_text_fontcolor = get_post_meta( $post->ID, '_cust_product_custom_text_fontcolor', true ); 
			$cust_product_custom_text_backcolor = get_post_meta( $post->ID, '_cust_product_custom_text_backcolor', true );
			$cust_product_custom_text_padding_top = get_post_meta( $post->ID, '_cust_product_custom_text_padding_top', true );
			$cust_product_custom_text_padding_right = get_post_meta( $post->ID, '_cust_product_custom_text_padding_right', true );
			$cust_product_custom_text_padding_bottom = get_post_meta( $post->ID, '_cust_product_custom_text_padding_bottom', true );
			$cust_product_custom_text_padding_left = get_post_meta( $post->ID, '_cust_product_custom_text_padding_left', true );

			if($cust_product_option == "image" || $cust_product_option == "") {
				$wliclassCustom = 'wli_none';
			} else {
				$wliclassCustom = 'wli_block';
			}

			$enable_cust_product_schedule_sticker = get_post_meta( $post->ID, '_enable_cust_product_schedule_sticker', true );
			$cust_product_schedule_start_sticker_date_time = get_post_meta( $post->ID, '_cust_product_schedule_start_sticker_date_time', true );
			$cust_product_schedule_end_sticker_date_time = get_post_meta( $post->ID, '_cust_product_schedule_end_sticker_date_time', true );
			$cust_schedule_product_custom_text_fontcolor = get_post_meta( $post->ID, '_cust_schedule_product_custom_text_fontcolor', true );
			$cust_schedule_product_custom_text_backcolor = get_post_meta( $post->ID, '_cust_schedule_product_custom_text_backcolor', true );
			$cust_schedule_product_custom_text_padding_top = get_post_meta( $post->ID, '_cust_schedule_product_custom_text_padding_top', true );
			$cust_product_schedule_custom_text_padding_right = get_post_meta( $post->ID, '_cust_product_schedule_custom_text_padding_right', true );
			$cust_product_schedule_custom_text_padding_bottom = get_post_meta( $post->ID, '_cust_product_schedule_custom_text_padding_bottom', true );
			$cust_product_schedule_custom_text_padding_left = get_post_meta( $post->ID, '_cust_product_schedule_custom_text_padding_left', true );

			$cust_product_schedule_option = get_post_meta( $post->ID, '_cust_product_schedule_option', true ); 
			if($cust_product_schedule_option == "image_schedule" || $cust_product_schedule_option == "") {
				$wliclass = 'wli_none';
			} else {
				$wliclass = 'wli_block';
			}
			
			woocommerce_wp_select( array(
				'id'          => 'enable_cust_sticker',
				'value'       => get_post_meta( $post->ID, '_enable_cust_sticker', true ),
				'wrapper_class' => '',
				'label'       => __( 'Enable Custom Sticker:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'yes' => __( 'Yes', 'woo-stickers-by-webline' ), 'no' => __( 'No', 'woo-stickers-by-webline' ) ),
			) );

			woocommerce_wp_select( array(
				'id'          => 'cust_sticker_pos',
				'value'       => get_post_meta( $post->ID, '_cust_sticker_pos', true ),
				'wrapper_class' => '',
				'label'       => __( 'Sticker Position:', 'woo-stickers-by-webline' ),
				'options'     => array( '' => __( 'Default', 'woo-stickers-by-webline' ), 'left' => __( 'Left', 'woo-stickers-by-webline' ), 'right' => __( 'Right', 'woo-stickers-by-webline' ) ),
			) ); 

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_top',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_top', true ),
				'label'             => __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_text_input( array(
					'id'                => 'cust_sticker_rotate',
					'value'             => get_post_meta( $post->ID, 'cust_sticker_rotate', true ),
					'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true,
					'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
				) );
			}else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<input type="number" class="small-text file-input" id="" name="" value="<?php echo get_post_meta( $post->ID, 'cust_sticker_rotate', true ); ?>"  disabled />
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			
			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'cust_sticker_animation_type',
					'value'       => get_post_meta( $post->ID, 'cust_sticker_animation_type', true ),
					'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
					'description' => __( 'Specify animation type.', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'desc_tip'    => true,
					'options'     => array(
						'none'    => __( 'none', 'woo-stickers-by-webline' ),
						'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
						'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
						'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
						'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
						'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
					)
				) );
			}
			else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Sticker Animation:', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<select class="small-text file-input" disabled>
								<option><?php echo get_post_meta( $post->ID, 'cust_sticker_animation_type', true ) ?></option>
							</select>	
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}
			if(get_option('wosbw_premium_access_allowed') == 1){

			?>
				<div id="zoominout-options-cust-product" style="display: none;">
					<?php
						woocommerce_wp_text_input( array(
							'id'                => 'cust_sticker_animation_scale',
							'value'             => get_post_meta( $post->ID, 'cust_sticker_animation_scale', true ),
							'label'             => __( '', 'woo-stickers-by-webline' ),
							'class'  	        => 'wsbw-small-text',
							'description'       => __( 'Specify animation scale.', 'woo-stickers-by-webline' ),
							'desc_tip'			=> true,
							'placeholder'       => __( 'Scale', 'woo-stickers-by-webline' )
						) );
					?>
				</div>
			<?php
			
			woocommerce_wp_select( array(
				'id'                => 'cust_sticker_animation_direction',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_animation_direction', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation direction', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'options'     => array(
					'normal'    => __( 'Normal', 'woo-stickers-by-webline' ),
					'reverse'    => __( 'Reverse', 'woo-stickers-by-webline' ),
					'alternate'  => __( 'Alternate', 'woo-stickers-by-webline' ),
					'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' )
				)
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_animation_iteration_count',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_animation_iteration_count', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_animation_delay', true ),
				'label'             => __( '', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );
			}

			if(get_option('wosbw_premium_access_allowed') == 1){
				woocommerce_wp_select( array(
					'id'          => 'enable_cust_product_schedule_sticker',
					'value'         => get_post_meta($post->ID, '_enable_cust_product_schedule_sticker', true ) ?: 'no', 
					'wrapper_class' => '',
					'label'       => __( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ),
					'class'  	        => 'wsbw-small-text',
					'options'     => array(
						'yes'    => __( 'Yes', 'woo-stickers-by-webline' ),
						'no'    => __( 'No', 'woo-stickers-by-webline' )
					)
				) );
			}else{
				?>
					<div class="custom-p-field">
						<label><?php _e( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ); ?></label>
						<div class="wosbw-pro-ribbon-banner">
							<select class="small-text file-input" disabled>
								<option><?php echo get_post_meta( $post->ID, '_enable_cust_product_schedule_sticker', true ) ?></option>
							</select>
							<div class="ribbon">
								<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
									<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
									<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
									<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
									<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
									<defs>
									<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
									<stop stop-color="#FDAB00"/>
									<stop offset="1" stop-color="#CD8F0D"/>
									</linearGradient>
									</defs>
								</svg>
							</div>
							<div class="learn-more">
								<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
							</div>
						</div>
					</div>
				<?php
			}

			if(get_option('wosbw_premium_access_allowed') == 1){
			?>

			<div class="form-field term-thumbnail-wrap">
			
				<label for="cust_product_schedule_start_sticker_date_time"><?php _e( 'Schedule Sticker:', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_start_sticker_date_time" name="cust_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($cust_product_schedule_start_sticker_date_time) ? $cust_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify start date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<label for="cust_product_schedule_end_sticker_date_time"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_end_sticker_date_time" name="cust_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($cust_product_schedule_end_sticker_date_time) ? $cust_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>"
				min="<?php echo $formatted_date_time; ?>" />
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify end date and time to schedule the sticker.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<div class="woo_opt cust_product_schedule_option">
					<label for="cust_product_schedule_option"><?php _e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-p-schedule" id="image_schedule_cust" value="image_schedule" <?php if($cust_product_schedule_option == 'image_schedule' || $cust_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $cust_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-p-schedule" id="text_schedule_cust" value="text_schedule" <?php if($cust_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $cust_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="cust_product_schedule_option" class="wli_schedule_product_option_product" name="cust_product_schedule_option" value="<?php if($cust_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $cust_product_schedule_option ); } ?>"/>
				</div>
			</div>
			
			<div class="custom_option custom_optimage_sch">
				<?php
			
					woocommerce_wp_text_input( array(
						'id'                => 'cust_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'cust_schedule_sticker_image_width', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
			
					woocommerce_wp_text_input( array(
						'id'                => 'cust_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'cust_schedule_sticker_image_height', true ),
						'label'             => __( '', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
				?>
			</div>
			
			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($cust_product_schedule_option == 'image_schedule' || $cust_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $cust_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="cust_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="cust_schedule_sticker_custom_id" value="<?php echo absint( $cust_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cust"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cust"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				
			
			</div>
			
			<?php
			
				woocommerce_wp_text_input( array(
					'id'                => 'cust_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_cust_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );
			
				woocommerce_wp_select( array(
					'id'          => 'cust_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_cust_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( '', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );
			
			?>
			
			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_cust" <?php if($cust_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_fontcolor" value="<?php echo ($cust_schedule_product_custom_text_fontcolor) ? esc_attr( $cust_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_cust"<?php if($cust_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_schedule_product_custom_text_backcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $cust_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($cust_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="cust_schedule_product_custom_text_padding"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="cust_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="cust_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $cust_schedule_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="cust_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="cust_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_right ); ?>"/>
					<input type="number" id="cust_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="cust_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="cust_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="cust_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<?php } ?>
		
		    <div class="form-field term-thumbnail-wrap">
				<div class="woo_opt cust_product_option">
					<label for="cust_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="image3" value="image" <?php if($cust_product_option == 'image' || $cust_product_option == '') { echo "checked"; } ?> <?php checked( $cust_product_option, 'image'); ?>/>
					<label for="image3" class="radio-label"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="text3" value="text" <?php if($cust_product_option == 'text') { echo "checked"; } ?> <?php checked( $cust_product_option, 'text'); ?>/>
					<label for="text3" class="radio-label"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="cust_product_option" class="wli_product_option" name="cust_product_option" value="<?php if($cust_product_option == '') { echo "image"; } else { echo esc_attr( $cust_product_option ); } ?>"/>
				</div>
			</div>

		    <?php 

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );	
			
			woocommerce_wp_text_input( array(
				'id'                => 'cust_product_custom_text',
				'value'             => get_post_meta( $post->ID, '_cust_product_custom_text', true ),
				'wrapper_class' => 'custom_option custom_opttext ' . $wliclassCustom,
				'label'             => __( 'Custom Sticker Text:', 'woo-stickers-by-webline' ),
			) );

			woocommerce_wp_select( array(
				'id'          => 'cust_sticker_type',
				'value'       => get_post_meta( $post->ID, '_cust_sticker_type', true ),
				'wrapper_class' => 'custom_option custom_opttext ' . $wliclassCustom,
				'label'       => __( 'Custom Sticker Type:', 'woo-stickers-by-webline' ),
				'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
			) );

			?>
			<p class="form-field custom_option custom_opttext" <?php if($cust_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="cust_product_custom_text_fontcolor" value="<?php echo ($cust_product_custom_text_fontcolor) ? esc_attr( $cust_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext" <?php if($cust_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="cust_product_custom_text_backcolor" value="<?php echo esc_attr( $cust_product_custom_text_backcolor ); ?>"/>
			</p>
			
			<p class="form-field custom_option custom_opttext" <?php if($cust_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_product_custom_text_padding"><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
				<input type="number" id="cust_product_custom_text_padding_top" class="wsbw-small-text" placeholder="Top" name="cust_product_custom_text_padding_top" value="<?php echo esc_attr( $cust_product_custom_text_padding_top ); ?>"/>
				<input type="number" id="cust_product_custom_text_padding_right" class="wsbw-small-text" placeholder="Right" name="cust_product_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_custom_text_padding_right ); ?>"/>
				<input type="number" id="cust_product_custom_text_padding_bottom" class="wsbw-small-text"  placeholder="Bottom" name="cust_product_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_custom_text_padding_bottom ); ?>"/>
				<input type="number" id="cust_product_custom_text_padding_left" class="wsbw-small-text" placeholder="Left" name="cust_product_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_custom_text_padding_left ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($cust_product_option == 'image' || $cust_product_option == '') { echo 'style="display:block"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $cust_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="cust_sticker_custom_id" class="wsbw_upload_img_id" name="cust_sticker_custom_id" value="<?php echo absint( $cust_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>
		</div>
		<?php
		echo '</div>';
	}

	/**
	 * Save the custom fields.
	 */
	function save_sticker_option_fields( $post_id ) {

		//Save all new product options
		$enable_np_sticker = isset( $_POST['enable_np_sticker'] ) ? sanitize_text_field( $_POST['enable_np_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_np_sticker', $enable_np_sticker );
		$np_no_of_days = isset( $_POST['np_no_of_days'] ) ? absint( $_POST['np_no_of_days'] ) : '';
		update_post_meta( $post_id, '_np_no_of_days', $np_no_of_days );
		$np_sticker_pos = isset( $_POST['np_sticker_pos'] ) ? sanitize_text_field( $_POST['np_sticker_pos'] ) : '';
		update_post_meta( $post_id, '_np_sticker_pos', $np_sticker_pos );

		$np_sticker_left_right = isset( $_POST['np_sticker_left_right'] ) ? sanitize_text_field( $_POST['np_sticker_left_right'] ) : '';
		update_post_meta( $post_id, 'np_sticker_left_right', $np_sticker_left_right );
		$np_sticker_top = isset( $_POST['np_sticker_top'] ) ? sanitize_text_field( $_POST['np_sticker_top'] ) : '';
		update_post_meta( $post_id, 'np_sticker_top', $np_sticker_top );

		$np_product_option = isset( $_POST['np_product_option'] ) ? sanitize_key( $_POST['np_product_option'] ) : '';
		update_post_meta( $post_id, '_np_product_option', $np_product_option );

		$np_sticker_image_width = isset( $_POST['np_sticker_image_width'] ) ? sanitize_text_field( $_POST['np_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'np_sticker_image_width', $np_sticker_image_width );
		$np_sticker_image_height = isset( $_POST['np_sticker_image_height'] ) ? sanitize_text_field( $_POST['np_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'np_sticker_image_height', $np_sticker_image_height );

		$np_product_custom_text = isset( $_POST['np_product_custom_text'] ) ? sanitize_text_field( $_POST['np_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_np_product_custom_text', $np_product_custom_text );
		$np_sticker_type = isset( $_POST['np_sticker_type'] ) ? sanitize_text_field( $_POST['np_sticker_type'] ) : '';
		update_post_meta( $post_id, '_np_sticker_type', $np_sticker_type );
		$np_product_custom_text_fontcolor = isset( $_POST['np_product_custom_text_fontcolor'] ) ? sanitize_hex_color( $_POST['np_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_fontcolor', $np_product_custom_text_fontcolor );
		$np_product_custom_text_backcolor = isset( $_POST['np_product_custom_text_backcolor'] ) ? sanitize_hex_color( $_POST['np_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_backcolor', $np_product_custom_text_backcolor );

		$np_product_custom_text_padding_top = isset( $_POST['np_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['np_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_top', $np_product_custom_text_padding_top );
		$np_product_custom_text_padding_right = isset( $_POST['np_product_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['np_product_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_right', $np_product_custom_text_padding_right );
		$np_product_custom_text_padding_bottom = isset( $_POST['np_product_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['np_product_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_bottom', $np_product_custom_text_padding_bottom );
		$np_product_custom_text_padding_left = isset( $_POST['np_product_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['np_product_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_left', $np_product_custom_text_padding_left );

		$np_sticker_custom_id = isset( $_POST['np_sticker_custom_id'] ) ? absint( $_POST['np_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_np_sticker_custom_id', $np_sticker_custom_id );

		//Rotate
		$np_sticker_rotate = isset( $_POST['np_sticker_rotate'] ) ? sanitize_text_field( $_POST['np_sticker_rotate'] ) : '';
		update_post_meta( $post_id, 'np_sticker_rotate', $np_sticker_rotate );
		
		//Animation
		$np_sticker_animation_type = isset( $_POST['np_sticker_animation_type'] ) ? sanitize_text_field( $_POST['np_sticker_animation_type'] ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_type', $np_sticker_animation_type );
		$np_sticker_animation_direction = isset( $_POST['np_sticker_animation_direction'] ) ? sanitize_text_field( $_POST['np_sticker_animation_direction'] ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_direction', $np_sticker_animation_direction );
		$np_sticker_animation_scale = isset( $_POST['np_sticker_animation_scale'] ) ? sanitize_text_field( $_POST['np_sticker_animation_scale'] ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_scale', $np_sticker_animation_scale );
		$np_sticker_animation_iteration_count = isset( $_POST['np_sticker_animation_iteration_count'] ) ? sanitize_text_field( $_POST['np_sticker_animation_iteration_count'] ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_iteration_count', $np_sticker_animation_iteration_count );
		$np_sticker_animation_delay = isset( $_POST['np_sticker_animation_delay'] ) ? sanitize_text_field( $_POST['np_sticker_animation_delay'] ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_delay', $np_sticker_animation_delay );

		// Scheduled Sticker for New Products
		
		$enable_np_product_schedule_sticker = isset( $_POST['enable_np_product_schedule_sticker'] ) ? sanitize_text_field( $_POST['enable_np_product_schedule_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_np_product_schedule_sticker', $enable_np_product_schedule_sticker );

		$np_product_schedule_start_sticker_date_time = isset( $_POST['np_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( $_POST['np_product_schedule_start_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_np_product_schedule_start_sticker_date_time', $np_product_schedule_start_sticker_date_time );
		$np_product_schedule_end_sticker_date_time = isset( $_POST['np_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( $_POST['np_product_schedule_end_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_np_product_schedule_end_sticker_date_time', $np_product_schedule_end_sticker_date_time );

		$np_product_schedule_option = isset( $_POST['np_product_schedule_option'] ) ? sanitize_text_field( $_POST['np_product_schedule_option'] ) : '';
		update_post_meta( $post_id, '_np_product_schedule_option', $np_product_schedule_option );

		$np_schedule_sticker_image_width = isset( $_POST['np_schedule_sticker_image_width'] ) ? sanitize_text_field( $_POST['np_schedule_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'np_schedule_sticker_image_width', $np_schedule_sticker_image_width );
		$np_schedule_sticker_image_height = isset( $_POST['np_schedule_sticker_image_height'] ) ? sanitize_text_field( $_POST['np_schedule_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'np_schedule_sticker_image_height', $np_schedule_sticker_image_height );
		$np_schedule_sticker_custom_id = isset( $_POST['np_schedule_sticker_custom_id'] ) ? sanitize_text_field( $_POST['np_schedule_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_np_schedule_sticker_custom_id', $np_schedule_sticker_custom_id );

		$np_schedule_product_custom_text = isset( $_POST['np_schedule_product_custom_text'] ) ? sanitize_text_field( $_POST['np_schedule_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text', $np_schedule_product_custom_text );
		$np_schedule_sticker_type = isset( $_POST['np_schedule_sticker_type'] ) ? sanitize_text_field( $_POST['np_schedule_sticker_type'] ) : '';
		update_post_meta( $post_id, '_np_schedule_sticker_type', $np_schedule_sticker_type );
		$np_schedule_product_custom_text_fontcolor = isset( $_POST['np_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( $_POST['np_schedule_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text_fontcolor', $np_schedule_product_custom_text_fontcolor );
		$np_schedule_product_custom_text_backcolor = isset( $_POST['np_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( $_POST['np_schedule_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text_backcolor', $np_schedule_product_custom_text_backcolor );

		$np_schedule_product_custom_text_padding_top = isset( $_POST['np_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['np_schedule_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text_padding_top', $np_schedule_product_custom_text_padding_top );
		$np_product_schedule_custom_text_padding_right = isset( $_POST['np_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['np_product_schedule_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_np_product_schedule_custom_text_padding_right', $np_product_schedule_custom_text_padding_right );
		$np_product_schedule_custom_text_padding_bottom = isset( $_POST['np_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['np_product_schedule_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_np_product_schedule_custom_text_padding_bottom', $np_product_schedule_custom_text_padding_bottom );
		$np_product_schedule_custom_text_padding_left = isset( $_POST['np_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['np_product_schedule_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_np_product_schedule_custom_text_padding_left', $np_product_schedule_custom_text_padding_left );

		//Save on sale product options
		$enable_pos_sticker = isset( $_POST['enable_pos_sticker'] ) ? sanitize_text_field( $_POST['enable_pos_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_pos_sticker', $enable_pos_sticker );
		$pos_sticker_pos = isset( $_POST['pos_sticker_pos'] ) ? sanitize_text_field( $_POST['pos_sticker_pos'] ) : '';
		update_post_meta( $post_id, '_pos_sticker_pos', $pos_sticker_pos );
		
		$pos_sticker_left_right = isset( $_POST['pos_sticker_left_right'] ) ? sanitize_text_field( $_POST['pos_sticker_left_right'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_left_right', $pos_sticker_left_right );
		$pos_sticker_top = isset( $_POST['pos_sticker_top'] ) ? sanitize_text_field( $_POST['pos_sticker_top'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_top', $pos_sticker_top );

		$pos_product_option = isset( $_POST['pos_product_option'] ) ? sanitize_key( $_POST['pos_product_option'] ) : '';
		update_post_meta( $post_id, '_pos_product_option', $pos_product_option );

		$pos_sticker_image_width = isset( $_POST['pos_sticker_image_width'] ) ? sanitize_text_field( $_POST['pos_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_image_width', $pos_sticker_image_width );
		$pos_sticker_image_height = isset( $_POST['pos_sticker_image_height'] ) ? sanitize_text_field( $_POST['pos_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_image_height', $pos_sticker_image_height );

		$pos_product_custom_text = isset( $_POST['pos_product_custom_text'] ) ? sanitize_text_field( $_POST['pos_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text', $pos_product_custom_text );
		$pos_sticker_type = isset( $_POST['pos_sticker_type'] ) ? sanitize_text_field( $_POST['pos_sticker_type'] ) : '';
		update_post_meta( $post_id, '_pos_sticker_type', $pos_sticker_type );
		$pos_product_custom_text_fontcolor = isset( $_POST['pos_product_custom_text_fontcolor'] ) ? sanitize_hex_color( $_POST['pos_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_fontcolor', $pos_product_custom_text_fontcolor );
		$pos_product_custom_text_backcolor = isset( $_POST['pos_product_custom_text_backcolor'] ) ? sanitize_hex_color( $_POST['pos_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_backcolor', $pos_product_custom_text_backcolor );

		$pos_product_custom_text_padding_top = isset( $_POST['pos_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['pos_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_top', $pos_product_custom_text_padding_top );
		$pos_product_custom_text_padding_right = isset( $_POST['pos_product_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['pos_product_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_right', $pos_product_custom_text_padding_right );
		$pos_product_custom_text_padding_bottom = isset( $_POST['pos_product_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['pos_product_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_bottom', $pos_product_custom_text_padding_bottom );
		$pos_product_custom_text_padding_left = isset( $_POST['pos_product_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['pos_product_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_left', $pos_product_custom_text_padding_left );

		$pos_sticker_custom_id = isset( $_POST['pos_sticker_custom_id'] ) ? absint( $_POST['pos_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_pos_sticker_custom_id', $pos_sticker_custom_id );

		//Rotate
		$pos_sticker_rotate = isset( $_POST['pos_sticker_rotate'] ) ? sanitize_text_field( $_POST['pos_sticker_rotate'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_rotate', $pos_sticker_rotate );

		//Animation
		$pos_sticker_animation_type = isset( $_POST['pos_sticker_animation_type'] ) ? sanitize_text_field( $_POST['pos_sticker_animation_type'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_type', $pos_sticker_animation_type );
		$pos_sticker_animation_direction = isset( $_POST['pos_sticker_animation_direction'] ) ? sanitize_text_field( $_POST['pos_sticker_animation_direction'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_direction', $pos_sticker_animation_direction );
		$pos_sticker_animation_scale = isset( $_POST['pos_sticker_animation_scale'] ) ? sanitize_text_field( $_POST['pos_sticker_animation_scale'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_scale', $pos_sticker_animation_scale );
		$pos_sticker_animation_iteration_count = isset( $_POST['pos_sticker_animation_iteration_count'] ) ? sanitize_text_field( $_POST['pos_sticker_animation_iteration_count'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_iteration_count', $pos_sticker_animation_iteration_count );
		$pos_sticker_animation_delay = isset( $_POST['pos_sticker_animation_delay'] ) ? sanitize_text_field( $_POST['pos_sticker_animation_delay'] ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_delay', $pos_sticker_animation_delay );

		// Scheduled Sticker for sale Products

		$enable_pos_product_schedule_sticker = isset( $_POST['enable_pos_product_schedule_sticker'] ) ? sanitize_text_field( $_POST['enable_pos_product_schedule_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_pos_product_schedule_sticker', $enable_pos_product_schedule_sticker );

		$pos_product_schedule_start_sticker_date_time = isset( $_POST['pos_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( $_POST['pos_product_schedule_start_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_start_sticker_date_time', $pos_product_schedule_start_sticker_date_time );
		$pos_product_schedule_end_sticker_date_time = isset( $_POST['pos_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( $_POST['pos_product_schedule_end_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_end_sticker_date_time', $pos_product_schedule_end_sticker_date_time );

		$pos_product_schedule_option = isset( $_POST['pos_product_schedule_option'] ) ? sanitize_text_field( $_POST['pos_product_schedule_option'] ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_option', $pos_product_schedule_option );

		$pos_schedule_sticker_image_width = isset( $_POST['pos_schedule_sticker_image_width'] ) ? sanitize_text_field( $_POST['pos_schedule_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'pos_schedule_sticker_image_width', $pos_schedule_sticker_image_width );
		$pos_schedule_sticker_image_height = isset( $_POST['pos_schedule_sticker_image_height'] ) ? sanitize_text_field( $_POST['pos_schedule_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'pos_schedule_sticker_image_height', $pos_schedule_sticker_image_height );
		$pos_schedule_sticker_custom_id = isset( $_POST['pos_schedule_sticker_custom_id'] ) ? sanitize_text_field( $_POST['pos_schedule_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_pos_schedule_sticker_custom_id', $pos_schedule_sticker_custom_id );

		$pos_schedule_product_custom_text = isset( $_POST['pos_schedule_product_custom_text'] ) ? sanitize_text_field( $_POST['pos_schedule_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text', $pos_schedule_product_custom_text );
		$pos_schedule_sticker_type = isset( $_POST['pos_schedule_sticker_type'] ) ? sanitize_text_field( $_POST['pos_schedule_sticker_type'] ) : '';
		update_post_meta( $post_id, '_pos_schedule_sticker_type', $pos_schedule_sticker_type );
		$pos_schedule_product_custom_text_fontcolor = isset( $_POST['pos_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( $_POST['pos_schedule_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text_fontcolor', $pos_schedule_product_custom_text_fontcolor );
		$pos_schedule_product_custom_text_backcolor = isset( $_POST['pos_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( $_POST['pos_schedule_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text_backcolor', $pos_schedule_product_custom_text_backcolor );

		$pos_schedule_product_custom_text_padding_top = isset( $_POST['pos_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['pos_schedule_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text_padding_top', $pos_schedule_product_custom_text_padding_top );
		$pos_product_schedule_custom_text_padding_right = isset( $_POST['pos_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['pos_product_schedule_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_custom_text_padding_right', $pos_product_schedule_custom_text_padding_right );
		$pos_product_schedule_custom_text_padding_bottom = isset( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_custom_text_padding_bottom', $pos_product_schedule_custom_text_padding_bottom );
		$pos_product_schedule_custom_text_padding_left = isset( $_POST['pos_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['pos_product_schedule_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_custom_text_padding_left', $pos_product_schedule_custom_text_padding_left );

		//Save on sold out product options

		$enable_sop_sticker = isset( $_POST['enable_sop_sticker'] ) ? sanitize_text_field( $_POST['enable_sop_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_sop_sticker', $enable_sop_sticker );
		$sop_sticker_pos = isset( $_POST['sop_sticker_pos'] ) ? sanitize_text_field( $_POST['sop_sticker_pos'] ) : '';
		update_post_meta( $post_id, '_sop_sticker_pos', $sop_sticker_pos );

		$sop_sticker_left_right = isset( $_POST['sop_sticker_left_right'] ) ? sanitize_text_field( $_POST['sop_sticker_left_right'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_left_right', $sop_sticker_left_right );
		$sop_sticker_top = isset( $_POST['sop_sticker_top'] ) ? sanitize_text_field( $_POST['sop_sticker_top'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_top', $sop_sticker_top );

		$sop_product_option = isset( $_POST['sop_product_option'] ) ? sanitize_key( $_POST['sop_product_option'] ) : '';
		update_post_meta( $post_id, '_sop_product_option', $sop_product_option );

		$sop_sticker_image_width = isset( $_POST['sop_sticker_image_width'] ) ? sanitize_text_field( $_POST['sop_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_image_width', $sop_sticker_image_width );
		$sop_sticker_image_height = isset( $_POST['sop_sticker_image_height'] ) ? sanitize_text_field( $_POST['sop_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_image_height', $sop_sticker_image_height );

		$sop_product_custom_text = isset( $_POST['sop_product_custom_text'] ) ? sanitize_text_field( $_POST['sop_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text', $sop_product_custom_text );
		$sop_sticker_type = isset( $_POST['sop_sticker_type'] ) ? sanitize_text_field( $_POST['sop_sticker_type'] ) : '';
		update_post_meta( $post_id, '_sop_sticker_type', $sop_sticker_type );
		$sop_product_custom_text_fontcolor = isset( $_POST['sop_product_custom_text_fontcolor'] ) ? sanitize_hex_color( $_POST['sop_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_fontcolor', $sop_product_custom_text_fontcolor );
		$sop_product_custom_text_backcolor = isset( $_POST['sop_product_custom_text_backcolor'] ) ? sanitize_hex_color( $_POST['sop_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_backcolor', $sop_product_custom_text_backcolor );

		$sop_product_custom_text_padding_top = isset( $_POST['sop_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['sop_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_top', $sop_product_custom_text_padding_top );
		$sop_product_custom_text_padding_right = isset( $_POST['sop_product_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['sop_product_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_right', $sop_product_custom_text_padding_right );
		$sop_product_custom_text_padding_bottom = isset( $_POST['sop_product_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['sop_product_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_bottom', $sop_product_custom_text_padding_bottom );
		$sop_product_custom_text_padding_left = isset( $_POST['sop_product_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['sop_product_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_left', $sop_product_custom_text_padding_left );

		$sop_sticker_custom_id = isset( $_POST['sop_sticker_custom_id'] ) ? absint( $_POST['sop_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_sop_sticker_custom_id', $sop_sticker_custom_id );

		//Rotate
		$sop_sticker_rotate = isset( $_POST['sop_sticker_rotate'] ) ? sanitize_text_field( $_POST['sop_sticker_rotate'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_rotate', $sop_sticker_rotate );

		//Animation
		$sop_sticker_animation_type = isset( $_POST['sop_sticker_animation_type'] ) ? sanitize_text_field( $_POST['sop_sticker_animation_type'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_type', $sop_sticker_animation_type );
		$sop_sticker_animation_direction = isset( $_POST['sop_sticker_animation_direction'] ) ? sanitize_text_field( $_POST['sop_sticker_animation_direction'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_direction', $sop_sticker_animation_direction );
		$sop_sticker_animation_scale = isset( $_POST['sop_sticker_animation_scale'] ) ? sanitize_text_field( $_POST['sop_sticker_animation_scale'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_scale', $sop_sticker_animation_scale );
		$sop_sticker_animation_iteration_count = isset( $_POST['sop_sticker_animation_iteration_count'] ) ? sanitize_text_field( $_POST['sop_sticker_animation_iteration_count'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_iteration_count', $sop_sticker_animation_iteration_count );
		$sop_sticker_animation_delay = isset( $_POST['sop_sticker_animation_delay'] ) ? sanitize_text_field( $_POST['sop_sticker_animation_delay'] ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_delay', $sop_sticker_animation_delay );

		// Scheduled Sticker for Sold Products

		$enable_sop_product_schedule_sticker = isset( $_POST['enable_sop_product_schedule_sticker'] ) ? sanitize_text_field( $_POST['enable_sop_product_schedule_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_sop_product_schedule_sticker', $enable_sop_product_schedule_sticker );

		$sop_product_schedule_start_sticker_date_time = isset( $_POST['sop_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( $_POST['sop_product_schedule_start_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_start_sticker_date_time', $sop_product_schedule_start_sticker_date_time );
		$sop_product_schedule_end_sticker_date_time = isset( $_POST['sop_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( $_POST['sop_product_schedule_end_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_end_sticker_date_time', $sop_product_schedule_end_sticker_date_time );

		$sop_product_schedule_option = isset( $_POST['sop_product_schedule_option'] ) ? sanitize_text_field( $_POST['sop_product_schedule_option'] ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_option', $sop_product_schedule_option );

		$sop_schedule_sticker_image_width = isset( $_POST['sop_schedule_sticker_image_width'] ) ? sanitize_text_field( $_POST['sop_schedule_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'sop_schedule_sticker_image_width', $sop_schedule_sticker_image_width );
		$sop_schedule_sticker_image_height = isset( $_POST['sop_schedule_sticker_image_height'] ) ? sanitize_text_field( $_POST['sop_schedule_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'sop_schedule_sticker_image_height', $sop_schedule_sticker_image_height );
		$sop_schedule_sticker_custom_id = isset( $_POST['sop_schedule_sticker_custom_id'] ) ? sanitize_text_field( $_POST['sop_schedule_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_sop_schedule_sticker_custom_id', $sop_schedule_sticker_custom_id );

		$sop_schedule_product_custom_text = isset( $_POST['sop_schedule_product_custom_text'] ) ? sanitize_text_field( $_POST['sop_schedule_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text', $sop_schedule_product_custom_text );
		$sop_schedule_sticker_type = isset( $_POST['sop_schedule_sticker_type'] ) ? sanitize_text_field( $_POST['sop_schedule_sticker_type'] ) : '';
		update_post_meta( $post_id, '_sop_schedule_sticker_type', $sop_schedule_sticker_type );
		$sop_schedule_product_custom_text_fontcolor = isset( $_POST['sop_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( $_POST['sop_schedule_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text_fontcolor', $sop_schedule_product_custom_text_fontcolor );
		$sop_schedule_product_custom_text_backcolor = isset( $_POST['sop_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( $_POST['sop_schedule_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text_backcolor', $sop_schedule_product_custom_text_backcolor );

		$sop_schedule_product_custom_text_padding_top = isset( $_POST['sop_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['sop_schedule_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text_padding_top', $sop_schedule_product_custom_text_padding_top );
		$sop_product_schedule_custom_text_padding_right = isset( $_POST['sop_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['sop_product_schedule_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_custom_text_padding_right', $sop_product_schedule_custom_text_padding_right );
		$sop_product_schedule_custom_text_padding_bottom = isset( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_custom_text_padding_bottom', $sop_product_schedule_custom_text_padding_bottom );
		$sop_product_schedule_custom_text_padding_left = isset( $_POST['sop_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['sop_product_schedule_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_custom_text_padding_left', $sop_product_schedule_custom_text_padding_left );

		//Save custom product sticker options
		$enable_cust_sticker = isset( $_POST['enable_cust_sticker'] ) ? sanitize_text_field( $_POST['enable_cust_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_cust_sticker', $enable_cust_sticker );
		$cust_sticker_pos = isset( $_POST['cust_sticker_pos'] ) ? sanitize_text_field( $_POST['cust_sticker_pos'] ) : '';
		update_post_meta( $post_id, '_cust_sticker_pos', $cust_sticker_pos );

		$cust_sticker_left_right = isset( $_POST['cust_sticker_left_right'] ) ? sanitize_text_field( $_POST['cust_sticker_left_right'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_left_right', $cust_sticker_left_right );
		$cust_sticker_top = isset( $_POST['cust_sticker_top'] ) ? sanitize_text_field( $_POST['cust_sticker_top'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_top', $cust_sticker_top );

		$cust_product_option = isset( $_POST['cust_product_option'] ) ? sanitize_key( $_POST['cust_product_option'] ) : '';
		update_post_meta( $post_id, '_cust_product_option', $cust_product_option );

		$cust_sticker_image_width = isset( $_POST['cust_sticker_image_width'] ) ? sanitize_text_field( $_POST['cust_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_image_width', $cust_sticker_image_width );
		$cust_sticker_image_height = isset( $_POST['cust_sticker_image_height'] ) ? sanitize_text_field( $_POST['cust_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_image_height', $cust_sticker_image_height );

		$cust_product_custom_text = isset( $_POST['cust_product_custom_text'] ) ? sanitize_text_field( $_POST['cust_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text', $cust_product_custom_text );
		$cust_sticker_type = isset( $_POST['cust_sticker_type'] ) ? sanitize_text_field( $_POST['cust_sticker_type'] ) : '';
		update_post_meta( $post_id, '_cust_sticker_type', $cust_sticker_type );
		$cust_product_custom_text_fontcolor = isset( $_POST['cust_product_custom_text_fontcolor'] ) ? sanitize_hex_color( $_POST['cust_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_fontcolor', $cust_product_custom_text_fontcolor );
		$cust_product_custom_text_backcolor = isset( $_POST['cust_product_custom_text_backcolor'] ) ? sanitize_hex_color( $_POST['cust_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_backcolor', $cust_product_custom_text_backcolor );

		$cust_product_custom_text_padding_top = isset( $_POST['cust_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['cust_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_top', $cust_product_custom_text_padding_top );
		$cust_product_custom_text_padding_right = isset( $_POST['cust_product_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['cust_product_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_right', $cust_product_custom_text_padding_right );
		$cust_product_custom_text_padding_bottom = isset( $_POST['cust_product_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['cust_product_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_bottom', $cust_product_custom_text_padding_bottom );
		$cust_product_custom_text_padding_left = isset( $_POST['cust_product_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['cust_product_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_left', $cust_product_custom_text_padding_top );
		
		$cust_sticker_custom_id = isset( $_POST['cust_sticker_custom_id'] ) ? absint( $_POST['cust_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_cust_sticker_custom_id', $cust_sticker_custom_id );
		
		//Rotate
		$cust_sticker_rotate = isset( $_POST['cust_sticker_rotate'] ) ? sanitize_text_field( $_POST['cust_sticker_rotate'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_rotate', $cust_sticker_rotate );

		//Animation
		$cust_sticker_animation_type = isset( $_POST['cust_sticker_animation_type'] ) ? sanitize_text_field( $_POST['cust_sticker_animation_type'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_type', $cust_sticker_animation_type );
		$cust_sticker_animation_direction = isset( $_POST['cust_sticker_animation_direction'] ) ? sanitize_text_field( $_POST['cust_sticker_animation_direction'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_direction', $cust_sticker_animation_direction );
		$cust_sticker_animation_scale = isset( $_POST['cust_sticker_animation_scale'] ) ? sanitize_text_field( $_POST['cust_sticker_animation_scale'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_scale', $cust_sticker_animation_scale );
		$cust_sticker_animation_iteration_count = isset( $_POST['cust_sticker_animation_iteration_count'] ) ? sanitize_text_field( $_POST['cust_sticker_animation_iteration_count'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_iteration_count', $cust_sticker_animation_iteration_count );
		$cust_sticker_animation_delay = isset( $_POST['cust_sticker_animation_delay'] ) ? sanitize_text_field( $_POST['cust_sticker_animation_delay'] ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_delay', $cust_sticker_animation_delay );

		// Scheduled Sticker for Custom Sticker

		$enable_cust_product_schedule_sticker = isset( $_POST['enable_cust_product_schedule_sticker'] ) ? sanitize_text_field( $_POST['enable_cust_product_schedule_sticker'] ) : '';
		update_post_meta( $post_id, '_enable_cust_product_schedule_sticker', $enable_cust_product_schedule_sticker );

		$cust_product_schedule_start_sticker_date_time = isset( $_POST['cust_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( $_POST['cust_product_schedule_start_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_start_sticker_date_time', $cust_product_schedule_start_sticker_date_time );
		$cust_product_schedule_end_sticker_date_time = isset( $_POST['cust_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( $_POST['cust_product_schedule_end_sticker_date_time'] ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_end_sticker_date_time', $cust_product_schedule_end_sticker_date_time );

		$cust_product_schedule_option = isset( $_POST['cust_product_schedule_option'] ) ? sanitize_text_field( $_POST['cust_product_schedule_option'] ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_option', $cust_product_schedule_option );

		$cust_schedule_sticker_image_width = isset( $_POST['cust_schedule_sticker_image_width'] ) ? sanitize_text_field( $_POST['cust_schedule_sticker_image_width'] ) : '';
		update_post_meta( $post_id, 'cust_schedule_sticker_image_width', $cust_schedule_sticker_image_width );
		$cust_schedule_sticker_image_height = isset( $_POST['cust_schedule_sticker_image_height'] ) ? sanitize_text_field( $_POST['cust_schedule_sticker_image_height'] ) : '';
		update_post_meta( $post_id, 'cust_schedule_sticker_image_height', $cust_schedule_sticker_image_height );
		$cust_schedule_sticker_custom_id = isset( $_POST['cust_schedule_sticker_custom_id'] ) ? sanitize_text_field( $_POST['cust_schedule_sticker_custom_id'] ) : '';
		update_post_meta( $post_id, '_cust_schedule_sticker_custom_id', $cust_schedule_sticker_custom_id );

		$cust_schedule_product_custom_text = isset( $_POST['cust_schedule_product_custom_text'] ) ? sanitize_text_field( $_POST['cust_schedule_product_custom_text'] ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text', $cust_schedule_product_custom_text );
		$cust_schedule_sticker_type = isset( $_POST['cust_schedule_sticker_type'] ) ? sanitize_text_field( $_POST['cust_schedule_sticker_type'] ) : '';
		update_post_meta( $post_id, '_cust_schedule_sticker_type', $cust_schedule_sticker_type );
		$cust_schedule_product_custom_text_fontcolor = isset( $_POST['cust_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( $_POST['cust_schedule_product_custom_text_fontcolor'] ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text_fontcolor', $cust_schedule_product_custom_text_fontcolor );
		$cust_schedule_product_custom_text_backcolor = isset( $_POST['cust_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( $_POST['cust_schedule_product_custom_text_backcolor'] ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text_backcolor', $cust_schedule_product_custom_text_backcolor );

		$cust_schedule_product_custom_text_padding_top = isset( $_POST['cust_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( $_POST['cust_schedule_product_custom_text_padding_top'] ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text_padding_top', $cust_schedule_product_custom_text_padding_top );
		$cust_product_schedule_custom_text_padding_right = isset( $_POST['cust_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( $_POST['cust_product_schedule_custom_text_padding_right'] ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_custom_text_padding_right', $cust_product_schedule_custom_text_padding_right );
		$cust_product_schedule_custom_text_padding_bottom = isset( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_custom_text_padding_bottom', $cust_product_schedule_custom_text_padding_bottom );
		$cust_product_schedule_custom_text_padding_left = isset( $_POST['cust_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( $_POST['cust_product_schedule_custom_text_padding_left'] ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_custom_text_padding_left', $cust_product_schedule_custom_text_padding_left );
	}

	/**
	 * Category sticker fields.
	 */
	public function add_category_fields() {
		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = date($format, $current_timestamp);
	?>
	<div class="wsbw-sticker-options-wrap">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="#wsbw_new_products"><?php _e( "New Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_products_sale"><?php _e( "Products On Sale", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_soldout_products"><?php _e( "Soldout Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_cust_products"><?php _e( "Custom Product Sticker", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_category_sticker"><?php _e( "Category Sticker", 'woo-stickers-by-webline' );?></a>
		</h2>

		<div id="wsbw_new_products" class="wsbw_tab_content">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable_np_sticker"><?php _e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_np_sticker" name="enable_np_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="np_no_of_days"><?php _e( 'Number of Days for New Product:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_no_of_days" value="" class="small-text"></td>
					</tr>
					<tr>
						<th scope="row"><label for="np_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="np_sticker_pos" name="np_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="np_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_top" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><label for="np_sticker_left_right"><?php _e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_left_right" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td><input type="number" name="np_sticker_rotate" value="" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
								<?php
							}else{
								?>
								<td>
								<div class="wosbw-pro-ribbon-banner">	
									<input type="number" name="np_sticker_rotate" value="" class="small-text file-input" disabled><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>

									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
								<?php
							}
						?>
						
					</tr>
					<tr>
						<th scope="row"><label for="np_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="np_sticker_category_animation_type" id="np_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'none', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php } else{?>
							<td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="np_sticker_category_animation_type" id="np_sticker_category_animation_type" class="file-input" disabled>
												<?php
												$animation_options = array(
													'none'      => __( 'none', 'woo-stickers-by-webline' ),
													'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
													'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
													'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
													'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
													'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
												);

												$saved_value = '';

												foreach ($animation_options as $value => $label) {
													$selected = ($saved_value == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
										</div>
									</td>
							<?php }?>

					</tr>

					<?php if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr id="zoominout-options-new-add-cat" style="display:none;">
							<th scope="row"><label for="np_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="np_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>

						<tr>
							<th scope="row"><label for="np_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="np_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = '';

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="np_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="np_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="np_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="np_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>
					<tr>
						<th scope="row"><label for="enable_np_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="enable_np_product_schedule_sticker_category" id="enable_np_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as NEW in wooCommerce..', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<select disabled>
										<option>No</option>
									</select>
									<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as NEW in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>

									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
						<?php } ?>
					</tr>
					<?php if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_start_sticker_date_time" name="np_product_schedule_start_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_end_sticker_date_time" name="np_product_schedule_end_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" min="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="np_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt np_product_schedule_option">
									<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule" id="image_schedule_np" value="image_schedule" checked="checked"/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule" id="text_schedule_np" value="text_schedule"/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="np_product_schedule_option" name="np_product_schedule_option" value=""/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="np_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="np_schedule_sticker_image_width" name="np_schedule_sticker_image_width" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="np_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="np_schedule_sticker_image_height" name="np_schedule_sticker_image_height" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top">
								<label for="np_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="np_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
								</div>
								<div style="line-height: 60px;">
									<input type="hidden" id="np_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="np_schedule_sticker_custom_id" value="" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_np"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_np"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="np_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="np_product_schedule_custom_text" name="np_product_schedule_custom_text" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="np_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='np_schedule_sticker_type'
									name="np_schedule_sticker_type">
									<option value="ribbon">Ribbon</option>
									<option value="round">Round</option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_np">
							<th scope="row" valign="top">
								<label for="np_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="np_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="np_schedule_product_custom_text_fontcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_np">
							<th scope="row" valign="top">
								<label for="np_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="np_schedule_product_custom_text_backcolor" class="wli_color_picker" name="np_schedule_product_custom_text_backcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="np_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="np_product_schedule_custom_text_padding_top" value=""/>
								<input type="number" id="np_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="np_product_schedule_custom_text_padding_right" value=""/>
								<input type="number" id="np_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="np_product_schedule_custom_text_padding_bottom" value=""/>
								<input type="number" id="np_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="np_product_schedule_custom_text_padding_left" value=""/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<th scope="row"><div class="woo_opt np_product_option"><label for="np_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="image1" value="image" checked="checked"/> <?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="text1" value="text"/> <?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="np_product_option" name="np_product_option" value="image"/>
						<d>
					</tr>
					<tr class ="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="np_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_image_width" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class ="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="np_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_image_height" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_product_custom_text"><?php _e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="np_product_custom_text" name="np_product_custom_text" value=""></td>
					</tr>
					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_sticker_type"><?php _e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="np_sticker_type" name="np_sticker_type"><option value="ribbon"><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php _e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>
					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="np_product_custom_text_fontcolor" class="wli_color_picker" name="np_product_custom_text_fontcolor" value="#ffffff"></td>
					</tr>
					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="np_product_custom_text_backcolor" class="wli_color_picker" name="np_product_custom_text_backcolor" value="#000000"></td>
					</tr>

					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_product_custom_text_padding_top" value="np_product_custom_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="np_product_custom_text_padding_right" value="np_product_custom_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="np_product_custom_text_padding_bottom" value="np_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="np_product_custom_text_padding_left" value="np_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class ="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="np_sticker_custom_id" class="wsbw_upload_img_id" name="np_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="wsbw_products_sale" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable_pos_sticker"><?php _e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_pos_sticker" name="enable_pos_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="pos_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="pos_sticker_pos" name="pos_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="pos_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_top" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><label for="pos_sticker_left_right"><?php _e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_left_right" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
							<td><input type="number" name="pos_sticker_rotate" value="" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<input type="number" value="" class="small-text file-input" disabled>
									<p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
						<?php } ?>
					</tr>
					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="pos_sticker_category_animation_type" id="pos_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'none', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php }else{ ?>
						<td>
							<div class="wosbw-pro-ribbon-banner">
								<select class="file-input" disabled>
									<option>None</option>
								</select>
								<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

								<div class="ribbon">
									<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
										<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
										<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
										<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
										<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
										<defs>
										<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
										<stop stop-color="#FDAB00"/>
										<stop offset="1" stop-color="#CD8F0D"/>
										</linearGradient>
										<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
										<stop stop-color="#FDAB00"/>
										<stop offset="1" stop-color="#CD8F0D"/>
										</linearGradient>
										</defs>
									</svg>
								</div>

								<div class="learn-more">
									<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
								</div>
							</div>
						</td>
						<?php } ?>

					</tr>
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<tr id="zoominout-options-pos-add-cat" style="display:none;">
							<th scope="row"><label for="pos_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="pos_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="pos_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="pos_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = '';

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pos_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="pos_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="pos_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="pos_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php }else ?>


					<tr>
						<th scope="row"><label for="enable_pos_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="enable_pos_product_schedule_sticker_category" id="enable_pos_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as Sale in wooCommerce..', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php }else{ ?>
						<td>
							<div class="wosbw-pro-ribbon-banner">
								<select class="file-input" disabled>
									<option >No</option>
								</select>
								<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as Sale in wooCommerce..', 'woo-stickers-by-webline' ); ?></p>

								<div class="ribbon">
									<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
										<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
										<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
										<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
										<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
										<defs>
										<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
										<stop stop-color="#FDAB00"/>
										<stop offset="1" stop-color="#CD8F0D"/>
										</linearGradient>
										<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
										<stop stop-color="#FDAB00"/>
										<stop offset="1" stop-color="#CD8F0D"/>
										</linearGradient>
										</defs>
									</svg>
								</div>

								<div class="learn-more">
									<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
								</div>	
							</div>
						</td>
					<?php } ?>
					</tr>
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_start_sticker_date_time" name="pos_product_schedule_start_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_end_sticker_date_time" name="pos_product_schedule_end_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" min="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="pos_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt pos_product_schedule_option">
									<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule" id="image_schedule_pos" value="image_schedule" checked="checked"/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule" id="text_schedule_pos" value="text_schedule"/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="pos_product_schedule_option" name="pos_product_schedule_option" value=""/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="pos_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="pos_schedule_sticker_image_width" name="pos_schedule_sticker_image_width" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="pos_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="pos_schedule_sticker_image_height" name="pos_schedule_sticker_image_height" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top">
								<label for="pos_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="pos_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
								</div>
								<div style="line-height: 60px;">
									<input type="hidden" id="pos_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="pos_schedule_sticker_custom_id" value="" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_pos"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_pos"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="pos_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="pos_product_schedule_custom_text" name="pos_product_schedule_custom_text" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="pos_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='pos_schedule_sticker_type'
									name="pos_schedule_sticker_type">
									<option value="ribbon">Ribbon</option>
									<option value="round">Round</option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_pos">
							<th scope="row" valign="top">
								<label for="pos_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="pos_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_fontcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_pos">
							<th scope="row" valign="top">
								<label for="pos_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="pos_schedule_product_custom_text_backcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_backcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="pos_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="pos_product_schedule_custom_text_padding_top" value=""/>
								<input type="number" id="pos_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="pos_product_schedule_custom_text_padding_right" value=""/>
								<input type="number" id="pos_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="pos_product_schedule_custom_text_padding_bottom" value=""/>
								<input type="number" id="pos_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="pos_product_schedule_custom_text_padding_left" value=""/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row"><div class="woo_opt pos_product_option"><label for="pos_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="image2" value="image" checked="checked"/> <?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="text2" value="text"/> <?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="pos_product_option" name="pos_product_option" value="image"/>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="pos_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_image_width" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="pos_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_image_height" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_product_custom_text"><?php _e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="pos_product_custom_text" name="pos_product_custom_text" value=""></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_sticker_type"><?php _e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="pos_sticker_type" name="pos_sticker_type"><option value="ribbon"><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php _e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="pos_product_custom_text_fontcolor" class="wli_color_picker" name="pos_product_custom_text_fontcolor" value="#ffffff"/></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="pos_product_custom_text_backcolor" class="wli_color_picker" name="pos_product_custom_text_backcolor" value="#000000"/></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_product_custom_text_padding_top" value="pos_product_custom_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="pos_product_custom_text_padding_right" value="pos_product_custom_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="pos_product_custom_text_padding_bottom" value="pos_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="pos_product_custom_text_padding_left" value="pos_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="pos_sticker_custom_id" class="wsbw_upload_img_id" name="pos_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="wsbw_soldout_products" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable_sop_sticker"><?php _e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_sop_sticker" name="enable_sop_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="sop_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="sop_sticker_pos" name="sop_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="sop_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_top" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><label for="sop_sticker_left_right"><?php _e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_left_right" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>

						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>

						<td><input type="number" name="sop_sticker_rotate" value="" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
						<?php }else{ ?>
						<td>
							<div class="wosbw-pro-ribbon-banner">
								<input type="number" value="" class="small-text file-input" disabled>
								<p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>

								<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
							</div>
						</td>
						<?php } ?>

					</tr>
					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
							<td>
								<select name="sop_sticker_category_animation_type" id="sop_sticker_category_animation_type">
									<?php
									$animation_options = array(
										'none'      => __( 'none', 'woo-stickers-by-webline' ),
										'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
										'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
										'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
										'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
										'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
									);

									$saved_value = '';

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<select class="file-input" disabled>
										<option>None</option>
									</select>
									<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>
									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</td>
						<?php } ?>

					</tr>
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<tr id="zoominout-options-sop-add-cat" style="display:none;">
							<th scope="row"><label for="sop_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="sop_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="sop_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="sop_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = '';

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sop_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="sop_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="sop_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="sop_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row"><label for="enable_sop_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="enable_sop_product_schedule_sticker_category" id="enable_sop_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SOLD in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php }else{ ?>
							<td>
							<div class="wosbw-pro-ribbon-banner">
								<select class="file-input" disabled>
									<option>No</option>
								</select>
								<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SOLD in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>

								<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
							</div>
							</td>
						<?php } ?>
					</tr>
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_start_sticker_date_time" name="sop_product_schedule_start_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_end_sticker_date_time" name="sop_product_schedule_end_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" min="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="sop_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt sop_product_schedule_option">
									<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule" id="image_schedule_sop" value="image_schedule" checked="checked"/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule" id="text_schedule_sop" value="text_schedule"/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="sop_product_schedule_option" name="sop_product_schedule_option" value=""/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="sop_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="sop_schedule_sticker_image_width" name="sop_schedule_sticker_image_width" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="sop_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="sop_schedule_sticker_image_height" name="sop_schedule_sticker_image_height" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top">
								<label for="sop_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="sop_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
								</div>
								<div style="line-height: 60px;">
									<input type="hidden" id="sop_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="sop_schedule_sticker_custom_id" value="" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_sop"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_sop"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="sop_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="sop_product_schedule_custom_text" name="sop_product_schedule_custom_text" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="sop_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='sop_schedule_sticker_type'
									name="sop_schedule_sticker_type">
									<option value="ribbon">Ribbon</option>
									<option value="round">Round</option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_sop">
							<th scope="row" valign="top">
								<label for="sop_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="sop_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_fontcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_sop">
							<th scope="row" valign="top">
								<label for="sop_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="sop_schedule_product_custom_text_backcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_backcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="sop_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="sop_product_schedule_custom_text_padding_top" value=""/>
								<input type="number" id="sop_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="sop_product_schedule_custom_text_padding_right" value=""/>
								<input type="number" id="sop_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="sop_product_schedule_custom_text_padding_bottom" value=""/>
								<input type="number" id="sop_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="sop_product_schedule_custom_text_padding_left" value=""/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row"><div class="woo_opt sop_product_option"><label for="sop_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="image3" value="image" checked="checked"/> <?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="text3" value="text"/> <?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="sop_product_option" name="sop_product_option" value="image"/>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="sop_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_image_width" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="sop_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_image_height" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_product_custom_text"><?php _e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="sop_product_custom_text" name="sop_product_custom_text" value=""></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_sticker_type"><?php _e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="sop_sticker_type" name="sop_sticker_type"><option value="ribbon"><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php _e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="sop_product_custom_text_fontcolor" class="wli_color_picker" name="sop_product_custom_text_fontcolor" value="#ffffff"/></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="sop_product_custom_text_backcolor" class="wli_color_picker" name="sop_product_custom_text_backcolor" value="#000000"/></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_product_custom_text_padding_right" value="sop_product_custom_text_padding_right" placeholder="Top" class="small-text">
							<input type="number" name="sop_product_custom_text_padding_bottom" value="sop_product_custom_text_padding_bottom" placeholder="Right" class="small-text">
							<input type="number" name="sop_product_custom_text_padding_top" value="sop_product_custom_text_padding_top" placeholder="Bottom" class="small-text">
							<input type="number" name="sop_product_custom_text_padding_left" value="sop_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage">
						<th scope="row"><label><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="sop_sticker_custom_id" class="wsbw_upload_img_id" name="sop_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="wsbw_cust_products" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable_cust_sticker"><?php _e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_cust_sticker" name="enable_cust_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cust_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="cust_sticker_pos" name="cust_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cust_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_top" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><label for="cust_sticker_left_right"><?php _e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_left_right" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td><input type="number" name="cust_sticker_rotate" value="" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<input type="number" class="small-text file-input" disabled>
									<p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>

									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>
									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
						<?php } ?>

					</tr>
					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="cust_sticker_category_animation_type" id ="cust_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'none', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<select class="file-input" disabled>
										<option>None</option>
									</select>
									<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
						<?php } ?>

					</tr>
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<tr id="zoominout-options-cust-add-cat" style="display:none;">
							<th scope="row"><label for="cust_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="cust_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="cust_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="cust_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = '';

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="cust_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="cust_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="cust_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="cust_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>
					<tr>
						<th scope="row"><label for="enable_cust_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="enable_cust_product_schedule_sticker_category" id="enable_cust_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as CUSTOM in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<select class="file-input" disabled>
										<option>No</option>
									</select>
									<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as CUSTOM in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>

									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
						<?php } ?>
					</tr>
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" id="cust_product_schedule_start_sticker_date_time" name="cust_product_schedule_start_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" id="cust_product_schedule_end_sticker_date_time" name="cust_product_schedule_end_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" min="<?php echo $formatted_date_time; ?>"/>
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="cust_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt cust_product_schedule_option">
									<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule" id="image_schedule_cust" value="image_schedule" checked="checked"/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule" id="text_schedule_cust" value="text_schedule"/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="cust_product_schedule_option" name="cust_product_schedule_option" value=""/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="cust_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="cust_schedule_sticker_image_width" name="cust_schedule_sticker_image_width" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="cust_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="cust_schedule_sticker_image_height" name="cust_schedule_sticker_image_height" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top">
								<label for="cust_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="cust_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
								</div>
								<div style="line-height: 60px;">
									<input type="hidden" id="cust_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="cust_schedule_sticker_custom_id" value="" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cust"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cust"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="cust_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="cust_product_schedule_custom_text" name="cust_product_schedule_custom_text" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="cust_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='cust_schedule_sticker_type'
									name="cust_schedule_sticker_type">
									<option value="ribbon">Ribbon</option>
									<option value="round">Round</option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_cust">
							<th scope="row" valign="top">
								<label for="cust_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="cust_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_fontcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_cust">
							<th scope="row" valign="top">
								<label for="cust_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="cust_schedule_product_custom_text_backcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_backcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="cust_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="cust_product_schedule_custom_text_padding_top" value=""/>
								<input type="number" id="cust_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="cust_product_schedule_custom_text_padding_right" value=""/>
								<input type="number" id="cust_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="cust_product_schedule_custom_text_padding_bottom" value=""/>
								<input type="number" id="cust_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="cust_product_schedule_custom_text_padding_left" value=""/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>


					<tr>
						<th scope="row"><div class="woo_opt cust_product_option"><label for="cust_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="image4" value="image" checked="checked"/> <?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="text4" value="text"/> <?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="cust_product_option" name="cust_product_option" value="image"/>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="cust_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_image_width" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="cust_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_image_height" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_product_custom_text"><?php _e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="cust_product_custom_text" name="cust_product_custom_text" value=""></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_sticker_type"><?php _e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="cust_sticker_type" name="cust_sticker_type"><option value="ribbon"><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php _e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_product_custom_text_fontcolor"><?php _e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="cust_product_custom_text_fontcolor" value="#ffffff"></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_product_custom_text_backcolor"><?php _e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="cust_product_custom_text_backcolor" value="#000000"></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_product_custom_text_padding_top" value="cust_product_custom_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="cust_product_custom_text_padding_right" value="cust_product_custom_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="cust_product_custom_text_padding_bottom" value="cust_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="cust_product_custom_text_padding_left" value="cust_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage">
						<th scope="row"><label><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="cust_sticker_custom_id" class="wsbw_upload_img_id" name="cust_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="wsbw_category_sticker" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable_category_sticker"><?php _e( 'Enable Category Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_category_sticker" name="enable_category_sticker" class="postform">
								<option value=""><?php _e( 'Please select', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Enable sticker on this category', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="category_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="category_sticker_pos" name="category_sticker_pos" class="postform">
								<option value="left"><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="category_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_top" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr>
						<th scope="row"><label for="category_sticker_left_right"><?php _e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_left_right" value="" class="small-text"><p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
							?>
							<td><input type="number" name="category_sticker_sticker_rotate" value="" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<input type="number" value="" class="small-text file-input" disabled>
									<p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
						<?php } ?>
					</tr>
					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<td>
							<select name="category_sticker_sticker_category_animation_type" id="category_sticker_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'none', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
						</td>
						<?php }else{ ?>
						<td>
							<div class="wosbw-pro-ribbon-banner">
								<select class="file-input" disabled>
									<option>None</option>
								</select>
								<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

								<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
							</div>
								</td>
						<?php } ?>

					</tr>

					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
						<tr id="zoominout-options-category-add-cat" style="display:none;">
							<th scope="row"><label for="category_sticker_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="category_sticker_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="category_sticker_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="category_sticker_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = '';

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="category_sticker_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="category_sticker_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="category_sticker_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="category_sticker_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row"><label for="enable_category_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
							?>
							<td>
								<select name="enable_category_product_schedule_sticker_category" id="enable_category_product_schedule_sticker_category">
									<option value="yes">Yes</option>
									<option value="no" selected>No</option>
								</select>
								<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked by Category in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						<?php }else{ ?>
							<td>
								<div class="wosbw-pro-ribbon-banner">
									<select class="file-inputs" disabled>
										<option>No</option>
									</select>
									<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked by Category in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>

									<div class="ribbon">
										<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
											<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
											<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
											<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
											<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
											<defs>
											<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FDAB00"/>
											<stop offset="1" stop-color="#CD8F0D"/>
											</linearGradient>
											</defs>
										</svg>
									</div>

									<div class="learn-more">
										<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
									</div>
								</div>
							</td>
						<?php } ?>
					</tr>
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
							?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_start_sticker_date_time" name="category_product_schedule_start_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_end_sticker_date_time" name="category_product_schedule_end_sticker_date_time" 
									value="<?php echo $formatted_date_time; ?>" min="<?php echo $formatted_date_time; ?>"/>
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="category_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt category_product_schedule_option">
									<input type="radio" name="stickeroption_sch_5" class="wli-woosticker-radio-schedule" id="image_schedule_category" value="image_schedule" checked="checked"/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_5" class="wli-woosticker-radio-schedule" id="text_schedule_category" value="text_schedule"/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="category_product_schedule_option" name="category_product_schedule_option" value=""/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="category_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="category_schedule_sticker_image_width" name="category_schedule_sticker_image_width" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top"><label for="category_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="category_schedule_sticker_image_height" name="category_schedule_sticker_image_height" value="" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" style="display: table-row;">
							<th scope="row" valign="top">
								<label for="category_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="category_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
								</div>
								<div style="line-height: 60px;">
									<input type="hidden" id="category_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="category_schedule_sticker_custom_id" value="" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cat"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cat"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="category_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="category_product_schedule_custom_text" name="category_product_schedule_custom_text" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for="category_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='category_schedule_sticker_type'
									name="category_schedule_sticker_type">
									<option value="ribbon">Ribbon</option>
									<option value="round">Round</option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat">
							<th scope="row" valign="top">
								<label for="category_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="category_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="category_schedule_product_custom_text_fontcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat">
							<th scope="row" valign="top">
								<label for="category_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="category_schedule_product_custom_text_backcolor" class="wli_color_picker" name="category_schedule_product_custom_text_backcolor" value=""/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch">
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="category_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="category_product_schedule_custom_text_padding_top" value=""/>
								<input type="number" id="category_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="category_product_schedule_custom_text_padding_right" value=""/>
								<input type="number" id="category_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="category_product_schedule_custom_text_padding_bottom" value=""/>
								<input type="number" id="category_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="category_product_schedule_custom_text_padding_left" value=""/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<th scope="row"><div class="woo_opt category_sticker_option"><label for="category_sticker_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption5" class="wli-woosticker-radio" id="image5" value="image" checked="checked"/> <?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption5" class="wli-woosticker-radio" id="text4" value="text"/> <?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="category_sticker_option" name="category_sticker_option" value="image"/>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="category_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_image_width" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="category_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_image_height" value="" class="small-text"><p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_text"><?php _e( 'Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="category_sticker_text" name="category_sticker_text" value=""></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_type"><?php _e( 'Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="category_sticker_type" name="category_sticker_type"><option value="ribbon"><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php _e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_text_fontcolor"><?php _e( 'Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="category_sticker_text_fontcolor" class="wli_color_picker" name="category_sticker_text_fontcolor" value="#ffffff"/></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_text_backcolor"><?php _e( 'Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="category_sticker_text_backcolor" class="wli_color_picker" name="category_sticker_text_backcolor" value="#000000"/></td>
					</tr>
					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_text_padding_top" value="category_sticker_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="category_sticker_text_padding_right" value="category_sticker_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="category_sticker_text_padding_bottom" value="category_sticker_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="category_sticker_text_padding_left" value="category_sticker_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					<tr class="custom_option custom_optimage">
						<th scope="row"><label><?php _e( 'Add your sticker image:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="category_sticker_image" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 70px;">
								<input type="hidden" id="category_sticker_image_id" class="wsbw_upload_img_id" name="category_sticker_image_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
							<p class="description"><?php _e( 'Upload your sticker image which you want to display on this category.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<script>
		jQuery( document ).ajaxComplete( function( event, request, options ) {
			if ( request && 4 === request.readyState && 200 === request.status
				&& options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

				var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
				if ( ! res || res.errors ) {
					return;
				}
				jQuery('.wsbw_tab_content input[type="number"], .wsbw_tab_content input[type="text"]').val('');
				jQuery('.wsbw_tab_content select').prop('selectedIndex', 0);
				jQuery('.wsbw_tab_content input[type="radio"][value="image"]').prop('checked', true);
				jQuery('.custom_optimage').show();
				jQuery('.custom_opttext').hide();
				jQuery(".wp-picker-clear").click();
				jQuery(".wsbw_remove_image_button").click();
				return;
			}
		} );
	</script>
	<?php
	}

	/**
	 * Category sticker fields.
	 */
	public function edit_category_fields( $term ) {

		//Get WC placeholder image
		$placeholder_img = wc_placeholder_img_src();

		//Get new product sticker options
		$enable_np_sticker = get_term_meta( $term->term_id, 'enable_np_sticker', true );
		$np_no_of_days = get_term_meta( $term->term_id, 'np_no_of_days', true );
		$np_sticker_pos = get_term_meta( $term->term_id, 'np_sticker_pos', true );
		$np_sticker_left_right = get_term_meta( $term->term_id, 'np_sticker_left_right', true );
		$np_sticker_top = get_term_meta( $term->term_id, 'np_sticker_top', true );
		$np_product_option = get_term_meta( $term->term_id, 'np_product_option', true );
		$np_sticker_image_width = get_term_meta( $term->term_id, 'np_sticker_image_width', true );
		$np_sticker_image_height = get_term_meta( $term->term_id, 'np_sticker_image_height', true );
		$np_product_custom_text = get_term_meta( $term->term_id, 'np_product_custom_text', true );
		$np_sticker_type = get_term_meta( $term->term_id, 'np_sticker_type', true );
		$np_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'np_product_custom_text_fontcolor', true );
		$np_product_custom_text_backcolor = get_term_meta( $term->term_id, 'np_product_custom_text_backcolor', true );
		
		$np_product_custom_text_padding_top = get_term_meta( $term->term_id, 'np_product_custom_text_padding_top', true );
		$np_product_custom_text_padding_right = get_term_meta( $term->term_id, 'np_product_custom_text_padding_right', true );
		$np_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'np_product_custom_text_padding_bottom', true );
		$np_product_custom_text_padding_left = get_term_meta( $term->term_id, 'np_product_custom_text_padding_left', true );

		$np_sticker_rotate = get_term_meta( $term->term_id, 'np_sticker_rotate', true );

		$np_sticker_category_animation_type = get_term_meta( $term->term_id, 'np_sticker_category_animation_type', true );
		$np_sticker_category_animation_direction = get_term_meta( $term->term_id, 'np_sticker_category_animation_direction', true );
		$np_sticker_category_animation_scale = get_term_meta( $term->term_id, 'np_sticker_category_animation_scale', true );
		$np_sticker_category_animation_iteration_count = get_term_meta( $term->term_id, 'np_sticker_category_animation_iteration_count', true );
		$np_sticker_category_animation_type_delay = get_term_meta( $term->term_id, 'np_sticker_category_animation_type_delay', true );

		$enable_np_product_schedule_sticker_category = get_term_meta( $term->term_id, 'enable_np_product_schedule_sticker_category', true );
		$np_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'np_product_schedule_start_sticker_date_time', true );
		$np_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'np_product_schedule_end_sticker_date_time', true );
		$np_product_schedule_option = get_term_meta( $term->term_id, 'np_product_schedule_option', true );
		$np_schedule_sticker_image_width = get_term_meta( $term->term_id, 'np_schedule_sticker_image_width', true );
		$np_schedule_sticker_image_height = get_term_meta( $term->term_id, 'np_schedule_sticker_image_height', true );
		$np_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'np_schedule_sticker_custom_id', true );
		$np_product_schedule_custom_text = get_term_meta( $term->term_id, 'np_product_schedule_custom_text', true );
		$np_schedule_sticker_type = get_term_meta( $term->term_id, 'np_schedule_sticker_type', true );
		$np_schedule_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'np_schedule_product_custom_text_fontcolor', true );
		$np_schedule_product_custom_text_backcolor = get_term_meta( $term->term_id, 'np_schedule_product_custom_text_backcolor', true );
		$np_product_schedule_custom_text_padding_top = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_top', true );
		$np_product_schedule_custom_text_padding_right = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_right', true );
		$np_product_schedule_custom_text_padding_bottom = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_bottom', true );
		$np_product_schedule_custom_text_padding_left = get_term_meta( $term->term_id, 'np_product_schedule_custom_text_padding_left', true );

		$np_sticker_custom_id = get_term_meta( $term->term_id, 'np_sticker_custom_id', true );
		if ( !empty( $np_sticker_custom_id ) ) {
			$np_image = wp_get_attachment_thumb_url( $np_sticker_custom_id );
		} else {
			$np_image = $placeholder_img;
		}
		$show_text_np_product  = ($np_product_option == "text") ? 'style="display: table-row;"' : '';
		$show_image_np_product = ( empty( $np_product_option ) || $np_product_option == "image" ) ? 'style="display: table-row;"' : '';

		$np_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'np_schedule_sticker_custom_id', true );
		if ( !empty( $np_schedule_sticker_custom_id ) ) {
			$np_schedule_image = wp_get_attachment_thumb_url( $np_schedule_sticker_custom_id );
		} else {
			$np_schedule_image = $placeholder_img;
		}
		$show_text_np_schedule_product  = ($np_product_schedule_option == "text_schedule") ? 'style="display: table-row;"' : '';
		$show_image_np_schedule_product = ( empty( $np_product_schedule_option ) || $np_product_schedule_option == "image_schedule" ) ? 'style="display: table-row;"' : '';

		//Get product sale sticker options
		$enable_pos_sticker = get_term_meta( $term->term_id, 'enable_pos_sticker', true );
		$pos_sticker_pos = get_term_meta( $term->term_id, 'pos_sticker_pos', true );
		$pos_sticker_left_right = get_term_meta( $term->term_id, 'pos_sticker_left_right', true );
		$pos_sticker_top = get_term_meta( $term->term_id, 'pos_sticker_top', true );
		$pos_product_option = get_term_meta( $term->term_id, 'pos_product_option', true );
		$pos_sticker_image_width = get_term_meta( $term->term_id, 'pos_sticker_image_width', true );
		$pos_sticker_image_height = get_term_meta( $term->term_id, 'pos_sticker_image_height', true );
		$pos_product_custom_text = get_term_meta( $term->term_id, 'pos_product_custom_text', true );
		$pos_sticker_type = get_term_meta( $term->term_id, 'pos_sticker_type', true );
		$pos_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'pos_product_custom_text_fontcolor', true );
		$pos_product_custom_text_backcolor = get_term_meta( $term->term_id, 'pos_product_custom_text_backcolor', true );
		$pos_product_custom_text_padding_top = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_top', true );
		$pos_product_custom_text_padding_right = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_right', true );
		$pos_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_bottom', true );
		$pos_product_custom_text_padding_left = get_term_meta( $term->term_id, 'pos_product_custom_text_padding_left', true );

		$pos_sticker_rotate = get_term_meta( $term->term_id, 'pos_sticker_rotate', true );

		$pos_sticker_category_animation_type = get_term_meta( $term->term_id, 'pos_sticker_category_animation_type', true );
		$pos_sticker_category_animation_direction = get_term_meta( $term->term_id, 'pos_sticker_category_animation_direction', true );
		$pos_sticker_category_animation_scale = get_term_meta( $term->term_id, 'pos_sticker_category_animation_scale', true );
		$pos_sticker_category_animation_iteration_count = get_term_meta( $term->term_id, 'pos_sticker_category_animation_iteration_count', true );
		$pos_sticker_category_animation_type_delay = get_term_meta( $term->term_id, 'pos_sticker_category_animation_type_delay', true );

		$enable_pos_product_schedule_sticker_category = get_term_meta( $term->term_id, 'enable_pos_product_schedule_sticker_category', true );
		$pos_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'pos_product_schedule_start_sticker_date_time', true );
		$pos_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'pos_product_schedule_end_sticker_date_time', true );
		$pos_product_schedule_option = get_term_meta( $term->term_id, 'pos_product_schedule_option', true );
		$pos_schedule_sticker_image_width = get_term_meta( $term->term_id, 'pos_schedule_sticker_image_width', true );
		$pos_schedule_sticker_image_height = get_term_meta( $term->term_id, 'pos_schedule_sticker_image_height', true );
		$pos_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'pos_schedule_sticker_custom_id', true );
		$pos_product_schedule_custom_text = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text', true );
		$pos_schedule_sticker_type = get_term_meta( $term->term_id, 'pos_schedule_sticker_type', true );
		$pos_schedule_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'pos_schedule_product_custom_text_fontcolor', true );
		$pos_schedule_product_custom_text_backcolor = get_term_meta( $term->term_id, 'pos_schedule_product_custom_text_backcolor', true );
		$pos_product_schedule_custom_text_padding_top = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_top', true );
		$pos_product_schedule_custom_text_padding_right = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_right', true );
		$pos_product_schedule_custom_text_padding_bottom = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_bottom', true );
		$pos_product_schedule_custom_text_padding_left = get_term_meta( $term->term_id, 'pos_product_schedule_custom_text_padding_left', true );
		
		$pos_sticker_custom_id = get_term_meta( $term->term_id, 'pos_sticker_custom_id', true );
		if ( !empty( $pos_sticker_custom_id ) ) {
			$pos_image = wp_get_attachment_thumb_url( $pos_sticker_custom_id );
		} else {
			$pos_image = $placeholder_img;
		}
		$show_text_pos_sticker  = ($pos_product_option == "text") ? 'style="display: table-row;"' : '';
		$show_image_pos_sticker = ( empty( $pos_product_option) || $pos_product_option == "image" ) ? 'style="display: table-row;"' : '';

		$pos_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'pos_schedule_sticker_custom_id', true );
		if ( !empty( $pos_schedule_sticker_custom_id ) ) {
			$pos_schedule_image = wp_get_attachment_thumb_url( $pos_schedule_sticker_custom_id );
		} else {
			$pos_schedule_image = $placeholder_img;
		}
		$show_text_pos_schedule_product  = ($pos_product_schedule_option == "text_schedule") ? 'style="display: table-row;"' : '';
		$show_image_pos_schedule_product = ( empty( $pos_product_schedule_option ) || $pos_product_schedule_option == "image_schedule" ) ? 'style="display: table-row;"' : '';

		//Get soldout product sticker options
		$enable_sop_sticker = get_term_meta( $term->term_id, 'enable_sop_sticker', true );
		$sop_sticker_pos = get_term_meta( $term->term_id, 'sop_sticker_pos', true );
		$sop_sticker_left_right = get_term_meta( $term->term_id, 'sop_sticker_left_right', true );
		$sop_sticker_top = get_term_meta( $term->term_id, 'sop_sticker_top', true );
		$sop_product_option = get_term_meta( $term->term_id, 'sop_product_option', true );
		$sop_sticker_image_width = get_term_meta( $term->term_id, 'sop_sticker_image_width', true );
		$sop_sticker_image_height = get_term_meta( $term->term_id, 'sop_sticker_image_height', true );
		$sop_product_custom_text = get_term_meta( $term->term_id, 'sop_product_custom_text', true );
		$sop_sticker_type = get_term_meta( $term->term_id, 'sop_sticker_type', true );
		$sop_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'sop_product_custom_text_fontcolor', true );
		$sop_product_custom_text_backcolor = get_term_meta( $term->term_id, 'sop_product_custom_text_backcolor', true );
		$sop_product_custom_text_padding_top = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_top', true );
		$sop_product_custom_text_padding_right = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_right', true );
		$sop_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_bottom', true );
		$sop_product_custom_text_padding_left = get_term_meta( $term->term_id, 'sop_product_custom_text_padding_left', true );

		$sop_sticker_rotate = get_term_meta( $term->term_id, 'sop_sticker_rotate', true );

		$sop_sticker_category_animation_type = get_term_meta( $term->term_id, 'sop_sticker_category_animation_type', true );
		$sop_sticker_category_animation_direction = get_term_meta( $term->term_id, 'sop_sticker_category_animation_direction', true );
		$sop_sticker_category_animation_scale = get_term_meta( $term->term_id, 'sop_sticker_category_animation_scale', true );
		$sop_sticker_category_animation_iteration_count = get_term_meta( $term->term_id, 'sop_sticker_category_animation_iteration_count', true );
		$sop_sticker_category_animation_type_delay = get_term_meta( $term->term_id, 'sop_sticker_category_animation_type_delay', true );

		$enable_sop_product_schedule_sticker_category = get_term_meta( $term->term_id, 'enable_sop_product_schedule_sticker_category', true );
		$sop_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'sop_product_schedule_start_sticker_date_time', true );
		$sop_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'sop_product_schedule_end_sticker_date_time', true );
		$sop_product_schedule_option = get_term_meta( $term->term_id, 'sop_product_schedule_option', true );
		$sop_schedule_sticker_image_width = get_term_meta( $term->term_id, 'sop_schedule_sticker_image_width', true );
		$sop_schedule_sticker_image_height = get_term_meta( $term->term_id, 'sop_schedule_sticker_image_height', true );
		$sop_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'sop_schedule_sticker_custom_id', true );
		$sop_product_schedule_custom_text = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text', true );
		$sop_schedule_sticker_type = get_term_meta( $term->term_id, 'sop_schedule_sticker_type', true );
		$sop_schedule_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'sop_schedule_product_custom_text_fontcolor', true );
		$sop_schedule_product_custom_text_backcolor = get_term_meta( $term->term_id, 'sop_schedule_product_custom_text_backcolor', true );
		$sop_product_schedule_custom_text_padding_top = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_top', true );
		$sop_product_schedule_custom_text_padding_right = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_right', true );
		$sop_product_schedule_custom_text_padding_bottom = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_bottom', true );
		$sop_product_schedule_custom_text_padding_left = get_term_meta( $term->term_id, 'sop_product_schedule_custom_text_padding_left', true );

		$sop_sticker_custom_id = get_term_meta( $term->term_id, 'sop_sticker_custom_id', true );
		if ( !empty( $sop_sticker_custom_id ) ) {
			$sop_image = wp_get_attachment_thumb_url( $sop_sticker_custom_id );
		} else {
			$sop_image = $placeholder_img;
		}
		$show_text_sop_sticker  = ($sop_product_option == "text") ? 'style="display: table-row;"' : '';
		$show_image_sop_sticker = ( empty( $sop_product_option ) || $sop_product_option == "image" ) ? 'style="display: table-row;"' : '';

		$sop_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'sop_schedule_sticker_custom_id', true );
		if ( !empty( $sop_schedule_sticker_custom_id ) ) {
			$sop_schedule_image = wp_get_attachment_thumb_url( $sop_schedule_sticker_custom_id );
		} else {
			$sop_schedule_image = $placeholder_img;
		}
		$show_text_sop_schedule_product  = ($sop_product_schedule_option == "text_schedule") ? 'style="display: table-row;"' : '';
		$show_image_sop_schedule_product = ( empty( $sop_product_schedule_option ) || $sop_product_schedule_option == "image_schedule" ) ? 'style="display: table-row;"' : '';

		//Get custom product sticker options
		$enable_cust_sticker = get_term_meta( $term->term_id, 'enable_cust_sticker', true );
		$cust_sticker_pos = get_term_meta( $term->term_id, 'cust_sticker_pos', true );
		$cust_sticker_left_right = get_term_meta( $term->term_id, 'cust_sticker_left_right', true );
		$cust_sticker_top = get_term_meta( $term->term_id, 'cust_sticker_top', true );
		$cust_product_option = get_term_meta( $term->term_id, 'cust_product_option', true );
		$cust_sticker_image_width = get_term_meta( $term->term_id, 'cust_sticker_image_width', true );
		$cust_sticker_image_height = get_term_meta( $term->term_id, 'cust_sticker_image_height', true );
		$cust_product_custom_text = get_term_meta( $term->term_id, 'cust_product_custom_text', true );
		$cust_sticker_type = get_term_meta( $term->term_id, 'cust_sticker_type', true );
		$cust_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'cust_product_custom_text_fontcolor', true );
		$cust_product_custom_text_backcolor = get_term_meta( $term->term_id, 'cust_product_custom_text_backcolor', true );
		$cust_product_custom_text_padding_top = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_top', true );
		$cust_product_custom_text_padding_right = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_right', true );
		$cust_product_custom_text_padding_bottom = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_bottom', true );
		$cust_product_custom_text_padding_left = get_term_meta( $term->term_id, 'cust_product_custom_text_padding_left', true );

		$cust_sticker_rotate = get_term_meta( $term->term_id, 'cust_sticker_rotate', true );

		$cust_sticker_category_animation_type = get_term_meta( $term->term_id, 'cust_sticker_category_animation_type', true );
		$cust_sticker_category_animation_direction = get_term_meta( $term->term_id, 'cust_sticker_category_animation_direction', true );
		$cust_sticker_category_animation_scale = get_term_meta( $term->term_id, 'cust_sticker_category_animation_scale', true );
		$cust_sticker_category_animation_iteration_count = get_term_meta( $term->term_id, 'cust_sticker_category_animation_iteration_count', true );
		$cust_sticker_category_animation_type_delay = get_term_meta( $term->term_id, 'cust_sticker_category_animation_type_delay', true );

		$enable_cust_product_schedule_sticker_category = get_term_meta( $term->term_id, 'enable_cust_product_schedule_sticker_category', true );
		$cust_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'cust_product_schedule_start_sticker_date_time', true );
		$cust_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'cust_product_schedule_end_sticker_date_time', true );
		$cust_product_schedule_option = get_term_meta( $term->term_id, 'cust_product_schedule_option', true );
		$cust_schedule_sticker_image_width = get_term_meta( $term->term_id, 'cust_schedule_sticker_image_width', true );
		$cust_schedule_sticker_image_height = get_term_meta( $term->term_id, 'cust_schedule_sticker_image_height', true );
		$cust_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'cust_schedule_sticker_custom_id', true );
		$cust_product_schedule_custom_text = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text', true );
		$cust_schedule_sticker_type = get_term_meta( $term->term_id, 'cust_schedule_sticker_type', true );
		$cust_schedule_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'cust_schedule_product_custom_text_fontcolor', true );
		$cust_schedule_product_custom_text_backcolor = get_term_meta( $term->term_id, 'cust_schedule_product_custom_text_backcolor', true );
		$cust_product_schedule_custom_text_padding_top = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_top', true );
		$cust_product_schedule_custom_text_padding_right = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_right', true );
		$cust_product_schedule_custom_text_padding_bottom = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_bottom', true );
		$cust_product_schedule_custom_text_padding_left = get_term_meta( $term->term_id, 'cust_product_schedule_custom_text_padding_left', true );

		$cust_sticker_custom_id = get_term_meta( $term->term_id, 'cust_sticker_custom_id', true );
		if ( !empty( $cust_sticker_custom_id ) ) {
			$cust_image = wp_get_attachment_thumb_url( $cust_sticker_custom_id );
		} else {
			$cust_image = $placeholder_img;
		}
		$show_text_cust_product  = ($cust_product_option == "text") ? 'style="display: table-row;"' : '';
		$show_image_cust_product = ( empty( $cust_product_option ) || $cust_product_option == "image" ) ? 'style="display: table-row;"' : '';

		$cust_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'cust_schedule_sticker_custom_id', true );
		if ( !empty( $cust_schedule_sticker_custom_id ) ) {
			$cust_schedule_image = wp_get_attachment_thumb_url( $cust_schedule_sticker_custom_id );
		} else {
			$cust_schedule_image = $placeholder_img;
		}
		$show_text_cust_schedule_product  = ($cust_product_schedule_option == "text_schedule") ? 'style="display: table-row;"' : '';
		$show_image_cust_schedule_product = ( empty( $cust_product_schedule_option ) || $cust_product_schedule_option == "image_schedule" ) ? 'style="display: table-row;"' : '';

		//Get category sticker options
		$enable_category_sticker = get_term_meta( $term->term_id, 'enable_category_sticker', true );
		$category_sticker_pos 	 = get_term_meta( $term->term_id, 'category_sticker_pos', true );
		$category_sticker_left_right = get_term_meta( $term->term_id, 'category_sticker_left_right', true );
		$category_sticker_top 	 = get_term_meta( $term->term_id, 'category_sticker_top', true );
		$category_sticker_option = get_term_meta( $term->term_id, 'category_sticker_option', true );
		$category_sticker_image_width = get_term_meta( $term->term_id, 'category_sticker_image_width', true );
		$category_sticker_image_height = get_term_meta( $term->term_id, 'category_sticker_image_height', true );
		$category_sticker_text 	 = get_term_meta( $term->term_id, 'category_sticker_text', true );
		$category_sticker_type 	 = get_term_meta( $term->term_id, 'category_sticker_type', true );
		$category_sticker_text_fontcolor = get_term_meta( $term->term_id, 'category_sticker_text_fontcolor', true );
		$category_sticker_text_backcolor = get_term_meta( $term->term_id, 'category_sticker_text_backcolor', true );
		$category_sticker_text_padding_top = get_term_meta( $term->term_id, 'category_sticker_text_padding_top', true );
		$category_sticker_text_padding_right = get_term_meta( $term->term_id, 'category_sticker_text_padding_right', true );
		$category_sticker_text_padding_bottom = get_term_meta( $term->term_id, 'category_sticker_text_padding_bottom', true );
		$category_sticker_text_padding_left = get_term_meta( $term->term_id, 'category_sticker_text_padding_left', true );

		$category_sticker_sticker_rotate = get_term_meta( $term->term_id, 'category_sticker_sticker_rotate', true );

		$category_sticker_sticker_category_animation_type = get_term_meta( $term->term_id, 'category_sticker_sticker_category_animation_type', true );
		$category_sticker_sticker_category_animation_direction = get_term_meta( $term->term_id, 'category_sticker_sticker_category_animation_direction', true );
		$category_sticker_sticker_category_animation_scale = get_term_meta( $term->term_id, 'category_sticker_sticker_category_animation_scale', true );
		$category_sticker_sticker_category_animation_iteration_count = get_term_meta( $term->term_id, 'category_sticker_sticker_category_animation_iteration_count', true );
		$category_sticker_sticker_category_animation_type_delay = get_term_meta( $term->term_id, 'category_sticker_sticker_category_animation_type_delay', true );

		$enable_category_product_schedule_sticker_category = get_term_meta( $term->term_id, 'enable_category_product_schedule_sticker_category', true );
		$category_product_schedule_start_sticker_date_time = get_term_meta( $term->term_id, 'category_product_schedule_start_sticker_date_time', true );
		$category_product_schedule_end_sticker_date_time = get_term_meta( $term->term_id, 'category_product_schedule_end_sticker_date_time', true );
		$category_product_schedule_option = get_term_meta( $term->term_id, 'category_product_schedule_option', true );
		$category_schedule_sticker_image_width = get_term_meta( $term->term_id, 'category_schedule_sticker_image_width', true );
		$category_schedule_sticker_image_height = get_term_meta( $term->term_id, 'category_schedule_sticker_image_height', true );
		$category_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'category_schedule_sticker_custom_id', true );
		$category_product_schedule_custom_text = get_term_meta( $term->term_id, 'category_product_schedule_custom_text', true );
		$category_schedule_sticker_type = get_term_meta( $term->term_id, 'category_schedule_sticker_type', true );
		$category_schedule_product_custom_text_fontcolor = get_term_meta( $term->term_id, 'category_schedule_product_custom_text_fontcolor', true );
		$category_schedule_product_custom_text_backcolor = get_term_meta( $term->term_id, 'category_schedule_product_custom_text_backcolor', true );
		$category_product_schedule_custom_text_padding_top = get_term_meta( $term->term_id, 'category_product_schedule_custom_text_padding_top', true );
		$category_product_schedule_custom_text_padding_right = get_term_meta( $term->term_id, 'category_product_schedule_custom_text_padding_right', true );
		$category_product_schedule_custom_text_padding_bottom = get_term_meta( $term->term_id, 'category_product_schedule_custom_text_padding_bottom', true );
		$category_product_schedule_custom_text_padding_left = get_term_meta( $term->term_id, 'category_product_schedule_custom_text_padding_left', true );

		$category_sticker_image_id = get_term_meta( $term->term_id, 'category_sticker_image_id', true );
		if ( !empty( $category_sticker_image_id ) ) {
			$category_image = wp_get_attachment_thumb_url( $category_sticker_image_id );
		} else {
			$category_image = $placeholder_img;
		}
		$show_text_sticker 	= ($category_sticker_option == "text") ? 'style="display: table-row;"' : '';
		$show_image_sticker = ( empty( $category_sticker_option ) || $category_sticker_option == "image" ) ? 'style="display: table-row;"' : '';

		$category_schedule_sticker_custom_id = get_term_meta( $term->term_id, 'category_schedule_sticker_custom_id', true );
		if ( !empty( $category_schedule_sticker_custom_id ) ) {
			$category_schedule_image = wp_get_attachment_thumb_url( $category_schedule_sticker_custom_id );
		} else {
			$category_schedule_image = $placeholder_img;
		}
		$show_text_category_schedule_product  = ($category_product_schedule_option == "text_schedule") ? 'style="display: table-row;"' : '';
		$show_image_category_schedule_product = ( empty( $category_product_schedule_option ) || $category_product_schedule_option == "image_schedule" ) ? 'style="display: table-row;"' : '';

		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = date($format, $current_timestamp);

		?>
		<tr class="form-field wsbw-sticker-options-wrap">
			<th scope="row" valign="top"><label><?php _e( 'Sticker Options', 'woocommerce' ); ?></label></th>
			<td>
				<h2 class="nav-tab-wrapper">
					<a class="nav-tab nav-tab-active" href="#wsbw_new_products"><?php _e( "New Products", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_products_sale"><?php _e( "Products On Sale", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_soldout_products"><?php _e( "Soldout Products", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_cust_products"><?php _e( "Custom Product Sticker", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_category_sticker"><?php _e( "Category Sticker", 'woo-stickers-by-webline' );?></a>
				</h2>
				<table id="wsbw_new_products" class="wsbw_tab_content">
					<tr>
						<th scope="row" valign="top"><label for="enable_np_sticker"><?php _e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_np_sticker" name="enable_np_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_np_sticker, 'yes');?>><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_np_sticker, 'no');?>><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top"><label for="np_no_of_days"><?php _e( 'Number of Days for New Product:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_no_of_days" value="<?php echo absint( $np_no_of_days ); ?>" class="small-text">
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="np_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="np_sticker_pos" name="np_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $np_sticker_pos, 'left');?>><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $np_sticker_pos, 'right');?>><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top"><label for="np_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_top" value="<?php echo ( $np_sticker_top ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top"><label for="np_sticker_left_right"><?php _e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_left_right" value="<?php echo ( $np_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>							
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
							<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<input type="number" name="np_sticker_rotate" value="<?php echo ( $np_sticker_rotate ); ?>" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else{ 
								?>
									<td>
										<div class="wosbw-pro-ribbon-banner">	
											<input type="number" class="small-text file-input" name="np_sticker_rotate" value="<?php echo ( $np_sticker_rotate ); ?>" class="small-text" disabled><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>

											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
										</div>
									</td>
								<?php
							}
						?>
					</tr>
					<tr>
						<th scope="row"><label for="np_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<select name="np_sticker_category_animation_type" id="np_sticker_category_animation_type">
											<?php
											$animation_options = array(
												'none'      => __( 'none', 'woo-stickers-by-webline' ),
												'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
												'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
												'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
												'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
												'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
											);

											$saved_value = $np_sticker_category_animation_type;

											foreach ($animation_options as $value => $label) {
												$selected = ($saved_value == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else {
								?>									
									<td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="np_sticker_category_animation_type" class="small-text file-input" id="np_sticker_category_animation_type" disabled>
												<?php
												$animation_options = array(
													'none'      => __( 'none', 'woo-stickers-by-webline' ),
													'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
													'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
													'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
													'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
													'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
												);

												$saved_value = $np_sticker_category_animation_type;

												foreach ($animation_options as $value => $label) {
													$selected = ($saved_value == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
										</div>
									</td>									
								<?php
							}
						?>
						
					</tr>
					<?php
					if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr id="zoominout-options-new-edit-cat" style="display:none;">
							<th scope="row"><label for="np_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="np_sticker_category_animation_scale" step="any" value="<?php echo ( $np_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="np_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="np_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = $np_sticker_category_animation_direction;

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="np_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="np_sticker_category_animation_iteration_count" value="<?php echo ( $np_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="np_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="np_sticker_category_animation_type_delay" value="<?php echo ( $np_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row"><label for="enable_np_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
									<select name="enable_np_product_schedule_sticker_category" id="enable_np_product_schedule_sticker_category">
										<?php
										$enable_options = array(
											'yes' => __( 'Yes', 'woo-stickers-by-webline' ),
											'no'  => __( 'No', 'woo-stickers-by-webline' ),
										);

										$saved_value_enable = !empty($enable_np_product_schedule_sticker_category) ? $enable_np_product_schedule_sticker_category : 'no';

										foreach ($enable_options as $value => $label) {
											$selected = ($saved_value_enable == $value) ? 'selected' : '';
											echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
										}
										?>
									</select>
										<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as NEW in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else {
								?>
									<td>
										<div class="wosbw-pro-ribbon-banner">
											<select class="small-text file-input" disabled>
												<?php
												$enable_options = array(
													'no'      => __( 'No', 'woo-stickers-by-webline' ),
												);

												$saved_value_enable = $enable_np_product_schedule_sticker_category;

												foreach ($enable_options as $value => $label) {
													$selected = ($saved_value_enable == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as NEW in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>										
											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
										</div>										
									</td>
								<?php
								}
						?>
						
					</tr>

					<?php
					if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_start_sticker_date_time" name="np_product_schedule_start_sticker_date_time" 
									value="<?php echo ( $np_product_schedule_start_sticker_date_time ); ?>"
									>
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_end_sticker_date_time" name="np_product_schedule_end_sticker_date_time" 
									value="<?php echo ( $np_product_schedule_end_sticker_date_time ); ?>" 
									min="<?php echo $formatted_date_time; ?>">
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label for="np_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt np_product_schedule_option">
									<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule_np" value="image_schedule" <?php if($np_product_schedule_option == 'image_schedule' || $np_product_schedule_option == '') { echo 'checked'; } ?>/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule_np" value="text_schedule" <?php if($np_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="np_product_schedule_option" name="np_product_schedule_option" value="<?php if($np_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $np_product_schedule_option ); } ?>"/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_np_schedule_product;?>>
							<th scope="row" valign="top"><label for="np_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="np_schedule_sticker_image_width" name="np_schedule_sticker_image_width" value="<?php echo ( $np_schedule_sticker_image_width ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_np_schedule_product;?>>
							<th scope="row" valign="top"><label for="np_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="np_schedule_sticker_image_height" name="np_schedule_sticker_image_height" value="<?php echo ( $np_schedule_sticker_image_height ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_np_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="np_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="np_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($np_schedule_image); ?>" width="60px" height="60px" /></div>
								<div style="line-height: 60px;">
									<input type="hidden" id="np_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="np_schedule_sticker_custom_id" value="<?php echo absint( $np_schedule_sticker_custom_id ); ?>" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_np"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_np"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_np_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="np_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="np_product_schedule_custom_text" name="np_product_schedule_custom_text" value="<?php echo esc_attr( $np_product_schedule_custom_text ); ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_np_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="np_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='np_schedule_sticker_type'
									name="np_schedule_sticker_type">
									<option value='ribbon'
										<?php selected( $np_schedule_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
									<option value='round'
										<?php selected( $np_schedule_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_np" <?php echo $show_text_np_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="np_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="np_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="np_schedule_product_custom_text_fontcolor" value="<?php echo ($np_schedule_product_custom_text_fontcolor) ? esc_attr( $np_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_np" <?php echo $show_text_np_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="np_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="np_schedule_product_custom_text_backcolor" class="wli_color_picker" name="np_schedule_product_custom_text_backcolor" value="<?php echo ($np_schedule_product_custom_text_backcolor) ? esc_attr( $np_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_np_schedule_product;?>>
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="np_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="np_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_top ); ?>"/>
								<input type="number" id="np_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="np_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_right ); ?>"/>
								<input type="number" id="np_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="np_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_bottom ); ?>"/>
								<input type="number" id="np_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="np_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_left ); ?>"/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row" valign="top">
							<label for="np_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt np_product_option">
								<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($np_product_option == 'image' || $np_product_option == '') { echo 'checked'; } ?>/>
								<label for="image"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($np_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="np_product_option" name="np_product_option" value="<?php if($np_product_option == '') { echo "image"; } else { echo esc_attr( $np_product_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo $show_image_np_product;?>>
						<th scope="row" valign="top"><label for="np_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_image_width" value="<?php echo ( $np_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_np_product;?>>
						<th scope="row" valign="top"><label for="np_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_image_height" value="<?php echo ( $np_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for="np_product_custom_text"><?php _e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_custom_text" name="np_product_custom_text" value="<?php echo esc_attr( $np_product_custom_text ); ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for="np_sticker_type"><?php _e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='np_sticker_type'
								name="np_sticker_type">
								<option value='ribbon'
									<?php selected( $np_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $np_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for="np_product_custom_text_fontcolor"><?php _e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_custom_text_fontcolor" class="wli_color_picker" name="np_product_custom_text_fontcolor" value="<?php echo ($np_product_custom_text_fontcolor) ? esc_attr( $np_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for="np_product_custom_text_backcolor"><?php _e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_custom_text_backcolor" class="wli_color_picker" name="np_product_custom_text_backcolor" value="<?php echo esc_attr( $np_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="np_product_custom_text_padding_top" placeholder="Top" class="small-text" name="np_product_custom_text_padding_top" value="<?php echo esc_attr( $np_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="np_product_custom_text_padding_right" placeholder="Right" class="small-text" name="np_product_custom_text_padding_right" value="<?php echo esc_attr( $np_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="np_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="np_product_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="np_product_custom_text_padding_left" placeholder="Left" class="small-text" name="np_product_custom_text_padding_left" value="<?php echo esc_attr( $np_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo $show_image_np_product;?>>
						<th scope="row" valign="top">
							<label for="np_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($np_image); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="np_sticker_custom_id" class="wsbw_upload_img_id" name="np_sticker_custom_id" value="<?php echo absint( $np_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</table>
				<table id="wsbw_products_sale" class="wsbw_tab_content" style="display: none;">
					<tr>
						<th scope="row" valign="top"><label for="enable_pos_sticker"><?php _e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_pos_sticker" name="enable_pos_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_pos_sticker, 'yes');?>><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_pos_sticker, 'no');?>><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="pos_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="pos_sticker_pos" name="pos_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $pos_sticker_pos, 'left');?>><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $pos_sticker_pos, 'right');?>><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="pos_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_top" value="<?php echo ( $pos_sticker_top ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top"><label for="pos_sticker_left_right"><?php _e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_left_right" value="<?php echo ( $pos_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><label for="pos_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<input type="number" name="pos_sticker_rotate" value="<?php echo ( $pos_sticker_rotate ); ?>" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else {
								?>
									<td>
										<div class="wosbw-pro-ribbon-banner">
											<input type="number" class="small-text file-input" name="pos_sticker_rotate" value="<?php echo ( $pos_sticker_rotate ); ?>" class="small-text" disabled><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>
											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
										</div>
									</td>
								<?php
							}
						?>
						
					</tr>
					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<select name="pos_sticker_category_animation_type" id="pos_sticker_category_animation_type">
											<?php
											$animation_options = array(
												'none'      => __( 'none', 'woo-stickers-by-webline' ),
												'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
												'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
												'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
												'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
												'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
											);

											$saved_value = $pos_sticker_category_animation_type;

											foreach ($animation_options as $value => $label) {
												$selected = ($saved_value == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else {
								?>
									<td>
									<div class="wosbw-pro-ribbon-banner">
										<select name="pos_sticker_category_animation_type" class="small-text file-input" id="pos_sticker_category_animation_type" disabled>
												<?php
												$animation_options = array(
													'none'      => __( 'none', 'woo-stickers-by-webline' ),
													'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
													'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
													'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
													'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
													'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
												);

												$saved_value = $pos_sticker_category_animation_type;

												foreach ($animation_options as $value => $label) {
													$selected = ($saved_value == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
										</select>
										<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>	

										<div class="ribbon">
											<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
												<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
												<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
												<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
												<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
												<defs>
												<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
												<stop stop-color="#FDAB00"/>
												<stop offset="1" stop-color="#CD8F0D"/>
												</linearGradient>
												<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
												<stop stop-color="#FDAB00"/>
												<stop offset="1" stop-color="#CD8F0D"/>
												</linearGradient>
												</defs>
											</svg>
										</div>

										<div class="learn-more">
											<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
										</div>
									</div>
									
									</td>
								<?php
							}
						?>
							
					</tr>
					<?php if(get_option('wosbw_premium_access_allowed') == 1){ ?>

						<tr id="zoominout-options-sale-edit-cat" style="display:none;">
							<th scope="row"><label for="pos_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="pos_sticker_category_animation_scale" step="any" value="<?php echo ( $pos_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="pos_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="pos_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = $pos_sticker_category_animation_direction;

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pos_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="pos_sticker_category_animation_iteration_count" value="<?php echo ( $pos_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="pos_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="pos_sticker_category_animation_type_delay" value="<?php echo ( $pos_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>


					<tr>
						<th scope="row"><label for="enable_pos_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<select name="enable_pos_product_schedule_sticker_category" id="enable_pos_product_schedule_sticker_category">
											<?php
											$enable_options = array(
												'yes'      => __( 'Yes', 'woo-stickers-by-webline' ),
												'no'      => __( 'No', 'woo-stickers-by-webline' ),
											);

											$saved_value_enable = !empty($enable_pos_product_schedule_sticker_category) ? $enable_pos_product_schedule_sticker_category : 'no';

											foreach ($enable_options as $value => $label) {
												$selected = ($saved_value_enable == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SALE in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>										
									</td>
								<?php
							}
							else {
								?>
									<td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="enable_pos_product_schedule_sticker_category" class="small-text file-input" id="enable_pos_product_schedule_sticker_category" disabled>
												<?php
												$enable_options = array(
													'no'      => __( 'No', 'woo-stickers-by-webline' ),
												);

												$saved_value_enable = $enable_pos_product_schedule_sticker_category;

												foreach ($enable_options as $value => $label) {
													$selected = ($saved_value_enable == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SALE in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>																					
											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
									</div>
										
									</td>
								<?php
							}
						?>
						
					</tr>

					<?php if(get_option('wosbw_premium_access_allowed') == 1){ ?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_start_sticker_date_time" name="pos_product_schedule_start_sticker_date_time" 
									value="<?php echo ( $pos_product_schedule_start_sticker_date_time ); ?>"
									/>
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_end_sticker_date_time" name="pos_product_schedule_end_sticker_date_time" 
									value="<?php echo ( $pos_product_schedule_end_sticker_date_time ); ?>"
									min="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="pos_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt pos_product_schedule_option">
									<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule" id="image_schedule_pos" value="image_schedule" <?php if($pos_product_schedule_option == 'image_schedule' || $pos_product_schedule_option == '') { echo 'checked'; } ?>/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule" id="text_schedule_pos" value="text_schedule" <?php if($pos_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="pos_product_schedule_option" name="pos_product_schedule_option" value="<?php if($pos_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $pos_product_schedule_option ); } ?>"/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_pos_schedule_product;?>>
							<th scope="row" valign="top"><label for="pos_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="pos_schedule_sticker_image_width" name="pos_schedule_sticker_image_width" value="<?php echo ( $pos_schedule_sticker_image_width ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_pos_schedule_product;?>>
							<th scope="row" valign="top"><label for="pos_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="pos_schedule_sticker_image_height" name="pos_schedule_sticker_image_height" value="<?php echo ( $pos_schedule_sticker_image_height ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_pos_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="pos_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="pos_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($pos_schedule_image); ?>" width="60px" height="60px" /></div>
								<div style="line-height: 60px;">
									<input type="hidden" id="pos_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="pos_schedule_sticker_custom_id" value="<?php echo absint( $pos_schedule_sticker_custom_id ); ?>" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_pos"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_pos"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_pos_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="pos_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="pos_product_schedule_custom_text" name="pos_product_schedule_custom_text" value="<?php echo esc_attr( $pos_product_schedule_custom_text ); ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_pos_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="pos_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='pos_schedule_sticker_type'
									name="pos_schedule_sticker_type">
									<option value='ribbon'
										<?php selected( $pos_schedule_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
									<option value='round'
										<?php selected( $pos_schedule_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_pos" <?php echo $show_text_pos_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="pos_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="pos_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_fontcolor" value="<?php echo ($pos_schedule_product_custom_text_fontcolor) ? esc_attr( $pos_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_pos" <?php echo $show_text_pos_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="pos_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="pos_schedule_product_custom_text_backcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_backcolor" value="<?php echo ($pos_schedule_product_custom_text_backcolor) ? esc_attr( $pos_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_pos_schedule_product;?>>
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="pos_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="pos_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_top ); ?>"/>
								<input type="number" id="pos_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="pos_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_right ); ?>"/>
								<input type="number" id="pos_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="pos_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_bottom ); ?>"/>
								<input type="number" id="pos_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="pos_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_left ); ?>"/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>


					<tr>
						<th scope="row" valign="top">
							<label for="pos_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt pos_product_option">
								<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="image1" value="image" <?php if($pos_product_option == 'image' || $pos_product_option == '') { echo 'checked'; } ?>/>
								<label for="image1"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="text1" value="text" <?php if($pos_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text1"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="pos_product_option" name="pos_product_option" value="<?php if($pos_product_option == '') { echo "image"; } else { echo esc_attr( $pos_product_option ); } ?>"/>
							</div>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_pos_sticker;?>>
						<th scope="row" valign="top"><label for="pos_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_image_width" value="<?php echo ( $pos_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_pos_sticker;?>>
						<th scope="row" valign="top"><label for="pos_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_image_height" value="<?php echo ( $pos_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_pos_sticker;?>>
						<th scope="row" valign="top">
							<label for="pos_product_custom_text"><?php _e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_custom_text" name="pos_product_custom_text" value="<?php echo esc_attr( $pos_product_custom_text ); ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_pos_sticker;?>>
						<th scope="row" valign="top">
							<label for="pos_sticker_type"><?php _e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='pos_sticker_type'
								name="pos_sticker_type">
								<option value='ribbon'
									<?php selected( $pos_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $pos_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_pos_sticker;?>>
						<th scope="row" valign="top">
							<label for="pos_product_custom_text_fontcolor"><?php _e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_custom_text_fontcolor" class="wli_color_picker" name="pos_product_custom_text_fontcolor" value="<?php echo ($pos_product_custom_text_fontcolor) ? esc_attr( $pos_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_pos_sticker;?>>
						<th scope="row" valign="top">
							<label for="pos_product_custom_text_backcolor"><?php _e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_custom_text_backcolor" class="wli_color_picker" name="pos_product_custom_text_backcolor" value="<?php echo esc_attr( $pos_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="pos_product_custom_text_padding_top" placeholder="Top" class="small-text" name="pos_product_custom_text_padding_top" value="<?php echo esc_attr( $pos_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="pos_product_custom_text_padding_right" placeholder="Right" class="small-text" name="pos_product_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="pos_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="pos_product_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="pos_product_custom_text_padding_left" placeholder="Left" class="small-text" name="pos_product_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_pos_sticker;?>>
						<th scope="row" valign="top">
							<label for="pos_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $pos_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="pos_sticker_custom_id" class="wsbw_upload_img_id" name="pos_sticker_custom_id" value="<?php echo absint( $pos_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</table>
				<table id="wsbw_soldout_products" class="wsbw_tab_content" style="display: none;">
					<tr>
						<th scope="row" valign="top"><label for="enable_sop_sticker"><?php _e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_sop_sticker" name="enable_sop_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_sop_sticker, 'yes');?>><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_sop_sticker, 'no');?>><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="sop_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="sop_sticker_pos" name="sop_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $sop_sticker_pos, 'left');?>><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $sop_sticker_pos, 'right');?>><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="sop_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_top" value="<?php echo ( $sop_sticker_top ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top"><label for="sop_sticker_left_right"><?php _e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_left_right" value="<?php echo ( $sop_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>

						<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
							?>
								<td>
									<input type="number" name="sop_sticker_rotate" value="<?php echo ( $sop_sticker_rotate ); ?>" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
								</td>
							<?php
						}
						else {
							?>
								<td>
									<div class="wosbw-pro-ribbon-banner">
										<input type="number" class="small-text file-input" name="sop_sticker_rotate" value="<?php echo ( $sop_sticker_rotate ); ?>" class="small-text" disabled><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>

										<div class="ribbon">
											<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
												<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
												<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
												<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
												<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
												<defs>
												<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
												<stop stop-color="#FDAB00"/>
												<stop offset="1" stop-color="#CD8F0D"/>
												</linearGradient>
												<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
												<stop stop-color="#FDAB00"/>
												<stop offset="1" stop-color="#CD8F0D"/>
												</linearGradient>
												</defs>
											</svg>
										</div>

										<div class="learn-more">
											<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
										</div>
									</div>
								</td>
							</div>									
							<?php
						}
						?>
					</tr>
					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						
						
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<select name="sop_sticker_category_animation_type" id="sop_sticker_category_animation_type">
											<?php
											$animation_options = array(
												'none'      => __( 'none', 'woo-stickers-by-webline' ),
												'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
												'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
												'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
												'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
												'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
											);

											$saved_value = $sop_sticker_category_animation_type;

											foreach ($animation_options as $value => $label) {
												$selected = ($saved_value == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else {
								?>
									<td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="sop_sticker_category_animation_type" class="small-text file-input" id="sop_sticker_category_animation_type" disabled>
												<?php
												$animation_options = array(
													'none'      => __( 'none', 'woo-stickers-by-webline' ),
													'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
													'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
													'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
													'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
													'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
												);

												$saved_value = $sop_sticker_category_animation_type;

												foreach ($animation_options as $value => $label) {
													$selected = ($saved_value == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
									</td>
								<?php
							}
						?>
						
					</tr>
					<?php if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr id="zoominout-options-sold-edit-cat" style="display:none;">
							<th scope="row"><label for="sop_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="sop_sticker_category_animation_scale" step="any" value="<?php echo ( $sop_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="sop_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="sop_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = $sop_sticker_category_animation_direction;

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sop_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="sop_sticker_category_animation_iteration_count" value="<?php echo ( $sop_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="sop_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="sop_sticker_category_animation_type_delay" value="<?php echo ( $sop_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>


					<tr>
						<th scope="row"><label for="enable_sop_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
					
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<select name="enable_sop_product_schedule_sticker_category" id="enable_sop_product_schedule_sticker_category">
											<?php
											$enable_options = array(
												'yes'      => __( 'Yes', 'woo-stickers-by-webline' ),
												'no'      => __( 'No', 'woo-stickers-by-webline' ),
											);

											$saved_value_enable = !empty($enable_sop_product_schedule_sticker_category) ? $enable_sop_product_schedule_sticker_category : 'no';

											foreach ($enable_options as $value => $label) {
												$selected = ($saved_value_enable == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SOLD in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else {
								?>
									<td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="enable_sop_product_schedule_sticker_category" class="small-text file-input" id="enable_sop_product_schedule_sticker_category" disabled>
												<?php
												$enable_options = array(
													'no'      => __( 'No', 'woo-stickers-by-webline' ),
												);

												$saved_value_enable = $enable_sop_product_schedule_sticker_category;

												foreach ($enable_options as $value => $label) {
													$selected = ($saved_value_enable == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SOLD in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>										

											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
										</div>
									</td>
								<?php
							}
						?>
						
					</tr>

					<?php if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_start_sticker_date_time" name="sop_product_schedule_start_sticker_date_time" 
									value="<?php echo ( $sop_product_schedule_start_sticker_date_time ); ?>"
									/>
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_end_sticker_date_time" name="sop_product_schedule_end_sticker_date_time" 
									value="<?php echo ( $sop_product_schedule_end_sticker_date_time ); ?>"
									min="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="sop_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt sop_product_schedule_option">
									<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule" id="image_schedule_sop" value="image_schedule" <?php if($sop_product_schedule_option == 'image_schedule' || $sop_product_schedule_option == '') { echo 'checked'; } ?>/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule" id="text_schedule_sop" value="text_schedule" <?php if($sop_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="sop_product_schedule_option" name="sop_product_schedule_option" value="<?php if($sop_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $sop_product_schedule_option ); } ?>"/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_sop_schedule_product;?>>
							<th scope="row" valign="top"><label for="sop_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="sop_schedule_sticker_image_width" name="sop_schedule_sticker_image_width" value="<?php echo ( $sop_schedule_sticker_image_width ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_sop_schedule_product;?>>
							<th scope="row" valign="top"><label for="sop_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="sop_schedule_sticker_image_height" name="sop_schedule_sticker_image_height" value="<?php echo ( $sop_schedule_sticker_image_height ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_sop_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="sop_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="sop_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($sop_schedule_image); ?>" width="60px" height="60px" /></div>
								<div style="line-height: 60px;">
									<input type="hidden" id="sop_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="sop_schedule_sticker_custom_id" value="<?php echo absint( $sop_schedule_sticker_custom_id ); ?>" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_sop"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_sop"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_sop_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="sop_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="sop_product_schedule_custom_text" name="sop_product_schedule_custom_text" value="<?php echo esc_attr( $sop_product_schedule_custom_text ); ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_sop_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="sop_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='sop_schedule_sticker_type'
									name="sop_schedule_sticker_type">
									<option value='ribbon'
										<?php selected( $sop_schedule_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
									<option value='round'
										<?php selected( $sop_schedule_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_sop" <?php echo $show_text_sop_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="sop_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="sop_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_fontcolor" value="<?php echo ($sop_schedule_product_custom_text_fontcolor) ? esc_attr( $sop_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_sop" <?php echo $show_text_sop_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="sop_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="sop_schedule_product_custom_text_backcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_backcolor" value="<?php echo ($sop_schedule_product_custom_text_backcolor) ? esc_attr( $sop_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_sop_schedule_product;?>>
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="sop_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="sop_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_top ); ?>"/>
								<input type="number" id="sop_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="sop_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_right ); ?>"/>
								<input type="number" id="sop_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="sop_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_bottom ); ?>"/>
								<input type="number" id="sop_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="sop_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_left ); ?>"/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>
					
					<tr>
						<th scope="row" valign="top">
							<label for="sop_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt sop_product_option">
								<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="image2" value="image" <?php if($sop_product_option == 'image' || $sop_product_option == '') { echo 'checked'; } ?>/>
								<label for="image2"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="text2" value="text" <?php if($sop_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text2"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="sop_product_option" name="sop_product_option" value="<?php if($sop_product_option == '') { echo "image"; } else { echo esc_attr( $sop_product_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo $show_image_sop_sticker;?>>
						<th scope="row" valign="top"><label for="sop_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_image_width" value="<?php echo ( $sop_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_sop_sticker;?>>
						<th scope="row" valign="top"><label for="sop_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_image_height" value="<?php echo ( $sop_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sop_sticker;?>>
						<th scope="row" valign="top">
							<label for="sop_product_custom_text"><?php _e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_custom_text" name="sop_product_custom_text" value="<?php echo esc_attr( $sop_product_custom_text ); ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sop_sticker;?>>
						<th scope="row" valign="top">
							<label for="sop_sticker_type"><?php _e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='sop_sticker_type'
								name="sop_sticker_type">
								<option value='ribbon'
									<?php selected( $sop_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $sop_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sop_sticker;?>>
						<th scope="row" valign="top">
							<label for="sop_product_custom_text_fontcolor"><?php _e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_custom_text_fontcolor" class="wli_color_picker" name="sop_product_custom_text_fontcolor" value="<?php echo ($sop_product_custom_text_fontcolor) ? esc_attr( $sop_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sop_sticker;?>>
						<th scope="row" valign="top">
							<label for="sop_product_custom_text_backcolor"><?php _e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_custom_text_backcolor" class="wli_color_picker" name="sop_product_custom_text_backcolor" value="<?php echo esc_attr( $sop_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="sop_product_custom_text_padding_top" placeholder="Top" class="small-text" name="sop_product_custom_text_padding_top" value="<?php echo esc_attr( $sop_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="sop_product_custom_text_padding_right" placeholder="Right" class="small-text" name="sop_product_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="sop_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="sop_product_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="sop_product_custom_text_padding_left" placeholder="Left" class="small-text" name="sop_product_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_sop_sticker;?>>
						<th scope="row" valign="top">
							<label for="sop_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $sop_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="sop_sticker_custom_id" class="wsbw_upload_img_id" name="sop_sticker_custom_id" value="<?php echo absint( $sop_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</table>
				<table id="wsbw_cust_products" class="wsbw_tab_content" style="display: none;">
					<tr>
						<th scope="row" valign="top"><label for="enable_cust_sticker"><?php _e( 'Enable Product Custom Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_cust_sticker" name="enable_cust_sticker" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_cust_sticker, 'yes');?>><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_cust_sticker, 'no');?>><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="cust_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="cust_sticker_pos" name="cust_sticker_pos" class="postform">
								<option value=""><?php _e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $cust_sticker_pos, 'left');?>><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $cust_sticker_pos, 'right');?>><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row" valign="top"><label for="cust_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_top" value="<?php echo ( $cust_sticker_top ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top"><label for="cust_sticker_left_right"><?php _e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_left_right" value="<?php echo ( $cust_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<?php
							if(get_option('wosbw_premium_access_allowed') == 1){
								?>
									<td>
										<input type="number" name="cust_sticker_rotate" value="<?php echo ( $cust_sticker_rotate ); ?>" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
									</td>
								<?php
							}
							else {
								?>
									<td>
										<div class="wosbw-pro-ribbon-banner">
											<input type="number" class="small-text file-input" name="cust_sticker_rotate" value="<?php echo ( $cust_sticker_rotate ); ?>" class="small-text" disabled><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>											
											<div class="ribbon">
												<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
													<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
													<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
													<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
													<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
													<defs>
													<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
													<stop stop-color="#FDAB00"/>
													<stop offset="1" stop-color="#CD8F0D"/>
													</linearGradient>
													</defs>
												</svg>
											</div>

											<div class="learn-more">
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
											</div>
										</div>
									</td>
							<?php
							}
						?>						
					</tr>
					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						
						<?php
                            if(get_option('wosbw_premium_access_allowed') == 1){
                                ?>
                                    <td>
										<select name="cust_sticker_category_animation_type" id="cust_sticker_category_animation_type">
											<?php
											$animation_options = array(
												'none'      => __( 'none', 'woo-stickers-by-webline' ),
												'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
												'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
												'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
												'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
												'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
											);

											$saved_value = $cust_sticker_category_animation_type;

											foreach ($animation_options as $value => $label) {
												$selected = ($saved_value == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
									</td>
                                <?php
                            }
							else {
								?>
                                    <td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="cust_sticker_category_animation_type" class="small-text file-input" id="cust_sticker_category_animation_type" disabled>
												<?php
												$animation_options = array(
													'none'      => __( 'none', 'woo-stickers-by-webline' ),
													'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
													'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
													'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
													'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
													'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
												);

												$saved_value = $cust_sticker_category_animation_type;

												foreach ($animation_options as $value => $label) {
													$selected = ($saved_value == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

											<div class="ribbon">
                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                    <defs>
                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>

                                            <div class="learn-more">
                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
                                            </div>
										</div>
									</td>
                                <?php
							}
						?>
						
					</tr>
					<?php if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr id="zoominout-options-cust-edit-cat" style="display:none;">
							<th scope="row"><label for="cust_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="cust_sticker_category_animation_scale" step="any" value="<?php echo ( $cust_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="cust_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="cust_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = $cust_sticker_category_animation_direction;

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="cust_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="cust_sticker_category_animation_iteration_count" value="<?php echo ( $cust_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="cust_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="cust_sticker_category_animation_type_delay" value="<?php echo ( $cust_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row"><label for="enable_cust_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						
						<?php
                            if(get_option('wosbw_premium_access_allowed') == 1){
                                ?>
                                    <td>
										<select name="enable_cust_product_schedule_sticker_category" id="enable_cust_product_schedule_sticker_category">
											<?php
											$enable_options = array(
												'yes'      => __( 'Yes', 'woo-stickers-by-webline' ),
												'no'      => __( 'No', 'woo-stickers-by-webline' ),
											);

											$saved_value_enable = !empty($enable_cust_product_schedule_sticker_category) ? $enable_cust_product_schedule_sticker_category : 'no';

											foreach ($enable_options as $value => $label) {
												$selected = ($saved_value_enable == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as CUSTOM in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>	
									</td>
                                <?php
                            }
							else {
								?>
                                    <td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="enable_cust_product_schedule_sticker_category" class="small-text file-input" id="enable_cust_product_schedule_sticker_category" disabled>
												<?php
												$enable_options = array(
													'yes'      => __( 'Yes', 'woo-stickers-by-webline' ),
													'no'      => __( 'No', 'woo-stickers-by-webline' ),
												);

												$saved_value_enable = $enable_cust_product_schedule_sticker_category;

												foreach ($enable_options as $value => $label) {
													$selected = ($saved_value_enable == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as CUSTOM in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>	


											<div class="ribbon">
                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                    <defs>
                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>

                                            <div class="learn-more">
                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
                                            </div>
										</div>
									</td>
                                <?php
							}
						?>
						
					</tr>

					<?php if(get_option('wosbw_premium_access_allowed') == 1){?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_start_sticker_date_time" name="cust_product_schedule_start_sticker_date_time"
									value="<?php echo $cust_product_schedule_start_sticker_date_time; ?>"
									/>
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_end_sticker_date_time" name="cust_product_schedule_end_sticker_date_time" 
									value="<?php echo $cust_product_schedule_end_sticker_date_time; ?>"
									min="<?php echo $formatted_date_time; ?>" />
								<p class="description"><?php _e('Specify end date and time to schedule the sticker', 'woo-stickers-by-webline'); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="cust_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt cust_product_schedule_option">
									<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule" id="image_schedule_cust" value="image_schedule" <?php if($cust_product_schedule_option == 'image_schedule' || $cust_product_schedule_option == '') { echo 'checked'; } ?>/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule" id="text_schedule_cust" value="text_schedule" <?php if($cust_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="cust_product_schedule_option" name="cust_product_schedule_option" value="<?php if($cust_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $cust_product_schedule_option ); } ?>"/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_cust_schedule_product;?>>
							<th scope="row" valign="top"><label for="cust_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="cust_schedule_sticker_image_width" name="cust_schedule_sticker_image_width" value="<?php echo ( $cust_schedule_sticker_image_width ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_cust_schedule_product;?>>
							<th scope="row" valign="top"><label for="cust_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="cust_schedule_sticker_image_height" name="cust_schedule_sticker_image_height" value="<?php echo ( $cust_schedule_sticker_image_height ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_cust_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="cust_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="cust_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($cust_schedule_image); ?>" width="60px" height="60px" /></div>
								<div style="line-height: 60px;">
									<input type="hidden" id="cust_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="cust_schedule_sticker_custom_id" value="<?php echo absint( $cust_schedule_sticker_custom_id ); ?>" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cust"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cust"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_cust_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="cust_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="cust_product_schedule_custom_text" name="cust_product_schedule_custom_text" value="<?php echo esc_attr( $cust_product_schedule_custom_text ); ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_cust_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="cust_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='cust_schedule_sticker_type'
									name="cust_schedule_sticker_type">
									<option value='ribbon'
										<?php selected( $cust_schedule_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
									<option value='round'
										<?php selected( $cust_schedule_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat_cust" <?php echo $show_text_cust_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="cust_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="cust_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_fontcolor" value="<?php echo ($cust_schedule_product_custom_text_fontcolor) ? esc_attr( $cust_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat_cust" <?php echo $show_text_cust_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="cust_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="cust_schedule_product_custom_text_backcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_backcolor" value="<?php echo ($cust_schedule_product_custom_text_backcolor) ? esc_attr( $cust_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_cust_schedule_product;?>>
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="cust_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="cust_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_top ); ?>"/>
								<input type="number" id="cust_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="cust_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_right ); ?>"/>
								<input type="number" id="cust_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="cust_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_bottom ); ?>"/>
								<input type="number" id="cust_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="cust_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_left ); ?>"/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?>

					<tr>
						<th scope="row" valign="top">
							<label for="cust_product_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt cust_product_option">
								<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="image3" value="image" <?php if($cust_product_option == 'image' || $cust_product_option == '') { echo 'checked'; } ?>/>
								<label for="image3"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="text3" value="text" <?php if($cust_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text3"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="cust_product_option" name="cust_product_option" value="<?php if($cust_product_option == '') { echo "image"; } else { echo esc_attr( $cust_product_option ); } ?>"/>
							</div>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_cust_product;?>>
						<th scope="row" valign="top"><label for="cust_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_image_width" value="<?php echo ( $cust_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_cust_product;?>>
						<th scope="row" valign="top"><label for="cust_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_image_height" value="<?php echo ( $cust_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_cust_product;?>>
						<th scope="row" valign="top">
							<label for="cust_product_custom_text"><?php _e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_custom_text" name="cust_product_custom_text" value="<?php echo esc_attr( $cust_product_custom_text ); ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_cust_product;?>>
						<th scope="row" valign="top">
							<label for="cust_sticker_type"><?php _e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='cust_sticker_type'
								name="cust_sticker_type">
								<option value='ribbon'
									<?php selected( $cust_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $cust_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_cust_product;?>>
						<th scope="row" valign="top">
							<label for="cust_product_custom_text_fontcolor"><?php _e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="cust_product_custom_text_fontcolor" value="<?php echo ($cust_product_custom_text_fontcolor) ? esc_attr( $cust_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_cust_product;?>>
						<th scope="row" valign="top">
							<label for="cust_product_custom_text_backcolor"><?php _e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="cust_product_custom_text_backcolor" value="<?php echo esc_attr( $cust_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="cust_product_custom_text_padding_top" placeholder="Top" class="small-text" name="cust_product_custom_text_padding_top" value="<?php echo esc_attr( $cust_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="cust_product_custom_text_padding_right" placeholder="Right" class="small-text" name="cust_product_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="cust_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="cust_product_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="cust_product_custom_text_padding_left" placeholder="Left" class="small-text" name="cust_product_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_cust_product;?>>
						<th scope="row" valign="top">
							<label for="cust_sticker_custom"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $cust_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="cust_sticker_custom_id" class="wsbw_upload_img_id" name="cust_sticker_custom_id" value="<?php echo absint( $cust_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>
				</table>
				<table id="wsbw_category_sticker" class="wsbw_tab_content" style="display: none;">
					<tr>
						<th scope="row" valign="top">
							<label for="enable_category_sticker"><?php _e( 'Enable Category Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="enable_category_sticker" name="enable_category_sticker" class="postform">
								<option value=""><?php _e( 'Please select', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_category_sticker, 'yes');?>><?php _e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_category_sticker, 'no');?>><?php _e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Enable sticker on this category', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="category_sticker_pos"><?php _e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="category_sticker_pos" name="category_sticker_pos" class="postform">
								<option value="left" <?php selected( $category_sticker_pos, 'left');?>><?php _e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $category_sticker_pos, 'right');?>><?php _e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="category_sticker_top"><?php _e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_top" value="<?php echo ( $category_sticker_top ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
										
					<tr>
						<th scope="row" valign="top"><label for="category_sticker_left_right"><?php _e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_left_right" value="<?php echo ( $category_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_rotate"><?php _e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						
						<?php
                            if(get_option('wosbw_premium_access_allowed') == 1){
                                ?>
                                    <td>
										<input type="number" name="category_sticker_sticker_rotate" value="<?php echo ( $category_sticker_sticker_rotate ); ?>" class="small-text"><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
									</td>
                                <?php
                            }
							else {
								?>
                                    <td>
										<div class="wosbw-pro-ribbon-banner">
											<input type="number" class="small-text file-input" name="category_sticker_sticker_rotate" value="<?php echo ( $category_sticker_sticker_rotate ); ?>" class="small-text" disabled><p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
											<div class="ribbon">
                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                    <defs>
                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>

                                            <div class="learn-more">
                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
                                            </div>
										</div>	
									</td>
                                <?php
							}
						?>
						
					</tr>
					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_type"><?php _e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						
						<?php
                            if(get_option('wosbw_premium_access_allowed') == 1){
                                ?>
                                    <td>
										<select name="category_sticker_sticker_category_animation_type" id="category_sticker_sticker_category_animation_type">
											<?php
											$animation_options = array(
												'none'      => __( 'none', 'woo-stickers-by-webline' ),
												'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
												'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
												'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
												'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
												'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
											);

											$saved_value = $category_sticker_sticker_category_animation_type;

											foreach ($animation_options as $value => $label) {
												$selected = ($saved_value == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>
									</td>
                                <?php
                            }
							else {
								?>
                                    <td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="category_sticker_sticker_category_animation_type" class="small-text file-input" id="category_sticker_sticker_category_animation_type" disabled>
												<?php
												$animation_options = array(
													'none'      => __( 'none', 'woo-stickers-by-webline' ),
													'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
													'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
													'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
													'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
													'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
												);

												$saved_value = $category_sticker_sticker_category_animation_type;

												foreach ($animation_options as $value => $label) {
													$selected = ($saved_value == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Specify animation type.', 'woo-stickers-by-webline' ); ?></p>

											<div class="ribbon">
                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                    <defs>
                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>

                                            <div class="learn-more">
                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
                                            </div>
									</td>
                                <?php
							}
						?>
					</tr>
					<?php if(get_option('wosbw_premium_access_allowed') == 1){ ?>
						<tr id="zoominout-options-category-edit-cat" style="display:none;">
							<th scope="row"><label for="category_sticker_sticker_category_animation_scale"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="category_sticker_sticker_category_animation_scale" step="any" value="<?php echo ( $category_sticker_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="category_sticker_sticker_category_animation_direction"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<select name="category_sticker_sticker_category_animation_direction">
									<?php
									$animation_options = array(
										'normal'      => __( 'Normal', 'woo-stickers-by-webline' ),
										'reverse'      => __( 'Reverse', 'woo-stickers-by-webline' ),
										'alternate'     => __( 'Alternate', 'woo-stickers-by-webline' ),
										'alternate-reverse' => __( 'Alternate Reverse', 'woo-stickers-by-webline' ),
									);

									$saved_value = $category_sticker_sticker_category_animation_direction;

									foreach ($animation_options as $value => $label) {
										$selected = ($saved_value == $value) ? 'selected' : '';
										echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php _e( 'Specify animation direction.', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="category_sticker_sticker_category_animation_iteration_count"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="text" name="category_sticker_sticker_category_animation_iteration_count" value="<?php echo ( $category_sticker_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
						<tr>
							<th scope="row"><label for="category_sticker_sticker_category_animation_type_delay"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td><input type="number" name="category_sticker_sticker_category_animation_type_delay" value="<?php echo ( $category_sticker_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php _e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
						</tr>
					<?php } ?> 


					<tr>
						<th scope="row"><label for="enable_category_product_schedule_sticker_category"><?php _e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						
						<?php
                            if(get_option('wosbw_premium_access_allowed') == 1){
                                ?>
                                    <td>
										<select name="enable_category_product_schedule_sticker_category" id="enable_category_product_schedule_sticker_category">
											<?php
											$enable_options = array(
												'yes'      => __( 'Yes', 'woo-stickers-by-webline' ),
												'no'      => __( 'No', 'woo-stickers-by-webline' ),
											);
											$saved_value_enable = !empty($enable_category_product_schedule_sticker_category) ? $enable_category_product_schedule_sticker_category : 'no';


											foreach ($enable_options as $value => $label) {
												$selected = ($saved_value_enable == $value) ? 'selected' : '';
												echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
											}
											?>
										</select>
										<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked Categoty Wise in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>			
										</td>
                                <?php
                            }
							else {
								?>
                                    <td>
										<div class="wosbw-pro-ribbon-banner">
											<select name="enable_category_product_schedule_sticker_category" class="small-text file-input" id="enable_category_product_schedule_sticker_category" disabled>
												<?php
												$enable_options = array(
													'no'      => __( 'No', 'woo-stickers-by-webline' ),
												);

												$saved_value_enable = $enable_category_product_schedule_sticker_category;

												foreach ($enable_options as $value => $label) {
													$selected = ($saved_value_enable == $value) ? 'selected' : '';
													echo '<option value="' . esc_attr($value) . '" ' . $selected . '>' . esc_html($label) . '</option>';
												}
												?>
											</select>
											<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked Categoty Wise in wooCommerce.', 'woo-stickers-by-webline' ); ?></p>			

											<div class="ribbon">
                                                <svg width="167" height="167" viewBox="0 0 167 167" fill="none">
                                                    <path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
                                                    <path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
                                                    <path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
                                                    <path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
                                                    <defs>
                                                    <linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    <linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FDAB00"/>
                                                    <stop offset="1" stop-color="#CD8F0D"/>
                                                    </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>

                                            <div class="learn-more">
                                                <a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
                                            </div>
									</td>
                                <?php
							}
						?>
						
					</tr>

					<?php if(get_option('wosbw_premium_access_allowed') == 1){ ?>
						<tr>
							<th scope="row" valign="top">
								<label><?php _e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_start_sticker_date_time" name="category_product_schedule_start_sticker_date_time" 
									value="<?php echo ( $category_product_schedule_start_sticker_date_time ); ?>" 
									/>
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" valign="top">
								<label><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_end_sticker_date_time" name="category_product_schedule_end_sticker_date_time" 
									value="<?php echo ( $category_product_schedule_end_sticker_date_time ); ?>" 
									min="<?php echo $formatted_date_time; ?>"/>
								<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
						<tr>
							<th scope="row" valign="top">
								<label for="category_product_schedule_option"><?php _e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div class="woo_opt category_product_schedule_option">
									<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule" id="image_schedule_cat" value="image_schedule" <?php if($category_product_schedule_option == 'image_schedule' || $category_product_schedule_option == '') { echo 'checked'; } ?>/>
									<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
									<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule" id="text_schedule_cat" value="text_schedule" <?php if($category_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
									<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
									<input type="hidden" class="wli_product_schedule_option" id="category_product_schedule_option" name="category_product_schedule_option" value="<?php if($category_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $category_product_schedule_option ); } ?>"/>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_category_schedule_product;?>>
							<th scope="row" valign="top"><label for="category_schedule_sticker_image_width"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="category_schedule_sticker_image_width" name="category_schedule_sticker_image_width" value="<?php echo ( $category_schedule_sticker_image_width ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>
						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_category_schedule_product;?>>
							<th scope="row" valign="top"><label for="category_schedule_sticker_image_height"><?php _e( '', 'woo-stickers-by-webline' ); ?></label></th>
							<td>
								<input type="number" id="category_schedule_sticker_image_height" name="category_schedule_sticker_image_height" value="<?php echo ( $category_schedule_sticker_image_height ); ?>" class="small-text">
								<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
							</td>
						</tr>

						<tr class="custom_option custom_optimage_sch" <?php echo $show_image_category_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="category_schedule_sticker_custom"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<div id="category_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($category_schedule_image); ?>" width="60px" height="60px" /></div>
								<div style="line-height: 60px;">
									<input type="hidden" id="category_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="category_schedule_sticker_custom_id" value="<?php echo absint( $category_schedule_sticker_custom_id ); ?>" />
									<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cat"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
									<button type="button" class="wsbw_remove_image_button button"id="wsbw_remove_image_button_cat"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
								</div>
							</td>
						</tr>

						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_category_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="category_product_schedule_custom_text"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="category_product_schedule_custom_text" name="category_product_schedule_custom_text" value="<?php echo esc_attr( $category_product_schedule_custom_text ); ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_category_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="category_schedule_sticker_type"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<select id='category_schedule_sticker_type'
									name="category_schedule_sticker_type">
									<option value='ribbon'
										<?php selected( $category_schedule_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
									<option value='round'
										<?php selected( $category_schedule_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
								</select>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch fontcolor_cat" <?php echo $show_text_category_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="category_schedule_product_custom_text_fontcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="category_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="category_schedule_product_custom_text_fontcolor" value="<?php echo ($category_schedule_product_custom_text_fontcolor) ? esc_attr( $category_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch backcolor_cat" <?php echo $show_text_category_schedule_product;?>>
							<th scope="row" valign="top">
								<label for="category_schedule_product_custom_text_backcolor"><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="text" id="category_schedule_product_custom_text_backcolor" class="wli_color_picker" name="category_schedule_product_custom_text_backcolor" value="<?php echo ($category_schedule_product_custom_text_backcolor) ? esc_attr( $category_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
							</td>
						</tr>
						<tr class="custom_option custom_opttext_sch" <?php echo $show_text_category_schedule_product;?>>
							<th scope="row" valign="top">
								<label for=""><?php _e( '', 'woo-stickers-by-webline' ); ?></label>
							</th>
							<td>
								<input type="number" id="category_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="category_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_top ); ?>"/>
								<input type="number" id="category_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="category_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_right ); ?>"/>
								<input type="number" id="category_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="category_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_bottom ); ?>"/>
								<input type="number" id="category_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="category_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_left ); ?>"/>
								<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
							</td>
						</tr>
					<?php } ?> 

					<tr>
						<th scope="row" valign="top">
							<label for="category_sticker_option"><?php _e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt category_sticker_option">
								<input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="image4" value="image" <?php if($category_sticker_option == 'image' || $category_sticker_option == '') { echo 'checked'; } ?>/>
								<label for="image4"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="text4" value="text" <?php if($category_sticker_option == 'text') { echo 'checked'; } ?>/>
								<label for="text4"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="category_sticker_option" name="category_sticker_option" value="<?php echo $category_sticker_option == '' ? "image" : esc_attr( $category_sticker_option );?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo $show_image_sticker;?>>
						<th scope="row" valign="top"><label for="category_sticker_image_width"><?php _e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_image_width" value="<?php echo ( $category_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_sticker;?>>
						<th scope="row" valign="top"><label for="category_sticker_image_height"><?php _e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_image_height" value="<?php echo ( $category_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sticker;?>>
						<th scope="row" valign="top">
							<label for="category_sticker_text"><?php _e( 'Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_sticker_text" name="category_sticker_text" value="<?php echo esc_attr( $category_sticker_text ); ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sticker;?>>
						<th scope="row" valign="top">
							<label for="category_sticker_type"><?php _e( 'Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='category_sticker_type'
								name="category_sticker_type">
								<option value='ribbon'
									<?php selected( $category_sticker_type, 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $category_sticker_type, 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sticker;?>>
						<th scope="row" valign="top">
							<label for="category_sticker_text_fontcolor"><?php _e( 'Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_sticker_text_fontcolor" class="wli_color_picker" name="category_sticker_text_fontcolor" value="<?php echo ($category_sticker_text_fontcolor) ? esc_attr( $category_sticker_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>
					<tr class="custom_option custom_opttext" <?php echo $show_text_sticker;?>>
						<th scope="row" valign="top">
							<label for="category_sticker_text_backcolor"><?php _e( 'Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_sticker_text_backcolor" class="wli_color_picker" name="category_sticker_text_backcolor" value="<?php echo esc_attr( $category_sticker_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo $show_text_np_product;?>>
						<th scope="row" valign="top">
							<label for=""><?php _e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="category_sticker_text_padding_top" placeholder="Top" class="small-text" name="category_sticker_text_padding_top" value="<?php echo esc_attr( $category_sticker_text_padding_top ); ?>"/>
							<input type="number" id="category_sticker_text_padding_right" placeholder="Right" class="small-text" name="category_sticker_text_padding_right" value="<?php echo esc_attr( $category_sticker_text_padding_right ); ?>"/>
							<input type="number" id="category_sticker_text_padding_bottom" placeholder="Bottom" class="small-text" name="category_sticker_text_padding_bottom" value="<?php echo esc_attr( $category_sticker_text_padding_bottom ); ?>"/>
							<input type="number" id="category_sticker_text_padding_left" placeholder="Left" class="small-text" name="category_sticker_text_padding_left" value="<?php echo esc_attr( $category_sticker_text_padding_left ); ?>"/>
							<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage" <?php echo $show_image_sticker;?>>
						<th scope="row" valign="top">
							<label for="category_sticker_image"><?php _e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="category_sticker_image" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $category_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="category_sticker_image_id" class="wsbw_upload_img_id" name="category_sticker_image_id" value="<?php echo absint( $category_sticker_image_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php _e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php _e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
							<p class="description"><?php _e( 'Upload your sticker image which you want to display on this category.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * save_category_fields function.
	 *
	 * @param mixed  $term_id Term ID being saved
	 * @param mixed  $tt_id
	 * @param string $taxonomy
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {

		//Save all new product sticker fields
		if ( isset( $_POST['enable_np_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_np_sticker', sanitize_text_field( $_POST['enable_np_sticker'] ) );
		}
		if ( isset( $_POST['np_no_of_days'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_no_of_days', absint( $_POST['np_no_of_days'] ) );
		}
		if ( isset( $_POST['np_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_pos', sanitize_text_field( $_POST['np_sticker_pos'] ) );
		}
		if ( isset( $_POST['np_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_top', sanitize_text_field( $_POST['np_sticker_top'] ) );
		}
		if ( isset( $_POST['np_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_left_right', sanitize_text_field( $_POST['np_sticker_left_right'] ) );
		}

		if ( isset( $_POST['np_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_rotate', sanitize_text_field( $_POST['np_sticker_rotate'] ) );
		}

		if ( isset( $_POST['np_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_type', sanitize_text_field( $_POST['np_sticker_category_animation_type'] ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_direction', sanitize_text_field( $_POST['np_sticker_category_animation_direction'] ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_scale', sanitize_text_field( $_POST['np_sticker_category_animation_scale'] ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_iteration_count', sanitize_text_field( $_POST['np_sticker_category_animation_iteration_count'] ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_type_delay', sanitize_text_field( $_POST['np_sticker_category_animation_type_delay'] ) );
		}

		if ( isset( $_POST['enable_np_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_np_product_schedule_sticker_category', sanitize_text_field( $_POST['enable_np_product_schedule_sticker_category'] ) );
		}

		if ( isset( $_POST['np_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_start_sticker_date_time', sanitize_text_field( $_POST['np_product_schedule_start_sticker_date_time'] ) );
		}

		if ( isset( $_POST['np_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_end_sticker_date_time', sanitize_text_field( $_POST['np_product_schedule_end_sticker_date_time'] ) );
		}

		if ( isset( $_POST['np_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_option', sanitize_text_field( $_POST['np_product_schedule_option'] ) );
		}

		if ( isset( $_POST['np_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_image_width', sanitize_text_field( $_POST['np_schedule_sticker_image_width'] ) );
		}

		if ( isset( $_POST['np_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_image_height', sanitize_text_field( $_POST['np_schedule_sticker_image_height'] ) );
		}

		if ( isset( $_POST['np_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_custom_id', sanitize_text_field( $_POST['np_schedule_sticker_custom_id'] ) );
		}

		if ( isset( $_POST['np_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text', sanitize_text_field( $_POST['np_product_schedule_custom_text'] ) );
		}

		if ( isset( $_POST['np_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_type', sanitize_text_field( $_POST['np_schedule_sticker_type'] ) );
		}

		if ( isset( $_POST['np_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_product_custom_text_fontcolor', sanitize_text_field( $_POST['np_schedule_product_custom_text_fontcolor'] ) );
		}

		if ( isset( $_POST['np_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_product_custom_text_backcolor', sanitize_text_field( $_POST['np_schedule_product_custom_text_backcolor'] ) );
		}

		if ( isset( $_POST['np_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_top', sanitize_text_field( $_POST['np_product_schedule_custom_text_padding_top'] ) );
		}

		if ( isset( $_POST['np_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_right', sanitize_text_field( $_POST['np_product_schedule_custom_text_padding_right'] ) );
		}

		if ( isset( $_POST['np_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_bottom', sanitize_text_field( $_POST['np_product_schedule_custom_text_padding_bottom'] ) );
		}

		if ( isset( $_POST['np_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_left', sanitize_text_field( $_POST['np_product_schedule_custom_text_padding_left'] ) );
		}
		

		if ( isset( $_POST['np_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_option', sanitize_key( $_POST['np_product_option'] ) );
		}
		if ( isset( $_POST['np_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_image_width', sanitize_key( $_POST['np_sticker_image_width'] ) );
		}
		if ( isset( $_POST['np_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_image_height', sanitize_key( $_POST['np_sticker_image_height'] ) );
		}
		if ( isset( $_POST['np_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text',sanitize_text_field( $_POST['np_product_custom_text'] ) );
		}
		if ( isset( $_POST['np_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_type', sanitize_text_field( $_POST['np_sticker_type'] ) );
		}
		if ( isset( $_POST['np_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_fontcolor', sanitize_hex_color( $_POST['np_product_custom_text_fontcolor'] ) );
		}
		if ( isset( $_POST['np_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_backcolor', sanitize_hex_color( $_POST['np_product_custom_text_backcolor'] ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_top', sanitize_text_field( $_POST['np_product_custom_text_padding_top'] ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_right', sanitize_text_field( $_POST['np_product_custom_text_padding_right'] ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_bottom', sanitize_text_field( $_POST['np_product_custom_text_padding_bottom'] ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_left', sanitize_text_field( $_POST['np_product_custom_text_padding_left'] ) );
		}
		if ( isset( $_POST['np_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_custom_id', absint( $_POST['np_sticker_custom_id'] ) );
		}

		//Save all product on sale sticker fields
		if ( isset( $_POST['enable_pos_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_pos_sticker', sanitize_text_field( $_POST['enable_pos_sticker'] ) );
		}
		if ( isset( $_POST['pos_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_pos', sanitize_text_field( $_POST['pos_sticker_pos'] ) );
		}
		if ( isset( $_POST['pos_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_left_right', sanitize_text_field( $_POST['pos_sticker_left_right'] ) );
		}
		if ( isset( $_POST['pos_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_top', sanitize_text_field( $_POST['pos_sticker_top'] ) );
		}

		if ( isset( $_POST['pos_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_rotate', sanitize_text_field( $_POST['pos_sticker_rotate'] ) );
		}
		
		if ( isset( $_POST['pos_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_type', sanitize_text_field( $_POST['pos_sticker_category_animation_type'] ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_direction', sanitize_text_field( $_POST['pos_sticker_category_animation_direction'] ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_scale', sanitize_text_field( $_POST['pos_sticker_category_animation_scale'] ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_iteration_count', sanitize_text_field( $_POST['pos_sticker_category_animation_iteration_count'] ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_type_delay', sanitize_text_field( $_POST['pos_sticker_category_animation_type_delay'] ) );
		}

		if ( isset( $_POST['enable_pos_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_pos_product_schedule_sticker_category', sanitize_text_field( $_POST['enable_pos_product_schedule_sticker_category'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_start_sticker_date_time', sanitize_text_field( $_POST['pos_product_schedule_start_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_end_sticker_date_time', sanitize_text_field( $_POST['pos_product_schedule_end_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_option', sanitize_text_field( $_POST['pos_product_schedule_option'] ) );
		}
		
		if ( isset( $_POST['pos_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_image_width', sanitize_text_field( $_POST['pos_schedule_sticker_image_width'] ) );
		}
		
		if ( isset( $_POST['pos_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_image_height', sanitize_text_field( $_POST['pos_schedule_sticker_image_height'] ) );
		}
		
		if ( isset( $_POST['pos_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_custom_id', sanitize_text_field( $_POST['pos_schedule_sticker_custom_id'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text', sanitize_text_field( $_POST['pos_product_schedule_custom_text'] ) );
		}
		
		if ( isset( $_POST['pos_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_type', sanitize_text_field( $_POST['pos_schedule_sticker_type'] ) );
		}
		
		if ( isset( $_POST['pos_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_product_custom_text_fontcolor', sanitize_text_field( $_POST['pos_schedule_product_custom_text_fontcolor'] ) );
		}
		
		if ( isset( $_POST['pos_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_product_custom_text_backcolor', sanitize_text_field( $_POST['pos_schedule_product_custom_text_backcolor'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_top', sanitize_text_field( $_POST['pos_product_schedule_custom_text_padding_top'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_right', sanitize_text_field( $_POST['pos_product_schedule_custom_text_padding_right'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_bottom', sanitize_text_field( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) );
		}
		
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_left', sanitize_text_field( $_POST['pos_product_schedule_custom_text_padding_left'] ) );
		}

		if ( isset( $_POST['pos_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_option', sanitize_key( $_POST['pos_product_option'] ) );
		}
		if ( isset( $_POST['pos_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_image_width', sanitize_text_field( $_POST['pos_sticker_image_width'] ) );
		}
		if ( isset( $_POST['pos_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_image_height', sanitize_text_field( $_POST['pos_sticker_image_height'] ) );
		}
		if ( isset( $_POST['pos_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text',sanitize_text_field( $_POST['pos_product_custom_text'] ) );
		}
		if ( isset( $_POST['pos_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_type', sanitize_text_field( $_POST['pos_sticker_type'] ) );
		}
		if ( isset( $_POST['pos_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_fontcolor', sanitize_hex_color( $_POST['pos_product_custom_text_fontcolor'] ) );
		}
		if ( isset( $_POST['pos_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_backcolor', sanitize_hex_color( $_POST['pos_product_custom_text_backcolor'] ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_top', sanitize_text_field( $_POST['pos_product_custom_text_padding_top'] ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_right', sanitize_text_field( $_POST['pos_product_custom_text_padding_right'] ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_bottom', sanitize_text_field( $_POST['pos_product_custom_text_padding_bottom'] ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_left', sanitize_text_field( $_POST['pos_product_custom_text_padding_left'] ) );
		}
		if ( isset( $_POST['pos_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_custom_id', absint( $_POST['pos_sticker_custom_id'] ) );
		}

		//Save all soldout product sticker fields
		if ( isset( $_POST['enable_sop_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_sop_sticker', sanitize_text_field( $_POST['enable_sop_sticker'] ) );
		}
		if ( isset( $_POST['sop_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_pos', sanitize_text_field( $_POST['sop_sticker_pos'] ) );
		}
		if ( isset( $_POST['sop_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_left_right', sanitize_text_field( $_POST['sop_sticker_left_right'] ) );
		}
		if ( isset( $_POST['sop_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_top', sanitize_text_field( $_POST['sop_sticker_top'] ) );
		}

		if ( isset( $_POST['sop_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_rotate', sanitize_text_field( $_POST['sop_sticker_rotate'] ) );
		}
		
		if ( isset( $_POST['sop_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_type', sanitize_text_field( $_POST['sop_sticker_category_animation_type'] ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_direction', sanitize_text_field( $_POST['sop_sticker_category_animation_direction'] ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_scale', sanitize_text_field( $_POST['sop_sticker_category_animation_scale'] ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_iteration_count', sanitize_text_field( $_POST['sop_sticker_category_animation_iteration_count'] ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_type_delay', sanitize_text_field( $_POST['sop_sticker_category_animation_type_delay'] ) );
		}

		if ( isset( $_POST['enable_sop_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_sop_product_schedule_sticker_category', sanitize_text_field( $_POST['enable_sop_product_schedule_sticker_category'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_start_sticker_date_time', sanitize_text_field( $_POST['sop_product_schedule_start_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_end_sticker_date_time', sanitize_text_field( $_POST['sop_product_schedule_end_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_option', sanitize_text_field( $_POST['sop_product_schedule_option'] ) );
		}
		
		if ( isset( $_POST['sop_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_image_width', sanitize_text_field( $_POST['sop_schedule_sticker_image_width'] ) );
		}
		
		if ( isset( $_POST['sop_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_image_height', sanitize_text_field( $_POST['sop_schedule_sticker_image_height'] ) );
		}
		
		if ( isset( $_POST['sop_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_custom_id', sanitize_text_field( $_POST['sop_schedule_sticker_custom_id'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text', sanitize_text_field( $_POST['sop_product_schedule_custom_text'] ) );
		}
		
		if ( isset( $_POST['sop_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_type', sanitize_text_field( $_POST['sop_schedule_sticker_type'] ) );
		}
		
		if ( isset( $_POST['sop_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_product_custom_text_fontcolor', sanitize_text_field( $_POST['sop_schedule_product_custom_text_fontcolor'] ) );
		}
		
		if ( isset( $_POST['sop_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_product_custom_text_backcolor', sanitize_text_field( $_POST['sop_schedule_product_custom_text_backcolor'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_top', sanitize_text_field( $_POST['sop_product_schedule_custom_text_padding_top'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_right', sanitize_text_field( $_POST['sop_product_schedule_custom_text_padding_right'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_bottom', sanitize_text_field( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) );
		}
		
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_left', sanitize_text_field( $_POST['sop_product_schedule_custom_text_padding_left'] ) );
		}

		if ( isset( $_POST['sop_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_option', sanitize_key( $_POST['sop_product_option'] ) );
		}
		if ( isset( $_POST['sop_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_image_width', sanitize_text_field( $_POST['sop_sticker_image_width'] ) );
		}
		if ( isset( $_POST['sop_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_image_height', sanitize_text_field( $_POST['sop_sticker_image_height'] ) );
		}
		if ( isset( $_POST['sop_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text',sanitize_text_field( $_POST['sop_product_custom_text'] ) );
		}
		if ( isset( $_POST['sop_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_type', sanitize_text_field( $_POST['sop_sticker_type'] ) );
		}
		if ( isset( $_POST['sop_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_fontcolor', sanitize_hex_color( $_POST['sop_product_custom_text_fontcolor'] ) );
		}
		if ( isset( $_POST['sop_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_backcolor', sanitize_hex_color( $_POST['sop_product_custom_text_backcolor'] ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_top', sanitize_text_field( $_POST['sop_product_custom_text_padding_top'] ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_right', sanitize_text_field( $_POST['sop_product_custom_text_padding_right'] ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_bottom', sanitize_text_field( $_POST['sop_product_custom_text_padding_bottom'] ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_left', sanitize_text_field( $_POST['sop_product_custom_text_padding_left'] ) );
		}
		if ( isset( $_POST['sop_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_custom_id', absint( $_POST['sop_sticker_custom_id'] ) );
		}

		//Save Custom Product Sticker fields
		if ( isset( $_POST['enable_cust_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_cust_sticker', sanitize_text_field( $_POST['enable_cust_sticker'] ) );
		}
		if ( isset( $_POST['cust_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_pos', sanitize_text_field( $_POST['cust_sticker_pos'] ) );
		}
		if ( isset( $_POST['cust_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_left_right', sanitize_text_field( $_POST['cust_sticker_left_right'] ) );
		}
		if ( isset( $_POST['cust_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_top', sanitize_text_field( $_POST['cust_sticker_top'] ) );
		}

		if ( isset( $_POST['cust_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_rotate', sanitize_text_field( $_POST['cust_sticker_rotate'] ) );
		}
		
		if ( isset( $_POST['cust_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_type', sanitize_text_field( $_POST['cust_sticker_category_animation_type'] ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_direction', sanitize_text_field( $_POST['cust_sticker_category_animation_direction'] ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_scale', sanitize_text_field( $_POST['cust_sticker_category_animation_scale'] ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_iteration_count', sanitize_text_field( $_POST['cust_sticker_category_animation_iteration_count'] ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_type_delay', sanitize_text_field( $_POST['cust_sticker_category_animation_type_delay'] ) );
		}

		if ( isset( $_POST['enable_cust_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_cust_product_schedule_sticker_category', sanitize_text_field( $_POST['enable_cust_product_schedule_sticker_category'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_start_sticker_date_time', sanitize_text_field( $_POST['cust_product_schedule_start_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_end_sticker_date_time', sanitize_text_field( $_POST['cust_product_schedule_end_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_option', sanitize_text_field( $_POST['cust_product_schedule_option'] ) );
		}
		
		if ( isset( $_POST['cust_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_image_width', sanitize_text_field( $_POST['cust_schedule_sticker_image_width'] ) );
		}
		
		if ( isset( $_POST['cust_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_image_height', sanitize_text_field( $_POST['cust_schedule_sticker_image_height'] ) );
		}
		
		if ( isset( $_POST['cust_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_custom_id', sanitize_text_field( $_POST['cust_schedule_sticker_custom_id'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text', sanitize_text_field( $_POST['cust_product_schedule_custom_text'] ) );
		}
		
		if ( isset( $_POST['cust_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_type', sanitize_text_field( $_POST['cust_schedule_sticker_type'] ) );
		}
		
		if ( isset( $_POST['cust_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_product_custom_text_fontcolor', sanitize_text_field( $_POST['cust_schedule_product_custom_text_fontcolor'] ) );
		}
		
		if ( isset( $_POST['cust_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_product_custom_text_backcolor', sanitize_text_field( $_POST['cust_schedule_product_custom_text_backcolor'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_top', sanitize_text_field( $_POST['cust_product_schedule_custom_text_padding_top'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_right', sanitize_text_field( $_POST['cust_product_schedule_custom_text_padding_right'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_bottom', sanitize_text_field( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) );
		}
		
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_left', sanitize_text_field( $_POST['cust_product_schedule_custom_text_padding_left'] ) );
		}

		if ( isset( $_POST['cust_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_option', sanitize_key( $_POST['cust_product_option'] ) );
		}
		if ( isset( $_POST['cust_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_image_width', sanitize_key( $_POST['cust_sticker_image_width'] ) );
		}
		if ( isset( $_POST['cust_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_image_height', sanitize_key( $_POST['cust_sticker_image_height'] ) );
		}
		if ( isset( $_POST['cust_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text',sanitize_text_field( $_POST['cust_product_custom_text'] ) );
		}
		if ( isset( $_POST['cust_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_type', sanitize_text_field( $_POST['cust_sticker_type'] ) );
		}
		if ( isset( $_POST['cust_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_fontcolor', sanitize_hex_color( $_POST['cust_product_custom_text_fontcolor'] ) );
		}
		if ( isset( $_POST['cust_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_backcolor', sanitize_hex_color( $_POST['cust_product_custom_text_backcolor'] ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_top', sanitize_text_field( $_POST['cust_product_custom_text_padding_top'] ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_right', sanitize_text_field( $_POST['cust_product_custom_text_padding_right'] ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_bottom', sanitize_text_field( $_POST['cust_product_custom_text_padding_bottom'] ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_left', sanitize_text_field( $_POST['cust_product_custom_text_padding_left'] ) );
		}
		if ( isset( $_POST['cust_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_custom_id', absint( $_POST['cust_sticker_custom_id'] ) );
		}

		//Save Category Sticker fields
		if ( isset( $_POST['enable_category_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_category_sticker', sanitize_text_field( $_POST['enable_category_sticker'] ) );
		}
		if ( isset( $_POST['category_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_pos', sanitize_text_field( $_POST['category_sticker_pos'] ) );
		}
		if ( isset( $_POST['category_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_left_right', sanitize_text_field( $_POST['category_sticker_left_right'] ) );
		}
		if ( isset( $_POST['category_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_top', sanitize_text_field( $_POST['category_sticker_top'] ) );
		}

		if ( isset( $_POST['category_sticker_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_rotate', sanitize_text_field( $_POST['category_sticker_sticker_rotate'] ) );
		}
		
		if ( isset( $_POST['category_sticker_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_type', sanitize_text_field( $_POST['category_sticker_sticker_category_animation_type'] ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_direction', sanitize_text_field( $_POST['category_sticker_sticker_category_animation_direction'] ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_scale', sanitize_text_field( $_POST['category_sticker_sticker_category_animation_scale'] ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_iteration_count', sanitize_text_field( $_POST['category_sticker_sticker_category_animation_iteration_count'] ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_type_delay', sanitize_text_field( $_POST['category_sticker_sticker_category_animation_type_delay'] ) );
		}

		if ( isset( $_POST['enable_category_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_category_product_schedule_sticker_category', sanitize_text_field( $_POST['enable_category_product_schedule_sticker_category'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_start_sticker_date_time', sanitize_text_field( $_POST['category_product_schedule_start_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_end_sticker_date_time', sanitize_text_field( $_POST['category_product_schedule_end_sticker_date_time'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_option', sanitize_text_field( $_POST['category_product_schedule_option'] ) );
		}
		
		if ( isset( $_POST['category_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_image_width', sanitize_text_field( $_POST['category_schedule_sticker_image_width'] ) );
		}
		
		if ( isset( $_POST['category_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_image_height', sanitize_text_field( $_POST['category_schedule_sticker_image_height'] ) );
		}
		
		if ( isset( $_POST['category_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_custom_id', sanitize_text_field( $_POST['category_schedule_sticker_custom_id'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text', sanitize_text_field( $_POST['category_product_schedule_custom_text'] ) );
		}
		
		if ( isset( $_POST['category_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_type', sanitize_text_field( $_POST['category_schedule_sticker_type'] ) );
		}
		
		if ( isset( $_POST['category_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_product_custom_text_fontcolor', sanitize_text_field( $_POST['category_schedule_product_custom_text_fontcolor'] ) );
		}
		
		if ( isset( $_POST['category_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_product_custom_text_backcolor', sanitize_text_field( $_POST['category_schedule_product_custom_text_backcolor'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_top', sanitize_text_field( $_POST['category_product_schedule_custom_text_padding_top'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_right', sanitize_text_field( $_POST['category_product_schedule_custom_text_padding_right'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_bottom', sanitize_text_field( $_POST['category_product_schedule_custom_text_padding_bottom'] ) );
		}
		
		if ( isset( $_POST['category_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_left', sanitize_text_field( $_POST['category_product_schedule_custom_text_padding_left'] ) );
		}

		if ( isset( $_POST['category_sticker_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_option', sanitize_key( $_POST['category_sticker_option'] ) );
		}
		if ( isset( $_POST['category_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_image_width', sanitize_key( $_POST['category_sticker_image_width'] ) );
		}
		if ( isset( $_POST['category_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_image_height', sanitize_key( $_POST['category_sticker_image_height'] ) );
		}
		if ( isset( $_POST['category_sticker_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text',sanitize_text_field( $_POST['category_sticker_text'] ) );
		}
		if ( isset( $_POST['category_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_type', sanitize_text_field( $_POST['category_sticker_type'] ) );
		}
		if ( isset( $_POST['category_sticker_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_fontcolor', sanitize_hex_color( $_POST['category_sticker_text_fontcolor'] ) );
		}
		if ( isset( $_POST['category_sticker_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_backcolor', sanitize_hex_color( $_POST['category_sticker_text_backcolor'] ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_top', sanitize_text_field( $_POST['category_sticker_text_padding_top'] ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_right', sanitize_text_field( $_POST['category_sticker_text_padding_right'] ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_bottom', sanitize_text_field( $_POST['category_sticker_text_padding_bottom'] ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_left', sanitize_text_field( $_POST['category_sticker_text_padding_left'] ) );
		}
		if ( isset( $_POST['category_sticker_image_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_image_id', absint( $_POST['category_sticker_image_id'] ) );
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		global $typenow;

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

		//Check if woosticker pages
		if( $hook == 'settings_page_wli-stickers' || (!empty($_GET['page']) == "wli-stickers" && !empty($_GET['tab']) == "new_product_settings") ||
			(!empty($_GET['page']) == "wli-stickers") ||
			( $typenow == 'product' && ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'term.php' ) ) ) {


			// Add the color picker CSS file       
	        wp_enqueue_style( 'wp-color-picker' );

	        // Add the color picker JS file       
	        wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-stickers-by-webline-admin.css', array(), $this->version, 'all' );

			// Enqueue Admin Notices CSS 
			
		}

		// if($hook == 'settings_page_upgrade-to-premium-wosbw'){
			wp_enqueue_style( "woo-stickers-by-webline-admin-notices", plugin_dir_url( __FILE__ ) . 'css/wosbw-admin-notices.css', array(), $this->version, 'all' );
		// }
	}

		/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		global $typenow;

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

		//Check if woosticker pages
		if( $hook == 'settings_page_wli-stickers' || (!empty($_GET['page']) == "wli-stickers" && !empty($_GET['tab']) == "new_product_settings") ||
			(!empty($_GET['page']) == "wli-stickers") ||
			( $typenow == 'product' && ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'term.php' ) ) ) {

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-stickers-by-webline-admin.js', array( 'jquery' ), $this->version, false );
	        wp_localize_script( $this->plugin_name, 'scriptsData', array(
	        	'ajaxurl'=>admin_url( 'admin-ajax.php' ),
	        	'choose_image_title' => __( 'Choose an image', 'woo-stickers-by-webline' ),
	        	'use_image_btn_text' => __( 'Use image', 'woo-stickers-by-webline' ),
	        	'placeholder_img_src' => esc_js( wc_placeholder_img_src() ),
	        ));
			add_thickbox();
		}

	}

	// Enqueue scripts and styles
	function enqueue_custom_scripts() {
		wp_enqueue_script( 'jquery' ); 
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'woo-stickers-admin-script', plugin_dir_url( __FILE__ ) . 'js/upgrade-form.js', array( 'jquery', 'jquery-ui-autocomplete' ), $this->version, false );
		wp_enqueue_script( 'auto-js', plugin_dir_url( __FILE__ ) . 'js/autocomplete.js', array( 'jquery', 'jquery-ui-autocomplete' ), $this->version, false );
		wp_enqueue_style( 'jquery-ui-autocomplete', plugin_dir_url( __FILE__ ) . 'css/jquery-ui-autocomplete.css', array(), $this->version );
		wp_localize_script('xmlsbw-script-js', 'url', array(
			'home_url' => home_url(),
			'plugin_url' => WOSBW_URL,
			'admin_email' => get_option('admin_email'),
			'plugin_name' => 'WOO Sticker By Webline',
			'page_name' => get_option('wosbw_selected_page'),
			'premium_access' => get_option('wosbw_premium_access_allowed'),
		)
		);
	}

	/**
	 * Register settings link on plugin page.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_link($links, $file)
    {   
    	$wooStickerFile = WS_PLUGIN_FILE;    	 
        if (basename($file) == $wooStickerFile) {
        	
            $linkSettings = '<a href="' . admin_url("options-general.php?page=wli-stickers") . '">'. __('Settings', 'woo-stickers-by-webline' ) .'</a>';
            array_unshift($links, $linkSettings);
        }
        return $links;
    }

	/**
	 * Loads settings from
	 * the database into their respective arrays.
	 * Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 *
	 * @since 1.0.0
	 * @var No arguments passed
	 * @return void
	 * @author Weblineindia
	 */
	public function load_settings() {
		$this->general_settings = ( array ) get_option ( $this->general_settings_key );
		$this->new_product_settings = ( array ) get_option ( $this->new_product_settings_key );
		$this->sale_product_settings = ( array ) get_option ( $this->sale_product_settings_key );
		$this->sold_product_settings = ( array ) get_option ( $this->sold_product_settings_key );
		$this->cust_product_settings = ( array ) get_option ( $this->cust_product_settings_key );
		// Merge with defaults
		$this->general_settings = array_merge ( array (
				'enable_sticker' => 'no',
				'enable_sticker_list' => 'no',
				'enable_sticker_detail' => 'no' 
		), $this->general_settings );
		
		$this->new_product_settings = array_merge ( array (
				'enable_new_product_sticker' => 'no',
				'new_product_sticker_days' => '10',
				'new_product_position' => 'left',
				'new_product_sticker_left_right' => '',
				'new_product_sticker_top' => '',
				'new_product_sticker_rotate' => '',
				'new_product_sticker_animation_type' => '',
				'new_product_sticker_animation_scale' => '',
				'new_product_sticker_animation_rotate' => '',
				'new_product_sticker_animation_translate' => '',
				'new_product_sticker_animation_iteration_count' => '',
				'new_product_sticker_animation_delay' => '',
				'new_product_sticker_animation_direction' => '',
				'new_product_schedule_start_sticker_date_time' => '',
				'new_product_schedule_end_sticker_date_time' => '',
				'new_product_schedule_sticker_time' => '',
				'enable_new_product_schedule_sticker' => '',
				'new_product_schedule_sticker_option' => '',
				'new_product_schedule_sticker_image_width' => '',
				'new_product_schedule_sticker_image_height' => '',
				'new_product_schedule_custom_sticker' => '',
				'new_product_schedule_custom_text' => '',
				'enable_new_schedule_product_style' => '',
				'new_product_schedule_text_padding_left' => '',
				'new_product_schedule_text_padding_bottom' => '',
				'new_product_schedule_text_padding_right' => '',
				'new_product_schedule_text_padding_top' => '',
				'new_product_schedule_custom_text_backcolor' => '',
				'new_product_schedule_custom_text_fontcolor' => '',
				'new_product_sticker_image_width' => '56px',
				'new_product_sticker_image_height' => '54px',
				'new_product_option' => '',
				'new_product_custom_text' => '',
				'enable_new_product_style' => 'ribbon',
				'new_product_custom_text_fontcolor' => '#ffffff',
				'new_product_custom_text_backcolor' => '#000000',
				'np_product_custom_text_padding_top' => '0',
				'np_product_custom_text_padding_right' => '0',
				'np_product_custom_text_padding_bottom' => '0',
				'np_product_custom_text_padding_left' => '0',
				'new_product_custom_sticker' => '',
		), $this->new_product_settings );
		
		$this->sale_product_settings = array_merge ( array (
				'enable_sale_product_sticker' => 'no',
				'sale_product_position' => 'left',
				'sale_product_sticker_top' => '',
				'sale_product_sticker_left_right' => '',
				'sale_product_sticker_rotate' => '',
				'sale_product_sticker_animation_type' => '',
				'sale_product_sticker_animation_scale' => '',
				'sale_product_sticker_animation_rotate' => '',
				'sale_product_sticker_animation_translate' => '',
				'sale_product_sticker_animation_iteration_count' => '',
				'sale_product_sticker_animation_delay' => '',
				'sale_product_sticker_animation_direction' => '',
				'sale_product_schedule_start_sticker_date_time' => '',
				'sale_product_schedule_end_sticker_date_time' => '',
				'sale_product_schedule_sticker_time' => '',
				'enable_sale_product_schedule_sticker' => '',
				'sale_product_schedule_sticker_option' => '',
				'sale_product_schedule_sticker_image_width' => '',
				'sale_product_schedule_sticker_image_height' => '',
				'sale_product_schedule_custom_sticker' => '',
				'sale_product_schedule_custom_text' => '',
				'enable_sale_schedule_product_style' => '',
				'sale_product_schedule_text_padding_left' => '',
				'sale_product_schedule_text_padding_bottom' => '',
				'sale_product_schedule_text_padding_right' => '',
				'sale_product_schedule_text_padding_top' => '',
				'sale_product_schedule_custom_text_backcolor' => '',
				'sale_product_schedule_custom_text_fontcolor' => '',
				'sale_product_sticker_image_width' => '56px',
				'sale_product_sticker_image_height' => '54px',
				'sale_product_option' => '',
				'sale_product_custom_text' => '',
				'enable_sale_product_style' => 'ribbon',
				'sale_product_custom_text_fontcolor' => '#ffffff',
				'sale_product_custom_text_backcolor' => '#000000',
				'sale_product_text_padding_top' => '',
				'sale_product_text_padding_right' => '',
				'sale_product_text_padding_bottom' => '',
				'sale_product_text_padding_left' => '',
				'sale_product_custom_sticker' => '',
		), $this->sale_product_settings );
		
		$this->sold_product_settings = array_merge ( array (
				'enable_sold_product_sticker' => 'no',
				'sold_product_position' => 'left',
				'sold_product_option' => '',
				'sold_product_sticker_left_right' => '',
				'sold_product_sticker_top' => '',
				'sold_product_sticker_rotate' => '',
				'sold_product_sticker_animation_type' => '',
				'sold_product_sticker_animation_scale' => '',
				'sold_product_sticker_animation_rotate' => '',
				'sold_product_sticker_animation_translate' => '',
				'sold_product_sticker_animation_iteration_count' => '',
				'sold_product_sticker_animation_delay' => '',
				'sold_product_sticker_animation_direction' => '',
				'sold_product_schedule_start_sticker_date_time' => '',
				'sold_product_schedule_end_sticker_date_time' => '',
				'sold_product_schedule_sticker_time' => '',
				'enable_sold_product_schedule_sticker' => '',
				'sold_product_schedule_sticker_option' => '',
				'sold_product_schedule_sticker_image_width' => '',
				'sold_product_schedule_sticker_image_height' => '',
				'sold_product_schedule_custom_sticker' => '',
				'sold_product_schedule_custom_text' => '',
				'enable_sold_schedule_product_style' => '',
				'sold_product_schedule_text_padding_left' => '',
				'sold_product_schedule_text_padding_bottom' => '',
				'sold_product_schedule_text_padding_right' => '',
				'sold_product_schedule_text_padding_top' => '',
				'sold_product_schedule_custom_text_backcolor' => '',
				'sold_product_schedule_custom_text_fontcolor' => '',
				'sold_product_sticker_image_width' => '56px',
				'sold_product_sticker_image_height' => '54px',
				'sold_product_custom_text' => '',
				'enable_sold_product_style' => 'ribbon',
				'sold_product_custom_text_fontcolor' => '#ffffff',
				'sold_product_custom_text_backcolor' => '#000000',
				'sold_product_custom_text_padding_top' => '0',
				'sold_product_custom_text_padding_right' => '0',
				'sold_product_custom_text_padding_bottom' => '0',
				'sold_product_custom_text_padding_left' => '0',
				'sold_product_custom_sticker' => ''
		), $this->sold_product_settings );
		
		$this->cust_product_settings = array_merge ( array (
				'enable_cust_product_sticker' => 'no',
				'cust_product_position' => 'left',
				'cust_product_option' => '',
				'cust_product_custom_text' => '',
				'enable_cust_product_style' => 'ribbon',
				'cust_product_custom_text_fontcolor' => '#ffffff',
				'cust_product_custom_text_backcolor' => '#000000',
				'cust_product_text_padding_top' => '',
				'cust_product_text_padding_right' => '',
				'cust_product_text_padding_bottom' => '',
				'cust_product_text_padding_left' => '',
				'cust_product_custom_sticker' => '',
				'cust_product_sticker_rotate' => '',
				'cust_product_sticker_animation_type' => '',
				'cust_product_sticker_animation_scale' => '',
				'cust_product_sticker_animation_rotate' => '',
				'cust_product_sticker_animation_translate' => '',
				'cust_product_sticker_animation_iteration_count' => '',
				'cust_product_sticker_animation_delay' => '',
				'cust_product_sticker_animation_direction' => '',
				'cust_product_schedule_start_sticker_date_time' => '',
				'cust_product_schedule_end_sticker_date_time' => '',
				'cust_product_schedule_sticker_time' => '',
				'enable_cust_product_schedule_sticker' => '',
				'cust_product_schedule_sticker_option' => '',
				'cust_product_schedule_sticker_image_width' => '',
				'cust_product_schedule_sticker_image_height' => '',
				'cust_product_schedule_custom_sticker' => '',
				'cust_product_schedule_custom_text' => '',
				'enable_cust_schedule_product_style' => '',
				'cust_product_schedule_text_padding_left' => '',
				'cust_product_schedule_text_padding_bottom' => '',
				'cust_product_schedule_text_padding_right' => '',
				'cust_product_schedule_text_padding_top' => '',
				'cust_product_schedule_custom_text_backcolor' => '',
				'cust_product_schedule_custom_text_fontcolor' => '',
		), $this->cust_product_settings );				
	}
	/**
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @since 1.0.0
	 * @var No arguments passed
	 * @return void
	 * @author Weblineindia
	 */
	public function register_general_settings() {
		$this->plugin_settings_tabs [$this->general_settings_key] = __( 'General', 'woo-stickers-by-webline' );
		
		register_setting ( $this->general_settings_key, $this->general_settings_key );
		add_settings_section ( 'section_general', __( 'General Plugin Settings', 'woo-stickers-by-webline' ), array (
				&$this,
				'section_general_desc' 
		), $this->general_settings_key );
		
		add_settings_field ( 'enable_sticker', __( 'Enable Product Sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_sticker' 
		), $this->general_settings_key, 'section_general' );
		
		add_settings_field ( 'enable_sticker_list', __( 'Enable Sticker On Product Listing Page:', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_sticker_list' 
		), $this->general_settings_key, 'section_general' );
		
		add_settings_field ( 'enable_sticker_detail', __( 'Enable Sticker On Product Details Page:', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_sticker_detail' 
		), $this->general_settings_key, 'section_general' );
		
		add_settings_field ( 'sticker_custom_css', __( 'Custom CSS:', 'woo-stickers-by-webline' ), array (
				&$this,
				'sticker_custom_css' 
		), $this->general_settings_key, 'section_general' );
	}
	
	/**
	 * Registers the New Product settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @since 1.0.0
	 * @var No arguments passed
	 * @return void
	 * @author Weblineindia
	 */
	public function register_new_product_settings() {
		$this->plugin_settings_tabs [$this->new_product_settings_key] = __( 'New Products', 'woo-stickers-by-webline' );
		
		register_setting ( $this->new_product_settings_key, $this->new_product_settings_key );
		
		add_settings_section ( 'section_new_product', __( 'Sticker Configurations for New Products', 'woo-stickers-by-webline' ), array (
				&$this,
				'section_new_product_desc' 
		), $this->new_product_settings_key );
		
		add_settings_field ( 'enable_new_product_sticker', __( 'Enable Product Sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_new_product_sticker' 
		), $this->new_product_settings_key, 'section_new_product' );
		
		add_settings_field ( 'new_product_sticker_days', __( 'Number of Days:', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_sticker_days'
			), $this->new_product_settings_key, 'section_new_product' );
		
		add_settings_field ( 'new_product_position', __( 'Sticker Position:', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_position'
			), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'new_product_sticker_left_right', __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_sticker_left_right'
				), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'new_product_sticker_top', __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_sticker_top'
			), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'new_product_sticker_rotate', __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_sticker_rotate'
				), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field('new_product_sticker_animation',__( 'Sticker Animation:', 'woo-stickers-by-webline' ),array( 
			&$this, 
			'new_product_sticker_animation' 
				),$this->new_product_settings_key,'section_new_product');

		add_settings_field ( 'enable_new_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_new_product_schedule_sticker' 
				), $this->new_product_settings_key, 'section_new_product' );
		if(get_option('wosbw_premium_access_allowed') == 1){

			add_settings_field ( 'new_product_schedule_sticker', __( 'Scheduled Sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_schedule_sticker'
					), $this->new_product_settings_key, 'section_new_product');

			add_settings_field ( 'new_product_schedule_sticker_image_width', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_schedule_sticker_image_width'
				), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage_sch' ) );

			add_settings_field ( 'new_product_schedule_sticker_image_height', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_schedule_sticker_image_height'
				), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage_sch' ) );

			add_settings_field ( 'new_product_schedule_custom_sticker', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_schedule_custom_sticker'
				), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage_sch' ) );

			add_settings_field ( 'new_product_schedule_custom_text', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_schedule_custom_text'
				), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

			add_settings_field ( 'enable_new_schedule_product_style', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_new_schedule_product_style'
				), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

			add_settings_field ( 'new_product_schedule_custom_text_fontcolor', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_schedule_custom_text_fontcolor'
					), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_new' ) );
		
			add_settings_field ( 'new_product_schedule_custom_text_backcolor', __( '', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_schedule_custom_text_backcolor'
				), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_new' ) );

			add_settings_field('new_product_schedule_custom_text_padding',__( '', 'woo-stickers-by-webline' ),array( 
				&$this, 
				'new_product_schedule_custom_text_padding' 
					),$this->new_product_settings_key,'section_new_product',array( 'class' => 'custom_option custom_opttext_sch' )
				);
		}

		add_settings_field ( 'new_product_option', __( 'Sticker Option:', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_option' 
		), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'new_product_sticker_image_width', __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_sticker_image_width'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'new_product_sticker_image_height', __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_sticker_image_height'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'new_product_custom_text', __( 'Add your custom text:', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_custom_text'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'enable_new_product_style', __( 'Select layout:', 'woo-stickers-by-webline' ), array (
		&$this,
		'enable_new_product_style'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'new_product_custom_text_fontcolor', __( 'Choose font color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_custom_text_fontcolor'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'new_product_custom_text_backcolor', __( 'Choose background color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_custom_text_backcolor'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field('new_product_custom_text_padding',__( 'Sticker Padding (px):', 'woo-stickers-by-webline' ),array( 
		&$this, 
		'new_product_custom_text_padding' 
			),$this->new_product_settings_key,'section_new_product',array( 'class' => 'custom_option custom_opttext' )
		);

		add_settings_field ( 'new_product_custom_sticker', __( 'Add your custom sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'new_product_custom_sticker'
		), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage' ) );
	}
	
	/**
	 * Registers the Sale Product settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function register_sale_product_settings() {
		$this->plugin_settings_tabs [$this->sale_product_settings_key] = __( 'Products On Sale', 'woo-stickers-by-webline' );
		
		register_setting ( $this->sale_product_settings_key, $this->sale_product_settings_key );
		add_settings_section ( 'section_sale_product', __( 'Sticker Configurations for Products On Sale', 'woo-stickers-by-webline' ), array (
				&$this,
				'section_sale_product_desc' 
		), $this->sale_product_settings_key );
		
		add_settings_field ( 'enable_sale_product_sticker', __( 'Enable Product Sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_sale_product_sticker' 
		), $this->sale_product_settings_key, 'section_sale_product' );
		
		add_settings_field ( 'sale_product_position', __( 'Sticker Position:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_position'
			), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_sticker_left_right', __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_sticker_left_right'
				), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_sticker_top', __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_sticker_top'
				), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_sticker_rotate', __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_sticker_rotate'
				), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_sticker_animation', __( 'Sticker Animation:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_sticker_animation'
				), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'enable_sale_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_sale_product_schedule_sticker' 
				), $this->sale_product_settings_key, 'section_sale_product' );

		if(get_option('wosbw_premium_access_allowed') == 1){
			add_settings_field ( 'sale_product_schedule_sticker', __( 'Scheduled Sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'sale_product_schedule_sticker'
					), $this->sale_product_settings_key, 'section_sale_product');
			
			add_settings_field ( 'sale_product_schedule_sticker_image_width', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sale_product_schedule_sticker_image_width'
				), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'sale_product_schedule_sticker_image_height', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sale_product_schedule_sticker_image_height'
				), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'sale_product_schedule_custom_sticker', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sale_product_schedule_custom_sticker'
				), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'sale_product_schedule_custom_text', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sale_product_schedule_custom_text'
				), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch' ) );
			
			add_settings_field ( 'enable_sale_schedule_product_style', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_sale_schedule_product_style'
				), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch' ) );
	
			add_settings_field ( 'sale_product_schedule_custom_text_fontcolor', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sale_product_schedule_custom_text_fontcolor'
					), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_sale' ) );
			
			add_settings_field ( 'sale_product_schedule_custom_text_backcolor', __( '', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_custom_text_backcolor'
				), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_sale' ) );
			
			add_settings_field('sale_product_schedule_custom_text_padding',__( '', 'woo-stickers-by-webline' ),array( 
				&$this, 
				'sale_product_schedule_custom_text_padding' 
					),$this->sale_product_settings_key,'section_sale_product',array( 'class' => 'custom_option custom_opttext_sch' )
				);
		}
		
		add_settings_field ( 'sale_product_option', __( 'Sticker Option:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_option'
			), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_sticker_image_width', __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_sticker_image_width'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'sale_product_sticker_image_height', __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_sticker_image_height'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'sale_product_custom_text', __( 'Add your custom text:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_custom_text'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'enable_sale_product_style', __( 'Select layout:', 'woo-stickers-by-webline' ), array (
		&$this,
		'enable_sale_product_style'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'sale_product_custom_text_fontcolor', __( 'Choose font color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_custom_text_fontcolor'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'sale_product_custom_text_backcolor', __( 'Choose background color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_custom_text_backcolor'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext' ) );
		
		add_settings_field ( 'sale_product_custom_sticker', __( 'Add your custom sticker:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_custom_sticker'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field('sale_product_custom_text_padding',__( 'Sticker Padding (px):', 'woo-stickers-by-webline' ),array( 
		&$this, 
		'sale_product_custom_text_padding' 
			),$this->sale_product_settings_key,'section_sale_product',array( 'class' => 'custom_option custom_opttext' )
		);
	}
	
	/**
	 * Registers the Sold Product settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function register_sold_product_settings() {
		$this->plugin_settings_tabs [$this->sold_product_settings_key] = __( 'Soldout Products', 'woo-stickers-by-webline' );
	
		register_setting ( $this->sold_product_settings_key, $this->sold_product_settings_key );
		add_settings_section ( 'section_sold_product', __( 'Sticker Configurations for Soldout Products', 'woo-stickers-by-webline' ), array (
		&$this,
		'section_sold_product_desc'
				), $this->sold_product_settings_key );
	
		add_settings_field ( 'enable_sold_product_sticker', __( 'Enable Product Sticker:', 'woo-stickers-by-webline' ), array (
		&$this,
		'enable_sold_product_sticker'
				), $this->sold_product_settings_key, 'section_sold_product' );
		
		add_settings_field ( 'sold_product_position', __( 'Sticker Position:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_position'
			), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'sold_product_sticker_left_right', __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_sticker_left_right'
				), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'sold_product_sticker_top', __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_sticker_top'
				), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'sold_product_sticker_rotate', __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_sticker_rotate'
				), $this->sold_product_settings_key, 'section_sold_product' );
		
		add_settings_field ( 'sold_product_sticker_animation', __( 'Sticker Animation:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_sticker_animation'
				), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'enable_sold_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_sold_product_schedule_sticker' 
				), $this->sold_product_settings_key, 'section_sold_product' );
		
		if(get_option('wosbw_premium_access_allowed') == 1){
		
			add_settings_field ( 'sold_product_schedule_sticker', __( 'Scheduled Sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'sold_product_schedule_sticker'
					), $this->sold_product_settings_key, 'section_sold_product');
			
			
			add_settings_field ( 'sold_product_schedule_sticker_image_width', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sold_product_schedule_sticker_image_width'
				), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'sold_product_schedule_sticker_image_height', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sold_product_schedule_sticker_image_height'
				), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'sold_product_schedule_custom_sticker', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sold_product_schedule_custom_sticker'
				), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'sold_product_schedule_custom_text', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sold_product_schedule_custom_text'
				), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch' ) );
			
			add_settings_field ( 'enable_sold_schedule_product_style', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_sold_schedule_product_style'
				), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

			add_settings_field ( 'sold_product_schedule_custom_text_fontcolor', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'sold_product_schedule_custom_text_fontcolor'
					), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_sold' ) );
			
			add_settings_field ( 'sold_product_schedule_custom_text_backcolor', __( '', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_custom_text_backcolor'
				), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_sold' ) );
			
			add_settings_field('sold_product_schedule_custom_text_padding',__( '', 'woo-stickers-by-webline' ),array( 
				&$this, 
				'sold_product_schedule_custom_text_padding' 
					),$this->sold_product_settings_key,'section_sold_product',array( 'class' => 'custom_option custom_opttext_sch' )
				);
		}
		
		add_settings_field ( 'sold_product_option', __( 'Sticker Option:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_option'
			), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'sold_product_sticker_image_width', __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_sticker_image_width'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'sold_product_sticker_image_height', __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_sticker_image_height'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'sold_product_custom_text', __( 'Add your custom text:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_custom_text'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'enable_sold_product_style', __( 'Select layout:', 'woo-stickers-by-webline' ), array (
		&$this,
		'enable_sold_product_style'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'sold_product_custom_text_fontcolor', __( 'Choose font color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_custom_text_fontcolor'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'sold_product_custom_text_backcolor', __( 'Choose background color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_custom_text_backcolor'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext' ) );
		
		add_settings_field ( 'sold_product_custom_sticker', __( 'Add your custom sticker:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_custom_sticker'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage' ) );
			
		add_settings_field('sold_product_custom_text_padding',__( 'Sticker Padding (px):', 'woo-stickers-by-webline' ),array( 
		&$this, 
		'sold_product_custom_text_padding' 
			),$this->sold_product_settings_key,'section_sold_product',array( 'class' => 'custom_option custom_opttext' )
		);
	}

	/**
	 * Registers Custom Product Sticker settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function register_cust_product_settings() {
		$this->plugin_settings_tabs [$this->cust_product_settings_key] = __( 'Custom Product Sticker', 'woo-stickers-by-webline' );
	
		register_setting ( $this->cust_product_settings_key, $this->cust_product_settings_key );
		add_settings_section ( 'section_cust_product', __( 'Custom Sticker Configurations for Products', 'woo-stickers-by-webline' ), array (
		&$this,
		'section_cust_product_desc'
				), $this->cust_product_settings_key );
	
		add_settings_field ( 'enable_cust_product_sticker', __( 'Enable Product Custom Sticker:', 'woo-stickers-by-webline' ), array (
		&$this,
		'enable_cust_product_sticker'
				), $this->cust_product_settings_key, 'section_cust_product' );
		
		add_settings_field ( 'cust_product_position', __( 'Sticker Position:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_position'
			), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'cust_product_sticker_left_right', __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_sticker_left_right'
			), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'cust_product_sticker_top', __( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_sticker_top'
			), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'cust_product_sticker_rotate', __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_sticker_rotate'
				), $this->cust_product_settings_key, 'section_cust_product' );
		
		add_settings_field ( 'cust_product_sticker_animation', __( 'Sticker Animation:', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_sticker_animation'
				), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'enable_cust_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_cust_product_schedule_sticker' 
				), $this->cust_product_settings_key, 'section_cust_product' );
		
		if(get_option('wosbw_premium_access_allowed') == 1){

			add_settings_field ( 'cust_product_schedule_sticker', __( 'Scheduled Sticker:', 'woo-stickers-by-webline' ), array (
				&$this,
				'cust_product_schedule_sticker'
					), $this->cust_product_settings_key, 'section_cust_product');
			
			
			add_settings_field ( 'cust_product_schedule_sticker_image_width', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'cust_product_schedule_sticker_image_width'
				), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'cust_product_schedule_sticker_image_height', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'cust_product_schedule_sticker_image_height'
				), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'cust_product_schedule_custom_sticker', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'cust_product_schedule_custom_sticker'
				), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
			
			add_settings_field ( 'cust_product_schedule_custom_text', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'cust_product_schedule_custom_text'
				), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch' ) );
			
			add_settings_field ( 'enable_cust_schedule_product_style', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'enable_cust_schedule_product_style'
				), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

			add_settings_field ( 'cust_product_schedule_custom_text_fontcolor', __( '', 'woo-stickers-by-webline' ), array (
				&$this,
				'cust_product_schedule_custom_text_fontcolor'
					), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_cust' ) );
			
			add_settings_field ( 'cust_product_schedule_custom_text_backcolor', __( '', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_custom_text_backcolor'
				), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_cust' ) );
			
			add_settings_field('cust_product_schedule_custom_text_padding',__( '', 'woo-stickers-by-webline' ),array( 
				&$this, 
				'cust_product_schedule_custom_text_padding' 
					),$this->cust_product_settings_key,'section_cust_product',array( 'class' => 'custom_option custom_opttext_sch' )
				);
		}

		add_settings_field ( 'cust_product_option', __( 'Sticker Option:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_option'
			), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'cust_product_sticker_image_width', __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_sticker_image_width'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'cust_product_sticker_image_height', __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_sticker_image_height'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage' ) );

		add_settings_field ( 'cust_product_custom_text', __( 'Add your custom text:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_custom_text'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'enable_cust_product_style', __( 'Select layout:', 'woo-stickers-by-webline' ), array (
		&$this,
		'enable_cust_product_style'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'cust_product_custom_text_fontcolor', __( 'Choose font color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_custom_text_fontcolor'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field ( 'cust_product_custom_text_backcolor', __( 'Choose background color:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_custom_text_backcolor'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext' ) );

		add_settings_field('cust_product_custom_text_padding',__( 'Sticker Padding (px):', 'woo-stickers-by-webline' ),array( 
		&$this, 
		'cust_product_custom_text_padding' 
			),$this->cust_product_settings_key,'section_cust_product',array( 'class' => 'custom_option custom_opttext' ));
		
		add_settings_field ( 'cust_product_custom_sticker', __( 'Add your custom sticker:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_custom_sticker'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage' ) );
	}

	/**
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function section_general_desc() {		
	}
	public function section_new_product_desc() {				
	}
	public function section_sale_product_desc() {		
	}
	public function section_sold_product_desc() {		
	}
	public function section_cust_product_desc() {		
	}

	/**
	 * General Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sticker() {
		?>
		<select id='enable_sticker'
			name="<?php echo $this->general_settings_key; ?>[enable_sticker]">
			<option value='yes'
				<?php selected( $this->general_settings['enable_sticker'], 'yes',true );?>><?php _e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( $this->general_settings['enable_sticker'], 'no',true );?>><?php _e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select wether you want to enable sticker feature or not.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	/**
	 * General Settings :: Enable Sticker On Product Listing Page
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sticker_list() {
		?>
		<select id='enable_sticker_list'
			name="<?php echo $this->general_settings_key; ?>[enable_sticker_list]">
			<option value='yes'
				<?php selected( $this->general_settings['enable_sticker_list'], 'yes',true );?>><?php _e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( $this->general_settings['enable_sticker_list'], 'no',true );?>><?php _e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select wether you want to enable sticker feature on product listing page or not.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * General Settings :: Enable Sticker On Product Listing Page
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sticker_detail() {
		?>
		<select id='enable_sticker_list'
			name="<?php echo $this->general_settings_key; ?>[enable_sticker_detail]">
			<option value='yes'
				<?php selected( $this->general_settings['enable_sticker_detail'], 'yes',true );?>><?php _e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( $this->general_settings['enable_sticker_detail'], 'no',true );?>><?php _e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select wether you want to enable sticker feature on product detail page or not.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * General Settings :: Custom CSS
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sticker_custom_css() {
		?>
		<textarea id="sticker_custom_css" name="<?php echo $this->general_settings_key; ?>[custom_css]" rows="4" cols="50"><?php echo !empty( $this->general_settings['custom_css'] ) ? $this->general_settings['custom_css'] : '';?></textarea>
		<p class="description"><?php _e( 'Add your custom css here to load on frontend.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	/**
	 * New Product Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_new_product_sticker() {
		?>
		<select id='enable_new_product_sticker'
			name="<?php echo $this->new_product_settings_key; ?>[enable_new_product_sticker]">
			<option value='yes'
				<?php selected( $this->new_product_settings['enable_new_product_sticker'], 'yes',true );?>><?php _e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( $this->new_product_settings['enable_new_product_sticker'], 'no',true );?>><?php _e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Control sticker display for products which are marked as NEW in wooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	/**
	 * New Product Settings :: Days to New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_sticker_days() {
		
		?>
		<input type="text" id="new_product_sticker_days" class="small-text" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_days]" value="<?php echo absint( $this->new_product_settings['new_product_sticker_days']); ?>" />
		<p class="description"><?php _e( 'Specify the No of days before to be display product as New (Default 10 days).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	/**
	 * New Product Settings :: Sticker Position
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_position() {
		?>
		<select id='new_product_position'
			name="<?php echo $this->new_product_settings_key; ?>[new_product_position]">
			<option value='left'
				<?php selected( $this->new_product_settings['new_product_position'], 'left',true );?>><?php _e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( $this->new_product_settings['new_product_position'], 'right',true );?>><?php _e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select the position of the sticker.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Top CSS for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_sticker_left_right() {
		
		?>
		<input type="number" class="small-text" id="new_product_sticker_left_right" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_left_right]" <?php if ( isset( $this->new_product_settings['new_product_sticker_left_right'] ) ) { echo 'value="' . $this->new_product_settings['new_product_sticker_left_right'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Top CSS for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_sticker_top() {
		
		?>
		<input type="number" class="small-text" id="new_product_sticker_top" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_top]" <?php if ( isset( $this->new_product_settings['new_product_sticker_top'] ) ) { echo 'value="' . $this->new_product_settings['new_product_sticker_top'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_sticker_rotate() {
		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<input type="number" class="small-text" id="new_product_sticker_rotate" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_rotate]" value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_rotate'] ); ?>"/>
			<p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' );?></p>
			<?php
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<input type="number" class="small-text file-input" placeholder="45" value="0" disabled/>
				<p class="description"><?php _e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' );?></p>
				
				<div class="ribbon">
					<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
						<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
						<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
						<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
						<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
						<defs>
						<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						</defs>
					</svg>
				</div>

				<div class="learn-more">
					<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			</div>
			
			<?php
		}
		
	}

	public function new_product_sticker_animation() {

		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<select id="new_product_sticker_animation_type" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_sticker_animation_type]">
				<?php
					$border_types = array(
						'none' => 'None',
						'spin' => 'Spin',
						'swing' => 'Swing',
						'zoominout' => 'Zoom In / Out',
						'leftright' => 'Left-Right',
						'updown' => 'Up-Down',
					);
					$current_value = esc_attr( $this->new_product_settings['new_product_sticker_animation_type'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>
			<br>
			<div id="zoominout-options-new-global" style="display: none;">
				<input type="number" id="new_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php _e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
				<br>
			</div>
			<select id="new_product_sticker_animation_direction" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_sticker_animation_direction]">
				<?php
					$border_types = array(
						'normal' => 'Normal',
						'reverse' => 'Reverse',
						'alternate' => 'Alternate',
						'alternate-reverse' => 'Alternate Reverse',
					);
					$current_value = esc_attr( $this->new_product_settings['new_product_sticker_animation_direction'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation direction', 'woo-stickers-by-webline' );?></p>
			<br>			
			<input type="text" id="new_product_sticker_animation_iteration_count" step="any" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_animation_iteration_count]" value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_animation_iteration_count'] ); ?>"placeholder='Iteration Count'/>
			<p class="description"><?php _e( 'Specify animation iteration count (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			<br>
			<input type="number" id="new_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_animation_delay]" value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_animation_delay'] ); ?>"placeholder='Delay'/>
			<p class="description"><?php _e( 'Specify animation delay time in seconds (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
		<?php
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<select id="new_product_sticker_animation_type" class="file-input" name="" value="none" disabled>
					<option>None</option>
				</select>
				<div class="ribbon">
					<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
						<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
						<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
						<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
						<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
						<defs>
						<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						</defs>
					</svg>
				</div>
				<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>
				<div class="learn-more">
					<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			</div>
			<?php
		}
	}

	public function enable_new_product_schedule_sticker() {
		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<select id='enable_new_product_schedule_sticker'
					name="<?php echo $this->new_product_settings_key; ?>[enable_new_product_schedule_sticker]">
				<option value='yes'
					<?php selected(!empty($this->new_product_settings['enable_new_product_schedule_sticker']) ? $this->new_product_settings['enable_new_product_schedule_sticker'] : 'no', 'yes', true); ?>>
					<?php _e('Yes', 'woo-stickers-by-webline'); ?>
				</option>
				<option value='no'
					<?php selected(!empty($this->new_product_settings['enable_new_product_schedule_sticker']) ? $this->new_product_settings['enable_new_product_schedule_sticker'] : 'no', 'no', true); ?>>
					<?php _e('No', 'woo-stickers-by-webline'); ?>
				</option>
			</select>
			<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as NEW in wooCommerce.', 'woo-stickers-by-webline' );?></p>
			<?php
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<select  class="file-input" disabled>
				<option>No</option>
				</select>
				<div class="ribbon">
						<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
							<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
							<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
							<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
							<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
							<defs>
							<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
							<stop stop-color="#FDAB00"/>
							<stop offset="1" stop-color="#CD8F0D"/>
							</linearGradient>
							<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
							<stop stop-color="#FDAB00"/>
							<stop offset="1" stop-color="#CD8F0D"/>
							</linearGradient>
							</defs>
						</svg>
				</div>
				<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as NEW in wooCommerce.', 'woo-stickers-by-webline' );?></p>
				<div class="learn-more">
						<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			<?php
		}
	}

	public function new_product_schedule_sticker() {
		
		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = date($format, $current_timestamp);
		?>
			<input type="datetime-local" class="custom_date_pkr" id="new_product_schedule_start_sticker_date_time" name="<?php echo $this->new_product_settings_key;?>[new_product_schedule_start_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->new_product_settings['new_product_schedule_start_sticker_date_time'] )) ? 
				($this->new_product_settings['new_product_schedule_start_sticker_date_time'] ) : $formatted_date_time ); ?>"
				/>
			<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
			<br>

			<input type="datetime-local" class="custom_date_pkr" id="new_product_schedule_end_sticker_date_time" name="<?php echo $this->new_product_settings_key;?>[new_product_schedule_end_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->new_product_settings['new_product_schedule_end_sticker_date_time'] )) ? 
				($this->new_product_settings['new_product_schedule_end_sticker_date_time'] ) : $formatted_date_time ); ?>"
				min="<?php echo $formatted_date_time; ?>" />
			<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>

			<br>

			<div class="woo_opt new_product_schedule_sticker_option" id="image_opt_sch">
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if($this->new_product_settings['new_product_schedule_sticker_option'] == 'image_schedule' || $this->new_product_settings['new_product_schedule_sticker_option'] == '') { echo "checked"; } ?> <?php checked($this->new_product_settings['new_product_schedule_sticker_option'] ); ?>/>
				<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if($this->new_product_settings['new_product_schedule_sticker_option'] == 'text_schedule') { echo "checked"; } ?> <?php checked( $this->new_product_settings['new_product_schedule_sticker_option'] ); ?>/>
				<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
				<input type="hidden" class="wli_product_schedule_option" id="new_product_schedule_sticker_option" name="<?php echo $this->new_product_settings_key; ?>[new_product_schedule_sticker_option]" value="<?php if($this->new_product_settings['new_product_schedule_sticker_option'] == '') { echo 'image_schedule'; } else { echo esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ); } ?>"/>
				<p class="description"><?php _e( 'Select any of option for the schedule sticker.', 'woo-stickers-by-webline' );?></p>
			</div>

			<?php
				if($this->new_product_settings['new_product_schedule_sticker_option'] == "text_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_opttext_sch { display: table-row; }
					</style>';
				}
				if($this->new_product_settings['new_product_schedule_sticker_option'] == "image_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}
				if($this->new_product_settings['new_product_schedule_sticker_option'] == "") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}		
	}

	public function new_product_schedule_custom_sticker() {
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		if ($this->new_product_settings ['new_product_schedule_custom_sticker'] == '')
		{				
			$image_url_sch = "";
			echo '<img class="new_product_schedule_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url_sch = $this->new_product_settings ['new_product_schedule_custom_sticker'];
			echo '<img class="new_product_schedule_custom_sticker" src="'.$image_url_sch.'" width="125px" height="auto" />';
		}
		echo '<br/>
			<input type="hidden" name="'.$this->new_product_settings_key .'[new_product_schedule_custom_sticker]" id="new_product_schedule_custom_sticker" value="'. esc_url( $image_url_sch ) .'" />
			<button class="upload_img_btn_sch button" id="upload_img_btn_sch">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
			<button class="remove_img_btn_sch button" id="remove_img_btn_sch">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
			'.$this->custom_sticker_script_sch('new_product_schedule_custom_sticker'); ?>

		<p class="description"><?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="new_product_schedule_sticker_image_width" placeholder="width" name="<?php echo $this->new_product_settings_key;?>[new_product_schedule_sticker_image_width]" <?php if ( isset( $this->new_product_settings['new_product_schedule_sticker_image_width'] ) ) { echo 'value="' . $this->new_product_settings['new_product_schedule_sticker_image_width'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="new_product_schedule_sticker_image_height" placeholder="height" name="<?php echo $this->new_product_settings_key;?>[new_product_schedule_sticker_image_height]" <?php if ( isset( $this->new_product_settings['new_product_schedule_sticker_image_height'] ) ) { echo 'value="' . $this->new_product_settings['new_product_schedule_sticker_image_height'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_schedule_custom_text() {
		?>
		<input type="text" id="new_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo $this->new_product_settings_key;?>[new_product_schedule_custom_text]" value="<?php echo esc_attr( $this->new_product_settings['new_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function enable_new_schedule_product_style() {
		?>
		<select id='enable_new_schedule_product_style'
			name="<?php echo $this->new_product_settings_key; ?>[enable_new_schedule_product_style]">
			<option value='ribbon'
				<?php selected( $this->new_product_settings['enable_new_schedule_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->new_product_settings['enable_new_schedule_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="new_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->new_product_settings_key;?>[new_product_schedule_custom_text_fontcolor]" value="<?php echo ($this->new_product_settings['new_product_schedule_custom_text_fontcolor']) ? esc_attr( $this->new_product_settings['new_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function new_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="new_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->new_product_settings_key;?>[new_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->new_product_settings['new_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function new_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="new_product_schedule_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->new_product_settings_key; ?>[new_product_schedule_text_padding_top]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_top'] ) ) { echo 'value="' . $this->new_product_settings['new_product_schedule_text_padding_top'] . '"'; } ?> />
		<input type="number" id="new_product_schedule_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->new_product_settings_key; ?>[new_product_schedule_text_padding_right]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_right'] ) ) { echo 'value="' . $this->new_product_settings['new_product_schedule_text_padding_right'] . '"'; } ?> />
		<input type="number" id="new_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->new_product_settings_key; ?>[new_product_schedule_text_padding_bottom]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_bottom'] ) ) { echo 'value="' . $this->new_product_settings['new_product_schedule_text_padding_bottom'] . '"'; } ?> />		
		<input type="number" id="new_product_schedule_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->new_product_settings_key; ?>[new_product_schedule_text_padding_left]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_left'] ) ) { echo 'value="' . $this->new_product_settings['new_product_schedule_text_padding_left'] . '"'; } ?> />
	
		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
	
		<?php
	}

	/**
	 * New Product Settings :: Image Width CSS for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_sticker_image_width() {
		
		?>
		<input type="number" class="small-text" id="new_product_sticker_image_width" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_image_width]" <?php if ( isset( $this->new_product_settings['new_product_sticker_image_width'] ) ) { echo 'value="' . $this->new_product_settings['new_product_sticker_image_width'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Image Height CSS for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_sticker_image_height() {
		
		?>
		<input type="number" class="small-text" id="new_product_sticker_image_height" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_image_height]" <?php if ( isset( $this->new_product_settings['new_product_sticker_image_height'] ) ) { echo 'value="' . $this->new_product_settings['new_product_sticker_image_height'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Sticker Settings :: Sticker Options
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_option() {
		?>
		<div class="woo_opt new_product_option">
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($this->new_product_settings['new_product_option'] == 'image' || $this->new_product_settings['new_product_option'] == '') { echo "checked"; } ?> <?php checked($this->new_product_settings['new_product_option'] ); ?>/>
			<label for="image"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($this->new_product_settings['new_product_option'] == 'text') { echo "checked"; } ?> <?php checked( $this->new_product_settings['new_product_option'] ); ?>/>
			<label for="text"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="new_product_option" name="<?php echo $this->new_product_settings_key; ?>[new_product_option]" value="<?php if($this->new_product_settings['new_product_option'] == '') { echo 'image'; } else { echo esc_attr( $this->new_product_settings['new_product_option'] ); } ?>"/>
			<p class="description"><?php _e( 'Select any of option for the custom sticker.', 'woo-stickers-by-webline' );?></p>
		</div>
		<?php
		if($this->new_product_settings['new_product_option'] == "text") {
			echo '<style type="text/css">
				.custom_option.custom_opttext { display: table-row; }
			</style>';
		}
		if($this->new_product_settings['new_product_option'] == "image") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
		if($this->new_product_settings['new_product_option'] == "") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
	}

	/**
	 * New Product Sticker Settings :: Custom text for New products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_custom_text() {
		?>
		<input type="text" id="new_product_custom_text" name="<?php echo $this->new_product_settings_key;?>[new_product_custom_text]" value="<?php echo esc_attr( $this->new_product_settings['new_product_custom_text'] ); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Sticker Settings :: Custom sticker type for New products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_new_product_style() {
		?>
		<select id='enable_new_product_style'
			name="<?php echo $this->new_product_settings_key; ?>[enable_new_product_style]">
			<option value='ribbon'
				<?php selected( $this->new_product_settings['enable_new_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->new_product_settings['enable_new_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' );?></p>
	<?php
	}

	/**
	 * New Product Sticker Settings :: Custom text font color for New products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_custom_text_fontcolor() {
		?>
		<input type="text" id="new_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->new_product_settings_key;?>[new_product_custom_text_fontcolor]" value="<?php echo ($this->new_product_settings['new_product_custom_text_fontcolor']) ? esc_attr( $this->new_product_settings['new_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Sticker Settings :: Custom text font color for New products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_custom_text_backcolor() {
		?>
		<input type="text" id="new_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->new_product_settings_key;?>[new_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->new_product_settings['new_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	/**
	 * New Product Sticker Settings :: Custom text padding for New products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */


	 public function new_product_custom_text_padding() {
		?>
		<input type="number" id="new_product_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->new_product_settings_key; ?>[new_product_text_padding_top]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_top'] ) ) { echo 'value="' . $this->new_product_settings['new_product_text_padding_top'] . '"'; } ?> />
		<input type="number" id="new_product_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->new_product_settings_key; ?>[new_product_text_padding_right]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_right'] ) ) { echo 'value="' . $this->new_product_settings['new_product_text_padding_right'] . '"'; } ?> />
		<input type="number" id="new_product_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->new_product_settings_key; ?>[new_product_text_padding_bottom]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_bottom'] ) ) { echo 'value="' . $this->new_product_settings['new_product_text_padding_bottom'] . '"'; } ?> />		
		<input type="number" id="new_product_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->new_product_settings_key; ?>[new_product_text_padding_left]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_left'] ) ) { echo 'value="' . $this->new_product_settings['new_product_text_padding_left'] . '"'; } ?> />

		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>

		<?php
	}
	

	/**
	 * New Product Settings :: Custom Stickers for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_custom_sticker() {
	
		?>
		
	<?php
	if (get_bloginfo('version') >= 3.5)
		wp_enqueue_media();
	else {
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
	}
	if ($this->new_product_settings ['new_product_custom_sticker'] == '')
	{				
		$image_url = "";
		echo '<img class="new_product_custom_sticker" width="125px" height="auto" />';
	}
	else
	{
		$image_url = $this->new_product_settings ['new_product_custom_sticker'];
		echo '<img class="new_product_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
	}
	
	
	echo '		<br/>
				<input type="hidden" name="'.$this->new_product_settings_key .'[new_product_custom_sticker]" id="new_product_custom_sticker" value="'. esc_url( $image_url ) .'" />
				<button class="upload_img_btn button">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
				<button class="remove_img_btn button">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
			'.$this->custom_sticker_script('new_product_custom_sticker'); ?>

	<p class="description"><?php _e( 'Add your own custom new product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
	<?php
	}

	/**
	 * Sale Product Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sale_product_sticker() {
		?>
		<select id='enable_sale_product_sticker'
			name="<?php echo $this->sale_product_settings_key; ?>[enable_sale_product_sticker]">
			<option value='yes'
				<?php selected( $this->sale_product_settings['enable_sale_product_sticker'], 'yes',true );?>><?php _e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( $this->sale_product_settings['enable_sale_product_sticker'], 'no',true );?>><?php _e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Control sticker display for products which are marked as under sale in wooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	/**
	 * Sale Product Settings :: Sticker Position
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_position() {
		?>
		<select id='sale_product_position'
			name="<?php echo $this->sale_product_settings_key; ?>[sale_product_position]">
			<option value='left'
				<?php selected( $this->sale_product_settings['sale_product_position'], 'left',true );?>><?php _e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( $this->sale_product_settings['sale_product_position'], 'right',true );?>><?php _e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select the position of the sticker.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sale Product Settings :: Top CSS for Sale Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_sticker_top() {
		?>
		<input type="number" class="small-text" id="sale_product_sticker_top" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_top]" value="<?php echo ( $this->sale_product_settings['sale_product_sticker_top']); ?>" />
		<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	/**
	 * New Product Settings :: Left/Right CSS for Sale Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_sticker_left_right() {
		
		?>
		<input type="number" class="small-text" id="sale_product_sticker_left_right" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_left_right]" value="<?php echo ( $this->sale_product_settings['sale_product_sticker_left_right']); ?>" />
		<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).' );?></p>
		<?php
	}

	public function sale_product_sticker_rotate() {
		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
				<input type="number" class="small-text" id="sale_product_sticker_rotate" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_rotate]" value="<?php echo ( $this->sale_product_settings['sale_product_sticker_rotate']); ?>" />
				<p class="description"><?php _e( 'Specify the degree to rotate the sticker.' );?></p>
			<?php
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<input type="number" class="small-text file-input" value="0" disabled/>
				<p class="description"><?php _e( 'Specify the degree to rotate the sticker.' );?></p>
	
				<div class="ribbon">
				<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
					<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
					<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
					<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
					<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
					<defs>
					<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
					<stop stop-color="#FDAB00"/>
					<stop offset="1" stop-color="#CD8F0D"/>
					</linearGradient>
					<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
					<stop stop-color="#FDAB00"/>
					<stop offset="1" stop-color="#CD8F0D"/>
					</linearGradient>
					</defs>
				</svg>
			</div>
	
			<div class="learn-more">
				<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
			</div>
			</div>
			
			<?php
		}

		
	}

	public function sale_product_sticker_animation() {
		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<select id="sale_product_sticker_animation_type" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_sticker_animation_type]">
				<?php
					$border_types = array(
						'none' => 'None',
						'spin' => 'Spin',
						'swing' => 'Swing',
						'zoominout' => 'Zoom In / Out',
						'leftright' => 'Left-Right',
						'updown' => 'Up-Down',
					);
					$current_value = esc_attr( $this->sale_product_settings['sale_product_sticker_animation_type'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>
			<br>
			<div id="zoominout-options-sale-global" style="display: none;">
				<input type="number" id="sale_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php _e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
				<br>
			</div>
			<select id="sale_product_sticker_animation_direction" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_sticker_animation_direction]">
				<?php
					$border_types = array(
						'normal' => 'Normal',
						'reverse' => 'Reverse',
						'alternate' => 'Alternate',
						'alternate-reverse' => 'Alternate Reverse',
					);
					$current_value = esc_attr( $this->sale_product_settings['sale_product_sticker_animation_direction'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation direction', 'woo-stickers-by-webline' );?></p>
			<br>
			<input type="text" id="sale_product_sticker_animation_iteration_count" step="any" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_animation_iteration_count]" 
			value="<?php echo ( $this->sale_product_settings['sale_product_sticker_animation_iteration_count']); ?>" placeholder='Iteration Count'/>
			<p class="description"><?php _e( 'Specify animation iteration count (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			<br>
			<input type="number" id="sale_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_animation_delay]" 
			value="<?php echo ( $this->sale_product_settings['sale_product_sticker_animation_delay']); ?>" placeholder='Delay'/>
			<p class="description"><?php _e( 'Specify animation delay time in seconds (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
		<?php
		}else{
			?>
				<div class="wosbw-pro-ribbon-banner">
					<select disabled>
						<option value="none">None</option>
					</select>
					<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>
					<div class="ribbon">
						<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
							<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
							<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
							<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
							<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
							<defs>
							<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
							<stop stop-color="#FDAB00"/>
							<stop offset="1" stop-color="#CD8F0D"/>
							</linearGradient>
							<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
							<stop stop-color="#FDAB00"/>
							<stop offset="1" stop-color="#CD8F0D"/>
							</linearGradient>
							</defs>
						</svg>
					</div>
					<div class="learn-more">
						<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
					</div>
				</div>
			<?php
		}

		
		
	}

	/**
	 * New Product Settings :: Image Width CSS for Sale Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_sticker_image_width() {
		
		?>
		<input type="number" class="small-text" id="sale_product_sticker_image_width" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_image_width]" value="<?php echo ( $this->sale_product_settings['sale_product_sticker_image_width']); ?>" />
		<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Image Height CSS for Sale Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_sticker_image_height() {
		
		?>
		<input type="number" class="small-text" id="sale_product_sticker_image_height" name="<?php echo $this->sale_product_settings_key;?>[sale_product_sticker_image_height]" value="<?php echo ( $this->sale_product_settings['sale_product_sticker_image_height']); ?>" />
		<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	/**
	 * Sale Product Sticker Settings :: Sticker Options
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_option() {
		?>
		<div class="woo_opt sale_product_option">
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($this->sale_product_settings['sale_product_option'] == 'image' || $this->sale_product_settings['sale_product_option'] == '') { echo "checked"; } ?> <?php checked($this->sale_product_settings['sale_product_option'] ); ?>/>
			<label for="image"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($this->sale_product_settings['sale_product_option'] == 'text') { echo "checked"; } ?> <?php checked( $this->sale_product_settings['sale_product_option'] ); ?>/>
			<label for="text"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="sale_product_option" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_option]" value="<?php if($this->sale_product_settings['sale_product_option'] == '') { echo 'image'; } else { echo esc_attr( $this->sale_product_settings['sale_product_option'] ); } ?>"/>
			<p class="description"><?php _e( 'Select any of option for the custom sticker.', 'woo-stickers-by-webline' );?></p>
		</div>
		<?php
		if($this->sale_product_settings['sale_product_option'] == "text") {
			echo '<style type="text/css">
				.custom_option.custom_opttext { display: table-row; }
			</style>';
		}
		if($this->sale_product_settings['sale_product_option'] == "image") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
		if($this->sale_product_settings['sale_product_option'] == "") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
	}

	/**
	 * Sale Product Sticker Settings :: Custom text for Sale products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_custom_text() {
		?>
		<input type="text" id="sale_product_custom_text" name="<?php echo $this->sale_product_settings_key;?>[sale_product_custom_text]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_custom_text']); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as custom sticker on sale products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sale Product Sticker Settings :: Custom sticker type for Sale products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sale_product_style() {
		?>
		<select id='enable_sale_product_style'
			name="<?php echo $this->sale_product_settings_key; ?>[enable_sale_product_style]">
			<option value='ribbon'
				<?php selected( $this->sale_product_settings['enable_sale_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->sale_product_settings['enable_sale_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker type to show on Sale Products.', 'woo-stickers-by-webline' );?></p>
	<?php
	}

	/**
	 * Sale Product Sticker Settings :: Custom text font color for Sale products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_custom_text_fontcolor() {
		?>
		<input type="text" id="sale_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->sale_product_settings_key;?>[sale_product_custom_text_fontcolor]" value="<?php echo ($this->sale_product_settings['sale_product_custom_text_fontcolor']) ? esc_attr( $this->sale_product_settings['sale_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on sale products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sale Product Sticker Settings :: Custom text font color for Sale products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_custom_text_backcolor() {
		?>
		<input type="text" id="sale_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->sale_product_settings_key;?>[sale_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on sale products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sale Product Sticker Settings :: Custom text padding for Sale products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */

	public function sale_product_custom_text_padding() {
		?>
		<input type="number" class="small-text" id="sale_product_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_text_padding_top]" value="<?php echo ( $this->sale_product_settings['sale_product_text_padding_top'] ); ?>" />
		<input type="number" class="small-text" id="sale_product_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_text_padding_right]" value="<?php echo ( $this->sale_product_settings['sale_product_text_padding_right'] ); ?>" />
		<input type="number" class="small-text" id="sale_product_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_text_padding_bottom]" value="<?php echo ( $this->sale_product_settings['sale_product_text_padding_bottom'] ); ?>" />		
		<input type="number" class="small-text" id="sale_product_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_text_padding_left]" value="<?php echo ( $this->sale_product_settings['sale_product_text_padding_left'] ); ?>" />

		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>

		<?php
	}
	
	public function enable_sale_product_schedule_sticker() {

		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
				<select id='enable_sale_product_schedule_sticker'
					name="<?php echo $this->sale_product_settings_key; ?>[enable_sale_product_schedule_sticker]">
					<option value='yes'
						<?php selected( !empty($this->sale_product_settings['enable_sale_product_schedule_sticker']) ? $this->sale_product_settings['enable_sale_product_schedule_sticker'] : 'no', 'yes', true ); ?>>
						<?php _e( 'Yes', 'woo-stickers-by-webline' ); ?>
					</option>
					<option value='no'
						<?php selected( !empty($this->sale_product_settings['enable_sale_product_schedule_sticker']) ? $this->sale_product_settings['enable_sale_product_schedule_sticker'] : 'no', 'no', true ); ?>>
						<?php _e( 'No', 'woo-stickers-by-webline' ); ?>
					</option>
				</select>

				<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SALE in wooCommerce.', 'woo-stickers-by-webline' );?></p>
			<?php	
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<select class="file-input" disabled>
					<option>No</option>
				</select>
				<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SALE in wooCommerce.', 'woo-stickers-by-webline' );?></p>

				<div class="ribbon">
					<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
						<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
						<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
						<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
						<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
						<defs>
						<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						</defs>
					</svg>
				</div>

				<div class="learn-more">
					<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			</div>
		<?php
		}
	}
	
	public function sale_product_schedule_sticker() {
		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = date($format, $current_timestamp);
		?>
			<input type="datetime-local" class="custom_date_pkr" id="sale_product_schedule_start_sticker_date_time" name="<?php echo $this->sale_product_settings_key;?>[sale_product_schedule_start_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->sale_product_settings['sale_product_schedule_start_sticker_date_time'] )) ? 
				($this->sale_product_settings['sale_product_schedule_start_sticker_date_time'] ) : $formatted_date_time ); ?>"
				/>
			<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>

			<br>

			<input type="datetime-local" class="custom_date_pkr" id="sale_product_schedule_end_sticker_date_time" name="<?php echo $this->sale_product_settings_key;?>[sale_product_schedule_end_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->sale_product_settings['sale_product_schedule_end_sticker_date_time'] )) ? 
				($this->sale_product_settings['sale_product_schedule_end_sticker_date_time'] ) : $formatted_date_time ); ?>"
				min="<?php echo $formatted_date_time; ?>" />
			<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
	
			<br>
	
			<div class="woo_opt sale_product_schedule_sticker_option">
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if($this->sale_product_settings['sale_product_schedule_sticker_option'] == 'image_schedule' || $this->sale_product_settings['sale_product_schedule_sticker_option'] == '') { echo "checked"; } ?> <?php checked($this->sale_product_settings['sale_product_schedule_sticker_option'] ); ?>/>
				<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if($this->sale_product_settings['sale_product_schedule_sticker_option'] == 'text_schedule') { echo "checked"; } ?> <?php checked( $this->sale_product_settings['sale_product_schedule_sticker_option'] ); ?>/>
				<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
				<input type="hidden" class="wli_product_schedule_option" id="sale_product_schedule_sticker_option" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_schedule_sticker_option]" value="<?php if($this->sale_product_settings['sale_product_schedule_sticker_option'] == '') { echo 'image_schedule'; } else { echo esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ); } ?>"/>
				<p class="description"><?php _e( 'Select any of option for the schedule sticker.', 'woo-stickers-by-webline' );?></p>
			</div>
	
			<?php
				if($this->sale_product_settings['sale_product_schedule_sticker_option'] == "text_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_opttext_sch { display: table-row; }
					</style>';
				}
				if($this->sale_product_settings['sale_product_schedule_sticker_option'] == "image_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}
				if($this->sale_product_settings['sale_product_schedule_sticker_option'] == "") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}		
	}
	
	public function sale_product_schedule_custom_sticker() {
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		if ($this->sale_product_settings ['sale_product_schedule_custom_sticker'] == '')
		{				
			$image_url = "";
			echo '<img class="sale_product_schedule_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url = $this->sale_product_settings ['sale_product_schedule_custom_sticker'];
			echo '<img class="sale_product_schedule_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
		}
		echo '<br/>
			<input type="hidden" name="'.$this->sale_product_settings_key .'[sale_product_schedule_custom_sticker]" id="sale_product_schedule_custom_sticker" value="'. esc_url( $image_url ) .'" />
			<button class="upload_img_btn_sch button" id="upload_img_btn_sch">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
			<button class="remove_img_btn_sch button" id="remove_img_btn_sch">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
			'.$this->custom_sticker_script_sch('sale_product_schedule_custom_sticker'); ?>
	
		<p class="description"><?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="sale_product_schedule_sticker_image_width" placeholder="width" name="<?php echo $this->sale_product_settings_key;?>[sale_product_schedule_sticker_image_width]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_sticker_image_width'] ) ) { echo 'value="' . $this->sale_product_settings['sale_product_schedule_sticker_image_width'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="sale_product_schedule_sticker_image_height" placeholder="height" name="<?php echo $this->sale_product_settings_key;?>[sale_product_schedule_sticker_image_height]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_sticker_image_height'] ) ) { echo 'value="' . $this->sale_product_settings['sale_product_schedule_sticker_image_height'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_custom_text() {
		?>
		<input type="text" id="sale_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo $this->sale_product_settings_key;?>[sale_product_schedule_custom_text]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function enable_sale_schedule_product_style() {
		?>
		<select id='enable_sale_schedule_product_style'
			name="<?php echo $this->sale_product_settings_key; ?>[enable_sale_schedule_product_style]">
			<option value='ribbon'
				<?php selected( $this->sale_product_settings['enable_sale_schedule_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->sale_product_settings['enable_sale_schedule_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sale_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="sale_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->sale_product_settings_key;?>[sale_product_schedule_custom_text_fontcolor]" value="<?php echo ($this->sale_product_settings['sale_product_schedule_custom_text_fontcolor']) ? esc_attr( $this->sale_product_settings['sale_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="sale_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->sale_product_settings_key;?>[sale_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="sale_product_schedule_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_schedule_text_padding_top]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_top'] ) ) { echo 'value="' . $this->sale_product_settings['sale_product_schedule_text_padding_top'] . '"'; } ?> />
		<input type="number" id="sale_product_schedule_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_schedule_text_padding_right]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_right'] ) ) { echo 'value="' . $this->sale_product_settings['sale_product_schedule_text_padding_right'] . '"'; } ?> />
		<input type="number" id="sale_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_schedule_text_padding_bottom]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_bottom'] ) ) { echo 'value="' . $this->sale_product_settings['sale_product_schedule_text_padding_bottom'] . '"'; } ?> />		
		<input type="number" id="sale_product_schedule_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->sale_product_settings_key; ?>[sale_product_schedule_text_padding_left]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_left'] ) ) { echo 'value="' . $this->sale_product_settings['sale_product_schedule_text_padding_left'] . '"'; } ?> />
	
		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
	
		<?php
	}

	/**
	 * Sale Product Settings :: Custom Stickers for Sale Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_custom_sticker() {
	
		?>
			
		<?php
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		if ($this->sale_product_settings ['sale_product_custom_sticker'] == '' )
		{
			$image_url = "";
			echo '<img class="sale_product_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url = $this->sale_product_settings ['sale_product_custom_sticker'];
			echo '<img class="sale_product_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
		}
		echo '		<br/>
					<input type="hidden" name="'.$this->sale_product_settings_key .'[sale_product_custom_sticker]" id="sale_product_custom_sticker" value="'. esc_url( $image_url ) .'" />
					<button class="upload_img_btn button">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
					<button class="remove_img_btn button">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
				'.$this->custom_sticker_script('sale_product_custom_sticker'); ?>		
		<p class="description"><?php _e( 'Add your own custom sale product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
			}
	/**
	 * Sold Product Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sold_product_sticker() {
		?>
		<select id='enable_sold_product_sticker'
			name="<?php echo $this->sold_product_settings_key; ?>[enable_sold_product_sticker]">
			<option value='yes'
				<?php selected( $this->sold_product_settings['enable_sold_product_sticker'], 'yes',true );?>><?php _e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( $this->sold_product_settings['enable_sold_product_sticker'], 'no',true );?>><?php _e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Control sticker display for products which are marked as under sold in wooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sold Product Settings :: Sticker Position
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_position() {
		?>
		<select id='sold_product_position'
			name="<?php echo $this->sold_product_settings_key; ?>[sold_product_position]">
			<option value='left'
				<?php selected( $this->sold_product_settings['sold_product_position'], 'left',true );?>><?php _e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( $this->sold_product_settings['sold_product_position'], 'right',true );?>><?php _e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select the position of the sticker.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sold Product Settings :: Top CSS for Sold Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_sticker_left_right() {
		
		?>
		<input type="number" class="small-text" id="sold_product_sticker_left_right" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_left_right]" value="<?php echo ( $this->sold_product_settings['sold_product_sticker_left_right']); ?>" />
		<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Top CSS for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_sticker_top() {
		
		?>
		<input type="number" class="small-text" id="sold_product_sticker_top" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_top]" value="<?php echo ( $this->sold_product_settings['sold_product_sticker_top']); ?>" />
		<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sold_product_sticker_rotate() {
		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<input type="number" class="small-text" id="sold_product_sticker_rotate" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_rotate]" value="<?php echo ( $this->sold_product_settings['sold_product_sticker_rotate']); ?>" />
			<p class="description"><?php _e( 'Specify the degree to rotate the sticker.' );?></p>
			<?php	
		}else{
			?>
		<div class="wosbw-pro-ribbon-banner">
			<input type="number" class="small-text file-input" value="0" disabled/>
			<p class="description"><?php _e( 'Specify the degree to rotate the sticker.' );?></p>

			<div class="ribbon">
				<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
					<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
					<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
					<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
					<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
					<defs>
					<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
					<stop stop-color="#FDAB00"/>
					<stop offset="1" stop-color="#CD8F0D"/>
					</linearGradient>
					<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
					<stop stop-color="#FDAB00"/>
					<stop offset="1" stop-color="#CD8F0D"/>
					</linearGradient>
					</defs>
				</svg>
			</div>

			<div class="learn-more">
				<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
			</div>
		</div>
		<?php
		}

		
	}
	
	public function sold_product_sticker_animation() {
		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<select id="sold_product_sticker_animation_type" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_sticker_animation_type]">
				<?php
					$border_types = array(
						'none' => 'None',
						'spin' => 'Spin',
						'swing' => 'Swing',
						'zoominout' => 'Zoom In / Out',
						'leftright' => 'Left-Right',
						'updown' => 'Up-Down',
					);
					$current_value = esc_attr( $this->sold_product_settings['sold_product_sticker_animation_type'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>
			<br>
			<div id="zoominout-options-sold-global" style="display: none;">
				<input type="number" id="sold_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php _e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
				<br>
			</div>
			<select id="sold_product_sticker_animation_direction" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_sticker_animation_direction]">
				<?php
					$border_types = array(
						'normal' => 'Normal',
						'reverse' => 'Reverse',
						'alternate' => 'Alternate',
						'alternate-reverse' => 'Alternate Reverse',
					);
					$current_value = esc_attr( $this->sold_product_settings['sold_product_sticker_animation_direction'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation direction', 'woo-stickers-by-webline' );?></p>
			<br>
			<input type="text" id="sold_product_sticker_animation_iteration_count" step="any" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_animation_iteration_count]" 
			value="<?php echo ( $this->sold_product_settings['sold_product_sticker_animation_iteration_count']); ?>" placeholder='Iteration Count'/>
			<p class="description"><?php _e( 'Specify animation iteration count (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			<br>
			<input type="number" id="sold_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_animation_delay]" 
			value="<?php echo ( $this->sold_product_settings['sold_product_sticker_animation_delay']); ?>" placeholder='Delay'/>
			<p class="description"><?php _e( 'Specify animation delay time in seconds (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
		<?php
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<select disabled>
					<option>None</option>
				</select>
				<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>

				<div class="ribbon">
					<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
						<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
						<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
						<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
						<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
						<defs>
						<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						</defs>
					</svg>
				</div>

				<div class="learn-more">
					<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			</div>
			<?php
		}
		
	}

	public function enable_sold_product_schedule_sticker() {

		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<select id='enable_sold_product_schedule_sticker'
					name="<?php echo $this->sold_product_settings_key; ?>[enable_sold_product_schedule_sticker]">
				<option value='yes'
					<?php selected( !empty($this->sold_product_settings['enable_sold_product_schedule_sticker']) ? $this->sold_product_settings['enable_sold_product_schedule_sticker'] : 'no', 'yes', true ); ?>>
					<?php _e( 'Yes', 'woo-stickers-by-webline' ); ?>
				</option>
				<option value='no'
					<?php selected( !empty($this->sold_product_settings['enable_sold_product_schedule_sticker']) ? $this->sold_product_settings['enable_sold_product_schedule_sticker'] : 'no', 'no', true ); ?>>
					<?php _e( 'No', 'woo-stickers-by-webline' ); ?>
				</option>
			</select>

			<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SOLD in wooCommerce.', 'woo-stickers-by-webline' );?></p>
			<?php	
		}else{
				?>
				<div class="wosbw-pro-ribbon-banner">
					<select disabled>
						<option>No</option>
					</select>
					<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as SOLD in wooCommerce.', 'woo-stickers-by-webline' );?></p>

					<div class="ribbon">
						<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
							<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
							<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
							<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
							<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
							<defs>
							<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
							<stop stop-color="#FDAB00"/>
							<stop offset="1" stop-color="#CD8F0D"/>
							</linearGradient>
							<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
							<stop stop-color="#FDAB00"/>
							<stop offset="1" stop-color="#CD8F0D"/>
							</linearGradient>
							</defs>
						</svg>
					</div>

					<div class="learn-more">
						<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
					</div>
				</div>
				<?php
		}
	}
	
	public function sold_product_schedule_sticker() {
		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = date($format, $current_timestamp);
		?>
			<input type="datetime-local" class="custom_date_pkr" id="sold_product_schedule_start_sticker_date_time" name="<?php echo $this->sold_product_settings_key;?>[sold_product_schedule_start_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->sold_product_settings['sold_product_schedule_start_sticker_date_time'] )) ? 
				($this->sold_product_settings['sold_product_schedule_start_sticker_date_time'] ) : $formatted_date_time ); ?>"
				/>
			<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>

			<br>

			<input type="datetime-local" class="custom_date_pkr" id="sold_product_schedule_end_sticker_date_time" name="<?php echo $this->sold_product_settings_key;?>[sold_product_schedule_end_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->sold_product_settings['sold_product_schedule_end_sticker_date_time'] )) ? 
				($this->sold_product_settings['sold_product_schedule_end_sticker_date_time'] ) : $formatted_date_time ); ?>"
				min="<?php echo $formatted_date_time; ?>" />
			<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
	
			<br>
	
			<div class="woo_opt sold_product_schedule_sticker_option">
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if($this->sold_product_settings['sold_product_schedule_sticker_option'] == 'image_schedule' || $this->sold_product_settings['sold_product_schedule_sticker_option'] == '') { echo "checked"; } ?> <?php checked($this->sold_product_settings['sold_product_schedule_sticker_option'] ); ?>/>
				<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if($this->sold_product_settings['sold_product_schedule_sticker_option'] == 'text_schedule') { echo "checked"; } ?> <?php checked( $this->sold_product_settings['sold_product_schedule_sticker_option'] ); ?>/>
				<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
				<input type="hidden" class="wli_product_schedule_option" id="sold_product_schedule_sticker_option" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_schedule_sticker_option]" value="<?php if($this->sold_product_settings['sold_product_schedule_sticker_option'] == '') { echo 'image_schedule'; } else { echo esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ); } ?>"/>
				<p class="description"><?php _e( 'Select any of option for the schedule sticker.', 'woo-stickers-by-webline' );?></p>
			</div>
	
			<?php
				if($this->sold_product_settings['sold_product_schedule_sticker_option'] == "text_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_opttext_sch { display: table-row; }
					</style>';
				}
				if($this->sold_product_settings['sold_product_schedule_sticker_option'] == "image_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}
				if($this->sold_product_settings['sold_product_schedule_sticker_option'] == "") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}		
	}
	
	public function sold_product_schedule_custom_sticker() {
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		if ($this->sold_product_settings ['sold_product_schedule_custom_sticker'] == '')
		{				
			$image_url = "";
			echo '<img class="sold_product_schedule_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url = $this->sold_product_settings ['sold_product_schedule_custom_sticker'];
			echo '<img class="sold_product_schedule_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
		}
		echo '<br/>
			<input type="hidden" name="'.$this->sold_product_settings_key .'[sold_product_schedule_custom_sticker]" id="sold_product_schedule_custom_sticker" value="'. esc_url( $image_url ) .'" />
			<button class="upload_img_btn button" id="upload_img_btn_sch">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
			<button class="remove_img_btn button" id="remove_img_btn_sch">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
			'.$this->custom_sticker_script('sold_product_schedule_custom_sticker'); ?>
	
		<p class="description"><?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="sold_product_schedule_sticker_image_width" placeholder="width" name="<?php echo $this->sold_product_settings_key;?>[sold_product_schedule_sticker_image_width]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_sticker_image_width'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_schedule_sticker_image_width'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="sold_product_schedule_sticker_image_height" placeholder="height" name="<?php echo $this->sold_product_settings_key;?>[sold_product_schedule_sticker_image_height]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_sticker_image_height'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_schedule_sticker_image_height'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_custom_text() {
		?>
		<input type="text" id="sold_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo $this->sold_product_settings_key;?>[sold_product_schedule_custom_text]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function enable_sold_schedule_product_style() {
		?>
		<select id='enable_sold_schedule_product_style'
			name="<?php echo $this->sold_product_settings_key; ?>[enable_sold_schedule_product_style]">
			<option value='ribbon'
				<?php selected( $this->sold_product_settings['enable_sold_schedule_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->sold_product_settings['enable_sold_schedule_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sold_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="sold_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->sold_product_settings_key;?>[sold_product_schedule_custom_text_fontcolor]" value="<?php echo ($this->sold_product_settings['sold_product_schedule_custom_text_fontcolor']) ? esc_attr( $this->sold_product_settings['sold_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="sold_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->sold_product_settings_key;?>[sold_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="sold_product_schedule_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_schedule_text_padding_top]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_top'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_schedule_text_padding_top'] . '"'; } ?> />
		<input type="number" id="sold_product_schedule_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_schedule_text_padding_right]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_right'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_schedule_text_padding_right'] . '"'; } ?> />
		<input type="number" id="sold_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_schedule_text_padding_bottom]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_bottom'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_schedule_text_padding_bottom'] . '"'; } ?> />		
		<input type="number" id="sold_product_schedule_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_schedule_text_padding_left]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_left'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_schedule_text_padding_left'] . '"'; } ?> />
	
		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
	
		<?php
	}

	/**
	 * Sold Product Settings :: Image Width CSS for Sold Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_sticker_image_width() {
		
		?>
		<input type="number" class="small-text" id="sold_product_sticker_image_width" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_image_width]" value="<?php echo ( $this->sold_product_settings['sold_product_sticker_image_width']); ?>" />
		<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Image Height CSS for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_sticker_image_height() {
		
		?>
		<input type="number" class="small-text" id="sold_product_sticker_image_height" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_image_height]" value="<?php echo ( $this->sold_product_settings['sold_product_sticker_image_height']); ?>" />
		<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sold Product Sticker Settings :: Sticker Options
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_option() {
		?>
		<div class="woo_opt sold_product_option">
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($this->sold_product_settings['sold_product_option'] == 'image' || $this->sold_product_settings['sold_product_option'] == '') { echo "checked"; } ?> <?php checked($this->sold_product_settings['sold_product_option'] ); ?>/>
			<label for="image"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($this->sold_product_settings['sold_product_option'] == 'text') { echo "checked"; } ?> <?php checked( $this->sold_product_settings['sold_product_option'] ); ?>/>
			<label for="text"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="sold_product_option" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_option]" value="<?php if($this->sold_product_settings['sold_product_option'] == '') { echo 'image'; } else { echo esc_attr( $this->sold_product_settings['sold_product_option'] ); } ?>"/>
			<p class="description"><?php _e( 'Select any of option for the custom sticker.', 'woo-stickers-by-webline' );?></p>
		</div>
		<?php
		if($this->sold_product_settings['sold_product_option'] == "text") {
			echo '<style type="text/css">
				.custom_option.custom_opttext { display: table-row; }
			</style>';
		}
		if($this->sold_product_settings['sold_product_option'] == "image") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
		if($this->sold_product_settings['sold_product_option'] == "") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
	}

	/**
	 * Sold Product Sticker Settings :: Custom text for Sold products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_custom_text() {
		?>
		<input type="text" id="sold_product_custom_text" name="<?php echo $this->sold_product_settings_key;?>[sold_product_custom_text]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_custom_text'] ); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as custom sticker on products, Leave it blank if you use WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sold Product Sticker Settings :: Custom sticker type for Sold products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sold_product_style() {
		?>
		<select id='enable_sold_product_style'
			name="<?php echo $this->sold_product_settings_key; ?>[enable_sold_product_style]">
			<option value='ribbon'
				<?php selected( $this->sold_product_settings['enable_sold_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->sold_product_settings['enable_sold_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker type to show on Sold Products.', 'woo-stickers-by-webline' );?></p>
	<?php
	}

	/**
	 * Sold Product Sticker Settings :: Custom text font color for Sold products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_custom_text_fontcolor() {
		?>
		<input type="text" id="sold_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->sold_product_settings_key;?>[sold_product_custom_text_fontcolor]" value="<?php echo ($this->sold_product_settings['sold_product_custom_text_fontcolor']) ? esc_attr( $this->sold_product_settings['sold_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on sold products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sold Product Sticker Settings :: Custom text font color for Sold products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_custom_text_backcolor() {
		?>
		<input type="text" id="sold_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->sold_product_settings_key;?>[sold_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on sold products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Sold Product Sticker Settings :: Custom text padding for Sold products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */

	 public function sold_product_custom_text_padding() {
		?>
		<input type="number" id="sold_product_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_text_padding_top]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_top'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_text_padding_top'] . '"'; } ?> />
		<input type="number" id="sold_product_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_text_padding_right]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_right'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_text_padding_right'] . '"'; } ?> />
		<input type="number" id="sold_product_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_text_padding_bottom]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_bottom'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_text_padding_bottom'] . '"'; } ?> />		
		<input type="number" id="sold_product_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->sold_product_settings_key; ?>[sold_product_text_padding_left]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_left'] ) ) { echo 'value="' . $this->sold_product_settings['sold_product_text_padding_left'] . '"'; } ?> />

		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>

		<?php
	}

	/**
	 * Sold Product Settings :: Custom Stickers for Sold Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_custom_sticker() {

		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		//print_r(CV_DEFAULT_IMAGE); die;	
		if ($this->sold_product_settings ['sold_product_custom_sticker'] == '')
		{
			$image_url = "";
			echo '<img class="sold_product_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url = $this->sold_product_settings ['sold_product_custom_sticker'];
			echo '<img class="sold_product_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
		}
		echo '		<br/>
					<input type="hidden" name="'.$this->sold_product_settings_key .'[sold_product_custom_sticker]" id="sold_product_custom_sticker" value="'. esc_url( $image_url ) .'" />
					<button class="upload_img_btn_sch button">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
					<button class="remove_img_btn_sch button">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
				'.$this->custom_sticker_script_sch('sold_product_custom_sticker'); ?>			
		<p class="description"><?php _e( 'Add your own custom sold product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Custom Product Sticker Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_cust_product_sticker() {
		?>
		<select id='enable_cust_product_sticker'
			name="<?php echo $this->cust_product_settings_key; ?>[enable_cust_product_sticker]">
			<option value='yes'
				<?php selected( $this->cust_product_settings['enable_cust_product_sticker'], 'yes',true );?>><?php _e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( $this->cust_product_settings['enable_cust_product_sticker'], 'no',true );?>><?php _e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Control custom sticker display for all products in wooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Custom Product Sticker Settings :: Sticker Position
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_position() {
		?>
		<select id='cust_product_position'
			name="<?php echo $this->cust_product_settings_key; ?>[cust_product_position]">
			<option value='left'
				<?php selected( $this->cust_product_settings['cust_product_position'], 'left',true );?>><?php _e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( $this->cust_product_settings['cust_product_position'], 'right',true );?>><?php _e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select the position of the custom sticker.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Top CSS for Custom Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_sticker_left_right() {
		
		?>
		<input type="number" class="small-text" id="cust_product_sticker_left_right" name="<?php echo $this->cust_product_settings_key;?>[cust_product_sticker_left_right]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_left_right'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_sticker_left_right'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify sticker position from left or right based on Sticker Position you choose above (Leave empty to use default).' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Top CSS for Custom Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_sticker_top() {
		
		?>
		<input type="number" class="small-text" id="cust_product_sticker_top" name="<?php echo $this->cust_product_settings_key;?>[cust_product_sticker_top]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_top'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_sticker_top'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify sticker position from top (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function cust_product_sticker_rotate() {
		if(get_option('wosbw_premium_access_allowed') == 1){

		?>
		<input type="number" class="small-text" id="cust_product_sticker_rotate" name="<?php echo $this->cust_product_settings_key;?>[cust_product_sticker_rotate]" value="<?php echo ( $this->cust_product_settings['cust_product_sticker_rotate']); ?>" />
		<p class="description"><?php _e( 'Specify the degree to rotate the sticker.' );?></p>
		<?php
		}
		else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<input type="number" class="small-text file-input" value="0" disabled/>
				<p class="description"><?php _e( 'Specify the degree to rotate the sticker.' );?></p>

				<div class="ribbon">
					<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
						<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
						<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
						<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
						<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
						<defs>
						<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						</defs>
					</svg>
				</div>

				<div class="learn-more">
					<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			</div>
			<?php
		}
	}
		
	public function cust_product_sticker_animation() {
		if(get_option('wosbw_premium_access_allowed') == 1){
			?>
			<select id="cust_product_sticker_animation_type" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_sticker_animation_type]">
				<?php
					$border_types = array(
						'none' => 'None',
						'spin' => 'Spin',
						'swing' => 'Swing',
						'zoominout' => 'Zoom In / Out',
						'leftright' => 'Lef-Right',
						'updown' => 'Up-Down',
					);
					$current_value = esc_attr( $this->cust_product_settings['cust_product_sticker_animation_type'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>
			<br>
			<div id="zoominout-options-cust-global" style="display: none;">
				<input type="number" id="sold_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo $this->sold_product_settings_key;?>[sold_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php _e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
				<br>
			</div>
			<select id="cust_product_sticker_animation_direction" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_sticker_animation_direction]">
				<?php
					$border_types = array(
						'normal' => 'Normal',
						'reverse' => 'Reverse',
						'alternate' => 'Alternate',
						'alternate-reverse' => 'Alternate Reverse',
					);
					$current_value = esc_attr( $this->cust_product_settings['cust_product_sticker_animation_direction'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='$value' $selected>$label</option>";
					}
				?>
			</select>
			<p class="description"><?php _e( 'Specify animation direction', 'woo-stickers-by-webline' );?></p>
			<br>
			<input type="text" id="cust_product_sticker_animation_iteration_count" step="any" name="<?php echo $this->cust_product_settings_key;?>[cust_product_sticker_animation_iteration_count]" 
			value="<?php echo ( $this->cust_product_settings['cust_product_sticker_animation_iteration_count']); ?>" placeholder='Iteration Count'/>
			<p class="description"><?php _e( 'Specify animation iteration count (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			<br>
			<input type="number" id="cust_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo $this->cust_product_settings_key;?>[cust_product_sticker_animation_delay]" 
			value="<?php echo ( $this->cust_product_settings['cust_product_sticker_animation_delay']); ?>" placeholder='Delay'/>
			<p class="description"><?php _e( 'Specify animation delay time in seconds (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
		<?php
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<select disabled>
					<option>None</option>
				</select>
				<p class="description"><?php _e( 'Specify animation type', 'woo-stickers-by-webline' );?></p>

				<div class="ribbon">
					<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
						<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
						<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
						<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
						<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
						<defs>
						<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						</defs>
					</svg>
				</div>

				<div class="learn-more">
					<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			</div>
			<?php
		}
		
	}
	
	public function enable_cust_product_schedule_sticker() {
		if(get_option('wosbw_premium_access_allowed') == 1){

		?>
		<select id='enable_cust_product_schedule_sticker'
				name="<?php echo $this->cust_product_settings_key; ?>[enable_cust_product_schedule_sticker]">
			<option value='yes'
				<?php selected( !empty($this->cust_product_settings['enable_cust_product_schedule_sticker']) ? $this->cust_product_settings['enable_cust_product_schedule_sticker'] : 'no', 'yes', true ); ?>>
				<?php _e( 'Yes', 'woo-stickers-by-webline' ); ?>
			</option>
			<option value='no'
				<?php selected( !empty($this->cust_product_settings['enable_cust_product_schedule_sticker']) ? $this->cust_product_settings['enable_cust_product_schedule_sticker'] : 'no', 'no', true ); ?>>
				<?php _e( 'No', 'woo-stickers-by-webline' ); ?>
			</option>
		</select>

		<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as CUSTOM in wooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php
		}else{
			?>
			<div class="wosbw-pro-ribbon-banner">
				<select disabled>
					<option>No</option>
				</select>
				<p class="description"><?php _e( 'Control Scheduled sticker display for products which are marked as CUSTOM in wooCommerce.', 'woo-stickers-by-webline' );?></p>

				<div class="ribbon">
					<svg width="167" height="167" viewBox="0 0 167 167" fill="none">
						<path d="M19 167L0 148L19 126L19 167Z" fill="url(#paint0_linear_1764_367)"/>
						<path d="M167 19L148 0L126 19H167Z" fill="url(#paint1_linear_1764_367)"/>
						<path d="M0 64.25V148.21L148.2 0H64.24C58.91 0 53.81 2.12 50.04 5.88L5.88 50.04C2.11 53.81 0 58.91 0 64.24V64.25Z" fill="#FDAB00"/>
						<path d="M30.7851 69.4813L37.207 63.0594C37.6582 62.6082 37.9528 62.111 38.0909 61.5678C38.229 61.0062 38.206 60.44 38.0219 59.8691C37.8377 59.2983 37.5017 58.7689 37.0137 58.2809C36.5165 57.7837 35.9825 57.443 35.4116 57.2589C34.8408 57.0563 34.2792 57.0287 33.7267 57.176C33.1743 57.3049 32.6679 57.5996 32.2076 58.0599L25.7856 64.4819L22.1396 60.8359L28.4649 54.5106C29.717 53.2584 31.0751 52.4159 32.539 51.9832C34.003 51.5321 35.4623 51.509 36.917 51.9142C38.3717 52.3193 39.7114 53.1341 40.9359 54.3587C42.1605 55.5832 42.9753 56.9228 43.3804 58.3776C43.7763 59.8231 43.7487 61.2778 43.2975 62.7417C42.8556 64.1965 42.0086 65.5499 40.7564 66.8021L34.4311 73.1274L30.7851 69.4813ZM20.4133 62.5622L24.1974 58.7781L44.2781 78.8588L40.494 82.6429L20.4133 62.5622ZM47.6203 52.0661L54.6085 45.0779C54.9952 44.6912 55.2392 44.2538 55.3405 43.7659C55.4417 43.2779 55.3911 42.7761 55.1885 42.2605C54.9952 41.7357 54.6683 41.2339 54.208 40.7551C53.7384 40.2856 53.2412 39.9541 52.7164 39.7608C52.2008 39.5582 51.699 39.5076 51.2111 39.6088C50.7231 39.7101 50.2811 39.9587 49.8852 40.3546L42.897 47.3428L39.251 43.6968L46.5431 36.4048C47.6479 35.2999 48.8679 34.5772 50.2029 34.2365C51.5379 33.8958 52.8914 33.9511 54.2632 34.4022C55.6259 34.8442 56.9057 35.6636 58.1026 36.8605C59.2995 38.0574 60.1236 39.3418 60.5747 40.7137C61.0166 42.0764 61.0627 43.4298 60.7128 44.774C60.3722 46.1091 59.654 47.3244 58.5583 48.4201L51.2663 55.7121L47.6203 52.0661ZM37.7457 45.2022L41.5298 41.418L61.6243 61.5126L57.8402 65.2967L37.7457 45.2022ZM54.8709 50.8645L58.2821 46.1827L73.0319 50.105L68.4468 54.6901L54.8709 50.8645ZM82.8651 40.6861C81.4104 42.1408 79.8222 43.1214 78.1005 43.6278C76.3787 44.1157 74.657 44.1065 72.9353 43.6001C71.2043 43.0845 69.6023 42.0902 68.1291 40.617L62.4391 34.927C60.966 33.4539 59.9762 31.8565 59.4699 30.1347C58.9543 28.4038 58.945 26.682 59.4422 24.9695C59.9394 23.2386 60.9154 21.6458 62.3701 20.191C63.8248 18.7363 65.413 17.765 67.1348 17.277C68.8565 16.7706 70.5828 16.7752 72.3138 17.2908C74.0355 17.7972 75.633 18.7869 77.1061 20.2601L82.7961 25.9501C84.2692 27.4232 85.2636 29.0253 85.7792 30.7562C86.2856 32.4779 86.2902 34.2043 85.793 35.9352C85.2958 37.6477 84.3199 39.2314 82.8651 40.6861ZM79.081 36.902C79.7623 36.2206 80.2181 35.4795 80.4483 34.6784C80.6692 33.8682 80.6508 33.058 80.393 32.2478C80.1444 31.4283 79.6703 30.6687 78.9705 29.969L73.0872 24.0856C72.3874 23.3859 71.6279 22.9117 70.8084 22.6631C69.9982 22.4053 69.1926 22.3915 68.3916 22.6217C67.5813 22.8427 66.8355 23.2938 66.1542 23.9752C65.4729 24.6565 65.0217 25.4023 64.8008 26.2125C64.5706 27.0135 64.5798 27.8237 64.8284 28.6432C65.0862 29.4534 65.565 30.2084 66.2647 30.9081L72.1481 36.7915C72.8478 37.4912 73.6028 37.97 74.413 38.2278C75.2324 38.4764 76.0473 38.4902 76.8575 38.2692C77.6585 38.039 78.3997 37.5833 79.081 36.902Z" fill="white"/>
						<defs>
						<linearGradient id="paint0_linear_1764_367" x1="12.5" y1="150" x2="4.5" y2="137.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						<linearGradient id="paint1_linear_1764_367" x1="167" y1="19" x2="137" y2="10.5" gradientUnits="userSpaceOnUse">
						<stop stop-color="#FDAB00"/>
						<stop offset="1" stop-color="#CD8F0D"/>
						</linearGradient>
						</defs>
					</svg>
				</div>

				<div class="learn-more">
					<a href="<?php home_url() ?>/wp-admin/admin.php?page=upgrade-to-premium-wosbw">Upgrade to Premium</a>
				</div>
			</div>
			<?php
		}
	}
	
	public function cust_product_schedule_sticker() {
		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = date($format, $current_timestamp);
		?>
			<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_start_sticker_date_time" name="<?php echo $this->cust_product_settings_key;?>[cust_product_schedule_start_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->cust_product_settings['cust_product_schedule_start_sticker_date_time'] )) ? 
				($this->cust_product_settings['cust_product_schedule_start_sticker_date_time'] ) : $formatted_date_time ); ?>"
				/>
			<p class="description"><?php _e( 'Specify start date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>

			<br>

			<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_end_sticker_date_time" name="<?php echo $this->cust_product_settings_key;?>[cust_product_schedule_end_sticker_date_time]" 
				value="<?php echo (esc_attr( !empty($this->cust_product_settings['cust_product_schedule_end_sticker_date_time'] )) ? 
				($this->cust_product_settings['cust_product_schedule_end_sticker_date_time'] ) : $formatted_date_time ); ?>"
				min="<?php echo $formatted_date_time; ?>" />
			<p class="description"><?php _e( 'Specify end date and time to schedule the sticker', 'woo-stickers-by-webline' );?></p>
	
			<br>
	
			<div class="woo_opt cust_product_schedule_sticker_option">
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if($this->cust_product_settings['cust_product_schedule_sticker_option'] == 'image_schedule' || $this->cust_product_settings['cust_product_schedule_sticker_option'] == '') { echo "checked"; } ?> <?php checked($this->cust_product_settings['cust_product_schedule_sticker_option'] ); ?>/>
				<label for="image_schedule"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
				<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if($this->cust_product_settings['cust_product_schedule_sticker_option'] == 'text_schedule') { echo "checked"; } ?> <?php checked( $this->cust_product_settings['cust_product_schedule_sticker_option'] ); ?>/>
				<label for="text_schedule"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
				<input type="hidden" class="wli_product_schedule_option" id="cust_product_schedule_sticker_option" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_schedule_sticker_option]" value="<?php if($this->cust_product_settings['cust_product_schedule_sticker_option'] == '') { echo 'image_schedule'; } else { echo esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ); } ?>"/>
				<p class="description"><?php _e( 'Select any of option for the schedule sticker.', 'woo-stickers-by-webline' );?></p>
			</div>
	
			<?php
				if($this->cust_product_settings['cust_product_schedule_sticker_option'] == "text_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_opttext_sch { display: table-row; }
					</style>';
				}
				if($this->cust_product_settings['cust_product_schedule_sticker_option'] == "image_schedule") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}
				if($this->cust_product_settings['cust_product_schedule_sticker_option'] == "") {
					echo '<style type="text/css">
						.custom_option.custom_optimage_sch { display: table-row; }
					</style>';
				}		
	}
	
	public function cust_product_schedule_custom_sticker() {
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		if ($this->cust_product_settings ['cust_product_schedule_custom_sticker'] == '')
		{				
			$image_url = "";
			echo '<img class="cust_product_schedule_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url = $this->cust_product_settings ['cust_product_schedule_custom_sticker'];
			echo '<img class="cust_product_schedule_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
		}
		echo '<br/>
			<input type="hidden" name="'.$this->cust_product_settings_key .'[cust_product_schedule_custom_sticker]" id="cust_product_schedule_custom_sticker" value="'. esc_url( $image_url ) .'" />
			<button class="upload_img_btn_sch button" id="upload_img_btn_sch">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
			<button class="remove_img_btn_sch button" id="remove_img_btn_sch">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
			'.$this->custom_sticker_script_sch('cust_product_schedule_custom_sticker'); ?>
	
		<p class="description"><?php _e( 'Add your own custom schedule sticker for new product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="cust_product_schedule_sticker_image_width" placeholder="width" name="<?php echo $this->cust_product_settings_key;?>[cust_product_schedule_sticker_image_width]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_sticker_image_width'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_schedule_sticker_image_width'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="cust_product_schedule_sticker_image_height" placeholder="height" name="<?php echo $this->cust_product_settings_key;?>[cust_product_schedule_sticker_image_height]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_sticker_image_height'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_schedule_sticker_image_height'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your schedule sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_custom_text() {
		?>
		<input type="text" id="cust_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo $this->cust_product_settings_key;?>[cust_product_schedule_custom_text]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function enable_cust_schedule_product_style() {
		?>
		<select id='enable_cust_schedule_product_style'
			name="<?php echo $this->cust_product_settings_key; ?>[enable_cust_schedule_product_style]">
			<option value='ribbon'
				<?php selected( $this->cust_product_settings['enable_cust_schedule_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->cust_product_settings['enable_cust_schedule_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function cust_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="cust_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->cust_product_settings_key;?>[cust_product_schedule_custom_text_fontcolor]" value="<?php echo ($this->cust_product_settings['cust_product_schedule_custom_text_fontcolor']) ? esc_attr( $this->cust_product_settings['cust_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="cust_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->cust_product_settings_key;?>[cust_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="cust_product_schedule_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_schedule_text_padding_top]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_top'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_schedule_text_padding_top'] . '"'; } ?> />
		<input type="number" id="cust_product_schedule_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_schedule_text_padding_right]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_right'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_schedule_text_padding_right'] . '"'; } ?> />
		<input type="number" id="cust_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_schedule_text_padding_bottom]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_bottom'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_schedule_text_padding_bottom'] . '"'; } ?> />		
		<input type="number" id="cust_product_schedule_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_schedule_text_padding_left]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_left'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_schedule_text_padding_left'] . '"'; } ?> />
	
		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
	
		<?php
	}

	/**
	 * Custom Product Sticker Settings :: Sticker Options
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_option() {
		?>
		<div class="woo_opt cust_product_option">
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($this->cust_product_settings['cust_product_option'] == 'image' || $this->cust_product_settings['cust_product_option'] == '') { echo "checked"; } ?> <?php checked($this->cust_product_settings['cust_product_option'] ); ?>/>
			<label for="image"><?php _e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($this->cust_product_settings['cust_product_option'] == 'text') { echo "checked"; } ?> <?php checked( $this->cust_product_settings['cust_product_option'] ); ?>/>
			<label for="text"><?php _e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="cust_product_option" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_option]" value="<?php if($this->cust_product_settings['cust_product_option'] == '') { echo 'image'; } else { echo esc_attr( $this->cust_product_settings['cust_product_option'] ); } ?>"/>
			<p class="description"><?php _e( 'Select any of option for the custom sticker.', 'woo-stickers-by-webline' );?></p>
		</div>
		<?php
		if($this->cust_product_settings['cust_product_option'] == "text") {
			echo '<style type="text/css">
				.custom_option.custom_opttext { display: table-row; }
			</style>';
		}
		if($this->cust_product_settings['cust_product_option'] == "image") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
		if($this->cust_product_settings['cust_product_option'] == "") {
			echo '<style type="text/css">
				.custom_option.custom_optimage { display: table-row; }
			</style>';
		}
	}

	/**
	 * New Product Settings :: Image Width CSS for Custom Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_sticker_image_width() {
		
		?>
		<input type="number" class="small-text" id="cust_product_sticker_image_width" name="<?php echo $this->cust_product_settings_key;?>[cust_product_sticker_image_width]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_image_width'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_sticker_image_width'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your sticker image width (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Settings :: Image Height CSS for Custom Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_sticker_image_height() {
		
		?>
		<input type="number" class="small-text" id="cust_product_sticker_image_height" name="<?php echo $this->cust_product_settings_key;?>[cust_product_sticker_image_height]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_image_height'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_sticker_image_height'] . '"'; } ?> />
		<p class="description"><?php _e( 'Specify your sticker image height (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Custom Product Sticker Settings :: Custom text for all products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_custom_text() {
		?>
		<input type="text" id="cust_product_custom_text" name="<?php echo $this->cust_product_settings_key;?>[cust_product_custom_text]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_custom_text'] ); ?>"/>
		<p class="description"><?php _e( 'Specify the text to show as custom sticker on products, Leave it blank if you use WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Custom Product Sticker Settings :: Custom sticker type for all products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_cust_product_style() {
		?>
		<select id='enable_cust_product_style'
			name="<?php echo $this->cust_product_settings_key; ?>[enable_cust_product_style]">
			<option value='ribbon'
				<?php selected( $this->cust_product_settings['enable_cust_product_style'], 'ribbon',true );?>><?php _e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( $this->cust_product_settings['enable_cust_product_style'], 'round',true );?>><?php _e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php _e( 'Select custom sticker layout to show on products.', 'woo-stickers-by-webline' );?></p>
	<?php
	}

	/**
	 * Custom Product Sticker Settings :: Custom text font color for all products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_custom_text_fontcolor() {
		?>
		<input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo $this->cust_product_settings_key;?>[cust_product_custom_text_fontcolor]" value="<?php echo ($this->cust_product_settings['cust_product_custom_text_fontcolor']) ? esc_attr( $this->cust_product_settings['cust_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php _e( 'Specify font color for text to show as custom sticker on products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Custom Product Sticker Settings :: Custom text font color for all products 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_custom_text_backcolor() {
		?>
		<input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo $this->cust_product_settings_key;?>[cust_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php _e( 'Specify background color for text to show as custom sticker on products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * New Product Sticker Settings :: Custom text padding for Custom Stickers 
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */


	 public function cust_product_custom_text_padding() {
		?>
		<input type="number" id="cust_product_text_padding_top" class="small-text" placeholder="Top" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_text_padding_top]" <?php if ( isset( $this->cust_product_settings['cust_product_text_padding_top'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_text_padding_top'] . '"'; } ?> />
		<input type="number" id="cust_product_text_padding_right" class="small-text" placeholder="Right" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_text_padding_right]" <?php if ( isset( $this->cust_product_settings['cust_product_text_padding_right'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_text_padding_right'] . '"'; } ?> />
		<input type="number" id="cust_product_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_text_padding_bottom]" <?php if ( isset( $this->cust_product_settings['cust_product_text_padding_bottom'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_text_padding_bottom'] . '"'; } ?> />
		<input type="number" id="cust_product_text_padding_left" class="small-text" placeholder="Left" name="<?php echo $this->cust_product_settings_key; ?>[cust_product_text_padding_left]" <?php if ( isset( $this->cust_product_settings['cust_product_text_padding_left'] ) ) { echo 'value="' . $this->cust_product_settings['cust_product_text_padding_left'] . '"'; } ?> />

		<p class="description"><?php _e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>

		<?php
	}

	/**
	 * Custom Product Sticker Settings :: Custom Stickers for all Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function cust_product_custom_sticker() {

		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		//print_r(CV_DEFAULT_IMAGE); die;	
		if ($this->cust_product_settings ['cust_product_custom_sticker'] == '')
		{
			$image_url = "";
			echo '<img class="cust_product_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url = $this->cust_product_settings ['cust_product_custom_sticker'];
			echo '<img class="cust_product_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
		}
		echo '		<br/>
					<input type="hidden" name="'.$this->cust_product_settings_key .'[cust_product_custom_sticker]" id="cust_product_custom_sticker" value="'. esc_url( $image_url ) .'" />
					<button class="upload_img_btn button">'. __( 'Upload Image', 'woo-stickers-by-webline' ) .'</button>
					<button class="remove_img_btn button">'. __( 'Remove Image', 'woo-stickers-by-webline' ) .'</button>								
				'.$this->custom_sticker_script('cust_product_custom_sticker'); ?>			
		<p class="description"><?php _e( 'Add your own custom product image instead of WooStickers default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menus() {

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

		// add_options_page ( __( 'WLI Woocommerce Stickers', 'woo-stickers-by-webline' ), __( 'WOO Stickers', 'woo-stickers-by-webline' ), 'manage_options', $this->plugin_options_key, array (
		// 		&$this,
		// 		'plugin_options_page' 
		// ) );

		add_menu_page(
			__( 'WLI Woocommerce Stickers', 'woo-stickers-by-webline' ), 
			__( 'WOO Stickers', 'woo-stickers-by-webline' ), 
			'manage_options', 
			$this->plugin_options_key, 
			array( &$this, 'plugin_options_page' ),
			'dashicons-format-image', 
			56
		);

		add_submenu_page(
			$this->plugin_options_key,
			__( 'Free Upgrade to PRO', 'woo-stickers-by-webline' ), // Page title
			__( 'Free Upgrade to PRO', 'woo-stickers-by-webline' ), // Menu title
			'manage_options', // Capability required to access the menu item
			'upgrade-to-premium-wosbw', // Submenu slug
			array( &$this, 'wosbw_upgrade_to_premium' ) // Callback function to render the page
		);

		
	}

	public function plugin_options_page(){
		$tab = isset ( $_GET ['tab'] ) ? $_GET ['tab'] : $this->general_settings_key;
		?>
		<div class="wrap-wosbw">
			<div class="inner-wosbw">
				<div class="left-box-wosbw">
				<h2><?php _e( 'WOO Stickers by Webline - Configuration Settings', 'woo-stickers-by-webline' );?></h2>
				<?php $this->plugin_options_tabs(); ?>
				<form class="wli-form-general" method="post" action="options.php">
					<?php wp_nonce_field( 'update-options' ); ?>
					<?php settings_fields( $tab ); ?>
					<?php do_settings_sections( $tab ); ?>
					<?php submit_button(); ?>
				</form>
				</div>
				<div class="right-box-wosbw">
					<?php $this->cta_section_callback(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one.
	 * Provides the heading for the
	 * plugin_options_page method.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function plugin_options_tabs() {
		$current_tab = isset ( $_GET ['tab'] ) ? $_GET ['tab'] : $this->general_settings_key;
		//screen_icon ();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}
		echo '</h2>';
	}
	
	/**
	 *   custom_sticker_script() is used to upload using wordpress upload.
	 *
	 *  @since    			1.0.0
	 *
	 *  @return             script
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 *
	 */
	public function custom_sticker_script($obj_url) {
		return '<script type="text/javascript">
	    jQuery(document).ready(function() {
			var wordpress_ver = "'.get_bloginfo("version").'", upload_button;
			jQuery(".upload_img_btn").click(function(event) {
				upload_button = jQuery(this);
				var frame;
				jQuery(this).parent().children("img").attr("src","").show();					
				if (wordpress_ver >= "3.5") {
					event.preventDefault();
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media();
					frame.on( "select", function() {					
						// Grab the selected attachment.
						var attachment = frame.state().get("selection").first();
						frame.close();
						if (upload_button.parent().prev().children().hasClass("cat_list")) {
							upload_button.parent().prev().children().val(attachment.attributes.url);
							upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
						}
						else
						{
							jQuery("#'.$obj_url.'").val(attachment.attributes.url);
							jQuery(".'.$obj_url.'").attr("src",attachment.attributes.url);
						}
					});
					frame.open();
				}
				else {
					tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
					return false;
				}
			});
	
			jQuery(".remove_img_btn").click(function() {
				jQuery("#'.$obj_url.'").val("");
				if(jQuery(this).parent().children("img").attr("src")!="undefined")	
				{ 
					jQuery(this).parent().children("img").attr("src","").hide();
					jQuery(this).parent().siblings(".title").children("img").attr("src"," ");
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val(""); 
				}	
				else
				{
					jQuery(this).parent().children("img").attr("src","").hide();
				}						
				return false;
			});
	
			if (wordpress_ver < "3.5") {
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					if (upload_button.parent().prev().children().hasClass("cat_list")) {
						upload_button.parent().prev().children().val(imgurl);
						upload_button.parent().prev().prev().children().attr("src", imgurl);
					}
					else
					{
						jQuery("#'.$obj_url.'").val(imgurl);
						jQuery(".'.$obj_url.'").attr("src",imgurl);
					}
					tb_remove();
				}
			}
	
			jQuery(".editinline").click(function(){
			    var tax_id = jQuery(this).parents("tr").attr("id").substr(4);
			    var thumb = jQuery("#tag-"+tax_id+" .thumb img").attr("src");
				if (thumb != "") {
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val(thumb);
				} else {
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val("");
				}
				jQuery(".inline-edit-col .title img").attr("src",thumb);
			    return true;
			});
	    });
	</script>';
	}

	public function custom_sticker_script_sch($obj_url) {
		return '<script type="text/javascript">
	    jQuery(document).ready(function() {
			var wordpress_ver = "'.get_bloginfo("version").'", upload_button;
			jQuery(".upload_img_btn_sch").click(function(event) {
				upload_button = jQuery(this);
				var frame;
				jQuery(this).parent().children("img").attr("src","").show();					
				if (wordpress_ver >= "3.5") {
					event.preventDefault();
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media();
					frame.on( "select", function() {					
						// Grab the selected attachment.
						var attachment = frame.state().get("selection").first();
						frame.close();
						if (upload_button.parent().prev().children().hasClass("cat_list")) {
							upload_button.parent().prev().children().val(attachment.attributes.url);
							upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
						}
						else
						{
							jQuery("#'.$obj_url.'").val(attachment.attributes.url);
							jQuery(".'.$obj_url.'").attr("src",attachment.attributes.url);
						}
					});
					frame.open();
				}
				else {
					tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
					return false;
				}
			});
	
			jQuery(".remove_img_btn_sch").click(function() {
				jQuery("#'.$obj_url.'").val("");
				if(jQuery(this).parent().children("img").attr("src")!="undefined")	
				{ 
					jQuery(this).parent().children("img").attr("src","").hide();
					jQuery(this).parent().siblings(".title").children("img").attr("src"," ");
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val(""); 
				}	
				else
				{
					jQuery(this).parent().children("img").attr("src","").hide();
				}						
				return false;
			});

			
	
			if (wordpress_ver < "3.5") {
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					if (upload_button.parent().prev().children().hasClass("cat_list")) {
						upload_button.parent().prev().children().val(imgurl);
						upload_button.parent().prev().prev().children().attr("src", imgurl);
					}
					else
					{
						jQuery("#'.$obj_url.'").val(imgurl);
						jQuery(".'.$obj_url.'").attr("src",imgurl);
					}
					tb_remove();
				}
			}
	
			jQuery(".editinline").click(function(){
			    var tax_id = jQuery(this).parents("tr").attr("id").substr(4);
			    var thumb = jQuery("#tag-"+tax_id+" .thumb img").attr("src");
				if (thumb != "") {
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val(thumb);
				} else {
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val("");
				}
				jQuery(".inline-edit-col .title img").attr("src",thumb);
			    return true;
			});
	    });
	</script>';
	}


	/**
	 * CTA section callback function.
	 *
	 * @since    1.0.0
	 */
	public function cta_section_callback() {
		?>
		<div class="wosbw-plugin-cta">
			<h2 class="wosbw-heading">Thank you for downloading our plugin - Woo Stickers by Webline.</h2>
			<h2 class="wosbw-heading">We're here to help !</h2>
			<p>Our plugin comes with free, basic support for all users. We also provide plugin customization in case you want to customize our plugin to suit your needs.</p>
			<a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Woo%20Stickers&utm_campaign=Free%20Support" target="_blank" class="button">Need help?</a>
			<a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Woo%20Stickers&utm_campaign=Plugin%20Customization" target="_blank" class="button button-primary">Want to customize plugin?</a>
		</div>
		<?php
		$all_plugins = get_plugins();
		if (!(isset($all_plugins['xml-sitemap-for-google/xml-sitemap-for-google.php']))) {
			?>
				<div class="wosbw-plugin-cta show-other-plugin" id="xml-plugin-banner">
					<h2 class="wosbw-heading">Want to Rank Higher on Google?</h2>
					<h3 class="wosbw-heading">Install <span>XML Sitemap for Google</span> Plugin</h3>
					<hr>
					<p>Our plugin comes with free, basic support for all users.</p>
					<ul class="custom-bullet">
						<li>Easy Setup and Effortless Integration</li>	
						<li>Automatic Updates</li>	
						<li>Improve Search Rankings</li>	
						<li>SEO Best Practices</li>
						<li>Optimized for Performance</li>
					</ul>						
					<br>
					<button id="open-install-wosbw" class="button-install">Install Plugin</button>
				</div>
			<?php 
		}
	}

	/**
	 * Display footer text that graciously asks them to rate us.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function admin_footer( $text ) {
			
		$url  = 'https://wordpress.org/support/plugin/woo-stickers-by-webline/reviews/?filter=5#new-post';
		$wpdev_url  = 'https://www.weblineindia.com/wordpress-development.html?utm_source=WP-Plugin&utm_medium=Woo%20Stickers&utm_campaign=Footer%20CTA';
		$text = sprintf(
			wp_kses(
				'Please rate our plugin %1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> to help us spread the word. Thank you from the <a href="%4$s" target="_blank" rel="noopener noreferrer">WordPress development</a> team at WeblineIndia.',
				array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
				)
			),
			'<strong>"WOO Stickers by Webline"</strong>',
			$url,
			$url,
			$wpdev_url
		);

		return $text;
	}

	function wosbw_upgrade_to_premium()
	{
		?>
		<div class="wrap-wosbw">
			<div class="inner-wosbw" id="inner-wosbw">
				<div class="left-box-wosbw wosbw-plans">
					<?php
						if(get_option('wosbw_premium_access_allowed') == 1){
						?>
							<h2 id="wosbw-heading" style="color: #4AB01A;"><?php esc_html_e('Pro Access Enabled', 'woo-stickers-by-webline'); ?></h2>
							<?php
						}else{
						?>
							<h2 id="wosbw-heading"><?php esc_html_e('Upgrade to Pro Features', 'woo-stickers-by-webline'); ?></h2> 
						<?php
						}	
					?>
					<div class="content" id="content" style="<?php echo (get_option('wosbw_premium_access_allowed') == 1) ? 'border-top: 3px solid #4AB01A;' : 'border-top: 3px solid #FDB930;'; ?>">	
						<div class="content-inside1">
							<div class="wosbw-left-title">
								<p>
									<?php esc_html_e('Free Pro Features:', 'woo-stickers-by-webline'); ?>
								</p>
							</div>
							<div class="wosbw-right-des">
								<?php
								$wosbw_premium_access_allowed = get_option('wosbw_premium_access_allowed');
								$fill_color = $wosbw_premium_access_allowed ? "#4AB01A" : "#FDBC33";
								?>
								<ul>
									<li>
										<svg wosbwns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
											<g clip-path="url(#clip0_1642_43)">
												<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="<?php echo $fill_color; ?>"/>
											</g>
											<defs>
												<clipPath id="clip0_1642_43">
													<rect width="17" height="17" fill="white"/>
												</clipPath>
											</defs>
										</svg>
										<span class="feture-item">Rotate Sticker</span>
									</li>
									<li>
										<svg wosbwns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
											<g clip-path="url(#clip0_1642_43)">
												<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="<?php echo $fill_color; ?>"/>
											</g>
											<defs>
												<clipPath id="clip0_1642_43">
													<rect width="17" height="17" fill="white"/>
												</clipPath>
											</defs>
										</svg>
										<span>Sticker Animation</span>
									</li>
									<li>
										<svg wosbwns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
											<g clip-path="url(#clip0_1642_43)">
												<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="<?php echo $fill_color; ?>"/>
											</g>
											<defs>
												<clipPath id="clip0_1642_43">
													<rect width="17" height="17" fill="white"/>
												</clipPath>
											</defs>
										</svg>
										<span>Scheduled Sticker</span>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<form method="post" action="" id="wosbw-upgrade-to-premium" class="wosbw-plans-form">
						<div class="wosbw-pricing-cards" id="wosbw-pricing-cards">
							<div class="wosbw-pricing-card">
								<input type="radio" name="wosbw_upgrade_option" class="wosbw_upgrade_option" value="backlink" id="card1" checked <?php echo (get_option('wosbw_upgrade_option') === 'backlink' || empty(get_option('wosbw_upgrade_option'))) ? 'checked' : ''; ?>>
								<label for="card1" class="wosbw-plan-lable">FREE UPGRADE</label>
							</div>
							<div class="wosbw-pricing-card">
								<input type="radio" name="wosbw_upgrade_option" class="wosbw_upgrade_option" value="premium" id="card2" <?php checked(get_option('wosbw_upgrade_option'), 'premium'); ?>>
								<label for="card2" class="wosbw-plan-lable">PAID UPGRADE</label>
							</div>
						</div>
						<div class="free-table-data">    
							<div id="backlink" class="content" style="<?php echo (get_option('wosbw_premium_access_allowed') == 1) ? 'border-top: 3px solid #4AB01A;' : 'border-top: 3px solid #FDB930;'; ?>">
								<p class="wosbw-plans-p border-bottom" id="wosbw-plans-p">
									<?php esc_html_e('We will be happy to provide you access to the premium features of this plugin for FREE if you can mention us on any of the pages in your website.
											To mention us, you can use any of the below mentioned Anchor text and link out to the given URL.', 'woo-stickers-by-webline'); ?>
								</p>
								<div class="content-inside border-bottom" id="content-inside">
									<div class="wosbw-left-title">
										<p>
											<?php esc_html_e('Keyword List:', 'woo-stickers-by-webline'); ?>
										</p>
									</div>
									<div class="wosbw-right-des">
										<p>
											<?php esc_html_e('Select one of the options from below dropdown. Copy html code shown below and paste in the page where you want to place the backlink.', 'woo-stickers-by-webline'); ?>
										</p>
										<?php
											$keyword_options = array(
												"custom software development" => "https://www.weblineindia.com/",
												"offshore software development company" => "https://www.weblineindia.com/about-us.html",
												"ai software development" => "https://www.weblineindia.com/ai-development.html",
												"software development outsourcing company" => "https://www.weblineindia.com/about-us.html",
												"software development outsourcing" => "https://www.weblineindia.com/",
												"offshore software development" => "https://www.weblineindia.com/",
												"software development services" => "https://www.weblineindia.com/",
												"hire software developers" => "https://www.weblineindia.com/hire-dedicated-developers.html",
												"hire software programmers" => "https://www.weblineindia.com/hire-dedicated-developers.html"
											);

											$saved_keyword = get_option('wosbw_saved_keyword');											
											preg_match('/<a\s+href="([^"]+)">([^<]+)<\/a>/', $saved_keyword, $matches);
											if (isset($matches[2])) {
												$saved_keyword = $matches[2];
											}
											
										?>
										<select id="wosbw_saved_keyword" name="wosbw_saved_keyword" class="wosbw-select-item">
											<?php foreach ($keyword_options as $text => $value):
												if(isset($saved_keyword) && !empty($saved_keyword)){
													if($saved_keyword == $text){
														$selected = 'selected=selected';
													}else{
														$selected = '';
													}
												}
												?>
												<option value="<?php echo esc_attr($value. '|' . $text); ?>" <?php echo $selected; ?>>
													<?php echo esc_html($text); ?>
												</option>
											<?php endforeach; ?>
										</select>
										<div class="link wosbw-copy-link-box">
											<div name="wosbw-dynamic-link" id="wosbw-dynamic-link"></div>
											<?php
												$saved_keyword = get_option('wosbw_saved_keyword');
												if (preg_match('/>(.*?)</', $saved_keyword, $match)) {
													$value_between_tags = $match[1];
												}
											?>
											<input type="hidden" id="keyword_value" name="keyword_value" value="<?php echo esc_attr($value_between_tags); ?>">
											<div class="wosbw-copy-btn" id="wosbw-copy-button">
												<svg wosbwns="http://www.w3.org/2000/svg" width="19" height="20" viewBox="0 0 19 20" fill="none">
												<path d="M3.4375 19H11.5625C12.9066 19 14 18.058 14 16.9V7.1C14 5.94199 12.9066 5 11.5625 5H3.4375C2.09338 5 1 5.94199 1 7.1V16.9C1 18.058 2.09338 19 3.4375 19ZM2.625 7.1C2.625 6.71411 2.9892 6.4 3.4375 6.4H11.5625C12.0108 6.4 12.375 6.71411 12.375 7.1V16.9C12.375 17.2859 12.0108 17.6 11.5625 17.6H3.4375C2.9892 17.6 2.625 17.2859 2.625 16.9V7.1Z" fill="#8D8D8D"/>
												<path d="M18.2483 12.586V4.02769C18.2483 2.61189 17.0966 1.46021 15.6808 1.46021H7.12249C6.64986 1.46021 6.26666 1.84341 6.26666 2.31603C6.26666 2.78866 6.64986 3.17186 7.12249 3.17186H15.6808C16.153 3.17186 16.5366 3.5559 16.5366 4.02769V12.586C16.5366 13.0586 16.9198 13.4418 17.3924 13.4418C17.8651 13.4418 18.2483 13.0586 18.2483 12.586Z" fill="#8D8D8D"/>
												</svg>
											</div>
										</div>
									</div>
								</div>
								<div class="content-inside border-bottom" id="select-page-div">
									<div class="wosbw-left-title">
										<p>
											<?php esc_html_e('Select the page:', 'woo-stickers-by-webline'); ?>
										</p>
									</div>
									<div class="wosbw-right-des">
										<input style="width: auto;" type="text" id="select_posts_input_wosbw" name="select_posts_input_wosbw" placeholder='Begin typing post title to search' value="">
										<input type="hidden" id="selected_post_permalink_wosbw" name="selected_post_permalink_wosbw" value="<?php echo esc_attr(get_option('wosbw_selected_page')); ?>">
										<p class="description">
											<?php esc_html_e('Provide the names of the post/page on which you have mentioned us and get FREE access instantly.', 'woo-stickers-by-webline'); ?>
										</p>
									</div>       
								</div>
								<div class="content-inside " id="select-page-div">
									<div class="wosbw-left-title">
									</div>
									<div class="wosbw-right-des">
										<p class="text-val-bef">
											<?php esc_html_e('Once done, please press validate button. Once validated your premium features will be enabled.', 'woo-stickers-by-webline'); ?>
										</p>
										<div class="validate-btn" id="validate-btn">
											<div id='validate-button' class="submit" style="display: flex; margin: 0; padding:0;">
												<input type="submit" name="save_upgrade_option" class="button button-primary" value="Validate"
												title="Updates your changes on click">
												<div class="loader">
													<div class="spinner"></div>
												</div>
											</div>
											<div class="success-message"></div>
										</div>
										<p class="text-val-aft">
										<?php
											$contact_url = 'https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Woo%20Stickers&utm_campaign=Free%20Support';
											printf(
												esc_html__('If you need help with Free Premium upgrade please feel free to %s', 'xml-sitemap-for-google'),
												sprintf(
													'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
													esc_url($contact_url),
													esc_html__('contact us', 'xml-sitemap-for-google')
												)
											);
										?>
										</p>
									</div>       
								</div>
								<div id="pop-up-box-upgrade" class="pop-up-box text-center" style="display: none;">
									<button class="close-popup-wosbw">
										<svg viewPort="0 0 12 12" version="1.1"
											wosbwns="
										http://www.w3.org/2000/svg">
										<line x1="1" y1="11" 
												x2="11" y2="1" 
												stroke="black" 
												stroke-width="2"/>
										<line x1="1" y1="1" 
												x2="11" y2="11" 
												stroke="black" 
												stroke-width="2"/>
										</svg>
									</button>
									<table class="pop-up-table">
										<tr>
											<td style="text-align:center;"><h3 style="color: #4AB01A;"><?php esc_html_e('Pro Access Enabled', 'woo-stickers-by-webline'); ?></h3></td>                                                
										</tr>
										<tr>
											<td class="description">
												<?php esc_html_e('We are happy to provide you access to the premium features of this plugin for FREE if you mention us on any of the pages on your website.', 'woo-stickers-by-webline'); ?>
											</td>
										</tr>
										<tr class="features-row">
											<td class="features">
												<svg wosbwns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
													<g clip-path="url(#clip0_1642_43)">
														<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#4AB01A"></path>
													</g>
													<defs>
														<clipPath id="clip0_1642_43">
															<rect width="17" height="17" fill="white"></rect>
														</clipPath>
													</defs>
												</svg>
												<p>Rotate Sticker</p>
											</td>
											<td class="features">
												<svg wosbwns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
													<g clip-path="url(#clip0_1642_43)">
														<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#4AB01A"></path>
													</g>
													<defs>
														<clipPath id="clip0_1642_43">
															<rect width="17" height="17" fill="white"></rect>
														</clipPath>
													</defs>
												</svg>
												<p>Sticker Animation</p>
											</td>
											<td class="features">
												<svg wosbwns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
													<g clip-path="url(#clip0_1642_43)">
														<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#4AB01A"></path>
													</g>
													<defs>
														<clipPath id="clip0_1642_43">
															<rect width="17" height="17" fill="white"></rect>
														</clipPath>
													</defs>
												</svg>
												<p>Scheduled Sticker</p>
											</td>
										</tr>
										<tr class="unlock-row">
											<td>
												<a href="<?php home_url() ?>/wp-admin/admin.php?page=wli-stickers&tab=new_product_settings" class="unlock-featues">Get Started Now</a>
											</td>
										</tr>
									</table>
								</div>
							</div>    
							<div id="premium" class="content" style="display: none;">
									<p><a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Woo%20Stickers&utm_campaign=Free%20Support" target="_blank">Click here</a> to leave us a message to know more about the pricing and getting access to our premium features.</p>
							</div>
						</div>
					</form> 
				</div>         
				<div class="right-box-wosbw" id="right-box-wosbw">
					<?php 
						
						$this->cta_section_callback();
					?>
				</div>  
			</div>
		</div>
		<?php
	}
}