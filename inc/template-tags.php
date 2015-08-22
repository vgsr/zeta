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
 * Display collection of site tools for in the site header
 *
 * @since 1.0.0
 *
 * @uses zeta_get_site_tools()
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
 * @uses zeta_get_site_tools()
 * @uses the_widget()
 * @uses do_action() Calls 'site_{$tool_id}_tool_content'
 */
function zeta_tools() {
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

					<div class="forgetmenot"><label><input name="rememberme" type="checkbox" id="wp-login-widget-rememberme" value="forever" /> <?php _e( 'Remember Me', 'zeta' ); ?></label></div>

					<input type="submit" name="wp-submit" id="wp-login-widget-submit" value="<?php esc_attr_e( 'Log In', 'zeta' ); ?>" />

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
	 * @uses is_user_logged_in()
	 * @uses wp_logout_url()
	 * @uses wp_login_url()
	 *
	 * @return array Site tools collection
	 */
	function zeta_get_site_tools() {
		return (array) apply_filters( 'site_tools', array(
			'search' => array(
				'label'  => __( 'Search the site', 'zeta' ),
				'url'    => '#',
				'toggle' => is_search() || is_404()
			),
			'login'  => array(
				'label'  => is_user_logged_in() ? __( 'Log Out', 'zeta' ) : __( 'Log In', 'zeta' ),
				'url'    => is_user_logged_in() ? wp_logout_url( $_SERVER['REQUEST_URI'] ) : wp_login_url( $_SERVER['REQUEST_URI'] ),
				'class'  => is_user_logged_in() ? 'no-toggle' : '',
			)
		) );
	}

/**
 * Display the background slider
 *
 * @since 1.0.0
 *
 * @uses is_singular()
 * @uses get_queried_object()
 * @uses zeta_get_post_images()
 * @uses zeta_get_first_post_image()
 * @uses apply_filters() Calls 'zeta_background_slider_slide'
 * @uses apply_filters() Calls 'zeta_background_slider_slides'
 * @uses wp_enqueue_script()
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
		if ( ! empty( $background ) ) {
			shuffle( $images );

			// Get a single image when not rotating
			if ( ! get_theme_mod( 'background_image_rotate' ) ) {
				$images = array_slice( $images, 0, 1 );
			}
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
				$slide['byline']  = sprintf( __( 'Posted on %s', 'zeta' ), get_the_date( '', $post->ID ) );
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
			'src'     => get_template_directory_uri() . '/images/default-background.jpg',
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
				$slide .= '<span class="byline">' . esc_html( $args['byline'] ) . '</span>';
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
 * @uses array $slides
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
		layer.setGeoJSON( [<?php echo file_get_contents( get_template_directory_uri() . '/js/demo.geojson' ); ?>] );

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
 * @uses bp_member_permalink()
 * @uses bp_member_name()
 * @uses bp_member_avatar()
 * 
 * @param int $post_id Attachment ID
 * @param bool $echo Optional. Whether to output the content
 */
function zeta_media_users( $post_id, $echo = true ) {

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

	if ( ! $echo ) {
		ob_start();
	}

	// Query associated members
	if ( bp_has_members( array(
		'type'    => '', // Order handled by vgsr plugin
		'include' => $users,
	) ) ) {
		?>
		<div class="media-users">
			<ul>
			<?php while ( bp_members() ) : bp_the_member(); ?>
				<li><a href="<?php echo esc_url( bp_get_member_permalink() ); ?>" data-title="<?php echo esc_attr( bp_get_member_name() ); ?>"><?php bp_member_avatar(); ?></a></li>
			<?php endwhile; ?>
			</ul>
		</div>
		<?php
	}

	if ( ! $echo ) {
		return ob_get_clean();
	}
}
