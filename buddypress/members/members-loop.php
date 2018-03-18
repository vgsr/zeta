<?php

/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * Changes to the default template:
 * - Removed current member type message before the top pagination
 * - Added size and type modifiers to the item's avatar. Use 'full' avatars, because the default 'thumb' is 50x50.
 * - Removed whitespace in .action
 * - Added button.action-toggle for displaying item actions
 * - Removed div.clear
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

	<ul id="members-list" class="item-list" role="main">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li <?php bp_member_class(); ?>>
			<div class="item-avatar">
				<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar( array( 'type' => 'full', 'width' => 80, 'height' => 80 ) ); ?></a>
			</div>

			<div class="item">
				<div class="item-title">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
				</div>

				<div class="item-meta">
					<span class="activity"><?php bp_member_last_active(); ?></span>

					<?php if ( bp_get_member_latest_update() ) : ?>
						<span class="update"><?php bp_member_latest_update(); ?></span>
					<?php endif; ?>
				</div>

				<?php

				/**
				 * Fires inside the listing of an individual member listing item.
				 *
				 * If you want to show specific profile fields here you can,
				 * but it'll add an extra query for each member in the loop
				 * (only one regardless of the number of fields you show):
				 *
				 * bp_member_profile_data( 'field=the field name' );
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_directory_members_item' ); ?>

			</div>

			<div class="action"><?php

				/**
				 * Fires inside the action section of an individual member listing item.
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
