<?php

/**
 * Econozel template tags and filters for this theme
 *
 * @package Zeta
 * @subpackage Econozel
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Modify the background slider slides for Econozel pages
 *
 * @since 1.0.0
 *
 * @param array $slides Slides
 * @param array $args Slides arguments
 * @return array Slider images and posts
 */
function zeta_econozel_background_slider_slides( $slides, $args ) {

	// Root page
	if ( econozel_is_root() ) {
		$article_slides = array();

		// Get latest published Articles
		if ( econozel_query_articles( array(
			'econozel_edition' => false,
			'econozel_archive' => false,
			'posts_per_page'   => 10
		) ) ) {
			while ( econozel_has_articles() ) : econozel_the_article();
				if ( has_post_thumbnail() ) {
					$article_slides[] = zeta_setup_background_slider_slide( array(
						'attachment_id' => get_post_thumbnail_id(),
						'title'         => get_the_title(),
						'url'           => get_permalink(),
						'byline'        => econozel_get_article_date() . ' / ' . econozel_get_article_author( 0, true )
					) );
				}
			endwhile;
		}

		// Display articles
		if ( $article_slides ) {
			$slides = $article_slides;
		}

	// Volume archives
	} elseif ( econozel_is_volume_archive() ) {
		$edition_slides = array();

		while ( econozel_has_volumes() ) : econozel_the_volume();
			foreach ( econozel_get_volume_editions() as $edition_id ) {
				if ( econozel_has_edition_cover_photo( $edition_id ) ) {
					$edition_slides[] = zeta_setup_background_slider_slide( array(
						'attachment_id' => econozel_get_edition_cover_photo( $edition_id ),
						'title'         => econozel_get_edition_title( $edition_id ),
						'url'           => econozel_get_edition_url( $edition_id ),
						'byline'        => econozel_edition_article_count( $edition_id, false )
					) );
				}
			}
		endwhile;

		// Display edition cover photos
		if ( $edition_slides ) {
			$slides = $edition_slides;
		}

	// Single Volume or Edition archives
	} elseif ( econozel_is_volume() || econozel_is_edition_archive() ) {
		$edition_slides = array();

		while ( econozel_has_editions() ) : econozel_the_edition();
			if ( econozel_has_edition_cover_photo() ) {
				$edition_slides[] = zeta_setup_background_slider_slide( array(
					'attachment_id' => econozel_get_edition_cover_photo(),
					'title'         => econozel_get_edition_title(),
					'url'           => econozel_get_edition_url(),
					'byline'        => econozel_edition_article_count( 0, false )
				) );
			}
		endwhile;

		// Display edition cover photos
		if ( $edition_slides ) {
			$slides = $edition_slides;
		}

	// Single Edition
	} elseif ( econozel_is_edition() && econozel_has_edition_cover_photo() ) {

		// Only display cover photo
		$slides = array(
			zeta_setup_background_slider_slide( array(
				'attachment_id' => econozel_get_edition_cover_photo()
			) )
		);

	// Any other Econozel pages, but not single Articles
	} elseif ( is_econozel() && ! econozel_is_article( true ) ) {

		// Walk slides
		foreach ( $slides as &$slide ) {

			// Add slide byline for article
			if ( $slide['post_id'] && ! $slide['byline'] ) {
				$slide['byline'] = econozel_get_article_date( $slide['post_id'] ) . ' / ' . econozel_get_article_author( $slide['post_id'], true );
			}
		}
	}

	return $slides;
}
add_filter( 'zeta_get_background_slider_slides', 'zeta_econozel_background_slider_slides', 10, 2 );

/**
 * Print entry meta for Econozel posts
 *
 * @since 1.0.0
 */
function zeta_econozel_entry_meta() {

	// Article or Edition
	if ( econozel_is_article() ) {

		// Author(s)
		printf( '<span class="author">%s</span>', sprintf( esc_html__( 'Written by %s', 'zeta' ), econozel_get_article_author_link( 0, true ) ) );

		// Edition
		if ( ! econozel_is_edition() ) {
			if ( econozel_has_article_edition() ) {
				printf( '<span class="article-edition">%s</span>', econozel_get_edition_link() );

			// Posted date
			} else {
				zeta_posted_on( false );
			}
		}
	}
}
add_action( 'zeta_entry_meta', 'zeta_econozel_entry_meta' );
