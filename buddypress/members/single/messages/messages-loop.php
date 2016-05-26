<?php

/**
 * BuddyPress - Members Messages Loop
 *
 * Changes to the default template:
 * - Replaced table structure with list structure
 * - Removed list header
 * - Moved thread avatar into .bulk-select-check and wrapped in .thread-avatar
 * - Removed the 'From:' and 'To:' title prefixes
 * - Replaced `bp_messages_thread_total_and_unread_count()` with `zeta_bp_message_thread_total_and_unread_count()`
 * - Added .thread-activity and moved .activity into it
 * - Moved .thread-star after .thread-activity
 * - Split .thread-info into new li.thread-title and new li.thread-excerpt
 * - Modified .thread-options links to contain span.icon and span.bp-screen-reader-text
 * - Added list footer with .bulk-select-all
 * - Moved .messages-options-nav into the list footer
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

/**
 * Fires before the members messages loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_member_messages_loop' ); ?>

<?php if ( bp_has_message_threads( bp_ajax_querystring( 'messages' ) ) ) : ?>

	<div class="pagination no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php bp_messages_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php

	/**
	 * Fires after the members messages pagination display.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_member_messages_pagination' ); ?>

	<?php

	/**
	 * Fires before the members messages threads.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_member_messages_threads' ); ?>

	<form action="<?php echo bp_loggedin_user_domain() . bp_get_messages_slug() . '/' . bp_current_action() ?>/bulk-manage/" method="post" id="messages-bulk-management">

		<ul id="message-threads" class="messages-notices table-list">
			<?php while ( bp_message_threads() ) : bp_message_thread(); ?>

			<li>
				<ul id="m-<?php bp_message_thread_id(); ?>" class="<?php bp_message_css_class(); ?> <?php echo bp_message_thread_has_unread() ? 'unread' : 'read'; ?>">
					<li class="bulk-select-check">
						<label for="bp-message-thread-<?php bp_message_thread_id(); ?>">
							<input type="checkbox" name="message_ids[]" id="bp-message-thread-<?php bp_message_thread_id(); ?>" class="message-check" value="<?php bp_message_thread_id(); ?>" />
							<div class="thread-avatar"><?php bp_message_thread_avatar( array( 'width' => 50, 'height' => 50 ) ); ?></div>
							<span class="bp-screen-reader-text"><?php _e( 'Select this message', 'buddypress' ); ?></span>
						</label>
					</li>

					<?php if ( 'sentbox' != bp_current_action() ) : ?>
						<li class="thread-from">
							<span class="from"><?php bp_message_thread_from(); ?></span>
							<?php zeta_bp_message_thread_total_and_unread_count(); ?>
						</li>
					<?php else: ?>
						<li class="thread-from">
							<span class="to"><?php bp_message_thread_to(); ?></span>
							<?php zeta_bp_message_thread_total_and_unread_count(); ?>
						</li>
					<?php endif; ?>

					<li class="thread-activity">
						<span class="activity"><?php bp_message_thread_last_post_date(); ?></span>
					</li>

					<?php if ( bp_is_active( 'messages', 'star' ) ) : ?>
						<li class="thread-star">
							<?php bp_the_message_star_action_link( array( 'thread_id' => bp_get_message_thread_id() ) ); ?>
						</li>
					<?php endif; ?>

					<li class="thread-title">
						<p><a href="<?php bp_message_thread_view_link(); ?>" title="<?php esc_attr_e( "View Message", 'buddypress' ); ?>"><?php bp_message_thread_subject(); ?></a></p>
					</li>

					<li class="thread-excerpt">
						<p><?php bp_message_thread_excerpt(); ?></p>
					</li>

					<?php

					/**
					 * Fires inside the messages box table row to add a new column.
					 *
					 * This is to primarily add a <li> cell to the message box table. Use the
					 * related 'bp_messages_inbox_list_header' hook to add a <li> header cell.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_messages_inbox_list_item' ); ?>

					<li class="thread-options">
						<?php if ( bp_message_thread_has_unread() ) : ?>
							<a class="read primary" href="<?php bp_the_message_thread_mark_read_url();?>"><span class="icon"></span><span class="bp-screen-reader-text"><?php _e( 'Read', 'buddypress' ); ?></span></a>
						<?php else : ?>
							<a class="unread" href="<?php bp_the_message_thread_mark_unread_url();?>"><span class="icon"></span><span class="bp-screen-reader-text"><?php _e( 'Unread', 'buddypress' ); ?></span></a>
						<?php endif; ?>

						<a class="delete secondary confirm" href="<?php bp_message_thread_delete_link(); ?>"><span class="icon"></span><span class="bp-screen-reader-text"><?php _e( 'Delete', 'buddypress' ); ?></span></a>

						<?php

						/**
						 * Fires after the thread options links for each message in the messages loop list.
						 *
						 * @since 2.5.0
						 */
						do_action( 'bp_messages_thread_options' ); ?>
					</li>
				</ul>
			</li>

			<?php endwhile; ?>

			<li>
				<ul>
					<li scope="col" class="thread-checkbox bulk-select-all"><input id="select-all-messages" type="checkbox"><label class="bp-screen-reader-text" for="select-all-messages"><?php _e( 'Select all', 'buddypress' ); ?></label></li>
					<li class="messages-options-nav"><?php bp_messages_bulk_management_dropdown(); ?></li><!-- .messages-options-nav -->
				</ul>
			</li>
		</ul><!-- #message-threads -->

		<?php wp_nonce_field( 'messages_bulk_nonce', 'messages_bulk_nonce' ); ?>
	</form>

	<?php

	/**
	 * Fires after the members messages threads.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_member_messages_threads' ); ?>

	<?php

	/**
	 * Fires and displays member messages options.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_member_messages_options' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'buddypress' ); ?></p>
	</div>

<?php endif;?>

<?php

/**
 * Fires after the members messages loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_member_messages_loop' ); ?>
