/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * global wp, jQuery, zetaCustomizeMultiImage
 */
( function( wp, $ ) {
	var api = wp.customize,
	    Select = wp.media.view.MediaFrame.Select,
	    Library = wp.media.controller.Library,
	    l10n = wp.media.view.l10n,
	    ZetaMultiImage;


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
		 */
		initFrame: function() {
			this.frame = new ZetaMultiImageFrame({
				button: {
					text: this.params.button_labels.frame_button
				},
				states: [
					new ZetaMultiImageLibrary({
						title: this.params.button_labels.frame_title,
						library: new ZetaMultiImageQuery( null, { 
							props: _.extend({
								type: 'image',
								orderby: 'date',

								// Custom query params
								minWidth: this.params.minWidth,
								minHeight: this.params.minHeight,
								maxWidth: this.params.maxWidth,
								maxHeight: this.params.maxHeight
							}, {
								query: true
							})
						}),
						multiple: 'add',
						search: true,
						editable: true
					})
				],
				control: this // Send control object along
			});

			// When a file is selected, run a callback
			this.frame.on( 'select', this.select );
		},

		/**
		 * Callback handler for when attachments are selected in the media modal.
		 * Gets the selected images information, and sets it within the control.
		 */
		select: function() {
			// Get the attachments from the modal frame.
			var attachments = this.frame.state().get( 'selection' ).toJSON();

			// Keep the selection in the control's memory
			this.params.attachments = attachments;

			// Set the Customizer setting; the callback takes care of rendering.
			this.setting( _.pluck( attachments, 'id' ) );
		},

		/**
		 * Remove the selected attachments
		 */
		removeSelection: function( event ) {
			if ( api.utils.isKeydownButNotEnterEvent( event ) ) {
				return;
			}
			event.preventDefault();

			this.params.attachments = {};
			this.setting( '' );
			this.renderContent(); // Not bound to setting change when emptying.
		}
	});

	/**
	 * Custom implementation of the Select view
	 *
	 * @since 1.0.0
	 * 
	 * @class
	 * @augments wp.media.MediaFrame.Select
	 * @augments wp.media.MediaFrame
	 * @augments wp.media.Frame
	 * @augments wp.media.View
	 */
	ZetaMultiImageFrame = Select.extend({

		/**
		 * Extend handler bindings
		 */
		bindHandlers: function() {
			Select.prototype.bindHandlers.apply( this, arguments );

			// Add toolbar 'edit-selection'
			this.on( 'toolbar:render:select', this.selectionStatusToolbar, this );

			// Display edit-selection window
			this.on( 'content:render:edit-selection', this.editSelectionContent, this );
		},

		/**
		 * @see wp.media.view.MediaFrame.Post
		 */
		editSelectionContent: function() {
			var state = this.state(),
				selection = state.get('selection'),
				view;

			view = new wp.media.view.AttachmentsBrowser({
				controller: this,
				collection: selection,
				selection:  selection,
				model:      state,
				sortable:   true,
				search:     false,
				date:       false,
				dragInfo:   true,

				AttachmentView: wp.media.view.Attachments.EditSelection
			}).render();

			view.toolbar.set( 'backToLibrary', {
				text:     l10n.returnToLibrary,
				priority: -100,

				click: function() {
					this.controller.content.mode('browse');
				}
			});

			// Browse our library of attachments.
			this.content.set( view );

			// Trigger the controller to set focus
			this.trigger( 'edit:selection', this );
		},

		/**
		 * Enable selection status toolbar
		 *
		 * @see wp.media.view.MediaFrame.Post
		 * 
		 * @param {wp.Backbone.View} view
		 */
		selectionStatusToolbar: function( view ) {
			var editable = this.state().get('editable');

			view.set( 'selection', new wp.media.view.Selection({
				controller: this,
				collection: this.state().get('selection'),
				priority:   -40,

				// If the selection is editable, pass the callback to
				// switch the content mode.
				editable: editable && function() {
					this.controller.content.mode('edit-selection');
				}
			}).render() );
		}
	});

	/**
	 * Custom implementation of the Library controller
	 *
	 * @since 1.0.0
	 * 
	 * @see wp.media.controller.FeaturedImage
	 * 
	 * @class
	 * @augments wp.media.controller.Library
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	ZetaMultiImageLibrary = Library.extend({

		/**
		 * Listen for the library's selection updates
		 */
		initialize: function() {
			var library, comparator;
		
			Library.prototype.initialize.apply( this, arguments );

			library = this.get('library');
			comparator = library.comparator;

			// Overload the library's comparator to push items that are not in
			// the mirrored query to the front of the aggregate collection.
			library.comparator = function( a, b ) {
				var aInQuery = !! this.mirroring.get( a.cid ),
					bInQuery = !! this.mirroring.get( b.cid );

				if ( ! aInQuery && bInQuery ) {
					return -1;
				} else if ( aInQuery && ! bInQuery ) {
					return 1;
				} else {
					return comparator.apply( this, arguments );
				}
			};

			// Add all items in the seleciton to the library, so any selected
			// images that are not initially loaded still appear.
			library.observe( this.get('selection') );
		},

		/**
		 * Update the library's selection when activating
		 */
		activate: function() {
			this.updateSelection();
			this.frame.on( 'open', this.updateSelection, this );

			Library.prototype.activate.apply( this, arguments );
		},

		/**
		 * Remove event listening when deactivating
		 */
		deactivate: function() {
			this.frame.off( 'open', this.updateSelection, this );

			Library.prototype.deactivate.apply( this, arguments );
		},

		/**
		 * Update the library's current selection
		 */
		updateSelection: function() {
			var selection = this.frame.state().get('selection'),
				attachment;

			_.each( _.pluck( this.frame.options.control.params.attachments, 'id' ), function( id ) {
				attachment = wp.media.attachment( id );
				attachment.fetch();

				selection.add( attachment ? [ attachment ] : [] );
			});
		},
	});

	/**
	 * Custom implementation of the Query model
	 *
	 * @since 1.0.0
	 *
	 * @augments wp.media.model.Query
	 * @augments wp.media.model.Attachments
	 * @augments Backbone.Collection
	 */
	ZetaMultiImageQuery = wp.media.model.Attachments.extend({

		/**
		 * Extend validation by checking for proper dimensions
		 */
		validator: function( attachment ) {
			var valid = wp.media.model.Attachments.prototype.validator.apply( this, arguments ),
			    props = this.props.attributes;

			// Check for min/max dimensions
			if ( valid && props.minWidth )
				valid = props.minWidth <= attachment.attributes.width;
			if ( valid && props.minHeight )
				valid = props.minHeight <= attachment.attributes.height;
			if ( valid && props.maxWidth )
				valid = props.maxWidth >= attachment.attributes.width;
			if ( valid && props.maxHeight )
				valid = props.maxHeight >= attachment.attributes.height;

			return valid;
		}
	});

	// Register control type
	$.extend( api.controlConstructor, {
		zeta_multi_image: api.ZetaMultiImageControl
	});

})( wp, jQuery );
