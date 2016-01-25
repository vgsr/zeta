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

	// Open tools container for tools with default toggle status
	if ( wp_list_filter( zeta_get_site_tools(), array( 'toggle' => true ) ) ) {
		$classes[] = 'tools-toggled';
	}

	// Layout. Not for the Front Page or 404.
	if ( ! is_front_page() && ! is_404() ) {
		$layout = get_theme_mod( 'default_layout' );

		// Non-single layout and sidebar is present
		if ( 'single-column' != $layout && is_active_sidebar( 'sidebar-1' ) ) {
			$classes[] = 'with-sidebar';
		}

		// Sidebar-Content. Not for BuddyPress
		if ( 'sidebar-content' == $layout && ! is_buddypress() ) {
			$classes[] = 'sidebar-content';
		}
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
 * Output a breadcrumbs trail before the page's content
 *
 * @since 1.0.0
 *
 * @uses is_front_page()
 * @uses yoast_breadcrumb()
 * @uses bbp_breadcrumb()
 */
function zeta_breadcrumbs() {

	// Bail when on the site's front page
	if ( is_front_page() )
		return;

	// Using Yoast SEO
	if ( function_exists( 'yoast_breadcrumb' ) ) {

		// Modify crumbs
		add_filter( 'wpseo_breadcrumb_links', 'zeta_wpseo_breadcrumb_links' );

		// Output crumbs
		yoast_breadcrumb( '<div id="breadcrumb" class="yoast-breadcrumb">', '</div>' );

		// Undo modify crumbs
		remove_filter( 'wpseo_breadcrumb_links', 'zeta_wpseo_breadcrumb_links' );

	// Using bbPress
	} elseif ( function_exists( 'bbp_breadcrumb' ) ) {

		// Set home text to page title
		if ( $front_id = get_option( 'page_on_front' ) ) {
			$pre_front_text = get_the_title( $front_id );

		// Default to 'Home'
		} else {
			$pre_front_text = __( 'Home', 'bbpress' );
		}

		// Remove separator
		add_filter( 'bbp_breadcrumb_separator', '__return_empty_string' );

		// Output crumbs
		bbp_breadcrumb( array(
			'before'       => '<div id="breadcrumb" class="bbp-breadcrumb">',
			'after'        => '</div>',
			'crumb_before' => '<span>',
			'crumb_after'  => '</span>',
			'home_text'    => '<span class="screen-reader-text">' . $pre_front_text . '</span>',
		) );

		// Undo remove separator
		remove_filter( 'bbp_breadcrumb_separator', '__return_empty_string' );
	}
}
add_action( 'zeta_pre_content', 'zeta_breadcrumbs', 6 );

	/**
	 * Modify the crumbs collection of Yoast SEO
	 *
	 * @since 1.0.0
	 *
	 * @uses get_home_url()
	 *
	 * @param array $crumbs Crumbs
	 * @return array Crumbs
	 */
	function zeta_wpseo_breadcrumb_links( $crumbs ) {

		// Walk all crumbs
		foreach ( $crumbs as $k => $crumb ) {

			// Wrap the Home crumb in screen-reader-text
			if ( get_home_url() === $crumb['url'] ) {
				$crumbs[ $k ]['text'] = '<span class="screen-reader-text">' . $crumb['text'] . '</span>';
				break;
			}
		}

		return $crumbs;
	}

/**
 * Modify the categories in the category list
 *
 * @since 1.0.0
 *
 * @param array $cats Post categories
 * @param int $post_id Post ID
 * @return array Post categories
 */
function zeta_the_category_list( $cats, $post_id ) {

	// Remove the default category ('uncategorized') from the list
	$cats = array_values( wp_list_filter( $cats, array( 'term_id' => (int) get_option( 'default_category' ) ), 'NOT' ) );

	return $cats;
}
add_filter( 'the_category_list', 'zeta_the_category_list', 10, 2 );

/**
 * Modify the category display name
 *
 * @since 1.0.0
 *
 * @param string $cat Category name
 * @return string Category name
 */
function zeta_the_category( $cat ) {

	// In the loop, hide the 'uncategorized' (default) category
	if ( __( 'Uncategorized' ) === $cat && ! is_admin() && in_the_loop() ) {
		$cat = '';
	}

	return $cat;
}
add_filter( 'the_category', 'zeta_the_category' );

/** Comments ***************************************************************/

/**
 * Modify the post's comment content
 *
 * @since 1.0.0
 *
 * @uses in_the_loop()
 * @uses get_comment_author_url()
 * @uses get_comment_author()
 * @uses apply_filters() Calls 'get_comment_author_link'
 *
 * @param string $content Comment content
 * @param WP_Comment $comment Comment object
 * @param array $args Comment query arguments
 * @return string Comment content
 */
function zeta_comment_text( $content, $comment = 0, $args = array() ) {

	// Only when we're looping a post's comments
	if ( ! is_admin() && in_the_loop() ) {

		/**
		 * Mimic {@see get_comment_author_link()}.
		 */
		$url    = get_comment_author_url( $comment );
		$author = get_comment_author( $comment );

		if ( empty( $url ) || 'http://' == $url ) {
			$link = '<span class="comment-author">%2$s</span>';
		} else {
			$link = '<a href="%s" class="comment-author url" rel="external nofollow">%s</a>';
		}

		/** This filter is documented in wp-includes/comment-template.php */
		$link = apply_filters( 'get_comment_author_link', sprintf( $link, $url, $author ), $author, $comment->comment_ID );

		// Prepend the comment user's display name to the comment content
		$content = "$link $content";
	}

	return $content;
}
add_filter( 'comment_text', 'zeta_comment_text', 4, 3 );

/** Media ******************************************************************/

/**
 * Return whether the given post contains a gallery
 *
 * @since 1.0.0
 *
 * @uses has_shortcode()
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
 * @uses has_post_thumbnail()
 * @uses get_post_thumbnail_id()
 * @uses zeta_has_post_gallery()
 * @uses get_post_galleries_images()
 * @uses zeta_get_attachment_id_from_url()
 * @uses get_attached_media()
 * @uses get_media_embedded_in_content()
 * @uses zeta_check_image_size()
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

	$collection = array();

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
	// $collection += array_filter( wp_list_pluck( (array) get_attached_media( 'image', $post->ID ), 'ID' ) );

	// Filter image collection
	$collection = apply_filters( 'zeta_get_post_images', $collection, $post, $size );

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

	return $collection;
}

	/**
	 * Add connected images to the current post's image collection
	 *
	 * @since 1.0.0
	 *
	 * @todo Move this to a plugin
	 * 
	 * @param array $images
	 * @param WP_Post $post The current post
	 * @param string|array $size
	 * @return array Post images. Ids or image paths
	 */
	function zeta_p2p_post_images( $images, $post, $size ) {

		// Bail when P2P is not active
		if ( ! did_action( 'p2p_init' ) )
			return $images;

		// BP user profile
		if ( function_exists( 'buddypress' ) && bp_is_user() ) {
			$connected_item  = bp_displayed_user_id();
			$connection_type = 'user';

		// Posts
		} else {
			$connected_item  = $post->ID;
			$connection_type = $post->post_type;
		}

		// Tagged images
		if ( $tagged = new WP_Query( array(
			'fields'          => 'ids',
			'connected_type'  => $connection_type . '_media',
			'connected_items' => $connected_item,
			'post_mime_type'  => 'image',
			'posts_per_page'  => 5,
			'orderby'         => 'rand'
		) ) ) {
			foreach ( $tagged->posts as $post_id ) {
				$images[] = $post_id;
			}
		}

		return $images;
	}
	add_filter( 'zeta_get_post_images', 'zeta_p2p_post_images', 10, 3 );

/**
 * Return the first image associated with the given post
 *
 * @since 1.0.0
 *
 * @uses zeta_get_post_images()
 * @uses zeta_check_image_size()
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
 * @uses get_post_galleries()
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
 *
 * @uses is_singular()
 * @uses has_post_format()
 * @uses zeta_get_post_galleries_attachment_ids()
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
 * Display the gallery post's image count
 *
 * @since 1.0.0
 *
 * @uses has_post_format()
 * @uses zeta_get_post_galleries_attachment_ids()
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
 * @uses zeta_get_image_size()
 * @uses zeta_get_larger_image_sizes()
 * @uses wp_get_attachment_image_src()
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
 * @uses zeta_get_image_sizes()
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
	 * @uses get_intermediate_image_sizes()
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

/** Search *****************************************************************/

/**
 * Append a context filter element to the search form
 *
 * @since 1.0.0
 *
 * @uses get_post_type()
 * @uses get_post_type_object()
 * @uses post_type_exists()
 * @uses is_buddypress()
 * @uses is_user_vgsr()
 * @uses apply_filters() Calls 'zeta_search_contexts'
 * @uses is_search()
 * 
 * @param string $form Search form markup
 * @return string Search form markup
 */
function zeta_search_context_select( $form ) {
	$contexts = array();

	// Get the requested context
	$_context = isset( $_GET['context'] ) ? $_GET['context'] : false;

	// Current post's post type
	if ( $post_type = get_post_type() ) {
		$contexts[ $post_type ] = get_post_type_object( $post_type )->labels->name;
	}

	// Make requested post type context available
	if ( $_context && post_type_exists( $_context ) ) {
		$contexts[ $_context ] = get_post_type_object( $_context )->labels->name;
	}

	// Consider BuddyPress 
	if ( function_exists( 'buddypress' ) ) {

		// Remove the post type (page) context
		if ( is_buddypress() ) {
			unset( $contexts[ $post_type ] );
		}

		// Search members
		if ( function_exists( 'vgsr' ) && is_user_vgsr() ) {
			$contexts['bp-members'] = __( 'Members', 'zeta' );
		}

		// Search groups
		if ( bp_is_active( 'groups' ) && 0 < groups_get_total_group_count() ) {
			$contexts['bp-groups'] = __( 'Groups', 'zeta' );
		}
	}

	$contexts = (array) apply_filters( 'zeta_search_contexts', $contexts );

	// Setup <select> element with available contexts
	if ( ! empty( $contexts ) ) {
		$options = "\t<option>" . _x( 'All', 'Search context', 'zeta' ) . '</option>';
		foreach ( $contexts as $context => $label ) {
			$options .= sprintf( "\t<option value=\"%s\" %s>%s</option>", esc_attr( $context ), selected( ( is_search() && $context === $_context ), true, false ), esc_html( $label ) );
		}

		// Append element to the form
		$form = str_replace( '</form>', '<select class="zeta-search-context" name="context">' . $options .'</select></form>', $form );

		// Add a context-aware form class
		$form = str_replace( 'class="search-form', 'class="search-form with-context', $form );
		$form = str_replace( 'class="searchform',  'class="searchform with-context',  $form );
	}

	return $form;
}
add_filter( 'get_search_form', 'zeta_search_context_select' );

/**
 * Redirect the search request to the context's specific results page
 *
 * @since 1.0.0
 *
 * @uses is_search()
 * @uses bp_core_get_directory_page_ids()
 * @uses is_user_vgsr()
 * @uses get_permalink()
 * @uses apply_filters() Calls 'zeta_search_context_redirect'
 * @uses wp_safe_redirect()
 */
function zeta_search_context_redirect() {

	// Bail when this is not a search request and no context is provided
	if ( ! is_search() || ! isset( $_GET['context'] ) )
		return;

	$location = false;
	$context  = esc_attr( $_GET['context'] );
	$s        = esc_attr( $_GET['s'] );

	if ( function_exists( 'buddypress' ) ) {
		$page_ids = bp_core_get_directory_page_ids( 'all' );
	}

	switch ( $context ) {
		case 'bp-members' :
			if ( function_exists( 'buddypress' ) && function_exists( 'vgsr' ) && is_user_vgsr() ) {
				$location = add_query_arg( 's', $s, get_permalink( $page_ids['members'] ) ); // Members index page
			}
			break;

		case 'bp-groups' :
			if ( function_exists( 'buddypress' ) && bp_is_active( 'groups' ) ) {
				$location = add_query_arg( 's', $s, get_permalink( $page_ids['groups'] ) ); // Groups index page
			}
			break;

		default :
			$location = apply_filters( 'zeta_search_context_redirect', $location, $context, $s );
			break;
	}

	// Redirect to valid location
	if ( $location ) {
		wp_safe_redirect( esc_url_raw( $location ) );
	}
}
add_action( 'template_redirect', 'zeta_search_context_redirect' );

/**
 * Handle search context specific redirectioning
 *
 * @since 1.0.0
 *
 * @uses WP_Query::is_main_query()
 * @uses WP_Query::is_search()
 * @uses post_type_exists()
 *
 * @param WP_Query $query The query
 */
function zeta_search_context_parse_query( $query ) {

	// Bail when this is not the main query and a search request and no context is provided
	if ( ! $query->is_main_query() || ! $query->is_search() || ! isset( $_GET['context'] ) )
		return;

	// Set the post type query var when given as context
	if ( post_type_exists( esc_attr( $_GET['context'] ) ) ) {
		$query->query_vars[ 'post_type' ] = esc_attr( $_GET['context'] );
	}
}
add_action( 'parse_query', 'zeta_search_context_parse_query' );

/** Widgets ****************************************************************/

/**
 * Modify the widget's form options
 *
 * @since 1.0.0
 * 
 * @uses WP_Widget::get_field_name()
 * 
 * @param WP_Widget $widget
 * @param string $return Form output markup
 * @param array $instance Widget settings
 */
function zeta_widget_form( $widget, $return, $instance ) { ?>

	<h4><?php esc_html_e( 'Theme Settings', 'zeta' ); ?></h4>

	<?php // Output the full-width checkbox
	printf( '<p><label><input id="%1$s" type="checkbox" name="%2$s" value="1" %3$s /> %4$s</label></p>',
		$widget->get_field_id( 'zeta_full_width' ),
		$widget->get_field_name( 'zeta-full-width' ),
		checked( isset( $instance['zeta-full-width'] ) && $instance['zeta-full-width'], true, false ),
		__( 'Use the full content width for this widget on larger screens.', 'zeta' )
	);
}
add_action( 'in_widget_form', 'zeta_widget_form', 50, 3 );

/**
 * Modify the widget's updated settings
 *
 * @since 1.0.0
 * 
 * @param array $instance Widget settings
 * @param array $new_instance
 * @param array $old_instance
 * @param WP_Widget $widget
 * @return array Widget settings
 */
function zeta_widget_update( $instance, $new_instance, $old_instance, $widget ) {

	// Update (un)checked full-width setting
	if ( isset( $new_instance['zeta-full-width'] ) ) {
		$instance['zeta-full-width'] = true;
	} else {
		unset( $instance['zeta-full-width'] );
	}

	return $instance;
}
add_filter( 'widget_update_callback', 'zeta_widget_update', 10, 4 );

/**
 * Modify the widget's display params
 *
 * @since 1.0.0
 *
 * @uses WP_Widget::get_settings()
 * 
 * @param array $params Widget's sidebar params
 * @return array Widget params
 */
function zeta_widget_display_params( $params ) {

	// Bail when in the admin
	if ( ! is_admin() ) {
		global $wp_registered_widgets;

		// Get this widget object's settings
		$widget_obj = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
		$widget_nr  = $params[1]['number'];
		$settings   = $widget_obj->get_settings();

		if ( $settings && isset( $settings[ $widget_nr ] ) ) {
			$widget = $settings[ $widget_nr ];

			// Add 'full-width' class when widget is marked as such
			if ( isset( $widget['zeta-full-width'] ) && $widget['zeta-full-width'] ) {
				$params[0]['before_widget'] = str_replace( 'class="', 'class="full-width ', $params[0]['before_widget'] );
			}
		}
	}

	return $params;
}
add_filter( 'dynamic_sidebar_params', 'zeta_widget_display_params' );

/**
 * Return the count of a sidebar's widgets that have a given setting
 *
 * @since 1.0.0
 *
 * @uses wp_get_sidebars_widgets()
 * 
 * @param string $sidebar_id Sidebar ID
 * @param string $key Setting key
 * @param mixed $value Optional. The value to match. Defaults to checking
 *                      for any value.
 * @return int Widget count
 */
function zeta_count_widgets_with_setting( $sidebar_id, $key, $value = null ) {
	global $wp_registered_widgets;

	// Get all sidebars and their widgets
	$sidebars = wp_get_sidebars_widgets();

	// Bail when sidebar is not found
	if ( ! isset( $sidebars[ $sidebar_id ] ) || ! is_array( $sidebars[ $sidebar_id ] ) )
		return false;

	// Define local variable(s)
	$count = 0;

	// Walk the sidebar's widgets
	foreach ( $sidebars[ $sidebar_id ] as $widget ) {

		// Get this widget object's settings
		$widget_obj = $wp_registered_widgets[ $widget ]['callback'][0];
		$widget_nr  = $wp_registered_widgets[ $widget ]['params'][0]['number'];
		$settings   = $widget_obj->get_settings();

		if ( $settings && isset( $settings[ $widget_nr ] ) ) {
			$widget = $settings[ $widget_nr ];

			// Skip when setting is not found
			if ( ! isset( $widget[ $key ] ) )
				continue;

			// Skip when value does not equal
			if ( null !== $value && $value !== $widget[ $key ] )
				continue;

			// Increment
			$count++;
		}
	}

	return $count;
}

/** Links ******************************************************************/

/**
 * Append a 'paged' number to the given url
 *
 * @see get_pagenum_link()
 *
 * @since 1.0.0
 *
 * @param string $url The url to append to
 * @param int $pagenum Optional. Page ID.
 * @param bool $front Optional. Whether the link is for the frontend. Defaults to true.
 * @param bool $escape Optional. Whether to escape the url before returning. Defaults to true.
 * @return string The link url for the given page number.
 */
function zeta_pagenum_link( $url, $pagenum = 2, $front = true, $escape = true ) {
	global $wp_rewrite;

	$pagenum = (int) $pagenum;

	// Strip domain
	$url     = str_replace( home_url(), '', $url );
	$request = remove_query_arg( 'paged', $url );

	$home_root = parse_url(home_url());
	$home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
	$home_root = preg_quote( $home_root, '|' );

	$request = preg_replace('|^'. $home_root . '|i', '', $request);
	$request = preg_replace('|^/+|', '', $request);

	if ( !$wp_rewrite->using_permalinks() || ( is_admin() && ! $front ) ) {
		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $pagenum > 1 ) {
			$result = add_query_arg( 'paged', $pagenum, $base . $request );
		} else {
			$result = $base . $request;
		}
	} else {
		$qs_regex = '|\?.*?$|';
		preg_match( $qs_regex, $request, $qs_match );

		if ( !empty( $qs_match[0] ) ) {
			$query_string = $qs_match[0];
			$request = preg_replace( $qs_regex, '', $request );
		} else {
			$query_string = '';
		}

		$request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request);
		$request = preg_replace( '|^' . preg_quote( $wp_rewrite->index, '|' ) . '|i', '', $request);
		$request = ltrim($request, '/');

		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) ) {
			$base .= $wp_rewrite->index . '/';
		}

		if ( $pagenum > 1 ) {
			$request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
		}

		$result = $base . $request . $query_string;
	}

	/**
	 * Filter the page number link for the current request.
	 *
	 * @since 2.5.0
	 *
	 * @param string $result The page number link.
	 */
	$result = apply_filters( 'get_pagenum_link', $result );

	if ( $escape )
		return esc_url( $result );
	else
		return esc_url_raw( $result );
}
