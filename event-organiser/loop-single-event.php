<?php

/**
 * Template for displaying an event within the archive loop.
 *
 * @package Zeta
 * @subpackage Event Organiser
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="event-header">
		<p class="event-title">
			<?php printf( eo_is_all_day() ? '%1$s' : __( '%2$s &ndash; %1$s', 'zeta' ),
				/* translators: 1. Event title 2. Event time */
				sprintf( '<a href="%s" rel="bookmark">%s</a>',
					esc_url( get_permalink() ),
					get_the_title()
				),
				eo_get_the_start( get_option( 'time_format' ) )
			); ?>
		</p>
	</header><!-- .entry-header -->
</article><!-- #post-## -->
