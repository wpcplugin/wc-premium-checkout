<?php

namespace WPC\Extension;

class Template_Selector extends \WPC\Abstract_Addon 
{	
	public function __construct() 
	{		
		$this->section      =   'wpc_template_selector';
		$this->setting      =   'wpc_template_selector_active';
		
		$this->type         =   'extension';
		$this->slug         =   'wpc-template-selector';
		$this->version      =   WPC_VERSION;
		$this->title        =   __( 'Template Selector', 'WPC' );
		$this->description  =   __( 'Embedded extension for active templates', 'WPC' );
		$this->author       =   __( 'WPC' );
		$this->author_url   =   WPC_URL;
		$this->thumbnail    =   plugins_url( 'assets/img/thumbnail.svg', __FILE__ );
		$this->embedded     =   true;

		add_action( 'customize_register', array( $this, 'customize_init' ) );
	}

	public function customize_init() 
	{
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_enqueue' ) );
	}
	
	public function customize_enqueue() 
	{
		wp_enqueue_script( 
			'wpc-template-selector', 
			plugins_url( 'assets/js/customizer.js', __FILE__ ), 
			array( 'jquery', 'customize-base' ), 
			WPC_VERSION, 
			true 
		);
	}
	
	public function customizer() 
	{
		return (
			array( 
				'sections' => array( 
					$this->section => array(
						'title' => __( 'Templates', 'WPC' ),
						'priority' => 5,
						'description'  =>  __( '', 'WPC' ),
					) 
				),
				'settings' => array( 
					$this->setting => array() 
				),
				'controls' => array( 
					'wpc_template_selector' => array(
						'class'           => 'WPC\Control\Card_Selector',
						'section'         => $this->section,
						'settings'        => $this->setting,
						'cards'           => $this->get_templates(),
						'columns'         => 2,
						'gap'             => '15px',
					)
				),
			)
		);

	}

	public function get_templates() 
	{		
		$templates = array(
			array(
				'screenshot' => 'http://wp-premium-checkout.com.br/wp-content/plugins/wc-premium-checkout/app/inc/Addons/wpc-onepage-checkout/assets/img/screenshot.png',
				'action' => array( 
					'id' => 'clear_1', 
					'value' => '{"wpc_theme_onepage_checkout_content_label_icon_border_style":"","wpc_theme_onepage_checkout_font_family":"Nunito","wpc_theme_onepage_checkout_font_base":"14px","wpc_theme_onepage_checkout_container_max_width":"1200px","wpc_theme_onepage_checkout_container_spacing":"50px","wpc_theme_onepage_checkout_content_primary_text_color":"#333333","wpc_theme_onepage_checkout_content_primary_color":"#ffffff","wpc_theme_onepage_checkout_content_decoration_text_color":"#ffffff","wpc_theme_onepage_checkout_form_layout":"inline-form","wpc_theme_onepage_checkout_pro_cart_item_image":"yes","wpc_theme_onepage_checkout_content_label_background_color":"#ffffff","wpc_theme_onepage_checkout_content_label_typography":"Poppins","wpc_theme_onepage_checkout_content_label_typography_weight":"700","wpc_theme_onepage_checkout_content_label_typography_style":"","wpc_theme_onepage_checkout_content_label_typography_size":"","wpc_theme_onepage_checkout_content_label_text_color":"#000000","wpc_theme_onepage_checkout_content_label_height":"70px","wpc_theme_onepage_checkout_content_label_radius":"3px","wpc_theme_onepage_checkout_content_label_text_transform":"uppercase","wpc_theme_onepage_checkout_content_label_icon_type":"","wpc_theme_onepage_checkout_content_label_icon_typography":"Poppins","wpc_theme_onepage_checkout_content_label_icon_typography_weight":"700","wpc_theme_onepage_checkout_content_label_icon_typography_style":"normal","wpc_theme_onepage_checkout_content_label_icon_typography_size":"1.2em","wpc_theme_onepage_checkout_content_label_icon_color":"#000000","wpc_theme_onepage_checkout_content_label_icon_border_color":"#1a5d84","wpc_theme_onepage_checkout_content_description_visible":"block","wpc_theme_onepage_checkout_content_description_spacing_top":"0px","wpc_theme_onepage_checkout_content_description_spacing_bottom":"20px","wpc_theme_onepage_checkout_content_description_background_color":"#ffffff","wpc_theme_onepage_checkout_content_description_text_color":"#000000","wpc_theme_onepage_checkout_content_description_line_bottom_color":"#f1f1f1","wpc_theme_onepage_checkout_content_description_typography":"Poppins","wpc_theme_onepage_checkout_content_description_typography_weight":"700","wpc_theme_onepage_checkout_content_description_typography_style":"","wpc_theme_onepage_checkout_content_description_typography_size":"","wpc_theme_onepage_checkout_content_box_content_background_color":"","wpc_theme_onepage_checkout_content_box_content_border_color":"#bbbbbb","wpc_theme_onepage_checkout_content_box_content_border_width":"1px","wpc_theme_onepage_checkout_content_box_content_spacing":"16px","wpc_theme_onepage_checkout_box_content_order_table_background_color":"","wpc_theme_onepage_checkout_cupom_button_style":"cupom_button_outline","wpc_theme_onepage_checkout_cupom_button_background_color":"#00bfb3","wpc_theme_onepage_checkout_order_button_color":"#00bfb3","wpc_theme_onepage_checkout_order_button_spacing":"10px","wpc_theme_onepage_checkout_order_button_radius":"3px","wpc_theme_onepage_checkout_order_button_style":"order_button_shadow","wpc_theme_onepage_checkout_order_button_typography":"Poppins","wpc_theme_onepage_checkout_order_button_typography_weight":"700","wpc_theme_onepage_checkout_order_button_typography_size":"","wpc_theme_onepage_checkout_order_button_text":"","wpc_theme_onepage_checkout_order_button_text_transform":"uppercase","wpc_theme_onepage_checkout_custom_css":""}',
				),
			),
			array(
				'screenshot' => 'http://wp-premium-checkout.com.br/wp-content/plugins/wc-premium-checkout/app/inc/Addons/wpc-onepage-checkout/assets/img/screenshot.png',
				'action' => array( 
					'id' => 'clear_2', 
					'value' => '{"wpc_theme_onepage_checkout_content_label_icon_border_style":"round","wpc_theme_onepage_checkout_font_family":"Arial, Helvetica, sans-serif","wpc_theme_onepage_checkout_font_base":"12px","wpc_theme_onepage_checkout_container_max_width":"1200px","wpc_theme_onepage_checkout_container_spacing":"50px","wpc_theme_onepage_checkout_content_primary_text_color":"#333333","wpc_theme_onepage_checkout_content_primary_color":"#7cc7c5","wpc_theme_onepage_checkout_content_decoration_text_color":"#ffffff","wpc_theme_onepage_checkout_form_layout":"inline-form","wpc_theme_onepage_checkout_pro_cart_item_image":"yes","wpc_theme_onepage_checkout_content_label_background_color":"","wpc_theme_onepage_checkout_content_label_typography":"Martel Sans","wpc_theme_onepage_checkout_content_label_typography_weight":"400","wpc_theme_onepage_checkout_content_label_typography_style":"","wpc_theme_onepage_checkout_content_label_typography_size":"15px","wpc_theme_onepage_checkout_content_label_text_color":"#ffffff","wpc_theme_onepage_checkout_content_label_height":"50px","wpc_theme_onepage_checkout_content_label_radius":"3px","wpc_theme_onepage_checkout_content_label_text_transform":"uppercase","wpc_theme_onepage_checkout_content_label_icon_type":"","wpc_theme_onepage_checkout_content_label_icon_typography":"Roboto","wpc_theme_onepage_checkout_content_label_icon_typography_weight":"300","wpc_theme_onepage_checkout_content_label_icon_typography_style":"normal","wpc_theme_onepage_checkout_content_label_icon_typography_size":"1em","wpc_theme_onepage_checkout_content_label_icon_color":"#7cc7c5","wpc_theme_onepage_checkout_content_label_icon_border_color":"#ffffff","wpc_theme_onepage_checkout_content_description_visible":"block","wpc_theme_onepage_checkout_content_description_spacing_top":"20px","wpc_theme_onepage_checkout_content_description_spacing_bottom":"10px","wpc_theme_onepage_checkout_content_description_background_color":"#ffffff","wpc_theme_onepage_checkout_content_description_text_color":"#4a4a4a","wpc_theme_onepage_checkout_content_description_line_bottom_color":"#ffffff","wpc_theme_onepage_checkout_content_description_typography":"Roboto","wpc_theme_onepage_checkout_content_description_typography_weight":"900","wpc_theme_onepage_checkout_content_description_typography_style":false,"wpc_theme_onepage_checkout_content_description_typography_size":"15px","wpc_theme_onepage_checkout_content_box_content_background_color":false,"wpc_theme_onepage_checkout_content_box_content_border_color":"#7cc7c5","wpc_theme_onepage_checkout_content_box_content_border_width":"3px","wpc_theme_onepage_checkout_content_box_content_spacing":"10px","wpc_theme_onepage_checkout_box_content_order_table_background_color":"#f5f5f5","wpc_theme_onepage_checkout_cupom_button_style":"cupom_button_3d","wpc_theme_onepage_checkout_cupom_button_background_color":"#7cc7c5","wpc_theme_onepage_checkout_order_button_color":"#f5b72e","wpc_theme_onepage_checkout_order_button_spacing":"15px","wpc_theme_onepage_checkout_order_button_radius":"3px","wpc_theme_onepage_checkout_order_button_style":"order_button_3d","wpc_theme_onepage_checkout_order_button_typography":"Arial, Helvetica, sans-serif","wpc_theme_onepage_checkout_order_button_typography_weight":"700","wpc_theme_onepage_checkout_order_button_typography_size":"15px","wpc_theme_onepage_checkout_order_button_text":"","wpc_theme_onepage_checkout_order_button_text_transform":"none","wpc_theme_onepage_checkout_custom_css":""}',
				),
			),
		);
		
		return $templates;
		
		//apply_filters( 'wpc_field_manager_to_validation_ignore_input', array( 'radio', 'checkbox' ) )
	}
	
}