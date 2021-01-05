(function( $, api ) {

	function updateControlType( control, value ) {
		switch ( control.params.type ) {
			case 'select':
			case 'text':
			case 'textarea':
			case 'wpc_typography':
				control.container.find( 'select, input, textarea' ).val( value ).trigger( 'change' );
				break;
			case 'wpc_range_value':
				control.container.find( 'input.range-slider__range' ).val( value.replaceAll( control.params.input_attrs.suffix, '' ) ).trigger( 'input' );
				break;
			case 'color':
				control.container.find( 'input.color-picker-hex.wp-color-picker' ).val( value ).trigger( 'change' );
				break;
			default:
				control.setting.set( value );
		}
	}
	
	api.bind( 'ready', function() {
			api.section( 'wpc_template_selector' ).container.find( '.wpc-container-card' ).on( 'changeCard', function( event, card ) {
				var obj = $( card ).data( 'value' );
				$.each( obj, function( option, value ) { 
					wp.customize.control( option, function ( control ) {
						updateControlType( control, value );					
					});
				} );
				wp.customize.previewer.refresh(); //fix upadate
			} );
		
	} );

})( jQuery, wp.customize );