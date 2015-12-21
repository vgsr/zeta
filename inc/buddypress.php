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

/**
 * Display the displayed member's member type
 *
 * @since 1.0.0
 *
 * @uses bp_get_member_type()
 * @uses bp_displayed_user_id()
 */
function zeta_bp_display_member_member_type() {
	$bp          = buddypress();
	$member_type = bp_get_member_type( bp_displayed_user_id() );

	if ( $member_type && isset( $bp->members->types[ $member_type ] ) ) {
		printf( '<span class="member-type">%s</span>', $bp->members->types[ $member_type ]->labels['singular_name'] );
	}
}
add_action( 'bp_before_member_header_meta', 'zeta_bp_display_member_member_type' );

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
