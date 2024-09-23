(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	// For Product Level
	$(document).ready(function(){
		jQuery('.wli_color_picker').wpColorPicker();
	
		jQuery('.wsbw_upload_img_id').each(function(){
			var id = $(this).val();
			if( id == '' || id == 0 ) $(this).siblings('.wsbw_remove_image_button').hide();
		});
	
		// Function to show/hide fields based on radio button selection
		function updateFields(element) {
			var val = $(element).attr("value");
			var $tabContent = $(element).closest('.wsbw_tab_content');

			if(val == 'text') {
				$tabContent.find('.custom_optimage').css('display', 'none');
				$tabContent.find('tr.custom_opttext').css('display', 'table-row');
				$tabContent.find('div.custom_opttext, p.custom_opttext').css('display', 'block');

				$tabContent.find(".np_sticker_image_width_field, .np_sticker_image_height_field").css("display","none");
				$tabContent.find(".pos_sticker_image_width_field, .pos_sticker_image_height_field").css("display","none");
				$tabContent.find(".sop_sticker_image_width_field, .sop_sticker_image_height_field").css("display","none");
				$tabContent.find(".cust_sticker_image_width_field, .cust_sticker_image_height_field").css("display","none");

			} else if(val == 'image') {
				$tabContent.find('.custom_opttext').css('display', 'none');
				$tabContent.find('tr.custom_optimage').css('display', 'table-row');
				$tabContent.find('div.custom_optimage').css('display', 'block');

				$tabContent.find(".np_sticker_image_width_field, .np_sticker_image_height_field").css("display","block");
				$tabContent.find(".pos_sticker_image_width_field, .pos_sticker_image_height_field").css("display","block");
				$tabContent.find(".sop_sticker_image_width_field , .sop_sticker_image_height_field").css("display","block");
				$tabContent.find(".cust_sticker_image_width_field, .cust_sticker_image_height_field").css("display","block");
			}
		}
	
		// Initial check on page load
		$('input:radio[class="wli-woosticker-radio"]').each(function(){
			if($(this).is(':checked')){
				updateFields(this);
			}
		});
	
		// Click event for radio buttons
		$('input:radio[class="wli-woosticker-radio"]').click(function(e){
			var val = $(this).attr("value");
			$(this).attr("checked", true);
			$(this).parent(".woo_opt").find(".wli_product_option").attr("value", val);
			updateFields(this);
		});
		
	});

	// For Product Level
	$(document).ready(function(){
		jQuery('.wli_color_picker').wpColorPicker();
	
		jQuery('.wsbw_upload_img_id').each(function(){
			var id = $(this).val();
			if( id == '' || id == 0 ) $(this).siblings('.wsbw_remove_image_button').hide();
		});
	
		// Function to show/hide fields based on radio button selection
		function updatedFields(element) {
			var val = $(element).attr("value");
			var $tabContent = $(element).closest('.wsbw_tab_content');

			if(val == 'text_schedule') {
				$tabContent.find('.custom_optimage_sch').css('display', 'none');
				$tabContent.find('tr.custom_opttext_sch').css('display', 'table-row');
				$tabContent.find('div.custom_opttext_sch, p.custom_opttext_sch').css('display', 'block');

			} else if(val == 'image_schedule') {
				$tabContent.find('.custom_opttext_sch').css('display', 'none');
				$tabContent.find('tr.custom_optimage_sch').css('display', 'table-row');
				$tabContent.find('div.custom_optimage_sch').css('display', 'block');
			}
		}
	
		// Initial check on page load
		$('input:radio[class="wli-woosticker-radio-p-schedule"]').each(function(){
			if($(this).is(':checked')){
				updatedFields(this);
			}
		});
	
		// Click event for radio buttons
		$('input:radio[class="wli-woosticker-radio-p-schedule"]').click(function(e){
			var val = $(this).attr("value");
			$(this).attr("checked", true);
			$(this).parent(".woo_opt").find(".wli_schedule_product_option_product").attr("value", val);
			updatedFields(this);
		});
		
	});

	// For General Level
	$(document).ready(function(){
		jQuery('.wli_color_picker').wpColorPicker();

		jQuery('.wsbw_upload_img_id').each(function(){
			var id = $(this).val();
			if( id == '' || id == 0 ) $(this).siblings('.wsbw_remove_image_button').hide();
		});
		jQuery('input:radio[class="wli-woosticker-radio"]').click(function(e){
			var val = $(this).attr("value");
			$(this).attr("checked", true);
			$(this).parent(".woo_opt").find(".wli_product_option").attr("value", val);
			//$('.custom_option').hide();
	
			if(val == 'text') {
				$(this).parents('.wli-form-general').find('.custom_optimage').css('display', 'none');
				$(this).parents('.wli-form-general').find('tr.custom_opttext').css('display', 'table-row');
				$(this).parents('.wli-form-general').find('div.custom_opttext, p.custom_opttext').css('display', 'block');

				$(this).parents('.wsbw_tab_content').find('.custom_optimage').css('display', 'none');
				$(this).parents('.wsbw_tab_content').find('tr.custom_opttext').css('display', 'table-row');
				$(this).parents('.wsbw_tab_content').find('div.custom_opttext, p.custom_opttext').css('display', 'block');
			} else if(val == 'image') {
				$(this).parents('.wli-form-general').find('.custom_opttext').css('display', 'none');
				$(this).parents('.wli-form-general').find('tr.custom_optimage').css('display', 'table-row');
				$(this).parents('.wli-form-general').find('div.custom_optimage').css('display', 'block');

				$(this).parents('.wsbw_tab_content').find('.custom_opttext').css('display', 'none');
				$(this).parents('.wsbw_tab_content').find('tr.custom_optimage').css('display', 'table-row');
				$(this).parents('.wsbw_tab_content').find('div.custom_optimage').css('display', 'block');
			}
		});
		
	});

	// For General Level
	$(document).ready(function(){
		jQuery('.wli_color_picker').wpColorPicker();

		jQuery('.wsbw_upload_img_id').each(function(){
			var id = $(this).val();
			if( id == '' || id == 0 ) $(this).siblings('.wsbw_remove_image_button').hide();
		});
		jQuery('input:radio[class="wli-woosticker-radio-schedule"]').click(function(e){
			var val = $(this).attr("value");
			$(this).attr("checked", true);
			$(this).parent(".woo_opt").find(".wli_product_schedule_option").attr("value", val);	
			if(val == 'text_schedule') {
				$(this).parents('.wli-form-general').find('.custom_optimage_sch').css('display', 'none');
				$(this).parents('.wli-form-general').find('tr.custom_opttext_sch').css('display', 'table-row');
				$(this).parents('.wli-form-general').find('div.custom_opttext_sch, p.custom_opttext_sch').css('display', 'block');

				$(this).parents('.wsbw_tab_content').find('.custom_optimage_sch').css('display', 'none');
				$(this).parents('.wsbw_tab_content').find('tr.custom_opttext_sch').css('display', 'table-row');
				$(this).parents('.wsbw_tab_content').find('div.custom_opttext_sch, p.custom_opttext_sch').css('display', 'block');
			} else if(val == 'image_schedule') {
				$(this).parents('.wli-form-general').find('.custom_opttext_sch').css('display', 'none');
				$(this).parents('.wli-form-general').find('tr.custom_optimage_sch').css('display', 'table-row');
				$(this).parents('.wli-form-general').find('div.custom_optimage_sch').css('display', 'block');

				$(this).parents('.wsbw_tab_content').find('.custom_opttext_sch').css('display', 'none');
				$(this).parents('.wsbw_tab_content').find('tr.custom_optimage_sch').css('display', 'table-row');
				$(this).parents('.wsbw_tab_content').find('div.custom_optimage_sch').css('display', 'block');
			}
		});
	});
	

	jQuery( document ).on( 'click', '.wsbw-sticker-options-wrap .nav-tab-wrapper .nav-tab', function( event ) {
		event.preventDefault();
		var $this = $(this);
		$('.nav-tab').removeClass( 'nav-tab-active' );
		$this.addClass( 'nav-tab-active' );
		jQuery( '.wsbw_tab_content' ).hide();
		jQuery( $this.attr('href') ).show();
	});

	 // Uploading files
	var file_frame;
	var $upload_btn;

	jQuery( document ).on( 'click', '.wsbw_upload_image_button', function( event ) {

		event.preventDefault();

		$upload_btn = $(this);

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media({
			title: scriptsData.choose_image_title,
			button: {
				text: scriptsData.use_image_btn_text
			},
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
			var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;
			$upload_btn.siblings( 'input.wsbw_upload_img_id' ).val( attachment.id );
			$upload_btn.parent().siblings( '.wsbw_upload_img_preview' ).find('img').attr( 'src', attachment_thumbnail.url );
			$upload_btn.siblings( '.wsbw_remove_image_button' ).show();
		});

		// Finally, open the modal.
		file_frame.open();
	});

	jQuery( document ).on( 'click', '.wsbw_remove_image_button', function() {
		var $this = $(this);
		$this.parent().siblings( '.wsbw_upload_img_preview' ).find( 'img' ).attr( 'src', scriptsData.placeholder_img_src );
		$this.siblings( '.wsbw_upload_img_id' ).val( '' );
		$this.hide();
		return false;
	});

	jQuery(document).ready(function($) {
		var pluginSlug = 'xml-sitemap-for-google';
		var installURL = 'plugin-install.php?tab=plugin-information&plugin=' + pluginSlug + '&TB_iframe=true&width=900&height=800';
		$('#open-install-wosbw').on('click', function(e) {
			e.preventDefault();
			tb_show('Plugin Installation', installURL);
			$('#TB_window').css({ 'max-width': '95%', 'max-height': 'calc(100% - 50px)', 'overflow-x': 'auto', 'margin': '7px auto 0 auto', 'top': '0', 'transform': 'translateX(-50%)' });
			$('#TB_iframeContent').css({ 'width': '100%', 'height': 'calc(100vh - 100px)' });
		});

		$('#new_product_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-new-global').show();
			} else {
				$('#zoominout-options-new-global').hide();
			}
		});
		$('#new_product_sticker_animation_type').trigger('change');
		
		$('#sale_product_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-sale-global').show();
			} else {
				$('#zoominout-options-sale-global').hide();
			}
		});
		$('#sale_product_sticker_animation_type').trigger('change');
		
		$('#sold_product_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-sold-global').show();
			} else {
				$('#zoominout-options-sold-global').hide();
			}
		});
		$('#sold_product_sticker_animation_type').trigger('change');
		
		$('#cust_product_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-cust-global').show();
			} else {
				$('#zoominout-options-cust-global').hide();
			}
		});
		$('#cust_product_sticker_animation_type').trigger('change');


		$('#np_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-np-product').show();
			} else {
				$('#zoominout-options-np-product').hide();
			}
		});
		$('#np_sticker_animation_type').trigger('change');

		$('#pos_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-pos-product').show();
			} else {
				$('#zoominout-options-pos-product').hide();
			}
		});
		$('#pos_sticker_animation_type').trigger('change');

		$('#sop_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-sop-product').show();
			} else {
				$('#zoominout-options-sop-product').hide();
			}
		});
		$('#sop_sticker_animation_type').trigger('change');

		$('#cust_sticker_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-cust-product').show();
			} else {
				$('#zoominout-options-cust-product').hide();
			}
		});
		$('#cust_sticker_animation_type').trigger('change');

		$('#np_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-new-add-cat').show();
			} else {
				$('#zoominout-options-new-add-cat').hide();
			}
		});
		$('#np_sticker_category_animation_type').trigger('change');

		$('#pos_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-pos-add-cat').show();
			} else {
				$('#zoominout-options-pos-add-cat').hide();
			}
		});
		$('#pos_sticker_category_animation_type').trigger('change');
		
		$('#sop_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-sop-add-cat').show();
			} else {
				$('#zoominout-options-sop-add-cat').hide();
			}
		});
		$('#sop_sticker_category_animation_type').trigger('change');

		$('#cust_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-cust-add-cat').show();
			} else {
				$('#zoominout-options-cust-add-cat').hide();
			}
		});
		$('#cust_sticker_category_animation_type').trigger('change');

		$('#category_sticker_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-category-add-cat').show();
			} else {
				$('#zoominout-options-category-add-cat').hide();
			}
		});
		$('#category_sticker_sticker_category_animation_type').trigger('change');

		$('#np_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-new-edit-cat').show();
			} else {
				$('#zoominout-options-new-edit-cat').hide();
			}
		});
		$('#np_sticker_category_animation_type').trigger('change');

		$('#pos_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-sale-edit-cat').show();
			} else {
				$('#zoominout-options-sale-edit-cat').hide();
			}
		});
		$('#pos_sticker_category_animation_type').trigger('change');
		
		$('#sop_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-sold-edit-cat').show();
			} else {
				$('#zoominout-options-sold-edit-cat').hide();
			}
		});
		$('#sop_sticker_category_animation_type').trigger('change');

		$('#cust_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-cust-edit-cat').show();
			} else {
				$('#zoominout-options-cust-edit-cat').hide();
			}
		});
		$('#cust_sticker_category_animation_type').trigger('change');

		$('#category_sticker_sticker_category_animation_type').on('change', function() {
			var selectedValue = $(this).val();
			if (selectedValue === 'zoominout') {
				$('#zoominout-options-category-edit-cat').show();
			} else {
				$('#zoominout-options-category-edit-cat').hide();
			}
		});
		$('#category_sticker_sticker_category_animation_type').trigger('change');
	});

	// Global Level

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_new_product_schedule_sticker',
			'new_product_schedule_start_sticker_date_time',
			'new_product_schedule_end_sticker_date_time',
			'text_schedule',
			'image_schedule',			
			'new_product_schedule_sticker_image_width',
			'new_product_schedule_sticker_image_height',
			'new_product_schedule_custom_text',
			'enable_new_schedule_product_style',
			'new_product_schedule_custom_text_backcolor',
			'new_product_schedule_text_padding_top',
			'new_product_schedule_text_padding_right',
			'new_product_schedule_text_padding_bottom',
			'new_product_schedule_text_padding_left',
			'upload_img_btn_sch',
			'remove_img_btn_sch'
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_new', 'backcolor_sch_new'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_new_product_schedule_sticker']) {

			function toggleDateTimeFields() {
				const isEnabled = elements['enable_new_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});

				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
		
			elements['enable_new_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
		
			// Initial check
			toggleDateTimeFields();
		}
	});
	
	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_sale_product_schedule_sticker',
			'sale_product_schedule_start_sticker_date_time',
			'sale_product_schedule_end_sticker_date_time',
			'text_schedule',
			'image_schedule',
			'sale_product_schedule_custom_sticker',
			'sale_product_schedule_sticker_image_width',
			'sale_product_schedule_sticker_image_height',
			'sale_product_schedule_custom_text',
			'enable_sale_schedule_product_style',
			'sale_product_schedule_custom_text_fontcolor',
			'sale_product_schedule_custom_text_backcolor',
			'sale_product_schedule_text_padding_top',
			'sale_product_schedule_text_padding_right',
			'sale_product_schedule_text_padding_bottom',
			'sale_product_schedule_text_padding_left',
			'upload_img_btn_sch',
			'remove_img_btn_sch'
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_sale', 'backcolor_sch_sale'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_sale_product_schedule_sticker']) {

			function toggleDateTimeFields() {
				const isEnabled = elements['enable_sale_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});
				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
		
			elements['enable_sale_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
		
			// Initial check
			toggleDateTimeFields();
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_sold_product_schedule_sticker',
			'sold_product_schedule_start_sticker_date_time',
			'sold_product_schedule_end_sticker_date_time',
			'text_schedule',
			'image_schedule',
			'sold_product_schedule_sticker_image_width',
			'sold_product_schedule_sticker_image_height',
			'sold_product_schedule_custom_text',
			'enable_sold_schedule_product_style',
			'sold_product_schedule_custom_text_fontcolor',
			'sold_product_schedule_custom_text_backcolor',
			'sold_product_schedule_text_padding_top',
			'sold_product_schedule_text_padding_right',
			'sold_product_schedule_text_padding_bottom',
			'sold_product_schedule_text_padding_left',
			'upload_img_btn_sch',
			'remove_img_btn_sch'
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_sold', 'backcolor_sch_sold'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_sold_product_schedule_sticker']) {

			function toggleDateTimeFields() {
				const isEnabled = elements['enable_sold_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});

				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
		
			elements['enable_sold_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
		
			// Initial check
			toggleDateTimeFields();
		}
	});
	
	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_cust_product_schedule_sticker',
			'cust_product_schedule_start_sticker_date_time',
			'cust_product_schedule_end_sticker_date_time',
			'text_schedule',
			'image_schedule',
			'cust_product_schedule_sticker_image_width',
			'cust_product_schedule_sticker_image_height',
			'cust_product_schedule_custom_text',
			'enable_cust_schedule_product_style',
			'cust_product_schedule_custom_text_fontcolor',
			'cust_product_schedule_custom_text_backcolor',
			'cust_product_schedule_text_padding_top',
			'cust_product_schedule_text_padding_right',
			'cust_product_schedule_text_padding_bottom',
			'cust_product_schedule_text_padding_left',
			'upload_img_btn_sch',
			'remove_img_btn_sch'
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_cust', 'backcolor_sch_cust'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_cust_product_schedule_sticker']) {

			function toggleDateTimeFields() {
				const isEnabled = elements['enable_cust_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});

				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
		
			elements['enable_cust_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
		
			// Initial check
			toggleDateTimeFields();
		}
	});

	// Product Level
	
	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_np_product_schedule_sticker',
			'np_product_schedule_start_sticker_date_time',
			'np_product_schedule_end_sticker_date_time',
			'text_schedule',
			'image_schedule',
			'np_schedule_sticker_image_width',
			'np_schedule_sticker_image_height',
			'wsbw_upload_image_button',
			'wsbw_remove_image_button',
			'np_schedule_product_custom_text',
			'np_schedule_sticker_type',
			'np_schedule_product_custom_text_padding_top',
			'np_product_schedule_custom_text_padding_right',
			'np_product_schedule_custom_text_padding_bottom',
			'np_product_schedule_custom_text_padding_left',
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_np', 'backcolor_sch_np'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_np_product_schedule_sticker']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_np_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});

				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_np_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_pos_product_schedule_sticker',
			'pos_product_schedule_start_sticker_date_time',
			'pos_product_schedule_end_sticker_date_time',
			'text_schedule_pos',
			'image_schedule_pos',
			'pos_schedule_sticker_image_width',
			'pos_schedule_sticker_image_height',
			'wsbw_upload_image_button_pos',
			'wsbw_remove_image_button_pos',
			'pos_schedule_product_custom_text',
			'pos_schedule_sticker_type',
			'pos_schedule_product_custom_text_padding_top',
			'pos_product_schedule_custom_text_padding_right',
			'pos_product_schedule_custom_text_padding_bottom',
			'pos_product_schedule_custom_text_padding_left',
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_pos', 'backcolor_sch_pos'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_pos_product_schedule_sticker']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_pos_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});

				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			
			elements['enable_pos_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});
	 
	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_sop_product_schedule_sticker',
			'sop_product_schedule_start_sticker_date_time',
			'sop_product_schedule_end_sticker_date_time',
			'text_schedule_sop',
			'image_schedule_sop',
			'sop_schedule_sticker_image_width',
			'sop_schedule_sticker_image_height',
			'wsbw_upload_image_button_sop',
			'wsbw_remove_image_button_sop',
			'sop_schedule_product_custom_text',
			'sop_schedule_sticker_type',
			'sop_schedule_product_custom_text_padding_top',
			'sop_product_schedule_custom_text_padding_right',
			'sop_product_schedule_custom_text_padding_bottom',
			'sop_product_schedule_custom_text_padding_left',
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_sop', 'backcolor_sch_sop'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_sop_product_schedule_sticker']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_sop_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});
				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_sop_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_cust_product_schedule_sticker',
			'cust_product_schedule_start_sticker_date_time',
			'cust_product_schedule_end_sticker_date_time',
			'text_schedule_cust',
			'image_schedule_cust',
			'cust_schedule_sticker_image_width',
			'cust_schedule_sticker_image_height',
			'wsbw_upload_image_button_cust',
			'wsbw_remove_image_button_cust',
			'cust_schedule_product_custom_text',
			'cust_schedule_sticker_type',
			'cust_schedule_product_custom_text_padding_top',
			'cust_product_schedule_custom_text_padding_right',
			'cust_product_schedule_custom_text_padding_bottom',
			'cust_product_schedule_custom_text_padding_left',
		];
	
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_sch_cust', 'backcolor_sch_cust'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_cust_product_schedule_sticker']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_cust_product_schedule_sticker'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});

				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_cust_product_schedule_sticker'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});

	// Category Level

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_np_product_schedule_sticker_category',
			'np_product_schedule_start_sticker_date_time',
			'np_product_schedule_end_sticker_date_time',
			'text_schedule_np',
			'image_schedule_np',
			'np_schedule_sticker_image_width',
			'np_schedule_sticker_image_height',
			'wsbw_upload_image_button_np',
			'wsbw_remove_image_button_np',
			'np_product_schedule_custom_text',
			'np_schedule_sticker_type',
			'np_product_schedule_custom_text_padding_top',
			'np_product_schedule_custom_text_padding_right',
			'np_product_schedule_custom_text_padding_bottom',
			'np_product_schedule_custom_text_padding_left'
		];
		
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_cat_np', 'backcolor_cat_np'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_np_product_schedule_sticker_category']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_np_product_schedule_sticker_category'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});

				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_np_product_schedule_sticker_category'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_pos_product_schedule_sticker_category',
			'pos_product_schedule_start_sticker_date_time',
			'pos_product_schedule_end_sticker_date_time',
			'text_schedule_pos',
			'image_schedule_pos',
			'pos_schedule_sticker_image_width',
			'pos_schedule_sticker_image_height',
			'wsbw_upload_image_button_pos',
			'wsbw_remove_image_button_pos',
			'pos_product_schedule_custom_text',
			'pos_schedule_sticker_type',
			'pos_product_schedule_custom_text_padding_top',
			'pos_product_schedule_custom_text_padding_right',
			'pos_product_schedule_custom_text_padding_bottom',
			'pos_product_schedule_custom_text_padding_left'
		];
		
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_cat_pos', 'backcolor_cat_pos'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});

	
		if (elements['enable_pos_product_schedule_sticker_category']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_pos_product_schedule_sticker_category'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});
				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_pos_product_schedule_sticker_category'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_sop_product_schedule_sticker_category',
			'sop_product_schedule_start_sticker_date_time',
			'sop_product_schedule_end_sticker_date_time',
			'text_schedule_sop',
			'image_schedule_sop',
			'sop_schedule_sticker_image_width',
			'sop_schedule_sticker_image_height',
			'wsbw_upload_image_button_sop',
			'wsbw_remove_image_button_sop',
			'sop_product_schedule_custom_text',
			'sop_schedule_sticker_type',
			'sop_product_schedule_custom_text_padding_top',
			'sop_product_schedule_custom_text_padding_right',
			'sop_product_schedule_custom_text_padding_bottom',
			'sop_product_schedule_custom_text_padding_left'
		];
		
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_cat_sop', 'backcolor_cat_sop'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});

	
		if (elements['enable_sop_product_schedule_sticker_category']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_sop_product_schedule_sticker_category'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});
				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_sop_product_schedule_sticker_category'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_cust_product_schedule_sticker_category',
			'cust_product_schedule_start_sticker_date_time',
			'cust_product_schedule_end_sticker_date_time',
			'text_schedule_cust',
			'image_schedule_cust',
			'cust_schedule_sticker_image_width',
			'cust_schedule_sticker_image_height',
			'wsbw_upload_image_button_cust',
			'wsbw_remove_image_button_cust',
			'cust_product_schedule_custom_text',
			'cust_schedule_sticker_type',
			'cust_product_schedule_custom_text_padding_top',
			'cust_product_schedule_custom_text_padding_right',
			'cust_product_schedule_custom_text_padding_bottom',
			'cust_product_schedule_custom_text_padding_left'
		];
		
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_cat_cust', 'backcolor_cat_cust'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});
	
		if (elements['enable_cust_product_schedule_sticker_category']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_cust_product_schedule_sticker_category'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});
				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_cust_product_schedule_sticker_category'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const elementIds = [
			'enable_category_product_schedule_sticker_category',
			'category_product_schedule_start_sticker_date_time',
			'category_product_schedule_end_sticker_date_time',
			'text_schedule_cat',
			'image_schedule_cat',
			'category_schedule_sticker_image_width',
			'category_schedule_sticker_image_height',
			'wsbw_upload_image_button_cat',
			'wsbw_remove_image_button_cat',
			'category_product_schedule_custom_text',
			'category_schedule_sticker_type',
			'category_product_schedule_custom_text_padding_top',
			'category_product_schedule_custom_text_padding_right',
			'category_product_schedule_custom_text_padding_bottom',
			'category_product_schedule_custom_text_padding_left'
		];
		
		const elements = {};
		elementIds.forEach(id => {
			elements[id] = document.getElementById(id);
		});

		const elementIds2 = ['fontcolor_cat', 'backcolor_cat'];
		const elements2 = {};
		elementIds2.forEach(id => {
			elements2[id] = document.getElementsByClassName(id);
		});

	
		if (elements['enable_category_product_schedule_sticker_category']) {
			function toggleDateTimeFields() {
				const isEnabled = elements['enable_category_product_schedule_sticker_category'].value === 'yes';
				elementIds.slice(1).forEach(id => {
					if (elements[id]) {
						elements[id].disabled = !isEnabled;
					}
				});
				elementIds2.forEach(id => {
					if (elements2[id]) {
						Array.from(elements2[id]).forEach(element => {
							if (isEnabled) {
								element.classList.remove('disabled');
							} else {
								element.classList.add('disabled');
							}
						});
					}
				});
			}
			elements['enable_category_product_schedule_sticker_category'].addEventListener('change', toggleDateTimeFields);
			toggleDateTimeFields();
		}
	});
	
})( jQuery );
