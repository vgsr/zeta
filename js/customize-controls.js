/**
 * Zeta Customize Controls
 *
 * Contains the logic for the theme's custom controls in the Customizer.
 *
 * global wp, jQuery
 */
( function( wp, $ ) {
	var api = wp.customize,
	    media = wp.media,
	    Select = media.view.MediaFrame.Select,
	    Library = media.controller.Library,
	    l10n = media.view.l10n;

	/** Zeta Multi Image Control */

	/**
	 * A control for uploading gallery images
	 *
	 * @since 1.0.0
	 * 
	 * @class
	 * @augments wp.customize.MediaControl
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	api.ZetaMultiImageControl = api.MediaControl.extend({

		/**
		 * When the control's DOM structure is ready
		 * set up the internal event bindings.
		 *
		 * @since 1.0.0
		 */
		ready: function() {
			api.MediaControl.prototype.ready.apply( this, arguments );

			_.bindAll( this, 'removeSelection' );

			// Bind event to remove attachments. Remove parent event.
			this.container.off( 'click keydown', '.remove-button', this.removeFile );
			this.container.on(  'click keydown', '.remove-button', this.removeSelection );
		},

		/**
		 * Create a media modal select frame.
		 *
		 * @since 1.0.0
		 *
		 * @uses ZetaMultiImageFrame
		 * @uses ZetaMultiImageLibrary
		 * @uses ZetaMultiImageQuery
		 */
		initFrame: function() {

			// Setup the media modal frame
			this.frame = new media.view.MediaFrame.ZetaMultiImageFrame({
				button: {
					text: this.params.button_labels.frame_button
				},
				states: [

					// Display the media browser state
					new media.controller.ZetaMultiImageLibrary({
						title: this.params.button_labels.frame_title,

						// Query the requested media items
						library: new media.model.ZetaMultiImageQuery( null, {
							props: _.extend({
								type:     'image',
								orderby:  'date',

								// Custom query params
								minWidth:  this.params.minWidth,
								minHeight: this.params.minHeight,
								maxWidth:  this.params.maxWidth,
								maxHeight: this.params.maxHeight
							}, {
								query: true
							})
						}),

						// Enable multi-select mode
						multiple: 'add',
						search: true,
						editable: true
					})
				],

				// Send the current Control object along. This provides
				// for using the Control's parameters in the context of
				// the media modal.
				control: this 
			});

			// When the selection is confirmed, run a callback
			this.frame.on( 'select', this.select );
		},

		/**
		 * Callback handler for when attachments are selected in the media modal.
		 * Gets the selected images information, and sets it within the control.
		 *
		 * @since 1.0.0
		 */
		select: function() {
			// Get the attachments from the modal frame
			var attachments = this.frame.state().get( 'selection' ).toJSON();

			// Keep the selection in the control's memory
			this.params.attachments = attachments;

			// Set the Customizer setting; the callback takes care of rendering.
			this.setting( _.pluck( attachments, 'id' ) );
		},

		/**
		 * Remove the selected attachments
		 *
		 * @since 1.0.0
		 */
		removeSelection: function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}

			event.preventDefault();

			// Clear the current selection
			this.params.attachments = {};
			this.setting( '' );
			this.renderContent(); // Not bound to setting change when emptying.
		}
	});

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
		'zeta_multi_image': api.ZetaMultiImageControl,
		'zeta_image_radio': api.ZetaImageRadioControl
	});

})( wp, jQuery );
