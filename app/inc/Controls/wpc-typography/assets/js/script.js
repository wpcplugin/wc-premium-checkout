wp.customize.controlConstructor['wpc_typography'] = wp.customize.Control.extend({

	// When we're finished loading continue processing.
	ready: function() {

		'use strict';

		var control  = this,
		    element  = this.container.find( '.wpc-typography' );
		
		jQuery( element ).WPC_select2();
	}

});
