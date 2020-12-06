<?php 

namespace WPC;

abstract class Abstract_Addon
{
	public $section;
	public $setting;
	
	public $type;
	public $embedded    = false;
	public $require     = [];
	public $_fields      = [];
	public $slug        = '';
	public $title       = '';
	public $thumbnail   = '';
	public $screenshot  = '';
	public $url         = '#';
	public $path        = '';
	public $description = '';
	public $version     = '';

	abstract public function customizer();

	public function get_customizer() 
	{		
		return( 
			apply_filters( 
				'wpc_addon_customizer', 
				$this->customizer(),
				$this->type,
				$this->slug,
				$this->version
			)		
		);		
	}
	
	public function load_template()
	{
		add_filter( 
			'woocommerce_locate_template', 
			function( $template, $template_name, $plugin_path ) {
				if ( !is_null( $this->path ) 
					&& file_exists( $this->path . $template_name ) 
					&& apply_filters( 'wpc_woocommerce_part_callback', true, $template, $template_name, $plugin_path, PHP_INT_MAX ) ) {
						return $this->path . $template_name;
				}
				return $template;
			}, 
			300, 
			3 
		);
	}

	public function get( $name )
	{
		if( property_exists( $this , $name ) ) {
			return $this->$name;
		}
		else {
			throw new Exception( 
				sprintf( 
					'The property %1$s dow not exists in %2$s class', 
					$name, 
					__CLASS__ 
				) 
			);
		}
	}

	public function set( $name, $value )
	{
		if( property_exists( $this , $name ) ) {
			$this->$name = $value;
			return $this->$name;
		}
		else {
			throw new Exception( 
				sprintf( 
					'The property %1$s dow not exists in %2$s class', 
					$name, 
					__CLASS__ 
				) 
			);
		}
	}
	
	public function register() 
	{
		$class_name = get_class( $this );
		
		add_filter( 
			'wpc_addons', 
			function( $addons ) use( $class_name ) {
				$addons[] = $class_name;
				return $addons;
			} 
		);
	}
		
	public function is_available() 
	{
		return true;
	}
}