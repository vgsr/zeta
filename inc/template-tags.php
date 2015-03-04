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
 * @uses is_singular()
 * @uses get_queried_object_id()
 * @uses has_post_thumbnail()
 * @uses get_post_thumbnail_id()
 * @uses has_shortcode()
 * @uses get_post_gallery_images()
 * @uses zeta_get_attachment_id_from_url()
 * @uses get_attached_media()
 * @uses zeta_header_slider_get_image_dims()
 * @uses get_attachment_metadata()
 * @uses apply_filters() Calls 'zeta_header_slider_slide'
 * @uses apply_filters() Calls 'zeta_header_slider_slides'
 * @uses wp_enqueue_script()
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

	// Define images and slides collection
	$images = $slides = array();

	// The current post object
	if ( is_singular() ) {
		$post = get_queried_object();

		// Get the main image: the post thumbnail
		if ( has_post_thumbnail( $post->ID ) ) {
			$images[] = get_post_thumbnail_id( $post->ID );
		}

		// Get the first gallery's images
		if ( has_shortcode( get_post( $post->ID )->post_content, 'gallery' ) ) {
			foreach ( get_post_gallery_images( $post->ID ) as $image_url ) {
				if ( $image_att_id = zeta_get_attachment_id_from_url( $image_url ) ) {
					$images[] = $image_att_id;
				}
			}

		// Default to attached images
		} else {
			$images += array_filter( wp_list_pluck( (array) get_attached_media( 'image', $post->ID ), 'ID' ) );

			// Get embedded images
			$embedded = get_media_embedded_in_content( $post->post_content, 'img' );
			if ( ! empty( $embedded ) ) {
				$doc = new DOMDocument();
				foreach ( $embedded as $embedded_image ) {
					$doc->loadHTML( $embedded_image );
					foreach ( $doc->getElementsByTagName( 'img' ) as $tag ) {
						$img_src = $tag->getAttribute( 'src' );
						if ( $image_att_id = zeta_get_attachment_id_from_url( $img_src ) ) {
							$images[] = $image_att_id;
						} else {
							$images[] = array( 'src' => $img_src );
						}
					}
				}
			}
		}

	// Posts collection
	} elseif ( ( is_home() || is_archive() || is_search() ) && have_posts() ) {
		global $wp_query;

		// Get all queried post IDs
		$images = wp_list_pluck( $wp_query->posts, 'ID' );

	// Front page
	} elseif ( is_front_page() ) {

		// What to do here? Latest posts that have featured images, featured posts, custom front page gallery?
		$query = new WP_Query( array( 
			'posts_per_page' => 5, 
			'fields'         => 'ids',
			'meta_key'       => '_thumbnail_id',
			'meta_compare'   => 'EXISTS',
		) );

		// Get the five latest post IDs
		$images = $query->query();
	}

	// Walk all images
	foreach ( array_unique( array_values( $images ) ) as $args ) {

		// Handle post IDs
		if ( is_numeric( $args ) ) {
			if ( ! $post = get_post( (int) $args ) )
				continue;

			// Check the post type
			switch ( $post->post_type ) {

				// Media
				case 'attachment' :
					$args = array( 'src' => $post->ID );
					break;

				// Other
				default :
					$args = array( 'post_id' => $post->ID );
					break;
			}
		}

		// Fill slide variables
		$args = wp_parse_args( (array) $args, array(
			'post_id' => false,
			'src'     => false,
			'href'    => false,
			'title'   => false,
			'byline'  => false
		) );

		// For a single slide: get post data. Not when we're already there.
		if ( ! empty( $args['post_id'] ) && get_queried_object_id() !== $args['post_id'] ) {
			if ( ! $post = get_post( $args['post_id'] ) )
				continue;

			// Find a single image for this slide
			if ( empty( $args['src'] ) ) {
				$src = false;

				do {

					// Get the post's featured image
					if ( has_post_thumbnail( $post->ID ) && ( $src = zeta_header_slider_check_image_dims( get_post_thumbnail_id( $post->ID ) ) ) )
						break;

					// Get the post's first gallery's first image
					if ( has_shortcode( $post->post_content, 'gallery' ) ) {
						foreach ( get_post_gallery_images( $post ) as $image_url ) {
							if ( $src = zeta_header_slider_check_image_dims( zeta_get_attachment_id_from_url( $image_url ) ) )
								break 2;
						}
					}

					// Get the post's first attached image
					if ( ( $imgs = get_attached_media( 'image', $post->ID ) ) && ! empty( $imgs ) ) {
						foreach ( wp_list_pluck( $imgs, 'ID' ) as $att_id ) {
							if ( $src = zeta_header_slider_check_image_dims( $att_id ) )
								break 2;
						}
					}

					// Get the post's first embedded image
					if ( ( $imgs = get_media_embedded_in_content( $post->post_content, 'img' ) ) && ! empty( $imgs ) ) {
						$doc = new DOMDocument();
						foreach ( $imgs as $embedded_image ) {
							$doc->loadHTML( $embedded_image );
							foreach ( $doc->getElementsByTagName( 'img' ) as $tag ) {
								$img_src = $tag->getAttribute( 'src' );
								if ( $att_id = zeta_get_attachment_id_from_url( $img_src ) ) {
									$img_src = $att_id;
								}
								if ( $src = zeta_header_slider_check_image_dims( $img_src ) )
									break 3;
								// if ( $src = zeta_header_slider_check_image_dims( zeta_get_attachment_id_from_url( $img_src ) ) )
								// 	break 3;
							}
						}
					}

				} while ( 0 );

				// Image found? Keep it. Else skip slide
				if ( $src ) {
					$args['src'] = $src;
				} else {
					continue;
				}
			}

			// Get post permalink
			$args['href'] = get_permalink( $post->ID );

			// Get post title
			$args['title'] = get_the_title( $post->ID );

			// Get post details
			$args['byline'] = sprintf( __( 'Posted on %s', 'zeta' ), get_the_date( '', $post->ID ) );
		}

		// Attachment ID provided instead of image url
		if ( is_numeric( $args['src'] ) ) {
			$att_id = (int) $args['src'];

			// Get correct image size's url
			if ( $src = zeta_header_slider_check_image_dims( $att_id ) ) {
				$args['src'] = $src;

			// Image is too small, so skip slide
			} else {
				continue;
			}

			$metadata = wp_get_attachment_metadata( $att_id );

			// Get original image link
			if ( apply_filters( 'zeta_header_image_use_image_url', false, $att_id ) ) {
				$upload_dir = wp_upload_dir();
				$args['href'] = trailingslashit( $upload_dir['baseurl'] ) . $metadata['file'];
			}

			// Get attachment title
			if ( apply_filters( 'zeta_header_image_use_image_title', false, $att_id ) 
				&& ( $att_title = get_the_title( $att_id ) ) && ! empty( $att_title ) ) {
				$args['title'] = $att_title;
			}

			// Get attachment details
			if ( apply_filters( 'zeta_header_image_use_image_credits', false, $att_id ) 
				&& ! empty( $metadata['image_meta']['credit'] ) ) {
				$args['byline'] = sprintf( __( 'Created by %s', 'zeta' ), $metadata['image_meta']['credit'] );
			}

		// Image is missing, so skip slide
		} elseif ( empty( $args['src'] ) ) {
			continue;
		}

		// If we made it this far, add to slides collection
		$slides[] = $args;
	}

	// When no valid images were found, get the default slider images
	if ( empty( $slides ) ) {
		$defaults = array( 'benches.jpg', 'bridge.jpg', 'desktop.jpg', 'downtown.jpg', 'tools.jpg' );
		$default  = array_rand( $defaults ); 
		$slides[] = array( 
			'post_id' => false,
			'src'     => get_template_directory_uri() . '/images/headers/' . $defaults[ $default ],
			'href'    => false,
			'title'   => false,
			'byline'  => false,
		);
	}

	// Build the slides
	foreach ( $slides as $i => $args ) {
		$slide = '';

		// Define image container tag. Use anchor when a link is provided
		$tag = ! empty( $args['href'] ) ? 'a' : 'div'; 

		// Start image container
		$slide = '<' . $tag . ' class="slide-inner" style="background-image: url(' . esc_attr( $args['src'] ) . ');"';

		// Add link to the element
		if ( 'a' === $tag ) {
			$slide .= ' href="' . esc_attr( $args['href'] ) . '"';
		}
		$slide .= '>';

		// Handle titles
		if ( ! empty( $args['title'] ) ) {
			$slide .= '<header class="slide-details"><h2>' . $args['title'] . '</h2>';

			// Append byline
			if ( ! empty( $args['byline'] ) ) {
				$slide .= '<span class="byline">' . $args['byline'] . '</span>';
			}

			$slide .= '</header>';
		}

		// Close image container
		$slide .= '</' . $tag . '>';

		// Filter and add slide content to the slides collection
		$slides[ $i ] = apply_filters( 'zeta_header_slider_slide', $slide, $args, $tag, $i );
	}

	// Filter all header slides
	$slides      = apply_filters( 'zeta_header_slider_slides', $slides ); 
	$slide_count = count( $slides ); ?>

	<div class="slider flexslider loading">
		<ul class="slides">
			<?php foreach ( array_values( $slides ) as $i => $slide ) : 
			?><li class="slide" style="z-index: <?php echo $slide_count - $i; ?>;"><?php echo $slide; ?></li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $slide_count > 1 ) : ?>

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

			// Enqueue flexslider script
			wp_enqueue_script( 'flexslider' );

		endif; ?>
	</div>

	<?php
}

/**
 * Return whether the given image can be used
 *
 * @since 1.0.0
 *
 * @uses wp_get_attachment_image_src()
 * 
 * @param int|string $image Attachment ID or image source
 * @return string|bool Image src if it can be used, false if not
 */
function zeta_header_slider_check_image_dims( $image_id ) {

	// Treat as attachment ID
	if ( is_numeric( $image_id ) ) {
		$image = wp_get_attachment_image_src( $image_id, 'full' );

	// Try to find it remotely
	} else {
		$image = getimagesize( $image_id );

		// Transform details setup when image was found
		if ( $image ) {
			$image[2] = $image[1];
			$image[1] = $image[0];
			$image[0] = $image_id;
		}
	}

	// Require image to be at least 1200 x 900
	if ( $image && 1200 <= (int) $image[1] && 900 <= (int) $image[2] ) {

		// Should we find or create an image size that is closest to 1200 x 900 instead of full?
		return $image[0];

	// Image not found or is too small
	} else {
		return false;
	}
}
