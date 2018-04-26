<?php

/**
 * BuddyPress template tags and filters for this theme.
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Entry ******************************************************************/

/**
 * Output entry meta's for a BuddyPress page
 *
 * @since 1.0.0
 */
function zeta_bp_entry_meta() {

	// Bail when this is not BuddyPress
	if ( ! function_exists( 'buddypress' ) || ! is_buddypress() )
		return;

	// Single user
	if ( bp_is_user() ) {

		// User mention nicename
		if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) {
			printf( '<span class="user-nicename">@%s</span>', bp_get_displayed_user_mentionname() );
		}

		// User member types
		if ( $member_types = bp_get_member_type( bp_displayed_user_id(), false ) ) {
			foreach ( (array) $member_types as $member_type ) :

				// Skip when the member type does not exist
				if ( ! $member_type = bp_get_member_type_object( $member_type ) )
					continue;

				printf( '<span class="member-type member-type-%s">%s</span>', esc_attr( $member_type->name ), esc_html( $member_type->labels['singular_name'] ) );
			endforeach;
		}

		/**
		 * Fires after the member header actions section.
		 *
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_profile_header_meta' );

		// User activity
		// printf( '<span class="last-activity">%s</span>', bp_get_last_activity( bp_displayed_user_id() ) );
	}

	// Single group
	if ( bp_is_group() ) {

		// Get current group
		$group_id = groups_get_current_group();
	
		// Group type
		printf( '<span class="group-type">%s</span>', bp_get_group_type( $group_id ) );

		// Member count
		$count = bp_get_group_total_members( $group_id );
		printf( '<span class="member-count">%s</span>', sprintf( _n( '%s member', '%s members', $count, 'buddypress' ), bp_core_number_format( $count ) ) );

		/**
		 * Fires after the group header actions section.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_group_header_meta' );

		// Group activity
		// printf( '<span class="last-activity">%s</span>', sprintf( esc_html__( 'active %s', 'buddypress' ), bp_get_group_last_active( $group_id ) ) );
	}
}
add_action( 'zeta_entry_meta', 'zeta_bp_entry_meta' );

/** XProfile ***************************************************************/

/**
 * Append the profile group edit link to the profile group name
 *
 * @since 1.0.0
 *
 * @param string $name Group name
 * @return string Group name
 */
function zeta_bp_profile_group_edit_link( $name ) {

	// Bail when the user is not capable
	if ( ! bp_is_my_profile() && ! current_user_can( 'bp_moderate' ) )
		return $name;

	// Bail when not on the profile page or editing it
	if ( ! bp_is_user_profile() || bp_is_user_profile_edit() )
		return $name;

	// Bail when the profile group is invalid
	if ( ! $group_id = bp_get_the_profile_group_id() )
		return $name;

	// Define profile group edit link
	$link_html = ' <a href="%s" class="edit-field-group dashicons-before dashicons-edit"><span class="screen-reader-text">%s</span></a>';
	$edit_link = trailingslashit( bp_displayed_user_domain() . buddypress()->profile->slug . '/edit/group/' . $group_id );

	// Append the edit link
	$name .= sprintf( $link_html, esc_url( $edit_link ), esc_html__( 'Edit this profile field group', 'zeta' ) );

	return $name;
}
add_filter( 'bp_get_the_profile_group_name', 'zeta_bp_profile_group_edit_link' );

/**
 * Display custom content before the profile loop
 *
 * @since 1.0.0
 */
function zeta_bp_before_profile_loop_content() {

	// When no profile data is registered
	if ( ! bp_has_profile() ) {

		// My profile
		if ( bp_is_my_profile() ) {
			printf( '<p>' . __( 'You have not yet published any profile information about yourself. Please <a href="%s">update your profile</a> so we can get to know you a little better.', 'zeta' ) . '</p>', trailingslashit( bp_displayed_user_domain() . buddypress()->profile->slug . '/edit' ) );

		// Other's profile
		} else {
			echo '<p>' . esc_html__( 'This person has not yet published any profile information about themselves.', 'zeta' ) . '</p>';
		}
	}
}
add_action( 'bp_before_profile_loop_content', 'zeta_bp_before_profile_loop_content' );

/** Activity ***************************************************************/

/**
 * Modify the activity comment content
 *
 * @since 1.0.0
 *
 * @param string $content Activity content
 * @return string Activity content
 */
function zeta_bp_activity_comment_content( $content ) {

	// Only when we're looping activity comments (or loading them through ajax)
	if ( ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) && bp_activity_current_comment() ) {

		// Prepend the comment member's display name to the comment content
		$content = sprintf( '<a href="%s" class="comment-author">%s</a> %s', bp_get_activity_comment_user_link(), bp_get_activity_comment_name(), $content );
	}

	return $content;
}
add_filter( 'bp_get_activity_content', 'zeta_bp_activity_comment_content', 4 );

/**
 * Add links to the activity comment options
 *
 * @since 1.0.0
 */
function zeta_bp_activity_comment_options() {

	// Get the raw comment datetime
	$date = bp_get_activity_comment_date_recorded_raw();

	// Append the 'since' line in the options
	printf( ' <a href="%s" class="time-since acomment-time-since"><time datetime="%s" title="%s">%s</time></a> ',
		bp_get_activity_comment_permalink(),
		mysql2date( 'c', $date ),
		// Mimic `get_comment_date()` and `get_comment_time()`
		sprintf( _x( '%1$s at %2$s', '1: date, 2: time' ), mysql2date( get_option( 'date_format' ), $date ), mysql2date( get_option( 'time_format'), $date, true ) ),
		bp_get_activity_comment_date_recorded()
	);
}
add_action( 'bp_activity_comment_options', 'zeta_bp_activity_comment_options' );

/** Directory **************************************************************/

/**
 * Display the members directory search in the sub navigation
 *
 * @since 1.0.0
 */
function zeta_bp_members_dir_search() {

	// Bail when not on the Members page
	if ( ! bp_is_members_component() )
		return;

	?>

	<li id="members-dir-search" class="dir-search" role="search">
		<?php bp_directory_members_search_form(); ?>
	</li>

	<?php
}
add_action( 'bp_members_directory_member_sub_types', 'zeta_bp_members_dir_search', 1 );

/**
 * Filter the member classes in the loop
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'bp_directory_members_actions'
 *
 * @param array $classes Collection of classes
 * @return array Classes
 */
function zeta_bp_member_class( $classes ) {
	global $members_template;

	// This is a members loop
	if ( isset( $members_template->member ) ) {

		// Collect member actions
		ob_start();
		do_action( 'bp_directory_members_actions' );

		// Add class when the current item does have classes
		if ( ob_get_clean() ) {
			$classes[] = 'has-actions';
		}
	}

	return $classes;
}
add_filter( 'bp_get_member_class', 'zeta_bp_member_class' );

/**
 * Filter the group classes in the loop
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'bp_directory_groups_actions'
 *
 * @param array $classes Collection of classes
 * @return array Classes
 */
function zeta_bp_group_class( $classes ) {
	global $groups_template;

	// This is a groups loop
	if ( isset( $groups_template->group ) ) {

		// Collect group actions
		ob_start();
		do_action( 'bp_directory_groups_actions' );

		// Add class when the current item does have classes
		if ( ob_get_clean() ) {
			$classes[] = 'has-actions';
		}
	}

	return $classes;
}
add_filter( 'bp_get_group_class',  'zeta_bp_group_class' );

/** Messages ***************************************************************/

/**
 * Wrap the Starred Messages template content with .messages
 *
 * This harmonizes the starred messages markup with the Inbox screen.
 *
 * @since 1.0.0
 */
function zeta_bp_messages_screen_star_wrap() {
	add_action( 'bp_before_member_plugin_template', function() {
		echo '<div class="messages">';
	}, 0 );
	add_action( 'bp_after_member_plugin_template', function() {
		echo '</div>';
	}, 99 );
}
add_action( 'bp_messages_screen_star', 'zeta_bp_messages_screen_star_wrap' );

/**
 * Return the short date stamp for a given timestamp
 *
 * Returns different versions of the date for within last 24 hours, within
 * the current year or otherwise.
 *
 * @since 1.0.0
 *
 * @param int $timestamp Timestamp in seconds, like results from strtotime()
 * @return string Date stamp
 */
function zeta_bp_get_date_stamp( $timestamp ) {

	// Get this moment
	$now = time();

	// Date is within the last 24 hours
	if ( ( $now - $timestamp ) <= ( 24 * HOUR_IN_SECONDS ) ) {
		$date = date_i18n( 'H:i', $timestamp );

	// Date is in the same year
	} elseif ( date( 'Y', $now ) == date( 'Y', $timestamp ) ) {
		$date = date_i18n( 'j M', $timestamp );

	// Fallback to 01-01-01
	} else {
		$date = date_i18n( 'd-m-y', $timestamp );
	}

	return $date;
}

/**
 * Modify the messages thread last post date
 *
 * @since 1.0.0
 *
 * @param string $formatted_date Formatted date
 * @return string Formatted date
 */
function zeta_bp_messages_thread_last_post_date( $formatted_date ) {

	// Get the date timestamp
	$date = zeta_bp_get_date_stamp( strtotime( bp_get_message_thread_last_post_date_raw() ) );

	return $date;
}
add_filter( 'bp_get_message_thread_last_post_date', 'zeta_bp_messages_thread_last_post_date' );

/**
 * Modify the messages message post date
 *
 * @since 1.0.0
 *
 * @param string $formatted_date Formatted date
 * @return string Formatted date
 */
function zeta_bp_get_the_thread_message_time_since( $formatted_date ) {

	// Get the date timestamp
	$date = zeta_bp_get_date_stamp( bp_get_the_thread_message_date_sent() );

	return $date;
}
add_filter( 'bp_get_the_thread_message_time_since', 'zeta_bp_get_the_thread_message_time_since' );

/**
 * Output the messages thread date stamp
 *
 * @since 1.0.0
 */
function zeta_bp_message_thread_date_stamp() {
	echo zeta_bp_get_message_thread_date_stamp();
}

	/**
	 * Returns the messages thread date stamp
	 *
	 * @since 1.0.0
	 *
	 * @return string Date stamp
	 */
	function zeta_bp_get_message_thread_date_stamp() {

		// Get the SQL date stamp
		$date = bp_get_message_thread_last_post_date_raw();

		// Parse markup
		$stamp = sprintf( '<time datetime="%s" title="%s">%s</time>',
			mysql2date( 'c', $date ),
			sprintf( _x( '%1$s at %2$s', '1: date, 2: time' ), mysql2date( get_option( 'date_format' ), $date ), mysql2date( get_option( 'time_format' ), $date ) ),
			bp_get_message_thread_last_post_date()
		);

		return $stamp;
	}

/**
 * Output the thread message date stamp
 *
 * @since 1.0.0
 */
function zeta_bp_the_thread_message_date_stamp() {
	echo zeta_bp_get_the_thread_message_date_stamp();
}

	/**
	 * Returns the thread message date stamp
	 *
	 * @since 1.0.0
	 *
	 * @return string Date stamp
	 */
	function zeta_bp_get_the_thread_message_date_stamp() {
		global $thread_template;

		// Get the SQL date stamp
		$date = $thread_template->message->date_sent;

		// Parse markup
		$stamp = sprintf( '<time datetime="%s" title="%s">%s</time>',
			mysql2date( 'c', $date ),
			sprintf( _x( '%1$s at %2$s', '1: date, 2: time' ), mysql2date( get_option( 'date_format' ), $date ), mysql2date( get_option( 'time_format' ), $date ) ),
			bp_get_the_thread_message_time_since()
		);

		return $stamp;
	}

/**
 * Implements a modified version of BuddyPress's equivalent which is without a filter
 *
 * @see bp_message_thread_total_and_unread_count()
 *
 * @since 1.0.0
 *
 * @param int $thread_id Thread ID
 */
function zeta_bp_message_thread_total_and_unread_count( $thread_id = 0 ) {
	if ( ! $thread_id ) {
		$thread_id = bp_get_message_thread_id();
	}

	// Define local variables
	$total  = bp_get_message_thread_total_count( $thread_id );
	$unread = bp_get_message_thread_unread_count( $thread_id );
	$text   = '<span class="bp-screen-reader-text">%2$s</span>';

	// Only display count when there is more than 1 message in the thread
	if ( $total > 1 ) {
		$text = '<span class="thread-count">%1$s</span> ' . $text;
	}

	// Parse counts
	$count = sprintf(
		$text,
		number_format_i18n( $total ),
		sprintf( _n( '%d unread', '%d unread', $unread, 'buddypress' ), number_format_i18n( $unread ) )
	);

	echo $count;
}

/**
 * Display the current thread's mark unread url
 *
 * @see bp_the_message_thread_mark_unread_url()
 *
 * @since 1.0.0
 */
function zeta_bp_the_thread_mark_unread_url() {
	echo esc_url( zeta_bp_get_the_thread_mark_unread_url() );
}

	/**
	 * Return the current thread's mark unread url
	 *
	 * @see bp_get_the_message_thread_mark_unread_url()
	 *
	 * @since 1.0.0
	 *
	 * @return string Thread mark unread url
	 */
	function zeta_bp_get_the_thread_mark_unread_url() {

		// Get the thread ID.
		$id = bp_get_the_thread_id();

		// Get the args to add to the URL.
		$args = array(
			'action'     => 'unread',
			'message_id' => $id
		);

		// Base unread URL.
		$url = trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() . '/inbox/unread' );

		// Add the args to the URL.
		$url = add_query_arg( $args, $url );

		// Add the nonce.
		$url = wp_nonce_url( $url, 'bp_message_thread_mark_unread_' . $id );

		return $url;
	}

/**
 * Modify the thread message css classes
 *
 * @since 1.0.0
 *
 * @param array $classes Thread message css classes
 * @return array CSS classes
 */
function zeta_bp_thread_messages_css_class( $classes ) {
	global $thread_template;

	// Collapse all thread messages but the last one
	if ( $thread_template->message_count > 1 && $thread_template->current_message + 1 < $thread_template->message_count
		// And the message is not starred
		&& ! bp_messages_is_message_starred( bp_get_the_thread_message_id() )
	) {
		$classes[] = 'collapsed';
	}

	return $classes;
}
add_filter( 'bp_get_the_thread_message_css_class', 'zeta_bp_thread_messages_css_class' );

/** Notifications **********************************************************/

/**
 * Modify the notifications mark read link
 *
 * @since 1.0.0
 *
 * @param string $link Mark read link
 * @return string Mark read link
 */
function zeta_bp_notifications_mark_read_link( $link ) {

	// Rewrite the link
	$link = sprintf( '<a href="%s" class="mark-read primary"><span class="icon"></span><span class="bp-screen-reader-text">%s</span></a>',
		esc_url( bp_get_the_notification_mark_read_url() ),
		__( 'Mark notification as read', 'zeta' )
	);

	return $link;
}
add_filter( 'bp_get_the_notification_mark_read_link', 'zeta_bp_notifications_mark_read_link' );

/**
 * Modify the notifications mark unread link
 *
 * @since 1.0.0
 *
 * @param string $link Mark unread link
 * @return string Mark unread link
 */
function zeta_bp_notifications_mark_unread_link( $link ) {

	// Rewrite the link
	$link = sprintf( '<a href="%s" class="mark-unread"><span class="icon"></span><span class="bp-screen-reader-text">%s</span></a>',
		esc_url( bp_get_the_notification_mark_unread_url() ),
		__( 'Mark notification as unread', 'zeta' )
	);

	return $link;
}
add_filter( 'bp_get_the_notification_mark_unread_link', 'zeta_bp_notifications_mark_unread_link' );

/**
 * Modify the notifications delete link
 *
 * @since 1.0.0
 *
 * @param string $link Delete link
 * @return string Delete link
 */
function zeta_bp_notifications_delete_link( $link ) {

	// Rewrite the link
	$link = sprintf( '<a href="%s" class="delete secondary confirm"><span class="icon"></span><span class="bp-screen-reader-text">%s</span></a>',
		esc_url( bp_get_the_notification_delete_url() ),
		__( 'Delete notification', 'zeta' )
	);

	return $link;
}
add_filter( 'bp_get_the_notification_delete_link', 'zeta_bp_notifications_delete_link' );
