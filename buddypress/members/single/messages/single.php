<?php

/**
 * BuddyPress - Members Single Message
 *
 * Changes to the default template:
 * - Added .thread-actions after the thread title
 * - Moved delete action link with new star and unread action links into .thread-actions
 * - Removed .avatar-box and moved avatar out of it
 * - Avatar args in array format
 * - Changed reply section title to h4.reply-title and moved it out of .avatar-box
 * - Replaced textarea with {@link zeta_editor()}
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

<div id="message-thread">

	<?php

	/**
	 * Fires before the display of a single member message thread content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_message_thread_content' ); ?>

	<?php if ( bp_thread_has_messages() ) : ?>

		<h3 id="message-subject"><?php bp_the_thread_subject(); ?></h3>

		<div class="thread-options">
			<a class="unread" href="<?php zeta_bp_the_thread_mark_unread_url();?>"><span class="icon"></span> <span class="bp-screen-reader-text"><?php _e( 'Unread', 'buddypress' ); ?></span></a>
			<a class="delete confirm" href="<?php bp_the_thread_delete_link(); ?>"><span class="icon"></span> <span class="bp-screen-reader-text"><?php _e( 'Delete', 'buddypress' ); ?></span></a>
		</div>

		<p id="message-recipients">
			<span class="highlight">
				<?php if ( bp_get_thread_recipients_count() <= 1 ) :
					_e( 'You are alone in this conversation.', 'buddypress' );
				
				elseif ( bp_get_max_thread_recipients_to_list() <= bp_get_thread_recipients_count() ) :
					printf( __( 'Conversation between %s recipients.', 'buddypress' ), number_format_i18n( bp_get_thread_recipients_count() ) );

				else :
					printf( __( 'Conversation between %s and you.', 'buddypress' ), bp_get_thread_recipients_list() );

				endif; ?>
			</span>

			<?php
			
			/**
			 * Fires after the action links in the header of a single message thread.
			 *
			 * @since 2.5.0
			 */
			do_action( 'bp_after_message_thread_recipients' ); ?>
		</p>

		<?php

		/**
		 * Fires before the display of the message thread list.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_before_message_thread_list' ); ?>

		<?php while ( bp_thread_messages() ) : bp_thread_the_message(); ?>
			<?php bp_get_template_part( 'members/single/messages/message' ); ?>
		<?php endwhile; ?>

		<?php

		/**
		 * Fires after the display of the message thread list.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_after_message_thread_list' ); ?>

		<?php

		/**
		 * Fires before the display of the message thread reply form.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_before_message_thread_reply' ); ?>

		<form id="send-reply" action="<?php bp_messages_form_action(); ?>" method="post" class="standard-form">

			<div class="message-box">

				<div class="message-metadata">

					<?php

					/** This action is documented in bp-templates/bp-legacy/buddypress-functions.php */
					do_action( 'bp_before_message_meta' ); ?>

					<?php bp_loggedin_user_avatar( array( 'width' => 30, 'height' => 30 ) ); ?>

					<h4 class="reply-title"><?php _e( 'Send a Reply', 'buddypress' ); ?></h4>

					<?php

					/** This action is documented in bp-templates/bp-legacy/buddypress-functions.php */
					do_action( 'bp_after_message_meta' ); ?>

				</div><!-- .message-metadata -->

				<div class="message-content">

					<?php

					/**
					 * Fires before the display of the message reply box.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_before_message_reply_box' ); ?>

					<label for="message_content" class="bp-screen-reader-text"><?php _e( 'Reply to Message', 'buddypress' ); ?></label>
					<?php zeta_editor( '', 'message_content', array( 'textarea_name' => 'content' ) ); ?>

					<?php

					/**
					 * Fires after the display of the message reply box.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_after_message_reply_box' ); ?>

					<div class="submit">
						<input type="submit" name="send" value="<?php esc_attr_e( 'Send Reply', 'buddypress' ); ?>" id="send_reply_button"/>
					</div>

					<input type="hidden" id="thread_id" name="thread_id" value="<?php bp_the_thread_id(); ?>" />
					<input type="hidden" id="messages_order" name="messages_order" value="<?php bp_thread_messages_order(); ?>" />
					<?php wp_nonce_field( 'messages_send_message', 'send_message_nonce' ); ?>

				</div><!-- .message-content -->

			</div><!-- .message-box -->

		</form><!-- #send-reply -->

		<?php

		/**
		 * Fires after the display of the message thread reply form.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_after_message_thread_reply' ); ?>

	<?php endif; ?>

	<?php

	/**
	 * Fires after the display of a single member message thread content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_message_thread_content' ); ?>

</div>
