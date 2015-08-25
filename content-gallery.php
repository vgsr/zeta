<?php
/**
 * The template part for displaying a post's content with the `gallery` Post Format.
 *
 * @package Zeta
 */

$attachment_ids = zeta_get_post_galleries_attachment_ids(); 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<div class="entry-meta">
			<?php 
				zeta_post_format_link(); 
				zeta_posted_on(); 

				// Display the post galleries' image count
				if ( $attachment_ids ) :
			?><span class="image-count"><?php printf( _nx( '%d Image', '%d Images', count( $attachment_ids ), 'Gallery post-format image count', 'zeta' ), count( $attachment_ids ) ); ?></span><?php
				endif; ?>
		</div><!-- .entry-meta -->

		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php 
			// Show a gallery preview
			if ( $attachment_ids ) {
				shuffle( $attachment_ids );
				$content = sprintf( ' [gallery ids="%s"]', implode( ',', array_slice( $attachment_ids, 0, 3 ) ) );

				echo apply_filters( 'the_content', $content );
			}
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer"><?php 
		zeta_entry_footer(); 
	?></footer><!-- .entry-footer -->
</article><!-- #post-## -->
