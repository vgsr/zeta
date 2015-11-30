<?php

/**
 * Template for displaying the [eo_events] shortcode.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

global $eo_event_loop;

?>

<?php if ( $eo_event_loop->have_posts() ) : ?>

	<div class="event-list">
		<ul <?php zeta_event_organiser_loop_id(); zeta_event_organiser_loop_class(); ?>>
			<?php while ( $eo_event_loop->have_posts() ) : $eo_event_loop->the_post(); ?>

			<?php eo_get_template_part( 'loop', 'list-month' ); ?>

			<?php endwhile; ?>
		</ul>
	</div>

<?php else : ?>

	<p class="eo-no-events">
		<?php if ( zeta_event_organiser_loop_arg( 'no_events' ) ) {
			echo esc_html( zeta_event_organiser_loop_arg( 'no_events' ) );
		} else {
			esc_html_e( 'There are no events found.', 'zeta' );
		} ?>
	</p>

<?php endif; ?>
