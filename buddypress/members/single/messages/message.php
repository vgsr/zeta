<?php

/**
 * BuddyPress - Private Message Content.
 *
 * This template is used in /messages/single.php during the message loop to
 * display each message and when a new message is created via AJAX.
 *
 * Changes to the default template:
 * - Added #message-{$id} to the .message-box container
 * - Avatar args in array format
 * - Changed message sender strong wrapper into span.message-sender
 * - Replaced `bp_the_thread_message_time_since()` with `zeta_bp_the_thread_message_date_stamp()`
 * - Removed div.clear
 * - Renamed .activity to .last-activity
 *
 * @package Zeta
 * @subpackage BuddyPress
 */

?>

			<div id="message-<?php bp_the_thread_message_id(); ?>" class="message-box <?php bp_the_thread_message_css_class(); ?>">

				<div class="message-metadata">

					<?php

					/**
					 * Fires before the single message header is displayed.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_before_message_meta' ); ?>

					<?php bp_the_thread_message_sender_avatar( array( 'width' => 30, 'height' => 30 ) ); ?>

					<?php if ( bp_get_the_thread_message_sender_link() ) : ?>

						<span class="message-sender"><a href="<?php bp_the_thread_message_sender_link(); ?>" title="<?php bp_the_thread_message_sender_name(); ?>"><?php bp_the_thread_message_sender_name(); ?></a></span>

					<?php else : ?>

						<span class="message-sender"><?php bp_the_thread_message_sender_name(); ?></span>

					<?php endif; ?>

					<span class="last-activity"><?php zeta_bp_the_thread_message_date_stamp(); ?></span>

					<?php if ( bp_is_active( 'messages', 'star' ) ) : ?>
						<div class="message-star-actions">
							<?php bp_the_message_star_action_link(); ?>
						</div>
					<?php endif; ?>

					<?php

					/**
					 * Fires after the single message header is displayed.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_after_message_meta' ); ?>

				</div><!-- .message-metadata -->

				<?php

				/**
				 * Fires before the message content for a private message.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_before_message_content' ); ?>

				<div class="message-content">

					<?php bp_the_thread_message_content(); ?>

				</div><!-- .message-content -->

				<?php

				/**
				 * Fires after the message content for a private message.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_after_message_content' ); ?>

			</div><!-- .message-box -->
