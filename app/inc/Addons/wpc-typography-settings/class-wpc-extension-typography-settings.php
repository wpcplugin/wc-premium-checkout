<?php

namespace WPC\Extension;

class Typography_Settings extends \WPC\Abstract_Addon 
{	
	public function __construct() 
	{		
		$this->section      =   'wpc_typography_settings';
		
		$this->type         =   'extension';
		$this->slug         =   'wpc-typography-settings';
		$this->version      =   WPC_VERSION;
		$this->title        =   __( 'Typography Selector', 'WPC' );
		$this->description  =   __( 'Embedded extension for add text typography support. This extension does not have configuration options.', 'WPC' );
		$this->author       =   __( 'WPC' );
		$this->author_url   =   WPC_URL;
		$this->thumbnail    =   plugins_url( 'assets/img/thumbnail.svg', __FILE__ );
		$this->embedded     =   true;
		
		add_action( 'wpc_template_init', array( $this, 'template_init' ) );			
		
	}

	public function customizer() 
	{
	
	}
	
	public function customize_init() 
	{
		
	}

	public function template_init() 
	{	
		add_action( 'wp_print_styles', array( $this, 'load_fonts' ), 10 );	
	}

	public function customize_enqueue() 
	{
		wp_enqueue_script( 
			'wpc-typography-settings', 
			plugins_url( 'assets/js/customizer.js', __FILE__ ), 
			array( 'jquery', 'customize-base' ), 
			WPC_VERSION, 
			true 
		);
		
		wp_localize_script(
			'wpc-typography-settings',
			'wpc_typography_settings',
			array(
				'nonce' => wp_create_nonce( 
					'wpc-theme-compatibility-nonce' 
				),
			)
		);
	}

	public function preview_enqueue() 
	{
		wp_enqueue_script( 
			'wpc-typography-settings', 
			plugins_url( 'assets/js/preview.js', __FILE__ ), 
			array( 'jquery', 'customize-preview' ), 
			WPC_VERSION, 
			true 
		);
		wp_localize_script( 
			'wpc-typography-settings', 
			'fonts_settings', 
			array(
				'googleFontsUrl' 	=> '//fonts.googleapis.com',
				'googleFontsWeight' => '100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i',
			)
		);
	}

	public function load_fonts() 
	{
		$addons = WPC()->addons->get();

		foreach ( $addons as $slug => $addon ) {
			if( !empty( $addon->get_customizer() ) ) {
				$customizer = $addon->get_customizer();
				if ( isset( $customizer['controls'] ) ) {	
					foreach( $customizer['controls'] as $id => $args ) {
						if( isset( $args['meta'] ) && is_array( $args['meta'] ) && in_array( 'typography', $args['meta'] ) ) {
							if( $font = get_option( $id, 'lato' ) ) {
								$this->enqueue_google_font( $font );
							}						
						}
					}
				
				}
			}		
		}
	}
	
	public function enqueue_google_font( $font ) 
	{
		require_once( WPC_PATH . '/inc/Controls/wpc-typography/inc/webfonts.php' );

		// Get list of all Google Fonts
		$google_fonts = wpc_typography_control_google_fonts_array();
		
		// Make sure font is in our list of fonts
		if ( ! $google_fonts || ! in_array( $font, $google_fonts ) ) {
			return;
		}

		// Sanitize handle
		$handle = trim( $font );
		$handle = strtolower( $handle );
		$handle = str_replace( ' ', '-', $handle );
	
		// Sanitize font name
		$font = trim( $font );
		$font = str_replace( ' ', '+', $font );

		// Subset
		$get_subsets 	= get_option( 'wpc_typography_settings_google_font_subsets', array( 'latin' ) );
		$subsets 		= '';
		if ( ! empty( $get_subsets ) ) {
			$font_subsets = array();
			foreach ( $get_subsets as $get_subset ) {
				$font_subsets[] = $get_subset;
			}
			$subsets .= implode( ',', $font_subsets );
		} else {
			$subsets = 'latin';
		}
		$subset = '&amp;subset='. $subsets;

		// Weights
		$weights = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );
		$weights = apply_filters( 'wpc_typography_settings_enqueue_weights', $weights, $font );
		$italics = apply_filters( 'wpc_typography_settings_enqueue_italics', true );

		// Main URL
		$url = '//fonts.googleapis.com/css?family='. str_replace(' ', '%20', $font ) .':';

		// Add weights to URL
		if ( ! empty( $weights ) ) {
			$url .= implode( ',', $weights ) .',';
			$italic_weights = array();
			if ( $italics ) {
				foreach ( $weights as $weight ) {
					$italic_weights[] = $weight .'i';
				}
				$url .= implode( ',', $italic_weights );
			}
		}
		// Add subset to URL
		$url .= $subset;		
		
		// Enqueue style
		wp_enqueue_style( 'wpc-typography-settings-google-font-'. $handle, $url, false, false, 'all' );		
	}

	
}