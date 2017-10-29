<?php

/**
 * The template for displaying the front page.
 *
 * @package Zeta
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php zeta_before_content(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'page' ); ?>

		<?php endwhile; // end of the loop. ?>

		<?php zeta_after_content(); ?>

		</main><!-- #main -->

		<?php if ( zeta_is_static_front_page() ) : ?>

			<div id="page-scroll-down">
				<button><span class="screen-reader-text"><?php _e( 'Scroll down', 'zeta' ); ?></span></button>
			</div>

		<?php endif; ?>
	</div><!-- #primary -->

	<?php if ( is_active_sidebar( 'front-page-1' ) ) : ?>

	<div id="secondary" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'front-page-1' ); ?>
	</div><!-- #secondary -->

	<?php endif; ?>

<?php get_footer(); ?>
