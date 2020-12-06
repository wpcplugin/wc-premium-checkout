<?php

namespace WPC\Control;

class Typography extends \WP_Customize_Control {

	public $type = 'wpc_typography';
	public $label;
	public $description;

	public function to_json() 
	{
		require_once( dirname( __FILE__ ) . '/inc/webfonts.php' );
		
		parent::to_json();
		
		$this->json['value'] = $this->value();
		$this->json['label'] = $this->label;
		$this->json['standard_fonts'] = wpc_typography_control_standard_fonts();
		$this->json['google_fonts']   = wpc_typography_control_google_fonts_array();
	}

	public function render_content() 
	{

	}

	public function content_template() 
	{
	?>		
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		
		<select class="wpc-typography">
			<option value="" <# if ( -1 !== data.value.indexOf( '' ) ) { #> selected="selected" <# } #>> <?php esc_attr_e( 'Default', 'WPC' ); ?></option>
			<#  if ( _.isArray( data.standard_fonts ) ) { #>
				<optgroup label="<?php esc_attr_e( 'Standard Fonts', 'WPC' );?>" class="wpc-typography-standard-fonts">
				<# _.each( data.standard_fonts, function( value ) { #>
					<option value="{{ value }}" <# if ( -1 !== data.value.indexOf( value ) ) { #> selected="selected" <# } #>>{{ value }}</option>
				<# } ) #>
				</optgroup>
			<# } #>

			<#  if ( _.isArray( data.google_fonts ) ) { #>
				<optgroup label="<?php esc_attr_e( 'Google Fonts', 'WPC' );?>" class="wpc-typography-google-fonts">
				<# _.each( data.google_fonts, function( value ) { #>
					<option value="{{ value }}" <# if ( -1 !== data.value.indexOf( value ) ) { #> selected="selected" <# } #>>{{ value }}</option>
				<# } ) #>
				</optgroup>
			<# } #>
		</select>

	<?php
	}

	public function enqueue() 
	{
		wp_enqueue_script( 
			'wpc_typography', 
			plugins_url( 'assets/js/script.js', __FILE__ ), 
			array( 'jquery', 'customize-base' ), 
			WPC_VERSION, 
			true  
		);

		wp_enqueue_script( 
			'WPC_select2' ,
			WPC_URI . 'app/assets/js/select2/WPC_select2.js',
			array( 'jquery' ), 
			'4.0.13', 
			true
		);
		
		wp_enqueue_style( 
			'wpc_typography-css', 
			plugins_url( 'assets/css/style.css', __FILE__ ), 
			array(), 
			WPC_VERSION, 
			'all' 
		);
		
		wp_enqueue_style( 
			'WPC_select2',
			WPC_URI . 'app/assets/css/select2/WPC_select2.css',
			array(), 
			'4.0.13',
			'all' 
		);
	}
}