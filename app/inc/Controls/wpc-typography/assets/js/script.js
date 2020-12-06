wp.customize.controlConstructor['wpc_typography'] = wp.customize.Control.extend({

	// When we're finished loading continue processing.
	ready: function() {

		'use strict';

		var control  = this,
			selectValue,
		    element  = this.container.find( '.wpc-typography' );
		
		jQuery( element ).WPC_select2().on( 'change', function() {
			selectValue = jQuery( this ).val();
			if ( null === selectValue ) {
				control.setting.set( '' );
			} else {
				control.setting.set( selectValue );
			}
		});
	}

});
