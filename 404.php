<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Zeta
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'zeta' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'zeta' ); ?></p>

					<?php
						/**
						 * Further content of this page is removed so the admin can
						 * use custom links and relevant 404 texts in the page's sidebar
						 * through text or other widgets, instead of the theme forcing
						 * which widgets are relevant to this page.
						 */
					?>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
