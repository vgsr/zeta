<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Zeta
 */

/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @param bool|string $title Optional. Link title attribute. Defaults to 'Posted by {author}'.
 */
function zeta_posted_on( $title = '' ) {
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

	$link_html = is_string( $title ) ? '<a href="%1$s" rel="bookmark" title="%3$s">%2$s</a>' : '<a href="%1$s" rel="bookmark">%2$s</a>';
	$posted_on = sprintf( $link_html,
		esc_url( get_permalink() ),
		$time_string,
		$title ? $title : sprintf( esc_attr_x( 'Posted by %s', 'post author', 'zeta' ), get_the_author() )
	);

	echo '<span class="posted-on">' . sprintf( _x( '<span class="screen-reader-text">Posted on </span>%s', 'post date', 'zeta' ), $posted_on ) . '</span>';
}

/**
 * Prints HTML with meta information below the entry title.
 */
function zeta_entry_meta() {

	// Search
	if ( is_search() ) {
		add_action( 'zeta_entry_meta', 'zeta_post_type', 6 );
	}

	// Posts
	if ( 'post' == get_post_type() ) {
		add_action( 'zeta_entry_meta', 'zeta_post_format_link', 8  );
		add_action( 'zeta_entry_meta', 'zeta_posted_on',        12 );
	}

	/**
	 * Fires when printing the page's meta in the header
	 *
	 * Please wrap separate footer elements in `<span>`.
	 *
	 * @since 1.0.0
	 */
	do_action( 'zeta_entry_meta' );

	// Comments
	if ( ! is_single() && ! post_password_required() && get_comments_number() ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', 'zeta' ), __( '1 Comment', 'zeta' ), __( '% Comments', 'zeta' ) );
		echo '</span>';
	}

	// Edit link
	edit_post_link( __( 'Edit', 'zeta' ), '<span class="edit-link">', '</span>' );
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function zeta_entry_footer() {

	// Show category and tag text for posts
	if ( 'post' == get_post_type() && is_single() ) {

		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'zeta' ) );
		if ( $categories_list && zeta_categorized_blog() ) {
			printf( '<span class="cat-links">' . __( 'Posted in %s', 'zeta' ) . '</span>', $categories_list );
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'zeta' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . __( 'Tagged %s', 'zeta' ) . '</span>', $tags_list );
		}
	}

	/**
	 * Fires when printing the page's details in the footer
	 *
	 * Please wrap separate footer elements in `<span>`.
	 *
	 * @since 1.0.0
	 */
	do_action( 'zeta_entry_footer' );
}

/**
 * Run a dedicated hook before the page's content
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'zeta_before_content'
 */
function zeta_before_content() {
	do_action( 'zeta_before_content' );
}

/**
 * Output the context-aware posts navigation markup
 *
 * @since 1.0.0
 */
function zeta_the_posts_navigation() {

	// Econozel pages
	if ( function_exists( 'econozel' ) && is_econozel() ) {
		econozel_the_posts_navigation();

	// Event Organiser pages
	} elseif ( 'event' === get_post_type() ) {
		zeta_event_organiser_the_posts_navigation();

	// Default
	} else {
		the_posts_navigation();
	}
}

/**
 * Run a dedicated hook after the page's content
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'zeta_after_content'
 */
function zeta_after_content() {
	do_action( 'zeta_after_content' );
}

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

if ( ! function_exists( 'zeta_post_format_link' ) ) :
/**
 * Prints HTML for the post's post format link
 *
 * @since 1.0.0
 */
function zeta_post_format_link() {
	if ( ( $format = get_post_format() ) && ! is_tax( 'post_format', "post-format-{$format}" ) ) {
		printf( '<span class="post-format %1$s"><a href="%2$s">%3$s</a></span>',
			esc_attr( $format ),
			get_post_format_link( $format ),
			get_post_format_string( $format )
		);
	}
}
endif;

if ( ! function_exists( 'zeta_post_type' ) ) :
/**
 * Prints HTML for the post's post type
 *
 * @since 1.0.0
 */
function zeta_post_type() {
	$type = get_post_type();
	printf( '<span class="post-type %1$s">%2$s</span>', $type, get_post_type_object( $type )->labels->singular_name );
}
endif;

/**
 * Return a modified version of the page menu as a nav menu fallback
 *
 * This should bring the HTML output of `wp_page_menu()` in sync with
 * that of `wp_nav_menu()`.
 *
 * @since 1.0.0
 *
 * @param array $args Menu arguments of `wp_nav_menu()`
 * @return string Page menu
 */
function zeta_page_menu( $args ) {

	// Force define arguments
	$args['before']     = sprintf( '<ul class="%s">', $args['menu_class'] );
	$args['menu_class'] = $args['container_class'];

	return wp_page_menu( $args );
}

/**
 * Return whether there are more posts in the loop.
 *
 * Mimics `have_posts()`, but without the rewinding.
 *
 * @since 1.0.0
 *
 * @param $query bool|WP_Query Optional. Defaults to the global main query
 * @return bool Loop has posts
 */
function zeta_has_posts( $query = false ) {

	// Default to the global main query
	if ( ! $query || ! is_a( $query, 'WP_Query' ) ) {
		$query = $GLOBALS['wp_query'];
	}

	return ( $query->current_post + 1 < $query->post_count );
}

/**
 * Display collection of site tools for in the site header
 *
 * @since 1.0.0
 */
function zeta_tools_nav() {
	$tools   = zeta_get_site_tools(); 
	$toggled = false; ?>

	<div class="tools-nav-container">
		<ul class="tools-nav">
		<?php foreach ( $tools as $tool_id => $tool ) {
			$tool_id = esc_attr( $tool_id );

			$class = array( "$tool_id-toggle" );
			if ( isset( $tool['class'] ) ) {
				$class[] = trim( esc_attr( $tool['class'] ) );
			}
			if ( ! $toggled && isset( $tool['toggle'] ) && $tool['toggle'] ) {
				$class[] = 'toggled';
				$toggled = true;
			}

			printf( '<li class="%1$s" data-tool="%2$s"><a href="%3$s"><span class="screen-reader-text">%4$s</span></a></li>', 
				implode( ' ', $class ),
				$tool_id,
				esc_url( $tool['url'] ),
				esc_html( $tool['label'] ) 
			);
		} ?>
		</ul>
	</div>

	<?php
}

/**
 * Display the tools content
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'site_{$tool_id}_tool_content'
 */
function zeta_tools_content() {
	$tools   = zeta_get_site_tools();
	$toggled = false;

	foreach ( $tools as $tool_id => $tool ) {
		$style = '';
		if ( ! $toggled && isset( $tool['toggle'] ) && $tool['toggle'] ) {
			$style = ' style="display:block;"';
			$toggled = true;
		}

		printf( '<div id="site-tool-%1$s" class="site-tool" %2$s>', esc_attr( $tool_id ), $style );

		switch ( $tool_id ) {

			// Search form
			case 'search' :
				the_widget( 'WP_Widget_Search', array(), array( 'before_widget' => '', 'after_widget' => '' ) );
				break;

			// Login form
			case 'login' :

				// Only for logging in
				if ( ! is_user_logged_in() ) : ?>

				<form name="wp-login-form" id="wp-login-widget-form" class="standard-form" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" />

					<label for="wp-login-widget-user-login"><?php _e( 'Username', 'zeta' ); ?></label>
					<input type="text" name="log" id="wp-login-widget-user-login" class="input" value="" placeholder="<?php esc_attr_e( 'Username', 'zeta' ); ?>" />

					<label for="wp-login-widget-user-pass"><?php _e( 'Password', 'zeta' ); ?></label>
					<input type="password" name="pwd" id="wp-login-widget-user-pass" class="input" value="" placeholder="<?php esc_attr_e( 'Password', 'zeta' ); ?>" />

					<input type="submit" name="wp-submit" id="wp-login-widget-submit" value="<?php esc_attr_e( 'Log In', 'zeta' ); ?>" />
					<div class="lostpassword"><?php printf( '<a href="%s">%s</a>', esc_url( network_site_url( 'wp-login.php?action=lostpassword', 'login_post' ) ), __( 'Lost Password?', 'zeta' ) ); ?></div>

					<?php if ( function_exists( 'buddypress' ) && bp_get_signup_allowed() ) : ?>
						<span class="wp-login-widget-register-link"><?php printf( __( '<a href="%s" title="Register for a new account">Register</a>', 'zeta' ), bp_get_signup_page() ); ?></span>
					<?php endif; ?>
				</form>

				<?php endif;

				break;

			// Custom tool
			default :
				do_action( "site_{$tool_id}_tool_content" );
				break;
		}
		echo '</div>';
	}
}

	/**
	 * Return the registered site tools
	 * 
	 * @since 1.0.0
	 * 
	 * @uses apply_filters() Calls 'site_tools'
	 *
	 * @return array Site tools collection
	 */
	function zeta_get_site_tools() {
		$loggedin = is_user_logged_in();

		return (array) apply_filters( 'site_tools', array(

			// Site search tool
			'search' => array(
				'label'  => __( 'Search the site', 'zeta' ),
				'url'    => '#',
				'toggle' => is_search() || is_404()
			),

			// Login/out tool
			'login'  => array(
				'label'  => $loggedin ? __( 'Log Out', 'zeta' ) : __( 'Log In', 'zeta' ),
				'url'    => $loggedin ? wp_logout_url( $_SERVER['REQUEST_URI'] ) : wp_login_url( $_SERVER['REQUEST_URI'] ),
				'class'  => $loggedin ? 'no-toggle' : '',
			)
		) );
	}

/**
 * Display the background slider
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'zeta_background_slider_slide'
 * @uses apply_filters() Calls 'zeta_background_slider_slides'
 */
function zeta_background_slider() {
	/**
	 * Images typically are served as arrays containing the following elements
	 *  - src    The image source to display as background
	 *  - post_id Optional. The image's linked post ID.
	 *  - href    Optional. The image anchor url. Defaults to the post permalink
	 *  - title   Optional. The title of the image/post. Defaults to the post title or the image's title.
	 *  - byline  Optional. The contents for the subtitle of the image/post. Requires a value for the `title` param
	 */

	// Define images, posts, and slides collection
	$images = $posts = $slides = array();

	// Define the slider's required image dimensions
	$image_size = array( 1200, 900 );

	//
	// Get images (and posts) of the current page
	// 

	// This is a single post/page/etc.
	if ( is_singular() ) {
		$images = zeta_get_post_images( get_queried_object(), $image_size );

	// This is a post collection
	} elseif ( ( is_home() || is_archive() || is_search() ) && have_posts() ) {
		global $wp_query;

		// Walk all queried posts
		$posts = wp_list_pluck( $wp_query->posts, 'ID' );
		foreach ( $posts as $post_id ) {

			// Get each post's first usable image
			$images[] = zeta_get_first_post_image( $post_id, $image_size );
		}

	// This is the front page or a not-found page
	} elseif ( is_front_page() || is_404() ) {

		// What to do here? Latest posts that have featured images, featured posts, custom front page gallery?
		// The five latest posts that have featured images
		$query = new WP_Query( array( 
			'posts_per_page' => 5,
			'fields'         => 'ids',
			'post_type'      => 'post',
			'meta_key'       => '_thumbnail_id',
			'meta_compare'   => 'EXISTS',
		) );

		// Get the post IDs from the query
		$posts = $query->posts;

		// Walk all found posts
		foreach ( $posts as $post_id ) {

			// Get each post's first usable image
			$images[] = zeta_get_first_post_image( $post_id, $image_size );
		}
	}

	$images = array_filter( array_values( $images ) );

	// Default to Background Image(s)
	if ( empty( $images ) ) {
		$images = get_theme_mod( 'background_image', array() );
		$posts = array();

		if ( ! empty( $images ) ) {
			shuffle( $images );

			// Get only a single image
			if ( get_theme_mod( 'background_image_single' ) ) {
				$images = array_slice( $images, 0, 1 );
			}
		} else {
			$images = array();
		}
	}

	//
	// Process images with post data
	// 

	// Walk all found images
	foreach ( $images as $i => $image ) {

		// Skip slide when there is no image
		if ( ! $image )
			continue;

		// Define slide details
		$slide = array(
			'post_id' => false,
			'att_id'  => false,
			'src'     => $image,
			'href'    => false,
			'title'   => false,
			'byline'  => false
		);

		// Get data from the associated post
		if ( $posts ) {

			// Skip slide when the post does not exist
			if ( ! $post = get_post( $posts[ $i ] ) )
				continue;

			// Setup post data
			$slide['post_id'] = $post->ID;
			$slide['href']    = get_permalink( $post->ID );
			$slide['title']   = get_the_title( $post->ID );

			// 'Posted on' only for posts
			if ( 'post' == $post->post_type ) {
				$slide['byline']  = sprintf( __( '<span class="screen-reader-text">Posted on </span>%s', 'zeta' ), get_the_date( '', $post->ID ) );
			}
		}

		// Get data from the image attachment
		if ( is_numeric( $image ) ) {
			$slide['att_id'] = (int) $image;
			$_image = wp_get_attachment_image_src( $image, $image_size );

			// Skip slide when image isn't found
			if ( ! $_image )
				continue;

			$slide['src'] = $_image[0];

			if ( ! $posts ) {
				$metadata = wp_get_attachment_metadata( $image );

				// Get original image file link
				if ( apply_filters( 'zeta_background_slider_image_url', false, $image ) ) {
					$upload_dir = wp_upload_dir();
					$slide['href'] = trailingslashit( $upload_dir['baseurl'] ) . $metadata['file'];
				}

				// Get attachment title
				if ( apply_filters( 'zeta_background_slider_image_title', false, $image ) 
					&& ( $att_title = get_the_title( $image ) ) && ! empty( $att_title ) ) {
					$slide['title'] = $att_title;
				}

				// Get attachment details
				if ( apply_filters( 'zeta_background_slider_image_credits', false, $image ) 
					&& ! empty( $metadata['image_meta']['credit'] ) ) {
					$slide['byline'] = sprintf( __( 'Created by %s', 'zeta' ), $metadata['image_meta']['credit'] );
				}
			}
		}

		// If we made it this far, add to slides collection
		$slides[] = $slide;
	}

	// When no valid images were found, default to the theme's default background
	if ( empty( $slides ) ) {
		$slides[] = array( 
			'post_id' => false,
			'att_id'  => false,
			'src'     => 'https://source.unsplash.com/random/1600x900/', // Unsplash API
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
		$slide = '<' . $tag . ' class="slide-inner" style="background-image: url(' . esc_url( $args['src'] ) . ');"';

		// Add link to the element
		if ( 'a' === $tag ) {
			$slide .= ' href="' . esc_attr( $args['href'] ) . '"';
		}
		$slide .= '>';

		// Handle post/image title
		if ( $args['title'] ) {
			$slide .= '<header class="slide-details"><h2>' . esc_html( $args['title'] ) . '</h2>';

			// Append byline
			if ( $args['byline'] ) {
				$slide .= '<span class="byline">' . $args['byline'] . '</span>';
			}

			$slide .= '</header>';
		}

		// Display image's associated users
		if ( $args['att_id'] ) {
			$slide .= zeta_media_users( $args['att_id'], false );
		}

		// Close image container
		$slide .= '</' . $tag . '>';

		// Filter and add slide content to the slides collection
		$slides[ $i ] = apply_filters( 'zeta_background_slider_slide', $slide, $args, $tag, $i );
	}

	// Filter all the slides
	$slides      = apply_filters( 'zeta_background_slider_slides', $slides ); 
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

				// Define slider and get the slider object
				var slider = $( '.flexslider' ).flexslider({
					controlNav: false,
					start: function( slider ) {

						// The loading class prevents a white flash on slider start
						// see https://github.com/woothemes/FlexSlider/issues/848#issuecomment-42573918
						slider.removeClass( 'loading' );
					}
				}).data( 'flexslider' );

				<?php if ( is_singular() ) : ?>

				// Link gallery thumbs to slides
				var $galleryItems = $( '.entry-content .gallery-item a' );
				$galleryItems.on( 'click', function( e ) {

					// Get the position of the selected thumb
					// NOTE: Here we assume two things. 1) All gallery items have 
					// corresponding slides, which means the thumbs' orginals are 
					// large enough for the slider AND 2) No other non-gallery-image 
					// slides exist in the slider. Meaning: gallery thumbs and slides
					// are a perfect 1-on-1 match.
					var target = $galleryItems.index( $(this) );

					// Show the targeted slide
					slider.flexAnimate( target, true );

					// Prevent linking
					e.preventDefault();
				});

				<?php endif; ?>
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
 * Append a slide with a map
 *
 * @since 1.0.0
 *
 * @param array $slides
 * @return array
 */
function zeta_map_slide( $slides ) {

	// The Mapbox details
	$mapbox_user  = 'mmcievgsr';
	$mapbox_token = 'pk.eyJ1IjoibW1jaWV2Z3NyIiwiYSI6ImFGbkd0R3cifQ.TMV3FZNQ3aBGmYaQ0MuT5Q';
	$mapbox_map   = 'ljkn6i7o';

	// Define the map's coordinates
	$zoom  = 15;
	$xtile = 51.915;
	$ytile = 4.440;

	// Open Street Map
	ob_start(); ?>

	<script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.6/mapbox.js'></script>
	<link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.6/mapbox.css' rel='stylesheet' />
	<div id="the-map" class="slide-inner"></div>

	<script>
		L.mapbox.accessToken = '<?php echo $mapbox_token; ?>';
		var map = L.mapbox.map( 'the-map', <?php echo "'$mapbox_user.$mapbox_map'"; ?>, {
		    	zoomControl: false
		    }).setView( <?php echo "[ $xtile, $ytile ], $zoom"; ?> ),
		    layer = L.mapbox.featureLayer().addTo( map );
		
		// Add data points
		layer.setGeoJSON( [<?php echo file_get_contents( get_template_directory_uri() . '/assets/js/demo.geojson' ); ?>] );

		// Redraw zoom control
		new L.Control.Zoom({ position: 'bottomleft' }).addTo( map );
	</script>

	<?php

	$slides = array( ob_get_clean() );

	return $slides;
}
// add_filter( 'zeta_background_slider_slides', 'zeta_map_slide' );

/**
 * Display or return the media item's associated users
 *
 * @since 1.0.0
 *
 * @param int $post_id Attachment ID
 * @param bool $echo Optional. Whether to output the content
 */
function zeta_media_users( $post_id, $echo = true ) {
	global $members_template;

	// Bail when BP or P2P is not active
	if ( ! did_action( 'bp_init' ) || ! did_action( 'p2p_init' ) )
		return;
	
	// Bail when no users are found
	if ( ! $users = get_users( array(
		'fields'          => 'ids',
		'connected_type'  => 'user_media',
		'connected_items' => $post_id,
		'exclude'         => bp_displayed_user_id()
	) ) )
		return;

	// Keep the original template global
	$old_members_template = $members_template;

	// Start output buffer
	ob_start();

	// Query associated members
	if ( bp_has_members( array(
		'type'    => '', // Query $wpdb->users, order by ID
		'include' => $users,
	) ) ) : ?>

		<div class="media-users">
			<ul class="bp-item-list">
				<?php while ( bp_members() ) : bp_the_member(); ?>
				<li <?php bp_member_class( array( 'member' ) ); ?>>
					<div class="item-avatar">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
					</div>

					<div class="item">
						<div class="item-title">
							<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
						</div>
					</div>
				</li>
				<?php endwhile; ?>
			</ul>
		</div>

	<?php endif;

	// End output buffer
	$retval = ob_get_clean();

	// Reset template global
	$members_template = $old_members_template;

	if ( $echo ) {
		echo $retval;
	} else {
		return $retval;
	}
}

/**
 * Output a modified version of the TinyMCE content editor
 *
 * @since 1.0.0
 *
 * @param string $content Content to edit
 * @param string $textarea_id ID value for textarea element
 * @param array $args Editor arguments
 */
function zeta_editor( $content, $textarea_id, $args = array() ) {

	// Enable additional TinyMCE plugins before outputting the editor
	add_filter( 'tiny_mce_plugins',   'zeta_get_tiny_mce_plugins'   );
	add_filter( 'teeny_mce_plugins',  'zeta_get_tiny_mce_plugins'   );
	add_filter( 'teeny_mce_buttons',  'zeta_get_teeny_mce_buttons'  );
	add_filter( 'quicktags_settings', 'zeta_get_quicktags_settings' );

	// Output the editor
	wp_editor( $content, $textarea_id, wp_parse_args( $args, array(
		'media_buttons' => false,
		'quicktags'     => false,
		'textarea_rows' => '12',
	) ) );

	// Remove additional TinyMCE plugins after outputting the editor
	remove_filter( 'tiny_mce_plugins',   'zeta_get_tiny_mce_plugins'   );
	remove_filter( 'teeny_mce_plugins',  'zeta_get_tiny_mce_plugins'   );
	remove_filter( 'teeny_mce_buttons',  'zeta_get_teeny_mce_buttons'  );
	remove_filter( 'quicktags_settings', 'zeta_get_quicktags_settings' );
}

/**
 * Edit TinyMCE plugins to match core behaviour
 *
 * @since 1.0.0
 *
 * @see bbPress
 *
 * @param array $plugins
 * @see tiny_mce_plugins, teeny_mce_plugins
 * @return array
 */
function zeta_get_tiny_mce_plugins( $plugins = array() ) {

	// Unset fullscreen
	foreach ( $plugins as $key => $value ) {
		if ( 'fullscreen' === $value ) {
			unset( $plugins[$key] );
			break;
		}
	}

	// Add the tabfocus plugin
	$plugins[] = 'tabfocus';

	return $plugins;
}

/**
 * Edit TeenyMCE buttons to match allowedtags
 *
 * @since 1.0.0
 *
 * @see bbPress
 *
 * @param array $buttons
 * @see teeny_mce_buttons
 * @return array
 */
function zeta_get_teeny_mce_buttons( $buttons = array() ) {

	// Remove some buttons from TeenyMCE
	$buttons = array_diff( $buttons, array(
		'underline',
		'justifyleft',
		'justifycenter',
		'justifyright'
	) );

	// Images
	array_push( $buttons, 'image' );

	return $buttons;
}

/**
 * Edit TinyMCE quicktags buttons to match allowedtags
 *
 * @since 1.0.0
 *
 * @see bbPress
 *
 * @param array $buttons
 * @see quicktags_settings
 * @return array Quicktags settings
 */
function zeta_get_quicktags_settings( $settings = array() ) {

	// Get buttons out of settings
	$buttons_array = explode( ',', $settings['buttons'] );

	// Diff the ones we don't want out
	$buttons = array_diff( $buttons_array, array(
		'ins',
		'more',
		'spell'
	) );

	// Put them back into a string in the $settings array
	$settings['buttons'] = implode( ',', $buttons );

	return $settings;
}
