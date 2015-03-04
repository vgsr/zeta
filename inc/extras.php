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
 * Modify the allowed media types to search for in content
 *
 * Filter is available since WP 4.2.
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
