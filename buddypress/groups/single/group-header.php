<?php

/**
 * BuddyPress - Groups Header
 *
 * Changes to the default template:
 * - Renamed #item-actions to #item-admins
 * - Moved #item-admins inside #item-header-content after the group description
 * - Added class 'item-avatar' the avatar's anchor
 * - Moved 'bp_group_header_meta' hook to entry meta {@see zeta_bp_entry_meta()}
 * - Moved group type and last activity to `zeta_bp_entry_meta()`
 * - Removed #item-meta, brought group description and #item-buttons one level up
 * - Removed whitespace from #item-buttons
 * - Renamed #item-buttons to #item-actions
 * - Added actions toggle button .item-actions-toggle
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_group_header' );

?>

<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
	<div id="item-header-avatar">
		<a  class="item-avatar" href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">
			<?php bp_group_avatar(); ?>
		</a>
	</div><!-- #item-header-avatar -->
<?php endif; ?>

<div id="item-header-content">

	<?php

	/**
	 * Fires before the display of the group's header meta.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_group_header_meta' ); ?>

	<?php bp_group_description(); ?>

	<div id="item-admins">

		<?php if ( bp_group_is_visible() ) : ?>

			<h2><?php _e( 'Group Admins', 'buddypress' ); ?></h2>

			<?php bp_group_list_admins();

			/**
			 * Fires after the display of the group's administrators.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_after_group_menu_admins' );

			if ( bp_group_has_moderators() ) :

				/**
				 * Fires before the display of the group's moderators, if there are any.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_before_group_menu_mods' ); ?>

				<h2><?php _e( 'Group Mods', 'buddypress' ); ?></h2>

				<?php bp_group_list_mods();

				/**
				 * Fires after the display of the group's moderators, if there are any.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_after_group_menu_mods' );

			endif;

		endif; ?>

	</div><!-- #item-admins -->

	<div id="item-actions"><?php

		/**
		 * Fires in the group header actions section.
		 *
		 * @since 1.2.6
		 */
		do_action( 'bp_group_header_actions' );

	?></div><!-- #item-actions -->

	<button class="item-actions-toggle" aria-controls="actions" aria-expanded="false">
		<?php _e( 'Actions', 'zeta' ); ?>
	</button>

</div><!-- #item-header-content -->

<?php

/**
 * Fires after the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_group_header' );

/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
do_action( 'template_notices' );
