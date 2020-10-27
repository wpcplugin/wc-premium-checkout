wp.customize.controlConstructor['wpc_repeater'] = wp.customize.Control.extend({

	// When we're finished loading continue processing.
	ready: function() {

		'use strict';

		var control  = this,
		    element  = this.container.find( '.repeater-default' ),
			repeaterValue,
		    repeaterOptions = { 
				initval: 1 
			},
		    sortableOptions = {
				items: 'section',
				axis: 'y',
				cursor: 'move',
				opacity: 0.5,
				placeholder: 'row-dragging',
				delay: 150,
				update: function() {
					updateControl();
				}
			};
			
		var updateControl = function() {
			priority( refreshVal );
		}
		
		var priority = function( callback ) {
			callback = callback || false;
			jQuery( element, '.repeater-default' ).find( '[data-repeater-item]' ).each( function( index ) {
				jQuery( this ).find( '[data-repeater-priority]' ).val( index ).change();
			});
			
			if ( callback ) {
				callback();
			}
		}

		var refreshVal = function() {
			repeaterValue = repeater.repeaterVal();
			control.setting.set( repeaterValue );
		}
		
		var repeater = jQuery( element ).repeater( repeaterOptions )
				.find( '.drag' ).sortable( sortableOptions ).disableSelection();

		var toggle = jQuery( document.body )
			.on( 'click', '.repeater-drag-title', function() {
				jQuery( this ).closest( '.repeater-drag' )
					.toggleClass( 'active' );
				} );

		var label = jQuery( '.repeater-field-label' ).data( 'repeater-title' );
		
		if( undefined != label ) {
			jQuery( document.body )
				.on( 'keypress keydown keyup click change load', '[data-field="' + label + '"]', function() {					
					var title = jQuery( this ).closest( '.repeater-drag' )
						.find( '.repeater-drag-title h3' ).text( jQuery( this ).val() );
			} );
			
			jQuery( '.repeater-drag' ).each(function( index ) {
				 var title = jQuery( this ).find( '[data-field="' + label + '"]' ).val();
				 jQuery( this ).find( '.repeater-drag-title h3' ).text( title );
			});
		}
		
		jQuery( '.hidden-delete' ).each(function( index ) {
			jQuery( this ).hide();
		});
		
		jQuery( document.body ).on( 'change', '[data-field]', function() {
			refreshVal();
		});
		
		jQuery( document ).on( 'repeaterDelete repeaterCreate', function( e ) {
			if( 'repeaterCreate' === e.type ) {
				var item_fields =  e.message.find( '.repeater-field:not(.repeater-field-hidden)' );
					jQuery( item_fields ).each( function( index ) {
						jQuery( this ).find( '[data-field]' ).prop( 'disabled', false )
					});
			}

			setTimeout( function() {
				updateControl();

			}, 200 );
		});
		
		window.onload = updateControl();
	}
	

});
