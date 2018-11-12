<?php

/**
 * VGSR template tags and filters for this theme.
 * 
 * @package Zeta
 * @subpackage VGSR
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Bail when plugin is not active
if ( ! function_exists( 'vgsr' ) || version_compare( vgsr()->version, '0.1.2', '<' ) )
	return;

/** BuddyPress *************************************************************/

/**
 * Print entry meta for BuddyPress pages
 *
 * @since 1.0.0
 */
function zeta_vgsr_bp_profile_header_meta() {

	// Get displayed user
	$user_id = bp_displayed_user_id();

	// Determine whether to show details
	$show_details = $user_id && ( is_user_vgsr( $user_id ) || is_user_exlid( $user_id ) );

	// Jaargroep
	if ( $show_details && $jaargroep = vgsr_get_jaargroep( $user_id ) ) {
		echo '<span class="jaargroep">' . sprintf( esc_html__( 'Jaargroep %s', 'vgsr' ), $jaargroep ) . '</span>';
	}
}
add_action( 'bp_profile_header_meta', 'zeta_vgsr_bp_profile_header_meta' );

/**
 * Print members directory loop item meta
 *
 * @since 1.0.0
 */
function zeta_vgsr_bp_directory_members_item() {

	// Get directory member
	$user_id = bp_get_member_user_id();

	// Determine whether to show details
	$show_details = $user_id && ( is_user_vgsr( $user_id ) || is_user_exlid( $user_id ) );

	// Jaargroep
	if ( $show_details && $jaargroep = vgsr_get_jaargroep( $user_id ) ) {
		echo '<span class="jaargroep">' . sprintf( esc_html__( 'Jaargroep %s', 'vgsr' ), $jaargroep ) . '</span>';
	}
}
add_action( 'bp_directory_members_item', 'zeta_vgsr_bp_directory_members_item' );
