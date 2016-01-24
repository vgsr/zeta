/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */
( function() {
	var container, button, menu;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );

	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== document.body.className.indexOf( 'menu-toggled' ) ) {
			document.body.className = document.body.className.replace( ' menu-toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			document.body.className += ' menu-toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

} )();

/**
 * Navigation
 *
 * Handles toggling submenus for small screens.
 * Code comes from TwentyFifteen.
 */
( function( $ ) {

	// Get the main navigation element
	$nav = $( '.main-navigation' );

	// Add dropdown toggle that display child menu items.
	$nav.find( '.menu-item-has-children > a, .page_item_has_children > a' ).after( '<button class="dropdown-toggle" aria-expanded="false">' + screenReaderText.expand + '</button>' );

	// Toggle buttons and submenu items with active children menu items.
	$nav.find( '.current-menu-ancestor > button, .current_page_ancestor > button' ).addClass( 'toggle-on' );
	$nav.find( '.current-menu-ancestor > .sub-menu, .current_page_ancestor > .children' ).addClass( 'toggled-on' );

	$( '.dropdown-toggle' ).click( function( e ) {
		var _this = $( this );
		e.preventDefault();
		_this.toggleClass( 'toggle-on' );
		_this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );
		_this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
		_this.html( _this.html() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand );
	} );

} )( jQuery );

/**
 * Navigation
 *
 * Handles toggling fixed header state for small screens
 */
( function( $ ) {

	// Get the body element
	var $body = $( 'body' ), w = window, d = document, e = d.documentElement, b = d.body,
	    fixHeader, width;

	// This logic is only needed with the Admin Bar
	if ( ! $body.hasClass( 'admin-bar' ) )
		return;

	/**
	 * Runs logic to fix the header
	 */
	fixHeader = function() {
		width = ( w.innerWidth || e.clientWidth || b.clientWidth );

		// Bail when window is larger than 740px
		if ( width >= 741 )
			return;

		// Add body class beyond 46px scroll top
		// 46 is the height of the admin bar on smaller screens
		if ( w.pageYOffset > 46 && ! $body.hasClass( 'fixed-header' ) ) {
			$body.addClass( 'fixed-header' );

		// Remove body class below 46px scroll top
		} else if ( w.pageYOffset <= 46 && $body.hasClass( 'fixed-header' ) ) {
			$body.removeClass( 'fixed-header' );
		}
	}

	// Run on scroll & resize
	$(document).on( 'scroll resize', fixHeader );

	// Run on first load
	fixHeader();

} )( jQuery );

/**
 * Front Page: animate scroll top
 */
( function( $ ) {

	// Get the body element
	var $body = $( 'body' ), w = window, d = document, e = d.documentElement, b = d.body,
	    width, height;

	// Bail when this is not the front page
	if ( ! $body.hasClass( 'home' ) )
		return;

	// When clicking the scroll-down button
	$( '#page-scroll-down' ).on( 'click', function( e ) {
		e.preventDefault();

		// Get window proportions
		width  = ( w.innerWidth  || e.clientWidth  || b.clientWidth  );
		height = ( w.innerHeight || e.clientHeight || b.clientHeight );

		// Consider header size
		if ( width < 561 ) {
			height -= 62;
		} else if ( width < 741 ) {
			height -= 76;
		} else {
			height -= 45;
		}

		// Consider admin-bar
		if ( $body.hasClass( 'admin-bar' ) ) {
			if ( width < 783 ) {
				height -= 46;
			} else {
				height -= 32;
			}
		}

		// Animate scrolling with 35px top margin
		$( 'html, body' ).animate({ scrollTop: height - 35 + 'px' }, 500 );
	})

} )( jQuery );

/**
 * Tools
 * 
 * Handles toggling the tools elements for all screens.
 */
( function( $ ) {

	// Get the body and tool elements
	var $body = $( 'body' ),
	    $toolsNav = $body.find( '.tools-nav li:not(.no-toggle)' ),
	    $toolsContainer = $body.find( '#site-tools' );

	// Walk all tool elements and toggle their active state
	$toolsNav.each( function() {
		var _nav  = $(this),
		    _tool = $toolsContainer.find( '#site-tool-' + _nav.data( 'tool' ) );

		_nav.on( 'click', 'a', function( e ) {
			e.preventDefault();

			// Tools have been toggled
			if ( $body.hasClass( 'tools-toggled' ) ) {
				$toolsNav.removeClass( 'toggled' );

				// Close opened tool
				if ( _tool.is( ':visible' ) ) {
					$body.removeClass( 'tools-toggled' );
					_tool.hide();

				// Open new tool
				} else {
					_nav.addClass( 'toggled' );
					_tool.show()
						.siblings()
							.hide()
							.end()
						.find( 'input[type!="hidden"], textarea' )
							.first()
								.focus();

					// Hide navigation menu
					$body.removeClass( 'menu-toggled' );
				}

			// Toggle tool
			} else {
				$body.addClass( 'tools-toggled' )
					// Hide navigation menu
					.removeClass( 'menu-toggled');
				_nav.addClass( 'toggled' );

				// Focus on the first focusable input field
				_tool.show()
					.find( 'input[type!="hidden"], textarea' )
						.first()
							.focus();
			}
		} );
	} );

} )( jQuery );

/**
 * BuddyPress
 *
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
