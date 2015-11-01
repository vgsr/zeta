<?php

/**
 * Template for displaying a section of events per month.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

?>

	<section class="event-section section-month">
		<header class="section-header">
			<h2 class="section-title">
				<a href="<?php echo esc_url( zeta_event_organiser_get_archive_url( 'month' ) ); ?>">
					<?php eo_the_start( _x( 'F', 'Monthly event section header', 'zeta' ) ); ?>
				</a>
			</h2>
		</header>

		<?php eo_get_template_part( 'loop', 'event-day' ); ?>

		<?php while ( zeta_have_posts() && zeta_event_organiser_is_date_same( 'month' ) ) : the_post(); ?>

			<?php eo_get_template_part( 'loop', 'event-day' ); ?>

		<?php endwhile; ?>

	</section>
