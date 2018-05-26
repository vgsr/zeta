<?php

/**
 * The template for displaying a single Econozel Volume page.
 *
 * @package Zeta
 * @subpackage Econozel
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php zeta_before_content(); ?>

		<?php if ( econozel_has_editions() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<p class="archive-description">', '</p>' );
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( econozel_has_editions() ) : econozel_the_edition(); ?>

				<?php econozel_get_template_part( 'content-econozel-edition' ); ?>

			<?php endwhile; ?>

			<?php the_post_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		<?php zeta_after_content(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
