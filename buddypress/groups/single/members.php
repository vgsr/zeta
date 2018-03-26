<?php

/**
 * BuddyPress - Groups Single Members
 *
 * Changes from the default bp-legacy template:
 * - Renamed list #member-list to #members-list
 * - Added `bp_member_class()` to the list element
 * - Wrapped item title in .item-title
 * - Wrapped item joined since in .item-meta
 * - Removed whitespace from .action
 * - Removed the friends condition and add-friend button
 * - Removed the 'bp_group_members_list_item_action' hook
 * - Added button.action-toggle for displaying item actions
 * - Renamed .activity to .joined-since
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) ) ) : ?>

	<?php

	/**
	 * Fires before the display of the group members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_group_members_content' ); ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the display of the group members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_group_members_list' ); ?>

	<ul id="members-list" class="item-list" role="main">

		<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

			<li <?php bp_member_class(); ?>>
				<div class="item-avatar">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar( array( 'width' => 80, 'height' => 80 ) ); ?></a>
				</div>

				<div class="item">
					<div class="item-title">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
					</div>

					<div class="item-meta">
						<span class="joined-since"><?php bp_group_member_joined_since(); ?></span>
					</div>

					<?php

					/**
					 * Fires inside the listing of an individual group member listing item.
					 *
					 * In bp-legacy this hook is called 'bp_group_members_list_item'.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_directory_members_item' ); ?>

				</div>

				<div class="action"><?php

					/**
					 * Fires inside the action section of an individual group member listing item.
					 *
					 * In bp-legacy this hook is called 'bp_group_members_list_item_action'.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_directory_members_actions' );

				?></div>

				<button class="action-toggle" aria-controls="actions" aria-expanded="false"><?php _e( 'Actions', 'zeta' ); ?></button>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the display of the group members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_group_members_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires after the display of the group members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No members were found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>
