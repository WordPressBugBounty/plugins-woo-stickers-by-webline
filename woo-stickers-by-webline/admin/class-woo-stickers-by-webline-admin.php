<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.weblineindia.com
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
		add_action( 'current_screen', array( $this, 'conditionally_add_footer_filter' ) );
	}

	/**
	 * Hook this on: add_action( 'current_screen', [ $this, 'conditionally_add_footer_filter' ] );
	 *
	 * @param WP_Screen $screen
	 */
	public function conditionally_add_footer_filter( $screen ) {
		if ( ! ( $screen instanceof WP_Screen ) ) {
			return;
		}

		// Adjust the screen ID to match your page. Common values:
		// 'toplevel_page_wli-stickers', 'settings_page_wli-stickers', etc.
		if ( 'toplevel_page_wli-stickers' === (string) $screen->id ) {
			add_filter( 'admin_footer_text', array( $this, 'admin_footer' ), 1, 1 );
		}
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

		wp_nonce_field( 'wli_stickers_save', 'wli_stickers_nonce' );


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
			<a class="nav-tab nav-tab-active" href="#wsbw_new_products"><?php esc_html_e( "New Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_products_sale"><?php esc_html_e( "Products On Sale", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_soldout_products"><?php esc_html_e( "Soldout Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_cust_products"><?php esc_html_e( "Custom Product Sticker", 'woo-stickers-by-webline' );?></a>
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
			$formatted_date_time = gmdate($format, $current_timestamp);
			
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
				'description'       => __( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'np_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_rotate',
				'value'             => get_post_meta( $post->ID, 'np_sticker_rotate', true ),
				'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
			) );

			?>

			<div class="form-field term-thumbnail-wrap">
				<div class="woo_opt np_product_option">
					<label for="np_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($np_product_option == 'image' || $np_product_option == '') { echo "checked"; } ?> <?php checked( $np_product_option, 'image'); ?>/>
					<label for="image" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($np_product_option == 'text') { echo "checked"; } ?> <?php checked( $np_product_option, 'text'); ?>/>
					<label for="text" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="np_product_option" class="wli_product_option" name="np_product_option" value="<?php if($np_product_option == '') { echo "image"; } else { echo esc_attr( $np_product_option ); } ?>"/>
				</div>
			</div>

		    <?php
			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'np_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'np_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
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
				<label for="np_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_product_custom_text_fontcolor" class="wli_color_picker" name="np_product_custom_text_fontcolor" value="<?php echo ($np_product_custom_text_fontcolor) ? esc_attr( $np_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext"<?php if($np_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_product_custom_text_backcolor" class="wli_color_picker" name="np_product_custom_text_backcolor" value="<?php echo esc_attr( $np_product_custom_text_backcolor ); ?>"/>
			</p>

			<p class="form-field custom_option custom_opttext" <?php if($np_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="np_product_custom_text_padding"><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="np_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="np_product_custom_text_padding_top" value="<?php echo esc_attr( $np_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="np_product_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="np_product_custom_text_padding_right" value="<?php echo esc_attr( $np_product_custom_text_padding_right ); ?>"/>
					<input type="number" id="np_product_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="np_product_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="np_product_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="np_product_custom_text_padding_left" value="<?php echo esc_attr( $np_product_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($np_product_option == 'image' || $np_product_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $np_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="np_sticker_custom_id" class="wsbw_upload_img_id" name="np_sticker_custom_id" value="<?php echo absint( $np_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>

			<hr>
			<?php

			woocommerce_wp_select( array(
				'id'          => 'np_sticker_animation_type',
				'value'       => get_post_meta( $post->ID, 'np_sticker_animation_type', true ),
				'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
				'description' => __( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'desc_tip'    => true,
				'options'     => array(
					'none'    => __( 'None', 'woo-stickers-by-webline' ),
					'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
					'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
					'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
					'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
					'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
				)
			) );

			?>
			<div id="zoominout-options-np-product" style="display: none;">
				<?php
					woocommerce_wp_text_input( array(
						'id'                => 'np_sticker_animation_scale',
						'value'             => get_post_meta( $post->ID, 'np_sticker_animation_scale', true ),
						'label'             => __( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Direction', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Select the animation direction.', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'np_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'np_sticker_animation_delay', true ),
				'label'             => __( 'Sticker Animation Delay', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );
			
			?>

			<hr>
			<?php
			
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

			?>

			<div class="form-field term-thumbnail-wrap">

				<label for="np_product_schedule_start_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time Start', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_start_sticker_date_time" name="np_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($np_product_schedule_start_sticker_date_time) ? $np_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				 />
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the start date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<label for="np_product_schedule_end_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_end_sticker_date_time" name="np_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($np_product_schedule_end_sticker_date_time) ? $np_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>" 
				/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the end date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<div class="woo_opt np_product_schedule_option">
					<label for="np_product_schedule_option"><?php esc_html_e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-p-schedule" id="image_schedule" value="image_schedule" <?php if($np_product_schedule_option == 'image_schedule' || $np_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $np_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-p-schedule" id="text_schedule" value="text_schedule" <?php if($np_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $np_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="np_product_schedule_option" class="wli_schedule_product_option_product" name="np_product_schedule_option" value="<?php if($np_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $np_product_schedule_option ); } ?>"/>
				</div>
			</div>

			<div class="custom_option custom_optimage_sch">
				<?php

					woocommerce_wp_text_input( array(
						'id'                => 'np_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'np_schedule_sticker_image_width', true ),
						'label'             => __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );


					woocommerce_wp_text_input( array(
						'id'                => 'np_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'np_schedule_sticker_image_height', true ),
						'label'             => __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );

				?>
			</div>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($np_product_schedule_option == 'image_schedule' || $np_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
				<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $np_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="np_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="np_schedule_sticker_custom_id" value="<?php echo absint( $np_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				

			</div>

			<?php

				woocommerce_wp_text_input( array(
					'id'                => 'np_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_np_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );

				woocommerce_wp_select( array(
					'id'          => 'np_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_np_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );

			?>

			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_np" <?php if($np_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="np_schedule_product_custom_text_fontcolor" value="<?php echo ($np_schedule_product_custom_text_fontcolor) ? esc_attr( $np_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_np"<?php if($np_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="np_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="np_schedule_product_custom_text_backcolor" class="wli_color_picker" name="np_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $np_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($np_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="np_schedule_product_custom_text_padding"><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="np_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="np_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $np_schedule_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="np_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="np_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_right ); ?>"/>
					<input type="number" id="np_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="np_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="np_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="np_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
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
				'description'       => __( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_rotate',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_rotate', true ),
				'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
			) );

			?>
			<div class="form-field term-thumbnail-wrap">
				<div class="woo_opt pos_product_option">
					<label><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="image1" value="image" <?php if($pos_product_option == 'image' || $pos_product_option == '') { echo "checked"; } ?> <?php checked( $pos_product_option, 'image'); ?>/>
					<label for="image1" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="text1" value="text" <?php if($pos_product_option == 'text') { echo "checked"; } ?> <?php checked( $pos_product_option, 'text'); ?>/>
					<label for="text1" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="pos_product_option" class="wli_product_option" name="pos_product_option" value="<?php if($pos_product_option == '') { echo "image"; } else { echo esc_attr( $pos_product_option ); } ?>"/>
				</div>
			</div>
			
		    <?php
			
			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
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
				<label for="pos_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_product_custom_text_fontcolor" class="wli_color_picker" name="pos_product_custom_text_fontcolor" value="<?php echo ($pos_product_custom_text_fontcolor) ? esc_attr( $pos_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext" <?php if($pos_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_product_custom_text_backcolor" class="wli_color_picker" name="pos_product_custom_text_backcolor" value="<?php echo esc_attr( $pos_product_custom_text_backcolor ); ?>"/>
			</p>

			<p class="form-field custom_option custom_opttext" <?php if($pos_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_product_custom_text_padding"><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
				<input type="number" id="pos_product_custom_text_padding_top" class="wsbw-small-text" placeholder="Top" name="pos_product_custom_text_padding_top" value="<?php echo esc_attr( $pos_product_custom_text_padding_top ); ?>"/>
				<input type="number" id="pos_product_custom_text_padding_right" class="wsbw-small-text" placeholder="Right" name="pos_product_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_custom_text_padding_right ); ?>"/>
				<input type="number" id="pos_product_custom_text_padding_bottom" class="wsbw-small-text"  placeholder="Bottom" name="pos_product_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_custom_text_padding_bottom ); ?>"/>
				<input type="number" id="pos_product_custom_text_padding_left" class="wsbw-small-text" placeholder="Left" name="pos_product_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_custom_text_padding_left ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($pos_product_option == 'image' || $pos_product_option == '') { echo 'style="display:block"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $pos_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="pos_sticker_custom_id" class="wsbw_upload_img_id" name="pos_sticker_custom_id" value="<?php echo absint( $pos_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>

			<hr>
			<?php
		
			woocommerce_wp_select( array(
				'id'          => 'pos_sticker_animation_type',
				'value'       => get_post_meta( $post->ID, 'pos_sticker_animation_type', true ),
				'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
				'description' => __( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'desc_tip'    => true,
				'options'     => array(
					'none'    => __( 'None', 'woo-stickers-by-webline' ),
					'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
					'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
					'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
					'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
					'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
				)
			) );

			?>
			<div id="zoominout-options-pos-product" style="display: none;">
				<?php
					woocommerce_wp_text_input( array(
						'id'                => 'pos_sticker_animation_scale',
						'value'             => get_post_meta( $post->ID, 'pos_sticker_animation_scale', true ),
						'label'             => __( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Direction', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Select the animation direction.', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'pos_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'pos_sticker_animation_delay', true ),
				'label'             => __( 'Sticker Animation Delay', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );

			?>

			<hr>

			<?php

			woocommerce_wp_select( array(
				'id'          => 'enable_pos_product_schedule_sticker',
				'value'         => get_post_meta($post->ID, '_enable_pos_product_schedule_sticker', true ) ?: 'no', 
				'wrapper_class' => 'Schedule Sticker Date/Time End',
				'label'       => __( 'Enable Scheduled Sticker:', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'options'     => array(
					'yes'    => __( 'Yes', 'woo-stickers-by-webline' ),
					'no'    => __( 'No', 'woo-stickers-by-webline' )
				)
			) );

			?>

			<div class="form-field term-thumbnail-wrap">

				<label for="pos_product_schedule_start_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time Start', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_start_sticker_date_time" name="pos_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($pos_product_schedule_start_sticker_date_time) ? $pos_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				 />
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the start date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<label for="pos_product_schedule_end_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_end_sticker_date_time" name="pos_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($pos_product_schedule_end_sticker_date_time) ? $pos_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>" 
				/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the end date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>

				<br><br>

				<div class="woo_opt pos_product_schedule_option">
					<label for="pos_product_schedule_option"><?php esc_html_e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-p-schedule" id="image_schedule_pos" value="image_schedule" <?php if($pos_product_schedule_option == 'image_schedule' || $pos_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $pos_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-p-schedule" id="text_schedule_pos" value="text_schedule" <?php if($pos_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $pos_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="pos_product_schedule_option" class="wli_schedule_product_option_product" name="pos_product_schedule_option" value="<?php if($pos_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $pos_product_schedule_option ); } ?>"/>
				</div>
			</div>

			<div class="custom_option custom_optimage_sch">
				<?php

					woocommerce_wp_text_input( array(
						'id'                => 'pos_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'pos_schedule_sticker_image_width', true ),
						'label'             => __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );


					woocommerce_wp_text_input( array(
						'id'                => 'pos_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'pos_schedule_sticker_image_height', true ),
						'label'             => __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );

				?>
			</div>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($pos_product_schedule_option == 'image_schedule' || $pos_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
				<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $pos_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="pos_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="pos_schedule_sticker_custom_id" value="<?php echo absint( $pos_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_pos"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_pos"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				

			</div>

			<?php
				woocommerce_wp_text_input( array(
					'id'                => 'pos_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_pos_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );

				woocommerce_wp_select( array(
					'id'          => 'pos_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_pos_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );
			?>

			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_pos" <?php if($pos_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_fontcolor" value="<?php echo ($pos_schedule_product_custom_text_fontcolor) ? esc_attr( $pos_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_pos"<?php if($pos_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="pos_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="pos_schedule_product_custom_text_backcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $pos_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($pos_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="pos_schedule_product_custom_text_padding"><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="pos_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="pos_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $pos_schedule_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="pos_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="pos_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_right ); ?>"/>
					<input type="number" id="pos_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="pos_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="pos_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="pos_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
		</div>
		
		<div id="wsbw_soldout_products" class="wsbw_tab_content" style="display: none;">
			<?php 
			$sop_product_option = get_post_meta( $post->ID, '_sop_product_option', true ); 
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
				'description'       => __( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_rotate',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_rotate', true ),
				'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
			) );
			
			?>

			<div class="form-field term-thumbnail-wrap">
				<div class="woo_opt sop_product_option">
					<label for="sop_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="image2" value="image" <?php if($sop_product_option == 'image' || $sop_product_option == '') { echo 'checked="checked"'; } ?> <?php checked( $sop_product_option, 'image'); ?>/>
					<label for="image2" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="text2" value="text" <?php if($sop_product_option == 'text') { echo "checked"; } ?> <?php checked( $sop_product_option, 'text'); ?>/>
					<label for="text2" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="sop_product_option" class="wli_product_option" name="sop_product_option" value="<?php if($sop_product_option == '') { echo "image"; } else { echo esc_attr( $sop_product_option ); } ?>"/>
				</div>
			</div>

		    <?php
			
			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
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
				<label for="sop_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_product_custom_text_fontcolor" class="wli_color_picker" name="sop_product_custom_text_fontcolor" value="<?php echo ($sop_product_custom_text_fontcolor) ? esc_attr( $sop_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext" <?php if($sop_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_product_custom_text_backcolor" class="wli_color_picker" name="sop_product_custom_text_backcolor" value="<?php echo esc_attr( $sop_product_custom_text_backcolor ); ?>"/>
			</p>

			<p class="form-field custom_option custom_opttext" <?php if($sop_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_product_custom_text_padding"><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
				<input type="number" id="sop_product_custom_text_padding_top" class="wsbw-small-text" placeholder="Top" name="sop_product_custom_text_padding_top" value="<?php echo esc_attr( $sop_product_custom_text_padding_top ); ?>"/>
				<input type="number" id="sop_product_custom_text_padding_right" class="wsbw-small-text" placeholder="Right" name="sop_product_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_custom_text_padding_right ); ?>"/>
				<input type="number" id="sop_product_custom_text_padding_bottom" class="wsbw-small-text"  placeholder="Bottom" name="sop_product_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_custom_text_padding_bottom ); ?>"/>
				<input type="number" id="sop_product_custom_text_padding_left" class="wsbw-small-text" placeholder="Left" name="sop_product_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_custom_text_padding_left ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($sop_product_option == 'image' || $sop_product_option == '') { echo 'style="display:block"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $sop_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="sop_sticker_custom_id" class="wsbw_upload_img_id" name="sop_sticker_custom_id" value="<?php echo absint( $sop_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>

			<hr>
			<?php

			woocommerce_wp_select( array(
				'id'          => 'sop_sticker_animation_type',
				'value'       => get_post_meta( $post->ID, 'sop_sticker_animation_type', true ),
				'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
				'description' => __( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'desc_tip'    => true,
				'options'     => array(
					'none'    => __( 'None', 'woo-stickers-by-webline' ),
					'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
					'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
					'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
					'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
					'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
				)
			) );

			?>
			<div id="zoominout-options-sop-product" style="display: none;">
				<?php
					woocommerce_wp_text_input( array(
						'id'                => 'sop_sticker_animation_scale',
						'value'             => get_post_meta( $post->ID, 'sop_sticker_animation_scale', true ),
						'label'             => __( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Direction', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Select the animation direction.', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'sop_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'sop_sticker_animation_delay', true ),
				'label'             => __( 'Sticker Animation Delay', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );

			?>
			<hr>
			<?php

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
			
			?>
			
			<div class="form-field term-thumbnail-wrap">
			
				<label for="sop_product_schedule_start_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time Start', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_start_sticker_date_time" name="sop_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($sop_product_schedule_start_sticker_date_time) ? $sop_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the start date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<label for="sop_product_schedule_end_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_end_sticker_date_time" name="sop_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($sop_product_schedule_end_sticker_date_time) ? $sop_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>"
				 />
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the end date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<div class="woo_opt sop_product_schedule_option">
					<label for="sop_product_schedule_option"><?php esc_html_e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-p-schedule" id="image_schedule_sop" value="image_schedule" <?php if($sop_product_schedule_option == 'image_schedule' || $sop_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $sop_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-p-schedule" id="text_schedule_sop" value="text_schedule" <?php if($sop_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $sop_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="sop_product_schedule_option" class="wli_schedule_product_option_product" name="sop_product_schedule_option" value="<?php if($sop_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $sop_product_schedule_option ); } ?>"/>
				</div>
			</div>
			
			<div class="custom_option custom_optimage_sch">
				<?php
			
					woocommerce_wp_text_input( array(
						'id'                => 'sop_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'sop_schedule_sticker_image_width', true ),
						'label'             => __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
			
					woocommerce_wp_text_input( array(
						'id'                => 'sop_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'sop_schedule_sticker_image_height', true ),
						'label'             => __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
				?>
			</div>
			
			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($sop_product_schedule_option == 'image_schedule' || $sop_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
				<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $sop_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="sop_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="sop_schedule_sticker_custom_id" value="<?php echo absint( $sop_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_sop"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_sop"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				
			
			</div>
			
			<?php
			
				woocommerce_wp_text_input( array(
					'id'                => 'sop_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_sop_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );
			
				woocommerce_wp_select( array(
					'id'          => 'sop_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_sop_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );
			
			?>
			
			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_sop" <?php if($sop_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_fontcolor" value="<?php echo ($sop_schedule_product_custom_text_fontcolor) ? esc_attr( $sop_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_sop"<?php if($sop_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="sop_schedule_product_custom_text_backcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $sop_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($sop_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="sop_schedule_product_custom_text_padding"><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
				<input type="number" id="sop_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="sop_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $sop_schedule_product_custom_text_padding_top ); ?>"/>
				<input type="number" id="sop_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="sop_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_right ); ?>"/>
				<input type="number" id="sop_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="sop_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_bottom ); ?>"/>
				<input type="number" id="sop_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="sop_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_left ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
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
				'description'       => __( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_left_right',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_left_right', true ),
				'label'             => __( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_rotate',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_rotate', true ),
				'label'             => __( 'Sticker Rotate (deg):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Degree', 'woo-stickers-by-webline' )
			) );

			?>
		
		    <div class="form-field term-thumbnail-wrap">
				<div class="woo_opt cust_product_option">
					<label for="cust_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="image3" value="image" <?php if($cust_product_option == 'image' || $cust_product_option == '') { echo "checked"; } ?> <?php checked( $cust_product_option, 'image'); ?>/>
					<label for="image3" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="text3" value="text" <?php if($cust_product_option == 'text') { echo "checked"; } ?> <?php checked( $cust_product_option, 'text'); ?>/>
					<label for="text3" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="cust_product_option" class="wli_product_option" name="cust_product_option" value="<?php if($cust_product_option == '') { echo "image"; } else { echo esc_attr( $cust_product_option ); } ?>"/>
				</div>
			</div>

		    <?php 

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_image_width',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_image_width', true ),
				'label'             => __( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true
			) );

			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_image_height',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_image_height', true ),
				'label'             => __( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
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
				<label for="cust_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="cust_product_custom_text_fontcolor" value="<?php echo ($cust_product_custom_text_fontcolor) ? esc_attr( $cust_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
			</p>
			<p class="form-field custom_option custom_opttext" <?php if($cust_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="cust_product_custom_text_backcolor" value="<?php echo esc_attr( $cust_product_custom_text_backcolor ); ?>"/>
			</p>
			
			<p class="form-field custom_option custom_opttext" <?php if($cust_product_option == 'text') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_product_custom_text_padding"><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
				<input type="number" id="cust_product_custom_text_padding_top" class="wsbw-small-text" placeholder="Top" name="cust_product_custom_text_padding_top" value="<?php echo esc_attr( $cust_product_custom_text_padding_top ); ?>"/>
				<input type="number" id="cust_product_custom_text_padding_right" class="wsbw-small-text" placeholder="Right" name="cust_product_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_custom_text_padding_right ); ?>"/>
				<input type="number" id="cust_product_custom_text_padding_bottom" class="wsbw-small-text"  placeholder="Bottom" name="cust_product_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_custom_text_padding_bottom ); ?>"/>
				<input type="number" id="cust_product_custom_text_padding_left" class="wsbw-small-text" placeholder="Left" name="cust_product_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_custom_text_padding_left ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>

			<div class="form-field term-thumbnail-wrap custom_option custom_optimage" <?php if($cust_product_option == 'image' || $cust_product_option == '') { echo 'style="display:block"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
				<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $cust_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="cust_sticker_custom_id" class="wsbw_upload_img_id" name="cust_sticker_custom_id" value="<?php echo absint( $cust_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
				</div>
			</div>

			<hr>
			<?php
			
			woocommerce_wp_select( array(
				'id'          => 'cust_sticker_animation_type',
				'value'       => get_post_meta( $post->ID, 'cust_sticker_animation_type', true ),
				'label'       => __( 'Sticker Animation', 'woo-stickers-by-webline' ),
				'description' => __( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'desc_tip'    => true,
				'options'     => array(
					'none'    => __( 'None', 'woo-stickers-by-webline' ),
					'spin'    => __( 'Spin', 'woo-stickers-by-webline' ),
					'swing'  => __( 'Swing', 'woo-stickers-by-webline' ),
					'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
					'leftright'  => __( 'Left-Right', 'woo-stickers-by-webline' ),
					'updown' => __( 'Up-Down', 'woo-stickers-by-webline' )
				)
			) );

			?>
			<div id="zoominout-options-cust-product" style="display: none;">
				<?php
					woocommerce_wp_text_input( array(
						'id'                => 'cust_sticker_animation_scale',
						'value'             => get_post_meta( $post->ID, 'cust_sticker_animation_scale', true ),
						'label'             => __( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Direction', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Select the animation direction.', 'woo-stickers-by-webline' ),
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
				'label'             => __( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ),
				'description'       => __( 'Specify animation iteration count.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Iteration Count', 'woo-stickers-by-webline' )
			) );
			
			woocommerce_wp_text_input( array(
				'id'                => 'cust_sticker_animation_delay',
				'value'             => get_post_meta( $post->ID, 'cust_sticker_animation_delay', true ),
				'label'             => __( 'Sticker Animation Delay', 'woo-stickers-by-webline' ),
				'class'  	        => 'wsbw-small-text',
				'description'       => __( 'Specify animation delay time.', 'woo-stickers-by-webline' ),
				'desc_tip'			=> true,
				'placeholder'       => __( 'Delay', 'woo-stickers-by-webline' )
			) );

			?>

			<hr>
			<?php

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

			?>

			<div class="form-field term-thumbnail-wrap">
			
				<label for="cust_product_schedule_start_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time Start', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_start_sticker_date_time" name="cust_product_schedule_start_sticker_date_time"  value="<?php echo esc_attr( !empty($cust_product_schedule_start_sticker_date_time) ? $cust_product_schedule_start_sticker_date_time : $formatted_date_time ); ?>"
				/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the start date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<label for="cust_product_schedule_end_sticker_date_time"><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
				<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_end_sticker_date_time" name="cust_product_schedule_end_sticker_date_time"  value="<?php echo esc_attr( !empty($cust_product_schedule_end_sticker_date_time) ? $cust_product_schedule_end_sticker_date_time : $formatted_date_time ); ?>"
				 />
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Set the end date and time for the sticker schedule.', 'woo-stickers-by-webline' ); ?>"></span>
			
				<br><br>
			
				<div class="woo_opt cust_product_schedule_option">
					<label for="cust_product_schedule_option"><?php esc_html_e( 'Scheduled Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
					<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-p-schedule" id="image_schedule_cust" value="image_schedule" <?php if($cust_product_schedule_option == 'image_schedule' || $cust_product_schedule_option == '') { echo "checked"; } ?> <?php checked( $cust_product_schedule_option, 'image_schedule'); ?>/>
					<label for="image" class="radio-label"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
					<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-p-schedule" id="text_schedule_cust" value="text_schedule" <?php if($cust_product_schedule_option == 'text_schedule') { echo "checked"; } ?> <?php checked( $cust_product_schedule_option, 'text_schedule'); ?>/>
					<label for="text" class="radio-label"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
					<input type="hidden" id="cust_product_schedule_option" class="wli_schedule_product_option_product" name="cust_product_schedule_option" value="<?php if($cust_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $cust_product_schedule_option ); } ?>"/>
				</div>
			</div>
			
			<div class="custom_option custom_optimage_sch">
				<?php
			
					woocommerce_wp_text_input( array(
						'id'                => 'cust_schedule_sticker_image_width',
						'value'             => get_post_meta( $post->ID, 'cust_schedule_sticker_image_width', true ),
						'label'             => __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
			
					woocommerce_wp_text_input( array(
						'id'                => 'cust_schedule_sticker_image_height',
						'value'             => get_post_meta( $post->ID, 'cust_schedule_sticker_image_height', true ),
						'label'             => __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ),
						'class'  	        => 'wsbw-small-text',
						'description'       => __( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ),
						'desc_tip'			=> true
					) );
			
				?>
			</div>
			
			<div class="form-field term-thumbnail-wrap custom_option custom_optimage_sch" <?php if($cust_product_schedule_option == 'image_schedule' || $cust_product_schedule_option == '') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
				<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $cust_schedule_image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="cust_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="cust_schedule_sticker_custom_id" value="<?php echo absint( $cust_schedule_sticker_custom_id ); ?>" />
					<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cust"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
					<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cust"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?>"></span>
				</div>
				
			
			</div>
			
			<?php
			
				woocommerce_wp_text_input( array(
					'id'                => 'cust_schedule_product_custom_text',
					'value'             => get_post_meta( $post->ID, '_cust_schedule_product_custom_text', true ),
					'wrapper_class' 	=> 'custom_option custom_opttext_sch ' . $wliclass,
					'label'             => __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ),
					'description'       => __( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' ),
					'desc_tip'			=> true
				) );
			
				woocommerce_wp_select( array(
					'id'          => 'cust_schedule_sticker_type',
					'value'       => get_post_meta( $post->ID, '_cust_schedule_sticker_type', true ),
					'wrapper_class' => 'custom_option custom_opttext_sch ' . $wliclass,
					'class'  	        => 'wsbw-small-text',
					'label'       => __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ),
					'description'       => __( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' ),
					'options'     => array( 'ribbon' => __( 'Ribbon', 'woo-stickers-by-webline' ), 'round' => __( 'Round', 'woo-stickers-by-webline' ) ),
					'desc_tip'			=> true
				) );
			
			?>
			
			<p class="form-field custom_option custom_opttext_sch fontcolor_sch_cust" <?php if($cust_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_fontcolor" value="<?php echo ($cust_schedule_product_custom_text_fontcolor) ? esc_attr( $cust_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch backcolor_sch_cust"<?php if($cust_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
				<label for="cust_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
				<input type="text" id="cust_schedule_product_custom_text_backcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_backcolor" value="<?php echo esc_attr( $cust_schedule_product_custom_text_backcolor ); ?>"/>
				<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
			<p class="form-field custom_option custom_opttext_sch" <?php if($cust_product_schedule_option == 'text_schedule') { echo 'style="display: block;"'; } else { echo 'style="display: none;"'; } ?>>
					<label for="cust_schedule_product_custom_text_padding"><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
					<input type="number" id="cust_schedule_product_custom_text_padding_top" placeholder="Top" class="wsbw-small-text" name="cust_schedule_product_custom_text_padding_top" value="<?php echo esc_attr( $cust_schedule_product_custom_text_padding_top ); ?>"/>
					<input type="number" id="cust_product_schedule_custom_text_padding_right" placeholder="Right" class="wsbw-small-text" name="cust_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_right ); ?>"/>
					<input type="number" id="cust_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="wsbw-small-text" name="cust_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_bottom ); ?>"/>
					<input type="number" id="cust_product_schedule_custom_text_padding_left" placeholder="Left" class="wsbw-small-text" name="cust_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_left ); ?>"/>
					<span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>"></span>
			</p>
		</div>
		<?php
		echo '</div>';
	}

	/**
	 * Save the custom fields.
	 */
	function save_sticker_option_fields( $post_id ) {

		// Bail on autosave/revisions.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Only for products.
		if ( 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		// Permission check.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Nonce check.
		$nonce = isset( $_POST['wli_stickers_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wli_stickers_nonce'] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wli_stickers_save' ) ) {
			return;
		}

		// -------- Save all NEW PRODUCT options --------
		$enable_np_sticker = isset( $_POST['enable_np_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_np_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_np_sticker', $enable_np_sticker );

		$np_no_of_days = isset( $_POST['np_no_of_days'] ) ? absint( wp_unslash( $_POST['np_no_of_days'] ) ) : '';
		update_post_meta( $post_id, '_np_no_of_days', $np_no_of_days );

		$np_sticker_pos = isset( $_POST['np_sticker_pos'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_pos'] ) ) : '';
		update_post_meta( $post_id, '_np_sticker_pos', $np_sticker_pos );

		$np_sticker_left_right = isset( $_POST['np_sticker_left_right'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_left_right'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_left_right', $np_sticker_left_right );

		$np_sticker_top = isset( $_POST['np_sticker_top'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_top'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_top', $np_sticker_top );

		$np_product_option = isset( $_POST['np_product_option'] ) ? sanitize_key( wp_unslash( $_POST['np_product_option'] ) ) : '';
		update_post_meta( $post_id, '_np_product_option', $np_product_option );

		$np_sticker_image_width  = isset( $_POST['np_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_image_width', $np_sticker_image_width );

		$np_sticker_image_height = isset( $_POST['np_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_image_height', $np_sticker_image_height );

		$np_product_custom_text = isset( $_POST['np_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_np_product_custom_text', $np_product_custom_text );

		$np_sticker_type = isset( $_POST['np_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_np_sticker_type', $np_sticker_type );

		$np_product_custom_text_fontcolor = isset( $_POST['np_product_custom_text_fontcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['np_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_fontcolor', $np_product_custom_text_fontcolor );

		$np_product_custom_text_backcolor = isset( $_POST['np_product_custom_text_backcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['np_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_backcolor', $np_product_custom_text_backcolor );

		$np_product_custom_text_padding_top    = isset( $_POST['np_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_top', $np_product_custom_text_padding_top );

		$np_product_custom_text_padding_right  = isset( $_POST['np_product_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_right', $np_product_custom_text_padding_right );

		$np_product_custom_text_padding_bottom = isset( $_POST['np_product_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_bottom', $np_product_custom_text_padding_bottom );

		$np_product_custom_text_padding_left   = isset( $_POST['np_product_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_np_product_custom_text_padding_left', $np_product_custom_text_padding_left );

		$np_sticker_custom_id = isset( $_POST['np_sticker_custom_id'] ) ? absint( wp_unslash( $_POST['np_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_np_sticker_custom_id', $np_sticker_custom_id );

		// Rotate
		$np_sticker_rotate = isset( $_POST['np_sticker_rotate'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_rotate'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_rotate', $np_sticker_rotate );

		// Animation
		$np_sticker_animation_type            = isset( $_POST['np_sticker_animation_type'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_animation_type'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_type', $np_sticker_animation_type );

		$np_sticker_animation_direction       = isset( $_POST['np_sticker_animation_direction'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_animation_direction'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_direction', $np_sticker_animation_direction );

		$np_sticker_animation_scale           = isset( $_POST['np_sticker_animation_scale'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_animation_scale'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_scale', $np_sticker_animation_scale );

		$np_sticker_animation_iteration_count = isset( $_POST['np_sticker_animation_iteration_count'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_animation_iteration_count'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_iteration_count', $np_sticker_animation_iteration_count );

		$np_sticker_animation_delay           = isset( $_POST['np_sticker_animation_delay'] ) ? sanitize_text_field( wp_unslash( $_POST['np_sticker_animation_delay'] ) ) : '';
		update_post_meta( $post_id, 'np_sticker_animation_delay', $np_sticker_animation_delay );

		// -------- Scheduled Sticker for NEW PRODUCTS --------
		$enable_np_product_schedule_sticker = isset( $_POST['enable_np_product_schedule_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_np_product_schedule_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_np_product_schedule_sticker', $enable_np_product_schedule_sticker );

		$np_product_schedule_start_sticker_date_time = isset( $_POST['np_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_schedule_start_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_np_product_schedule_start_sticker_date_time', $np_product_schedule_start_sticker_date_time );

		$np_product_schedule_end_sticker_date_time   = isset( $_POST['np_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_schedule_end_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_np_product_schedule_end_sticker_date_time', $np_product_schedule_end_sticker_date_time );

		$np_product_schedule_option = isset( $_POST['np_product_schedule_option'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_schedule_option'] ) ) : '';
		update_post_meta( $post_id, '_np_product_schedule_option', $np_product_schedule_option );

		$np_schedule_sticker_image_width  = isset( $_POST['np_schedule_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'np_schedule_sticker_image_width', $np_schedule_sticker_image_width );

		$np_schedule_sticker_image_height = isset( $_POST['np_schedule_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'np_schedule_sticker_image_height', $np_schedule_sticker_image_height );

		$np_schedule_sticker_custom_id    = isset( $_POST['np_schedule_sticker_custom_id'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_np_schedule_sticker_custom_id', $np_schedule_sticker_custom_id );

		$np_schedule_product_custom_text = isset( $_POST['np_schedule_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text', $np_schedule_product_custom_text );

		$np_schedule_sticker_type = isset( $_POST['np_schedule_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_np_schedule_sticker_type', $np_schedule_sticker_type );

		$np_schedule_product_custom_text_fontcolor = isset( $_POST['np_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text_fontcolor', $np_schedule_product_custom_text_fontcolor );

		$np_schedule_product_custom_text_backcolor = isset( $_POST['np_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text_backcolor', $np_schedule_product_custom_text_backcolor );

		$np_schedule_product_custom_text_padding_top    = isset( $_POST['np_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['np_schedule_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_np_schedule_product_custom_text_padding_top', $np_schedule_product_custom_text_padding_top );

		$np_product_schedule_custom_text_padding_right  = isset( $_POST['np_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_np_product_schedule_custom_text_padding_right', $np_product_schedule_custom_text_padding_right );

		$np_product_schedule_custom_text_padding_bottom = isset( $_POST['np_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_np_product_schedule_custom_text_padding_bottom', $np_product_schedule_custom_text_padding_bottom );

		$np_product_schedule_custom_text_padding_left   = isset( $_POST['np_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_np_product_schedule_custom_text_padding_left', $np_product_schedule_custom_text_padding_left );

		// -------- Save ON SALE product options --------
		$enable_pos_sticker = isset( $_POST['enable_pos_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_pos_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_pos_sticker', $enable_pos_sticker );

		$pos_sticker_pos = isset( $_POST['pos_sticker_pos'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_pos'] ) ) : '';
		update_post_meta( $post_id, '_pos_sticker_pos', $pos_sticker_pos );

		$pos_sticker_left_right = isset( $_POST['pos_sticker_left_right'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_left_right'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_left_right', $pos_sticker_left_right );

		$pos_sticker_top = isset( $_POST['pos_sticker_top'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_top'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_top', $pos_sticker_top );

		$pos_product_option = isset( $_POST['pos_product_option'] ) ? sanitize_key( wp_unslash( $_POST['pos_product_option'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_option', $pos_product_option );

		$pos_sticker_image_width  = isset( $_POST['pos_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_image_width', $pos_sticker_image_width );

		$pos_sticker_image_height = isset( $_POST['pos_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_image_height', $pos_sticker_image_height );

		$pos_product_custom_text = isset( $_POST['pos_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text', $pos_product_custom_text );

		$pos_sticker_type = isset( $_POST['pos_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_pos_sticker_type', $pos_sticker_type );

		$pos_product_custom_text_fontcolor = isset( $_POST['pos_product_custom_text_fontcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['pos_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_fontcolor', $pos_product_custom_text_fontcolor );

		$pos_product_custom_text_backcolor = isset( $_POST['pos_product_custom_text_backcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['pos_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_backcolor', $pos_product_custom_text_backcolor );

		$pos_product_custom_text_padding_top    = isset( $_POST['pos_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_top', $pos_product_custom_text_padding_top );

		$pos_product_custom_text_padding_right  = isset( $_POST['pos_product_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_right', $pos_product_custom_text_padding_right );

		$pos_product_custom_text_padding_bottom = isset( $_POST['pos_product_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_bottom', $pos_product_custom_text_padding_bottom );

		$pos_product_custom_text_padding_left   = isset( $_POST['pos_product_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_custom_text_padding_left', $pos_product_custom_text_padding_left );

		$pos_sticker_custom_id = isset( $_POST['pos_sticker_custom_id'] ) ? absint( wp_unslash( $_POST['pos_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_pos_sticker_custom_id', $pos_sticker_custom_id );

		// Rotate
		$pos_sticker_rotate = isset( $_POST['pos_sticker_rotate'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_rotate'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_rotate', $pos_sticker_rotate );

		// Animation
		$pos_sticker_animation_type            = isset( $_POST['pos_sticker_animation_type'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_animation_type'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_type', $pos_sticker_animation_type );

		$pos_sticker_animation_direction       = isset( $_POST['pos_sticker_animation_direction'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_animation_direction'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_direction', $pos_sticker_animation_direction );

		$pos_sticker_animation_scale           = isset( $_POST['pos_sticker_animation_scale'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_animation_scale'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_scale', $pos_sticker_animation_scale );

		$pos_sticker_animation_iteration_count = isset( $_POST['pos_sticker_animation_iteration_count'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_animation_iteration_count'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_iteration_count', $pos_sticker_animation_iteration_count );

		$pos_sticker_animation_delay           = isset( $_POST['pos_sticker_animation_delay'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_sticker_animation_delay'] ) ) : '';
		update_post_meta( $post_id, 'pos_sticker_animation_delay', $pos_sticker_animation_delay );

		// -------- Scheduled Sticker for SALE PRODUCTS --------
		$enable_pos_product_schedule_sticker = isset( $_POST['enable_pos_product_schedule_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_pos_product_schedule_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_pos_product_schedule_sticker', $enable_pos_product_schedule_sticker );

		$pos_product_schedule_start_sticker_date_time = isset( $_POST['pos_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_start_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_start_sticker_date_time', $pos_product_schedule_start_sticker_date_time );

		$pos_product_schedule_end_sticker_date_time   = isset( $_POST['pos_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_end_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_end_sticker_date_time', $pos_product_schedule_end_sticker_date_time );

		$pos_product_schedule_option = isset( $_POST['pos_product_schedule_option'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_option'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_option', $pos_product_schedule_option );

		$pos_schedule_sticker_image_width  = isset( $_POST['pos_schedule_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'pos_schedule_sticker_image_width', $pos_schedule_sticker_image_width );

		$pos_schedule_sticker_image_height = isset( $_POST['pos_schedule_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'pos_schedule_sticker_image_height', $pos_schedule_sticker_image_height );

		$pos_schedule_sticker_custom_id    = isset( $_POST['pos_schedule_sticker_custom_id'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_pos_schedule_sticker_custom_id', $pos_schedule_sticker_custom_id );

		$pos_schedule_product_custom_text = isset( $_POST['pos_schedule_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text', $pos_schedule_product_custom_text );

		$pos_schedule_sticker_type = isset( $_POST['pos_schedule_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_pos_schedule_sticker_type', $pos_schedule_sticker_type );

		$pos_schedule_product_custom_text_fontcolor = isset( $_POST['pos_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text_fontcolor', $pos_schedule_product_custom_text_fontcolor );

		$pos_schedule_product_custom_text_backcolor = isset( $_POST['pos_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text_backcolor', $pos_schedule_product_custom_text_backcolor );

		$pos_schedule_product_custom_text_padding_top    = isset( $_POST['pos_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_schedule_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_pos_schedule_product_custom_text_padding_top', $pos_schedule_product_custom_text_padding_top );

		$pos_product_schedule_custom_text_padding_right  = isset( $_POST['pos_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_custom_text_padding_right', $pos_product_schedule_custom_text_padding_right );

		$pos_product_schedule_custom_text_padding_bottom = isset( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_custom_text_padding_bottom', $pos_product_schedule_custom_text_padding_bottom );

		$pos_product_schedule_custom_text_padding_left   = isset( $_POST['pos_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_pos_product_schedule_custom_text_padding_left', $pos_product_schedule_custom_text_padding_left );

		// -------- Save SOLD-OUT product options --------
		$enable_sop_sticker = isset( $_POST['enable_sop_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_sop_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_sop_sticker', $enable_sop_sticker );

		$sop_sticker_pos = isset( $_POST['sop_sticker_pos'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_pos'] ) ) : '';
		update_post_meta( $post_id, '_sop_sticker_pos', $sop_sticker_pos );

		$sop_sticker_left_right = isset( $_POST['sop_sticker_left_right'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_left_right'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_left_right', $sop_sticker_left_right );

		$sop_sticker_top = isset( $_POST['sop_sticker_top'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_top'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_top', $sop_sticker_top );

		$sop_product_option = isset( $_POST['sop_product_option'] ) ? sanitize_key( wp_unslash( $_POST['sop_product_option'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_option', $sop_product_option );

		$sop_sticker_image_width  = isset( $_POST['sop_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_image_width', $sop_sticker_image_width );

		$sop_sticker_image_height = isset( $_POST['sop_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_image_height', $sop_sticker_image_height );

		$sop_product_custom_text = isset( $_POST['sop_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text', $sop_product_custom_text );

		$sop_sticker_type = isset( $_POST['sop_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_sop_sticker_type', $sop_sticker_type );

		$sop_product_custom_text_fontcolor = isset( $_POST['sop_product_custom_text_fontcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['sop_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_fontcolor', $sop_product_custom_text_fontcolor );

		$sop_product_custom_text_backcolor = isset( $_POST['sop_product_custom_text_backcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['sop_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_backcolor', $sop_product_custom_text_backcolor );

		$sop_product_custom_text_padding_top    = isset( $_POST['sop_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_top', $sop_product_custom_text_padding_top );

		$sop_product_custom_text_padding_right  = isset( $_POST['sop_product_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_right', $sop_product_custom_text_padding_right );

		$sop_product_custom_text_padding_bottom = isset( $_POST['sop_product_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_bottom', $sop_product_custom_text_padding_bottom );

		$sop_product_custom_text_padding_left   = isset( $_POST['sop_product_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_custom_text_padding_left', $sop_product_custom_text_padding_left );

		$sop_sticker_custom_id = isset( $_POST['sop_sticker_custom_id'] ) ? absint( wp_unslash( $_POST['sop_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_sop_sticker_custom_id', $sop_sticker_custom_id );

		// Rotate
		$sop_sticker_rotate = isset( $_POST['sop_sticker_rotate'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_rotate'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_rotate', $sop_sticker_rotate );

		// Animation
		$sop_sticker_animation_type            = isset( $_POST['sop_sticker_animation_type'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_animation_type'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_type', $sop_sticker_animation_type );

		$sop_sticker_animation_direction       = isset( $_POST['sop_sticker_animation_direction'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_animation_direction'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_direction', $sop_sticker_animation_direction );

		$sop_sticker_animation_scale           = isset( $_POST['sop_sticker_animation_scale'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_animation_scale'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_scale', $sop_sticker_animation_scale );

		$sop_sticker_animation_iteration_count = isset( $_POST['sop_sticker_animation_iteration_count'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_animation_iteration_count'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_iteration_count', $sop_sticker_animation_iteration_count );

		$sop_sticker_animation_delay           = isset( $_POST['sop_sticker_animation_delay'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_sticker_animation_delay'] ) ) : '';
		update_post_meta( $post_id, 'sop_sticker_animation_delay', $sop_sticker_animation_delay );

		// -------- Scheduled Sticker for SOLD PRODUCTS --------
		$enable_sop_product_schedule_sticker = isset( $_POST['enable_sop_product_schedule_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_sop_product_schedule_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_sop_product_schedule_sticker', $enable_sop_product_schedule_sticker );

		$sop_product_schedule_start_sticker_date_time = isset( $_POST['sop_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_start_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_start_sticker_date_time', $sop_product_schedule_start_sticker_date_time );

		$sop_product_schedule_end_sticker_date_time   = isset( $_POST['sop_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_end_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_end_sticker_date_time', $sop_product_schedule_end_sticker_date_time );

		$sop_product_schedule_option = isset( $_POST['sop_product_schedule_option'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_option'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_option', $sop_product_schedule_option );

		$sop_schedule_sticker_image_width  = isset( $_POST['sop_schedule_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'sop_schedule_sticker_image_width', $sop_schedule_sticker_image_width );

		$sop_schedule_sticker_image_height = isset( $_POST['sop_schedule_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'sop_schedule_sticker_image_height', $sop_schedule_sticker_image_height );

		$sop_schedule_sticker_custom_id    = isset( $_POST['sop_schedule_sticker_custom_id'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_sop_schedule_sticker_custom_id', $sop_schedule_sticker_custom_id );

		$sop_schedule_product_custom_text = isset( $_POST['sop_schedule_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text', $sop_schedule_product_custom_text );

		$sop_schedule_sticker_type = isset( $_POST['sop_schedule_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_sop_schedule_sticker_type', $sop_schedule_sticker_type );

		$sop_schedule_product_custom_text_fontcolor = isset( $_POST['sop_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text_fontcolor', $sop_schedule_product_custom_text_fontcolor );

		$sop_schedule_product_custom_text_backcolor = isset( $_POST['sop_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text_backcolor', $sop_schedule_product_custom_text_backcolor );

		$sop_schedule_product_custom_text_padding_top    = isset( $_POST['sop_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_schedule_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_sop_schedule_product_custom_text_padding_top', $sop_schedule_product_custom_text_padding_top );

		$sop_product_schedule_custom_text_padding_right  = isset( $_POST['sop_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_custom_text_padding_right', $sop_product_schedule_custom_text_padding_right );

		$sop_product_schedule_custom_text_padding_bottom = isset( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_custom_text_padding_bottom', $sop_product_schedule_custom_text_padding_bottom );

		$sop_product_schedule_custom_text_padding_left   = isset( $_POST['sop_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_sop_product_schedule_custom_text_padding_left', $sop_product_schedule_custom_text_padding_left );

		// -------- Save CUSTOM product sticker options --------
		$enable_cust_sticker = isset( $_POST['enable_cust_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_cust_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_cust_sticker', $enable_cust_sticker );

		$cust_sticker_pos = isset( $_POST['cust_sticker_pos'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_pos'] ) ) : '';
		update_post_meta( $post_id, '_cust_sticker_pos', $cust_sticker_pos );

		$cust_sticker_left_right = isset( $_POST['cust_sticker_left_right'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_left_right'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_left_right', $cust_sticker_left_right );

		$cust_sticker_top = isset( $_POST['cust_sticker_top'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_top'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_top', $cust_sticker_top );

		$cust_product_option = isset( $_POST['cust_product_option'] ) ? sanitize_key( wp_unslash( $_POST['cust_product_option'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_option', $cust_product_option );

		$cust_sticker_image_width  = isset( $_POST['cust_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_image_width', $cust_sticker_image_width );

		$cust_sticker_image_height = isset( $_POST['cust_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_image_height', $cust_sticker_image_height );

		$cust_product_custom_text = isset( $_POST['cust_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text', $cust_product_custom_text );

		$cust_sticker_type = isset( $_POST['cust_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_cust_sticker_type', $cust_sticker_type );

		$cust_product_custom_text_fontcolor = isset( $_POST['cust_product_custom_text_fontcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['cust_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_fontcolor', $cust_product_custom_text_fontcolor );

		$cust_product_custom_text_backcolor = isset( $_POST['cust_product_custom_text_backcolor'] ) ? sanitize_hex_color( wp_unslash( $_POST['cust_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_backcolor', $cust_product_custom_text_backcolor );

		$cust_product_custom_text_padding_top    = isset( $_POST['cust_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_top', $cust_product_custom_text_padding_top );

		$cust_product_custom_text_padding_right  = isset( $_POST['cust_product_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_right', $cust_product_custom_text_padding_right );

		$cust_product_custom_text_padding_bottom = isset( $_POST['cust_product_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_bottom', $cust_product_custom_text_padding_bottom );

		$cust_product_custom_text_padding_left   = isset( $_POST['cust_product_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_custom_text_padding_left', $cust_product_custom_text_padding_left ); // <-- fixed: use the left var here

		$cust_sticker_custom_id = isset( $_POST['cust_sticker_custom_id'] ) ? absint( wp_unslash( $_POST['cust_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_cust_sticker_custom_id', $cust_sticker_custom_id );

		// Rotate
		$cust_sticker_rotate = isset( $_POST['cust_sticker_rotate'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_rotate'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_rotate', $cust_sticker_rotate );

		// Animation
		$cust_sticker_animation_type            = isset( $_POST['cust_sticker_animation_type'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_animation_type'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_type', $cust_sticker_animation_type );

		$cust_sticker_animation_direction       = isset( $_POST['cust_sticker_animation_direction'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_animation_direction'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_direction', $cust_sticker_animation_direction );

		$cust_sticker_animation_scale           = isset( $_POST['cust_sticker_animation_scale'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_animation_scale'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_scale', $cust_sticker_animation_scale );

		$cust_sticker_animation_iteration_count = isset( $_POST['cust_sticker_animation_iteration_count'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_animation_iteration_count'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_iteration_count', $cust_sticker_animation_iteration_count );

		$cust_sticker_animation_delay           = isset( $_POST['cust_sticker_animation_delay'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_sticker_animation_delay'] ) ) : '';
		update_post_meta( $post_id, 'cust_sticker_animation_delay', $cust_sticker_animation_delay );

		// -------- Scheduled Sticker for CUSTOM STICKER --------
		$enable_cust_product_schedule_sticker = isset( $_POST['enable_cust_product_schedule_sticker'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_cust_product_schedule_sticker'] ) ) : '';
		update_post_meta( $post_id, '_enable_cust_product_schedule_sticker', $enable_cust_product_schedule_sticker );

		$cust_product_schedule_start_sticker_date_time = isset( $_POST['cust_product_schedule_start_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_start_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_start_sticker_date_time', $cust_product_schedule_start_sticker_date_time );

		$cust_product_schedule_end_sticker_date_time   = isset( $_POST['cust_product_schedule_end_sticker_date_time'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_end_sticker_date_time'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_end_sticker_date_time', $cust_product_schedule_end_sticker_date_time );

		$cust_product_schedule_option = isset( $_POST['cust_product_schedule_option'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_option'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_option', $cust_product_schedule_option );

		$cust_schedule_sticker_image_width  = isset( $_POST['cust_schedule_sticker_image_width'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_image_width'] ) ) : '';
		update_post_meta( $post_id, 'cust_schedule_sticker_image_width', $cust_schedule_sticker_image_width );

		$cust_schedule_sticker_image_height = isset( $_POST['cust_schedule_sticker_image_height'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_image_height'] ) ) : '';
		update_post_meta( $post_id, 'cust_schedule_sticker_image_height', $cust_schedule_sticker_image_height );

		$cust_schedule_sticker_custom_id    = isset( $_POST['cust_schedule_sticker_custom_id'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_custom_id'] ) ) : '';
		update_post_meta( $post_id, '_cust_schedule_sticker_custom_id', $cust_schedule_sticker_custom_id );

		$cust_schedule_product_custom_text = isset( $_POST['cust_schedule_product_custom_text'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_product_custom_text'] ) ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text', $cust_schedule_product_custom_text );

		$cust_schedule_sticker_type = isset( $_POST['cust_schedule_sticker_type'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_type'] ) ) : '';
		update_post_meta( $post_id, '_cust_schedule_sticker_type', $cust_schedule_sticker_type );

		$cust_schedule_product_custom_text_fontcolor = isset( $_POST['cust_schedule_product_custom_text_fontcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_product_custom_text_fontcolor'] ) ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text_fontcolor', $cust_schedule_product_custom_text_fontcolor );

		$cust_schedule_product_custom_text_backcolor = isset( $_POST['cust_schedule_product_custom_text_backcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_product_custom_text_backcolor'] ) ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text_backcolor', $cust_schedule_product_custom_text_backcolor );

		$cust_schedule_product_custom_text_padding_top    = isset( $_POST['cust_schedule_product_custom_text_padding_top'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_schedule_product_custom_text_padding_top'] ) ) : '';
		update_post_meta( $post_id, '_cust_schedule_product_custom_text_padding_top', $cust_schedule_product_custom_text_padding_top );

		$cust_product_schedule_custom_text_padding_right  = isset( $_POST['cust_product_schedule_custom_text_padding_right'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text_padding_right'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_custom_text_padding_right', $cust_product_schedule_custom_text_padding_right );

		$cust_product_schedule_custom_text_padding_bottom = isset( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_custom_text_padding_bottom', $cust_product_schedule_custom_text_padding_bottom );

		$cust_product_schedule_custom_text_padding_left   = isset( $_POST['cust_product_schedule_custom_text_padding_left'] ) ? sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text_padding_left'] ) ) : '';
		update_post_meta( $post_id, '_cust_product_schedule_custom_text_padding_left', $cust_product_schedule_custom_text_padding_left );

	}

	/**
	 * Category sticker fields.
	 */
	public function add_category_fields() {
		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = gmdate($format, $current_timestamp);
		wp_nonce_field( 'wli_stickers_save_category', 'wli_stickers_nonce' );
	?>
	<div class="wsbw-sticker-options-wrap">
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="#wsbw_new_products"><?php esc_html_e( "New Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_products_sale"><?php esc_html_e( "Products On Sale", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_soldout_products"><?php esc_html_e( "Soldout Products", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_cust_products"><?php esc_html_e( "Custom Product Sticker", 'woo-stickers-by-webline' );?></a>
			<a class="nav-tab" href="#wsbw_category_sticker"><?php esc_html_e( "Category Sticker", 'woo-stickers-by-webline' );?></a>
		</h2>

		<div id="wsbw_new_products" class="wsbw_tab_content">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable_np_sticker"><?php esc_html_e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_np_sticker" name="enable_np_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_no_of_days"><?php esc_html_e( 'Number of Days for New Product:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_no_of_days" value="" class="small-text"></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="np_sticker_pos" name="np_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_top" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_left_right"><?php esc_html_e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_left_right" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_rotate" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>						
					</tr>

					<tr>
						<th scope="row"><div class="woo_opt np_product_option"><label for="np_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="image1" value="image" checked="checked"/> <?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="text1" value="text"/> <?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="np_product_option" name="np_product_option" value="image"/>
						<d>
					</tr>

					<tr class ="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="np_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_image_width" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class ="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="np_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_image_height" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_product_custom_text"><?php esc_html_e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="np_product_custom_text" name="np_product_custom_text" value=""></td>
					</tr>

					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_sticker_type"><?php esc_html_e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="np_sticker_type" name="np_sticker_type"><option value="ribbon"><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>

					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="np_product_custom_text_fontcolor" class="wli_color_picker" name="np_product_custom_text_fontcolor" value="#ffffff"></td>
					</tr>

					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for="np_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="np_product_custom_text_backcolor" class="wli_color_picker" name="np_product_custom_text_backcolor" value="#000000"></td>
					</tr>

					<tr class = "custom_option custom_opttext">
						<th scope="row"><label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_product_custom_text_padding_top" value="np_product_custom_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="np_product_custom_text_padding_right" value="np_product_custom_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="np_product_custom_text_padding_bottom" value="np_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="np_product_custom_text_padding_left" value="np_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class ="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="np_sticker_custom_id" class="wsbw_upload_img_id" name="np_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="np_sticker_category_animation_type" id="np_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-new-add-cat" style="display:none;">
						<th scope="row"><label for="np_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="np_sticker_category_animation_scale" type="number" name="np_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="np_sticker_category_animation_direction" id="np_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="np_sticker_category_animation_iteration_count" type="text" name="np_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="np_sticker_category_animation_type_delay" input="np_sticker_category_animation_type_delay" type="number" name="np_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_np_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="enable_np_product_schedule_sticker_category" id="enable_np_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as NEW in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_start_sticker_date_time" name="np_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>" />
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_end_sticker_date_time" name="np_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>"  />
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="np_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt np_product_schedule_option">
								<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule-cat" id="image_schedule_np" value="image_schedule" checked="checked"/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule-cat" id="text_schedule_np" value="text_schedule"/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="np_product_schedule_option" name="np_product_schedule_option" value=""/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="np_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="np_schedule_sticker_image_width" name="np_schedule_sticker_image_width" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="np_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="np_schedule_sticker_image_height" name="np_schedule_sticker_image_height" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top">
							<label for="np_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="np_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
							<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="np_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="np_schedule_sticker_custom_id" value="" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_np"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_np"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="np_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_schedule_custom_text" name="np_product_schedule_custom_text" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="np_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
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
							<label for="np_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="np_schedule_product_custom_text_fontcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_np">
						<th scope="row" valign="top">
							<label for="np_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_schedule_product_custom_text_backcolor" class="wli_color_picker" name="np_schedule_product_custom_text_backcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="np_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="np_product_schedule_custom_text_padding_top" value=""/>
							<input type="number" id="np_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="np_product_schedule_custom_text_padding_right" value=""/>
							<input type="number" id="np_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="np_product_schedule_custom_text_padding_bottom" value=""/>
							<input type="number" id="np_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="np_product_schedule_custom_text_padding_left" value=""/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<div id="wsbw_products_sale" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row"><label for="enable_pos_sticker"><?php esc_html_e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_pos_sticker" name="enable_pos_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="pos_sticker_pos" name="pos_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_top" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_left_right"><?php esc_html_e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_left_right" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_rotate" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><div class="woo_opt pos_product_option"><label for="pos_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="image2" value="image" checked="checked"/> <?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="text2" value="text"/> <?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="pos_product_option" name="pos_product_option" value="image"/>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="pos_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_image_width" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="pos_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_image_height" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_product_custom_text"><?php esc_html_e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="pos_product_custom_text" name="pos_product_custom_text" value=""></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_sticker_type"><?php esc_html_e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="pos_sticker_type" name="pos_sticker_type"><option value="ribbon"><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="pos_product_custom_text_fontcolor" class="wli_color_picker" name="pos_product_custom_text_fontcolor" value="#ffffff"/></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="pos_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="pos_product_custom_text_backcolor" class="wli_color_picker" name="pos_product_custom_text_backcolor" value="#000000"/></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_product_custom_text_padding_top" value="pos_product_custom_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="pos_product_custom_text_padding_right" value="pos_product_custom_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="pos_product_custom_text_padding_bottom" value="pos_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="pos_product_custom_text_padding_left" value="pos_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="pos_sticker_custom_id" class="wsbw_upload_img_id" name="pos_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="pos_sticker_category_animation_type" id="pos_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-pos-add-cat" style="display:none;">
						<th scope="row"><label for="pos_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="pos_sticker_category_animation_scale" type="number" name="pos_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="pos_sticker_category_animation_direction" id="pos_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="pos_sticker_category_animation_iteration_count" type="text" name="pos_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					
					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="pos_sticker_category_animation_type_delay" type="number" name="pos_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_pos_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="enable_pos_product_schedule_sticker_category" id="enable_pos_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as Sale in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_start_sticker_date_time" name="pos_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>" />
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_end_sticker_date_time" name="pos_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>"  />
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="pos_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt pos_product_schedule_option">
								<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule-cat" id="image_schedule_pos" value="image_schedule" checked="checked"/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule-cat" id="text_schedule_pos" value="text_schedule"/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="pos_product_schedule_option" name="pos_product_schedule_option" value=""/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="pos_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="pos_schedule_sticker_image_width" name="pos_schedule_sticker_image_width" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="pos_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="pos_schedule_sticker_image_height" name="pos_schedule_sticker_image_height" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top">
							<label for="pos_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="pos_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
							<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="pos_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="pos_schedule_sticker_custom_id" value="" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_pos"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_pos"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="pos_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_schedule_custom_text" name="pos_product_schedule_custom_text" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="pos_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
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
							<label for="pos_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_fontcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_pos">
						<th scope="row" valign="top">
							<label for="pos_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_schedule_product_custom_text_backcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_backcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="pos_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="pos_product_schedule_custom_text_padding_top" value=""/>
							<input type="number" id="pos_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="pos_product_schedule_custom_text_padding_right" value=""/>
							<input type="number" id="pos_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="pos_product_schedule_custom_text_padding_bottom" value=""/>
							<input type="number" id="pos_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="pos_product_schedule_custom_text_padding_left" value=""/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<div id="wsbw_soldout_products" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row"><label for="enable_sop_sticker"><?php esc_html_e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_sop_sticker" name="enable_sop_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="sop_sticker_pos" name="sop_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_top" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_left_right"><?php esc_html_e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_left_right" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_rotate" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><div class="woo_opt sop_product_option"><label for="sop_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="image3" value="image" checked="checked"/> <?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="text3" value="text"/> <?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="sop_product_option" name="sop_product_option" value="image"/>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="sop_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_image_width" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="sop_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_sticker_image_height" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_product_custom_text"><?php esc_html_e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="sop_product_custom_text" name="sop_product_custom_text" value=""></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_sticker_type"><?php esc_html_e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="sop_sticker_type" name="sop_sticker_type"><option value="ribbon"><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="sop_product_custom_text_fontcolor" class="wli_color_picker" name="sop_product_custom_text_fontcolor" value="#ffffff"/></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="sop_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="sop_product_custom_text_backcolor" class="wli_color_picker" name="sop_product_custom_text_backcolor" value="#000000"/></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="sop_product_custom_text_padding_right" value="sop_product_custom_text_padding_right" placeholder="Top" class="small-text">
							<input type="number" name="sop_product_custom_text_padding_bottom" value="sop_product_custom_text_padding_bottom" placeholder="Right" class="small-text">
							<input type="number" name="sop_product_custom_text_padding_top" value="sop_product_custom_text_padding_top" placeholder="Bottom" class="small-text">
							<input type="number" name="sop_product_custom_text_padding_left" value="sop_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage">
						<th scope="row"><label><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="sop_sticker_custom_id" class="wsbw_upload_img_id" name="sop_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="sop_sticker_category_animation_type" id="sop_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-sop-add-cat" style="display:none;">
						<th scope="row"><label for="sop_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="sop_sticker_category_animation_scale" type="number" name="sop_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="sop_sticker_category_animation_direction" id="sop_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="sop_sticker_category_animation_iteration_count" type="text" name="sop_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="sop_sticker_category_animation_type_delay" type="number" name="sop_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_sop_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="enable_sop_product_schedule_sticker_category" id="enable_sop_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as Sold in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_start_sticker_date_time" name="sop_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>" />
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_end_sticker_date_time" name="sop_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>"  />
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="sop_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt sop_product_schedule_option">
								<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule-cat" id="image_schedule_sop" value="image_schedule" checked="checked"/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule-cat" id="text_schedule_sop" value="text_schedule"/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="sop_product_schedule_option" name="sop_product_schedule_option" value=""/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="sop_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="sop_schedule_sticker_image_width" name="sop_schedule_sticker_image_width" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="sop_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="sop_schedule_sticker_image_height" name="sop_schedule_sticker_image_height" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top">
							<label for="sop_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="sop_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
							<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="sop_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="sop_schedule_sticker_custom_id" value="" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_sop"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_sop"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="sop_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_schedule_custom_text" name="sop_product_schedule_custom_text" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="sop_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
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
							<label for="sop_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_fontcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_sop">
						<th scope="row" valign="top">
							<label for="sop_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_schedule_product_custom_text_backcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_backcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="sop_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="sop_product_schedule_custom_text_padding_top" value=""/>
							<input type="number" id="sop_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="sop_product_schedule_custom_text_padding_right" value=""/>
							<input type="number" id="sop_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="sop_product_schedule_custom_text_padding_bottom" value=""/>
							<input type="number" id="sop_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="sop_product_schedule_custom_text_padding_left" value=""/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<div id="wsbw_cust_products" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="enable_cust_sticker"><?php esc_html_e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_cust_sticker" name="enable_cust_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="cust_sticker_pos" name="cust_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left"><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_top" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_left_right"><?php esc_html_e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_left_right" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_rotate" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><div class="woo_opt cust_product_option"><label for="cust_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="image4" value="image" checked="checked"/> <?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="text4" value="text"/> <?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="cust_product_option" name="cust_product_option" value="image"/>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="cust_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_image_width" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="cust_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_image_height" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_product_custom_text"><?php esc_html_e( 'Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="cust_product_custom_text" name="cust_product_custom_text" value=""></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_sticker_type"><?php esc_html_e( 'Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="cust_sticker_type" name="cust_sticker_type"><option value="ribbon"><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_product_custom_text_fontcolor"><?php esc_html_e( 'Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="cust_product_custom_text_fontcolor" value="#ffffff"></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="cust_product_custom_text_backcolor"><?php esc_html_e( 'Custom Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="cust_product_custom_text_backcolor" value="#000000"></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_product_custom_text_padding_top" value="cust_product_custom_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="cust_product_custom_text_padding_right" value="cust_product_custom_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="cust_product_custom_text_padding_bottom" value="cust_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="cust_product_custom_text_padding_left" value="cust_product_custom_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage">
						<th scope="row"><label><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
								<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="cust_sticker_custom_id" class="wsbw_upload_img_id" name="cust_sticker_custom_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="cust_sticker_category_animation_type" id ="cust_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-cust-add-cat" style="display:none;">
						<th scope="row"><label for="cust_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="cust_sticker_category_animation_scale" type="number" name="cust_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="cust_sticker_category_animation_direction" id="cust_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="cust_sticker_category_animation_iteration_count" type="text" name="cust_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>
					
					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="cust_sticker_category_animation_type_delay" type="number" name="cust_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_cust_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="enable_cust_product_schedule_sticker_category" id="enable_cust_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled custom sticker display for products in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" id="cust_product_schedule_start_sticker_date_time" name="cust_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>" />
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" id="cust_product_schedule_end_sticker_date_time" name="cust_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>" />
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="cust_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt cust_product_schedule_option">
								<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule-cat" id="image_schedule_cust" value="image_schedule" checked="checked"/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule-cat" id="text_schedule_cust" value="text_schedule"/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="cust_product_schedule_option" name="cust_product_schedule_option" value=""/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="cust_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="cust_schedule_sticker_image_width" name="cust_schedule_sticker_image_width" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="cust_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="cust_schedule_sticker_image_height" name="cust_schedule_sticker_image_height" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top">
							<label for="cust_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="cust_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
							<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="cust_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="cust_schedule_sticker_custom_id" value="" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cust"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cust"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="cust_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_schedule_custom_text" name="cust_product_schedule_custom_text" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="cust_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
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
							<label for="cust_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_fontcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_cust">
						<th scope="row" valign="top">
							<label for="cust_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_schedule_product_custom_text_backcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_backcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="cust_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="cust_product_schedule_custom_text_padding_top" value=""/>
							<input type="number" id="cust_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="cust_product_schedule_custom_text_padding_right" value=""/>
							<input type="number" id="cust_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="cust_product_schedule_custom_text_padding_bottom" value=""/>
							<input type="number" id="cust_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="cust_product_schedule_custom_text_padding_left" value=""/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</tbody>
			</table>
		</div>

		<div id="wsbw_category_sticker" class="wsbw_tab_content" style="display: none;">
			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row"><label for="enable_category_sticker"><?php esc_html_e( 'Enable Category Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_category_sticker" name="enable_category_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Please select', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes"><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no"><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Enable sticker on this category', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="category_sticker_pos" name="category_sticker_pos" class="postform">
								<option value="left"><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right"><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_top" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_left_right"><?php esc_html_e( 'Sticker Position Left/Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_left_right" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_sticker_rotate" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><div class="woo_opt category_sticker_option"><label for="category_sticker_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label></div></th>
						<td>
							<label><input type="radio" name="stickeroption5" class="wli-woosticker-radio" id="image5" value="image" checked="checked"/> <?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
							<label><input type="radio" name="stickeroption5" class="wli-woosticker-radio" id="text4" value="text"/> <?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
							<input type="hidden" class="wli_product_option" id="category_sticker_option" name="category_sticker_option" value="image"/>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="category_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_image_width" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_optimage" style="display: block;">
						<th scope="row"><label for="category_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_image_height" value="" class="small-text"><p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_text"><?php esc_html_e( 'Sticker Text:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="category_sticker_text" name="category_sticker_text" value=""></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_type"><?php esc_html_e( 'Sticker Type:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><select id="category_sticker_type" name="category_sticker_type"><option value="ribbon"><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option><option value="round"><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option></select></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_text_fontcolor"><?php esc_html_e( 'Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="category_sticker_text_fontcolor" class="wli_color_picker" name="category_sticker_text_fontcolor" value="#ffffff"/></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for="category_sticker_text_backcolor"><?php esc_html_e( 'Sticker Text Background Color:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="text" id="category_sticker_text_backcolor" class="wli_color_picker" name="category_sticker_text_backcolor" value="#000000"/></td>
					</tr>

					<tr class="custom_option custom_opttext">
						<th scope="row"><label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="category_sticker_text_padding_top" value="category_sticker_text_padding_top" placeholder="Top" class="small-text">
							<input type="number" name="category_sticker_text_padding_right" value="category_sticker_text_padding_right" placeholder="Right" class="small-text">
							<input type="number" name="category_sticker_text_padding_bottom" value="category_sticker_text_padding_bottom" placeholder="Bottom" class="small-text">
							<input type="number" name="category_sticker_text_padding_left" value="category_sticker_text_padding_left" placeholder="Left" class="small-text">
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom, and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr class="custom_option custom_optimage">
						<th scope="row"><label><?php esc_html_e( 'Add your sticker image:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<div id="category_sticker_image" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 70px;">
								<input type="hidden" id="category_sticker_image_id" class="wsbw_upload_img_id" name="category_sticker_image_id" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
							<p class="description"><?php esc_html_e( 'Upload your sticker image which you want to display on this category.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="category_sticker_sticker_category_animation_type" id="category_sticker_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = '';

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-category-add-cat" style="display:none;">
						<th scope="row"><label for="category_sticker_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="category_sticker_sticker_category_animation_scale" type="number" name="category_sticker_sticker_category_animation_scale" step="any" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="category_sticker_sticker_category_animation_direction" id="category_sticker_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="category_sticker_sticker_category_animation_iteration_count" type="text" name="category_sticker_sticker_category_animation_iteration_count" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="category_sticker_sticker_category_animation_type_delay" type="number" name="category_sticker_sticker_category_animation_type_delay" value="" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_category_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="enable_category_product_schedule_sticker_category" id="enable_category_product_schedule_sticker_category">
								<option value="yes">Yes</option>
								<option value="no" selected>No</option>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled sticker display for Categories in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_start_sticker_date_time" name="category_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>" />
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_end_sticker_date_time" name="category_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr($formatted_date_time);; ?>" />
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="category_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt category_product_schedule_option">
								<input type="radio" name="stickeroption_sch_5" class="-cat" id="image_schedule_category" value="image_schedule" checked="checked"/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_5" class="wli-woosticker-radio-schedule-cat" id="text_schedule_category" value="text_schedule"/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="category_product_schedule_option" name="category_product_schedule_option" value=""/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="category_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="category_schedule_sticker_image_width" name="category_schedule_sticker_image_width" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top"><label for="category_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="category_schedule_sticker_image_height" name="category_schedule_sticker_image_height" value="" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="display: table-row;">
						<th scope="row" valign="top">
							<label for="category_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="category_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;">
							<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
							</div>
							<div style="line-height: 60px;">
								<input type="hidden" id="category_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="category_schedule_sticker_custom_id" value="" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cat"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cat"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="category_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_product_schedule_custom_text" name="category_product_schedule_custom_text" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for="category_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
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
							<label for="category_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="category_schedule_product_custom_text_fontcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat">
						<th scope="row" valign="top">
							<label for="category_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_schedule_product_custom_text_backcolor" class="wli_color_picker" name="category_schedule_product_custom_text_backcolor" value=""/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="category_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="category_product_schedule_custom_text_padding_top" value=""/>
							<input type="number" id="category_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="category_product_schedule_custom_text_padding_right" value=""/>
							<input type="number" id="category_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="category_product_schedule_custom_text_padding_bottom" value=""/>
							<input type="number" id="category_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="category_product_schedule_custom_text_padding_left" value=""/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
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

		wp_nonce_field( 'wli_stickers_save_category', 'wli_stickers_nonce' );

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
		$show_image_np_schedule_product = ($np_product_schedule_option == "image_schedule") ? 'display: table-row;' : '';
		$show_text_np_schedule_product = ($np_product_schedule_option == "text_schedule") ? 'display: table-row;' : '';
		if($show_text_np_schedule_product == ""){
			$show_image_np_schedule_product = "display: table-row;";
		}

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

		$show_image_pos_schedule_product = ($pos_product_schedule_option == "image_schedule") ? 'display: table-row;' : '';
		$show_text_pos_schedule_product = ($pos_product_schedule_option == "text_schedule") ? 'display: table-row;' : '';
		if($show_text_pos_schedule_product == ""){
			$show_image_pos_schedule_product = "display: table-row;";
		}

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

		$show_image_sop_schedule_product = ($sop_product_schedule_option == "image_schedule") ? 'display: table-row;' : '';
		$show_text_sop_schedule_product = ($sop_product_schedule_option == "text_schedule") ? 'display: table-row;' : '';
		if($show_text_sop_schedule_product == ""){
			$show_image_sop_schedule_product = "display: table-row;";
		}

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
		
		$show_image_cust_schedule_product = ($cust_product_schedule_option == "image_schedule") ? 'display: table-row;' : '';
		$show_text_cust_schedule_product = ($cust_product_schedule_option == "text_schedule") ? 'display: table-row;' : '';
		if($show_text_cust_schedule_product == ""){
			$show_image_cust_schedule_product = "display: table-row;";
		}

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

		$show_image_category_schedule_product = ($category_product_schedule_option == "image_schedule") ? 'display: table-row;' : '';
		$show_text_category_schedule_product = ($category_product_schedule_option == "text_schedule") ? 'display: table-row;' : '';
		if($show_text_category_schedule_product == ""){
			$show_image_category_schedule_product = "display: table-row;";
		}

		$format = 'Y-m-d\TH:i'; 
		$current_timestamp = current_time('timestamp');
		$formatted_date_time = gmdate($format, $current_timestamp);

		?>
		<tr class="form-field wsbw-sticker-options-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Sticker Options', 'woo-stickers-by-webline' ); ?></label></th>
			<td>
				<h2 class="nav-tab-wrapper">
					<a class="nav-tab nav-tab-active" href="#wsbw_new_products"><?php esc_html_e( "New Products", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_products_sale"><?php esc_html_e( "Products On Sale", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_soldout_products"><?php esc_html_e( "Soldout Products", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_cust_products"><?php esc_html_e( "Custom Product Sticker", 'woo-stickers-by-webline' );?></a>
					<a class="nav-tab" href="#wsbw_category_sticker"><?php esc_html_e( "Category Sticker", 'woo-stickers-by-webline' );?></a>
				</h2>
				<table id="wsbw_new_products" class="wsbw_tab_content">

					<tr>
						<th scope="row" valign="top"><label for="enable_np_sticker"><?php esc_html_e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_np_sticker" name="enable_np_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_np_sticker, 'yes');?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_np_sticker, 'no');?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="np_no_of_days"><?php esc_html_e( 'Number of Days for New Product:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_no_of_days" value="<?php echo absint( $np_no_of_days ); ?>" class="small-text">
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="np_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="np_sticker_pos" name="np_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $np_sticker_pos, 'left');?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $np_sticker_pos, 'right');?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="np_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_top" value="<?php echo esc_attr( $np_sticker_top ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="np_sticker_left_right"><?php esc_html_e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_left_right" value="<?php echo esc_attr( $np_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>							
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="np_sticker_rotate" value="<?php echo esc_attr( $np_sticker_rotate ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="np_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt np_product_option">
								<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if($np_product_option == 'image' || $np_product_option == '') { echo 'checked'; } ?>/>
								<label for="image"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($np_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="np_product_option" name="np_product_option" value="<?php if($np_product_option == '') { echo "image"; } else { echo esc_attr( $np_product_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_np_product);?>>
						<th scope="row" valign="top"><label for="np_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_image_width" value="<?php echo esc_attr( $np_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_np_product);?>>
						<th scope="row" valign="top"><label for="np_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="np_sticker_image_height" value="<?php echo esc_attr( $np_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for="np_product_custom_text"><?php esc_html_e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_custom_text" name="np_product_custom_text" value="<?php echo esc_attr( $np_product_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for="np_sticker_type"><?php esc_html_e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='np_sticker_type'
								name="np_sticker_type">
								<option value='ribbon'
									<?php selected( $np_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $np_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for="np_product_custom_text_fontcolor"><?php esc_html_e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_custom_text_fontcolor" class="wli_color_picker" name="np_product_custom_text_fontcolor" value="<?php echo ($np_product_custom_text_fontcolor) ? esc_attr( $np_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for="np_product_custom_text_backcolor"><?php esc_html_e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_custom_text_backcolor" class="wli_color_picker" name="np_product_custom_text_backcolor" value="<?php echo esc_attr( $np_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="np_product_custom_text_padding_top" placeholder="Top" class="small-text" name="np_product_custom_text_padding_top" value="<?php echo esc_attr( $np_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="np_product_custom_text_padding_right" placeholder="Right" class="small-text" name="np_product_custom_text_padding_right" value="<?php echo esc_attr( $np_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="np_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="np_product_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="np_product_custom_text_padding_left" placeholder="Left" class="small-text" name="np_product_custom_text_padding_left" value="<?php echo esc_attr( $np_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_np_product);?>>
						<th scope="row" valign="top">
							<label for="np_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="np_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($np_image); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="np_sticker_custom_id" class="wsbw_upload_img_id" name="np_sticker_custom_id" value="<?php echo absint( $np_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="np_sticker_category_animation_type" id="np_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = $np_sticker_category_animation_type;

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-new-edit-cat" style="display:none;">
						<th scope="row"><label for="np_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="np_sticker_category_animation_scale" type="number" name="np_sticker_category_animation_scale" step="any" value="<?php echo esc_attr( $np_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="np_sticker_category_animation_direction" id="np_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="np_sticker_category_animation_iteration_count" type="text" name="np_sticker_category_animation_iteration_count" value="<?php echo esc_attr( $np_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="np_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="np_sticker_category_animation_type_delay" type="number" name="np_sticker_category_animation_type_delay" value="<?php echo esc_attr( $np_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_np_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as NEW in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>						
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_start_sticker_date_time" name="np_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr( $np_product_schedule_start_sticker_date_time ); ?>"
								>
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="np_product_schedule_end_sticker_date_time" name="np_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr( $np_product_schedule_end_sticker_date_time ); ?>" 
								>
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="np_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt np_product_schedule_option">
								<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule-cat" id="image_schedule_np" value="image_schedule" <?php if($np_product_schedule_option == 'image_schedule' || $np_product_schedule_option == '') { echo 'checked'; } ?>/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule-cat" id="text_schedule_np" value="text_schedule" <?php if($np_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="np_product_schedule_option" name="np_product_schedule_option" value="<?php if($np_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $np_product_schedule_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_np_schedule_product); ?>">
						<th scope="row" valign="top"><label for="np_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="np_schedule_sticker_image_width" name="np_schedule_sticker_image_width" value="<?php echo esc_attr( $np_schedule_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_np_schedule_product); ?>">
						<th scope="row" valign="top"><label for="np_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="np_schedule_sticker_image_height" name="np_schedule_sticker_image_height" value="<?php echo esc_attr( $np_schedule_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_np_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="np_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="np_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($np_schedule_image); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="np_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="np_schedule_sticker_custom_id" value="<?php echo absint( $np_schedule_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_np"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_np"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_np_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="np_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_product_schedule_custom_text" name="np_product_schedule_custom_text" value="<?php echo esc_attr( $np_product_schedule_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_np_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="np_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='np_schedule_sticker_type'
								name="np_schedule_sticker_type">
								<option value='ribbon'
									<?php selected( $np_schedule_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $np_schedule_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch fontcolor_cat_np" style="<?php echo esc_attr($show_text_np_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="np_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="np_schedule_product_custom_text_fontcolor" value="<?php echo ($np_schedule_product_custom_text_fontcolor) ? esc_attr( $np_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_np" style="<?php echo esc_attr($show_text_np_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="np_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="np_schedule_product_custom_text_backcolor" class="wli_color_picker" name="np_schedule_product_custom_text_backcolor" value="<?php echo ($np_schedule_product_custom_text_backcolor) ? esc_attr( $np_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_np_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="np_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="np_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_top ); ?>"/>
							<input type="number" id="np_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="np_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_right ); ?>"/>
							<input type="number" id="np_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="np_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="np_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="np_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $np_product_schedule_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</table>

				<table id="wsbw_products_sale" class="wsbw_tab_content" style="display: none;">

					<tr>
						<th scope="row" valign="top"><label for="enable_pos_sticker"><?php esc_html_e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_pos_sticker" name="enable_pos_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_pos_sticker, 'yes');?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_pos_sticker, 'no');?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="pos_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="pos_sticker_pos" name="pos_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $pos_sticker_pos, 'left');?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $pos_sticker_pos, 'right');?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="pos_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_top" value="<?php echo esc_attr( $pos_sticker_top ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="pos_sticker_left_right"><?php esc_html_e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_left_right" value="<?php echo esc_attr( $pos_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><label for="pos_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="pos_sticker_rotate" value="<?php echo esc_attr( $pos_sticker_rotate ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="pos_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt pos_product_option">
								<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="image1" value="image" <?php if($pos_product_option == 'image' || $pos_product_option == '') { echo 'checked'; } ?>/>
								<label for="image1"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption1" class="wli-woosticker-radio" id="text1" value="text" <?php if($pos_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text1"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="pos_product_option" name="pos_product_option" value="<?php if($pos_product_option == '') { echo "image"; } else { echo esc_attr( $pos_product_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_pos_sticker);?>>
						<th scope="row" valign="top"><label for="pos_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_image_width" value="<?php echo esc_attr( $pos_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_pos_sticker);?>>
						<th scope="row" valign="top"><label for="pos_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="pos_sticker_image_height" value="<?php echo esc_attr( $pos_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_pos_sticker);?>>
						<th scope="row" valign="top">
							<label for="pos_product_custom_text"><?php esc_html_e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_custom_text" name="pos_product_custom_text" value="<?php echo esc_attr( $pos_product_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_pos_sticker);?>>
						<th scope="row" valign="top">
							<label for="pos_sticker_type"><?php esc_html_e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='pos_sticker_type'
								name="pos_sticker_type">
								<option value='ribbon'
									<?php selected( $pos_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $pos_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_pos_sticker);?>>
						<th scope="row" valign="top">
							<label for="pos_product_custom_text_fontcolor"><?php esc_html_e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_custom_text_fontcolor" class="wli_color_picker" name="pos_product_custom_text_fontcolor" value="<?php echo ($pos_product_custom_text_fontcolor) ? esc_attr( $pos_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_pos_sticker);?>>
						<th scope="row" valign="top">
							<label for="pos_product_custom_text_backcolor"><?php esc_html_e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_custom_text_backcolor" class="wli_color_picker" name="pos_product_custom_text_backcolor" value="<?php echo esc_attr( $pos_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="pos_product_custom_text_padding_top" placeholder="Top" class="small-text" name="pos_product_custom_text_padding_top" value="<?php echo esc_attr( $pos_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="pos_product_custom_text_padding_right" placeholder="Right" class="small-text" name="pos_product_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="pos_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="pos_product_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="pos_product_custom_text_padding_left" placeholder="Left" class="small-text" name="pos_product_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_pos_sticker);?>>
						<th scope="row" valign="top">
							<label for="pos_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="pos_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $pos_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="pos_sticker_custom_id" class="wsbw_upload_img_id" name="pos_sticker_custom_id" value="<?php echo absint( $pos_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="pos_sticker_category_animation_type" id="pos_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = $pos_sticker_category_animation_type;

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-sale-edit-cat" style="display:none;">
						<th scope="row"><label for="pos_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="pos_sticker_category_animation_scale" type="number" name="pos_sticker_category_animation_scale" step="any" value="<?php echo esc_attr( $pos_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="pos_sticker_category_animation_direction" id="pos_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="pos_sticker_category_animation_iteration_count" type="text" name="pos_sticker_category_animation_iteration_count" value="<?php echo esc_attr( $pos_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="pos_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="pos_sticker_category_animation_type_delay" type="number" name="pos_sticker_category_animation_type_delay" value="<?php echo esc_attr( $pos_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_pos_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as Sale in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>										
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_start_sticker_date_time" name="pos_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr( $pos_product_schedule_start_sticker_date_time ); ?>"
								/>
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="pos_product_schedule_end_sticker_date_time" name="pos_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr( $pos_product_schedule_end_sticker_date_time ); ?>"
								 />
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="pos_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt pos_product_schedule_option">
								<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule-cat" id="image_schedule_pos" value="image_schedule" <?php if($pos_product_schedule_option == 'image_schedule' || $pos_product_schedule_option == '') { echo 'checked'; } ?>/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_1" class="wli-woosticker-radio-schedule-cat" id="text_schedule_pos" value="text_schedule" <?php if($pos_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="pos_product_schedule_option" name="pos_product_schedule_option" value="<?php if($pos_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $pos_product_schedule_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_pos_schedule_product); ?>">
						<th scope="row" valign="top"><label for="pos_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="pos_schedule_sticker_image_width" name="pos_schedule_sticker_image_width" value="<?php echo esc_attr( $pos_schedule_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_pos_schedule_product); ?>">
						<th scope="row" valign="top"><label for="pos_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="pos_schedule_sticker_image_height" name="pos_schedule_sticker_image_height" value="<?php echo esc_attr( $pos_schedule_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_pos_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="pos_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="pos_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($pos_schedule_image); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="pos_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="pos_schedule_sticker_custom_id" value="<?php echo absint( $pos_schedule_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_pos"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_pos"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_pos_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="pos_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_product_schedule_custom_text" name="pos_product_schedule_custom_text" value="<?php echo esc_attr( $pos_product_schedule_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_pos_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="pos_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='pos_schedule_sticker_type'
								name="pos_schedule_sticker_type">
								<option value='ribbon'
									<?php selected( $pos_schedule_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $pos_schedule_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch fontcolor_cat_pos" style="<?php echo esc_attr($show_text_pos_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="pos_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_fontcolor" value="<?php echo ($pos_schedule_product_custom_text_fontcolor) ? esc_attr( $pos_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_pos" style="<?php echo esc_attr($show_text_pos_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="pos_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="pos_schedule_product_custom_text_backcolor" class="wli_color_picker" name="pos_schedule_product_custom_text_backcolor" value="<?php echo ($pos_schedule_product_custom_text_backcolor) ? esc_attr( $pos_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_pos_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="pos_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="pos_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_top ); ?>"/>
							<input type="number" id="pos_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="pos_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_right ); ?>"/>
							<input type="number" id="pos_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="pos_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="pos_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="pos_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $pos_product_schedule_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</table>

				<table id="wsbw_soldout_products" class="wsbw_tab_content" style="display: none;">

					<tr>
						<th scope="row" valign="top"><label for="enable_sop_sticker"><?php esc_html_e( 'Enable Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_sop_sticker" name="enable_sop_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_sop_sticker, 'yes');?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_sop_sticker, 'no');?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="sop_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="sop_sticker_pos" name="sop_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $sop_sticker_pos, 'left');?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $sop_sticker_pos, 'right');?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="sop_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_top" value="<?php echo esc_attr( $sop_sticker_top ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="sop_sticker_left_right"><?php esc_html_e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_left_right" value="<?php echo esc_attr( $sop_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_rotate" value="<?php echo esc_attr( $sop_sticker_rotate ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row" valign="top">
							<label for="sop_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt sop_product_option">
								<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="image2" value="image" <?php if($sop_product_option == 'image' || $sop_product_option == '') { echo 'checked'; } ?>/>
								<label for="image2"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption2" class="wli-woosticker-radio" id="text2" value="text" <?php if($sop_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text2"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="sop_product_option" name="sop_product_option" value="<?php if($sop_product_option == '') { echo "image"; } else { echo esc_attr( $sop_product_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_sop_sticker);?>>
						<th scope="row" valign="top"><label for="sop_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_image_width" value="<?php echo esc_attr( $sop_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_sop_sticker);?>>
						<th scope="row" valign="top"><label for="sop_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="sop_sticker_image_height" value="<?php echo esc_attr( $sop_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sop_sticker);?>>
						<th scope="row" valign="top">
							<label for="sop_product_custom_text"><?php esc_html_e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_custom_text" name="sop_product_custom_text" value="<?php echo esc_attr( $sop_product_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sop_sticker);?>>
						<th scope="row" valign="top">
							<label for="sop_sticker_type"><?php esc_html_e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='sop_sticker_type'
								name="sop_sticker_type">
								<option value='ribbon'
									<?php selected( $sop_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $sop_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sop_sticker);?>>
						<th scope="row" valign="top">
							<label for="sop_product_custom_text_fontcolor"><?php esc_html_e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_custom_text_fontcolor" class="wli_color_picker" name="sop_product_custom_text_fontcolor" value="<?php echo ($sop_product_custom_text_fontcolor) ? esc_attr( $sop_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sop_sticker);?>>
						<th scope="row" valign="top">
							<label for="sop_product_custom_text_backcolor"><?php esc_html_e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_custom_text_backcolor" class="wli_color_picker" name="sop_product_custom_text_backcolor" value="<?php echo esc_attr( $sop_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="sop_product_custom_text_padding_top" placeholder="Top" class="small-text" name="sop_product_custom_text_padding_top" value="<?php echo esc_attr( $sop_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="sop_product_custom_text_padding_right" placeholder="Right" class="small-text" name="sop_product_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="sop_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="sop_product_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="sop_product_custom_text_padding_left" placeholder="Left" class="small-text" name="sop_product_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_sop_sticker);?>>
						<th scope="row" valign="top">
							<label for="sop_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="sop_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $sop_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="sop_sticker_custom_id" class="wsbw_upload_img_id" name="sop_sticker_custom_id" value="<?php echo absint( $sop_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="sop_sticker_category_animation_type" id="sop_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = $sop_sticker_category_animation_type;

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-sold-edit-cat" style="display:none;">
						<th scope="row"><label for="sop_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="sop_sticker_category_animation_scale" type="number" name="sop_sticker_category_animation_scale" step="any" value="<?php echo esc_attr( $sop_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="sop_sticker_category_animation_direction" id="sop_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="sop_sticker_category_animation_iteration_count" type="text" name="sop_sticker_category_animation_iteration_count" value="<?php echo esc_attr( $sop_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="sop_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="sop_sticker_category_animation_type_delay" type="number" name="sop_sticker_category_animation_type_delay" value="<?php echo esc_attr( $sop_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_sop_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as Sold in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_start_sticker_date_time" name="sop_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr( $sop_product_schedule_start_sticker_date_time ); ?>"
								/>
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="sop_product_schedule_end_sticker_date_time" name="sop_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr( $sop_product_schedule_end_sticker_date_time ); ?>"
								 />
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="sop_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt sop_product_schedule_option">
								<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule-cat" id="image_schedule_sop" value="image_schedule" <?php if($sop_product_schedule_option == 'image_schedule' || $sop_product_schedule_option == '') { echo 'checked'; } ?>/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_2" class="wli-woosticker-radio-schedule-cat" id="text_schedule_sop" value="text_schedule" <?php if($sop_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="sop_product_schedule_option" name="sop_product_schedule_option" value="<?php if($sop_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $sop_product_schedule_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_sop_schedule_product); ?>">
						<th scope="row" valign="top"><label for="sop_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="sop_schedule_sticker_image_width" name="sop_schedule_sticker_image_width" value="<?php echo esc_attr( $sop_schedule_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_sop_schedule_product); ?>">
						<th scope="row" valign="top"><label for="sop_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="sop_schedule_sticker_image_height" name="sop_schedule_sticker_image_height" value="<?php echo esc_attr( $sop_schedule_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_sop_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="sop_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="sop_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($sop_schedule_image); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="sop_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="sop_schedule_sticker_custom_id" value="<?php echo absint( $sop_schedule_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_sop"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_sop"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_sop_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="sop_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_product_schedule_custom_text" name="sop_product_schedule_custom_text" value="<?php echo esc_attr( $sop_product_schedule_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_sop_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="sop_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='sop_schedule_sticker_type'
								name="sop_schedule_sticker_type">
								<option value='ribbon'
									<?php selected( $sop_schedule_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $sop_schedule_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch fontcolor_cat_sop" style="<?php echo esc_attr($show_text_sop_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="sop_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_fontcolor" value="<?php echo ($sop_schedule_product_custom_text_fontcolor) ? esc_attr( $sop_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_sop" style="<?php echo esc_attr($show_text_sop_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="sop_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="sop_schedule_product_custom_text_backcolor" class="wli_color_picker" name="sop_schedule_product_custom_text_backcolor" value="<?php echo ($sop_schedule_product_custom_text_backcolor) ? esc_attr( $sop_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_sop_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="sop_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="sop_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_top ); ?>"/>
							<input type="number" id="sop_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="sop_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_right ); ?>"/>
							<input type="number" id="sop_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="sop_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="sop_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="sop_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $sop_product_schedule_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</table>

				<table id="wsbw_cust_products" class="wsbw_tab_content" style="display: none;">

					<tr>
						<th scope="row" valign="top"><label for="enable_cust_sticker"><?php esc_html_e( 'Enable Product Custom Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select id="enable_cust_sticker" name="enable_cust_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_cust_sticker, 'yes');?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_cust_sticker, 'no');?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="cust_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="cust_sticker_pos" name="cust_sticker_pos" class="postform">
								<option value=""><?php esc_html_e( 'Default', 'woo-stickers-by-webline' ); ?></option>
								<option value="left" <?php selected( $cust_sticker_pos, 'left');?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $cust_sticker_pos, 'right');?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row" valign="top"><label for="cust_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_top" value="<?php echo esc_attr( $cust_sticker_top ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="cust_sticker_left_right"><?php esc_html_e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_left_right" value="<?php echo esc_attr( $cust_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_rotate" value="<?php echo esc_attr( $cust_sticker_rotate ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="cust_product_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt cust_product_option">
								<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="image3" value="image" <?php if($cust_product_option == 'image' || $cust_product_option == '') { echo 'checked'; } ?>/>
								<label for="image3"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption3" class="wli-woosticker-radio" id="text3" value="text" <?php if($cust_product_option == 'text') { echo 'checked'; } ?>/>
								<label for="text3"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="cust_product_option" name="cust_product_option" value="<?php if($cust_product_option == '') { echo "image"; } else { echo esc_attr( $cust_product_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_cust_product);?>>
						<th scope="row" valign="top"><label for="cust_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_image_width" value="<?php echo esc_attr( $cust_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_cust_product);?>>
						<th scope="row" valign="top"><label for="cust_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="cust_sticker_image_height" value="<?php echo esc_attr( $cust_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_cust_product);?>>
						<th scope="row" valign="top">
							<label for="cust_product_custom_text"><?php esc_html_e( 'Product Custom Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_custom_text" name="cust_product_custom_text" value="<?php echo esc_attr( $cust_product_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_cust_product);?>>
						<th scope="row" valign="top">
							<label for="cust_sticker_type"><?php esc_html_e( 'Product Custom Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='cust_sticker_type'
								name="cust_sticker_type">
								<option value='ribbon'
									<?php selected( $cust_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $cust_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_cust_product);?>>
						<th scope="row" valign="top">
							<label for="cust_product_custom_text_fontcolor"><?php esc_html_e( 'Product Custom Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="cust_product_custom_text_fontcolor" value="<?php echo ($cust_product_custom_text_fontcolor) ? esc_attr( $cust_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_cust_product);?>>
						<th scope="row" valign="top">
							<label for="cust_product_custom_text_backcolor"><?php esc_html_e( 'Product Custom Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="cust_product_custom_text_backcolor" value="<?php echo esc_attr( $cust_product_custom_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="cust_product_custom_text_padding_top" placeholder="Top" class="small-text" name="cust_product_custom_text_padding_top" value="<?php echo esc_attr( $cust_product_custom_text_padding_top ); ?>"/>
							<input type="number" id="cust_product_custom_text_padding_right" placeholder="Right" class="small-text" name="cust_product_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_custom_text_padding_right ); ?>"/>
							<input type="number" id="cust_product_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="cust_product_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="cust_product_custom_text_padding_left" placeholder="Left" class="small-text" name="cust_product_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_cust_product);?>>
						<th scope="row" valign="top">
							<label for="cust_sticker_custom"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="cust_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $cust_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="cust_sticker_custom_id" class="wsbw_upload_img_id" name="cust_sticker_custom_id" value="<?php echo absint( $cust_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="cust_sticker_category_animation_type" id="cust_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = $cust_sticker_category_animation_type;

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-cust-edit-cat" style="display:none;">
						<th scope="row"><label for="cust_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input type="number" name="cust_sticker_category_animation_scale" step="any" value="<?php echo esc_attr( $cust_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="cust_sticker_category_animation_direction" id="cust_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="cust_sticker_category_animation_iteration_count" type="text" name="cust_sticker_category_animation_iteration_count" value="<?php echo esc_attr( $cust_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="cust_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="cust_sticker_category_animation_type_delay" type="number" name="cust_sticker_category_animation_type_delay" value="<?php echo esc_attr( $cust_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_cust_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled custom sticker display for products in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>	
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_start_sticker_date_time" name="cust_product_schedule_start_sticker_date_time"
								value="<?php echo esc_attr( $cust_product_schedule_start_sticker_date_time); ?>"
								/>
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_end_sticker_date_time" name="cust_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr( $cust_product_schedule_end_sticker_date_time); ?>"
								 />
							<p class="description"><?php esc_html_e('Set the end date and time for the sticker schedule', 'woo-stickers-by-webline'); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="cust_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt cust_product_schedule_option">
								<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule-cat" id="image_schedule_cust" value="image_schedule" <?php if($cust_product_schedule_option == 'image_schedule' || $cust_product_schedule_option == '') { echo 'checked'; } ?>/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_3" class="wli-woosticker-radio-schedule-cat" id="text_schedule_cust" value="text_schedule" <?php if($cust_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="cust_product_schedule_option" name="cust_product_schedule_option" value="<?php if($cust_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $cust_product_schedule_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_cust_schedule_product); ?>">
						<th scope="row" valign="top"><label for="cust_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="cust_schedule_sticker_image_width" name="cust_schedule_sticker_image_width" value="<?php echo esc_attr( $cust_schedule_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_cust_schedule_product); ?>">
						<th scope="row" valign="top"><label for="cust_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="cust_schedule_sticker_image_height" name="cust_schedule_sticker_image_height" value="<?php echo esc_attr( $cust_schedule_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_cust_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="cust_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="cust_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($cust_schedule_image); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="cust_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="cust_schedule_sticker_custom_id" value="<?php echo absint( $cust_schedule_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cust"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button" id="wsbw_remove_image_button_cust"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_cust_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="cust_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_product_schedule_custom_text" name="cust_product_schedule_custom_text" value="<?php echo esc_attr( $cust_product_schedule_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_cust_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="cust_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='cust_schedule_sticker_type'
								name="cust_schedule_sticker_type">
								<option value='ribbon'
									<?php selected( $cust_schedule_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $cust_schedule_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch fontcolor_cat_cust" style="<?php echo esc_attr($show_text_cust_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="cust_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_fontcolor" value="<?php echo ($cust_schedule_product_custom_text_fontcolor) ? esc_attr( $cust_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat_cust" style="<?php echo esc_attr($show_text_cust_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="cust_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="cust_schedule_product_custom_text_backcolor" class="wli_color_picker" name="cust_schedule_product_custom_text_backcolor" value="<?php echo ($cust_schedule_product_custom_text_backcolor) ? esc_attr( $cust_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_cust_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="cust_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="cust_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_top ); ?>"/>
							<input type="number" id="cust_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="cust_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_right ); ?>"/>
							<input type="number" id="cust_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="cust_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="cust_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="cust_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $cust_product_schedule_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

				</table>

				<table id="wsbw_category_sticker" class="wsbw_tab_content" style="display: none;">

					<tr>
						<th scope="row" valign="top">
							<label for="enable_category_sticker"><?php esc_html_e( 'Enable Category Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="enable_category_sticker" name="enable_category_sticker" class="postform">
								<option value=""><?php esc_html_e( 'Please select', 'woo-stickers-by-webline' ); ?></option>
								<option value="yes" <?php selected( $enable_category_sticker, 'yes');?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?></option>
								<option value="no" <?php selected( $enable_category_sticker, 'no');?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Enable sticker on this category', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="category_sticker_pos"><?php esc_html_e( 'Sticker Position:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id="category_sticker_pos" name="category_sticker_pos" class="postform">
								<option value="left" <?php selected( $category_sticker_pos, 'left');?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' ); ?></option>
								<option value="right" <?php selected( $category_sticker_pos, 'right');?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' ); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label for="category_sticker_top"><?php esc_html_e( 'Sticker Position Top (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_top" value="<?php echo esc_attr( $category_sticker_top ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>
										
					<tr>
						<th scope="row" valign="top"><label for="category_sticker_left_right"><?php esc_html_e( 'Sticker Position Left / Right (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_left_right" value="<?php echo esc_attr( $category_sticker_left_right ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_rotate"><?php esc_html_e( 'Sticker Rotate:', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_sticker_rotate" value="<?php echo esc_attr( $category_sticker_sticker_rotate ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="category_sticker_option"><?php esc_html_e( 'Sticker Option:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt category_sticker_option">
								<input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="image4" value="image" <?php if($category_sticker_option == 'image' || $category_sticker_option == '') { echo 'checked'; } ?>/>
								<label for="image4"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption4" class="wli-woosticker-radio" id="text4" value="text" <?php if($category_sticker_option == 'text') { echo 'checked'; } ?>/>
								<label for="text4"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_option" id="category_sticker_option" name="category_sticker_option" value="<?php echo $category_sticker_option == '' ? "image" : esc_attr( $category_sticker_option );?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_sticker);?>>
						<th scope="row" valign="top"><label for="category_sticker_image_width"><?php esc_html_e( 'Sticker Image Width (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_image_width" value="<?php echo esc_attr( $category_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_sticker);?>>
						<th scope="row" valign="top"><label for="category_sticker_image_height"><?php esc_html_e( 'Sticker Image Height (px):', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" name="category_sticker_image_height" value="<?php echo esc_attr( $category_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sticker);?>>
						<th scope="row" valign="top">
							<label for="category_sticker_text"><?php esc_html_e( 'Sticker Text:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_sticker_text" name="category_sticker_text" value="<?php echo esc_attr( $category_sticker_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sticker);?>>
						<th scope="row" valign="top">
							<label for="category_sticker_type"><?php esc_html_e( 'Sticker Type:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='category_sticker_type'
								name="category_sticker_type">
								<option value='ribbon'
									<?php selected( $category_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $category_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sticker);?>>
						<th scope="row" valign="top">
							<label for="category_sticker_text_fontcolor"><?php esc_html_e( 'Sticker Text Font Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_sticker_text_fontcolor" class="wli_color_picker" name="category_sticker_text_fontcolor" value="<?php echo ($category_sticker_text_fontcolor) ? esc_attr( $category_sticker_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_sticker);?>>
						<th scope="row" valign="top">
							<label for="category_sticker_text_backcolor"><?php esc_html_e( 'Sticker Text Back Color:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_sticker_text_backcolor" class="wli_color_picker" name="category_sticker_text_backcolor" value="<?php echo esc_attr( $category_sticker_text_backcolor ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext" <?php echo esc_attr( $show_text_np_product);?>>
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Sticker Padding (px):', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="category_sticker_text_padding_top" placeholder="Top" class="small-text" name="category_sticker_text_padding_top" value="<?php echo esc_attr( $category_sticker_text_padding_top ); ?>"/>
							<input type="number" id="category_sticker_text_padding_right" placeholder="Right" class="small-text" name="category_sticker_text_padding_right" value="<?php echo esc_attr( $category_sticker_text_padding_right ); ?>"/>
							<input type="number" id="category_sticker_text_padding_bottom" placeholder="Bottom" class="small-text" name="category_sticker_text_padding_bottom" value="<?php echo esc_attr( $category_sticker_text_padding_bottom ); ?>"/>
							<input type="number" id="category_sticker_text_padding_left" placeholder="Left" class="small-text" name="category_sticker_text_padding_left" value="<?php echo esc_attr( $category_sticker_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage" <?php echo esc_attr( $show_image_sticker);?>>
						<th scope="row" valign="top">
							<label for="category_sticker_image"><?php esc_html_e( 'Add your custom sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="category_sticker_image" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $category_image ); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="category_sticker_image_id" class="wsbw_upload_img_id" name="category_sticker_image_id" value="<?php echo absint( $category_sticker_image_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
							<p class="description"><?php esc_html_e( 'Upload your sticker image which you want to display on this category.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_type"><?php esc_html_e( 'Sticker Animation Effects:', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-top:1px solid;">
							<select name="category_sticker_sticker_category_animation_type" id="category_sticker_sticker_category_animation_type">
								<?php
								$animation_options = array(
									'none'      => __( 'None', 'woo-stickers-by-webline' ),
									'spin'      => __( 'Spin', 'woo-stickers-by-webline' ),
									'swing'     => __( 'Swing', 'woo-stickers-by-webline' ),
									'zoominout' => __( 'Zoom In / Out', 'woo-stickers-by-webline' ),
									'leftright' => __( 'Left-Right', 'woo-stickers-by-webline' ),
									'updown'    => __( 'Up-Down', 'woo-stickers-by-webline' )
								);

								$saved_value = $category_sticker_sticker_category_animation_type;

								foreach ($animation_options as $value => $label) {
									$selected = ($saved_value == $value) ? 'selected' : '';
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation type for the sticker..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr id="zoominout-options-category-edit-cat" style="display:none;">
						<th scope="row"><label for="category_sticker_sticker_category_animation_scale"><?php esc_html_e( 'Sticker Animation Scale', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="category_sticker_sticker_category_animation_scale" type="number" name="category_sticker_sticker_category_animation_scale" step="any" value="<?php echo esc_attr( $category_sticker_sticker_category_animation_scale ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation scale.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_direction"><?php esc_html_e( 'Sticker Animation Direction', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<select name="category_sticker_sticker_category_animation_direction" id="category_sticker_sticker_category_animation_direction">
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Select the animation direction..', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_iteration_count"><?php esc_html_e( 'Sticker Animation Iteration Count:', 'woo-stickers-by-webline' ); ?></label></th>
						<td><input id="category_sticker_sticker_category_animation_iteration_count" type="text" name="category_sticker_sticker_category_animation_iteration_count" value="<?php echo esc_attr( $category_sticker_sticker_category_animation_iteration_count ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation Iteration Count.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="category_sticker_sticker_category_animation_type_delay"><?php esc_html_e( 'Sticker Animation Delay', 'woo-stickers-by-webline' ); ?></label></th>
						<td style="border-bottom:1px solid;"><input id="category_sticker_sticker_category_animation_type_delay" type="number" name="category_sticker_sticker_category_animation_type_delay" value="<?php echo esc_attr( $category_sticker_sticker_category_animation_type_delay ); ?>" class="small-text"><p class="description"><?php esc_html_e( 'Specify animation delay.', 'woo-stickers-by-webline' ); ?></p></td>
					</tr>

					<tr>
						<th scope="row"><label for="enable_category_product_schedule_sticker_category"><?php esc_html_e( 'Enable Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label></th>
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
									echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
								}
								?>
							</select>
							<p class="description"><?php esc_html_e( 'Enable or disable scheduled sticker display for Categories in WooCommerce.', 'woo-stickers-by-webline' ); ?></p>			
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Product Sticker:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_start_sticker_date_time" name="category_product_schedule_start_sticker_date_time" 
								value="<?php echo esc_attr( $category_product_schedule_start_sticker_date_time ); ?>" 
								/>
							<p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label><?php esc_html_e( 'Schedule Sticker Date/Time End', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="datetime-local" class="custom_date_pkr" id="category_product_schedule_end_sticker_date_time" name="category_product_schedule_end_sticker_date_time" 
								value="<?php echo esc_attr( $category_product_schedule_end_sticker_date_time ); ?>" 
								/>
							<p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top">
							<label for="category_product_schedule_option"><?php esc_html_e( 'Schedule Sticker Options:', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div class="woo_opt category_product_schedule_option">
								<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule-cat" id="image_schedule_cat" value="image_schedule" <?php if($category_product_schedule_option == 'image_schedule' || $category_product_schedule_option == '') { echo 'checked'; } ?>/>
								<label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
								<input type="radio" name="stickeroption_sch_4" class="wli-woosticker-radio-schedule-cat" id="text_schedule_cat" value="text_schedule" <?php if($category_product_schedule_option == 'text_schedule') { echo 'checked'; } ?>/>
								<label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
								<input type="hidden" class="wli_product_schedule_option" id="category_product_schedule_option" name="category_product_schedule_option" value="<?php if($category_product_schedule_option == '') { echo "image_schedule"; } else { echo esc_attr( $category_product_schedule_option ); } ?>"/>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_category_schedule_product); ?>">
						<th scope="row" valign="top"><label for="category_schedule_sticker_image_width"><?php esc_html_e( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="category_schedule_sticker_image_width" name="category_schedule_sticker_image_width" value="<?php echo esc_attr( $category_schedule_sticker_image_width ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_category_schedule_product); ?>">
						<th scope="row" valign="top"><label for="category_schedule_sticker_image_height"><?php esc_html_e( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ); ?></label></th>
						<td>
							<input type="number" id="category_schedule_sticker_image_height" name="category_schedule_sticker_image_height" value="<?php echo esc_attr( $category_schedule_sticker_image_height ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
						</td>
					</tr>

					<tr class="custom_option custom_optimage_sch" style="<?php echo esc_attr($show_image_category_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="category_schedule_sticker_custom"><?php esc_html_e( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<div id="category_schedule_sticker_custom" class="wsbw_upload_img_preview" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url($category_schedule_image); ?>" width="60px" height="60px" /></div>
							<div style="line-height: 60px;">
								<input type="hidden" id="category_schedule_sticker_custom_id" class="wsbw_upload_img_id" name="category_schedule_sticker_custom_id" value="<?php echo absint( $category_schedule_sticker_custom_id ); ?>" />
								<button type="button" class="wsbw_upload_image_button button" id="wsbw_upload_image_button_cat"><?php esc_html_e( 'Upload/Add image', 'woo-stickers-by-webline' ); ?></button>
								<button type="button" class="wsbw_remove_image_button button"id="wsbw_remove_image_button_cat"><?php esc_html_e( 'Remove image', 'woo-stickers-by-webline' ); ?></button>
							</div>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_category_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="category_product_schedule_custom_text"><?php esc_html_e( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_product_schedule_custom_text" name="category_product_schedule_custom_text" value="<?php echo esc_attr( $category_product_schedule_custom_text ); ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_category_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="category_schedule_sticker_type"><?php esc_html_e( 'Schedule Sticker Type', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<select id='category_schedule_sticker_type'
								name="category_schedule_sticker_type">
								<option value='ribbon'
									<?php selected( $category_schedule_sticker_type, 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
								<option value='round'
									<?php selected( $category_schedule_sticker_type, 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
							</select>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch fontcolor_cat" style="<?php echo esc_attr($show_text_category_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="category_schedule_product_custom_text_fontcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_schedule_product_custom_text_fontcolor" class="wli_color_picker" name="category_schedule_product_custom_text_fontcolor" value="<?php echo ($category_schedule_product_custom_text_fontcolor) ? esc_attr( $category_schedule_product_custom_text_fontcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch backcolor_cat" style="<?php echo esc_attr($show_text_category_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for="category_schedule_product_custom_text_backcolor"><?php esc_html_e( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="text" id="category_schedule_product_custom_text_backcolor" class="wli_color_picker" name="category_schedule_product_custom_text_backcolor" value="<?php echo ($category_schedule_product_custom_text_backcolor) ? esc_attr( $category_schedule_product_custom_text_backcolor ) : '#ffffff'; ?>"/>
						</td>
					</tr>

					<tr class="custom_option custom_opttext_sch" style="<?php echo esc_attr($show_text_category_schedule_product); ?>">
						<th scope="row" valign="top">
							<label for=""><?php esc_html_e( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ); ?></label>
						</th>
						<td>
							<input type="number" id="category_product_schedule_custom_text_padding_top" placeholder="Top" class="small-text" name="category_product_schedule_custom_text_padding_top" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_top ); ?>"/>
							<input type="number" id="category_product_schedule_custom_text_padding_right" placeholder="Right" class="small-text" name="category_product_schedule_custom_text_padding_right" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_right ); ?>"/>
							<input type="number" id="category_product_schedule_custom_text_padding_bottom" placeholder="Bottom" class="small-text" name="category_product_schedule_custom_text_padding_bottom" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_bottom ); ?>"/>
							<input type="number" id="category_product_schedule_custom_text_padding_left" placeholder="Left" class="small-text" name="category_product_schedule_custom_text_padding_left" value="<?php echo esc_attr( $category_product_schedule_custom_text_padding_left ); ?>"/>
							<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
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

		// Nonce check.
		$nonce = isset( $_POST['wli_stickers_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wli_stickers_nonce'] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wli_stickers_save_category' ) ) {
			return;
		}

		// NEW PRODUCT sticker fields
		if ( isset( $_POST['enable_np_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_np_sticker', sanitize_text_field( wp_unslash( $_POST['enable_np_sticker'] ) ) );
		}
		if ( isset( $_POST['np_no_of_days'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_no_of_days', absint( wp_unslash( $_POST['np_no_of_days'] ) ) );
		}
		if ( isset( $_POST['np_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_pos', sanitize_text_field( wp_unslash( $_POST['np_sticker_pos'] ) ) );
		}
		if ( isset( $_POST['np_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_top', sanitize_text_field( wp_unslash( $_POST['np_sticker_top'] ) ) );
		}
		if ( isset( $_POST['np_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_left_right', sanitize_text_field( wp_unslash( $_POST['np_sticker_left_right'] ) ) );
		}
		if ( isset( $_POST['np_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_rotate', sanitize_text_field( wp_unslash( $_POST['np_sticker_rotate'] ) ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_type', sanitize_text_field( wp_unslash( $_POST['np_sticker_category_animation_type'] ) ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_direction', sanitize_text_field( wp_unslash( $_POST['np_sticker_category_animation_direction'] ) ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_scale', sanitize_text_field( wp_unslash( $_POST['np_sticker_category_animation_scale'] ) ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_iteration_count', sanitize_text_field( wp_unslash( $_POST['np_sticker_category_animation_iteration_count'] ) ) );
		}
		if ( isset( $_POST['np_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_category_animation_type_delay', sanitize_text_field( wp_unslash( $_POST['np_sticker_category_animation_type_delay'] ) ) );
		}

		if ( isset( $_POST['enable_np_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_np_product_schedule_sticker_category', sanitize_text_field( wp_unslash( $_POST['enable_np_product_schedule_sticker_category'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_start_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_start_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_end_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_end_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_option', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_option'] ) ) );
		}
		if ( isset( $_POST['np_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_image_width', sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['np_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_image_height', sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['np_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_custom_id', sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_custom_id'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text'] ) ) );
		}
		if ( isset( $_POST['np_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_sticker_type', sanitize_text_field( wp_unslash( $_POST['np_schedule_sticker_type'] ) ) );
		}
		if ( isset( $_POST['np_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_product_custom_text_fontcolor', sanitize_text_field( wp_unslash( $_POST['np_schedule_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['np_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_schedule_product_custom_text_backcolor', sanitize_text_field( wp_unslash( $_POST['np_schedule_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['np_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_schedule_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['np_product_schedule_custom_text_padding_left'] ) ) );
		}

		if ( isset( $_POST['np_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_option', sanitize_key( wp_unslash( $_POST['np_product_option'] ) ) );
		}
		if ( isset( $_POST['np_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_image_width', sanitize_key( wp_unslash( $_POST['np_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['np_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_image_height', sanitize_key( wp_unslash( $_POST['np_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['np_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text', sanitize_text_field( wp_unslash( $_POST['np_product_custom_text'] ) ) );
		}
		if ( isset( $_POST['np_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_type', sanitize_text_field( wp_unslash( $_POST['np_sticker_type'] ) ) );
		}
		if ( isset( $_POST['np_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_fontcolor', sanitize_hex_color( wp_unslash( $_POST['np_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['np_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_backcolor', sanitize_hex_color( wp_unslash( $_POST['np_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['np_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_product_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['np_product_custom_text_padding_left'] ) ) );
		}
		if ( isset( $_POST['np_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'np_sticker_custom_id', absint( wp_unslash( $_POST['np_sticker_custom_id'] ) ) );
		}

		// ON SALE sticker fields
		if ( isset( $_POST['enable_pos_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_pos_sticker', sanitize_text_field( wp_unslash( $_POST['enable_pos_sticker'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_pos', sanitize_text_field( wp_unslash( $_POST['pos_sticker_pos'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_left_right', sanitize_text_field( wp_unslash( $_POST['pos_sticker_left_right'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_top', sanitize_text_field( wp_unslash( $_POST['pos_sticker_top'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_rotate', sanitize_text_field( wp_unslash( $_POST['pos_sticker_rotate'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_type', sanitize_text_field( wp_unslash( $_POST['pos_sticker_category_animation_type'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_direction', sanitize_text_field( wp_unslash( $_POST['pos_sticker_category_animation_direction'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_scale', sanitize_text_field( wp_unslash( $_POST['pos_sticker_category_animation_scale'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_iteration_count', sanitize_text_field( wp_unslash( $_POST['pos_sticker_category_animation_iteration_count'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_category_animation_type_delay', sanitize_text_field( wp_unslash( $_POST['pos_sticker_category_animation_type_delay'] ) ) );
		}

		if ( isset( $_POST['enable_pos_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_pos_product_schedule_sticker_category', sanitize_text_field( wp_unslash( $_POST['enable_pos_product_schedule_sticker_category'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_start_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_start_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_end_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_end_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_option', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_option'] ) ) );
		}
		if ( isset( $_POST['pos_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_image_width', sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['pos_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_image_height', sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['pos_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_custom_id', sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_custom_id'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text'] ) ) );
		}
		if ( isset( $_POST['pos_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_sticker_type', sanitize_text_field( wp_unslash( $_POST['pos_schedule_sticker_type'] ) ) );
		}
		if ( isset( $_POST['pos_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_product_custom_text_fontcolor', sanitize_text_field( wp_unslash( $_POST['pos_schedule_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['pos_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_schedule_product_custom_text_backcolor', sanitize_text_field( wp_unslash( $_POST['pos_schedule_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['pos_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_schedule_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['pos_product_schedule_custom_text_padding_left'] ) ) );
		}

		if ( isset( $_POST['pos_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_option', sanitize_key( wp_unslash( $_POST['pos_product_option'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_image_width', sanitize_text_field( wp_unslash( $_POST['pos_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_image_height', sanitize_text_field( wp_unslash( $_POST['pos_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['pos_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text', sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_type', sanitize_text_field( wp_unslash( $_POST['pos_sticker_type'] ) ) );
		}
		if ( isset( $_POST['pos_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_fontcolor', sanitize_hex_color( wp_unslash( $_POST['pos_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['pos_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_backcolor', sanitize_hex_color( wp_unslash( $_POST['pos_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['pos_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_product_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['pos_product_custom_text_padding_left'] ) ) );
		}
		if ( isset( $_POST['pos_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'pos_sticker_custom_id', absint( wp_unslash( $_POST['pos_sticker_custom_id'] ) ) );
		}

		// SOLDOUT sticker fields
		if ( isset( $_POST['enable_sop_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_sop_sticker', sanitize_text_field( wp_unslash( $_POST['enable_sop_sticker'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_pos', sanitize_text_field( wp_unslash( $_POST['sop_sticker_pos'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_left_right', sanitize_text_field( wp_unslash( $_POST['sop_sticker_left_right'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_top', sanitize_text_field( wp_unslash( $_POST['sop_sticker_top'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_rotate', sanitize_text_field( wp_unslash( $_POST['sop_sticker_rotate'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_type', sanitize_text_field( wp_unslash( $_POST['sop_sticker_category_animation_type'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_direction', sanitize_text_field( wp_unslash( $_POST['sop_sticker_category_animation_direction'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_scale', sanitize_text_field( wp_unslash( $_POST['sop_sticker_category_animation_scale'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_iteration_count', sanitize_text_field( wp_unslash( $_POST['sop_sticker_category_animation_iteration_count'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_category_animation_type_delay', sanitize_text_field( wp_unslash( $_POST['sop_sticker_category_animation_type_delay'] ) ) );
		}

		if ( isset( $_POST['enable_sop_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_sop_product_schedule_sticker_category', sanitize_text_field( wp_unslash( $_POST['enable_sop_product_schedule_sticker_category'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_start_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_start_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_end_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_end_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_option', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_option'] ) ) );
		}
		if ( isset( $_POST['sop_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_image_width', sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['sop_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_image_height', sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['sop_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_custom_id', sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_custom_id'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text'] ) ) );
		}
		if ( isset( $_POST['sop_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_sticker_type', sanitize_text_field( wp_unslash( $_POST['sop_schedule_sticker_type'] ) ) );
		}
		if ( isset( $_POST['sop_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_product_custom_text_fontcolor', sanitize_text_field( wp_unslash( $_POST['sop_schedule_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['sop_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_schedule_product_custom_text_backcolor', sanitize_text_field( wp_unslash( $_POST['sop_schedule_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['sop_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_schedule_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['sop_product_schedule_custom_text_padding_left'] ) ) );
		}

		if ( isset( $_POST['sop_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_option', sanitize_key( wp_unslash( $_POST['sop_product_option'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_image_width', sanitize_text_field( wp_unslash( $_POST['sop_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_image_height', sanitize_text_field( wp_unslash( $_POST['sop_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['sop_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text', sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_type', sanitize_text_field( wp_unslash( $_POST['sop_sticker_type'] ) ) );
		}
		if ( isset( $_POST['sop_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_fontcolor', sanitize_hex_color( wp_unslash( $_POST['sop_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['sop_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_backcolor', sanitize_hex_color( wp_unslash( $_POST['sop_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['sop_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_product_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['sop_product_custom_text_padding_left'] ) ) );
		}
		if ( isset( $_POST['sop_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'sop_sticker_custom_id', absint( wp_unslash( $_POST['sop_sticker_custom_id'] ) ) );
		}

		// CUSTOM sticker fields
		if ( isset( $_POST['enable_cust_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_cust_sticker', sanitize_text_field( wp_unslash( $_POST['enable_cust_sticker'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_pos', sanitize_text_field( wp_unslash( $_POST['cust_sticker_pos'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_left_right', sanitize_text_field( wp_unslash( $_POST['cust_sticker_left_right'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_top', sanitize_text_field( wp_unslash( $_POST['cust_sticker_top'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_rotate', sanitize_text_field( wp_unslash( $_POST['cust_sticker_rotate'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_type', sanitize_text_field( wp_unslash( $_POST['cust_sticker_category_animation_type'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_direction', sanitize_text_field( wp_unslash( $_POST['cust_sticker_category_animation_direction'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_scale', sanitize_text_field( wp_unslash( $_POST['cust_sticker_category_animation_scale'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_iteration_count', sanitize_text_field( wp_unslash( $_POST['cust_sticker_category_animation_iteration_count'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_category_animation_type_delay', sanitize_text_field( wp_unslash( $_POST['cust_sticker_category_animation_type_delay'] ) ) );
		}

		if ( isset( $_POST['enable_cust_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_cust_product_schedule_sticker_category', sanitize_text_field( wp_unslash( $_POST['enable_cust_product_schedule_sticker_category'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_start_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_start_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_end_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_end_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_option', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_option'] ) ) );
		}
		if ( isset( $_POST['cust_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_image_width', sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['cust_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_image_height', sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['cust_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_custom_id', sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_custom_id'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text'] ) ) );
		}
		if ( isset( $_POST['cust_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_sticker_type', sanitize_text_field( wp_unslash( $_POST['cust_schedule_sticker_type'] ) ) );
		}
		if ( isset( $_POST['cust_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_product_custom_text_fontcolor', sanitize_text_field( wp_unslash( $_POST['cust_schedule_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['cust_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_schedule_product_custom_text_backcolor', sanitize_text_field( wp_unslash( $_POST['cust_schedule_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['cust_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_schedule_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['cust_product_schedule_custom_text_padding_left'] ) ) );
		}

		if ( isset( $_POST['cust_product_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_option', sanitize_key( wp_unslash( $_POST['cust_product_option'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_image_width', sanitize_key( wp_unslash( $_POST['cust_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_image_height', sanitize_key( wp_unslash( $_POST['cust_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['cust_product_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text', sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_type', sanitize_text_field( wp_unslash( $_POST['cust_sticker_type'] ) ) );
		}
		if ( isset( $_POST['cust_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_fontcolor', sanitize_hex_color( wp_unslash( $_POST['cust_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['cust_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_backcolor', sanitize_hex_color( wp_unslash( $_POST['cust_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['cust_product_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_product_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['cust_product_custom_text_padding_left'] ) ) );
		}
		if ( isset( $_POST['cust_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'cust_sticker_custom_id', absint( wp_unslash( $_POST['cust_sticker_custom_id'] ) ) );
		}

		// CATEGORY sticker fields
		if ( isset( $_POST['enable_category_sticker'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_category_sticker', sanitize_text_field( wp_unslash( $_POST['enable_category_sticker'] ) ) );
		}
		if ( isset( $_POST['category_sticker_pos'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_pos', sanitize_text_field( wp_unslash( $_POST['category_sticker_pos'] ) ) );
		}
		if ( isset( $_POST['category_sticker_left_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_left_right', sanitize_text_field( wp_unslash( $_POST['category_sticker_left_right'] ) ) );
		}
		if ( isset( $_POST['category_sticker_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_top', sanitize_text_field( wp_unslash( $_POST['category_sticker_top'] ) ) );
		}
		if ( isset( $_POST['category_sticker_sticker_rotate'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_rotate', sanitize_text_field( wp_unslash( $_POST['category_sticker_sticker_rotate'] ) ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_type', sanitize_text_field( wp_unslash( $_POST['category_sticker_sticker_category_animation_type'] ) ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_direction'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_direction', sanitize_text_field( wp_unslash( $_POST['category_sticker_sticker_category_animation_direction'] ) ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_scale'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_scale', sanitize_text_field( wp_unslash( $_POST['category_sticker_sticker_category_animation_scale'] ) ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_iteration_count'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_iteration_count', sanitize_text_field( wp_unslash( $_POST['category_sticker_sticker_category_animation_iteration_count'] ) ) );
		}
		if ( isset( $_POST['category_sticker_sticker_category_animation_type_delay'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_sticker_category_animation_type_delay', sanitize_text_field( wp_unslash( $_POST['category_sticker_sticker_category_animation_type_delay'] ) ) );
		}

		if ( isset( $_POST['enable_category_product_schedule_sticker_category'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'enable_category_product_schedule_sticker_category', sanitize_text_field( wp_unslash( $_POST['enable_category_product_schedule_sticker_category'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_start_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_start_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_start_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_end_sticker_date_time'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_end_sticker_date_time', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_end_sticker_date_time'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_option', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_option'] ) ) );
		}
		if ( isset( $_POST['category_schedule_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_image_width', sanitize_text_field( wp_unslash( $_POST['category_schedule_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['category_schedule_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_image_height', sanitize_text_field( wp_unslash( $_POST['category_schedule_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['category_schedule_sticker_custom_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_custom_id', sanitize_text_field( wp_unslash( $_POST['category_schedule_sticker_custom_id'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_custom_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_custom_text'] ) ) );
		}
		if ( isset( $_POST['category_schedule_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_sticker_type', sanitize_text_field( wp_unslash( $_POST['category_schedule_sticker_type'] ) ) );
		}
		if ( isset( $_POST['category_schedule_product_custom_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_product_custom_text_fontcolor', sanitize_text_field( wp_unslash( $_POST['category_schedule_product_custom_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['category_schedule_product_custom_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_schedule_product_custom_text_backcolor', sanitize_text_field( wp_unslash( $_POST['category_schedule_product_custom_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_custom_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_top', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_custom_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_custom_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_right', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_custom_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_custom_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_custom_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['category_product_schedule_custom_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_product_schedule_custom_text_padding_left', sanitize_text_field( wp_unslash( $_POST['category_product_schedule_custom_text_padding_left'] ) ) );
		}

		if ( isset( $_POST['category_sticker_option'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_option', sanitize_key( wp_unslash( $_POST['category_sticker_option'] ) ) );
		}
		if ( isset( $_POST['category_sticker_image_width'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_image_width', sanitize_key( wp_unslash( $_POST['category_sticker_image_width'] ) ) );
		}
		if ( isset( $_POST['category_sticker_image_height'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_image_height', sanitize_key( wp_unslash( $_POST['category_sticker_image_height'] ) ) );
		}
		if ( isset( $_POST['category_sticker_text'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text', sanitize_text_field( wp_unslash( $_POST['category_sticker_text'] ) ) );
		}
		if ( isset( $_POST['category_sticker_type'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_type', sanitize_text_field( wp_unslash( $_POST['category_sticker_type'] ) ) );
		}
		if ( isset( $_POST['category_sticker_text_fontcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_fontcolor', sanitize_hex_color( wp_unslash( $_POST['category_sticker_text_fontcolor'] ) ) );
		}
		if ( isset( $_POST['category_sticker_text_backcolor'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_backcolor', sanitize_hex_color( wp_unslash( $_POST['category_sticker_text_backcolor'] ) ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_top'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_top', sanitize_text_field( wp_unslash( $_POST['category_sticker_text_padding_top'] ) ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_right'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_right', sanitize_text_field( wp_unslash( $_POST['category_sticker_text_padding_right'] ) ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_bottom'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_bottom', sanitize_text_field( wp_unslash( $_POST['category_sticker_text_padding_bottom'] ) ) );
		}
		if ( isset( $_POST['category_sticker_text_padding_left'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_text_padding_left', sanitize_text_field( wp_unslash( $_POST['category_sticker_text_padding_left'] ) ) );
		}
		if ( isset( $_POST['category_sticker_image_id'] ) && 'product_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'category_sticker_image_id', absint( wp_unslash( $_POST['category_sticker_image_id'] ) ) );
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {
		global $typenow;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of admin context.
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of admin context.
		$tab  = isset( $_GET['tab'] )  ? sanitize_key( wp_unslash( $_GET['tab'] ) )  : '';

		$is_product_screen = ( 'product' === (string) $typenow )
			&& in_array( $hook, array( 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ), true );

		$is_plugin_screen = ( 'settings_page_wli-stickers' === $hook ) || ( 'wli-stickers' === $page );
		$is_plugin_tab    = ( 'wli-stickers' === $page && 'new_product_settings' === $tab );

		if ( $is_product_screen || $is_plugin_screen || $is_plugin_tab ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_style(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'css/woo-stickers-by-webline-admin.css',
				array(),
				$this->version,
				'all'
			);
		}

		// Notices CSS can load globally if you prefer.
		wp_enqueue_style(
			'woo-stickers-by-webline-admin-notices',
			plugin_dir_url( __FILE__ ) . 'css/wosbw-admin-notices.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		global $typenow;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of admin context.
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of admin context.
		$tab  = isset( $_GET['tab'] )  ? sanitize_key( wp_unslash( $_GET['tab'] ) )  : '';

		$is_product_screen = ( 'product' === (string) $typenow )
			&& in_array( $hook, array( 'post.php', 'post-new.php', 'edit-tags.php', 'term.php' ), true );

		$is_plugin_screen = ( 'settings_page_wli-stickers' === $hook ) || ( 'wli-stickers' === $page );
		$is_plugin_tab    = ( 'wli-stickers' === $page && 'new_product_settings' === $tab );

		if ( $is_product_screen || $is_plugin_screen || $is_plugin_tab ) {
			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'js/woo-stickers-by-webline-admin.js',
				array( 'jquery' ),
				$this->version,
				false
			);

			wp_localize_script(
				$this->plugin_name,
				'scriptsData',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'choose_image_title' => __( 'Choose an image', 'woo-stickers-by-webline' ),
					'use_image_btn_text' => __( 'Use image', 'woo-stickers-by-webline' ),
					'placeholder_img_src'=> wc_placeholder_img_src(),
					// Optional: add a nonce for any AJAX you handle.
					//'nonce'              => wp_create_nonce( 'wosbw_admin' ),
				)
			);

			add_thickbox();
		}
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
		$this->plugin_settings_tabs[ $this->general_settings_key ] = __( 'General', 'woo-stickers-by-webline' );

		// Register with sanitization.
		register_setting(
			$this->general_settings_key,                         // option_group
			$this->general_settings_key,                         // option_name (array of fields)
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_general_settings' ),
				'default'           => array(
					'enable_sticker'        => 'no',
					'enable_sticker_list'   => 'no',
					'enable_sticker_detail' => 'no',
					'sticker_custom_css'    => '',
				),
			)
		);

		add_settings_section(
			'section_general',
			__( 'General Plugin Settings', 'woo-stickers-by-webline' ),
			array( $this, 'section_general_desc' ),
			$this->general_settings_key
		);

		add_settings_field( 'enable_sticker', __( 'Enable Product Sticker:', 'woo-stickers-by-webline' ), array( $this, 'enable_sticker' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'enable_sticker_list', __( 'Enable Sticker On Product Listing Page:', 'woo-stickers-by-webline' ), array( $this, 'enable_sticker_list' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'enable_sticker_detail', __( 'Enable Sticker On Product Details Page:', 'woo-stickers-by-webline' ), array( $this, 'enable_sticker_detail' ), $this->general_settings_key, 'section_general' );
		add_settings_field( 'sticker_custom_css', __( 'Custom CSS:', 'woo-stickers-by-webline' ), array( $this, 'sticker_custom_css' ), $this->general_settings_key, 'section_general' );
	}

	/**
	 * Sanitize the "General" settings array.
	 *
	 * @param array|string $input Raw input from Settings API (usually array).
	 * @return array Sanitized settings.
	 */
	public function sanitize_general_settings( $input ) {
		$input      = is_array( $input ) ? $input : array();
		$sanitized  = array();

		// Helper to coerce checkbox-like values to 'yes' or 'no'.
		$to_yes_no = static function( $val ) {
			if ( is_array( $val ) ) {
				return 'no';
			}
			$val = strtolower( trim( (string) $val ) );
			return in_array( $val, array( '1', 'yes', 'on', 'true' ), true ) ? 'yes' : 'no';
		};

		$sanitized['enable_sticker']        = isset( $input['enable_sticker'] )        ? $to_yes_no( wp_unslash( $input['enable_sticker'] ) )        : 'no';
		$sanitized['enable_sticker_list']   = isset( $input['enable_sticker_list'] )   ? $to_yes_no( wp_unslash( $input['enable_sticker_list'] ) )   : 'no';
		$sanitized['enable_sticker_detail'] = isset( $input['enable_sticker_detail'] ) ? $to_yes_no( wp_unslash( $input['enable_sticker_detail'] ) ) : 'no';

		// Multi-line text; strip tags, keep newlines and CSS punctuation.
		$sanitized['sticker_custom_css']    = isset( $input['sticker_custom_css'] )
			? sanitize_textarea_field( wp_unslash( $input['sticker_custom_css'] ) )
			: '';

		return $sanitized;
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
		$this->plugin_settings_tabs[ $this->new_product_settings_key ] = __( 'New Products', 'woo-stickers-by-webline' );

		register_setting(
			$this->new_product_settings_key,
			$this->new_product_settings_key,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_new_product_settings' ),
				'default'           => array(
					'enable_new_product_sticker'               => 'no',
					'new_product_sticker_days'                 => 7,
					'new_product_position'                     => 'left',
					'new_product_sticker_left_right'           => 0,
					'new_product_sticker_top'                  => 0,
					'new_product_sticker_rotate'               => 0,
					'new_product_sticker_animation'            => '',
					'enable_new_product_schedule_sticker'      => 'no',
					'new_product_schedule_sticker'             => '',
					'new_product_schedule_sticker_image_width' => '',
					'new_product_schedule_sticker_image_height'=> '',
					'new_product_schedule_custom_sticker'      => '',
					'new_product_schedule_custom_text'         => '',
					'enable_new_schedule_product_style'        => 'no',
					'new_product_schedule_custom_text_fontcolor'=> '',
					'new_product_schedule_custom_text_backcolor'=> '',
					'new_product_schedule_text_padding_top' => '',
					'new_product_schedule_text_padding_right' => '',
					'new_product_schedule_text_padding_bottom' => '',
					'new_product_schedule_text_padding_left' => '',
					'new_product_option'                       => 'image',
					'new_product_sticker_image_width'          => '',
					'new_product_sticker_image_height'         => '',
					'new_product_custom_text'                  => '',
					'enable_new_product_style'                 => '',
					'new_product_custom_text_fontcolor'        => '',
					'new_product_custom_text_backcolor'        => '',
					'new_product_custom_sticker'               => '',
					'new_product_text_padding_top'		   => '',
					'new_product_text_padding_right'		   => '',
					'new_product_text_padding_bottom'		   => '',
					'new_product_text_padding_left'		   => '',
					'stickeroption_sch' => '',
					'new_product_schedule_start_sticker_date_time' => '',
					'new_product_schedule_end_sticker_date_time' => '',

					'new_product_sticker_animation_iteration_count' => "",
					'new_product_sticker_animation_delay' => "",

					'new_product_sticker_animation_type'  => '',
					'new_product_sticker_animation_scale' => '',
					'new_product_sticker_animation_direction' => '',
				),
			)
		);

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

		add_settings_field('divider_new_product_animation_start','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
				},$this->new_product_settings_key,
				'section_new_product'
		);

		add_settings_field ( 'new_product_sticker_animation_type', __( 'Sticker Animation:', 'woo-stickers-by-webline' ), array (
    		&$this,
    		'new_product_sticker_animation_type'
        	), $this->new_product_settings_key, 'section_new_product' );


		add_settings_field(
			'new_product_sticker_animation_scale',
			__( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
			array( &$this, 'new_product_sticker_animation_scale' ),
			$this->new_product_settings_key,
			'section_new_product',
			array(
				'class'    => 'zoominout-options-new-global', 
				'style' => 'display: none;'
			)
		);

		add_settings_field ( 'new_product_sticker_animation_direction', __( 'Sticker Animation Direction:', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_sticker_animation_direction'
        ), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'new_product_sticker_animation_delay', __( 'Sticker Animation Delay:', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_sticker_animation_delay'
			), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'new_product_sticker_animation_iteration_count', __( 'Sticker Animation Iteration Count:', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_sticker_animation_iteration_count'
				), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field('divider_new_product_animation_end','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
				},$this->new_product_settings_key,
				'section_new_product'
		);

		add_settings_field ( 'enable_new_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_new_product_schedule_sticker' 
				), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'new_product_schedule_sticker_date_time_start', __( 'Scheduled Sticker Date/Time Start:', 'woo-stickers-by-webline' ), array (
            &$this,
            'new_product_schedule_sticker_date_time_start'
                ), $this->new_product_settings_key, 'section_new_product');
 
        add_settings_field ( 'new_product_schedule_sticker_date_time_end', __( 'Scheduled Sticker Date/Time End:', 'woo-stickers-by-webline' ), array (
            &$this,
            'new_product_schedule_sticker_date_time_end'
                ), $this->new_product_settings_key, 'section_new_product');
 
        add_settings_field ( 'new_product_schedule_sticker_option_callback', __( 'Scheduled Sticker Custom Image:', 'woo-stickers-by-webline' ), array (
            &$this,
            'new_product_schedule_sticker_option_callback'
                ), $this->new_product_settings_key, 'section_new_product');

		add_settings_field ( 'new_product_schedule_sticker_image_width', __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_schedule_sticker_image_width'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage_sch' ) );

		add_settings_field ( 'new_product_schedule_sticker_image_height', __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_schedule_sticker_image_height'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage_sch' ) );

		add_settings_field ( 'new_product_schedule_custom_sticker', __( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_schedule_custom_sticker'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_optimage_sch' ) );

		add_settings_field ( 'new_product_schedule_custom_text', __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_schedule_custom_text'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

		add_settings_field ( 'enable_new_schedule_product_style', __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_new_schedule_product_style'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

		add_settings_field ( 'new_product_schedule_custom_text_fontcolor', __( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ), array (
			&$this,
			'new_product_schedule_custom_text_fontcolor'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_new' ) );
	
		add_settings_field ( 'new_product_schedule_custom_text_backcolor', __( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ), array (
		&$this,
		'new_product_schedule_custom_text_backcolor'
			), $this->new_product_settings_key, 'section_new_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_new' ) );

		add_settings_field('new_product_schedule_custom_text_padding',__( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ),array( 
			&$this, 
			'new_product_schedule_custom_text_padding' 
				),$this->new_product_settings_key,'section_new_product',array( 'class' => 'custom_option custom_opttext_sch' ));
	}

	public function sanitize_new_product_settings( $input ) {
		$in = is_array( $input ) ? $input : array();

		$yesno = static function( $v ) {
			$v = strtolower( trim( (string) wp_unslash( $v ) ) );
			return in_array( $v, array( '1', 'yes', 'on', 'true' ), true ) ? 'yes' : 'no';
		};
		$key    = static function( $v ) { return sanitize_key( wp_unslash( $v ) ); };
		$text   = static function( $v ) { return sanitize_text_field( wp_unslash( $v ) ); };
		$hex    = static function( $v ) { return sanitize_hex_color( wp_unslash( $v ) ); };
		$int    = static function( $v ) { return absint( wp_unslash( $v ) ); };
		$float  = static function( $v ) { return (float) ( is_numeric( $v ) ? wp_unslash( $v ) : 0 ); };
		$url    = static function( $v ) { return esc_url_raw( wp_unslash( $v ) ); };

		return array(
			'enable_new_product_sticker'                => isset( $in['enable_new_product_sticker'] ) ? $yesno( $in['enable_new_product_sticker'] ) : 'no',
			'new_product_sticker_days'                  => isset( $in['new_product_sticker_days'] ) ? $int( $in['new_product_sticker_days'] ) : 7,
			'new_product_position'                      => isset( $in['new_product_position'] ) ? $key( $in['new_product_position'] ) : 'left',
			'new_product_sticker_left_right'            => isset( $in['new_product_sticker_left_right'] ) ? $int( $in['new_product_sticker_left_right'] ) : 0,
			'new_product_sticker_top'                   => isset( $in['new_product_sticker_top'] ) ? $int( $in['new_product_sticker_top'] ) : 0,
			'new_product_sticker_rotate'                => isset( $in['new_product_sticker_rotate'] ) ? $float( $in['new_product_sticker_rotate'] ) : 0,
			'new_product_sticker_animation'             => isset( $in['new_product_sticker_animation'] ) ? $key( $in['new_product_sticker_animation'] ) : '',
			'enable_new_product_schedule_sticker'       => isset( $in['enable_new_product_schedule_sticker'] ) ? $yesno( $in['enable_new_product_schedule_sticker'] ) : 'no',
			'new_product_schedule_sticker'              => isset( $in['new_product_schedule_sticker'] ) ? $key( $in['new_product_schedule_sticker'] ) : '',
			'new_product_schedule_sticker_image_width'  => isset( $in['new_product_schedule_sticker_image_width'] ) ? $text( $in['new_product_schedule_sticker_image_width'] ) : '',
			'new_product_schedule_sticker_image_height' => isset( $in['new_product_schedule_sticker_image_height'] ) ? $text( $in['new_product_schedule_sticker_image_height'] ) : '',
			'new_product_schedule_custom_sticker'       => isset( $in['new_product_schedule_custom_sticker'] ) ? $url( $in['new_product_schedule_custom_sticker'] ) : '',
			'new_product_schedule_custom_text'          => isset( $in['new_product_schedule_custom_text'] ) ? $text( $in['new_product_schedule_custom_text'] ) : '',
			'enable_new_schedule_product_style'          => isset( $in['enable_new_schedule_product_style'] ) ? $text( $in['enable_new_schedule_product_style'] ) : '',
			'new_product_schedule_custom_text_fontcolor'=> isset( $in['new_product_schedule_custom_text_fontcolor'] ) ? $hex( $in['new_product_schedule_custom_text_fontcolor'] ) : '',
			'new_product_schedule_custom_text_backcolor'=> isset( $in['new_product_schedule_custom_text_backcolor'] ) ? $hex( $in['new_product_schedule_custom_text_backcolor'] ) : '',
			'new_product_schedule_text_padding_top'  => isset( $in['new_product_schedule_text_padding_top'] ) ? $text( $in['new_product_schedule_text_padding_top'] ) : '',
			'new_product_schedule_text_padding_right'  => isset( $in['new_product_schedule_text_padding_right'] ) ? $text( $in['new_product_schedule_text_padding_right'] ) : '',
			'new_product_schedule_text_padding_bottom'  => isset( $in['new_product_schedule_text_padding_bottom'] ) ? $text( $in['new_product_schedule_text_padding_bottom'] ) : '',
			'new_product_schedule_text_padding_left'  => isset( $in['new_product_schedule_text_padding_left'] ) ? $text( $in['new_product_schedule_text_padding_left'] ) : '',
			'new_product_option'                        => isset( $in['new_product_option'] ) ? $key( $in['new_product_option'] ) : 'image',
			'new_product_sticker_image_width'           => isset( $in['new_product_sticker_image_width'] ) ? $text( $in['new_product_sticker_image_width'] ) : '',
			'new_product_sticker_image_height'          => isset( $in['new_product_sticker_image_height'] ) ? $text( $in['new_product_sticker_image_height'] ) : '',
			'new_product_custom_text'                   => isset( $in['new_product_custom_text'] ) ? $text( $in['new_product_custom_text'] ) : '',
			'enable_new_product_style'          => isset( $in['enable_new_product_style'] ) ? $text( $in['enable_new_product_style'] ) : '',
			'new_product_custom_text_fontcolor'         => isset( $in['new_product_custom_text_fontcolor'] ) ? $hex( $in['new_product_custom_text_fontcolor'] ) : '',
			'new_product_custom_text_backcolor'         => isset( $in['new_product_custom_text_backcolor'] ) ? $hex( $in['new_product_custom_text_backcolor'] ) : '',
			'new_product_custom_sticker'                => isset( $in['new_product_custom_sticker'] ) ? $url( $in['new_product_custom_sticker'] ) : '',
			'new_product_text_padding_top'           => isset( $in['new_product_text_padding_top'] ) ? $text( $in['new_product_text_padding_top'] ) : '',
			'new_product_text_padding_right'           => isset( $in['new_product_text_padding_right'] ) ? $text( $in['new_product_text_padding_right'] ) : '',
			'new_product_text_padding_bottom'           => isset( $in['new_product_text_padding_bottom'] ) ? $text( $in['new_product_text_padding_bottom'] ) : '',
			'new_product_text_padding_left'           => isset( $in['new_product_text_padding_left'] ) ? $text( $in['new_product_text_padding_left'] ) : '',
			'new_product_schedule_sticker_option'                        => isset( $in['new_product_schedule_sticker_option'] ) ? $key( $in['new_product_schedule_sticker_option'] ) : 'image',
			'new_product_schedule_start_sticker_date_time'           => isset( $in['new_product_schedule_start_sticker_date_time'] ) ? $text( $in['new_product_schedule_start_sticker_date_time'] ) : '',
			'new_product_schedule_end_sticker_date_time'           => isset( $in['new_product_schedule_end_sticker_date_time'] ) ? $text( $in['new_product_schedule_end_sticker_date_time'] ) : '',
			'new_product_sticker_animation_iteration_count'           => isset( $in['new_product_sticker_animation_iteration_count'] ) ? $text( $in['new_product_sticker_animation_iteration_count'] ) : '',
			'new_product_sticker_animation_delay'           => isset( $in['new_product_sticker_animation_delay'] ) ? $text( $in['new_product_sticker_animation_delay'] ) : '',
			'new_product_sticker_animation_type'           => isset( $in['new_product_sticker_animation_type'] ) ? $text( $in['new_product_sticker_animation_type'] ) : '',
			'new_product_sticker_animation_scale'           => isset( $in['new_product_sticker_animation_scale'] ) ? $text( $in['new_product_sticker_animation_scale'] ) : '',
			'new_product_sticker_animation_direction'           => isset( $in['new_product_sticker_animation_direction'] ) ? $text( $in['new_product_sticker_animation_direction'] ) : '',

		);
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
		$this->plugin_settings_tabs[ $this->sale_product_settings_key ] = __( 'Products On Sale', 'woo-stickers-by-webline' );

		register_setting(
			$this->sale_product_settings_key,
			$this->sale_product_settings_key,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_sale_product_settings' ),
				'default'           => array(
					'enable_sale_product_sticker'                => 'no',
					'sale_product_position'                      => 'left',
					'sale_product_sticker_left_right'            => 0,
					'sale_product_sticker_top'                   => 0,
					'sale_product_sticker_rotate'                => 0,
					'sale_product_sticker_animation'             => '',
					'enable_sale_product_schedule_sticker'       => 'no',
					'sale_product_schedule_sticker'              => '',
					'sale_product_schedule_sticker_image_width'  => '',
					'sale_product_schedule_sticker_image_height' => '',
					'sale_product_schedule_custom_sticker'       => '',
					'sale_product_schedule_custom_text'          => '',
					'enable_sale_schedule_product_style'         => 'no',
					'sale_product_schedule_custom_text_fontcolor'=> '',
					'sale_product_schedule_custom_text_backcolor'=> '',
					'sale_product_schedule_custom_text_padding'  => '',
					'sale_product_option'                        => 'image',
					'sale_product_sticker_image_width'           => '',
					'sale_product_sticker_image_height'          => '',
					'sale_product_custom_text'                   => '',
					'enable_sale_product_style'                  => '',
					'sale_product_custom_text_fontcolor'         => '',
					'sale_product_custom_text_backcolor'         => '',
					'sale_product_custom_text_padding'           => '',
					'sale_product_custom_sticker'                => '',

					'sale_product_schedule_text_padding_top' => '',
					'sale_product_schedule_text_padding_right' => '',
					'sale_product_schedule_text_padding_bottom' => '',
					'sale_product_schedule_text_padding_left' => '',

					'sale_product_text_padding_top'		   => '',
					'sale_product_text_padding_right'		   => '',
					'sale_product_text_padding_bottom'		   => '',
					'sale_product_text_padding_left'		   => '',
					'stickeroption_sch' => '',

					'sale_product_schedule_start_sticker_date_time' => '',
					'sale_product_schedule_end_sticker_date_time' => '',

					'sale_product_sticker_animation_iteration_count' => "",
					'sale_product_sticker_animation_delay' => "",

					'sale_product_sticker_animation_type'  => '',
					'sale_product_sticker_animation_scale' => '',
					'sale_product_sticker_animation_direction' => '',

				),
			)
		);

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

		add_settings_field('divider_sale_product_animation_start','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
				},$this->sale_product_settings_key,
				'section_sale_product'
		);

		add_settings_field ( 'sale_product_sticker_animation_type', __( 'Sticker Animation:', 'woo-stickers-by-webline' ), array (
    		&$this,
    		'sale_product_sticker_animation_type'
        	), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field(
			'sale_product_sticker_animation_scale',
			__( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
			array( &$this, 'sale_product_sticker_animation_scale' ),
			$this->sale_product_settings_key,
			'section_sale_product',
			array(
				'class'    => 'zoominout-options-sale-global', 
				'style' => 'display: none;'
			)
		);

		add_settings_field ( 'sale_product_sticker_animation_direction', __( 'Sticker Animation Direction:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_sticker_animation_direction'
        ), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_sticker_animation_delay', __( 'Sticker Animation Delay:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_sticker_animation_delay'
			), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_sticker_animation_iteration_count', __( 'Sticker Animation Iteration Count:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_sticker_animation_iteration_count'
				), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field('divider_sale_product_animation_end','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
				},$this->sale_product_settings_key,
				'section_sale_product'
		);

		add_settings_field ( 'enable_sale_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_sale_product_schedule_sticker' 
				), $this->sale_product_settings_key, 'section_sale_product' );

		add_settings_field ( 'sale_product_schedule_sticker_date_time_start', __( 'Scheduled Sticker Date/Time Start:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_sticker_date_time_start'
				), $this->sale_product_settings_key, 'section_sale_product');

		add_settings_field ( 'sale_product_schedule_sticker_date_time_end', __( 'Scheduled Sticker Date/Time End:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_sticker_date_time_end'
				), $this->sale_product_settings_key, 'section_sale_product');

		add_settings_field ( 'sale_product_schedule_sticker_option_callback', __( 'Scheduled Sticker Custom Image:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_sticker_option_callback'
				), $this->sale_product_settings_key, 'section_sale_product');
		
		add_settings_field ( 'sale_product_schedule_sticker_image_width', __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_sticker_image_width'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'sale_product_schedule_sticker_image_height', __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_sticker_image_height'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'sale_product_schedule_custom_sticker', __( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_custom_sticker'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'sale_product_schedule_custom_text', __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_custom_text'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch' ) );
		
		add_settings_field ( 'enable_sale_schedule_product_style', __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_sale_schedule_product_style'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

		add_settings_field ( 'sale_product_schedule_custom_text_fontcolor', __( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ), array (
			&$this,
			'sale_product_schedule_custom_text_fontcolor'
				), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_sale' ) );
		
		add_settings_field ( 'sale_product_schedule_custom_text_backcolor', __( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ), array (
		&$this,
		'sale_product_schedule_custom_text_backcolor'
			), $this->sale_product_settings_key, 'section_sale_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_sale' ) );
		
		add_settings_field('sale_product_schedule_custom_text_padding',__( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ),array( 
			&$this, 
			'sale_product_schedule_custom_text_padding' 
				),$this->sale_product_settings_key,'section_sale_product',array( 'class' => 'custom_option custom_opttext_sch' )
			);
	}

	public function sanitize_sale_product_settings( $input ) {
		$in = is_array( $input ) ? $input : array();

		$yesno = static function( $v ) { $v = strtolower( trim( (string) wp_unslash( $v ) ) ); return in_array( $v, array( '1','yes','on','true' ), true ) ? 'yes' : 'no'; };
		$key   = static function( $v ) { return sanitize_key( wp_unslash( $v ) ); };
		$text  = static function( $v ) { return sanitize_text_field( wp_unslash( $v ) ); };
		$hex   = static function( $v ) { return sanitize_hex_color( wp_unslash( $v ) ); };
		$int   = static function( $v ) { return absint( wp_unslash( $v ) ); };
		$float = static function( $v ) { return (float) ( is_numeric( $v ) ? wp_unslash( $v ) : 0 ); };
		$url   = static function( $v ) { return esc_url_raw( wp_unslash( $v ) ); };

		return array(
			'enable_sale_product_sticker'                 => isset( $in['enable_sale_product_sticker'] ) ? $yesno( $in['enable_sale_product_sticker'] ) : 'no',
			'sale_product_position'                       => isset( $in['sale_product_position'] ) ? $key( $in['sale_product_position'] ) : 'left',
			'sale_product_sticker_left_right'             => isset( $in['sale_product_sticker_left_right'] ) ? $int( $in['sale_product_sticker_left_right'] ) : 0,
			'sale_product_sticker_top'                    => isset( $in['sale_product_sticker_top'] ) ? $int( $in['sale_product_sticker_top'] ) : 0,
			'sale_product_sticker_rotate'                 => isset( $in['sale_product_sticker_rotate'] ) ? $float( $in['sale_product_sticker_rotate'] ) : 0,
			'sale_product_sticker_animation'              => isset( $in['sale_product_sticker_animation'] ) ? $key( $in['sale_product_sticker_animation'] ) : '',
			'enable_sale_product_schedule_sticker'        => isset( $in['enable_sale_product_schedule_sticker'] ) ? $yesno( $in['enable_sale_product_schedule_sticker'] ) : 'no',
			'sale_product_schedule_sticker'               => isset( $in['sale_product_schedule_sticker'] ) ? $key( $in['sale_product_schedule_sticker'] ) : '',
			'sale_product_schedule_sticker_image_width'   => isset( $in['sale_product_schedule_sticker_image_width'] ) ? $text( $in['sale_product_schedule_sticker_image_width'] ) : '',
			'sale_product_schedule_sticker_image_height'  => isset( $in['sale_product_schedule_sticker_image_height'] ) ? $text( $in['sale_product_schedule_sticker_image_height'] ) : '',
			'sale_product_schedule_custom_sticker'        => isset( $in['sale_product_schedule_custom_sticker'] ) ? $url( $in['sale_product_schedule_custom_sticker'] ) : '',
			'sale_product_schedule_custom_text'           => isset( $in['sale_product_schedule_custom_text'] ) ? $text( $in['sale_product_schedule_custom_text'] ) : '',
			'enable_sale_schedule_product_style'          => isset( $in['enable_sale_schedule_product_style'] ) ? $yesno( $in['enable_sale_schedule_product_style'] ) : 'no',
			'sale_product_schedule_custom_text_fontcolor' => isset( $in['sale_product_schedule_custom_text_fontcolor'] ) ? $hex( $in['sale_product_schedule_custom_text_fontcolor'] ) : '',
			'sale_product_schedule_custom_text_backcolor' => isset( $in['sale_product_schedule_custom_text_backcolor'] ) ? $hex( $in['sale_product_schedule_custom_text_backcolor'] ) : '',
			'sale_product_schedule_custom_text_padding'   => isset( $in['sale_product_schedule_custom_text_padding'] ) ? $text( $in['sale_product_schedule_custom_text_padding'] ) : '',
			'sale_product_option'                         => isset( $in['sale_product_option'] ) ? $key( $in['sale_product_option'] ) : 'image',
			'sale_product_sticker_image_width'            => isset( $in['sale_product_sticker_image_width'] ) ? $text( $in['sale_product_sticker_image_width'] ) : '',
			'sale_product_sticker_image_height'           => isset( $in['sale_product_sticker_image_height'] ) ? $text( $in['sale_product_sticker_image_height'] ) : '',
			'sale_product_custom_text'                    => isset( $in['sale_product_custom_text'] ) ? $text( $in['sale_product_custom_text'] ) : '',
			'enable_sale_product_style'          => isset( $in['enable_sale_product_style'] ) ? $text( $in['enable_sale_product_style'] ) : '',
			'sale_product_custom_text_fontcolor'          => isset( $in['sale_product_custom_text_fontcolor'] ) ? $hex( $in['sale_product_custom_text_fontcolor'] ) : '',
			'sale_product_custom_text_backcolor'          => isset( $in['sale_product_custom_text_backcolor'] ) ? $hex( $in['sale_product_custom_text_backcolor'] ) : '',
			'sale_product_custom_text_padding'            => isset( $in['sale_product_custom_text_padding'] ) ? $text( $in['sale_product_custom_text_padding'] ) : '',
			'sale_product_custom_sticker'                 => isset( $in['sale_product_custom_sticker'] ) ? $url( $in['sale_product_custom_sticker'] ) : '',

			'sale_product_schedule_text_padding_top'  => isset( $in['sale_product_schedule_text_padding_top'] ) ? $text( $in['sale_product_schedule_text_padding_top'] ) : '',
			'sale_product_schedule_text_padding_right'  => isset( $in['sale_product_schedule_text_padding_right'] ) ? $text( $in['sale_product_schedule_text_padding_right'] ) : '',
			'sale_product_schedule_text_padding_bottom'  => isset( $in['sale_product_schedule_text_padding_bottom'] ) ? $text( $in['sale_product_schedule_text_padding_bottom'] ) : '',
			'sale_product_schedule_text_padding_left'  => isset( $in['sale_product_schedule_text_padding_left'] ) ? $text( $in['sale_product_schedule_text_padding_left'] ) : '',

			'sale_product_text_padding_top'           => isset( $in['sale_product_text_padding_top'] ) ? $text( $in['sale_product_text_padding_top'] ) : '',
			'sale_product_text_padding_right'           => isset( $in['sale_product_text_padding_right'] ) ? $text( $in['sale_product_text_padding_right'] ) : '',
			'sale_product_text_padding_bottom'           => isset( $in['sale_product_text_padding_bottom'] ) ? $text( $in['sale_product_text_padding_bottom'] ) : '',
			'sale_product_text_padding_left'           => isset( $in['sale_product_text_padding_left'] ) ? $text( $in['sale_product_text_padding_left'] ) : '',
			'sale_product_schedule_sticker_option'     => isset( $in['sale_product_schedule_sticker_option'] ) ? $key( $in['sale_product_schedule_sticker_option'] ) : 'image',

			'sale_product_schedule_start_sticker_date_time'     => isset( $in['sale_product_schedule_start_sticker_date_time'] ) ? $text( $in['sale_product_schedule_start_sticker_date_time'] ) : '',
			'sale_product_schedule_end_sticker_date_time'       => isset( $in['sale_product_schedule_end_sticker_date_time'] ) ? $text( $in['sale_product_schedule_end_sticker_date_time'] ) : '',

			'sale_product_sticker_animation_iteration_count'           => isset( $in['sale_product_sticker_animation_iteration_count'] ) ? $text( $in['sale_product_sticker_animation_iteration_count'] ) : '',
			'sale_product_sticker_animation_delay'           => isset( $in['sale_product_sticker_animation_delay'] ) ? $text( $in['sale_product_sticker_animation_delay'] ) : '',
			
			'sale_product_sticker_animation_type'           => isset( $in['sale_product_sticker_animation_type'] ) ? $text( $in['sale_product_sticker_animation_type'] ) : '',
			'sale_product_sticker_animation_scale'           => isset( $in['sale_product_sticker_animation_scale'] ) ? $text( $in['sale_product_sticker_animation_scale'] ) : '',
			'sale_product_sticker_animation_direction'           => isset( $in['sale_product_sticker_animation_direction'] ) ? $text( $in['sale_product_sticker_animation_direction'] ) : '',

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
		$this->plugin_settings_tabs[ $this->sold_product_settings_key ] = __( 'Soldout Products', 'woo-stickers-by-webline' );

		register_setting(
			$this->sold_product_settings_key,
			$this->sold_product_settings_key,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_sold_product_settings' ),
				'default'           => array(
					'enable_sold_product_sticker'                => 'no',
					'sold_product_position'                      => 'left',
					'sold_product_sticker_left_right'            => 0,
					'sold_product_sticker_top'                   => 0,
					'sold_product_sticker_rotate'                => 0,
					'sold_product_sticker_animation'             => '',
					'enable_sold_product_schedule_sticker'       => 'no',
					'sold_product_schedule_sticker'              => '',
					'sold_product_schedule_sticker_image_width'  => '',
					'sold_product_schedule_sticker_image_height' => '',
					'sold_product_schedule_custom_sticker'       => '',
					'sold_product_schedule_custom_text'          => '',
					'enable_sold_schedule_product_style'         => 'no',
					'sold_product_schedule_custom_text_fontcolor'=> '',
					'sold_product_schedule_custom_text_backcolor'=> '',
					'sold_product_schedule_custom_text_padding'  => '',
					'sold_product_option'                        => 'image',
					'sold_product_sticker_image_width'           => '',
					'sold_product_sticker_image_height'          => '',
					'sold_product_custom_text'                   => '',
					'enable_sold_product_style'                  => 'ribbon',
					'sold_product_custom_text_fontcolor'         => '',
					'sold_product_custom_text_backcolor'         => '',
					'sold_product_custom_text_padding'           => '',
					'sold_product_custom_sticker'                => '',

					'sold_product_schedule_text_padding_top' => '',
					'sold_product_schedule_text_padding_right' => '',
					'sold_product_schedule_text_padding_bottom' => '',
					'sold_product_schedule_text_padding_left' => '',

					'sold_product_text_padding_top'		   => '',
					'sold_product_text_padding_right'		   => '',
					'sold_product_text_padding_bottom'		   => '',
					'sold_product_text_padding_left'		   => '',
					'stickeroption_sch' => '',

					'sold_product_schedule_start_sticker_date_time' => '',
					'sold_product_schedule_end_sticker_date_time' => '',

					'sold_product_sticker_animation_iteration_count' => "",
					'sold_product_sticker_animation_delay' => "",

					'sold_product_sticker_animation_type'  => '',
					'sold_product_sticker_animation_scale' => '',
					'sold_product_sticker_animation_direction' => '',

				),
			)
		);

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

		add_settings_field('divider_sold_product_animation_start','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
				},$this->sold_product_settings_key,
				'section_sold_product'
		);

		add_settings_field ( 'sold_product_sticker_animation_type', __( 'Sticker Animation:', 'woo-stickers-by-webline' ), array (
    	&$this,
    		'sold_product_sticker_animation_type'
        	), $this->sold_product_settings_key, 'section_sold_product' );


		add_settings_field(
			'sold_product_sticker_animation_scale',
			__( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
			array( &$this, 'sold_product_sticker_animation_scale' ),
			$this->sold_product_settings_key,
			'section_sold_product',
			array(
				'class'    => 'zoominout-options-sold-global', 
				'style' => 'display: none;'
			)
		);

		add_settings_field ( 'sold_product_sticker_animation_direction', __( 'Sticker Animation Direction:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_sticker_animation_direction'
        ), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'sold_product_sticker_animation_delay', __( 'Sticker Animation Delay:', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_sticker_animation_delay'
			), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'sold_product_sticker_animation_iteration_count', __( 'Sticker Animation Iteration Count:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_sticker_animation_iteration_count'
				), $this->sold_product_settings_key, 'section_sold_product' );
		
		add_settings_field('divider_sold_product_animation_end','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
				},$this->sold_product_settings_key,
				'section_sold_product'
		);

		add_settings_field ( 'enable_sold_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_sold_product_schedule_sticker' 
				), $this->sold_product_settings_key, 'section_sold_product' );

		add_settings_field ( 'sold_product_schedule_sticker_date_time_start', __( 'Scheduled Sticker Date/Time Start:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_sticker_date_time_start'
				), $this->sold_product_settings_key, 'section_sold_product');

		add_settings_field ( 'sold_product_schedule_sticker_date_time_end', __( 'Scheduled Sticker Date/Time End:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_sticker_date_time_end'
				), $this->sold_product_settings_key, 'section_sold_product');

		add_settings_field ( 'sold_product_schedule_sticker_option_callback', __( 'Scheduled Sticker Custom Image:', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_sticker_option_callback'
				), $this->sold_product_settings_key, 'section_sold_product');
		
		add_settings_field ( 'sold_product_schedule_sticker_image_width', __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_sticker_image_width'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'sold_product_schedule_sticker_image_height', __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_sticker_image_height'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'sold_product_schedule_custom_sticker', __( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_custom_sticker'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'sold_product_schedule_custom_text', __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_custom_text'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch' ) );
		
		add_settings_field ( 'enable_sold_schedule_product_style', __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_sold_schedule_product_style'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

		add_settings_field ( 'sold_product_schedule_custom_text_fontcolor', __( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ), array (
			&$this,
			'sold_product_schedule_custom_text_fontcolor'
				), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_sold' ) );
		
		add_settings_field ( 'sold_product_schedule_custom_text_backcolor', __( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ), array (
		&$this,
		'sold_product_schedule_custom_text_backcolor'
			), $this->sold_product_settings_key, 'section_sold_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_sold' ) );
		
		add_settings_field('sold_product_schedule_custom_text_padding',__( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ),array( 
			&$this, 
			'sold_product_schedule_custom_text_padding' 
				),$this->sold_product_settings_key,'section_sold_product',array( 'class' => 'custom_option custom_opttext_sch' )
			);
	}

	public function sanitize_sold_product_settings( $input ) {
		$in = is_array( $input ) ? $input : array();

		$yesno = static function( $v ) { $v = strtolower( trim( (string) wp_unslash( $v ) ) ); return in_array( $v, array( '1','yes','on','true' ), true ) ? 'yes' : 'no'; };
		$key   = static function( $v ) { return sanitize_key( wp_unslash( $v ) ); };
		$text  = static function( $v ) { return sanitize_text_field( wp_unslash( $v ) ); };
		$hex   = static function( $v ) { return sanitize_hex_color( wp_unslash( $v ) ); };
		$int   = static function( $v ) { return absint( wp_unslash( $v ) ); };
		$float = static function( $v ) { return (float) ( is_numeric( $v ) ? wp_unslash( $v ) : 0 ); };
		$url   = static function( $v ) { return esc_url_raw( wp_unslash( $v ) ); };

		return array(
			'enable_sold_product_sticker'                 => isset( $in['enable_sold_product_sticker'] ) ? $yesno( $in['enable_sold_product_sticker'] ) : 'no',
			'sold_product_position'                       => isset( $in['sold_product_position'] ) ? $key( $in['sold_product_position'] ) : 'left',
			'sold_product_sticker_left_right'             => isset( $in['sold_product_sticker_left_right'] ) ? $int( $in['sold_product_sticker_left_right'] ) : 0,
			'sold_product_sticker_top'                    => isset( $in['sold_product_sticker_top'] ) ? $int( $in['sold_product_sticker_top'] ) : 0,
			'sold_product_sticker_rotate'                 => isset( $in['sold_product_sticker_rotate'] ) ? $float( $in['sold_product_sticker_rotate'] ) : 0,
			'sold_product_sticker_animation'              => isset( $in['sold_product_sticker_animation'] ) ? $key( $in['sold_product_sticker_animation'] ) : '',
			'enable_sold_product_schedule_sticker'        => isset( $in['enable_sold_product_schedule_sticker'] ) ? $yesno( $in['enable_sold_product_schedule_sticker'] ) : 'no',
			'sold_product_schedule_sticker'               => isset( $in['sold_product_schedule_sticker'] ) ? $key( $in['sold_product_schedule_sticker'] ) : '',
			'sold_product_schedule_sticker_image_width'   => isset( $in['sold_product_schedule_sticker_image_width'] ) ? $text( $in['sold_product_schedule_sticker_image_width'] ) : '',
			'sold_product_schedule_sticker_image_height'  => isset( $in['sold_product_schedule_sticker_image_height'] ) ? $text( $in['sold_product_schedule_sticker_image_height'] ) : '',
			'sold_product_schedule_custom_sticker'        => isset( $in['sold_product_schedule_custom_sticker'] ) ? $url( $in['sold_product_schedule_custom_sticker'] ) : '',
			'sold_product_schedule_custom_text'           => isset( $in['sold_product_schedule_custom_text'] ) ? $text( $in['sold_product_schedule_custom_text'] ) : '',
			'enable_sold_schedule_product_style'          => isset( $in['enable_sold_schedule_product_style'] ) ? $yesno( $in['enable_sold_schedule_product_style'] ) : 'no',
			'sold_product_schedule_custom_text_fontcolor' => isset( $in['sold_product_schedule_custom_text_fontcolor'] ) ? $hex( $in['sold_product_schedule_custom_text_fontcolor'] ) : '',
			'sold_product_schedule_custom_text_backcolor' => isset( $in['sold_product_schedule_custom_text_backcolor'] ) ? $hex( $in['sold_product_schedule_custom_text_backcolor'] ) : '',
			'sold_product_schedule_custom_text_padding'   => isset( $in['sold_product_schedule_custom_text_padding'] ) ? $text( $in['sold_product_schedule_custom_text_padding'] ) : '',
			'sold_product_option'                         => isset( $in['sold_product_option'] ) ? $key( $in['sold_product_option'] ) : 'image',
			'sold_product_sticker_image_width'            => isset( $in['sold_product_sticker_image_width'] ) ? $text( $in['sold_product_sticker_image_width'] ) : '',
			'sold_product_sticker_image_height'           => isset( $in['sold_product_sticker_image_height'] ) ? $text( $in['sold_product_sticker_image_height'] ) : '',
			'sold_product_custom_text'                    => isset( $in['sold_product_custom_text'] ) ? $text( $in['sold_product_custom_text'] ) : '',
			'enable_sold_product_style'                       => isset( $in['enable_sold_product_style'] ) ? $key( $in['enable_sold_product_style'] ) : 'ribb',
			'sold_product_custom_text_fontcolor'          => isset( $in['sold_product_custom_text_fontcolor'] ) ? $hex( $in['sold_product_custom_text_fontcolor'] ) : '',
			'sold_product_custom_text_backcolor'          => isset( $in['sold_product_custom_text_backcolor'] ) ? $hex( $in['sold_product_custom_text_backcolor'] ) : '',
			'sold_product_custom_text_padding'            => isset( $in['sold_product_custom_text_padding'] ) ? $text( $in['sold_product_custom_text_padding'] ) : '',
			'sold_product_custom_sticker'                 => isset( $in['sold_product_custom_sticker'] ) ? $url( $in['sold_product_custom_sticker'] ) : '',
			'sold_product_schedule_text_padding_top'  => isset( $in['sold_product_schedule_text_padding_top'] ) ? $text( $in['sold_product_schedule_text_padding_top'] ) : '',
			'sold_product_schedule_text_padding_right'  => isset( $in['sold_product_schedule_text_padding_right'] ) ? $text( $in['sold_product_schedule_text_padding_right'] ) : '',
			'sold_product_schedule_text_padding_bottom'  => isset( $in['sold_product_schedule_text_padding_bottom'] ) ? $text( $in['sold_product_schedule_text_padding_bottom'] ) : '',
			'sold_product_schedule_text_padding_left'  => isset( $in['sold_product_schedule_text_padding_left'] ) ? $text( $in['sold_product_schedule_text_padding_left'] ) : '',

			'sold_product_text_padding_top'           => isset( $in['sold_product_text_padding_top'] ) ? $text( $in['sold_product_text_padding_top'] ) : '',
			'sold_product_text_padding_right'           => isset( $in['sold_product_text_padding_right'] ) ? $text( $in['sold_product_text_padding_right'] ) : '',
			'sold_product_text_padding_bottom'           => isset( $in['sold_product_text_padding_bottom'] ) ? $text( $in['sold_product_text_padding_bottom'] ) : '',
			'sold_product_text_padding_left'           => isset( $in['sold_product_text_padding_left'] ) ? $text( $in['sold_product_text_padding_left'] ) : '',
			'sold_product_schedule_sticker_option'     => isset( $in['sold_product_schedule_sticker_option'] ) ? $key( $in['sold_product_schedule_sticker_option'] ) : 'image',

			'sold_product_schedule_start_sticker_date_time'     => isset( $in['sold_product_schedule_start_sticker_date_time'] ) ? $text( $in['sold_product_schedule_start_sticker_date_time'] ) : '',
			'sold_product_schedule_end_sticker_date_time'       => isset( $in['sold_product_schedule_end_sticker_date_time'] ) ? $text( $in['sold_product_schedule_end_sticker_date_time'] ) : '',

			'sold_product_sticker_animation_iteration_count'           => isset( $in['sold_product_sticker_animation_iteration_count'] ) ? $text( $in['sold_product_sticker_animation_iteration_count'] ) : '',
			'sold_product_sticker_animation_delay'           => isset( $in['sold_product_sticker_animation_delay'] ) ? $text( $in['sold_product_sticker_animation_delay'] ) : '',

			'sold_product_sticker_animation_type'           => isset( $in['sold_product_sticker_animation_type'] ) ? $text( $in['sold_product_sticker_animation_type'] ) : '',
			'sold_product_sticker_animation_scale'           => isset( $in['sold_product_sticker_animation_scale'] ) ? $text( $in['sold_product_sticker_animation_scale'] ) : '',
			'sold_product_sticker_animation_direction'           => isset( $in['sold_product_sticker_animation_direction'] ) ? $text( $in['sold_product_sticker_animation_direction'] ) : '',

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
		$this->plugin_settings_tabs[ $this->cust_product_settings_key ] = __( 'Custom Product Sticker', 'woo-stickers-by-webline' );

		register_setting(
			$this->cust_product_settings_key,
			$this->cust_product_settings_key,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_cust_product_settings' ),
				'default'           => array(
					'enable_cust_product_sticker'                => 'no',
					'cust_product_position'                      => 'left',
					'cust_product_sticker_left_right'            => 0,
					'cust_product_sticker_top'                   => 0,
					'cust_product_sticker_rotate'                => 0,
					'cust_product_sticker_animation'             => '',
					'enable_cust_product_schedule_sticker'       => 'no',
					'cust_product_schedule_sticker'              => '',
					'cust_product_schedule_sticker_image_width'  => '',
					'cust_product_schedule_sticker_image_height' => '',
					'cust_product_schedule_custom_sticker'       => '',
					'cust_product_schedule_custom_text'          => '',
					'enable_cust_schedule_product_style'         => 'no',
					'cust_product_schedule_custom_text_fontcolor'=> '',
					'cust_product_schedule_custom_text_backcolor'=> '',
					'cust_product_schedule_custom_text_padding'  => '',
					'cust_product_option'                        => 'image',
					'cust_product_sticker_image_width'           => '',
					'cust_product_sticker_image_height'          => '',
					'cust_product_custom_text'                   => '',
					'enable_cust_product_style'                  => 'no',
					'cust_product_custom_text_fontcolor'         => '',
					'cust_product_custom_text_backcolor'         => '',
					'cust_product_custom_text_padding'           => '',
					'cust_product_custom_sticker'                => '',

					'cust_product_schedule_text_padding_top' => '',
					'cust_product_schedule_text_padding_right' => '',
					'cust_product_schedule_text_padding_bottom' => '',
					'cust_product_schedule_text_padding_left' => '',

					'cust_product_text_padding_top'		   => '',
					'cust_product_text_padding_right'		   => '',
					'cust_product_text_padding_bottom'		   => '',
					'cust_product_text_padding_left'		   => '',
					'stickeroption_sch' => '',

					'cust_product_schedule_start_sticker_date_time' => '',
					'cust_product_schedule_end_sticker_date_time' => '',

					'cust_product_sticker_animation_iteration_count' => "",
					'cust_product_sticker_animation_delay' => "",

					'cust_product_sticker_animation_type'  => '',
					'cust_product_sticker_animation_scale' => '',
					'cust_product_sticker_animation_direction' => '',

				),
			)
		);

		add_settings_section( 'section_cust_product', __( 'Custom Sticker Configurations for Products', 'woo-stickers-by-webline' ), array( $this, 'section_cust_product_desc' ), $this->cust_product_settings_key );

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

		add_settings_field('divider_cust_product_animation_start','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
			},$this->cust_product_settings_key,
			'section_cust_product'
		);
		
		add_settings_field ( 'cust_product_sticker_animation_type', __( 'Sticker Animation:', 'woo-stickers-by-webline' ), array (
    	&$this,
    		'cust_product_sticker_animation_type'
        	), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field(
			'cust_product_sticker_animation_scale',
			__( 'Sticker Animation Scale', 'woo-stickers-by-webline' ),
			array( &$this, 'cust_product_sticker_animation_scale' ),
			$this->cust_product_settings_key,
			'section_cust_product',
			array(
				'class'    => 'zoominout-options-cust-global', 
				'style' => 'display: none;'
			)
		);

		add_settings_field ( 'cust_product_sticker_animation_direction', __( 'Sticker Animation Direction:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_sticker_animation_direction'
        ), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'cust_product_sticker_animation_delay', __( 'Sticker Animation Delay:', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_sticker_animation_delay'
			), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'cust_product_sticker_animation_iteration_count', __( 'Sticker Animation Iteration Count:', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_sticker_animation_iteration_count'
				), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field('divider_cust_product_animation_end','',
			function() {echo '<hr style="border: 0; border-top: 1px solid #444;">';
				},$this->cust_product_settings_key,
				'section_cust_product'
		);

		add_settings_field ( 'enable_cust_product_schedule_sticker', __( 'Enable Scheduled Product Sticker:', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_cust_product_schedule_sticker' 
				), $this->cust_product_settings_key, 'section_cust_product' );

		add_settings_field ( 'cust_product_schedule_sticker_date_time_start', __( 'Scheduled Sticker Date/Time Start:', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_sticker_date_time_start'
				), $this->cust_product_settings_key, 'section_cust_product');

		add_settings_field ( 'cust_product_schedule_sticker_date_time_end', __( 'Scheduled Sticker Date/Time End:', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_sticker_date_time_end'
				), $this->cust_product_settings_key, 'section_cust_product');

		add_settings_field ( 'cust_product_schedule_sticker_option_callback', __( 'Scheduled Sticker Custom Image:', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_sticker_option_callback'
				), $this->cust_product_settings_key, 'section_cust_product');
		
		add_settings_field ( 'cust_product_schedule_sticker_image_width', __( 'Schedule Sticker Image Width', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_sticker_image_width'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'cust_product_schedule_sticker_image_height', __( 'Schedule Sticker Image Height', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_sticker_image_height'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'cust_product_schedule_custom_sticker', __( 'Schedule Sticker Custom Image', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_custom_sticker'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_optimage_sch' ) );
		
		add_settings_field ( 'cust_product_schedule_custom_text', __( 'Schedule Sticker Custom Text', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_custom_text'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch' ) );
		
		add_settings_field ( 'enable_cust_schedule_product_style', __( 'Schedule Sticker Type', 'woo-stickers-by-webline' ), array (
			&$this,
			'enable_cust_schedule_product_style'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch' ) );

		add_settings_field ( 'cust_product_schedule_custom_text_fontcolor', __( 'Schedule Sticker Custom Text Font Color', 'woo-stickers-by-webline' ), array (
			&$this,
			'cust_product_schedule_custom_text_fontcolor'
				), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch fontcolor_sch_cust' ) );
		
		add_settings_field ( 'cust_product_schedule_custom_text_backcolor', __( 'Schedule Sticker Custom Text Back Color', 'woo-stickers-by-webline' ), array (
		&$this,
		'cust_product_schedule_custom_text_backcolor'
			), $this->cust_product_settings_key, 'section_cust_product', array( 'class' => 'custom_option custom_opttext_sch backcolor_sch_cust' ) );
		
		add_settings_field('cust_product_schedule_custom_text_padding',__( 'Schedule Sticker Custom Text Padding', 'woo-stickers-by-webline' ),array( 
			&$this, 
			'cust_product_schedule_custom_text_padding' 
				),$this->cust_product_settings_key,'section_cust_product',array( 'class' => 'custom_option custom_opttext_sch' )
			);
	}

	public function sanitize_cust_product_settings( $input ) {
		$in = is_array( $input ) ? $input : array();

		$yesno = static function( $v ) { $v = strtolower( trim( (string) wp_unslash( $v ) ) ); return in_array( $v, array( '1','yes','on','true' ), true ) ? 'yes' : 'no'; };
		$key   = static function( $v ) { return sanitize_key( wp_unslash( $v ) ); };
		$text  = static function( $v ) { return sanitize_text_field( wp_unslash( $v ) ); };
		$hex   = static function( $v ) { return sanitize_hex_color( wp_unslash( $v ) ); };
		$int   = static function( $v ) { return absint( wp_unslash( $v ) ); };
		$float = static function( $v ) { return (float) ( is_numeric( $v ) ? wp_unslash( $v ) : 0 ); };
		$url   = static function( $v ) { return esc_url_raw( wp_unslash( $v ) ); };

		return array(
			'enable_cust_product_sticker'                 => isset( $in['enable_cust_product_sticker'] ) ? $yesno( $in['enable_cust_product_sticker'] ) : 'no',
			'cust_product_position'                       => isset( $in['cust_product_position'] ) ? $key( $in['cust_product_position'] ) : 'left',
			'cust_product_sticker_left_right'             => isset( $in['cust_product_sticker_left_right'] ) ? $int( $in['cust_product_sticker_left_right'] ) : 0,
			'cust_product_sticker_top'                    => isset( $in['cust_product_sticker_top'] ) ? $int( $in['cust_product_sticker_top'] ) : 0,
			'cust_product_sticker_rotate'                 => isset( $in['cust_product_sticker_rotate'] ) ? $float( $in['cust_product_sticker_rotate'] ) : 0,
			'cust_product_sticker_animation'              => isset( $in['cust_product_sticker_animation'] ) ? $key( $in['cust_product_sticker_animation'] ) : '',
			'enable_cust_product_schedule_sticker'        => isset( $in['enable_cust_product_schedule_sticker'] ) ? $yesno( $in['enable_cust_product_schedule_sticker'] ) : 'no',
			'cust_product_schedule_sticker'               => isset( $in['cust_product_schedule_sticker'] ) ? $key( $in['cust_product_schedule_sticker'] ) : '',
			'cust_product_schedule_sticker_image_width'   => isset( $in['cust_product_schedule_sticker_image_width'] ) ? $text( $in['cust_product_schedule_sticker_image_width'] ) : '',
			'cust_product_schedule_sticker_image_height'  => isset( $in['cust_product_schedule_sticker_image_height'] ) ? $text( $in['cust_product_schedule_sticker_image_height'] ) : '',
			'cust_product_schedule_custom_sticker'        => isset( $in['cust_product_schedule_custom_sticker'] ) ? $url( $in['cust_product_schedule_custom_sticker'] ) : '',
			'cust_product_schedule_custom_text'           => isset( $in['cust_product_schedule_custom_text'] ) ? $text( $in['cust_product_schedule_custom_text'] ) : '',
			'enable_cust_schedule_product_style'          => isset( $in['enable_cust_schedule_product_style'] ) ? $yesno( $in['enable_cust_schedule_product_style'] ) : 'no',
			'cust_product_schedule_custom_text_fontcolor' => isset( $in['cust_product_schedule_custom_text_fontcolor'] ) ? $hex( $in['cust_product_schedule_custom_text_fontcolor'] ) : '',
			'cust_product_schedule_custom_text_backcolor' => isset( $in['cust_product_schedule_custom_text_backcolor'] ) ? $hex( $in['cust_product_schedule_custom_text_backcolor'] ) : '',
			'cust_product_schedule_custom_text_padding'   => isset( $in['cust_product_schedule_custom_text_padding'] ) ? $text( $in['cust_product_schedule_custom_text_padding'] ) : '',
			'cust_product_option'                         => isset( $in['cust_product_option'] ) ? $key( $in['cust_product_option'] ) : 'image',
			'cust_product_sticker_image_width'            => isset( $in['cust_product_sticker_image_width'] ) ? $text( $in['cust_product_sticker_image_width'] ) : '',
			'cust_product_sticker_image_height'           => isset( $in['cust_product_sticker_image_height'] ) ? $text( $in['cust_product_sticker_image_height'] ) : '',
			'cust_product_custom_text'                    => isset( $in['cust_product_custom_text'] ) ? $text( $in['cust_product_custom_text'] ) : '',
			'enable_cust_product_style'                   => isset( $in['enable_cust_product_style'] ) ? $yesno( $in['enable_cust_product_style'] ) : 'no',
			'cust_product_custom_text_fontcolor'          => isset( $in['cust_product_custom_text_fontcolor'] ) ? $hex( $in['cust_product_custom_text_fontcolor'] ) : '',
			'cust_product_custom_text_backcolor'          => isset( $in['cust_product_custom_text_backcolor'] ) ? $hex( $in['cust_product_custom_text_backcolor'] ) : '',
			'cust_product_custom_text_padding'            => isset( $in['cust_product_custom_text_padding'] ) ? $text( $in['cust_product_custom_text_padding'] ) : '',
			'cust_product_custom_sticker'                 => isset( $in['cust_product_custom_sticker'] ) ? $url( $in['cust_product_custom_sticker'] ) : '',

			'cust_product_schedule_text_padding_top'  => isset( $in['cust_product_schedule_text_padding_top'] ) ? $text( $in['cust_product_schedule_text_padding_top'] ) : '',
			'cust_product_schedule_text_padding_right'  => isset( $in['cust_product_schedule_text_padding_right'] ) ? $text( $in['cust_product_schedule_text_padding_right'] ) : '',
			'cust_product_schedule_text_padding_bottom'  => isset( $in['cust_product_schedule_text_padding_bottom'] ) ? $text( $in['cust_product_schedule_text_padding_bottom'] ) : '',
			'cust_product_schedule_text_padding_left'  => isset( $in['cust_product_schedule_text_padding_left'] ) ? $text( $in['cust_product_schedule_text_padding_left'] ) : '',

			'cust_product_text_padding_top'           => isset( $in['cust_product_text_padding_top'] ) ? $text( $in['cust_product_text_padding_top'] ) : '',
			'cust_product_text_padding_right'           => isset( $in['cust_product_text_padding_right'] ) ? $text( $in['cust_product_text_padding_right'] ) : '',
			'cust_product_text_padding_bottom'           => isset( $in['cust_product_text_padding_bottom'] ) ? $text( $in['cust_product_text_padding_bottom'] ) : '',
			'cust_product_text_padding_left'           => isset( $in['cust_product_text_padding_left'] ) ? $text( $in['cust_product_text_padding_left'] ) : '',
			'cust_product_schedule_sticker_option'     => isset( $in['cust_product_schedule_sticker_option'] ) ? $key( $in['cust_product_schedule_sticker_option'] ) : 'image',

			'cust_product_schedule_start_sticker_date_time'     => isset( $in['cust_product_schedule_start_sticker_date_time'] ) ? $text( $in['cust_product_schedule_start_sticker_date_time'] ) : '',
			'cust_product_schedule_end_sticker_date_time'       => isset( $in['cust_product_schedule_end_sticker_date_time'] ) ? $text( $in['cust_product_schedule_end_sticker_date_time'] ) : '',

			'cust_product_sticker_animation_iteration_count'           => isset( $in['cust_product_sticker_animation_iteration_count'] ) ? $text( $in['cust_product_sticker_animation_iteration_count'] ) : '',
			'cust_product_sticker_animation_delay'           => isset( $in['cust_product_sticker_animation_delay'] ) ? $text( $in['cust_product_sticker_animation_delay'] ) : '',

			'cust_product_sticker_animation_type'           => isset( $in['cust_product_sticker_animation_type'] ) ? $text( $in['cust_product_sticker_animation_type'] ) : '',
			'cust_product_sticker_animation_scale'           => isset( $in['cust_product_sticker_animation_scale'] ) ? $text( $in['cust_product_sticker_animation_scale'] ) : '',
			'cust_product_sticker_animation_direction'           => isset( $in['cust_product_sticker_animation_direction'] ) ? $text( $in['cust_product_sticker_animation_direction'] ) : '',

		);
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
			name="<?php echo esc_attr( $this->general_settings_key ); ?>[enable_sticker]">
			<option value='yes'
				<?php selected( esc_attr( $this->general_settings['enable_sticker'] ), 'yes',true );?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( esc_attr( $this->general_settings['enable_sticker'] ), 'no',true );?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select whether you want to enable sticker feature or not', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->general_settings_key ); ?>[enable_sticker_list]">
			<option value='yes'
				<?php selected( esc_attr( $this->general_settings['enable_sticker_list'] ), 'yes',true );?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( esc_attr( $this->general_settings['enable_sticker_list'] ), 'no',true );?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select whether you want to enable sticker feature on product listing page or not.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->general_settings_key ); ?>[enable_sticker_detail]">
			<option value='yes'
				<?php selected( esc_attr( $this->general_settings['enable_sticker_detail'] ), 'yes',true );?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( esc_attr( $this->general_settings['enable_sticker_detail'] ), 'no',true );?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select whether you want to enable sticker feature on product detail page or not.', 'woo-stickers-by-webline' );?></p>
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
		<textarea id="sticker_custom_css" name="<?php echo esc_attr( $this->general_settings_key ); ?>[custom_css]" rows="4" cols="50"><?php echo !empty( $this->general_settings['custom_css'] ) ? esc_textarea( $this->general_settings['custom_css'] ) : '';?></textarea>
		<p class="description"><?php esc_html_e( 'Add your custom css here to load on frontend.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[enable_new_product_sticker]">
			<option value='yes'
				<?php selected( esc_attr( $this->new_product_settings['enable_new_product_sticker'] ), 'yes',true );?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( esc_attr( $this->new_product_settings['enable_new_product_sticker'] ), 'no',true );?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Enable or disable sticker display for products marked as NEW in WooCommerce.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="new_product_sticker_days" class="small-text" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_sticker_days]" value="<?php echo absint( $this->new_product_settings['new_product_sticker_days']); ?>" />
		<p class="description"><?php esc_html_e( 'Specify the number of days a product should be displayed as New (Default: 10 days)', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr($this->new_product_settings_key); ?>[new_product_position]">
			<option value='left'
				<?php selected( esc_attr($this->new_product_settings['new_product_position']), 'left',true );?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( esc_attr($this->new_product_settings['new_product_position']), 'right',true );?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select the position of the sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="new_product_sticker_left_right" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_sticker_left_right]" 
			<?php 
			if ( isset( $this->new_product_settings['new_product_sticker_left_right'] ) ) {
				echo 'value="' . esc_attr( $this->new_product_settings['new_product_sticker_left_right'] ) . '"'; 
			}
			?> 
		/>
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
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
		<input type="number" class="small-text" id="new_product_sticker_top" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_sticker_top]" <?php if ( isset( $this->new_product_settings['new_product_sticker_top'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_sticker_top'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_sticker_rotate() {
		?>
			<input type="number" class="small-text" id="new_product_sticker_rotate" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_sticker_rotate]" value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_rotate'] ); ?>"/>
			<p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_sticker_animation_type() {
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
					echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
				}
			?>
		</select>
		<p class="description"><?php esc_html_e( 'Select the animation type for the sticker.', 'woo-stickers-by-webline' );?></p>
	<?php		
	}

	public function new_product_sticker_animation_scale() {
		?>
			<div id="zoominout-options-new-global" style="display: none;">
				<input type="number" id="new_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo esc_attr($this->new_product_settings_key );?>[new_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php esc_html_e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			</div>
		<?php		
	}

	public function new_product_sticker_animation_direction() {
		?>
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
						echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
					}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select the animation direction.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function new_product_sticker_animation_iteration_count() {
		?>
			<input type="text" id="new_product_sticker_animation_iteration_count" step="any" name="<?php echo esc_attr($this->new_product_settings_key);?>[new_product_sticker_animation_iteration_count]" 
			value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_animation_iteration_count']); ?>" placeholder='Iteration Count'/>
			<p class="description"><?php esc_html_e( 'Set number of times animation repeats. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_sticker_animation_delay() {
		?>
			<input type="number" id="new_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo esc_attr($this->new_product_settings_key);?>[new_product_sticker_animation_delay]" 
			value="<?php echo esc_attr( $this->new_product_settings['new_product_sticker_animation_delay']); ?>" placeholder='Delay'/>
			<p class="description"><?php esc_html_e( 'Set animation duration (seconds) to complete the animation.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function enable_new_product_schedule_sticker() {
		?>
			<select id='enable_new_product_schedule_sticker'
					name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[enable_new_product_schedule_sticker]">
				<option value='yes'
					<?php selected(!empty($this->new_product_settings['enable_new_product_schedule_sticker']) ? esc_attr( $this->new_product_settings['enable_new_product_schedule_sticker'] ) : 'no', 'yes', true); ?>>
					<?php esc_html_e('Yes', 'woo-stickers-by-webline'); ?>
				</option>
				<option value='no'
					<?php selected(!empty($this->new_product_settings['enable_new_product_schedule_sticker']) ? esc_attr( $this->new_product_settings['enable_new_product_schedule_sticker'] ) : 'no', 'no', true); ?>>
					<?php esc_html_e('No', 'woo-stickers-by-webline'); ?>
				</option>
			</select>
			<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as NEW in WooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	
    public function new_product_schedule_sticker_date_time_start(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="new_product_schedule_start_sticker_date_time" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_schedule_start_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->new_product_settings['new_product_schedule_start_sticker_date_time'] )) ?
                esc_attr( $this->new_product_settings['new_product_schedule_start_sticker_date_time'] ) : esc_attr($formatted_date_time) ); ?>"
                />
            <p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function new_product_schedule_sticker_date_time_end(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="new_product_schedule_end_sticker_date_time" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_schedule_end_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->new_product_settings['new_product_schedule_end_sticker_date_time'] )) ?
                esc_attr( $this->new_product_settings['new_product_schedule_end_sticker_date_time'] ) : esc_attr($formatted_date_time ) ); ?>"
                 />
            <p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function new_product_schedule_sticker_option_callback() {        
    ?>
        <div class="woo_opt new_product_schedule_sticker_option" id="image_opt_sch">
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if(esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ) == 'image_schedule' || esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ) == '') { echo "checked"; } ?> <?php checked(esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ) ); ?>/>
            <label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if(esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ) == 'text_schedule') { echo "checked"; } ?> <?php checked( esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ) ); ?>/>
            <label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
            <input type="hidden" class="wli_product_schedule_option" id="new_product_schedule_sticker_option" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_schedule_sticker_option]" value="<?php if(esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ) == '') { echo 'image_schedule'; } else { echo esc_attr( $this->new_product_settings['new_product_schedule_sticker_option'] ); } ?>"/>
            <p class="description"><?php esc_html_e( 'Select an option for the scheduled sticker.', 'woo-stickers-by-webline' );?></p>
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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url_sch = '';
		if ( ! empty( $this->new_product_settings['new_product_schedule_custom_sticker'] ) ) {
			$image_url_sch = esc_url( $this->new_product_settings['new_product_schedule_custom_sticker'] );
		}

		// Image tag (with or without src) — all attributes escaped.
		if ( ! $image_url_sch ) {
			echo '<img class="new_product_schedule_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="new_product_schedule_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr($image_url_sch)
			);
		}

		// Hidden input + buttons — attributes and text escaped.
		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[new_product_schedule_custom_sticker]" id="new_product_schedule_custom_sticker" value="%2$s" />' .
			' <button class="upload_img_btn_sch button" id="upload_img_btn_sch">%3$s</button>' .
			' <button class="remove_img_btn_sch button" id="remove_img_btn_sch">%4$s</button>',
			esc_attr( $this->new_product_settings_key ),
			esc_attr( $image_url_sch ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// Inline script produced by a trusted internal method.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- safe inline JS from internal generator.
		echo $this->custom_sticker_script_sch( 'new_product_schedule_custom_sticker' );
		?>

		<p class="description">
			<?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?>
		</p>
		<?php
	}


	public function new_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="new_product_schedule_sticker_image_width" placeholder="width" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_schedule_sticker_image_width]" <?php if ( isset( $this->new_product_settings['new_product_schedule_sticker_image_width'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_schedule_sticker_image_width'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="new_product_schedule_sticker_image_height" placeholder="height" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_schedule_sticker_image_height]" <?php if ( isset( $this->new_product_settings['new_product_schedule_sticker_image_height'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_schedule_sticker_image_height'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_schedule_custom_text() {
		?>
		<input type="text" id="new_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_schedule_custom_text]" value="<?php echo esc_attr( $this->new_product_settings['new_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function enable_new_schedule_product_style() {
		?>
		<select id='enable_new_schedule_product_style'
			name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[enable_new_schedule_product_style]">
			<option value='ribbon'
				<?php selected( esc_attr( $this->new_product_settings['enable_new_schedule_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( esc_attr( $this->new_product_settings['enable_new_schedule_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function new_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="new_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_schedule_custom_text_fontcolor]" value="<?php echo ($this->new_product_settings['new_product_schedule_custom_text_fontcolor']) ? esc_attr( $this->new_product_settings['new_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function new_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="new_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->new_product_settings['new_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function new_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="new_product_schedule_text_padding_top" class="small-text" placeholder="Top" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_schedule_text_padding_top]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_top'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_schedule_text_padding_top'] ) . '"'; } ?> />
		<input type="number" id="new_product_schedule_text_padding_right" class="small-text" placeholder="Right" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_schedule_text_padding_right]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_right'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_schedule_text_padding_right'] ) . '"'; } ?> />
		<input type="number" id="new_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_schedule_text_padding_bottom]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_bottom'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_schedule_text_padding_bottom'] ) . '"'; } ?> style="width: auto; max-width: 70px;" />		
		<input type="number" id="new_product_schedule_text_padding_left" class="small-text" placeholder="Left" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_schedule_text_padding_left]" <?php if ( isset( $this->new_product_settings['new_product_schedule_text_padding_left'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_schedule_text_padding_left'] ) . '"'; } ?> />
	
		<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
	
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
		<input type="number" class="small-text" id="new_product_sticker_image_width" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_sticker_image_width]" <?php if ( isset( $this->new_product_settings['new_product_sticker_image_width'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_sticker_image_width'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="new_product_sticker_image_height" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_sticker_image_height]" <?php if ( isset( $this->new_product_settings['new_product_sticker_image_height'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_sticker_image_height'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if(esc_attr( $this->new_product_settings['new_product_option'] ) == 'image' || esc_attr( $this->new_product_settings['new_product_option'] ) == '') { echo "checked"; } ?> <?php checked(esc_attr( $this->new_product_settings['new_product_option'] ) ); ?>/>
			<label for="image"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if(esc_attr( $this->new_product_settings['new_product_option'] ) == 'text') { echo "checked"; } ?> <?php checked( esc_attr( $this->new_product_settings['new_product_option'] ) ); ?>/>
			<label for="text"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="new_product_option" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_option]" value="<?php if(esc_attr( $this->new_product_settings['new_product_option'] ) == '') { echo 'image'; } else { echo esc_attr( $this->new_product_settings['new_product_option'] ); } ?>"/>
			<p class="description"><?php esc_html_e( 'Select Image or Text for the custom sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="new_product_custom_text" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_custom_text]" value="<?php echo esc_attr( $this->new_product_settings['new_product_custom_text'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[enable_new_product_style]">
			<option value='ribbon'
				<?php selected( esc_attr( $this->new_product_settings['enable_new_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( esc_attr( $this->new_product_settings['enable_new_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select custom sticker type to show on New Products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="new_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_custom_text_fontcolor]" value="<?php echo ($this->new_product_settings['new_product_custom_text_fontcolor']) ? esc_attr( $this->new_product_settings['new_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="new_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->new_product_settings_key );?>[new_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->new_product_settings['new_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" id="new_product_text_padding_top" class="small-text" placeholder="Top" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_text_padding_top]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_top'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_text_padding_top'] ) . '"'; } ?> />
		<input type="number" id="new_product_text_padding_right" class="small-text" placeholder="Right" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_text_padding_right]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_right'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_text_padding_right'] ) . '"'; } ?> />
		<input type="number" id="new_product_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_text_padding_bottom]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_bottom'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_text_padding_bottom'] ) . '"'; } ?> style="width: auto; max-width: 70px;" />		
		<input type="number" id="new_product_text_padding_left" class="small-text" placeholder="Left" name="<?php echo esc_attr( $this->new_product_settings_key ); ?>[new_product_text_padding_left]" <?php if ( isset( $this->new_product_settings['new_product_text_padding_left'] ) ) { echo 'value="' . esc_attr( $this->new_product_settings['new_product_text_padding_left'] ) . '"'; } ?> />

		<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>

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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url = '';
		if ( ! empty( $this->new_product_settings['new_product_custom_sticker'] ) ) {
			$image_url = esc_url( $this->new_product_settings['new_product_custom_sticker'] );
		}

		// Image (with or without src)
		if ( ! $image_url ) {
			echo '<img class="new_product_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="new_product_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr( $image_url )
			);
		}

		// Hidden input and buttons
		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[new_product_custom_sticker]" id="new_product_custom_sticker" value="%2$s" /> ' .
			'<button class="upload_img_btn button">%3$s</button> ' .
			'<button class="remove_img_btn button">%4$s</button>',
			esc_attr( $this->new_product_settings_key ),
			esc_attr( $image_url ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline JS from internal generator.
		echo $this->custom_sticker_script( 'new_product_custom_sticker' );
		?>
		<p class="description"><?php esc_html_e( 'Upload your own custom sticker image instead of using WooStickers default.', 'woo-stickers-by-webline' ); ?></p>
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
			name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[enable_sale_product_sticker]">
			<option value='yes'
				<?php selected( esc_attr( $this->sale_product_settings['enable_sale_product_sticker'] ), 'yes',true );?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( esc_attr( $this->sale_product_settings['enable_sale_product_sticker'] ), 'no',true );?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Enable or disable sticker display for products marked as on Sale in WooCommerce.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_position]">
			<option value='left'
				<?php selected( esc_attr( $this->sale_product_settings['sale_product_position'] ), 'left',true );?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( esc_attr( $this->sale_product_settings['sale_product_position'] ), 'right',true );?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select the position of the sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="sale_product_sticker_top" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_sticker_top]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_top']); ?>" />
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="sale_product_sticker_left_right" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_sticker_left_right]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_left_right']); ?>" />
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}

	public function sale_product_sticker_rotate() {
		?>
			<input type="number" class="small-text" id="sale_product_sticker_rotate" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_sticker_rotate]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_rotate']); ?>" />
			<p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}

	public function sale_product_sticker_animation_type() {
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
						echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
					}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select the animation type for the sticker.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function sale_product_sticker_animation_scale() {
		?>
			<div id="zoominout-options-sale-global" style="display: none;">
				<input type="number" id="sale_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo esc_attr($this->sale_product_settings_key );?>[sale_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php esc_html_e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			</div>
		<?php		
	}

	public function sale_product_sticker_animation_direction() {
		?>
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
						echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
					}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select the animation direction.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function sale_product_sticker_animation_iteration_count() {
		?>
			<input type="text" id="sale_product_sticker_animation_iteration_count" step="any" name="<?php echo esc_attr($this->sale_product_settings_key);?>[sale_product_sticker_animation_iteration_count]" 
			value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_animation_iteration_count']); ?>" placeholder='Iteration Count'/>
			<p class="description"><?php esc_html_e( 'Set number of times animation repeats. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sale_product_sticker_animation_delay() {
		?>
			<input type="number" id="sale_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo esc_attr($this->sale_product_settings_key);?>[sale_product_sticker_animation_delay]" 
			value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_animation_delay']); ?>" placeholder='Delay'/>
			<p class="description"><?php esc_html_e( 'Set animation duration (seconds) to complete the animation.', 'woo-stickers-by-webline' );?></p>
		<?php		
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
		<input type="number" class="small-text" id="sale_product_sticker_image_width" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_sticker_image_width]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_image_width']); ?>" />
		<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="sale_product_sticker_image_height" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_sticker_image_height]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_sticker_image_height']); ?>" />
		<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if(esc_attr( $this->sale_product_settings['sale_product_option'] ) == 'image' || esc_attr( $this->sale_product_settings['sale_product_option'] ) == '') { echo "checked"; } ?> <?php checked(esc_attr( $this->sale_product_settings['sale_product_option'] ) ); ?>/>
			<label for="image"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if(esc_attr( $this->sale_product_settings['sale_product_option'] ) == 'text') { echo "checked"; } ?> <?php checked( esc_attr( $this->sale_product_settings['sale_product_option'] ) ); ?>/>
			<label for="text"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="sale_product_option" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_option]" value="<?php if(esc_attr( $this->sale_product_settings['sale_product_option'] ) == '') { echo 'image'; } else { echo esc_attr( $this->sale_product_settings['sale_product_option'] ); } ?>"/>
			<p class="description"><?php esc_html_e( 'Select Image or Text for the custom sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="sale_product_custom_text" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_custom_text]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_custom_text']); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as custom sticker on sale products.', 'woo-stickers-by-webline' );?></p>
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
				name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[enable_sale_product_style]">
				<option value='ribbon'
					<?php selected( esc_attr( $this->sale_product_settings['enable_sale_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
				<option value='round'
					<?php selected( esc_attr( $this->sale_product_settings['enable_sale_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
			</select>
			<p class="description"><?php esc_html_e( 'Select custom sticker type to show on Sale Products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="sale_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_custom_text_fontcolor]" value="<?php echo ($this->sale_product_settings['sale_product_custom_text_fontcolor']) ? esc_attr( $this->sale_product_settings['sale_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on sale products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="sale_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on sale products.', 'woo-stickers-by-webline' );?></p>
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
			<input type="number" class="small-text" id="sale_product_text_padding_top" class="small-text" placeholder="Top" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_text_padding_top]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_text_padding_top'] ); ?>" />
			<input type="number" class="small-text" id="sale_product_text_padding_right" class="small-text" placeholder="Right" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_text_padding_right]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_text_padding_right'] ); ?>" />
			<input type="number" class="small-text" id="sale_product_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_text_padding_bottom]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_text_padding_bottom'] ); ?>" />		
			<input type="number" class="small-text" id="sale_product_text_padding_left" class="small-text" placeholder="Left" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_text_padding_left]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_text_padding_left'] ); ?>" />
			<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function enable_sale_product_schedule_sticker() {
		?>
			<select id='enable_sale_product_schedule_sticker'
				name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[enable_sale_product_schedule_sticker]">
				<option value='yes'
					<?php selected( !empty($this->sale_product_settings['enable_sale_product_schedule_sticker']) ? esc_attr( $this->sale_product_settings['enable_sale_product_schedule_sticker'] ) : 'no', 'yes', true ); ?>>
					<?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?>
				</option>
				<option value='no'
					<?php selected( !empty($this->sale_product_settings['enable_sale_product_schedule_sticker']) ? esc_attr( $this->sale_product_settings['enable_sale_product_schedule_sticker'] ) : 'no', 'no', true ); ?>>
					<?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?>
				</option>
			</select>

			<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as Sale in WooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php	
	}
	
	public function sale_product_schedule_sticker_date_time_start(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="sale_product_schedule_start_sticker_date_time" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_schedule_start_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->sale_product_settings['sale_product_schedule_start_sticker_date_time'] )) ?
                esc_attr( $this->sale_product_settings['sale_product_schedule_start_sticker_date_time'] ) : esc_attr($formatted_date_time) ); ?>"
                />
            <p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function sale_product_schedule_sticker_date_time_end(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="sale_product_schedule_end_sticker_date_time" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_schedule_end_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->sale_product_settings['sale_product_schedule_end_sticker_date_time'] )) ?
                esc_attr( $this->sale_product_settings['sale_product_schedule_end_sticker_date_time'] ) : esc_attr($formatted_date_time ) ); ?>"
                 />
            <p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function sale_product_schedule_sticker_option_callback() {        
    ?>
        <div class="woo_opt sale_product_schedule_sticker_option" id="image_opt_sch">
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if(esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ) == 'image_schedule' || esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ) == '') { echo "checked"; } ?> <?php checked(esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ) ); ?>/>
            <label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if(esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ) == 'text_schedule') { echo "checked"; } ?> <?php checked( esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ) ); ?>/>
            <label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
            <input type="hidden" class="wli_product_schedule_option" id="sale_product_schedule_sticker_option" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_schedule_sticker_option]" value="<?php if(esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ) == '') { echo 'image_schedule'; } else { echo esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_option'] ); } ?>"/>
            <p class="description"><?php esc_html_e( 'Select an option for the scheduled sticker.', 'woo-stickers-by-webline' );?></p>
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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url = '';
		if ( ! empty( $this->sale_product_settings['sale_product_schedule_custom_sticker'] ) ) {
			$image_url = esc_url( $this->sale_product_settings['sale_product_schedule_custom_sticker'] );
		}

		if ( ! $image_url ) {
			echo '<img class="sale_product_schedule_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="sale_product_schedule_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr( $image_url )
			);
		}

		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[sale_product_schedule_custom_sticker]" id="sale_product_schedule_custom_sticker" value="%2$s" /> ' .
			'<button class="upload_img_btn_sch button" id="upload_img_btn_sch">%3$s</button> ' .
			'<button class="remove_img_btn_sch button" id="remove_img_btn_sch">%4$s</button>',
			esc_attr( $this->sale_product_settings_key ),
			esc_attr( $image_url ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline JS from internal generator.
		echo $this->custom_sticker_script_sch( 'sale_product_schedule_custom_sticker' );
		?>
		<p class="description"><?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}

	
	public function sale_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="sale_product_schedule_sticker_image_width" placeholder="width" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_schedule_sticker_image_width]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_sticker_image_width'] ) ) { echo 'value="' . esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_image_width'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="sale_product_schedule_sticker_image_height" placeholder="height" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_schedule_sticker_image_height]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_sticker_image_height'] ) ) { echo 'value="' . esc_attr( $this->sale_product_settings['sale_product_schedule_sticker_image_height'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_custom_text() {
		?>
		<input type="text" id="sale_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_schedule_custom_text]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function enable_sale_schedule_product_style() {
		?>
		<select id='enable_sale_schedule_product_style'
			name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[enable_sale_schedule_product_style]">
			<option value='ribbon'
				<?php selected( esc_attr( $this->sale_product_settings['enable_sale_schedule_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( esc_attr( $this->sale_product_settings['enable_sale_schedule_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sale_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="sale_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_schedule_custom_text_fontcolor]" value="<?php echo (esc_attr( $this->sale_product_settings['sale_product_schedule_custom_text_fontcolor'] )) ? esc_attr( $this->sale_product_settings['sale_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="sale_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sale_product_settings_key );?>[sale_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->sale_product_settings['sale_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sale_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="sale_product_schedule_text_padding_top" class="small-text" placeholder="Top" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_schedule_text_padding_top]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_top'] ) ) { echo 'value="' . esc_attr( $this->sale_product_settings['sale_product_schedule_text_padding_top'] ) . '"'; } ?> />
		<input type="number" id="sale_product_schedule_text_padding_right" class="small-text" placeholder="Right" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_schedule_text_padding_right]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_right'] ) ) { echo 'value="' . esc_attr( $this->sale_product_settings['sale_product_schedule_text_padding_right'] ) . '"'; } ?> />
		<input type="number" id="sale_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_schedule_text_padding_bottom]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_bottom'] ) ) { echo 'value="' . esc_attr( $this->sale_product_settings['sale_product_schedule_text_padding_bottom'] ) . '"'; } ?> style="width: auto; max-width: 70px;" />		
		<input type="number" id="sale_product_schedule_text_padding_left" class="small-text" placeholder="Left" name="<?php echo esc_attr( $this->sale_product_settings_key ); ?>[sale_product_schedule_text_padding_left]" <?php if ( isset( $this->sale_product_settings['sale_product_schedule_text_padding_left'] ) ) { echo 'value="' . esc_attr( $this->sale_product_settings['sale_product_schedule_text_padding_left'] ) . '"'; } ?> />
	
		<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
	
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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url = '';
		if ( ! empty( $this->sale_product_settings['sale_product_custom_sticker'] ) ) {
			$image_url = esc_url( $this->sale_product_settings['sale_product_custom_sticker'] );
		}

		if ( ! $image_url ) {
			echo '<img class="sale_product_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="sale_product_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr( $image_url )
			);
		}

		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[sale_product_custom_sticker]" id="sale_product_custom_sticker" value="%2$s" /> ' .
			'<button class="upload_img_btn button">%3$s</button> ' .
			'<button class="remove_img_btn button">%4$s</button>',
			esc_attr( $this->sale_product_settings_key ),
			esc_attr( $image_url ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline JS from internal generator.
		echo $this->custom_sticker_script( 'sale_product_custom_sticker' );
		?>
		<p class="description"><?php esc_html_e( 'Upload your own custom sticker image instead of using WooStickers default.', 'woo-stickers-by-webline' ); ?></p>
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
			name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[enable_sold_product_sticker]">
			<option value='yes'
				<?php selected( esc_attr( $this->sold_product_settings['enable_sold_product_sticker'] ), 'yes',true );?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( esc_attr( $this->sold_product_settings['enable_sold_product_sticker'] ), 'no',true );?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Enable or disable sticker display for products marked as Sold Out in WooCommerce.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_position]">
			<option value='left'
				<?php selected( esc_attr( $this->sold_product_settings['sold_product_position'] ), 'left',true );?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( esc_attr( $this->sold_product_settings['sold_product_position'] ), 'right',true );?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select the position of the sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="sold_product_sticker_left_right" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_sticker_left_right]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_left_right']); ?>" />
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
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
		<input type="number" class="small-text" id="sold_product_sticker_top" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_sticker_top]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_top']); ?>" />
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sold_product_sticker_rotate() {
		?>
					<input type="number" class="small-text" id="sold_product_sticker_rotate" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_sticker_rotate]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_rotate']); ?>" />
		<p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}
	
	public function sold_product_sticker_animation_type() {
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
						echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
					}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select the animation type for the sticker.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function sold_product_sticker_animation_scale() {
		?>
			<div id="zoominout-options-sold-global" style="display: none;">
				<input type="number" id="sold_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo esc_attr($this->sold_product_settings_key );?>[sold_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php esc_html_e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			</div>
		<?php		
	}

	public function sold_product_sticker_animation_direction() {
		?>
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
						echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
					}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select the animation direction.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function sold_product_sticker_animation_iteration_count() {
		?>
			<input type="text" id="sold_product_sticker_animation_iteration_count" step="any" name="<?php echo esc_attr($this->sold_product_settings_key);?>[sold_product_sticker_animation_iteration_count]" 
			value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_animation_iteration_count']); ?>" placeholder='Iteration Count'/>
			<p class="description"><?php esc_html_e( 'Set number of times animation repeats. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sold_product_sticker_animation_delay() {
		?>
			<input type="number" id="sold_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo esc_attr($this->sold_product_settings_key);?>[sold_product_sticker_animation_delay]" 
			value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_animation_delay']); ?>" placeholder='Delay'/>
			<p class="description"><?php esc_html_e( 'Set animation duration (seconds) to complete the animation.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function enable_sold_product_schedule_sticker() {
		?>
			<select id='enable_sold_product_schedule_sticker'
					name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[enable_sold_product_schedule_sticker]">
				<option value='yes'
					<?php selected( !empty($this->sold_product_settings['enable_sold_product_schedule_sticker']) ? esc_attr( $this->sold_product_settings['enable_sold_product_schedule_sticker'] ) : 'no', 'yes', true ); ?>>
					<?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?>
				</option>
				<option value='no'
					<?php selected( !empty($this->sold_product_settings['enable_sold_product_schedule_sticker']) ? esc_attr( $this->sold_product_settings['enable_sold_product_schedule_sticker'] ) : 'no', 'no', true ); ?>>
					<?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?>
				</option>
			</select>

			<p class="description"><?php esc_html_e( 'Enable or disable scheduled stickers for products marked as Sold in WooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php	
	}
	
	public function sold_product_schedule_sticker_date_time_start(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="sold_product_schedule_start_sticker_date_time" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_schedule_start_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->sold_product_settings['sold_product_schedule_start_sticker_date_time'] )) ?
                esc_attr( $this->sold_product_settings['sold_product_schedule_start_sticker_date_time'] ) : esc_attr($formatted_date_time) ); ?>"
                />
            <p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function sold_product_schedule_sticker_date_time_end(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="sold_product_schedule_end_sticker_date_time" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_schedule_end_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->sold_product_settings['sold_product_schedule_end_sticker_date_time'] )) ?
                esc_attr( $this->sold_product_settings['sold_product_schedule_end_sticker_date_time'] ) : esc_attr($formatted_date_time ) ); ?>"
                 />
            <p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function sold_product_schedule_sticker_option_callback() {        
    ?>
        <div class="woo_opt sold_product_schedule_sticker_option" id="image_opt_sch">
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if(esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ) == 'image_schedule' || esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ) == '') { echo "checked"; } ?> <?php checked(esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ) ); ?>/>
            <label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if(esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ) == 'text_schedule') { echo "checked"; } ?> <?php checked( esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ) ); ?>/>
            <label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
            <input type="hidden" class="wli_product_schedule_option" id="sold_product_schedule_sticker_option" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_schedule_sticker_option]" value="<?php if(esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ) == '') { echo 'image_schedule'; } else { echo esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_option'] ); } ?>"/>
            <p class="description"><?php esc_html_e( 'Select an option for the scheduled sticker.', 'woo-stickers-by-webline' );?></p>
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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url = '';
		if ( ! empty( $this->sold_product_settings['sold_product_schedule_custom_sticker'] ) ) {
			$image_url = esc_url( $this->sold_product_settings['sold_product_schedule_custom_sticker'] );
		}

		if ( ! $image_url ) {
			echo '<img class="sold_product_schedule_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="sold_product_schedule_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr( $image_url )
			);
		}

		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[sold_product_schedule_custom_sticker]" id="sold_product_schedule_custom_sticker" value="%2$s" /> ' .
			'<button class="upload_img_btn button" id="upload_img_btn_sch">%3$s</button> ' .
			'<button class="remove_img_btn button" id="remove_img_btn_sch">%4$s</button>',
			esc_attr( $this->sold_product_settings_key ),
			esc_attr( $image_url ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline JS from internal generator.
		echo $this->custom_sticker_script( 'sold_product_schedule_custom_sticker' );
		?>
		<p class="description"><?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}
	
	public function sold_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="sold_product_schedule_sticker_image_width" placeholder="width" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_schedule_sticker_image_width]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_sticker_image_width'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_image_width'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="sold_product_schedule_sticker_image_height" placeholder="height" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_schedule_sticker_image_height]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_sticker_image_height'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_schedule_sticker_image_height'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_custom_text() {
		?>
		<input type="text" id="sold_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_schedule_custom_text]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function enable_sold_schedule_product_style() {
		?>
		<select id='enable_sold_schedule_product_style'
			name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[enable_sold_schedule_product_style]">
			<option value='ribbon'
				<?php selected( esc_attr( $this->sold_product_settings['enable_sold_schedule_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( esc_attr( $this->sold_product_settings['enable_sold_schedule_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function sold_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="sold_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_schedule_custom_text_fontcolor]" value="<?php echo (esc_attr( $this->sold_product_settings['sold_product_schedule_custom_text_fontcolor'] )) ? esc_attr( $this->sold_product_settings['sold_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="sold_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function sold_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="sold_product_schedule_text_padding_top" class="small-text" placeholder="Top" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_schedule_text_padding_top]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_top'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_schedule_text_padding_top'] ) . '"'; } ?> />
		<input type="number" id="sold_product_schedule_text_padding_right" class="small-text" placeholder="Right" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_schedule_text_padding_right]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_right'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_schedule_text_padding_right'] ) . '"'; } ?> />
		<input type="number" id="sold_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_schedule_text_padding_bottom]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_bottom'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_schedule_text_padding_bottom'] ) . '"'; } ?> style="width: auto; max-width: 70px;" />		
		<input type="number" id="sold_product_schedule_text_padding_left" class="small-text" placeholder="Left" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_schedule_text_padding_left]" <?php if ( isset( $this->sold_product_settings['sold_product_schedule_text_padding_left'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_schedule_text_padding_left'] ) . '"'; } ?> />
	
		<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>
	
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
		<input type="number" class="small-text" id="sold_product_sticker_image_width" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_sticker_image_width]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_image_width']); ?>" />
		<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="sold_product_sticker_image_height" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_sticker_image_height]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_sticker_image_height']); ?>" />
		<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="image" value="image" <?php if(esc_attr( $this->sold_product_settings['sold_product_option'] ) == 'image' || esc_attr( $this->sold_product_settings['sold_product_option'] ) == '') { echo "checked"; } ?> <?php checked(esc_attr( $this->sold_product_settings['sold_product_option'] ) ); ?>/>
			<label for="image"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if(esc_attr( $this->sold_product_settings['sold_product_option'] ) == 'text') { echo "checked"; } ?> <?php checked( esc_attr( $this->sold_product_settings['sold_product_option'] ) ); ?>/>
			<label for="text"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="sold_product_option" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_option]" value="<?php if(esc_attr( $this->sold_product_settings['sold_product_option'] ) == '') { echo 'image'; } else { echo esc_attr( $this->sold_product_settings['sold_product_option'] ); } ?>"/>
			<p class="description"><?php esc_html_e( 'Select Image or Text for the custom sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="sold_product_custom_text" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_custom_text]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_custom_text'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as custom sticker on products, Leave it blank if you use WooStickers default.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[enable_sold_product_style]">
			<option value='ribbon'
				<?php selected( esc_attr( $this->sold_product_settings['enable_sold_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( esc_attr( $this->sold_product_settings['enable_sold_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select custom sticker type to show on Sold Products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="sold_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_custom_text_fontcolor]" value="<?php echo (esc_attr( $this->sold_product_settings['sold_product_custom_text_fontcolor'] )) ? esc_attr( $this->sold_product_settings['sold_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on sold products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="sold_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr( $this->sold_product_settings_key );?>[sold_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->sold_product_settings['sold_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on sold products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" id="sold_product_text_padding_top" class="small-text" placeholder="Top" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_text_padding_top]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_top'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_text_padding_top'] ) . '"'; } ?> />
		<input type="number" id="sold_product_text_padding_right" class="small-text" placeholder="Right" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_text_padding_right]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_right'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_text_padding_right'] ) . '"'; } ?> />
		<input type="number" id="sold_product_text_padding_bottom" class="small-text" placeholder="Bottom" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_text_padding_bottom]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_bottom'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_text_padding_bottom'] ) . '"'; } ?> style="width: auto; max-width: 70px;" />		
		<input type="number" id="sold_product_text_padding_left" class="small-text" placeholder="Left" name="<?php echo esc_attr( $this->sold_product_settings_key ); ?>[sold_product_text_padding_left]" <?php if ( isset( $this->sold_product_settings['sold_product_text_padding_left'] ) ) { echo 'value="' . esc_attr( $this->sold_product_settings['sold_product_text_padding_left'] ) . '"'; } ?> />

		<p class="description"><?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' );?></p>

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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url = '';
		if ( ! empty( $this->sold_product_settings['sold_product_custom_sticker'] ) ) {
			$image_url = esc_url( $this->sold_product_settings['sold_product_custom_sticker'] );
		}

		if ( ! $image_url ) {
			echo '<img class="sold_product_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="sold_product_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr( $image_url )
			);
		}

		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[sold_product_custom_sticker]" id="sold_product_custom_sticker" value="%2$s" /> ' .
			'<button class="upload_img_btn_sch button">%3$s</button> ' .
			'<button class="remove_img_btn_sch button">%4$s</button>',
			esc_attr( $this->sold_product_settings_key ),
			esc_attr( $image_url ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline JS from internal generator.
		echo $this->custom_sticker_script_sch( 'sold_product_custom_sticker' );
		?>
		<p class="description"><?php esc_html_e( 'Upload your own custom sticker image instead of using WooStickers default.', 'woo-stickers-by-webline' ); ?></p>
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
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[enable_cust_product_sticker]">
			<option value='yes'
				<?php selected( esc_attr( $this->cust_product_settings['enable_cust_product_sticker'] ), 'yes',true );?>><?php esc_html_e( 'Yes', 'woo-stickers-by-webline' );?></option>
			<option value='no'
				<?php selected( esc_attr( $this->cust_product_settings['enable_cust_product_sticker'] ), 'no',true );?>><?php esc_html_e( 'No', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Enable or disable custom sticker display for products in WooCommerce.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_position]">
			<option value='left'
				<?php selected( esc_attr( $this->cust_product_settings['cust_product_position'] ), 'left',true );?>><?php esc_html_e( 'Left', 'woo-stickers-by-webline' );?></option>
			<option value='right'
				<?php selected( esc_attr( $this->cust_product_settings['cust_product_position'] ), 'right',true );?>><?php esc_html_e( 'Right', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select the position of the custom sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="cust_product_sticker_left_right" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_sticker_left_right]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_left_right'] ) ) { echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_sticker_left_right'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from left or right. Leave empty for default.', 'woo-stickers-by-webline' ); ?></p>
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
		<input type="number" class="small-text" id="cust_product_sticker_top" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_sticker_top]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_top'] ) ) { echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_sticker_top'] ) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set sticker position (px) from top. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function cust_product_sticker_rotate() {
		?>
		<input type="number" class="small-text" id="cust_product_sticker_rotate" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_sticker_rotate]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_sticker_rotate']); ?>" />
		<p class="description"><?php esc_html_e( 'Specify the degree to rotate the sticker.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}
		
	public function cust_product_sticker_animation_type() {
		?>
			<select id="cust_product_sticker_animation_type" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_sticker_animation_type]">
				<?php
					$border_types = array(
						'none' => 'None',
						'spin' => 'Spin',
						'swing' => 'Swing',
						'zoominout' => 'Zoom In / Out',
						'leftright' => 'Left-Right',
						'updown' => 'Up-Down',
					);
					$current_value = esc_attr( $this->cust_product_settings['cust_product_sticker_animation_type'] );
					foreach ( $border_types as $value => $label ) {
						$selected = ( $current_value === $value ) ? 'selected' : '';
						echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
					}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select the animation type for the sticker.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function cust_product_sticker_animation_scale() {
		?>
			<div id="zoominout-options-cust-global" style="display: none;">
				<input type="number" id="cust_product_sticker_animation_scale" step="any" class="small-text" name="<?php echo esc_attr($this->cust_product_settings_key );?>[cust_product_sticker_animation_scale]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_sticker_animation_scale'] ); ?>" placeholder='Scale'/>
				<p class="description"><?php esc_html_e( 'Specify scale for Zoom In / Out animation (Leave empty to use default)', 'woo-stickers-by-webline' );?></p>
			</div>
		<?php		
	}

	public function cust_product_sticker_animation_direction() {
		?>
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
						echo "<option value='" . esc_attr( $value ) . "' " . esc_attr( $selected ) . ">" . esc_html( $label ) . "</option>";
					}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select the animation direction.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}

	public function cust_product_sticker_animation_iteration_count() {
		?>
			<input type="text" id="cust_product_sticker_animation_iteration_count" step="any" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_sticker_animation_iteration_count]" 
			value="<?php echo esc_attr( $this->cust_product_settings['cust_product_sticker_animation_iteration_count']); ?>" placeholder='Iteration Count'/>
			<p class="description"><?php esc_html_e( 'Set number of times animation repeats. Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function cust_product_sticker_animation_delay() {
		?>
			<input type="number" id="cust_product_sticker_animation_delay" step="any" class="small-text" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_sticker_animation_delay]" 
			value="<?php echo esc_attr( $this->cust_product_settings['cust_product_sticker_animation_delay']); ?>" placeholder='Delay'/>
			<p class="description"><?php esc_html_e( 'Set animation duration (seconds) to complete the animation.', 'woo-stickers-by-webline' );?></p>
		<?php		
	}
	
	public function enable_cust_product_schedule_sticker() {
		?>
		<select id='enable_cust_product_schedule_sticker'
				name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[enable_cust_product_schedule_sticker]">
			<option value='yes'
				<?php selected( !empty($this->cust_product_settings['enable_cust_product_schedule_sticker']) ? esc_attr( $this->cust_product_settings['enable_cust_product_schedule_sticker'] ) : 'no', 'yes', true ); ?>>
				<?php esc_html_e( 'Yes', 'woo-stickers-by-webline' ); ?>
			</option>
			<option value='no'
				<?php selected( !empty($this->cust_product_settings['enable_cust_product_schedule_sticker']) ? esc_attr( $this->cust_product_settings['enable_cust_product_schedule_sticker'] ) : 'no', 'no', true ); ?>>
				<?php esc_html_e( 'No', 'woo-stickers-by-webline' ); ?>
			</option>
		</select>

		<p class="description"><?php esc_html_e( 'Enable or disable scheduled custom sticker display for products in WooCommerce.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_sticker_date_time_start(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_start_sticker_date_time" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_schedule_start_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->cust_product_settings['cust_product_schedule_start_sticker_date_time'] )) ?
                esc_attr( $this->cust_product_settings['cust_product_schedule_start_sticker_date_time'] ) : esc_attr($formatted_date_time) ); ?>"
                />
            <p class="description"><?php esc_html_e( 'Set the start date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function cust_product_schedule_sticker_date_time_end(){
        $format = 'Y-m-d\TH:i';
        $current_timestamp = current_time('timestamp');
        $formatted_date_time = gmdate($format, $current_timestamp);
        ?>
            <input type="datetime-local" class="custom_date_pkr" id="cust_product_schedule_end_sticker_date_time" name="<?php echo esc_attr( $this->cust_product_settings_key );?>[cust_product_schedule_end_sticker_date_time]"
                value="<?php echo (esc_attr( !empty($this->cust_product_settings['cust_product_schedule_end_sticker_date_time'] )) ?
                esc_attr( $this->cust_product_settings['cust_product_schedule_end_sticker_date_time'] ) : esc_attr($formatted_date_time ) ); ?>"
                 />
            <p class="description"><?php esc_html_e( 'Set the end date and time for the sticker schedule', 'woo-stickers-by-webline' );?></p>
            <?php
    }
 
    public function cust_product_schedule_sticker_option_callback() {        
    ?>
        <div class="woo_opt cust_product_schedule_sticker_option" id="image_opt_sch">
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="image_schedule" value="image_schedule" <?php if(esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ) == 'image_schedule' || esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ) == '') { echo "checked"; } ?> <?php checked(esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ) ); ?>/>
            <label for="image_schedule"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
            <input type="radio" name="stickeroption_sch" class="wli-woosticker-radio-schedule" id="text_schedule" value="text_schedule" <?php if(esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ) == 'text_schedule') { echo "checked"; } ?> <?php checked( esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ) ); ?>/>
            <label for="text_schedule"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
            <input type="hidden" class="wli_product_schedule_option" id="cust_product_schedule_sticker_option" name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_schedule_sticker_option]" value="<?php if(esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ) == '') { echo 'image_schedule'; } else { echo esc_attr( $this->cust_product_settings['cust_product_schedule_sticker_option'] ); } ?>"/>
            <p class="description"><?php esc_html_e( 'Select an option for the scheduled sticker.', 'woo-stickers-by-webline' );?></p>
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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url = '';
		if ( ! empty( $this->cust_product_settings['cust_product_schedule_custom_sticker'] ) ) {
			$image_url = esc_url( $this->cust_product_settings['cust_product_schedule_custom_sticker'] );
		}

		if ( ! $image_url ) {
			echo '<img class="cust_product_schedule_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="cust_product_schedule_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr( $image_url )
			);
		}

		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[cust_product_schedule_custom_sticker]" id="cust_product_schedule_custom_sticker" value="%2$s" /> ' .
			'<button class="upload_img_btn_sch button" id="upload_img_btn_sch">%3$s</button> ' .
			'<button class="remove_img_btn_sch button" id="remove_img_btn_sch">%4$s</button>',
			esc_attr( $this->cust_product_settings_key ),
			esc_attr( $image_url ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline JS from internal generator.
		echo $this->custom_sticker_script_sch( 'cust_product_schedule_custom_sticker' );
		?>
		<p class="description"><?php esc_html_e( 'Upload a custom scheduled sticker to replace the default WooSticker.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}
	
	public function cust_product_schedule_sticker_image_width() {
		?>
		<input type="number" class="small-text" id="cust_product_schedule_sticker_image_width" placeholder="width" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_schedule_sticker_image_width]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_sticker_image_width'] ) ) { echo 'value="' . esc_attr($this->cust_product_settings['cust_product_schedule_sticker_image_width']) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_sticker_image_height() {
		?>
		<input type="number" class="small-text" id="cust_product_schedule_sticker_image_height" placeholder="height" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_schedule_sticker_image_height]" <?php if ( isset( $this->cust_product_settings['cust_product_schedule_sticker_image_height'] ) ) { echo 'value="' . esc_attr($this->cust_product_settings['cust_product_schedule_sticker_image_height']) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set scheduled sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_custom_text() {
		?>
		<input type="text" id="cust_product_schedule_custom_text" placeholder="Enter the custom text" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_schedule_custom_text]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_schedule_custom_text'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as scheduled custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function enable_cust_schedule_product_style() {
		?>
		<select id='enable_cust_schedule_product_style'
			name="<?php echo esc_attr($this->cust_product_settings_key); ?>[enable_cust_schedule_product_style]">
			<option value='ribbon'
				<?php selected( esc_attr( $this->cust_product_settings['enable_cust_schedule_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( esc_attr( $this->cust_product_settings['enable_cust_schedule_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select custom sticker type to show on Scheduled New Products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function cust_product_schedule_custom_text_fontcolor() {
		?>
		<input type="text" id="cust_product_schedule_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_schedule_custom_text_fontcolor]" value="<?php echo ($this->cust_product_settings['cust_product_schedule_custom_text_fontcolor']) ? esc_attr( $this->cust_product_settings['cust_product_schedule_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}
	
	public function cust_product_schedule_custom_text_backcolor() {
		?>
		<input type="text" id="cust_product_schedule_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_schedule_custom_text_backcolor]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_schedule_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on new products.', 'woo-stickers-by-webline' );?></p>
		<?php
	}

	public function cust_product_schedule_custom_text_padding() {
		?>
		<input type="number" id="cust_product_schedule_text_padding_top" class="small-text" placeholder="Top" 
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_schedule_text_padding_top]" 
			<?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_top'] ) ) { 
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_schedule_text_padding_top'] ) . '"'; 
			} ?> 
		/>
		<input type="number" id="cust_product_schedule_text_padding_right" class="small-text" placeholder="Right" 
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_schedule_text_padding_right]" 
			<?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_right'] ) ) { 
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_schedule_text_padding_right'] ) . '"'; 
			} ?> 
		/>
		<input type="number" id="cust_product_schedule_text_padding_bottom" class="small-text" placeholder="Bottom" 
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_schedule_text_padding_bottom]" 
			<?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_bottom'] ) ) { 
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_schedule_text_padding_bottom'] ) . '"'; 
			} ?>  style="width: auto; max-width: 70px;"
		/>		
		<input type="number" id="cust_product_schedule_text_padding_left" class="small-text" placeholder="Left" 
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_schedule_text_padding_left]" 
			<?php if ( isset( $this->cust_product_settings['cust_product_schedule_text_padding_left'] ) ) { 
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_schedule_text_padding_left'] ) . '"'; 
			} ?> 
		/>

		<p class="description">
			<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>
		</p>
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
			<label for="image"><?php esc_html_e( 'Image', 'woo-stickers-by-webline' );?></label>
			<input type="radio" name="stickeroption" class="wli-woosticker-radio" id="text" value="text" <?php if($this->cust_product_settings['cust_product_option'] == 'text') { echo "checked"; } ?> <?php checked( $this->cust_product_settings['cust_product_option'] ); ?>/>
			<label for="text"><?php esc_html_e( 'Text', 'woo-stickers-by-webline' );?></label>
			<input type="hidden" class="wli_product_option" id="cust_product_option" name="<?php echo esc_attr($this->cust_product_settings_key); ?>[cust_product_option]" value="<?php if($this->cust_product_settings['cust_product_option'] == '') { echo 'image'; } else { echo esc_attr( $this->cust_product_settings['cust_product_option'] ); } ?>"/>
			<p class="description"><?php esc_html_e( 'Select Image or Text for the custom sticker.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="cust_product_sticker_image_width" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_sticker_image_width]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_image_width'] ) ) { echo 'value="' . esc_attr($this->cust_product_settings['cust_product_sticker_image_width']) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set custom sticker width(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" class="small-text" id="cust_product_sticker_image_height" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_sticker_image_height]" <?php if ( isset( $this->cust_product_settings['cust_product_sticker_image_height'] ) ) { echo 'value="' . esc_attr($this->cust_product_settings['cust_product_sticker_image_height']) . '"'; } ?> />
		<p class="description"><?php esc_html_e( 'Set custom sticker height(px). Leave empty for default.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="cust_product_custom_text" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_custom_text]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_custom_text'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify the text to show as custom sticker on products, Leave it blank if you use WooStickers default.', 'woo-stickers-by-webline' );?></p>
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
			name="<?php echo esc_attr($this->cust_product_settings_key); ?>[enable_cust_product_style]">
			<option value='ribbon'
				<?php selected( esc_attr( $this->cust_product_settings['enable_cust_product_style'] ), 'ribbon',true );?>><?php esc_html_e( 'Ribbon', 'woo-stickers-by-webline' );?></option>
			<option value='round'
				<?php selected( esc_attr( $this->cust_product_settings['enable_cust_product_style'] ), 'round',true );?>><?php esc_html_e( 'Round', 'woo-stickers-by-webline' );?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Select custom sticker layout to show on products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="cust_product_custom_text_fontcolor" class="wli_color_picker" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_custom_text_fontcolor]" value="<?php echo esc_attr($this->cust_product_settings['cust_product_custom_text_fontcolor']) ? esc_attr( $this->cust_product_settings['cust_product_custom_text_fontcolor'] ) : '#ffffff' ?>"/>
		<p class="description"><?php esc_html_e( 'Specify font color for text to show as custom sticker on products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="text" id="cust_product_custom_text_backcolor" class="wli_color_picker" name="<?php echo esc_attr($this->cust_product_settings_key);?>[cust_product_custom_text_backcolor]" value="<?php echo esc_attr( $this->cust_product_settings['cust_product_custom_text_backcolor'] ); ?>"/>
		<p class="description"><?php esc_html_e( 'Specify background color for text to show as custom sticker on products.', 'woo-stickers-by-webline' );?></p>
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
		<input type="number" id="cust_product_text_padding_top" class="small-text" placeholder="Top"
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_text_padding_top]"
			<?php if ( isset( $this->cust_product_settings['cust_product_text_padding_top'] ) ) {
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_text_padding_top'] ) . '"';
			} ?>
		/>
		<input type="number" id="cust_product_text_padding_right" class="small-text" placeholder="Right"
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_text_padding_right]"
			<?php if ( isset( $this->cust_product_settings['cust_product_text_padding_right'] ) ) {
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_text_padding_right'] ) . '"';
			} ?>
		/>
		<input type="number" id="cust_product_text_padding_bottom" class="small-text" placeholder="Bottom"
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_text_padding_bottom]"
			<?php if ( isset( $this->cust_product_settings['cust_product_text_padding_bottom'] ) ) {
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_text_padding_bottom'] ) . '"';
			} ?> style="width: auto; max-width: 70px;"
		/>
		<input type="number" id="cust_product_text_padding_left" class="small-text" placeholder="Left"
			name="<?php echo esc_attr( $this->cust_product_settings_key ); ?>[cust_product_text_padding_left]"
			<?php if ( isset( $this->cust_product_settings['cust_product_text_padding_left'] ) ) {
				echo 'value="' . esc_attr( $this->cust_product_settings['cust_product_text_padding_left'] ) . '"';
			} ?>
		/>

		<p class="description">
			<?php esc_html_e( 'Specify sticker padding for top, right, bottom and left, respectively (Leave empty to use default).', 'woo-stickers-by-webline' ); ?>
		</p>
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
		if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
		}

		$image_url = '';
		if ( ! empty( $this->cust_product_settings['cust_product_custom_sticker'] ) ) {
			$image_url = esc_url( $this->cust_product_settings['cust_product_custom_sticker'] );
		}

		if ( ! $image_url ) {
			echo '<img class="cust_product_custom_sticker" width="125" height="auto" />';
		} else {
			printf(
				'<img class="cust_product_custom_sticker" src="%s" width="125" height="auto" />',
				esc_attr( $image_url )
			);
		}

		printf(
			'<br/>' .
			'<input type="hidden" name="%1$s[cust_product_custom_sticker]" id="cust_product_custom_sticker" value="%2$s" /> ' .
			'<button class="upload_img_btn button">%3$s</button> ' .
			'<button class="remove_img_btn button">%4$s</button>',
			esc_attr( $this->cust_product_settings_key ),
			esc_attr( $image_url ),
			esc_html__( 'Upload Image', 'woo-stickers-by-webline' ),
			esc_html__( 'Remove Image', 'woo-stickers-by-webline' )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline JS from internal generator.
		echo $this->custom_sticker_script( 'cust_product_custom_sticker' );
		?>
		<p class="description"><?php esc_html_e( 'Upload your own custom sticker image instead of using WooStickers default.', 'woo-stickers-by-webline' ); ?></p>
		<?php
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menus() {

		add_menu_page(
			__( 'WLI Woocommerce Stickers', 'woo-stickers-by-webline' ), 
			__( 'Stickers for WooCommerce', 'woo-stickers-by-webline' ), 
			'manage_options', 
			$this->plugin_options_key, 
			array( &$this, 'plugin_options_page' ),
			'dashicons-format-image', 
			56
		);		
	}

	public function plugin_options_page() {
		// Read-only GET param for which settings tab to render.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display logic only; no state is changed here.
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $this->general_settings_key;

		// OPTIONAL: whitelist tabs you actually register. Replace with your own keys/method.
		$allowed_tabs = array(
			$this->general_settings_key,
			$this->new_product_settings_key ?? '',
			$this->sale_product_settings_key ?? '',
			$this->sold_product_settings_key ?? '',
			$this->cust_product_settings_key ?? '',
		);
		$allowed_tabs = array_filter( $allowed_tabs ); // remove empties
		if ( ! in_array( $tab, $allowed_tabs, true ) ) {
			$tab = $this->general_settings_key;
		}
		?>
		<div class="wrap-wosbw">
			<div class="inner-wosbw">
				<div class="left-box-wosbw">
					<h2><?php esc_html_e( 'Stickers for WooCommerce - Configuration Settings', 'woo-stickers-by-webline' ); ?></h2>
					<?php settings_errors(); ?>
					<?php $this->plugin_options_tabs(); ?>
					<form class="wli-form-general" method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
						<?php
						// settings_fields() outputs the proper nonce + hidden fields for the option group in $tab.
						settings_fields( $tab );
						do_settings_sections( $tab );
						submit_button();
						?>
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
		$default_tab  = $this->general_settings_key;
		$allowed_tabs = array_keys( (array) $this->plugin_settings_tabs ); // keys you registered.

		// Determine current tab ONLY if a valid nonce is present.
		$current_tab = $default_tab;
		if ( isset( $_GET['tab'], $_GET['_wli_tab_nonce'] ) ) {
			$maybe_tab = sanitize_key( wp_unslash( $_GET['tab'] ) );
			$nonce     = sanitize_text_field( wp_unslash( $_GET['_wli_tab_nonce'] ) );

			if ( $nonce && wp_verify_nonce( $nonce, 'wli_stickers_switch_tab' ) && in_array( $maybe_tab, $allowed_tabs, true ) ) {
				$current_tab = $maybe_tab;
			}
		}

		echo '<h2 class="nav-tab-wrapper">';

		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$is_active = ( $current_tab === $tab_key ) ? 'nav-tab-active' : '';

			$url = add_query_arg(
				array(
					'page' => $this->plugin_options_key,
					'tab'  => $tab_key,
				),
				admin_url( 'admin.php' )
			);
			$url = add_query_arg( '_wli_tab_nonce', wp_create_nonce( 'wli_stickers_switch_tab' ), $url );

			printf(
				'<a class="nav-tab %1$s" href="%2$s">%3$s</a>',
				esc_attr( $is_active ),
				esc_url( $url ),
				esc_html( $tab_caption )
			);
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
			<h2 class="wosbw-heading">Thank you for downloading our plugin - Stickers for WooCommerce.</h2>
			<h2 class="wosbw-heading">We're here to help !</h2>
			<p>Our plugin comes with free, basic support for all users. We also provide plugin customization in case you want to customize our plugin to suit your needs.</p>
			<a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Stickers%20for%20WooCommerce=Free%20Support" target="_blank" class="button">Need help?</a>
			<a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Stickers%20for%20WooCommerce=Plugin%20Customization" target="_blank" class="button button-primary">Want to customize plugin?</a>
		</div>
		<div class="wosbw-plugin-cta upgrade">
			<p class="note">Want to hire Wordpress Developer to finish your wordpress website quicker or need any help in
				maintenance and upgrades?</p>
			<a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=Stickers%20for%20WooCommerce=Hire%20WP%20Developer"
				target="_blank" class="button button-primary">Hire now</a>
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
			
		$url  = 'https://wordpress.org/support/plugin/woo-stickers-by-webline/reviews/';
		$wpdev_url  = 'https://www.weblineindia.com/wordpress-development.html?utm_source=WP-Plugin&utm_medium=Stickers%20for%20WooCommerce=Footer%20CTA';
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
			'<strong>"Stickers for WooCommerce"</strong>',
			$url,
			$url,
			$wpdev_url
		);

		return $text;
	}

}