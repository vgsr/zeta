<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Zeta
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function zeta_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'zeta_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function zeta_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary:
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( __( 'Page %s', 'zeta' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'zeta_wp_title', 10, 2 );

	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function zeta_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'zeta_render_title' );
endif;

/**
 * Media
 */

/**
 * Return all images associated with the given post
 *
 * @since 1.0.0
 *
 * @uses has_post_thumbnail()
 * @uses get_post_thumbnail_id()
 * @uses has_shortcode()
 * @uses get_post_galleries_images()
 * @uses zeta_get_attachment_id_from_url()
 * @uses get_attached_media()
 * @uses get_media_embedded_in_content()
 * @uses zeta_check_image_size()
 * @uses apply_filters() Calls 'zeta_get_post_images'
 * 
 * @param int|object $post Post ID or post object
 * @param string|array $size Optional. Required image size as image size name
 *                            or as an array with width|height values.
 * @return array Collection of attachment IDs and/or image urls
 */
function zeta_get_post_images( $post, $size = '' ) {
	if ( ! $post = get_post( $post ) )
		return array();

	$collection = array();

	// 1. Featured image
	if ( has_post_thumbnail( $post->ID ) ) {
		$collection[] = get_post_thumbnail_id( $post->ID );
	}

	// 2. Galleries
	if ( has_shortcode( $post->post_content, 'gallery' ) ) {

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
	// $collection += array_filter( wp_list_pluck( (array) get_attached_media( 'image', $post->ID ), 'ID' ) );

	// Make collection contain only unique images
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

	// Filter image collection and return
	return apply_filters( 'zeta_get_post_images', $collection, $post, $size );
}

/**
 * Return the first image associated with the given post
 *
 * @since 1.0.0
 *
 * @uses zeta_get_post_images()
 * @uses zeta_check_image_size()
 * @uses apply_filters() Calls 'zeta_get_first_post_image'
 * 
 * @param int|object $post Post ID or post object
 * @param string|array $size Optional. Required image size. If empty any image
 *                            is returned.
 * @return bool|int|string False when no image was found, attachment ID or image url
 */
function zeta_get_first_post_image( $post, $size = '' ) {
	if ( ! $post = get_post( $post ) )
		return false;

	$images = zeta_get_post_images( $post );

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
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Finally, run a custom query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT p.ID FROM $wpdb->posts p, $wpdb->postmeta pm WHERE p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file' AND pm.meta_value = %s AND p.post_type = 'attachment'", $attachment_url ) );
	}

	return $attachment_id;
}

/**
 * Return whether the given image can be used
 *
 * @since 1.0.0
 *
 * @uses wp_get_attachment_image_src()
 * 
 * @param int|string $image Attachment ID or image src
 * @param string|array $size Optional. Required image size as image size name
 *                            or as an array with width|height values.
 * @return string|bool Image src if it can be used, false if not
 */
function zeta_check_image_size( $image, $size = 'medium' ) {

	// Treat as attachment ID
	if ( is_numeric( $image ) ) {
		$_image = wp_get_attachment_image_src( $image, $size );

	// Try to find it remotely
	} else {
		$_image = getimagesize( $image );

		// Transform details order when image was found
		if ( $_image ) {
			$_image[2] = $_image[1];
			$_image[1] = $_image[0];
			$_image[0] = $image;
		}
	}

	// Match image data with required size
	if ( $_image ) {

		// Get images size values
		if ( ! array( $size ) ) {
			$size = zeta_get_image_size( $size );

		// Map numeric dimensions
		} else {
			$size = array( 'width' => (int) $size[0], 'height' => (int) $size[1] );
		}

		// Compare sizes
		if ( $size['width'] <= (int) $_image[1] && $size['height'] <= (int) $_image[2] ) {

			// Return the image's src
			return $_image[0];
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
 * @uses zeta_get_image_sizes()
 * 
 * @param string $size Image size name
 * @return array|bool Image size details or false when size is not found.
 */
function zeta_get_image_size( $size ) {
	$sizes = zeta_get_image_sizes();

	// Get only 1 size if found
	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	} else {
		return false;
	}
}

	/**
	 * Return all availabel image sizes with details
	 *
	 * @since 1.0.0
	 *
	 * @uses get_intermediate_image_sizes()
	 * @return array Image sizes with details
	 */
	function zeta_get_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop details
		foreach( $get_intermediate_image_sizes as $_size ) {
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
