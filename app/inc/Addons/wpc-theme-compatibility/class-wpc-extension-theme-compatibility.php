<?php

namespace WPC\Extension;

class Theme_Compatibility extends \WPC\Abstract_Addon 
{	
	public function __construct() 
	{		
		$this->section      =   'wpc_theme_compatibility';
		
		$this->type         =   'extension';
		$this->slug         =   'wpc-theme-compatibility';
		$this->version      =   WPC_VERSION;
		$this->title        =   __( 'Theme Compatibility', 'WPC' );
		$this->description  =   __( 'Embedded extension for help avoid conflicts caused by CSS styles and JS scripts that affect the look and functionalities of the checkout theme.', 'WPC' );
		$this->author       =   __( 'WPC' );
		$this->author_url   =   WPC_URL;
		$this->thumbnail    =   plugins_url( 'assets/img/thumbnail.svg', __FILE__ );
		$this->embedded     =   true;
		
		add_action( 'wp', array( $this, 'template_container_type' ) );
		add_action( 'wpc_template_file_prefix', array( $this, 'template_file_prefix' ) );
		add_action( 'customize_register', array( $this, 'customize_init' ) );
		add_action( 'wpc_template_init', array( $this, 'template_init' ) );
		
		add_action( 'wpc_template_init', array( $this, 'hooks' ), 999999 );
		
		add_action( 'wpc_template_init', array( $this, 'do_remove_hooks' ) );
		add_action( 'wp_ajax_wpc_theme_compatibility_print_enquetes', array( $this, 'print_enquetes' ) );	
		//add_action( 'wp_ajax_wpc_theme_compatibility_print_hooks', array( $this, 'print_hooks' ) );	
	}
	
	public function customize_init() 
	{
		add_action( 'wpc_addon_control_active_callback', array( $this, 'controls_active_callback' ), 10, 4 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_enqueue' ) );
		add_action( 'customize_preview_init', array( $this, 'preview_enqueue' ) );		
	}

	public function template_init() 
	{	
		add_action( 'wp_print_styles', array( $this, 'save_multiple_control_updated' ), PHP_INT_MAX );
		add_action( 'wp_print_styles', array( $this, 'do_dequeue_scripts' ), PHP_INT_MAX );
		add_action( 'wp_print_styles', array( $this, 'do_dequeue_styles' ), PHP_INT_MAX );
		add_action( 'wp_footer', array( $this, 'add_style_tag_in_template' ), PHP_INT_MAX );
		add_action( 'wp_footer', array( $this, 'add_script_tag_in_template' ), PHP_INT_MAX );
		add_filter( 'wpc_theme_compatibility_removed_styles', array( $this, 'do_dequeue_all_theme_styles' ) );
		add_filter( 'wpc_theme_compatibility_removed_scripts', array( $this, 'do_dequeue_all_theme_scripts' ) );
		add_filter( 'wpc_theme_compatibility_removed_styles', array( $this, 'do_dequeue_plugin_styles' ) );
		add_filter( 'wpc_theme_compatibility_removed_scripts', array( $this, 'do_dequeue_plugin_scripts' ) );
		add_filter( 'wpc_theme_compatibility_removed_hooks', array( $this, 'do_remove_theme_hooks' ) );	
		add_filter( 'wpc_theme_compatibility_remove_hooks_wp_theme', array( $this, 'do_remove_all_woocommerce_hooks' ) );	
		add_filter( 'wpc_theme_compatibility_ignore_plugins_to_sanitize_enquete', array( $this, 'to_sanitize_enquete_ignore_plugins' ), 10, 2 );
		add_filter( 'wpc_theme_compatibility_selected_theme_handle_to_sanitize_enquete', array( $this, 'to_sanitize_enquete_check_selected_handle' ), 10, 4 );
		add_filter( 'wpc_theme_compatibility_selected_plugin_handle_to_sanitize_enquete', array( $this, 'to_sanitize_enquete_check_selected_handle' ), 10, 4 );
		add_filter( 'wpc_theme_compatibility_disabled_theme_handle_to_sanitize_enquete', array( $this, 'to_sanitize_enquete_check_disabled_theme_handle' ), 10, 4 );
		
	}

	public function customize_enqueue() 
	{
		wp_enqueue_script( 
			'wpc-theme-compatibility', 
			plugins_url( 'assets/js/customizer.js', __FILE__ ), 
			array( 'jquery', 'customize-base' ), 
			WPC_VERSION, 
			true 
		);
		
		wp_localize_script(
			'wpc-theme-compatibility',
			'wpc_theme_compatibility',
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
			'wpc-theme-compatibility', 
			plugins_url( 'assets/js/preview.js', __FILE__ ), 
			array( 'jquery', 'customize-preview' ), 
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
						'title' => __( 'Compatibility', 'WPC' ),
						'description'  =>  __( 'These options help to avoid conflicts caused by CSS styles and JS scripts that affect the theme appearance and functionality at checkout.', 'WPC' ),
						'priority' => 205,
					) 
				),
				'settings' => array( 
					'wpc_theme_compatibility_removed_styles'  => array ( 
						'default' => $this->get_initial_removed_styles()
					),
					'wpc_theme_compatibility_removed_scripts' => array (
						'default' => $this->get_initial_removed_scripts()
					),
					'wpc_theme_compatibility_custom_css' => array (
						'default'   => '',
						'transport' => 'postMessage',
					),
					'wpc_theme_compatibility_page_template' => array (
						'default' => 'wp_theme',
					),
					'wpc_theme_compatibility_page_template_wp_theme_container' => array (
						'default' => 'full',
					),
					'wpc_theme_compatibility_custom_js'      => array (),
					'wpc_theme_compatibility_disable_styles_wp_theme'  => array ( 
						'default'              => 'no', 
						'sanitize_callback'    => 'wpc_bool_to_string', 
						'sanitize_js_callback' => 'wpc_string_to_bool',
					),
					'wpc_theme_compatibility_disable_scripts_wp_theme' => array ( 
						'default'              => 'no', 
						'sanitize_callback'    => 'wpc_bool_to_string', 
						'sanitize_js_callback' => 'wpc_string_to_bool',
					),
					'wpc_theme_compatibility_remove_hooks_wp_theme' => array ( 
						'default'              => 'no', 
						'sanitize_callback'    => 'wpc_bool_to_string', 
						'sanitize_js_callback' => 'wpc_string_to_bool',
					),
					'wpc_theme_compatibility_remove_woocommerce_hooks_wp_theme' => array ( 
						'default'              => 'yes', 
						'sanitize_callback'    => 'wpc_bool_to_string', 
						'sanitize_js_callback' => 'wpc_string_to_bool',
					),
					/*'wpc_theme_compatibility_remove_woocommerce_hooks' => array ( 
						
					),*/
				),
				'controls' => array( 
					'wpc_theme_compatibility_page_template' => array(
						'type'         =>  'select',
						'choices'      =>  array( 'default' => __( 'Default', 'WPC' ), 'wp_theme' => __( 'Theme', 'WPC' ) ),
						'label'        =>  __( 'Page Template' , 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_page_template'
					),
					'wpc_theme_compatibility_page_template_wp_theme_container' => array(
						'type'         =>  'select',
						'choices'      =>  array( 'default' => __( 'Default', 'WPC' ), 'full' => __( 'Full', 'WPC' ) ),
						'label'        =>  __( 'Page Container' , 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_page_template_wp_theme_container'
					),
					'wpc_theme_compatibility_removed_styles' => array(
						'class'        =>  'WPC\Control\Multiple_Select',
						'label'        =>  __( 'Remove CSS in Checkout', 'WPC' ),
						'choices'      =>  array(),
						'value'        =>  array(),
						'description'  =>  __( 'Select styles to remove from the checkout.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_removed_styles',
					), 
					'wpc_theme_compatibility_removed_scripts' => array(
						'class'        =>  'WPC\Control\Multiple_Select',
						'label'        =>  __( 'Remove JS in Checkout', 'WPC' ),
						'choices'      =>  array(),
						'value'        =>  array(),
						'description'  =>  __( 'Select scripts to remove from the checkout.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_removed_scripts',
					), 
					'wpc_theme_compatibility_disable_styles_wp_theme' => array(
						'type'         =>  'checkbox',
						'label'        =>  __( 'Disable theme styles', 'WPC' ),
						'description'  =>  __( 'By checking this option all CSS styles of the active theme will be disabled.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_disable_styles_wp_theme'
					),
					'wpc_theme_compatibility_disable_scripts_wp_theme' => array(
						'type'         =>  'checkbox',
						'label'        =>  __( 'Disable theme scripts', 'WPC' ),
						'description'  =>  __( 'By checking this option all JS scripts of the active theme will be disabled.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_disable_scripts_wp_theme',
					),
					'wpc_theme_compatibility_remove_hooks_wp_theme' => array(
						'type'         =>  'checkbox',
						'label'        =>  __( 'Remove theme hooks', 'WPC' ),
						'description'  =>  __( 'By checking this option all hooks of the active theme will be removed.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_remove_hooks_wp_theme',
					),
					'wpc_theme_compatibility_remove_woocommerce_hooks_wp_theme' => array(
						'type'         =>  'checkbox',
						'label'        =>  __( 'Remove WooCommerce template hooks', 'WPC' ),
						'description'  =>  __( 'By checking this option all WooCommerce template hooks of the active theme will be removed.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_remove_woocommerce_hooks_wp_theme',
					),
					/*'wpc_theme_compatibility_remove_woocommerce_hooks' => array(
						'class'        =>  'WPC\Control\Multiple_Select',
						'label'        =>  __( 'Remove WooCommerce hooks', 'WPC' ),
						'choices'      =>  array(),
						'value'        =>  array(),
						'description'  =>  __( 'Select hooks to remove from the checkout.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_remove_woocommerce_hooks',
					), 
					*/
					'wpc_theme_compatibility_custom_css' => array(
						'type'         =>  'textarea',
						'label'        =>  __( 'Custom CSS', 'WPC' ),
						'description'  =>  __( 'Insert custom CSS on the Checkout page. Use to correct elements or conflicts in the layout.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_custom_css',
					),
					'wpc_theme_compatibility_custom_js' => array(
						'type'         =>  'textarea',
						'label'        =>  __( 'Custom JS', 'WPC' ),
						'description'  =>  __( 'Insert custom JS on the Checkout page. Use to add visual effects or dynamism to the checkout fields.', 'WPC' ),
						'section'      =>  $this->section,
						'settings'     =>  'wpc_theme_compatibility_custom_js',
					),
				),

			)
		);

	}

	public function get_removed_styles() 
	{		
		return(
			(array) (
				get_option( 
					'wpc_theme_compatibility_removed_styles',
					$this->get_initial_removed_styles()
				)
			)
		);
	}

	public function get_initial_removed_styles() 
	{		
		return(
			apply_filters( 
				'wpc_theme_compatibility_initial_removed_styles', 
				array( 'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general' ) 
			)
		);
	}

	public function get_removed_scripts() 
	{
		return(
			(array) (
				get_option( 
					'wpc_theme_compatibility_removed_scripts',
					$this->get_initial_removed_scripts()
				)
			)
		);
	}

	public function get_initial_removed_scripts() 
	{		
		return(
			apply_filters( 
				'wpc_theme_compatibility_initial_removed_scripts', 
					array()
				)
		);
	}
	
	public function save_multiple_control_updated() 
	{
		if ( true === wpc_customize_on_embed() ) {
			$enqueue_content['styles']   = $this->sanitize_enquete( 'styles' );
			$enqueue_content['scripts']  = $this->sanitize_enquete( 'scripts' );
			//$hooks_content['woocommerce_hooks']  = $this->sanitize_hooks();

			$json_encode_enqueue_content = json_encode( $enqueue_content );
			//$json_encode_hooks_content = json_encode( $hooks_content );
			
			update_option( 'wpc_theme_compatibility_enquete', $json_encode_enqueue_content );
			//update_option( 'wpc_theme_compatibility_woocommerce_hooks', $json_encode_hooks_content );
		}
	}

	public function to_sanitize_enquete_ignore_plugins( $plugins, $type ) 
	{		
		return(
			array(
				WPC_BASENAME,
			)
		);
	}

	public function to_sanitize_enquete_check_selected_handle( $status, $type, $handle, $data ) 
	{
		if( 'styles' === $type ) {
			$save_handles = $this->get_removed_styles(); 
		} else {
			$save_handles = $this->get_removed_scripts(); 
		}

		if ( in_array( $handle, $save_handles ) ) { 
			return true;
		}
		
		return $status;
	}

	public function to_sanitize_enquete_check_disabled_theme_handle( $status, $type, $handle, $data ) 
	{	
		if( 'yes' === get_option( 'wpc_theme_compatibility_disable_styles_wp_theme', 'no' ) ) {
			return true; 
		}
		
		return $status;
	}
	
	public function print_enquetes() 
	{
		
		check_ajax_referer( 'wpc-theme-compatibility-nonce', 'security' );
	
		$json_content = get_option( 'wpc_theme_compatibility_enquete' );
		
		print( $json_content ); // print content in ajax response
		
		die;
	}

	/*
	public function print_hooks() 
	{
		check_ajax_referer( 'wpc-theme-compatibility-nonce', 'security' );
	
		$json_content = get_option( 'wpc_theme_compatibility_woocommerce_hooks' );
		
		print( $json_content ); // print content in ajax response
		
		die;
	}
	*/
	
	public function sanitize_enquete( $type, $return_themes = true, $return_plugin = true ) 
	{			
		$sanitize  = [];
		$enquetes  = self::list_enquete( $type );
		
		if( true === $return_themes && isset ( $enquetes['themes'] ) ) {
			foreach ( $enquetes['themes'] as $theme_id => $theme ) {
				$ignore_themes = apply_filters( 'wpc_theme_compatibility_ignore_themes_to_sanitize_enquete', array(), $type );				
				if ( $ignore_themes !== 'ignoreall' && is_array( $ignore_themes ) ) {
					if ( !in_array( $theme_id, $ignore_themes ) ) {
						$enquete = null;				
						$enquete['text'] = $theme['Name'];				
						$enquete['class'] = 'theme';				
						foreach ( $theme[ $type ] as $handle => $data ) {
							$ignore_theme_handles = apply_filters( 'wpc_theme_compatibility_ignore_theme_handles_to_sanitize_enquete', array(), $type, $handle, $data );
							if ( !in_array( $handle, $ignore_theme_handles ) ) {
								$disabled_theme_handle = apply_filters( 'wpc_theme_compatibility_disabled_theme_handle_to_sanitize_enquete', false, $type, $handle, $data );
								$selected_theme_handle = apply_filters( 'wpc_theme_compatibility_selected_theme_handle_to_sanitize_enquete', false, $type, $handle, $data );
								$enquete['children'][] = array( 'id' => $handle, 'text' => $handle, 'selected' => $selected_theme_handle, 'disabled' => $disabled_theme_handle );
							}
						}
						$sanitize[] = $enquete;		
					}
				}
			}
		}

		if( true === $return_plugin && isset ( $enquetes['plugins'] ) ) {
			foreach ( $enquetes['plugins'] as $plugin_id => $plugin ) {
				$ignore_plugins = apply_filters( 'wpc_theme_compatibility_ignore_plugins_to_sanitize_enquete', array(), $type );
				if ( !in_array( $plugin_id, $ignore_plugins ) ) {
					$enquete = null;				
					$enquete['text'] = $plugin['Name'];		
					$enquete['class'] = 'plugin';				
					foreach ( $plugin[ $type ] as $handle => $data ) {
						$ignore_plugin_handles = apply_filters( 'wpc_theme_compatibility_ignore_plugin_handles_to_sanitize_enquete', array(), $type, $handle, $data );
						if ( !in_array( $handle, $ignore_plugin_handles ) ) {
							$disabled_plugin_handle = apply_filters( 'wpc_theme_compatibility_disabled_plugin_handle_to_sanitize_enquete', false, $type, $handle, $data );
							$selected_plugin_handle = apply_filters( 'wpc_theme_compatibility_selected_plugin_handle_to_sanitize_enquete', false, $type, $handle, $data );
							$enquete['children'][] = array( 'id' => $handle, 'text' => $handle, 'selected' => $selected_plugin_handle, 'disabled' => $disabled_plugin_handle );
						}
					}
					$sanitize[] = $enquete;					
				}		
			}
		}

		return $sanitize;		
	}
	
	public static function list_enquete( $type ) 
	{
		$plugins = [];
		$themes  = [];
		
		if ( 'styles' === $type ) {
			$wp_dependencie = wp_styles();
		} elseif ( 'scripts' === $type ) {
			$wp_dependencie = wp_scripts();
		} else {
			return false;
		}
		foreach ( $wp_dependencie->queue as $handle ) {
			$base_url     = wpc_remove_url_protocol( $wp_dependencie->base_url );
			$handle_url   = $wp_dependencie->registered[ $handle ]->src;
			$handle_parse = wpc_url_to_array( str_replace( $base_url, null, $handle_url ) );				
			$handle_parse = wpc_get_util_path( $handle_parse );

			foreach ( wpc_get_themes() as $folder => $theme ) {
				if ( in_array( $folder, $handle_parse, true ) && $folder === $handle_parse[2] ) {
					if ( !isset( $themes[ $folder ] ) )
						$themes[ $folder ] = $theme;
					$themes[ $folder ][ $type ][ $handle ] = $handle_url;
				}
			}
			foreach ( wpc_get_plugins() as $path => $plugin ) {
				$folder = explode( '/', $path )[0];
				if ( in_array( $folder, $handle_parse, true ) && $folder === $handle_parse[2] ) {
					if ( !isset( $plugins[ $path ] ) )
						$plugins[ $path ] = $plugin;
					$plugins[ $path ][ $type ][ $handle ] = $handle_url;
				}				
			}				
		}

		return (
			compact (
				'themes',
				'plugins'
			)
		);
	}

	public static function list_hooks( $hook = '', $list_hook = [] ) {
		global $wp_filter;
	
		$themes  = [];
		$plugins = [];
		$all     = [];

		if ( !empty( $hook ) && isset( $wp_filter[$hook]->callbacks ) ) {      
			array_walk( $wp_filter[$hook]->callbacks, function( $callbacks, $priority ) use ( &$hooks ) {           
				foreach ( $callbacks as $id => $callback )
					$hooks[] = array_merge( [ 'id' => $id, 'priority' => $priority ], $callback );
			});         
		} else {
			foreach( $wp_filter as $fhook => $fdata ){
				
				if ( !empty( $list_hook ) ) {
					if( in_array( $fhook, $list_hook ) ) {
						$all = array_replace_recursive( $all, static::list_hooks( $fhook ) );
					} 
				} else {
					$all = array_replace_recursive( $all, static::list_hooks( $fhook ) );
				}
			}

			return $all;
		}

		foreach( $hooks as &$item ) {			
			// skip if callback does not exist
			if ( !is_callable( $item['function'] ) ) continue;

			// function name as string or static class method eg. 'Foo::Bar'
			if ( is_string( $item['function'] ) ) {
				$ref = strpos( $item['function'], '::' ) ? new \ReflectionClass( strstr( $item['function'], '::', true ) ) : new \ReflectionFunction( $item['function'] );
				$item['file'] = $ref->getFileName();
				$item['line'] = get_class( $ref ) == 'ReflectionFunction' 
					? $ref->getStartLine() 
					: $ref->getMethod( substr( $item['function'], strpos( $item['function'], '::' ) + 2 ) )->getStartLine();

			// array( object, method ), array( string object, method ), array( string object, string 'parent::method' )
			} elseif ( is_array( $item['function'] ) ) {

				$ref = new \ReflectionClass( $item['function'][0] );

				// $item['function'][0] is a reference to existing object
				$item['function'] = array(
					is_object( $item['function'][0] ) ? get_class( $item['function'][0] ) : $item['function'][0],
					$item['function'][1]
				);
				$item['file'] = $ref->getFileName();
				$item['line'] = strpos( $item['function'][1], '::' )
					? $ref->getParentClass()->getMethod( substr( $item['function'][1], strpos( $item['function'][1], '::' ) + 2 ) )->getStartLine()
					: $ref->getMethod( $item['function'][1] )->getStartLine();

			// closures
			} elseif ( is_callable( $item['function'] ) ) {     
				$ref = new \ReflectionFunction( $item['function'] );         
				$item['function'] = get_class( $item['function'] );
				$item['file'] = $ref->getFileName();
				$item['line'] = $ref->getStartLine();

			} else {
				$item['function'] = '';
				$item['file'] = '';
				$item['line'] = '';
			}
			
		}
		
		foreach( $hooks as &$item ) {			
			// skip if callback does not exist
			if ( !is_callable( $item['function'] ) ) continue;
			
			$base_path  = wpc_fix_dir_separator( ABSPATH );
			$path_parse = wpc_path_to_array( str_replace( ABSPATH, null, $item['file'] ) );
			$path_parse = wpc_get_util_path( $path_parse );

			foreach ( wpc_get_themes() as $folder => $theme ) {
				if ( in_array( $folder, $path_parse ) && $folder === $path_parse[3] ) {
					if ( !isset( $themes[ $folder ] ) )
						$themes[ $folder ] = $theme;
					$themes[ $folder ][ 'hooks' ][ $hook ][] = $item;
				}
			}
			foreach ( wpc_get_plugins() as $path => $plugin ) {
				$folder = explode( '/', $path )[0];
				if ( in_array( $folder, $path_parse ) && $folder === $path_parse[3] ) {
					if ( !isset( $plugins[ $path ] ) )
						$plugins[ $path ] = $plugin;
					$plugins[ $path ][ 'hooks' ][ $hook ][] = $item;
				}
			}
			

		}

		return (
			compact (
				'themes',
				'plugins'
			)
		);
	}
	
	public function do_dequeue_scripts() 
	{		
		$this->dequeue_handles( 
			(array) apply_filters( 
				'wpc_theme_compatibility_removed_scripts', 
				(array) ( 
					$this->get_removed_scripts()
				)
			), 
			'scripts' 
		);
	}

	public function do_dequeue_styles() 
	{
		$this->dequeue_handles( 
			(array) apply_filters( 
				'wpc_theme_compatibility_removed_styles', 
				(array) ( 
					$this->get_removed_styles()
				)
			), 
			'styles' 
		);
	}

	public function do_dequeue_all_theme_styles( $styles ) 
	{
		if ( 'yes' === get_option( 'wpc_theme_compatibility_disable_styles_wp_theme', 'no' ) ) {
			$theme_handles = $this->sanitize_enquete( 'styles', true, false );
			foreach ( $theme_handles as $key => $theme ) {
				foreach ( $theme['children'] as $children ) {
					if ( ! in_array( $children['id'], $styles ) ) {
						$styles[] = $children['id'];
					}
				}
			}
		}		
		return $styles;

	}

	public function do_dequeue_all_theme_scripts( $scripts ) 
	{
		if ( 'yes' === get_option( 'wpc_theme_compatibility_disable_scripts_wp_theme', 'no' ) ) {
			$theme_handles = $this->sanitize_enquete( 'scripts', true, false );
			foreach ( $theme_handles as $key => $theme ) {
				foreach ( $theme['children'] as $children ) {
					if ( ! in_array( $children['id'], $scripts ) ) {
						$scripts[] = $children['id'];
					}				
				}
			}
		}
		return $scripts;

	}

	public function do_dequeue_plugin_styles( $styles ) 
	{
		$plugins = apply_filters( 
			'wpc_theme_compatibility_dequeue_plugin_styles', 
			array()
		);
		
		if( !empty( $plugins ) ) {
			foreach ( $plugins as $key => $handles ) {
				if( !empty( $handles ) ) {
					foreach ( $handles as $handle ) {
						$styles[] = $handle;
					}	
				} else {
					$all_styles_enquete  = self::list_enquete( 'styles' );					
					if( isset( $all_styles_enquete[ 'plugins' ][ $key ] ) ) {						
						$plugin_data = $all_styles_enquete[ 'plugins' ][ $key ];
						foreach ( $plugin_data[ 'styles' ] as $handle => $path ) {
							$styles[] = $handle;
						}
					}
				}
			}	
		}
		return $styles;

	}

	public function do_dequeue_plugin_scripts ( $scripts ) 
	{
		$plugins = apply_filters( 
			'wpc_theme_compatibility_dequeue_plugin_scripts', 
			array()
		);
		
		if( !empty( $plugins ) ) {
			foreach ( $plugins as $key => $handles ) {
				if( !empty( $handles ) ) {
					foreach ( $handles as $handle ) {
						$scripts[] = $handle;
					}	
				} else {
					$all_scripts_enquete  = self::list_enquete( 'scripts' );					
					if( isset( $all_scripts_enquete['plugins'][ $key ] ) ) {						
						$plugin_data = $all_scripts_enquete['plugins'][ $key ];						
						foreach ( $plugin_data[ 'scripts' ] as $handle => $path ) {
							$scripts[] = $handle;
						}
					}
				}
			}	
		}
		return $scripts;

	}

	public function dequeue_handles( $dequeue_handles, $type ) 
	{
		foreach ( $dequeue_handles as $handle ) {
			if ( 'styles' === $type ) {
				wp_dequeue_style( $handle );
			} elseif ( 'scripts' === $type ) {
				wp_dequeue_script( $handle );
			}
		}

	}
	
	public function do_remove_theme_hooks( $hooks ) 
	{
		$remove = apply_filters( 'wpc_theme_compatibility_remove_hooks_wp_theme', array() );

		if ( 'yes' === get_option( 'wpc_theme_compatibility_remove_hooks_wp_theme', 'no' ) ) {
			$remove[] = 'wp_head';
			$remove[] = 'wp_footer';
			$remove[] = 'wp_print_styles';
		}
		
		$list = static::list_hooks( '', $remove )[ 'themes' ];
		
		if ( !empty( $list ) ) {
			foreach ( $list as $theme ) {
				if ( isset( $theme['hooks'] ) && !empty( $theme['hooks'] ) ) {
					foreach ( $theme['hooks'] as $hook => $items ) {
						foreach( $items as $item ) {
							$hooks[ $hook ][] = $item;
						}
					}
				}
			}
		}			
		
		return $hooks;

	}

	public static function list_woocommerce_hooks() 
	{
		return(
			array(
				'woocommerce_login_form_start',
				'woocommerce_login_form',
				'woocommerce_checkout_login_form',
				'woocommerce_login_form_end',
				'woocommerce_checkout_coupon_form',
				'woocommerce_before_checkout_form',
				'woocommerce_checkout_before_customer_details',
				'woocommerce_before_checkout_billing_form',
				'woocommerce_after_checkout_billing_form',
				'woocommerce_before_checkout_shipping_form',
				'woocommerce_after_checkout_shipping_form',
				'woocommerce_before_order_notes',
				'woocommerce_after_order_notes',
				'woocommerce_order_review',
				'woocommerce_review_order_before_cart_contents',
				'woocommerce_review_order_after_cart_contents',
				'woocommerce_review_order_before_shipping',
				'woocommerce_review_order_after_shipping',
				'woocommerce_review_order_before_order_total',
				'woocommerce_review_order_after_order_total',
				'woocommerce_review_order_before_payment',
				'woocommerce_review_order_before_submit',
				'woocommerce_review_order_after_submit',
				'woocommerce_review_order_after_payment',
				'woocommerce_checkout_before_order_review_heading',
				'woocommerce_checkout_before_order_review',
				'woocommerce_checkout_after_order_review',
				'woocommerce_checkout_billing',
				'woocommerce_checkout_shipping',
				'woocommerce_checkout_after_customer_details',
				'woocommerce_before_checkout_shipping_form',
				'woocommerce_after_checkout_shipping_form',
				'wc_terms_and_conditions_page_content',
				'wc_checkout_privacy_policy_text',
				'woocommerce_checkout_after_terms_and_conditions',
				'woocommerce_after_checkout_form',
				'woocommerce_checkout_payment',
			)
		);

	}
	
	/*
	public function sanitize_hooks( $return_themes = true, $return_plugin = true ) 
	{			
		$sanitize  = [];
		$woocommerce_hooks  = static::list_woocommerce_hooks();
		$all_hooks  = static::list_hooks( '', $woocommerce_hooks );
		
		if( true === $return_themes && isset ( $all_hooks['themes'] ) ) {
			foreach ( $all_hooks['themes'] as $theme_id => $theme ) {
				$ignore_themes = apply_filters( 'wpc_theme_compatibility_ignore_themes_to_sanitize_hook', array() );				
				if ( $ignore_themes !== 'ignoreall' && is_array( $ignore_themes ) ) {
					if ( !in_array( $theme_id, $ignore_themes ) ) {
						$hooks = null;				
						$hooks['text'] = $theme['Name'];				
						$hooks['class'] = 'theme';			
						foreach ( $theme[ 'hooks' ] as $hook => $data ) {
							$ignore_theme_hooks = apply_filters( 'wpc_theme_compatibility_ignore_theme_handles_to_sanitize_hook', array(), $hook, $data );
							
							if ( !in_array( $hook, $ignore_theme_hooks ) ) {
								foreach ( $data as $hdata ) {
									$disabled_theme_hook = apply_filters( 'wpc_theme_compatibility_disabled_theme_handle_to_sanitize_hook', false, $hook, $hdata );
									$selected_theme_hook = apply_filters( 'wpc_theme_compatibility_selected_theme_handle_to_sanitize_hook', false, $hook, $hdata );
									$hooks['children'][] = array( 'id' => serialize( array( $hook, $hdata['priority'] ) ), 'text' => '[' . $hdata['priority'] . '] ' . $hook, 'selected' => $selected_theme_hook, 'disabled' => $disabled_theme_hook, 'priority' => $hdata['priority'], 'data' => $hdata );
								}
						
								
							}
						}
						$sanitize[] = $hooks;		
					}
				}
			}
		}
		
		if( true === $return_plugin && isset ( $all_hooks['plugins'] ) ) {
			foreach ( $all_hooks['plugins'] as $plugin_id => $plugin ) {
				$ignore_plugins = apply_filters( 'wpc_theme_compatibility_ignore_plugins_to_sanitize_hook', array() );
				if ( !in_array( $plugin_id, $ignore_plugins ) ) {
					$hooks = null;				
					$hooks['text'] = $plugin['Name'];		
					$hooks['class'] = 'plugin';				
					foreach ( $plugin[ 'hooks' ] as $hook => $data ) {
						$ignore_plugin_hooks = apply_filters( 'wpc_theme_compatibility_ignore_plugin_handles_to_sanitize_hook', array(), $hook, $data );
						if ( !in_array( $hook, $ignore_plugin_hooks ) ) {
							foreach ( $data as $hdata ) {
								$disabled_plugin_hook = apply_filters( 'wpc_theme_compatibility_disabled_plugin_handle_to_sanitize_hook', false, $hook, $hdata );
								$selected_plugin_hook = apply_filters( 'wpc_theme_compatibility_selected_plugin_handle_to_sanitize_hook', false, $hook, $hdata );
								$hooks['children'][] = array( 'id' => serialize( array( $hook, $hdata['priority'] ) ), 'text' => '[' . $hdata['priority'] . '] ' . $hook, 'selected' => $selected_plugin_hook, 'disabled' => $disabled_plugin_hook, 'priority' => $hdata['priority'], 'data' => $hdata );
							}
						}
					}
					$sanitize[] = $hooks;					
				}		
			}
		}

		return $sanitize;		
	}
	
	*/
	
	public function hooks()  //list_hooks
	{	//fi woo content position
		$base_hooks = array(
			'woocommerce_checkout_payment',
			'woocommerce_checkout_billing',
			'woocommerce_checkout_shipping',
			'woocommerce_order_review',
			'wc_checkout_privacy_policy_text',
			'wc_terms_and_conditions_page_content',
		);
		
		$checkout_hooks    = static::list_woocommerce_hooks();
		$woocommerce_hooks = static::list_hooks( '', $checkout_hooks )['plugins']['woocommerce/woocommerce.php']['hooks'];
		
		foreach( $woocommerce_hooks  as $action => $hooks  ) {
			foreach( $hooks as $hook ) {
				if( in_array( $hook['id'], $base_hooks ) ) {
					remove_action( $action, $hook['id'], $hook['priority'] );	
				}
			}
			
		}
	}
	
	/*
	public function do_remove_all_woocommerce_hooks( $hooks ) 
	{
		
		if ( 'yes' === get_option( 'wpc_theme_compatibility_remove_woocommerce_hooks_wp_theme', 'yes' ) ) {
			$theme_hooks = $this->sanitize_hooks( true, false );

			foreach ( $theme_hooks as $key => $theme ) {
				foreach ( $theme['children'] as $children ) {
					
					if ( ! in_array( $children['id'], $hooks ) ) {						
						$hooks[ $children['id'] ][] = $children;
					}
				}
			}
		}

		return $hooks;

	}
	*/

	public function do_remove_all_woocommerce_hooks( $remove ) 
	{
		$is_remove_all_hooks = 'yes' === get_option( 'wpc_theme_compatibility_remove_hooks_wp_theme', 'no' );
		$is_remove_woocommerce_hooks = 'yes' === get_option( 'wpc_theme_compatibility_remove_woocommerce_hooks_wp_theme', 'yes' );
		
		if ( $is_remove_all_hooks || $is_remove_woocommerce_hooks ) {
		
			return array_merge(
				$remove,
				static::list_woocommerce_hooks()
			);
		}
		
		return $remove;
	}
	
	public function do_remove_hooks() 
	{
		$this->remove_hooks( 
			apply_filters( 
				'wpc_theme_compatibility_removed_hooks', 
				array()
			)
		);
	}

	public function remove_hooks( $hooks ) 
	{		
		foreach ( $hooks as $hook => $items ) {
			foreach( $items as $item ) {
				remove_action( $hook, $item['id'], $item['priority'] );
			}
		}
	}
	
	public function add_style_tag_in_template() 
	{	
	?>
		<style id="wpc-theme-compatibility-css">
			<?php do_action( 'wpc_theme_compatibility_print_css' ); ?>
		</style>

		<style id="wpc-theme-compatibility-css-saved">
			<?php 
				$conetnt = get_option( 'wpc_theme_compatibility_custom_css', false );
				if ( false !== $conetnt && is_string( $conetnt ) ) {
					_e(
						$conetnt
					);
				}
			?>
		</style>
	<?php
	}

	public function add_script_tag_in_template() 
	{	
	?>
		<script id="wpc-theme-compatibility-js">
			<?php do_action( 'wpc_theme_compatibility_print_js' ); ?>
		</script>

		<script id="wpc-theme-compatibility-js-saved">
			<?php 
				$conetnt = get_option( 'wpc_theme_compatibility_custom_js', false );
				if ( false !== $conetnt && is_string( $conetnt ) ) {
					_e(
						$conetnt
					);
				}
			?>
		</script>
	<?php
	}
	
	public function template_file_prefix() 
	{
		return (
			get_option( 
				'wpc_theme_compatibility_page_template', 
				'wp_theme' 
			)
		);

	}

	public function template_container_type() 
	{
		$template_container_type = get_option( 'wpc_theme_compatibility_page_template_wp_theme_container', 'full' );
		$template_file_prefix = get_option( 'wpc_theme_compatibility_page_template', 'wp_theme' );
	
		if( 'default' === $template_container_type && 'wp_theme' === $template_file_prefix ) {
			remove_filter( 'template_include', 'wpc_template_include' );
			add_filter( 'template_redirect', 'wpc_template_init_callback' );
		}

	}
	
	public function controls_active_callback( $status, $type, $slug, $id ) 
	{
		$is_wp_theme = ( 'wp_theme' === get_option( 'wpc_theme_compatibility_page_template', 'wp_theme' ) );
		$is_remove_hooks_wp_theme = ( 'no' === get_option( 'wpc_theme_compatibility_remove_hooks_wp_theme', 'no' ) );
		
		if ( 'wpc_theme_compatibility_page_template_wp_theme_container' === $id ) {
			return $is_wp_theme;
		}

		if ( 'wpc_theme_compatibility_remove_woocommerce_hooks_wp_theme' === $id ) {
			return $is_remove_hooks_wp_theme;
		}
		
		return $status;

	}
}