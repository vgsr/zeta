<?php

/**
 * The template used for displaying page content in single-event.php
 *
 * @package Zeta
 * @subpackage Event Organiser
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<div class="entry-meta">
			<?php 
				// Use Event Organiser's template stack
				eo_get_template_part( 'event-meta', 'event-single' );
			?>
		</div><!-- .entry-meta -->

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
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
