/**
 * Zeta Customize Controls
 *
 * Contains the logic for the theme's custom controls in the Customizer.
 *
 * @package Zeta
 * @subpackage Customizer
 */

/* global wp, jQuery */
( function( wp, $ ) {
	var api = wp.customize;

	/** Zeta Image Radio Control */

	/**
	 * A control for selecting a radio through images
	 *
	 * @since 1.0.0
	 * 
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.ZetaImageRadioControl = api.Control.extend({

		/**
		 * When the control's DOM structure is ready
		 * set up the internal event bindings.
		 *
		 * @since 1.0.0
		 */
		ready: function() {
			var control = this;

			// Update the setting when changing the selection
			this.container.on( 'change', 'input', function() {
				control.setting.set( this.value );
			});
		}
	});

	/** Register Control Types */

	$.extend( api.controlConstructor, {
		'zeta_image_radio': api.ZetaImageRadioControl
	});

})( wp, jQuery );
