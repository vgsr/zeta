<?php

/**
 * Responsive Lightbox template tags and filters for this theme
 *
 * @package Zeta
 * @subpackage Responsive Lightbox
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Bail when plugin is not active
if ( ! class_exists( 'Responsive_Lightbox' ) )
	return;

/**
 * Modify the gallery's shortcode content
 *
 * Responsive Lightbox applies lightbox logic for gallery items at the
 * `wp_get_attachment_link` filter, but this filter is not used by the Tiled Gallery
 * logic. Here the RL logic is applied after the fact of the construction of tiled
 * galleries.
 *
 * @see Responsive_Lightbox_Frontend::wp_get_attachment_link()
 *
 * @since 1.0.0
 *
 * @param string $html Gallery HTML
 * @param array $args Shortcode arguments
 * @return string Gallery HTML
 */
function zeta_rl_post_gallery( $html, $args, $instance ) {

	// For single pages, when applied to galleries
	if ( is_singular() && Responsive_Lightbox()->options['settings']['galleries'] ) {

		// get current script
		$script = Responsive_Lightbox()->options['settings']['script'];

		// prepare arguments
		$args = array(
			'selector'	=> Responsive_Lightbox()->options['settings']['selector'],
			'script'	=> $script,
			'settings'	=> array(
				'script'	=> Responsive_Lightbox()->options['configuration'][$script],
				'plugin'	=> Responsive_Lightbox()->options['settings']
			),
			'supports'	=> Responsive_Lightbox()->settings->scripts[$script]['supports'],
			'image_id'	=> null,
			'title'		=> '',
			'caption'	=> '',
			'src'		=> array()
		);

		// Apply RL's attachment link filter to all items in the gallery
		$html = Responsive_Lightbox()->frontend->lightbox_gallery_link( $html, $args );
	}

	return $html;
}
add_filter( 'post_gallery', 'zeta_rl_post_gallery', 1002, 3 ); // After Tiled Gallery's filter at priority 1001
