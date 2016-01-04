<?php

/**
 * BuddyPress - Groups Single Members
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) ) ) : ?>

	<?php do_action( 'bp_before_group_members_content' ); ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_group_members_list' ); ?>

	<ul id="member-list" class="item-list" role="main">

		<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

			<li>
				<div class="item-avatar">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar( array( 'width' => 80, 'height' => 80 ) ); ?></a>
				</div>

				<div class="item">
					<div class="item-title">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
					</div>

					<div class="item-meta">
						<span class="activity"><?php bp_group_member_joined_since(); ?></span>
					</div>

					<?php do_action( 'bp_group_members_list_item' ); ?>

				</div>

				<?php if ( bp_is_active( 'friends' ) ) : ?>

					<div class="action">

						<?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ); ?>

						<?php do_action( 'bp_group_members_list_item_action' ); ?>

					</div>

				<?php endif; ?>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_group_members_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No members were found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>
