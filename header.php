<?php

/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Zeta
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'zeta' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<h1 class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			</h1>
			<p class="site-description">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'description' ); ?></a>
			</p>
		</div><!-- .site-branding -->

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<?php if ( has_nav_menu( 'social' ) ) : ?>
				<?php
					// Social links navigation menu.
					wp_nav_menu( array(
						'theme_location'  => 'social',
						'depth'           => 1,
						'container_class' => 'menu-social',
						'link_before'     => '<span class="screen-reader-text">',
						'link_after'      => '</span>',
					) );
				?>
			<?php endif; ?>

			<button class="menu-toggle" aria-controls="menu" aria-expanded="false"><?php _e( 'Menu', 'zeta' ); ?></button>
			<?php
				// Primary navigation menu.
				wp_nav_menu( array(
					'theme_location'  => 'primary',
					'container_class' => 'menu-primary',
				) );
			?>
			<?php zeta_tools_nav(); ?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<div id="site-tools">
		<?php zeta_tools_content(); ?>
	</div>

	<div id="header-aside">
		<?php zeta_background_slider(); ?>
	</div><!-- #header-aside -->

	<div id="content" class="site-content">
