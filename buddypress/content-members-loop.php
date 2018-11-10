<?php

/**
 * BuddyPress - Members Content Loop
 *
 * See also members/members-loop.php
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
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

	<ul id="members-list" class="item-list" role="main">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li data-permalink="<?php bp_member_permalink(); ?>" <?php bp_member_class(); ?>>
			<div class="item-avatar">
				<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar( array( 'type' => 'full', 'width' => 80, 'height' => 80 ) ); ?></a>
			</div>

			<div class="item">
				<div class="item-title">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
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

			<button class="action-toggle" aria-controls="actions" aria-expanded="false"><?php esc_html_e( 'Actions', 'zeta' ); ?></button>

			<div class="action"><?php

				/**
				 * Fires inside the action section of an individual member listing item.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_directory_members_actions' );

			?></div>
		</li>

	<?php endwhile; ?>

	</ul>
