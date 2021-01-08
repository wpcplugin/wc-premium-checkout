<?php

namespace WPC\Theme;
	
class Onepage_Checkout extends \WPC\Abstract_Addon 
{
	public function __construct() 
	{
		require_once( __DIR__ . '/inc/template-functions.php' );
		require_once( __DIR__ . '/inc/template-hooks.php' );

		$this->type        = 'theme';
		$this->slug        = 'wpc_theme_onepage_checkout';
		$this->title       = __( 'Onepage Checkout' , 'WPC' );
		$this->thumbnail   = plugins_url( 'assets/img/thumbnail.svg', __FILE__ );
		$this->screenshot  = plugins_url( 'assets/img/screenshot.png', __FILE__ );
		$this->path        = __DIR__ . '/templates/';
		$this->description = __( 'The Onepage Checkout theme for WPC', 'WPC' );
		$this->author      = WPC_TITLE;
		$this->author_url  = WPC_URL;
		$this->version     = WPC_VERSION;
		$this->embedded    =   true;

		$this->register();
		
		add_filter( 'wpc_template_init', array( $this, 'frontend_enqueue' ) );
		add_filter( 'wpc_field_manager_setting_key', array( $this, 'wpc_field_manager_setting_key' ) );
		add_action( 'customize_preview_init',  array( $this, 'preview_enqueue' ) );
		add_action( 'wpc_addon_control_active_callback', array( $this, 'controls_active_callback' ), 10, 4 );
	}
	
	public function preview_enqueue() 
	{
		wp_enqueue_script( 
			'wpc_theme_onepage_checkout_preview', 
			plugins_url( 'assets/js/preview.js', __FILE__ ), 
			array( 'jquery', 'customize-preview' ), 
			WPC_VERSION, 
			true 
		);
	}

	public function frontend_enqueue() 
	{
		wp_enqueue_style( 
			'wpc_theme_onepage_checkout_frontend',
			plugins_url( 'assets/css/frontend.css', __FILE__ ),
			array(), 
			WPC_VERSION,
			'all' 
		);

		wp_enqueue_style( 
			'wpc_theme_onepage_checkout_frontend_form',
			plugins_url( 'assets/css/frontend-form.css', __FILE__ ),
			array(), 
			WPC_VERSION,
			'all' 
		);

		wp_enqueue_style( 
			'wpc_theme_onepage_checkout_frontend_form_inline',
			plugins_url( 'assets/css/frontend-form-inline.css', __FILE__ ),
			array(), 
			WPC_VERSION,
			'all' 
		);
	
		wp_enqueue_style( 
			'normalize',
			plugins_url( 'assets/css/normalize.min.css', __FILE__ ),
			array(), 
			'8.0.1',
			'all' 
		);
		
		wp_enqueue_script( 
			'wpc_theme_onepage_checkout_frontend', 
			plugins_url( 'assets/js/frontend.js', __FILE__ ), 
			array( 'jquery' ), 
			WPC_VERSION, 
			true 
		);
	}

	public function wpc_field_manager_setting_key( $key ) 
	{
		return(
			'wpc_field_manager_list/wpc_theme_onepage_checkout'
		);
	}
	
	public function customizer() 
	{
		return (
			(
				array( 
					'sections' => array( 
						'wpc_onepage_checkout_styles' => array(
						'title' => __( 'Checkout Editor', 'WPC' ),
						'description'  =>  __( '', 'WPC' ),
						'priority' => 10,
						) 
					),
					'settings' => array(
						'wpc_theme_onepage_checkout_container_max_width' => array( 
							'default'   => 1024,
							'transport' => 'postMessage',
						),
						'wpc_theme_onepage_checkout_container_spacing' => array( 
							'default'   => 20,
							'transport' => 'postMessage',
						),
						'wpc_theme_onepage_checkout_order_button_color' => array( 
							'default'   => '#00899d',
							'transport' => 'postMessage',
						), 
						'wpc_theme_onepage_checkout_content_primary_color' => array( 
							'default'   => '#00646d',
							'transport' => 'postMessage',
						),
						'wpc_theme_onepage_checkout_form_layout' => array( 
						),  
					),
					'controls' => array(
						'wpc_theme_onepage_checkout_container_max_width' => array(
							'class'        =>  'WPC\Control\Range_Value',
							'label'        =>  __( 'Container Max Width' , 'WPC' ),
							'input_attrs' => array(
								'min'    => 930,
								'max'    => 1600,
								'suffix' => 'px',
							),
							'section'      =>  'wpc_onepage_checkout_styles',
							'settings'     =>  'wpc_theme_onepage_checkout_container_max_width'
						),
						'wpc_theme_onepage_checkout_container_spacing' => array(
							'class'        =>  'WPC\Control\Range_Value',
							'label'        =>  __( 'Container Spacing' , 'WPC' ),
							'input_attrs' => array(
								'min'    => 0,
								'max'    => 80,
								'suffix' => 'px',
							),
							'section'      =>  'wpc_onepage_checkout_styles',
							'settings'     =>  'wpc_theme_onepage_checkout_container_spacing'
						),
						'wpc_theme_onepage_checkout_content_primary_color' => array(
							'class'        =>  'WP_Customize_Color_Control',
							'label'        =>  __( 'Primary Color' , 'WPC' ),
							'section'      =>  'wpc_onepage_checkout_styles',
							'settings'     =>  'wpc_theme_onepage_checkout_content_primary_color'
						),
						'wpc_theme_onepage_checkout_order_button_color' => array(
							'class'         =>  'WP_Customize_Color_Control',
							'label'        =>  __( 'Button Place Order Color' , 'WPC' ),
							'section'      =>  'wpc_onepage_checkout_styles',
							'settings'     =>  'wpc_theme_onepage_checkout_order_button_color'
						),
						'wpc_theme_onepage_checkout_form_layout' => array(
							'type'         =>  'select',
							'choices'      =>  array( 'inline-form' => __( 'Inline', 'WPC' ), '' => __( 'Default', 'WPC' ) ),
							'label'        =>  __( 'Form Layout' , 'WPC' ),
							'section'      =>  'wpc_onepage_checkout_styles',
							'settings'     =>  'wpc_theme_onepage_checkout_form_layout'
						)
					),
				)
			)
		);
		
	}
	
	public function controls_active_callback( $status, $type, $slug, $id ) 
	{
		$is_wp_theme = 'wp_theme' === get_option( 'wpc_theme_compatibility_page_template', 'wp_theme' );
		$is_full_container = 'full' === get_option( 'wpc_theme_compatibility_page_template_wp_theme_container', 'full' );
		
		if ( 'wpc_theme_onepage_checkout_container_max_width' === $id ) {
			return ( $is_wp_theme && $is_full_container ) || ( !$is_wp_theme );
		}
		
		return $status;

	}

}


