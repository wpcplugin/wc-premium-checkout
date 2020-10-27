(function( $, api ) {
	
	var request = function(){
		
		$.post( 
			ajaxurl,
			{
				'action': 'wpc_theme_compatibility_print_enquetes',
				'security': wpc_theme_compatibility.nonce
			}, 
			function( response ) {

				if( typeof response !== 'undefined' ) {
					populate( response );		
				}
			}
		);
	}

	var populate = function( data ){
		
		var content = JSON.parse( data ),
			control_styles  = api.control( 'wpc_theme_compatibility_removed_styles' ).container.find( 'select.wpc-multiple-select' ),
			control_scripts = api.control( 'wpc_theme_compatibility_removed_scripts' ).container.find( 'select.wpc-multiple-select' );
	
			control_styles.find( 'optgroup' ).remove();
			control_scripts.find( 'optgroup' ).remove();
			
			control_styles.WPC_select2({ data: content['styles'], tags: true, tokenSeparators: [','] })
			control_scripts.WPC_select2({ data: content['scripts'], tags: true, tokenSeparators: [','] });	
	}

	api.bind( 'ready', function() {	
	
	  api.previewer.bind( 'ready', function( message ) {
		  request();
	  } );
	} );
	
})( jQuery, wp.customize );