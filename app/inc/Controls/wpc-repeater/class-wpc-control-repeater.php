<?php

namespace WPC\Control;

class Repeater extends \WP_Customize_Control {

	public $type = 'wpc_repeater';
	public $fields;
	public $row_label;
	public $default;
	public $id;
	public $label;
	public $description;

	public function to_json() 
	{

	parent::to_json();

	$this->json['value']       =     (array) $this->value();
	$this->json['fields']      =     $this->fields;
	
	
	//$this->json['fields']['step_disabled_fields']      =     explode( ' ', $this->fields['step_disabled_fields'] );
	
	$this->json['row_label']   =     $this->row_label;
	$this->json['id']          =     $this->id;
	$this->json['default']     =     $this->default;
	$this->json['label']       =     $this->label;
	$this->json['description'] =     $this->description;
	
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
		
	<div class="repeater-default">
		  
		<div data-repeater-list="{{{ data.id }}}" class="drag">
		
			<# _.each( data.default = _.sortBy(data.default, function(o) { return o.priority; }), function( key, value ) { #>

			<section data-repeater-item>
				
			<div class="repeater-drag">

				<# if ( data.row_label ) { #>

					<# if ( data.row_label.field ) { #>

						<input class="repeater-field-label" type="hidden" data-repeater-title="{{{ data.row_label.field }}}"/>

					<# } #>
				
					<# if ( data.row_label.value ) { #>

						<div class="repeater-drag-title"><h3>{{{ data.row_label.value }}}</h3></div>
					
					<# } else { #>	

						<div class="repeater-drag-title"><h3>Your Custom Value</h3></div>
					
					<# } #>	
					
				<# } #>

			<div class="repeater-drag-form">

			<# _.each( data.fields, function( field, id ) { #>

				<div class="repeater-field repeater-field-{{{ field.type }}} repeater-field-{{ id }}">
				
						<# if ( 'text' === field.type ) { #>

							<label class="repeater-field-{{{ field.type }}} repeater-field-{{ id }}">
								<# if ( field.label ) { #><span class="customize-control-title">{{{ field.label }}}</span><# } #>
								<# if ( field.description ) { #><span class="description customize-control-description">{{{ field.description }}}</span><# } #>
								<input type="{{field.type}}" name="{{{ id }}}" value="<# if (  key[id] ) { #>{{{ key[id] }}}<# } #>" data-field="{{{ id }}}">
							</label>

						<# } #>

						<# if ( 'hidden' === field.type ) { #>

							<label class="repeater-field-{{{ field.type }}} repeater-field-{{ id }}">
								<input type="{{field.type}}" name="{{{ id }}}" value="<# if (  key[id] ) { #>{{{ key[id] }}}<# } #>" data-field="{{{ id }}}" disabled="disabled">
							</label>

						<# } #>	

						<# if ( 'textarea' === field.type ) { #>

							<label class="repeater-field-{{{ field.type }}} repeater-field-{{ id }}">
								<# if ( field.label ) { #><span class="customize-control-title">{{{ field.label }}}</span><# } #>
								<# if ( field.description ) { #><span class="description customize-control-description">{{{ field.description }}}</span><# } #>
								<textarea name="{{{ id }}}" data-field="{{{ id }}}"><# if (  key[id] ) { #>{{{ key[id] }}}<# } #></textarea>
							</label>

						<# } #>	

						<# if ( 'select' === field.type ) { #>

							<label class="repeater-field-{{{ field.type }}} repeater-field-{{ id }}">
								<# if ( field.label ) { #><span class="customize-control-title">{{{ field.label }}}</span><# } #>
								<# if ( field.description ) { #><span class="description customize-control-description">{{{ field.description }}}</span><# } #>
								<select name="{{{ id }}}" data-field="{{{ id }}}"<# if ( ! _.isUndefined( field.multiple ) && false !== field.multiple ) { #> multiple="multiple" data-multiple="{{ field.multiple }}"<# } #>								
								
								<# if ( key['step_disabled_fields'] ) { 
								if ( _.contains( Array.isArray( key['step_disabled_fields'] ) ? key['step_disabled_fields'] : key['step_disabled_fields'].split( ',' ), id ) ) { #>  disabled="disabled" <# } } #>>
									<# _.each( field.choices, function( choice, i ) { #>
										<option value="{{{ i }}}" <# if ( i === key[id] ) { #> selected="selected" <# } #>>{{ choice }}</option>
									<# }); #>
								</select>
							</label>

						<# } #>	

				</div>
			
				<# } ) #>
				
					<div class="repeater-delete <# if ( 1 == key['step_remove_delete'] ) { #> hidden-delete <# } #>">
					  <span data-repeater-delete> <?php _e( 'Delete', 'wc-premium-checkout' ); ?> </span>
					</div>
	
				</div>
	
				<input type="hidden" data-repeater-priority name="priority" value="<# if ( key['priority'] ) { #>{{{  key['priority'] }}}<# } else { #>{{{ value }}}<# } #>"  disabled="disabled" />
	
			</section>
			
			
			<# } ) #>

		</div>	
		
		<div class="repeater-add">
			<span data-repeater-create> <?php _e( 'Add', 'wc-premium-checkout' ); ?> </span>
		</div>
		
		</div>	

	</div>
	
	<?php
	
	}
	
	public function enqueue() 
	{
		wp_enqueue_script( 
			'wpc-multiple-repeater', 
			plugins_url( 'assets/js/script.js', __FILE__ ),
			array( 'jquery', 'customize-base' ), 
			WPC_VERSION, 
			true 
		);

		wp_enqueue_style( 
			'wpc-multiple-repeater-css', 
			plugins_url( 'assets/css/style.css', __FILE__ ),
			array(  ), 
			WPC_VERSION, 
			'all' 
		);
		
		wp_enqueue_script( 
			'jquery-ui-core', 
			plugins_url( 'assets/js/jquery-ui.min.js', __FILE__ ),
			array( 'jquery' ), 
			WPC_VERSION, 
			true 
		);

		wp_enqueue_style( 
			'jquery-ui-core', 
			plugins_url( 'assets/css/jquery-ui.css', __FILE__ ),
			array(  ), 
			WPC_VERSION, 
			'all' 
		);
		
		wp_enqueue_script( 
			'wpc-repeater', 
			plugins_url( 'assets/js/repeater/repeater.js', __FILE__ ),
			array( 'jquery' ), 
			WPC_VERSION, 
			true 
		);
	}

}

