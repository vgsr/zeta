<?php
/**
 * BuddyPress template tags and filters for this theme.
 *
 * @package Zeta
 */

/**
 * Display the displayed member's member type
 *
 * @since 1.0.0
 *
 * @uses bp_get_member_type()
 * @uses bp_displayed_user_id()
 */
function zeta_display_member_member_type() {
	$bp          = buddypress();
	$member_type = bp_get_member_type( bp_displayed_user_id() );

	if ( $member_type && isset( $bp->members->types[ $member_type ] ) ) {
		printf( '<span class="member-type">%s</span>', $bp->members->types[ $member_type ]->labels['singular_name'] );
	}
}
add_action( 'bp_before_member_header_meta', 'zeta_display_member_member_type' );

/**
 * Display the edit link for the displayed profile group
 *
 * @since 1.0.0
 *
 * @uses bp_is_my_profile()
 * @uses bp_get_the_profile_group_id()
 * @uses bp_displayed_user_domain()
 */
function zeta_profile_group_edit_link() {

	// Bail when capabilities lack
	if ( ! bp_is_my_profile() && ! current_user_can( 'bp_moderate' ) )
		return;

	// Bail when there's no profile group
	if ( ! $group_id = bp_get_the_profile_group_id() )
		return;

	// Build profile group edit url
	$url = trailingslashit( bp_displayed_user_domain() . buddypress()->profile->slug . '/edit/group/' . $group_id );

	// Output the link
	echo '<a href="' . esc_url( $url ) . '" class="edit-field-group dashicons-before dashicons-edit"><span class="screen-reader-text">' . __( 'Edit this profile field group', 'zeta' ) . '</span></a>';
}
