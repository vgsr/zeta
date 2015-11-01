<?php

/**
 * Template for displaying a section of events per day.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

?>

	<section class="event-section section-day">
		<header class="section-header">
			<h3 class="section-title">
				<a href="<?php echo esc_url( zeta_event_organiser_get_archive_url() ); ?>">
					<?php eo_the_start( _x( 'D jS', 'Daily event section header', 'zeta' ) ); ?>
				</a>
			</h3>
		</header>

		<main class="section-content">

			<?php eo_get_template_part( 'loop', 'single-event' ); ?>

			<?php while ( zeta_have_posts() && zeta_event_organiser_is_date_same( 'day' ) ) : the_post(); ?>

			<?php eo_get_template_part( 'loop', 'single-event' ); ?>

			<?php endwhile; ?>

		</main>

	</section>
