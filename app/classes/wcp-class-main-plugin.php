<?php 

namespace WPC;

final class Main_Plugin 
{
	use Singleton;

	public $loader;	
	public $addons;
	public $controls;
	
	private function __construct()
	{		
		$this->loader   = new Auto_Loader();
		$this->addons   = new Load_Addons();
		$this->controls = new Register_Controls();		
	}
	

}