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
 * Handles toggling the tools elements for all screens.
 */
( function( $ ) {
	var $body = $( 'body' ),
	    $toolsNav = $body.find( '.tools-nav li:not(.no-toggle)' ),
	    $toolsContainer = $body.find( '#site-tools' );

	$toolsNav.each( function() {
		var $nav = $(this),
		    $tool = $toolsContainer.find( '#site-tool-' + $nav.data( 'tool' ) );

		$nav.on( 'click', 'a', function( e ) {
			e.preventDefault();

			if ( $body.hasClass( 'tools-toggled' ) ) {
				$toolsNav.removeClass( 'toggled' );

				if ( $tool.is( ':visible' ) ) {
					$body.removeClass( 'tools-toggled' );
					$tool.hide();
				} else {
					$nav.addClass( 'toggled' );
					$tool.show()
						.siblings()
							.hide()
							.end()
						.find( 'input[type!="hidden"], textarea' )
							.first()
								.focus();
				}

			} else {
				$body.addClass( 'tools-toggled' );
				$nav.addClass( 'toggled' );
				$tool.show()
					.find( 'input[type!="hidden"], textarea' )
						.first()
							.focus();
			}
		});
	});
} )( jQuery );
