(function( $, api ) {

	var request = function( type ){
		
		$.post( 
			ajaxurl,
			{
				'action': 'wpc_theme_compatibility_print_' + type,
				'security': wpc_theme_compatibility.nonce
			}, 
			function( response ) {
				
				console.log(response);
				
				if( typeof response !== 'undefined' ) {
					
					if( 'enquetes' == type ) {
						populate_enquetes( response );		
					} else if( 'hooks' == type ) {
						//populate_hooks( response );		
					}
				}
			}
		);
	}

	var populate_enquetes = function( data ){
		
		var content = JSON.parse( data ),
			control_styles  = api.control( 'wpc_theme_compatibility_removed_styles' ).container.find( 'select.wpc-multiple-select' ),
			control_scripts = api.control( 'wpc_theme_compatibility_removed_scripts' ).container.find( 'select.wpc-multiple-select' );
	
			control_styles.find( 'optgroup' ).remove();
			control_scripts.find( 'optgroup' ).remove();
			
			control_styles.WPC_select2({ data: content['styles'], tags: true, tokenSeparators: [','] })
			control_scripts.WPC_select2({ data: content['scripts'], tags: true, tokenSeparators: [','] });	
	}

	/*
	var populate_hooks = function( data ){
		
		var content = JSON.parse( data ),
			control_hooks = api.control( 'wpc_theme_compatibility_remove_woocommerce_hooks' ).container.find( 'select.wpc-multiple-select' );
	
			control_hooks.find( 'optgroup' ).remove();
			
			control_hooks.WPC_select2({ data: content['woocommerce_hooks'], tags: true, tokenSeparators: [','] });	
	}
	*/

	api.bind( 'ready', function() {	
	
	  api.previewer.bind( 'ready', function( message ) {
		  request( 'enquetes' );
		  request( 'hooks' );
	  } );
	} );
	
})( jQuery, wp.customize );