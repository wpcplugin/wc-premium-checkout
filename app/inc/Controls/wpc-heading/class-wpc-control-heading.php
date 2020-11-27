<?php

namespace WPC\Control;

class Heading extends \WP_Customize_Control {

	public $type = 'wpc_heading';
	public $label;
	public $description ;

	public function to_json() 
	{
		parent::to_json();

		$this->json['label']       =  $this->label;
		$this->json['description'] =  $this->description;
	}

	public function render_content() 
	{
	}

	public function content_template() 
	{
	?>
		<h4 class="wpc-customizer-heading">{{{ data.label }}}</h4>
		<div class="description">{{{ data.description }}}</div>
	<?php
	
	}
	
	public function enqueue() 
	{
		wp_enqueue_style( 
			'wpc-heading-css', 
			plugins_url( 'assets/css/style.css', __FILE__ ),
			array(  ), 
			WPC_VERSION, 
			'all' 
		);
	}

}

