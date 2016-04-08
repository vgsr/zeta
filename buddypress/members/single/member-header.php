<?php

/**
 * BuddyPress - Users Header
 *
 * Changes to the default template:
 * - Changed url of header avatar to point to profile/change-avatar when `bp_is_my_profile()`
 * - Added profile/change-avatar link for `bp_is_my_profile()`
 * - Moved 'bp_profile_header_meta' hook to entry meta {@see zeta_bp_entry_meta()}
 * - Moved user @-mention name and last activity to `zeta_bp_entry_meta()`
 * - Removed #item-meta, brought #latest-update and #item-buttons one level up
 * - Removed whitespace from #item-buttons
 * - Renamed #item-buttons to #item-actions
 * - Added actions toggle button .item-actions-toggle
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<?php

/**
 * Fires before the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar">
	<a class="item-avatar" href="<?php bp_is_my_profile() ? bp_members_component_link( 'profile', 'change-avatar' ) : bp_displayed_user_link(); ?>">
		<?php bp_displayed_user_avatar( array( 'type' => 'full' ) ); ?>
	</a>

	<?php if ( bp_is_my_profile() ) : ?>

	<a class="change-avatar" href="<?php bp_members_component_link( 'profile', 'change-avatar' ); ?>">
		<span class="bp-screen-reader-text"><?php _e( 'Change your profile photo', 'zeta' ); ?></span>
	</a>

	<?php endif; ?>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<?php

	/**
	 * Fires before the display of the member's header meta.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_member_header_meta' ); ?>

	<?php if ( bp_is_active( 'activity' ) && bp_get_activity_latest_update( bp_displayed_user_id() ) ) : ?>

	<div id="latest-update"><?php

		bp_activity_latest_update( bp_displayed_user_id() );

	?></div>

	<?php endif; ?>

	<div id="item-actions"><?php

		/**
		 * Fires in the member header actions section.
		 *
		 * @since 1.2.6
		 */
		do_action( 'bp_member_header_actions' );

	?></div><!-- #item-actions -->

	<button class="item-actions-toggle" aria-controls="actions" aria-expanded="false">
		<?php _e( 'Actions', 'zeta' ); ?>
	</button>

</div><!-- #item-header-content -->

<?php

/**
 * Fires after the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_member_header' );

/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
do_action( 'template_notices' ); ?>
