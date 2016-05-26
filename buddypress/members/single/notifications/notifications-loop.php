<?php

/**
 * BuddyPress - Members Notifications Loop
 *
 * Changes to the default template:
 * - Replaced table structure with list structure
 * - Removed list header
 * - Removed empty .icon column
 * - Marked list items with #notification-{$id}
 * - Added arguments array to `bp_the_notification_action_links()`
 * - Added list footer with .bulk-select-all
 * - Moved .notifications-options-nav into the list footer
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<form action="" method="post" id="notifications-bulk-management">
	<ul class="notifications table-list">
		<?php while ( bp_the_notifications() ) : bp_the_notification(); ?>

		<li id="notification-<?php bp_the_notification_id(); ?>">
			<ul>
				<li class="bulk-select-check"><label for="<?php bp_the_notification_id(); ?>"><input id="<?php bp_the_notification_id(); ?>" type="checkbox" name="notifications[]" value="<?php bp_the_notification_id(); ?>" class="notification-check"><span class="bp-screen-reader-text"><?php _e( 'Select this notification', 'buddypress' ); ?></span></label></li>
				<li class="notification-description"><?php bp_the_notification_description() ?></li>
				<li class="notification-since"><?php bp_the_notification_time_since(); ?></li>
				<li class="notification-actions"><?php bp_the_notification_action_links( array( 'sep' => '' ) ); ?></li>
			</ul>
		</li>

		<?php endwhile; ?>

		<li>
			<ul>
				<li class="bulk-select-all"><input id="select-all-notifications" type="checkbox"><label class="bp-screen-reader-text" for="select-all-notifications"><?php _e( 'Select all', 'buddypress' ); ?></label></li>
				<li class="notifications-options-nav"><?php bp_notifications_bulk_management_dropdown(); ?></li><!-- .notifications-options-nav -->
			</ul>
		</li>
	</ul>

	<?php wp_nonce_field( 'notifications_bulk_nonce', 'notifications_bulk_nonce' ); ?>
</form>
