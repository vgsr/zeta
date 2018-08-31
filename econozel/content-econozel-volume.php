<?php

/**
 * The template used for displaying Econozel Volume content in volume archives.
 * 
 * @package Zeta
 * @subpackage Econozel
 */

?>

<article id="term-<?php econozel_the_volume_id(); ?>" <?php econozel_term_class(); ?>>
	<header class="entry-header">
		<h2 class="entry-title"><?php econozel_the_volume_link(); ?></h2>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php econozel_the_volume_description(); ?>

		<?php econozel_the_volume_content(); ?>
	</div><!-- .entry-content -->
</article><!-- #term-## -->
