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

/** Navigation *************************************************************/

/**
 * Override the template stack for VGSR entities
 *
 * @since 1.0.0
 *
 * @uses vgsr_entity()
 *
 * @param string $template Template file to load
 * @return string Template file
 */
function zeta_vgsr_entity_template_include( $template ) {

	// Get VGSR Entity
	$entity = vgsr_entity();

	$post_type = get_post_type();

	// Use `single.php` instead of `page.php` for single entity pages
	if ( in_array( $post_type, $entity->get_entities() ) && is_singular( $post_type ) ) {
		$template = get_query_template( $post_type, array( 'single.php' ) );
	}

	return $template;
}
add_filter( 'template_include', 'zeta_vgsr_entity_template_include' );

/**
 * Filter the adjacent post navigation label
 *
 * @since 1.0.0
 *
 * @uses vgsr_entity()
 *
 * @param string $label Label
 * @return string Label
 */
function zeta_vgsr_entity_adjacent_post_navigation_label( $label ) {

	// Get VGSR Entity
	$entity = vgsr_entity();

	// Previous or next?
	$previous = ( 'zeta_previous_post_navigation_label' === current_filter() );

	// Bestuur: display bestuur season
	if ( is_bestuur() && is_singular() ) {
		$label = $entity->bestuur->get_season( get_adjacent_post( false, '', $previous ) );
	}

	return $label;
}
add_filter( 'zeta_previous_post_navigation_label', 'zeta_vgsr_entity_adjacent_post_navigation_label' );
add_filter( 'zeta_next_post_navigation_label',     'zeta_vgsr_entity_adjacent_post_navigation_label' );

/**
 * Print entry meta for VGSR entities
 *
 * @since 1.0.0
 *
 * @uses is_entity()
 * @uses vgsr_entity_get_meta()
 */
function zeta_vgsr_entity_entry_meta() {

	// When this is an entity post
	if ( is_entity() ) {
		$type = get_post_type();

		// Print all entity meta
		foreach ( vgsr_entity_get_meta() as $key => $args ) {
			printf( '<span class="%s">%s</span>', "{$type}-{$key}", $args['full'] );
		}
	}
}
add_action( 'zeta_entry_meta', 'zeta_vgsr_entity_entry_meta' );
