<?php
/**
 * Jetpack Compatibility File
 * 
 * @link http://jetpack.me/
 *
 * @package Zeta
 * @subpackage Jetpack
 */

/**
 * Add theme support for Infinite Scroll.
 * 
 * @link http://jetpack.me/support/infinite-scroll/
 *
 * @since 1.0.0
 */
function zeta_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'zeta_jetpack_setup' );

/**
 * Add a cloned version of Jetpack's tiled-gallery module.
 *
 * This overwrites the [gallery] shortcode with a much nicer and
 * more interesting implementation to fit into the theme.
 *
 * @since 1.0.0
 */
function zeta_tiled_gallery() {

	// Prevent tiled-gallery collision with Jetpack's own module
	if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) )
		return;

	/**
	 * Define fallback Jetpack classes and methods that are used
	 * in the tiled-gallery module.
	 */
	if ( ! class_exists( 'Jetpack' ) ) {
		class Jetpack {

			/**
			 * Return the theme's content width.
			 *
			 * @since 1.0.0
			 *
			 * @return int Theme's content width in pixels
			 */
			public static function get_content_width() {
				global $content_width;
				return $content_width;
			}

			/**
			 * Return a modified array for active Jetpack modules.
			 *
			 * @since 1.0.0
			 * 
			 * @return array Active module names
			 */
			public static function get_active_modules() {
				return array( 'tiled-gallery' );
			}
		}

		class Jetpack_Options {
			/**
			 * Return value for the requested option.
			 *
			 * @since 1.0.0
			 * 
			 * @return bool False
			 */
			public static function get_option() {
				return false;
			}
		}
	}

	if ( ! function_exists( 'jetpack_photon_url' ) ) {
		/**
		 * Return empty when requesting the Jetpack Photon url
		 *
		 * @since 1.0.0
		 * @since 1.1.0 Returns the first parameter untouched
		 *
		 * @param string $src Original file source
		 * @param array $args File request arguments
		 * @return string Original file source
		 */
		function jetpack_photon_url( $src, $args ) {

			// Try to find a matching size'd image
			if ( isset( $args['w'], $args['h'] ) ) {
				$sizes = zeta_get_larger_image_sizes( array( $args['w'], $args['h'] ) );

				// Remove cropped sizes
				if ( ! isset( $args['crop'] ) || ! $args['crop'] ) {
					foreach ( $sizes as $k => $size ) {
						if ( $size['crop'] ) {
							unset( $sizes[ $k ] );
						}
					}
				}

				// Sizes left, find image match by attachment id from url
				if ( $sizes ) {
					if ( $attid = zeta_get_attachment_id_from_url( $src ) ) {
						$src = wp_get_attachment_image_src( $attid, key( $sizes ) );
						$src = $src[0];
					}
				}
			}

			return $src;
		}
	}

	// Load the tiled-gallery module
	require( get_template_directory() . '/inc/tiled-gallery/tiled-gallery.php' );

	// A little help for my friend
	add_action( 'wp_enqueue_scripts', 'zeta_tiled_gallery_register_scripts', 5 );
}
add_action( 'after_setup_theme', 'zeta_tiled_gallery' );

/**
 * Register tiled-gallery scripts
 *
 * @see Jetpack_Tiled_Gallery::default_scripts_and_styles()
 *
 * @since 1.0.0
 */
function zeta_tiled_gallery_register_scripts() {
	$tiled_url = trailingslashit( get_template_directory_uri() . '/inc/tiled-gallery' );
	wp_register_script( 'tiled-gallery', $tiled_url . 'tiled-gallery/tiled-gallery.js', array( 'jquery' ) );
	if ( is_rtl() ) {
		wp_register_style( 'tiled-gallery', $tiled_url . 'tiled-gallery/rtl/tiled-gallery-rtl.css', array(), '2012-09-21' );
	} else {
		wp_register_style( 'tiled-gallery', $tiled_url . 'tiled-gallery/tiled-gallery.css', array(), '2012-09-21' );
	}
}
