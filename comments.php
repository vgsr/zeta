<?php

/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package Zeta
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

?>

<div id="comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'zeta' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'zeta' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( __( 'Older Comments', 'zeta' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'zeta' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'walker'      => new Zeta_Walker_Comment,
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 50
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php _e( 'Comment navigation', 'zeta' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( __( 'Older Comments', 'zeta' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments', 'zeta' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'zeta' ); ?></p>
	<?php endif; ?>

	<?php

		// Define comment form arguments
		$args = array(
			'submit_button' => is_user_logged_in()
				? '<button name="%1$s" type="submit" id="%2$s" class="%3$s"><span class="screen-reader-text">%4$s</span></button>'
				// Use `get_cancel_comment_reply_link()` before it is used in `comment_form()`
				: '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" /> &nbsp; ' . get_cancel_comment_reply_link( __( 'Cancel', 'zeta' ) ),
		);

		// Prevent rendering of default cancel comment reply link in the comment form
		add_filter( 'cancel_comment_reply_link', '__return_empty_string' );

		comment_form( $args );

		// Remove link filter
		remove_filter( 'cancel_comment_reply_link', '__return_empty_string' );
	?>
</div><!-- #comments -->
