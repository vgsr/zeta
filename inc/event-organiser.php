<?php

/**
 * Event Organiser template tags and filters for this theme
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
 * Filter the document page title for event pages
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
 * @uses is_tax()
 * @uses single_term_title()
 * @uses is_post_type_archive()
 * @uses eo_is_event_archive()
 * @uses eo_get_event_archive_date()
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
			$title = sprintf( $title, eo_get_event_archive_date( _x( 'Y', 'Event archives title: Year', 'zeta' ) ) );

		// Month archives
		} elseif ( eo_is_event_archive( 'month' ) ) {
			$title = sprintf( $title, eo_get_event_archive_date( _x( 'F Y', 'Event archives title: Month', 'zeta' ) ) );

		// Day archives
		} elseif ( eo_is_event_archive( 'day' ) ) {
			$title = sprintf( $title, eo_get_event_archive_date( _x( 'jS F Y', 'Event archives title: Day', 'zeta' ) ) );

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

	// Check the archive type
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

	// Pass the `$date` as array elements to the native archive link getter
	$url = call_user_func_array( 'eo_get_event_archive_link', explode( '-', $date ) );

	return $url;
}

/**
 * Display navigation to next/previous set of posts when applicable
 *
 * @see the_posts_navigation()
 *
 * @since 1.0.0
 *
 * @uses zeta_event_organiser_get_previous_archive_link()
 * @uses zeta_event_organiser_get_next_archive_link()
 * @uses get_previous_posts_link()
 * @uses previous_posts_link()
 * @uses get_next_posts_link()
 * @uses next_posts_link()
 */
function zeta_event_organiser_the_posts_navigation() {

	// Find adjacent event archives
	$archives = array(
		'previous' => zeta_event_organiser_get_previous_archive_link(),
		'next'     => zeta_event_organiser_get_next_archive_link(),
	);

	// Don't print empty markup if there's only one page and no adjacent archives
	if ( $GLOBALS['wp_query']->max_num_pages < 2 && ! array_filter( $archives ) ) {
		return;
	}

	/**
	 * Determine how we navigate pages based on query order.
	 * Descending order, so older events come next
	 *
	 * @see eventorganiser_sort_events()
	 */
	if ( ! empty( $GLOBALS['wp_query']->query_vars['orderby'] )
		&& in_array( $GLOBALS['wp_query']->query_vars['orderby'], array( 'eventstart', 'eventend' ) )
		&& 'DESC' == $GLOBALS['wp_query']->query_vars['order']
	) {
		$first  = 'next';
		$second = 'previous';

	// Ascending order, so newer events come next. This is the default order
	} else {
		$first  = 'previous';
		$second = 'next';		
	}

	?>
	<nav class="navigation posts-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Events navigation', 'zeta' ); ?></h2>
		<div class="nav-links">

			<?php if ( call_user_func( "get_{$first}_posts_link" ) ) : ?>
			<div class="nav-previous"><?php call_user_func_array( "{$first}_posts_link", array( esc_html__( 'Older events', 'zeta' ) ) ); ?></div>
			<?php elseif ( $archives[ $first ] ) : ?>
			<div class="nav-previous"><?php echo $archives[ $first ]; ?></div>
			<?php endif; ?>

			<?php if ( call_user_func( "get_{$second}_posts_link" ) ) : ?>
			<div class="nav-next"><?php call_user_func_array( "{$second}_posts_link", array( esc_html__( 'Newer events', 'zeta' ) ) ); ?></div>
			<?php elseif ( $archives[ $second ] ) : ?>
			<div class="nav-next"><?php echo $archives[ $second ]; ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}

/**
 * Return the link of the next archive
 *
 * @since 1.0.0
 *
 * @uses zeta_event_organiser_get_adjacent_archive_link()
 * @return string Archive link
 */
function zeta_event_organiser_get_next_archive_link() {
	return zeta_event_organiser_get_adjacent_archive_link( false );
}

/**
 * Return the link of the previous archive
 *
 * @since 1.0.0
 *
 * @uses zeta_event_organiser_get_adjacent_archive_link()
 * @return string Archive link
 */
function zeta_event_organiser_get_previous_archive_link() {
	return zeta_event_organiser_get_adjacent_archive_link( true );
}

/**
 * Return the link of the previous archive of the same type.
 *
 * @since 1.0.0
 *
 * @uses eo_is_event_archive()
 * @uses zeta_event_organiser_get_archive_url()
 * @uses zeta_pagenum_link()
 * @uses apply_filters() Calls '{previous|next}_posts_link_attributes'
 *
 * @param bool $previous Optional. Whether to return the previous or next archive. Default true.
 * @return string Archive link
 */
function zeta_event_organiser_get_adjacent_archive_link( $previous = true ) {
	global $wp_query;

	// Bail when these are not event archives
	if ( ! eo_is_event_archive() )
		return;

	$adjacent = $previous ? 'previous' : 'next';

	// Find the first adjacent post following the current query
	// and then serve that event's year/month/day event archive.
	$qv = $wp_query->query_vars;
	$qv['paged'] = false;
	$qv['posts_per_page'] = 1;
	$qv['ondate'] = false;
	$qv['event_end_after'] = false;

	/**
	 * When querying in Event Organiser for events with {start|end}_{before|after}
	 * dates, the given date is included in the resulting query. Hence, we need
	 * to subtract|add a single day for previous|next date queries.
	 */
	if ( $previous ) {
		$start_before = new DateTime( $wp_query->query_vars['event_end_after'] );
		$start_before->modify( '-1 day' );

		$qv['event_start_before'] = $start_before->format( 'Y-m-d H:i:s' );
	} else {
		$start_after = new DateTime( $wp_query->query_vars['event_start_before'] );
		$start_after->modify( '+1 day' );

		$qv['event_start_before'] = false;
		$qv['event_start_after'] = $start_after->format( 'Y-m-d H:i:s' );
	}

	// Follow Event Organiser in defaulting to 'eventstart' order
	if ( empty( $wp_query->query_vars['orderby'] ) ) {
		$qv['orderby'] = 'eventstart';
	}

	if ( ! empty( $wp_query->query_vars['orderby'] )
		&& in_array( $wp_query->query_vars['orderby'], array( 'eventstart', 'eventend' ) )
		&& 'DESC' == $wp_query->query_vars['order']
	) {
		$qv['order'] = $previous ? 'ASC' : 'DESC';
	} else {
		$qv['order'] = $previous ? 'DESC' : 'ASC';
	}

	// An adjacent event was found
	if ( ( $q = new WP_Query( $qv ) ) && $q->posts ) {

		// Get the adjacent date
		$date = strtotime( $q->posts[0]->StartDate );

		// Define query vars for the archive pagination query
		$aqv = $wp_query->query_vars;
		$aqv['paged'] = false;

		// Define adjacent archive link args
		if ( eo_is_event_archive( 'year' ) ) {
			$type = 'year';
			$label = sprintf( esc_html_x( 'Events in %s', 'For yearly archives', 'zeta' ), date( 'Y', $date ) );

			// Pagination query
			$aqv['ondate']             = date( 'Y', $date );
			$aqv['event_start_before'] = date( 'Y-12-31 00:00:00', $date );
			$aqv['event_end_after']    = date( 'Y-01-01 00:00:00', $date );

		} elseif ( eo_is_event_archive( 'month' ) ) {
			$type = 'month';
			$label = sprintf( esc_html_x( 'Events in %s', 'For monthly archives', 'zeta' ), date( 'F', $date ) );

			// Pagination query
			$aqv['ondate']             = date( 'Y/m', $date );
			$aqv['event_start_before'] = date( 'Y-m-t 00:00:00',  $date );
			$aqv['event_end_after']    = date( 'Y-m-01 00:00:00', $date );

		} elseif ( eo_is_event_archive( 'day' ) ) {
			$type = 'day';
			$label = sprintf( esc_html_x( 'Events on %s', 'For daily archives', 'zeta' ), date( get_option( 'date_format' ), $date ) );

			// Pagination query
			$aqv['ondate']             = date( 'Y/m/d', $date );
			$aqv['event_start_before'] = date( 'Y-m-d 00:00:00', $date );
			$aqv['event_end_after']    = date( 'Y-m-d 00:00:00', $date );

		// Default to daily archives
		} else {
			$type  = 'day';
			$label = $previous ? esc_html__( 'Older events', 'zeta' ) : esc_html__( 'Newer events', 'zeta' );
		}

		$url = zeta_event_organiser_get_archive_url( $type, $date );

		// Consider pagination for previous archives
		if ( $previous && ( $aq = new WP_Query( $aqv ) ) && $aq->max_num_pages > 1 ) {
			$url = zeta_pagenum_link( $url, $aq->max_num_pages );
		}

		/**
		 * Filter the anchor tag attributes for the next posts page link.
		 *
		 * @since 2.7.0 WordPress
		 *
		 * @param string $attributes Attributes for the anchor tag.
		 */
		$attr = apply_filters( "{$adjacent}_posts_link_attributes", '' );

		return '<a href="' . esc_url( $url ) . "\" $attr>$label</a>";
	}
}

/**
 * Return whether the date of another event has the same year as the current event
 *
 * @since 1.0.0
 *
 * @see zeta_event_organiser_is_date_same()
 * @return bool Event has the same year
 */
function zeta_event_organiser_is_date_same_year( $query = false, $check = 'next' ) {
	return zeta_event_organiser_is_date_same( 'Y', $query, $check );
}

/**
 * Return whether the date of another event has the same month as the current event
 *
 * @since 1.0.0
 *
 * @see zeta_event_organiser_is_date_same()
 * @return bool Event has the same month
 */
function zeta_event_organiser_is_date_same_month( $query = false, $check = 'next' ) {
	return zeta_event_organiser_is_date_same( 'Y-m', $query, $check );
}

/**
 * Return whether the date of another event has the same day as the current event
 *
 * @since 1.0.0
 *
 * @see zeta_event_organiser_is_date_same()
 * @return bool Event has the same day
 */
function zeta_event_organiser_is_date_same_day( $query = false, $check = 'next' ) {
	return zeta_event_organiser_is_date_same( 'Y-m-d', $query, $check );
}

	/**
	 * Return whether the date of another event is the same as the current date type.
	 *
	 * @since 1.0.0
	 *
	 * @uses zeta_has_posts()
	 * @uses eo_get_the_start()
	 *
	 * @param string             $format Date format. Used to check the date equality.
	 * @param bool|WP_Query      $query  Optional. Query object. Defaults to main query global.
	 * @param string|int|WP_Post $check  Optional. Which post to check against. Either 'prev'
	 *                                   or 'next', which results in the suggested post within
	 *                                   the current loop, event post ID or post object.
	 *                                   Defaults to 'next'.
	 * @return bool Event is of the same date format
	 */
	function zeta_event_organiser_is_date_same( $format = 'Y-m-d', $query = false, $check = 'next' ) {

		// Default the query context to the global main query
		if ( ! $query || ! is_a( $query, 'WP_Query' ) ) {
			$query = $GLOBALS['wp_query'];
		}

		// Bail when this isn't an event query
		if ( 'event' !== $query->query['post_type'] )
			return false;

		// Default to check the next query item
		if ( ! $check ) {
			$check = 'next';
		}

		// Bail when there are no next or previous posts in the query
		if (   ( 'next' === $check && ! zeta_has_posts( $query ) )
			|| ( 'prev' === $check && 0 === $query->current_post )
		)
			return false;

		/**
		 * To compare dates, we're using `eo_get_the_start()`, but it needs
		 * to use the global `$post`. So we set it apart here to override it.
		 */
		$_post = $GLOBALS['post'];

		// Use event start date from the post to compare from
		$GLOBALS['post'] = $query->post;
		$date1 = eo_get_the_start( $format, $query->post->ID );

		// Get the post to compare to
		if ( 'next' === $check ) {
			$post2 = $query->posts[ $query->current_post + 1 ];
		} elseif ( 'prev' === $check ) {
			$post2 = $query->posts[ $query->current_post - 1 ];
		} else {
			$post2 = get_post( $check );
		}

		// Bail when a post wasn't found
		if ( ! $post2 ) {
			$GLOBALS['post'] = $_post;
			return false;
		}

		// Use event start date from the post to compare to
		$GLOBALS['post'] = $post2;
		$date2 = eo_get_the_start( $format, $post2->ID );

		// Restore the global `$post`
		$GLOBALS['post'] = $_post;

		// Check for equality in dates
		$equal = ( $date1 === $date2 );

		return $equal;
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

/** The Event Loop *********************************************************/

/**
 * Print the event loop id attribute
 *
 * @since 1.0.0
 *
 * @uses zeta_event_organiser_loop_arg()
 */
function zeta_event_organiser_loop_id() {
	$id = zeta_event_organiser_loop_arg( 'id' );
	if ( $id ) {
		printf( ' id="%s"', esc_attr( $id ) );
	}
}

/**
 * Print the event loop class attribute
 *
 * @since 1.0.0
 *
 * @uses zeta_event_organiser_loop_arg()
 */
function zeta_event_organiser_loop_class() {
	$class = zeta_event_organiser_loop_arg( 'class' );
	if ( $class ) {
		printf( ' class="%s"', esc_attr( $class ) );
	}
}

/**
 * Return the given argument from the current event loop
 *
 * @since 1.0.0
 *
 * @param string $arg Requested argument name
 * @return mixed|null Requested argument value
 */
function zeta_event_organiser_loop_arg( $arg = '' ) {
	global $eo_event_loop_args;

	if ( isset( $eo_event_loop_args[ $arg ] ) ) {
		return $eo_event_loop_args[ $arg ];
	} else {
		return null;
	}
}
