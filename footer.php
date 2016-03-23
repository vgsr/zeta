<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Zeta
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
		<div id="tertiary" class="footer-area" role="complementary">
			<?php dynamic_sidebar( 'footer-1' ); ?>
		</div><!-- #tertiary -->
		<?php endif; ?>

		<div class="site-info">
			<span><?php printf( __( 'Built with %s', 'zeta' ), '<a href="http://wordpress.org/">WordPress</a>' ); ?></span>
			<span><?php printf( __( '<a href="%2$s">Design</a> by %1$s', 'zeta' ), '<a href="https://github.com/vgsr" rel="designer">MMC der VGSR</a>', wp_get_theme()->get( 'ThemeURI' ) ); ?></span>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
