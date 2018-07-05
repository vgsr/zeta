<?php

/**
 * The template used for displaying Econozel Edition content in edition archives or single volumes.
 * 
 * @package Zeta
 * @subpackage Econozel
 */

?>

<article id="term-<?php econozel_the_edition_id(); ?>" <?php econozel_term_class(); ?>>
	<header class="entry-header">
		<h2 class="entry-title"><?php econozel_the_edition_link(); ?></h2>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php econozel_the_edition_description(); ?>

		<?php econozel_the_edition_toc(); ?>
	</div><!-- .entry-content -->
</article><!-- #term-## -->
