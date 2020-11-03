<?php

namespace WPC\Control;

class Range_Value extends \WP_Customize_Control {

	public $type = 'wpc_range_value';
	public $label;
	public $description ;
	public $value;
	public $input_attrs;

	public function to_json() 
	{
		parent::to_json();

		$this->json['label']       =  $this->label;
		$this->json['description'] =  $this->description;
		$this->json['value']       =  maybe_serialize( $this->value() );
		$this->json['input_attrs'] =  $this->input_attrs;
		$this->json['inputAttrs']  =  '';
		
		foreach ( $this->input_attrs as $attr => $value ) {
			$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
		}
		
	}

	public function render_content() 
	{
	}

	public function content_template() 
	{
	?>
		<label>
			<# if ( data.label ) { #><span class="customize-control-title">{{ data.label }}</span><# } #>
			<div class="range-slider"  style="width:100%; display:flex;flex-direction: row;justify-content: flex-start;">
				<span  style="width:100%; flex: 1 0 0; vertical-align: middle;">
					<input class="range-slider__range" type="range" value="{{data.value}}" {{{data.inputAttrs}}} >
				<span class="range-slider__value">0</span></span>
			</div>
			<# if ( data.description ) { #><span class="description customize-control-description">{{{ data.description }}}</span><# } #>
		</label>
	
	<?php
	
	}
	
	public function enqueue() 
	{
		wp_enqueue_script( 
			'wpc-range-value', 
			plugins_url( 'assets/js/script.js', __FILE__ ),
			array( 'jquery', 'customize-base' ), 
			WPC_VERSION, 
			true 
		);

		wp_enqueue_style( 
			'wpc-range-value-css', 
			plugins_url( 'assets/css/style.css', __FILE__ ),
			array(  ), 
			WPC_VERSION, 
			'all' 
		);
	}

}

