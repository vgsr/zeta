/**
 * Zeta BuddyPress scripts
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

/**
 * Handles toggling fixed entry header for single items.
 */
( function( $ ) {

	// Get the body element
	var $body = $( 'body' ), w = window, d = document, e = d.documentElement, b = d.body,
	    fixEntryHeader, width, height, _entryHeader = $( '.entry-header' ),
	    _entryContent = $( '.entry-content' ), setEntryContentMargin, margin, _style;

	// This logic is only needed for BuddyPress single items
	if ( ! $body.hasClass( 'bp-user' ) )
		return;

	/**
	 * Runs logic to fix the entry header
	 */
	fixEntryHeader = function() {
		width = ( w.innerWidth || e.clientWidth || b.clientWidth );

		// Bail when window is larger than 740px
		if ( width >= 741 )
			return;

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
	}

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
			_style.text( '@media screen and (max-width: 740px) { body.fixed-bp-single-item .entry-content { margin-top: ' + margin + 'px; } }' );
		}
	}

	// Run on scroll & resize
	$(document).on( 'scroll resize', fixEntryHeader );

	// Run on first load
	fixEntryHeader();

} )( jQuery );

/**
 * Handles toggling the item actions menu for all screens.
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
