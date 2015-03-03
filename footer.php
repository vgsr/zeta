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
		<div class="site-info">
			<?php printf( __( 'Built with %s', 'zeta' ), '<a href="http://wordpress.org/">WordPress</a>' ); ?>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'zeta' ), 'Zeta', '<a href="https://github.com/vgsr/" rel="designer">MMC</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
