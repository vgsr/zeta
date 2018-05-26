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
 * Print entry meta for Econozel posts
 *
 * @since 1.0.0
 */
function zeta_econozel_entry_meta() {

	// Article or Edition
	if ( econozel_is_article() ) {

		// Author(s)
		printf( '<span class="author">%s</span>', implode( ' ', econozel_get_article_author_link() ) );

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
