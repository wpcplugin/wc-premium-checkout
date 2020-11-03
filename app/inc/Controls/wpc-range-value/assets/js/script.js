wp.customize.controlConstructor['wpc_range_value'] = wp.customize.Control.extend({

	// When we're finished loading continue processing.
	ready: function() {

		'use strict';

		var control  = this,
		    slider  = this.container.find( '.range-slider' ),
		    range   = this.container.find( '.range-slider__range' ),
		    value   = this.container.find( '.range-slider__value' );
			
		var rangeSlider = function() {
			
			slider.each(function() {

				value.each(function() {
					var value = $(this).prev().attr('value');
					var suffix = ($(this).prev().attr('suffix')) ? $(this).prev().attr('suffix') : '';
					$(this).html(value + suffix);
				});

				range.on('input', function() {
					var suffix = ( $(this).attr('suffix') ) ? $(this).attr('suffix') : '';
					$(this).next( value ).html( this.value + suffix );
					control.setting.set( this.value );
				});
			});
		};
		
		window.onload = rangeSlider();
	}
});