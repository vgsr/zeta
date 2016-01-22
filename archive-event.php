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

		<?php zeta_pre_content(); ?>

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php if ( eo_is_event_archive( 'year' ) || is_tax() ) : ?>

				<?php eo_get_template_part( 'loop', 'event-month' ); ?>

				<?php elseif ( eo_is_event_archive( 'month' ) ) : ?>

				<?php eo_get_template_part( 'loop', 'event-day' ); ?>

				<?php else : ?>

				<?php get_template_part( 'content', 'event' ); ?>

				<?php endif; ?>

			<?php endwhile; ?>

			<?php zeta_event_organiser_the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		<?php zeta_after_content(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
