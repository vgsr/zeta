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
	$user_id = bp_displayed_user_id();

	// Profile of VGSR user
	if ( $user_id && is_user_vgsr( $user_id ) && $jaargroep = vgsr_get_jaargroep( $user_id ) ) {
		echo '<span class="jaargroep">' . sprintf( esc_html__( 'Jaargroep %s', 'vgsr' ), $jaargroep ) . '</span>';
	}
}
add_action( 'bp_profile_header_meta', 'zeta_vgsr_bp_profile_header_meta' );
