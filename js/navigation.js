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
 * Tools
 * 
 * Handles toggling the tools elements for all screens.
 */
( function( $ ) {
	var _body = $( 'body' ),
	    _toolsNav = _body.find( '.tools-nav li:not(.no-toggle)' ),
	    _toolsContainer = _body.find( '#site-tools' );

	_toolsNav.each( function() {
		var _nav  = $(this),
		    _tool = _toolsContainer.find( '#site-tool-' + _nav.data( 'tool' ) );

		_nav.on( 'click', 'a', function( e ) {
			e.preventDefault();

			if ( _body.hasClass( 'tools-toggled' ) ) {
				_toolsNav.removeClass( 'toggled' );

				if ( _tool.is( ':visible' ) ) {
					_body.removeClass( 'tools-toggled' );
					_tool.hide();
				} else {
					_nav.addClass( 'toggled' );
					_tool.show()
						.siblings()
							.hide()
							.end()
						.find( 'input[type!="hidden"], textarea' )
							.first()
								.focus();
				}

			} else {
				_body.addClass( 'tools-toggled' );
				_nav.addClass( 'toggled' );
				_tool.show()
					.find( 'input[type!="hidden"], textarea' )
						.first()
							.focus();
			}
		});
	});

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
