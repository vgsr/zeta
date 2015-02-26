<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Zeta
 */

if ( ! function_exists( 'the_posts_navigation' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function the_posts_navigation() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation posts-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'zeta' ); ?></h2>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( 'Older posts', 'zeta' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts', 'zeta' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'the_post_navigation' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function the_post_navigation() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Post navigation', 'zeta' ); ?></h2>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', '%title' );
				next_post_link( '<div class="nav-next">%link</div>', '%title' );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'zeta_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function zeta_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( 'Posted on %s', 'post date', 'zeta' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		_x( 'by %s', 'post author', 'zeta' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>';

}
endif;

if ( ! function_exists( 'zeta_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function zeta_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'zeta' ) );
		if ( $categories_list && zeta_categorized_blog() ) {
			printf( '<span class="cat-links">' . __( 'Posted in %1$s', 'zeta' ) . '</span>', $categories_list );
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'zeta' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . __( 'Tagged %1$s', 'zeta' ) . '</span>', $tags_list );
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', 'zeta' ), __( '1 Comment', 'zeta' ), __( '% Comments', 'zeta' ) );
		echo '</span>';
	}

	edit_post_link( __( 'Edit', 'zeta' ), '<span class="edit-link">', '</span>' );
}
endif;

if ( ! function_exists( 'the_archive_title' ) ) :
/**
 * Shim for `the_archive_title()`.
 *
 * Display the archive title based on the queried object.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the title. Default empty.
 * @param string $after  Optional. Content to append to the title. Default empty.
 */
function the_archive_title( $before = '', $after = '' ) {
	if ( is_category() ) {
		$title = sprintf( __( 'Category: %s', 'zeta' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( __( 'Tag: %s', 'zeta' ), single_tag_title( '', false ) );
	} elseif ( is_author() ) {
		$title = sprintf( __( 'Author: %s', 'zeta' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( __( 'Year: %s', 'zeta' ), get_the_date( _x( 'Y', 'yearly archives date format', 'zeta' ) ) );
	} elseif ( is_month() ) {
		$title = sprintf( __( 'Month: %s', 'zeta' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'zeta' ) ) );
	} elseif ( is_day() ) {
		$title = sprintf( __( 'Day: %s', 'zeta' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'zeta' ) ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title', 'zeta' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title', 'zeta' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( __( 'Archives: %s', 'zeta' ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( __( '%1$s: %2$s', 'zeta' ), $tax->labels->singular_name, single_term_title( '', false ) );
	} else {
		$title = __( 'Archives', 'zeta' );
	}

	/**
	 * Filter the archive title.
	 *
	 * @param string $title Archive title to be displayed.
	 */
	$title = apply_filters( 'get_the_archive_title', $title );

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;
	}
}
endif;

if ( ! function_exists( 'the_archive_description' ) ) :
/**
 * Shim for `the_archive_description()`.
 *
 * Display category, tag, or term description.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the description. Default empty.
 * @param string $after  Optional. Content to append to the description. Default empty.
 */
function the_archive_description( $before = '', $after = '' ) {
	$description = apply_filters( 'get_the_archive_description', term_description() );

	if ( ! empty( $description ) ) {
		/**
		 * Filter the archive description.
		 *
		 * @see term_description()
		 *
		 * @param string $description Archive description to be displayed.
		 */
		echo $before . $description . $after;
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function zeta_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'zeta_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'zeta_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so zeta_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so zeta_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in zeta_categorized_blog.
 */
function zeta_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'zeta_categories' );
}
add_action( 'edit_category', 'zeta_category_transient_flusher' );
add_action( 'save_post',     'zeta_category_transient_flusher' );

/**
 * Display the header slider
 *
 * @since 1.0.0
 *
 * @uses wp_enqueue_style()
 */
function zeta_header_slider() {

	/**
	 * Images typically are served as arrays containing the following elements
	 *  - src    The image source to display as background
	 *  - post_id Optional. The image's linked post ID.
	 *  - href    Optional. The image anchor url. Defaults to the post permalink
	 *  - title   Optional. The title of the image/post. Defaults to the post title or the image's title.
	 *  - byline  Optional. The contents for the subtitle of the image/post. Requires a value for the `title` param
	 */
	$images = array();

	// The current post object
	if ( is_singular() ) {
		$post_id = get_queried_object_id();

		// Treat galleries differently?
		if ( has_post_format( $post_id ) && 'gallery' == get_post_format( $post_id ) ) {

			// Get all attached images of the loop's posts
			$images = array_filter( wp_list_pluck( (array) get_attached_media( 'image', $post_id ), 'ID' ) );

		// Default post object
		} else {

			// Get all attached images of the loop's posts
			$images = array_filter( wp_list_pluck( (array) get_attached_media( 'image', $post_id ), 'ID' ) );
		}

	// Posts collection
	} elseif ( ( is_home() || is_archive() || is_search() ) && have_posts() ) {
		global $wp_query;

		// Get all queried post IDs
		$images = wp_list_pluck( $wp_query->posts, 'ID' );

	// Front page
	} elseif ( is_front_page() ) {

		// What to do here? Latest posts, featured posts, front page gallery?
		$query = new WP_Query( array( 'posts_per_page' => 5, 'fields' => 'ids' ) );

		// Get the five latest post IDs
		$images = $query->query();
	}

	// When no images were found, get the default slider images
	if ( empty( $images ) ) {
		foreach ( array( 'benches.jpg', 'bridge.jpg', 'desktop.jpg', 'downtown.jpg', 'tools.jpg' ) as $file ) {
			$images[] = array( 
				'src' => get_template_directory_uri() . '/images/headers/' . $file, 
			);
		}
	}

	// Define image count
	$img_count = count( $images );
	$slides    = 0; ?>

	<div class="slider flexslider loading">
		<ul class="slides">
			<?php foreach ( $images as $i => $data ) : 

				// Handle post IDs
				if ( is_numeric( $data ) ) {
					$post = get_post( (int) $data );
					if ( ! $post )
						continue;

					// Check the post type
					switch ( $post->post_type ) {

						// Media
						case 'attachment' :
							$data = array( 'src' => $post->ID );
							break;

						// Other
						default :
							$data = array( 'post_id' => $post->ID );
							break;
					}
				}

				// Fill data variables
				$data = wp_parse_args( (array) $data, array(
					'post_id' => false,
					'src'     => false,
					'href'    => false,
					'title'   => false,
					'byline'  => false
				) );

				// Handle post data. Not when already there.
				if ( ! empty( $data['post_id'] ) && get_queried_object_id() !== $data['post_id'] ) {
					$post_id = $data['post_id'];

					// Find an image for the post
					if ( empty( $data['src'] ) ) {
						$atts = array();

						// Get the post's featured image
						if ( has_post_thumbnail( $post_id ) ) {
							$atts = array( get_post_thumbnail_id( $post_id ) );

						// Get the post's attached images
						} elseif ( ( $atts = get_attached_media( 'image', $post_id ) ) && ! empty( $atts ) ) {
							$atts = wp_list_pluck( $atts, 'ID' );
						}

						// Find the first post's image that can be used
						foreach ( $atts as $att_id ) {
							$image = wp_get_attachment_image_src( (int) $att_id, 'full' );

							// Require image to be at least 1600px wide
							if ( 1600 <= (int) $image[1] ) {
								// Find or create an image size that is closest to 1600px wide?
								$data['src'] = $image[0];

							// Image is too small: skip slide
							} else {
								continue;
							}
						}

						// Still no image found: skip slide
						if ( empty( $data['src'] ) ) {
							continue;
						}
					}

					// Get post permalink
					$data['href'] = get_permalink( $post_id );

					// Get post title
					$data['title'] = get_the_title( $post_id );

					// Get post details
					$data['byline'] = sprintf( __( 'Posted by %s', 'zeta' ), get_post_field( 'post_author', $post_id ) );
				}

				// Image is missing: skip slide
				if ( empty( $data['src'] ) ) {
					continue;

				// Attachment ID provided
				} elseif ( is_numeric( $data['src'] ) ) {
					$att_id =  (int) $data['src'];
					$image  = wp_get_attachment_image_src( $att_id, 'full' );

					// Require image to be at least 1600px wide
					if ( 1600 <= (int) $image[1] ) {
						// Find or create an image size that is closest to 1600px wide?
						$data['src'] = $image[0];

					// Image is too small: skip slide
					} else {
						continue;
					}

					$metadata = wp_get_attachment_metadata( $att_id );

					// Get original image link
					if ( apply_filters( 'zeta_header_image_use_image_url', false, $att_id ) ) {
						$upload_dir = wp_upload_dir();
						$data['href'] = trailingslashit( $upload_dir['baseurl'] ) . $metadata['file'];
					}

					// Get attachment title
					if ( apply_filters( 'zeta_header_image_use_image_title', false, $att_id ) 
						&& ( $att_title = get_the_title( $att_id ) ) && ! empty( $att_title ) ) {
						$data['title'] = $att_title;
					}

					// Get attachment details
					if ( apply_filters( 'zeta_header_image_use_image_credits', false, $att_id ) 
						&& ! empty( $metadata['image_meta']['credit'] ) ) {
						$data['byline'] = sprintf( __( 'Created by %s', 'zeta' ), $metadata['image_meta']['credit'] );
					}
				}

			// Start slide
			?><li class="slide" style="z-index: <?php echo $img_count - $i; ?>;"><?php

				// Define image container tag. Use anchor when a link is provided
				$tag = ! empty( $data['href'] ) ? 'a' : 'div'; 

				// Start image container
				$el = '<' . $tag . ' class="slide-inner" style="background-image: url(' . esc_attr( $data['src'] ) . ');"';

				// Add link to the element
				if ( 'a' === $tag ) {
					$el .= ' href="' . esc_attr( $data['href'] ) . '"';
				}
				$el .= '>';

				// Handle titles
				if ( ! empty( $data['title'] ) ) {
					$el .= '<header class="slide-details"><h2>' . $data['title'] . '</h2>';

					// Append byline
					if ( ! empty( $data['byline'] ) ) {
						$el .= '<span class="byline">' . $data['byline'] . '</span>';
					}

					$el .= '</header>';
				}

				// Close image container
				$el .= '</' . $tag . '>';

				// Filter and display slide content
				echo apply_filters( 'zeta_header_slider_slide', $el, $data, $tag, $i );

			// End slide
			?></li>
			<?php $slides++; endforeach; ?>
		</ul>

		<?php if ( $slides > 1 ) : ?>

		<script>
			jQuery(document).ready( function( $ ) {
				$( '.flexslider' ).flexslider({
					controlNav: false,
					start: function( slider ) {

						// The loading class prevents a white flash on slider start
						// see https://github.com/woothemes/FlexSlider/issues/848#issuecomment-42573918
						slider.removeClass( 'loading' );
					}
				});
			});
		</script>

		<?php 

			// Flexslider
			wp_enqueue_script( 'flexslider' );

		endif; ?>
	</div>

	<?php
}
