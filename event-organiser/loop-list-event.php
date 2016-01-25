<?php

/**
 * Template for displaying a single event as a list item.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

?>

	<li class="<?php echo implode( ' ', eo_get_event_classes() ); ?>">
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php
				/* translators: 1. Event title 2. Event time */
				printf( eo_is_all_day() ? '%1$s' : __( '%2$s &mdash; %1$s', 'zeta' ),
					get_the_title(),
					eo_get_the_start( get_option( 'time_format' ) )
			); ?>
		</a>
	</li>
