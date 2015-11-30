<?php

/**
 * Template for displaying a section of events per day as a list.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

global $eo_event_loop;

?>

	<li class="event-section section-day">
		<header class="section-header">
			<h4 class="section-title">
				<a href="<?php echo esc_url( zeta_event_organiser_get_archive_url( 'day' ) ); ?>">
					<?php eo_the_start( _x( 'D jS', 'Daily event section header', 'zeta' ) ); ?>
				</a>
			</h4>
		</header>

		<ul class="children">

		<?php eo_get_template_part( 'loop', 'list-event' ); ?>

		<?php while ( zeta_has_posts( $eo_event_loop ) && zeta_event_organiser_is_date_same_day( $eo_event_loop ) ) : $eo_event_loop->the_post(); ?>

			<?php eo_get_template_part( 'loop', 'list-event' ); ?>

		<?php endwhile; ?>

		</ul>
	</li>
