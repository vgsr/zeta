<?php

/**
 * The template used for displaying post content in index.php
 * 
 * @package Zeta
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<div class="entry-meta"><?php
			zeta_entry_meta();
		?></div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			/* translators: 1. Name of current post 2. Arrow */
			the_content( sprintf(
				__( 'Continue reading %1$s $2%s', 'zeta' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false ),
				'<span class="meta-nav">' . _x( '&rarr;', 'Continue reading arrow', 'zeta' ) . '</span>',
			) );
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'zeta' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer"><?php 
		zeta_entry_footer(); 
	?></footer><!-- .entry-footer -->
</article><!-- #post-## -->