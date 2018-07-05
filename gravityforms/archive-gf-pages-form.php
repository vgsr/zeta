<?php

/**
 * The template for displaying Gravityforms Forms archive pages.
 *
 * @package Zeta
 * @subpackage Gravity Forms Pages
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php zeta_before_content(); ?>

		<?php if ( gf_pages_has_forms() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( gf_pages_has_forms() ) : gf_pages_the_form(); ?>

				<?php gf_pages_get_template_part( 'content-gf-pages-form' ); ?>

			<?php endwhile; ?>

			<?php zeta_the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		<?php zeta_after_content(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
