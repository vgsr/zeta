<?php

/**
 * BuddyPress template tags and filters for this theme.
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Bail when plugin is not active
if ( ! function_exists( 'buddypress' ) ) :
/**
 * Provide a backup function when BuddyPress is not active
 *
 * @since 1.0.0
 *
 * @return bool False
 */
function is_buddypress() {
	return false;
}
endif;

/** Entry ******************************************************************/

/**
 * Output entry meta's for a BuddyPress page
 *
 * @since 1.0.0
 *
 * @uses is_buddypress()
 * @uses bp_is_user()
 * @uses bp_activity_do_mentions()
 * @uses bp_get_displayed_user_mentionname()
 * @uses bp_get_member_type()
 * @uses bp_displayed_user_id()
 * @uses bp_get_member_type_object()
 */
function zeta_bp_entry_meta() {

	// Bail when this is not BuddyPress
	if ( ! is_buddypress() )
		return;

	// Single user
	if ( bp_is_user() ) {

		// User mention nicename
		if ( bp_activity_do_mentions() ) :
			printf( '<span class="user-nicename">@%s</span>', bp_get_displayed_user_mentionname() );
		endif;

		// User member types
		if ( $member_types = bp_get_member_type( bp_displayed_user_id(), false ) ) {
			foreach ( (array) $member_types as $member_type ) :
				$member_type = bp_get_member_type_object( $member_type );
				printf( '<span class="member-type member-type-%s">%s</span>', esc_attr( $member_type->name ), esc_html( $member_type->labels['singular_name'] ) );
			endforeach;
		}

		/**
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		do_action( 'bp_profile_header_meta' );

		// User activity
		printf( '<span class="activity">%s</span>', bp_get_last_activity( bp_displayed_user_id() ) );
	}
}
add_action( 'zeta_entry_meta', 'zeta_bp_entry_meta' );

/** XProfile ***************************************************************/

/**
 * Append the profile group edit link to the profile group name
 *
 * @since 1.0.0
 *
 * @uses bp_is_my_profile()
 * @uses bp_get_the_profile_group_id()
 * @uses bp_displayed_user_domain()
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

/** Activity ***************************************************************/

/**
 * Modify the activity comment content
 *
 * @since 1.0.0
 *
 * @uses bp_activity_current_comment()
 * @uses bp_get_activity_comment_user_link()
 * @uses bp_get_activity_comment_name()
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
 *
 * @uses bp_get_activity_comment_date_recorded_raw()
 * @uses bp_get_activity_comment_permalink()
 * @uses mysql2date()
 * @uses bp_get_activity_comment_date_recorded()
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
