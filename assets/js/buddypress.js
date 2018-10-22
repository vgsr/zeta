/**
 * Zeta BuddyPress scripts
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

/**
 * Handles toggling fixed entry header for single items.
 *
 * @component Members
 */
( function( $ ) {

	// Get the body element
	var $body = $( 'body' ), w = window, d = document, e = d.documentElement, b = d.body,
	    fixEntryHeader, width, height, _entryHeader = $( '.entry-header' ).eq(0),
	    _entryContent = $( '.entry-content' ).eq(0), setEntryContentMargin, margin, _style;

	// This logic is only needed for BuddyPress single items
	if ( ! $body.hasClass( 'bp-user' ) ) {
		return;
	}

	/**
	 * Runs logic to fix the entry header
	 */
	fixEntryHeader = function() {
		width = ( w.innerWidth || e.clientWidth || b.clientWidth );

		// Bail when window is larger than 740px
		if ( width >= 741 ) {
			return;
		}

		height = _entryContent.offset().top - 55;

		// Consider header size
		if ( width < 561 ) {
			height -= 62;
		} else if ( width < 741 ) {
			height -= 76;
		} else {
			height -= 45;
		}

		// Consider non-sticky Admin Bar
		if ( $body.hasClass( 'admin-bar' ) && width > 601 ) {
			height -= 46;
		}

		// Add body class beyond entry header bottom minus 55px scroll top
		// 55 is the height of the fixed entry header
		if ( w.pageYOffset > height && ! $body.hasClass( 'fixed-bp-single-item' ) ) {

			// Define dummy margin for .entry-content
			setEntryContentMargin();

			// Add body class
			$body.addClass( 'fixed-bp-single-item' );

		// Remove body class below header bottom minus 55px scroll top
		} else if ( w.pageYOffset <= height && $body.hasClass( 'fixed-bp-single-item' ) ) {
			$body.removeClass( 'fixed-bp-single-item' );
		}
	};

	/**
	 * Runs logic to define the entry content margin top
	 */
	setEntryContentMargin = function() {

		// Initialize <style> tag. Add it to the <head>
		if ( 'undefined' === typeof margin ) {
			_style = $( '<style/>' ).attr({ 'id': 'zeta-fixed-bp-single-item' }).appendTo( 'head' );
		}

		var h = _entryHeader.height();

		// .entry-header changed height, so change margin
		if ( margin !== h ) {
			margin = h;

			// Define margin-top in the <style> tag
			_style.text( '@media screen and (max-width: 740px) { body.fixed-bp-single-item .site-main > .page > .entry-content { margin-top: ' + margin + 'px; } }' );
		}
	};

	// Run on scroll & resize
	$( document ).on( 'scroll resize', fixEntryHeader );

	// Run on first load
	fixEntryHeader();

} )( jQuery );

/**
 * Handles toggling the single item actions menu for all screens.
 *
 * @component Members
 * @component Groups
 */
( function() {
	var container, button, actions;

	container = document.getElementById( 'item-header-content' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	actions = document.getElementById( 'item-actions' );

	// Hide actions toggle button if actions is empty and return early.
	if ( ! actions.childNodes.length ) {
		button.style.display = 'none';
		return;
	}

	actions.setAttribute( 'aria-expanded', 'false' );

	button.onclick = function() {
		if ( -1 !== document.body.className.indexOf( 'item-actions-toggled' ) ) {
			document.body.className = document.body.className.replace( ' item-actions-toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			actions.setAttribute( 'aria-expanded', 'false' );
		} else {
			document.body.className += ' item-actions-toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			actions.setAttribute( 'aria-expanded', 'true' );
		}
	};

} )();

/**
 * Handles toggling the dir list item actions menu for all screens.
 *
 * @component Members
 * @component Groups
 */
( function( $ ) {

	// For all dir-list items
	$( '.dir-list' ).on( 'click', '.item-list > li button.action-toggle', function( e ) {
		var $button = $( e.target ),
		    $item = $button.parents( 'li' ).first(),
		    $actions = $item.find( '.action' );

		e.preventDefault();

		// Bail when no actions are present
		if ( $actions.is( ':empty' ) ) {
			$button.hide();
			return;
		}

		// Toggle item actions
		if ( $item.hasClass( 'actions-toggled' ) ) {
			$item.removeClass( 'actions-toggled' );
			$button.attr( 'aria-expanded', 'false' );
			$actions.attr( 'aria-expanded', 'false' );
		} else {
			$item.addClass( 'actions-toggled' );
			$button.attr( 'aria-expanded', 'true' );
			$actions.attr( 'aria-expanded', 'true' );
		}
	});

	// Toggle button when clicking outside
	$( document ).on( 'click', function( e ) {
		var $this = $( e.target );

		// Not when clicking an item action
		if ( ! $this.is( '.dir-list .item-list > li.actions-toggled .action a' ) ) {

			// Mimic click on button.action-toggle
			$( '.dir-list .item-list > li.actions-toggled' ).not( $this.parents( '.dir-list .item-list > li' ) )
				.removeClass( 'actions-toggled' )
				.find( 'button.action-toggle')
					.attr( 'aria-expanded', 'false' )
					.end()
				.find( '.action' )
					.attr( 'aria-expanded', 'false' )
					.end();
		}
	});

})( jQuery );

/**
 * Handles linking member tiles to their data-permalink uri
 *
 * @component Members
 */
( function( $ ) {

	$( '#buddypress' ).on( 'click', '#members-list [data-permalink]', function( e ) {
		var $this = $( e.target );

		// Only act when we're not clicking an `<a>` or `<button>` tag
		if ( ! $this.is( 'a, button' ) && ! $this.parents( 'a, button' ).length ) {
			window.location = this.attributes[ 'data-permalink' ].value;
		}
	});

})( jQuery );

/**
 * Handles toggling the thread messages collapsed state
 * 
 * @component Messages
 */
( function( $ ) {

	$( '#message-thread' ).on( 'click', '.message-box:not(:last-child) .message-metadata, .message-box.collapsed .message-content', function( e ) {
		var $this = $( e.target );

		// Only act when we're not clicking an `<a>` tag
		if ( ! $this.is( 'a' ) && ! $this.parents( 'a' ).length ) {
			$this.parents( '.message-box' ).first().toggleClass( 'collapsed' );
		}
	});

})( jQuery );
