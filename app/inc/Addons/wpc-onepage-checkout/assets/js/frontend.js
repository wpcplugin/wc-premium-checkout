(function( $ ) {
	
	/* Login Form */
	$( '.checkouttogglelogin' ).on( 'click', function(e) {
		$( '.login-container' ).fadeToggle();
		$( this ).closest( 'html' ).toggleClass( 'overflowhidden' );
	} );

	/* BlockUI to Custom Shipping Methods Box */
	$( document.body ).on( 'update_checkout', function(e) {
		$( document.body ).find( '.woocommerce-shipping-methods' ).block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	} );

})( jQuery );