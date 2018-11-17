<?php

/**
 * The template for displaying Gravityforms Form content in volume archives.
 * 
 * @package Zeta
 * @subpackage Gravity Forms Pages
 */

?>

<article id="form-<?php gf_pages_the_form_id(); ?>" <?php gf_pages_form_class(); ?>>
	<header class="entry-header">
		<?php gf_pages_the_form_link( array(
			'link_before' => '<h2 class="entry-title">',
			'link_after'  => '</h2>'
		) ); ?>

		<div class="entry-meta"><?php
			zeta_entry_meta();
		?></div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php gf_pages_the_form_description(); ?>

		<?php gf_pages_the_form_link( array(
			'link_before' => '<p>',
			'link_after' => '</p>',
			'link_text' => esc_html__( 'Complete the form &rarr;', 'zeta' )
		) ); ?>
	</div><!-- .entry-content -->
</article><!-- #form-## -->
