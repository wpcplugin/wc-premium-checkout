<?php

namespace WPC\Extension;

class Template_Selector extends \WPC\Abstract_Addon 
{	
	public function __construct() 
	{		
		$this->section      =   'wpc_template_selector';
		$this->setting      =   'wpc_template_selector_active';
		
		$this->type         =   'extension';
		$this->slug         =   'wpc_template_selector';
		$this->version      =   WPC_VERSION;
		$this->title        =   __( 'Template Selector', 'WPC' );
		$this->description  =   __( 'Embedded extension to switch between pre-configured template pages.', 'WPC' );
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
				'controls' => array(),
			)
		);

	}
	
}