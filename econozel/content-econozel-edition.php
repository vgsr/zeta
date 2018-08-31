<?php

/**
 * The template for displaying Econozel Edition content in edition archives or single volumes.
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

		<?php if ( econozel_has_edition_document() ) : ?>
			<p><a href="<?php echo esc_url( econozel_get_edition_document_url() ); ?>" target="_blank" rel="nofollow"><?php esc_html_e( "Download the Edition's document file", 'econozel' ); ?></a></p>
		<?php endif; ?>

	</div><!-- .entry-content -->
</article><!-- #term-## -->
