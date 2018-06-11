<?php

/**
 * Template for displaying archive pages for the Event Organiser plugin.
 *
 * Similar to archive.php, except for the content part.
 * 
 * @package Zeta
 * @subpackage Event Organiser
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php zeta_before_content(); ?>

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php if ( eo_is_event_archive( 'year' ) || is_tax() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php eo_get_template_part( 'loop', 'event-month' ); ?>

				<?php endwhile; ?>

			<?php elseif ( eo_is_event_archive( 'month' ) ) : ?>

				<div class="entry-content">
					<ul class="month-event-days">

						<?php while ( have_posts() ) : the_post(); ?>

							<li><?php eo_get_template_part( 'loop', 'event-day' ); ?></li>

						<?php endwhile; ?>

					</ul>
				</div>

			<?php else : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'event' ); ?>

				<?php endwhile; ?>

			<?php endif; ?>

			<?php zeta_the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		<?php zeta_after_content(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
