<?php

/**
 * Zeta functions and definitions
 *
 * @package Zeta
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 569; /* pixels */
}

if ( ! function_exists( 'zeta_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function zeta_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on zeta, use a find and replace
	 * to change 'zeta' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'zeta', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Declare support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'zeta' ),
		'social'  => __( 'Social Links Menu', 'zeta' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Declare support for Post Formats.
	 *
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'gallery' // See content-gallery.php for the template part in The Loop
	) );

	/*
	 * Declare support for the Customizer's selective refresh for widgets.
	 */
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * Declare support for the Event Organiser plugin.
	 * By adding theme support, we declare that this theme handles page templates.
	 */
	add_theme_support( 'event-organiser' );

	/*
	 * Declare support for Yoast SEO's breadcrumbs.
	 */
	add_theme_support( 'yoast-seo-breadcrumbs' );

	/*
	 * Register styles for the TinyMCE editor.
	 */
	add_editor_style( array( 'editor-style.css', zeta_fonts_url() ) );

	/*
	 * Register theme image sizes
	 */
	zeta_add_image_sizes();

	/**
	 * Register theme classes
	 */
	require_once( get_template_directory() . '/inc/classes/class-zeta-walker-comment.php' );
}
endif; // zeta_setup
add_action( 'after_setup_theme', 'zeta_setup' );

/**
 * Regirster additional image sizes
 *
 * @since 1.0.0
 */
function zeta_add_image_sizes() {

	// Default featured image size
	set_post_thumbnail_size( 300, 300 );

	// Header slider image size
	add_image_size( 'zeta-background-slider', 9999, 900 );
}

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 *
 * @since 1.0.0
 *
 * @uses register_sidebar()
 */
function zeta_widgets_init() {

	// Main sidebar
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'zeta' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	// Front Page sidebar
	register_sidebar( array(
		'name'          => __( 'Front Page', 'zeta' ),
		'id'            => 'front-page-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	// Footer sidebar
	register_sidebar( array(
		'name'          => __( 'Footer', 'zeta' ),
		'id'            => 'footer-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'zeta_widgets_init' );

if ( ! function_exists( 'zeta_fonts_url' ) ) :
/**
 * Register Google fonts for Zeta.
 *
 * @since 1.0.0
 *
 * @return string Google fonts URL for the theme.
 */
function zeta_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Lato, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Sans font: on or off', 'zeta' ) ) {
		$fonts[] = 'Montserrat:400italic,700italic,400,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by PT Serif, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Serif font: on or off', 'zeta' ) ) {
		$fonts[] = 'PT Serif:400italic,700italic,400,700';
	}

	/*
	 * Translators: To add an additional character subset specific to your language,
	 * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'zeta' );

	if ( 'cyrillic' == $subset ) {
		$subsets .= ',cyrillic,cyrillic-ext';
	} elseif ( 'greek' == $subset ) {
		$subsets .= ',greek,greek-ext';
	} elseif ( 'devanagari' == $subset ) {
		$subsets .= ',devanagari';
	} elseif ( 'vietnamese' == $subset ) {
		$subsets .= ',vietnamese';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), '//fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * Enqueue scripts and styles.
 */
function zeta_scripts() {

	$assets_url = get_template_directory_uri() . '/assets/';

	// Load theme's main styles
	wp_enqueue_style( 'zeta-style', get_stylesheet_uri(), array( 'tiled-gallery', 'dashicons' ), '0.9.0' );

	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'zeta-fonts', zeta_fonts_url(), array(), null );

	// BuddyPress
	if ( function_exists( 'buddypress' ) && is_buddypress() ) {
		wp_enqueue_script( 'zeta-buddypress', $assets_url . 'js/zeta-buddypress.js', array( 'jquery' ), '0.9.0', true );
		wp_dequeue_style( 'bp-legacy-css' );
		wp_enqueue_style( 'zeta-buddypress', $assets_url . 'css/buddypress.css', array(), '0.9.0' );
	}

	// Contact Card
	if ( function_exists( 'contact_card' ) ) {
		wp_deregister_style( 'contact-card' );
		wp_register_style( 'contact-card', $assets_url . 'css/contact-card.css', array(), '0.9.0' );
	}

	// Event Organiser
	if ( defined( 'EVENT_ORGANISER_VER' ) ) {
		wp_enqueue_style( 'zeta-event-organiser', $assets_url . 'css/event-organiser.css', array(), '0.9.0' );
	}

	// Navigation menu for small screens
	wp_enqueue_script( 'zeta-navigation', $assets_url . 'js/navigation.js', array( 'jquery' ), '0.9.0', true );
	wp_localize_script( 'zeta-navigation', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . __( 'Expand child menu',   'zeta' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . __( 'Collapse child menu', 'zeta' ) . '</span>',
	) );

	wp_enqueue_script( 'zeta-skip-link-focus-fix', $assets_url . 'js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Flexslider
	wp_register_script( 'flexslider', $assets_url . 'js/jquery.flexslider.min.js', array( 'jquery' ), '2.6.1', true );
}
add_action( 'wp_enqueue_scripts', 'zeta_scripts' );

/**
 * Enqueue scripts and styles in the Dashboard.
 */
function zeta_admin_scripts() {

	// Add custom fonts, used in the editor.
	wp_enqueue_style( 'zeta-fonts', zeta_fonts_url(), array(), null );
}
add_action( 'admin_enqueue_scripts', 'zeta_admin_scripts' );

/**
 * Add additional inline styles.
 *
 * @since Zeta 1.0.0
 *
 * @uses apply_filters() Calls 'zeta_previous_post_navigation_label'
 * @uses apply_filters() Calls 'zeta_next_post_navigation_label'
 * @uses wp_add_inline_style()
 */
function zeta_inline_styles() {

	// Define local variable(s)
	$css = '';

	/**
	 * Posts, Post & Comment Navigation
	 */

	// Define prev/next labels
	$prev = __( 'Previous', 'zeta' );
	$next = __( 'Next',     'zeta' );

	// Comment/Posts navigation description
	$css .= '
		.comment-navigation .nav-previous a:before, .posts-navigation .nav-previous a:before { content: "' . $prev . '"; }
		.comment-navigation .nav-next a:before, .posts-navigation .nav-next a:before { content: "'         . $next . '"; }
	';

	// Post navigation description
	$css .= '
		.post-navigation .nav-previous a:before { content: "' . apply_filters( 'zeta_previous_post_navigation_label', $prev ) . '"; }
		.post-navigation .nav-next a:before { content: "'     . apply_filters( 'zeta_next_post_navigation_label',     $next ) . '"; }
	';

	wp_add_inline_style( 'zeta-style', $css );
}
add_action( 'wp_enqueue_scripts', 'zeta_inline_styles' );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since Zeta 1.0.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function zeta_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'zeta_search_form_modify' );

/**
 * Return whether the user has access
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. Defaults to the current user.
 * @return bool Has the user access?
 */
function zeta_check_access( $user_id = 0 ) {

	// Default to the current user
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	return function_exists( 'vgsr' ) && is_user_vgsr( $user_id );
}

/**
 * Custom template tags for this theme.
 */
require( get_template_directory() . '/inc/template-tags.php' );

/**
 * Custom functions that act independently of the theme templates.
 */
require( get_template_directory() . '/inc/extras.php' );

/**
 * Customizer additions.
 */
require( get_template_directory() . '/inc/customizer.php' );

/**
 * Load plugin compatibility files.
 */
require( get_template_directory() . '/inc/buddypress.php' );
require( get_template_directory() . '/inc/event-organiser.php' );
require( get_template_directory() . '/inc/gravityforms.php' );
require( get_template_directory() . '/inc/jetpack.php' );
require( get_template_directory() . '/inc/vgsr-entity.php' );
