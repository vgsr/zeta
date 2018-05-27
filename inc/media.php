<?php

/**
 * Zeta Media Functions
 *
 * @package Zeta
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/** Post Images ************************************************************/

/**
 * Return whether the given post contains a gallery
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post_id Optional. Post ID or object. Defaults to current post
 * @return bool Post contains a gallery
 */
function zeta_has_post_gallery( $post_id = 0 ) {
	if ( ! $post = get_post( $post_id ) )
		return false;

	return has_shortcode( $post->post_content, 'gallery' );
}

/**
 * Return all images associated with the given post
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'zeta_pre_get_post_images'
 * @uses apply_filters() Calls 'zeta_get_post_images'
 * 
 * @param int|object $post Optional. Post ID or post object. Defaults to the current post.
 * @param string|array $size Optional. Required image size as image size name
 *                            or as an array with width|height values.
 * @return array Collection of attachment IDs and/or image urls
 */
function zeta_get_post_images( $post = 0, $size = false ) {
	if ( ! $post = get_post( $post ) )
		return array();

	// Passing a non-empty array will effectively short ciruit the default logic
	if ( ! $collection = (array) apply_filters( 'zeta_pre_get_post_images', array(), $post, $size ) ) {

		// 1. Featured image
		if ( has_post_thumbnail( $post->ID ) ) {
			$collection[] = get_post_thumbnail_id( $post->ID );
		}

		// 2. Galleries
		if ( zeta_has_post_gallery( $post ) ) {

			// Walk all post's galleries
			foreach ( get_post_galleries_images( $post->ID ) as $srcs ) {
				foreach ( $srcs as $image_src ) {

					// Find the image's attachment ID 
					if ( $image_att_id = zeta_get_attachment_id_from_url( $image_src ) ) {
						$collection[] = $image_att_id;

					// Use the image src
					} else {
						$collection[] = $image_src;
					}
				}
			}
		}

		// 3. Embedded images
		if ( $embedded = get_media_embedded_in_content( $post->post_content, 'img' ) ) {
			$doc = new DOMDocument();

			// Walk all found <img>s in the content
			foreach ( $embedded as $embedded_image ) {
				$doc->loadHTML( $embedded_image );
				foreach ( $doc->getElementsByTagName( 'img' ) as $tag ) {
					$image_src = $tag->getAttribute( 'src' );

					// Find the image's attachment ID
					if ( $image_att_id = zeta_get_attachment_id_from_url( $image_src ) ) {
						$collection[] = $image_att_id;

					// Use the image src
					} else {
						$collection[] = $image_src;
					}
				}
			}
		}

		// 4. Attached images
		$collection += array_filter( wp_list_pluck( (array) get_attached_media( 'image', $post->ID ), 'ID' ) );
	}

	// Filter image collection
	$collection = apply_filters( 'zeta_get_post_images', $collection, $post, $size );

	// Have only unique images
	$collection = array_unique( $collection );

	// Check for image sizes
	if ( $size ) {
		foreach ( $collection as $k => $image ) {

			// Remove images from collection when they are too small
			if ( ! zeta_check_image_size( $image, $size ) ) {
				unset ( $collection[ $k ] );
			}
		}

		$collection = array_values( $collection );
	}

	return $collection;
}

/**
 * Support Featured Images plugin
 *
 * When the post has Featured Images, let those take
 * precedence over other associated images.
 *
 * @since 1.0.0
 *
 * @param array $images Attachment ids
 * @param WP_Post $post Post object
 * @param string|array $size
 * @return array Attachment ids
 */
function zeta_get_featured_images( $images, $post, $size ) {

	// When using Featured Images plugin
	if ( function_exists( 'featured_images' ) ) {

		// When the post has featured images
		if ( $featured = get_featured_images( $post->ID ) ) {

			// Prepend post thumbnail
			if ( has_post_thumbnail( $post->ID ) ) {
				$images[] = get_post_thumbnail_id( $post->ID );
			}

			// Get other featured images
			$images = array_merge( $images, $featured );
		}
	}

	return $images;
}
add_filter( 'zeta_pre_get_post_images', 'zeta_get_featured_images', 10, 3 );

/**
 * Return the first image associated with the given post
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'zeta_get_first_post_image'
 * 
 * @param int|object $post Optional. Post ID or post object. Defaults to the current post.
 * @param string|array $size Optional. Required image size. If empty any image
 *                            is returned.
 * @return bool|int|string False when no image was found, attachment ID or image url
 */
function zeta_get_first_post_image( $post = 0, $size = '' ) {
	if ( ! $post = get_post( $post ) )
		return false;

	$images = zeta_get_post_images( $post );
	$image  = false;

	// Require by image size
	if ( $size ) {
		foreach ( $images as $image ) {

			// Stop walking when a valid image was found
			if ( $image = zeta_check_image_size( $image, $size ) )
				break;
		}
	} else {
		$image = reset( $images );
	}

	return apply_filters( 'zeta_get_first_post_image', $image, $post, $size );
}

/**
 * Return the attachment ids of a post's gallery shortcodes
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post Optional. Post ID. Defaults to the current post
 * @return array Attachment ids
 */
function zeta_get_post_galleries_attachment_ids( $post = 0 ) {
	$galleries = get_post_galleries( $post, false );
	$attachments = array();

	if ( $galleries ) {
		$attachments =
			// Remove any blanks
			array_filter(
				// Check whether these are real attachments
				array_map( 'zeta_is_post_attachment',
					// Remove excess space
					array_map( 'trim',
						// Turn the single id list into an array
						explode( ',',
							// Combine the elements of multiple galleries
							implode( ',',
								// Get `ids` shortcode argument values
								wp_list_pluck( $galleries, 'ids' )
							)
						)
					)
				)
			);
	}

	return $attachments;
}

/**
 * Filter the content for a gallery post's excerpt
 *
 * @since 1.0.0
 */
function zeta_gallery_post_excerpt( $content ) {

	// Only when this is not a single view
	if ( ! is_singular() && has_post_format( 'gallery' ) && $attachment_ids = zeta_get_post_galleries_attachment_ids() ) {

		// Randomize the images
		shuffle( $attachment_ids );

		// Setup gallery shortcode with a preview of 6 images
		$content = sprintf( '[gallery ids="%s"]', implode( ',', array_slice( $attachment_ids, 0, 6 ) ) );
	}

	return $content;
}
add_filter( 'the_content', 'zeta_gallery_post_excerpt', 8 );

/**
 * Filter the gallery post's excerpt image link
 *
 * @since 1.0.0
 *
 * @param string $url The attachment url
 * @param int $post_id Post ID
 * @return string Attachment link url
 */
function zeta_gallery_post_excerpt_image_link( $url, $post_id ) {

	// Only when this is not a single view
	// The 'post_gallery' hook is used by the Tiled Gallery module to short-circuit the gallery logic.
	if ( ! is_singular() && doing_action( 'the_content' ) && has_post_format( 'gallery' ) && doing_action( 'post_gallery' ) ) {

		// Define the url as the gallery post's permalink
		$url = get_the_permalink();
	}

	return $url;
}
add_filter( 'attachment_link', 'zeta_gallery_post_excerpt_image_link', 10, 6 );

/**
 * Display the gallery post's image count
 *
 * @since 1.0.0
 */
function zeta_gallery_post_image_count() {

	// When post has a gallery with images
	if ( has_post_format( 'gallery' ) && $attachment_ids = zeta_get_post_galleries_attachment_ids() ) {
		printf( '<span class="image-count">%s</span>', sprintf( _nx( '%d Image', '%d Images', count( $attachment_ids ), 'Gallery post-format image count', 'zeta' ), count( $attachment_ids ) ) );
	}
}
add_action( 'zeta_entry_meta', 'zeta_gallery_post_image_count' );

/**
 * Return whether the post is an attachment
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post Optional. Post ID or object. Defaults to the current post.
 * @return bool|int False if post is not an attachment, else the post ID.
 */
function zeta_is_post_attachment( $post = 0 ) {
	if ( ! ( $post = get_post( $post ) ) || ( 'attachment' != $post->post_type ) ) {
		return false;
	} else {
		return $post->ID;
	}
}

/**
 * Return the attachment ID for an attachment url
 *
 * @since 1.0.0
 *
 * @link https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
 * 
 * @param string $image_url Attachment url
 * @return int|bool Attachment ID or false when not found
 */
function zeta_get_attachment_id_from_url( $attachment_url ) {
	$attachment_id = false;

	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
		global $wpdb;

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = strtok( $attachment_url, '?' );

		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Finally, run a custom query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT p.ID FROM $wpdb->posts p, $wpdb->postmeta pm WHERE p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file' AND pm.meta_value = %s AND p.post_type = 'attachment'", $attachment_url ) );
	}

	return $attachment_id;
}

/**
 * Return whether the given image matches the required size
 *
 * @since 1.0.0
 *
 * @param int|string $image Attachment ID or image src
 * @param string|array $size Optional. Required image size as image size name
 *                            or as an array with width|height values.
 * @return string|bool Image src if it can be used, false if not
 */
function zeta_check_image_size( $image, $size = 'medium' ) {

	// Get the requested image size's dimensions
	if ( ! is_array( $size ) ) {
		$dims = zeta_get_image_size( $size );
	} else {
		$dims = array( 'width' => (int) $size[0], 'height' => (int) $size[1] );
	}

	// An image location
	if ( ! is_numeric( $image ) ) {

		// Try to find the image (remotely)
		$_image = @getimagesize( $image );

		// Transform details order when image was found
		if ( $_image ) {
			$_image[2] = $_image[1];
			$_image[1] = $_image[0];
			$_image[0] = $image;

			// Return the image's src when it is large enough
			if ( $dims['width'] <= (int) $_image[1] && $dims['height'] <= (int) $_image[2] ) {
				return $_image[0];
			}
		}

	// An attachment ID
	} else {

		// Walk all larger image sizes
		foreach ( array_keys( array_reverse( zeta_get_larger_image_sizes( $size ), true ) ) as $size ) {

			// Get attachment
			$_image = wp_get_attachment_image_src( $image, $size );

			// Match image data with required size
			if ( $_image ) {

				// Return the image's src when it is large enough
				if ( $dims['width'] <= (int) $_image[1] && $dims['height'] <= (int) $_image[2] ) {
					return $_image[0];
				}
			}
		}
	}

	// Image was not found or too small
	return false;
}

/**
 * Return a given image size
 *
 * @since 1.0.0
 *
 * @param string $size Image size name
 * @return array|bool Image size details or false when size is not found.
 */
function zeta_get_image_size( $size ) {
	$sizes = zeta_get_image_sizes();

	// Get only 1 size when found
	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	} else {
		return false;
	}
}

/**
 * Return the image sizes that contain at least the given size's dimensions
 *
 * @since 1.0.0
 * 
 * @param string|array $size Image size
 * @return array Larger image sizes
 */
function zeta_get_larger_image_sizes( $size ) {
	$sizes = zeta_get_image_sizes();

	// Get the size's numbers
	if ( is_string( $size ) ) {
		if ( isset( $sizes[ $size ] ) ) {
			$size = array( $sizes[ $size ]['width'], $sizes[ $size ]['height'] );
		}
	}

	// Walk the available sizes
	foreach ( $sizes as $k => $_size ) {

		// Remove smaller sizes
		if ( $_size['width'] < $size[0] || $_size['height'] < $size[1] ) {
			unset( $sizes[ $k ] );
		}
	}

	// Order to size, largest to smallest
	uasort( $sizes, 'zeta_image_size_cmp' );

	// Append 'full' original image size
	$sizes['full'] = array( 'width' => 9999, 'height' => 9999, 'crop' => false );

	return $sizes;
}

	/**
	 * Return all availabel image sizes with details
	 *
	 * @since 1.0.0
	 *
	 * @return array Image sizes with details
	 */
	function zeta_get_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = array();
		$intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop details
		foreach( $intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );

			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array( 
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}

		return $sizes;
	}

/**
 * Sort image sizes, largest to smallest
 *
 * @since 1.0.0
 * 
 * @param array $a
 * @param array $b
 * @return int Compare value
 */
function zeta_image_size_cmp( $a, $b ) {
	if ( $a['width'] > $b['width'] ) {
		if ( $a['height'] > $b['height'] ) {
			return 1;
		} else {
			return -1;
		}
	} else {
		return -1;
	}
}

/**
 * Modify the allowed media types to search for in content
 *
 * Filter is available since WP 4.2.0.
 *
 * @since 1.0.0
 * 
 * @param array $types Allowed media types
 * @return Allowed media types
 */
function zeta_get_media_embedded_in_content_allowed( $types ) {

	// Allow searching for <img> tags
	$types[] = 'img';

	return $types;
}
add_filter( 'get_media_embedded_in_content_allowed', 'zeta_get_media_embedded_in_content_allowed' );

/**
 * Short-circuit the post thumbnail ID when it doens't have one and
 * serve another image.
 *
 * @since 1.0.0
 *
 * @param mixed|null $value Short-circuit value.
 * @param int $object_id Object ID
 * @param string $meta_key Meta key
 * @param bool $single Whether to query one or multiple values
 * @return int|null Thumbnail ID or null to skip the short-circuit
 */
function zeta_post_thumbnail_id( $value, $object_id, $meta_key, $single ) {

	// Querying the thumbnail ID in the front
	if ( null === $value && '_thumbnail_id' == $meta_key && ! is_admin() ) {
		global $wpdb, $wp_current_filter;

		// Prevent infinite looping
		$counts = array_count_values( $wp_current_filter );
		if ( $counts['get_post_metadata'] > 1 ) {
			return $value;
		}

		// Try fetching from the cache
		$meta_cache = wp_cache_get( $object_id, 'post_meta' );
		if ( $meta_cache && isset( $meta_cache[ $meta_key ] ) ) {
			return null;
		}

		// Does the post have a thumbnail?
		$sql = $wpdb->prepare( "SELECT 1 FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s", $object_id, $meta_key );
		$has_thumbnail =  $wpdb->get_var( $sql );

		// When without thumbnail, get our own version
		if ( ! $has_thumbnail && $image = zeta_get_first_post_image( $object_id ) ) {
			if ( is_numeric( $image ) ) {
				$value = (int) $image;
			}
		}
	}

	return $value;
}
add_filter( 'get_post_metadata', 'zeta_post_thumbnail_id', 10, 4 );
