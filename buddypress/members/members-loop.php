<?php

/**
 * BuddyPress - Members Loop
 *
 * See also content-members-loop.php
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * Changes to the default template:
 * - Removed current member type message before the top pagination
 * - Added size and type modifiers to the item's avatar. Use 'full' avatars, because the default 'thumb' is 50x50.
 * - Removed whitespace in .action
 * - Added button.action-toggle for displaying item actions
 * - Removed div.clear
 * - Removed .item-meta with .activity and .update with bp_member_latest_update()
 * - Moved the loop contents in a new template file content-members-loop.php
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<?php

/**
 * Fires before the display of members from the members loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the listing of the members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_members_list' ); ?>

	<?php bp_get_template_part( 'content-members-loop' ); ?>

	<?php

	/**
	 * Fires after the listing of the members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the display of members from the members loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_members_loop' ); ?>
