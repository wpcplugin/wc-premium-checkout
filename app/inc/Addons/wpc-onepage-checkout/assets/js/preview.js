(function( $, api ) {

	/* Container Max Width*/
	api( 'wpc_theme_onepage_checkout_container_max_width', function( value ) {
		value.bind( function( maxwidth ) {
			document.documentElement.style.setProperty( '--wpc-base-max-width', maxwidth + 'px' );
		});
	});

	/* Container Spacing */
	api( 'wpc_theme_onepage_checkout_container_spacing', function( value ) {
		value.bind( function( spacing ) {
			document.documentElement.style.setProperty( '--wpc-base-spacing', spacing + 'px' );;
		});
	});

	/* Place Order Button */
	api( 'wpc_theme_onepage_checkout_order_button_color', function( value ) {
		value.bind( function( color ) {
			document.documentElement.style.setProperty( '--wpc-order-button-color', color );

		});
	});

	/* Title Label */
	api( 'wpc_theme_onepage_checkout_content_primary_color', function( value ) {
		value.bind( function( color ) {
			document.documentElement.style.setProperty( '--wpc-primary-background-color', color );
		});
	});

	
})( jQuery, wp.customize );