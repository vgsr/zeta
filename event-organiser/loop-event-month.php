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
			<span class="section-title">
				<a href="<?php echo esc_url( zeta_event_organiser_get_archive_url( 'month' ) ); ?>">
					<?php echo ucfirst( eo_get_the_start( is_tax()
						? esc_html_x( 'F Y', 'Monthly event section header with year context', 'zeta' )
						: esc_html_x( 'F', 'Monthly event section header', 'zeta' )
					) ); ?>
				</a>
			</span>
		</header>

		<?php eo_get_template_part( 'loop', 'event-day' ); ?>

		<?php while ( zeta_has_posts() && zeta_event_organiser_is_date_same_month() ) : the_post(); ?>

			<?php eo_get_template_part( 'loop', 'event-day' ); ?>

		<?php endwhile; ?>

	</section>
