<?php

/**
 * VGSR Entity template tags and filters for this theme.
 * 
 * @package Zeta
 * @subpackage VGSR Entity
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Bail when plugin is not active
if ( ! function_exists( 'vgsr_entity' ) )
	return;

/**
 * Modify the background slider slide for VGSR Entity items
 *
 * @since 1.0.0
 *
 * @param array $slide Slide details
 * @param array $args Original slide arguments
 * @param array $slider_args Slides arguments
 * @return array Slide details
 */
function zeta_vgsr_entity_background_slider_slide( $slide, $args, $slider_args ) {

	// Get the slide's post
	$post = get_post( $args['post_id'] );

	// Entity
	if ( $post && vgsr_is_entity( $post ) ) {

		// Entity type
		$slide['byline'] = get_post_type_object( $post->post_type )->labels->singular_name;

		// Collect and display meta values
		$slide['byline'] .= ' &#8226; ' . implode( ' &#8226; ', array_map( function( $args ) {
			return vsprintf( $args['label'], (array) $args['value'] );
		}, vgsr_entity_get_meta( $post ) ) );
	}

	return $slide;
}
add_filter( 'zeta_setup_background_slider_slide', 'zeta_vgsr_entity_background_slider_slide', 10, 3 );

/** Template ***************************************************************/

/**
 * Override the template stack for VGSR entities
 *
 * @since 1.0.0
 *
 * @param string $template Template file to load
 * @return string Template file
 */
function zeta_vgsr_entity_template_include( $template ) {

	// Entity: use `single.php` instead of the default `page.php`
	if ( vgsr_is_entity() && is_singular() ) {
		$template = get_query_template( get_post_type(), array( 'single.php' ) );
	}

	return $template;
}
add_filter( 'template_include', 'zeta_vgsr_entity_template_include' );

/** Entry ******************************************************************/

/**
 * Print entry meta for VGSR entities
 *
 * @since 1.0.0
 */
function zeta_vgsr_entity_entry_meta() {

	// When this is an entity post
	if ( vgsr_is_entity() ) {
		$type = vgsr_entity_get_type();

		// Print all entity meta
		foreach ( vgsr_entity_get_meta() as $key => $args ) {
			printf( '<span class="%s">%s</span>',
				"{$type}-{$key}",
				vsprintf( $args['label'], (array) $args['value'] )
			);
		}
	}
}
add_action( 'zeta_entry_meta', 'zeta_vgsr_entity_entry_meta' );

/** Navigation *************************************************************/

/**
 * Modify the adjacent post navigation label
 *
 * @since 1.0.0
 *
 * @param string $label Label
 * @return string Label
 */
function zeta_vgsr_entity_adjacent_post_navigation_label( $label ) {

	// Bestuur: display bestuur season
	if ( vgsr_is_bestuur() && is_singular() ) {

		// Previous or next?
		$previous = ( 'zeta_previous_post_navigation_label' === current_filter() );

		// Make the bestuur's season the navigation label
		if ( $type = vgsr_entity_get_type( 'bestuur', true ) ) {
			$label = $type->get( 'season', get_adjacent_post( false, '', $previous ) );
		}
	}

	return $label;
}
add_filter( 'zeta_previous_post_navigation_label', 'zeta_vgsr_entity_adjacent_post_navigation_label' );
add_filter( 'zeta_next_post_navigation_label',     'zeta_vgsr_entity_adjacent_post_navigation_label' );
