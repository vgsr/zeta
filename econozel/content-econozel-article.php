<?php

/**
 * The template used for displaying Econozel Article content in single editions.
 * 
 * @package Zeta
 * @subpackage Econozel
 */

?>

<article id="post-<?php econozel_the_article_id(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="entry-meta"><?php
			zeta_entry_meta();
		?></div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php econozel_the_article_description(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer"><?php 
		zeta_entry_footer(); 
	?></footer><!-- .entry-footer -->
</article><!-- #post-## -->
