<?php

/**
 * Event Organiser template tags and filters for this theme.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Filter the Event Organiser template stack to add ours
 *
 * Only used when `eo_get_template_part()` is called.
 *
 * @since 1.0.0
 *
 * @param array $stack Template stack
 * @return array Template stack
 */
function zeta_event_organiser_template_stack( $stack ) {

	// Push our templates folder to the front
	$stack = array_merge( array( get_stylesheet_directory() . '/event-organiser' ), $stack );

	return $stack;
}
add_filter( 'eventorganiser_template_stack', 'zeta_event_organiser_template_stack' );

/**
 * Filter the page title for event pages
 *
 * Available since WP 4.4.0 in `wp_get_document_title()`.
 *
 * @since 1.0.0
 *
 * @uses zeta_event_organiser_archive_title()
 *
 * @param string $title Page title
 * @return string Page title
 */
function zeta_event_organiser_page_title( $title ) {

	// Run page title through our archive title filter
	$title['title'] = zeta_event_organiser_archive_title( $title['title'] );

	return $title;
}
add_filter( 'document_title_parts', 'zeta_event_organiser_page_title' );

/**
 * Filter the archive title for events
 *
 * @since 1.0.0
 *
 * @param string $title Archive title
 * @return string Archive title
 */
function zeta_event_organiser_archive_title( $title ) {

	// When displaying an event category
	if ( is_tax( 'event-category' ) ) {
		$title = sprintf( __( 'Events: %s', 'zeta' ), single_term_title( '', false ) );
	}

	// When displaying an event tag
	if ( is_tax( 'event-tag' ) ) {
		$title = sprintf( __( 'Events by tag: %s', 'zeta' ), single_term_title( '', false ) );
	}

	// When displaying an event venue
	if ( is_tax( 'event-venue' ) ) {
		$title = sprintf( __( 'Events at %s', 'zeta' ), single_term_title( '', false ) );
	}

	// When displaying event archives of a certain period
	if ( is_post_type_archive( 'event' ) ) {
		$title = __( 'Events: %s', 'zeta' );

		// Year archives
		if ( eo_is_event_archive( 'year' ) ) {
			$title = sprintf( $title, eo_get_event_archive_date( _x( 'Y', 'Year event archives title', 'zeta' ) ) );

		// Month archives
		} elseif ( eo_is_event_archive( 'month' ) ) {
			$title = sprintf( $title, eo_get_event_archive_date( _x( 'F Y', 'Month event archives title', 'zeta' ) ) );

		// Day archives
		} elseif ( eo_is_event_archive( 'day' ) ) {
			$title = sprintf( $title, eo_get_event_archive_date( _x( 'jS F Y', 'Day event archives title', 'zeta' ) ) );

		// Fallback
		} else {
			$title = __( 'Events', 'zeta' );
		}
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'zeta_event_organiser_archive_title' );

/**
 * Return the date's event archive url
 *
 * @since 1.0.0
 *
 * @uses eo_get_the_start()
 * @uses eo_get_event_archive_link()
 *
 * @param string $type The archive type to return the url of
 * @param string $date The date to process. Defaults to the current event's date.
 * @return string Event archive url
 */
function zeta_event_organiser_get_archive_url( $type = 'day', $date = null ) {

	switch ( $type ) {
		case 'year' :
			$format = 'Y';
			break;
		case 'month' :
			$format = 'Y-m';
			break;
		case 'day' :
		default :
			$format = 'Y-m-d';
	}

	if ( ! $date ) {
		$date = eo_get_the_start( $format );
	} else {
		$date = date( $format, $date );
	}

	$url = call_user_func_array( 'eo_get_event_archive_link', explode( '-', $date ) );

	return $url;
}

/**
 * Return whether the date of another event is the same as the current date type.
 *
 * @since 1.0.0
 *
 * @param string $type Date type: 'month' or 'day'
 * @param string|int|WP_Post $like Which post to check. Either 'prev' or 'next', which results in
 *                                 the suggested post within the current loop, event post ID or 
 *                                 post object. Defaults to 'next'.
 * @return bool Event is next of date type
 */
function zeta_event_organiser_is_same( $type = '', $like = 'next' ) {
	global $wp_query, $post;

	// Define date statically
	static $date = array( 'month' => 0, 'day' => 0 );

	// Set initial date values of the most current post
	$date['month'] = $date['day'] = eo_get_the_start( 'U', $post->ID );

	// Bail when no post can be found
	if ( ( 'next' == $like && ! zeta_have_posts() ) || ( 'prev' == $post && 0 == $wp_query->current_post ) ) {
		return false;
	}

	// Keep global variable
	$_post = $post;
	$retval = true; // Assume we're in the same date

	// Get the post to compare
	if ( 'next' == $like ) {
		$post = $wp_query->posts[ $wp_query->current_post + 1 ];
	} elseif ( 'prev' == $like ) {
		$post = $wp_query->posts[ $wp_query->current_post - 1 ];
	} else {
		$post = get_post( $like );
	}

	// Bail when the post type or type is invalid
	if ( ! $post || 'event' != $post->post_type || ! in_array( $type, array( 'month', 'day' ) ) ) {
		return false;
	}

	// When on an event archive page
	if ( eo_is_event_archive() ) {

		// Use event start date to compare dates
		$compare = eo_get_the_start( 'U', $post->ID );
		$map = array( 'month' => 'Y-m', 'day' => 'Y-m-d' );

		// Update the current date
		if ( $compare && date( $map[ $type ], $date[ $type ] ) != date( $map[ $type ], $compare ) ) {
			$date[ $type ] = $compare;

			// When comparing months, update the 'day' date too.
			if ( 'month' == $type ) {
				$date[ 'day' ] = $compare;
			}

			$retval = false;
		}
	}

	// Restore global variable
	$post = $_post;

	return (bool) $retval;
}

/**
 * Display the event's details in the entry footer
 *
 * @since 1.0.0
 *
 * @uses get_the_term_list()
 */
function zeta_event_organiser_entry_footer() {

	// Show event categories for events
	if ( 'event' == get_post_type() ) {

		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_term_list( get_the_ID(), 'event-category', '', __( ', ', 'zeta' ) );
		if ( $categories_list ) {
			printf( '<span class="event-category-list">' . __( 'Posted in %s', 'zeta' ) . '</span>', $categories_list );
		}
	}
}
add_action( 'zeta_entry_footer', 'zeta_event_organiser_entry_footer' );
