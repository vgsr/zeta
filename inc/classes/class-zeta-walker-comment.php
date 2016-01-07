<?php

/**
 * Zeta Walker Comment class
 * 
 * @package Zeta
 * @subpackage Comments
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Zeta_Walker_Comment' ) ) :
/**
 * Zeta Walker Comment class
 *
 * @since 0.1.0
 *
 * @see Walker_Comment
 */
class Zeta_Walker_Comment extends Walker_Comment {

	/**
	 * Output a comment in the HTML5 format.
	 *
	 * @access protected
	 *
	 * @see wp_list_comments()
	 *
	 * @param object $comment Comment to display.
	 * @param int    $depth   Depth of comment.
	 * @param array  $args    An array of arguments.
	 */
	protected function html5_comment( $comment, $depth, $args ) {

		// Define enclosing HTML element
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

		// Has comment author a uri?
		$with_link = ( $url = get_comment_author_url() ) && ! ( empty( $url ) || 'http://' == $url );

		?>

		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '' ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body <?php if ( ! $with_link ) { echo 'without-link'; } ?>">
				<div class="comment-avatar">
					<?php if ( 0 != $args['avatar_size'] ) {
						printf( $with_link ? '<a href="%2$s">%1$s</a>' : '%1$s', get_avatar( $comment, $args['avatar_size'] ), $url );
					} ?>
				</div><!-- .comment-avatar -->

				<div class="comment-content">
					<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
					<?php endif; ?>

					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<div class="comment-actions">
					<?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>

					<?php comment_reply_link( array_merge( $args, array(
						'add_below' => 'div-comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<span class="reply-link">',
						'after'     => '</span>'
					) ) ); ?>

					<?php printf( '<span class="since-link"><a href="%s" class="post-comment-since"><time datetime="%s" title="%s">%s</time></a></span>',
						esc_url( get_comment_link( $comment->comment_ID, $args ) ),
						get_comment_time( 'c' ),
						sprintf( _x( '%1$s at %2$s', '1: date, 2: time' ), get_comment_date(), get_comment_time() ),
						// Use BP's time differ when present, else fallback to native WP's differ
						function_exists( 'bp_core_time_since' ) ? bp_core_time_since( get_comment_time( 'c' ) ) : human_time_diff( get_comment_time( 'c' ) )
					); ?>
				</div>
			</article><!-- .comment-body -->
		<?php
	}
}

endif; // class_exists
