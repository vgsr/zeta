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
 * @since 1.0.0
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function zeta_body_classes( $classes ) {

	// Adds a more distinctive class for the front page
	if ( 'page' == get_option( 'show_on_front') && get_option( 'page_on_front' ) == get_the_ID() ) {
		$classes[] = 'front-page';
	}

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
		if ( 'sidebar-content' == $layout && ( ! function_exists( 'buddypress' ) || ! is_buddypress() ) ) {
			$classes[] = 'sidebar-content';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'zeta_body_classes' );

/**
 * Modify the init args for the TinyMCE editor
 *
 * @since 1.0.0
 *
 * @param array $mce Editor args
 * @return array Editor args
 */
function zeta_editor_body_classes( $mce ) {

	// For the front page, add a class to the editor body
	if ( zeta_is_static_front_page() ) {
		$mce['body_class'] .= ' front-page';
	}

	// Restrict available block formats: p, h3, h4, h5, blockquote, and pre
	$mce['block_formats'] = 'Paragraph=p;Heading 3=h3;Heading 4=h4;Heading 5=h5;Blockquote=blockquote;Pre=pre';

	return $mce;
}
add_filter( 'teeny_mce_before_init', 'zeta_editor_body_classes' );
add_filter( 'tiny_mce_before_init',  'zeta_editor_body_classes' );

/**
 * Return whether the page is the static front page
 *
 * @since 1.0.0
 *
 * @param WP_Post|int $post Optional. Post object or ID. Defaults to the current page.
 * @return bool Is this the static front page?
 */
function zeta_is_static_front_page( $post = false ) {
	if ( $post ) {
		$post     = get_post( $post );
		$is_front = $post && 'page' == get_option( 'show_on_front') && get_option( 'page_on_front' ) == $post->ID;
	} else {
		$is_front = is_front_page() && ! is_home();
	}

	return $is_front;
}

/**
 * Output a breadcrumbs trail before the page's content
 *
 * @since 1.0.0
 */
function zeta_breadcrumbs() {

	// Bail when on the site's front page
	if ( is_front_page() )
		return;

	// Using Yoast SEO
	if ( function_exists( 'yoast_breadcrumb' ) ) {

		// Modify crumbs
		add_filter( 'wpseo_breadcrumb_links',     'zeta_wpseo_breadcrumb_links' );
		add_filter( 'wpseo_breadcrumb_separator', '__return_empty_string'       );

		// Output crumbs
		yoast_breadcrumb( '<div id="breadcrumb" class="yoast-breadcrumb">', '</div>' );

		// Undo modify crumbs
		remove_filter( 'wpseo_breadcrumb_links',     'zeta_wpseo_breadcrumb_links' );
		remove_filter( 'wpseo_breadcrumb_separator', '__return_empty_string'       );

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
add_action( 'zeta_before_content', 'zeta_breadcrumbs', 6 );

	/**
	 * Modify the crumbs collection of Yoast SEO
	 *
	 * @since 1.0.0
	 *
	 * @param array $crumbs Crumbs
	 * @return array Crumbs
	 */
	function zeta_wpseo_breadcrumb_links( $crumbs ) {

		// Walk all crumbs
		foreach ( $crumbs as $k => $crumb ) {

			// Wrap the Home crumb in screen-reader-text
			if ( WPSEO_Utils::home_url() === $crumb['url'] ) {
				$crumbs[ $k ]['text'] = '<span class="screen-reader-text">' . $crumb['text'] . '</span>';
				break;
			}
		}

		return $crumbs;
	}

/**
 * Modify the excerpt more text
 *
 * @since 1.0.0
 *
 * @param string $more
 * @return string Excerpt more
 */
function zeta_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more',           'zeta_excerpt_more' );
add_filter( 'bp_excerpt_append_text', 'zeta_excerpt_more' );

/** Comments ***************************************************************/

/**
 * Modify the post's comment content
 *
 * @since 1.0.0
 *
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

/**
 * Modify the default comment form arguments
 *
 * @since 1.0.0
 *
 * @param  array $defaults Default comment form arguments
 * @return array Default comment form arguments
 */
function zeta_comment_form_defaults( $defaults ) {

	// Define local variables
	$req = get_option( 'require_name_email' );
	$name_map = array(
		'author' => __( 'Name' ) . ( $req ? ' *' : '' ),
		'email'  => __( 'Email' ) . ( $req ? ' *' : '' ),
		'url'    => __( 'Website' )
	);

	// Modify fields
	foreach ( $defaults['fields'] as $name => $field ) {

		// Skip cookies checkbox
		if ( 'cookies' === $name )
			continue;

		// Add screen reader class
		$field = str_replace( '<label', '<label class="screen-reader-text-small-screen"', $field );

		// Add input placeholder
		if ( isset( $name_map[ $name ] ) ) {
			$field = str_replace( '<input', sprintf( '<input placeholder="%s"', $name_map[ $name ] ), $field );
		}

		$defaults['fields'][ $name ] = $field;
	}

	$defaults = wp_parse_args( array(
		'logged_in_as'  => '<div class="comment-reply-avatar">' . get_avatar( get_current_user_id(), 50 ) . '</div>',
		'comment_field' => '<div class="comment-form-comment"><label for="comment" class="screen-reader-text">' . _x( 'Comment', 'noun' ) . '</label> <textarea id="comment" name="comment" aria-required="true" required="required" placeholder="' . ( is_user_logged_in() ? sprintf( __( 'Reply as %s', 'zeta' ), get_userdata( get_current_user_id() )->display_name ) : '' ) . '" rows="' . ( is_user_logged_in() ? '1' : '3' ) . '"></textarea></div>',
	), $defaults );

	// For logged-in users
	if ( is_user_logged_in() ) {
		$defaults = wp_parse_args( array(
			'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title screen-reader-text">', // Hide reply title
			'submit_field'       => '<div class="form-submit">%1$s %2$s</div>'
		), $defaults );
	}

	return $defaults;
}
add_filter( 'comment_form_defaults', 'zeta_comment_form_defaults' );

/**
 * Append markup to the comment form
 *
 * @since 1.0.0
 *
 * @param  int $post_id Current post ID
 */
function zeta_comment_form( $post_id ) {

	// Bail when there is no logged-in user
	if ( ! is_user_logged_in() )
		return;

	// Unhook temp filter. See ../comments.php
	remove_filter( 'cancel_comment_reply_link', '__return_empty_string' );

	/**
	 * Hack to add markup _after_ the comment form.
	 *
	 * There are no hooks provided to insert markup after the comment form, so
	 * we're using the last hook for appending markup within the comment form.
	 * Here we manually close the comment form and add our markup. The remaining
	 * closing form tag in `comment_form()` will be preceded by a hidden form tag
	 * which has no other purpose than to provide a matching opening tag.
	 */

	?>
	</form>

	<div class="comment-actions">
		<span class="cancel-reply"><?php cancel_comment_reply_link( __( 'Cancel', 'zeta' ) ); ?></span>
	</div>

	<form class="hidden"><?php /* Nothing happens here */

	// Rehook temp filter. See ../comments.php
	add_filter( 'cancel_comment_reply_link', '__return_empty_string' );
}
add_action( 'comment_form', 'zeta_comment_form', 99 );

/** Search *****************************************************************/

/**
 * Append a context filter element to the search form
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'zeta_search_contexts'
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
		if ( zeta_check_access() ) {
			$contexts['bp-members'] = __( 'Members', 'zeta' );
		}

		// Search groups
		if ( zeta_check_access() && bp_is_active( 'groups' ) && 0 < groups_get_total_group_count() ) {
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
 * @uses apply_filters() Calls 'zeta_search_context_redirect'
 */
function zeta_search_context_redirect() {

	// Bail when this is not a search request and no context is provided
	if ( ! is_search() || ! isset( $_GET['context'] ) )
		return;

	// Define local variable(s)
	$location = false;
	$context  = esc_attr( $_GET['context'] );
	$s        = esc_attr( $_GET['s'] );
	$bp       = function_exists( 'buddypress' ) ? buddypress() : false;

	if ( $bp ) {
		$page_ids = bp_core_get_directory_page_ids( 'all' );
	}

	switch ( $context ) {
		case 'bp-members' :
			if ( $bp && zeta_check_access() ) {
				$location = add_query_arg( 's', $s, get_permalink( $page_ids['members'] ) ); // Members index page
			}
			break;

		case 'bp-groups' :
			if ( $bp && zeta_check_access() && bp_is_active( 'groups' ) ) {
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
 * @param WP_Widget $widget
 * @param string $return Form output markup
 * @param array $instance Widget settings
 */
function zeta_widget_form( $widget, $return, $instance ) {

	// @todo Find a way to display this only on the Main Sidebar's widgets ?>

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
