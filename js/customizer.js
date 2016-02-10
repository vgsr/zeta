/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @package Zeta
 * @subpackage Customizer
 */

/* global wp, jQuery */
( function( wp, $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		});
	});
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description a' ).text( to );
		});
	});

	// Theme Settings: Default layout
	wp.customize( 'default_layout', function( value ) {
		value.bind( function( choice ) {
			$body = $( 'body' );

			// Bail when on the front page
			if ( $body.hasClass( 'home' ) )
				return;

			switch ( choice ) {
				case 'sidebar-content' :
					$body.addClass( 'with-sidebar sidebar-content' );
					break;
				case 'content-sidebar' :
					$body.removeClass( 'sidebar-content' ).addClass( 'with-sidebar' );
					break;
				case 'single-column' :
					$body.removeClass( 'with-sidebar sidebar-content' );
					break;
			}
		});
	});

})( wp, jQuery );
