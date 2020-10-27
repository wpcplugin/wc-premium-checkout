<?php

namespace WPC;

final class Auto_Loader
{
	public $loader = [];

	public function __construct() 
	{		
		$embedded = array();
		$loader  = apply_filters( 'wpc_autoloader', $embedded );

		foreach ( $loader as $load ) {
			
			if ( is_string( $load ) && class_exists( $load ) ) {
				$this->loader[] = $load;
				
				new $load();
			}
	
		}
	}
	
	public function list() 
	{
		return $this->loader;
	}
	
}