<?php

/**
 * Template for displaying an event within the loop.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title">
			<?php printf( '<a href="%s" rel="bookmark">%s</a>',
				esc_url( get_permalink() ),
				/* translators: 1. Event title 2. Event time */
			    sprintf( eo_is_all_day() ? '%1$s' : __( '%2$s &mdash; %1$s', 'zeta' ),
					get_the_title(),
					eo_get_the_start( _x( 'g:i A', 'Event time title prefix', 'zeta' ) )
				)
			); ?>
		</h1>
	</header><!-- .entry-header -->

	<?php if ( zeta_has_content() ) : ?>

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'zeta' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<?php endif; ?>

	<footer class="entry-footer"><?php 
		zeta_entry_footer(); 
	?></footer><!-- .entry-footer -->
</article><!-- #post-## -->
