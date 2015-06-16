<?php
/**
 * zeta functions and definitions
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
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'zeta' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	/**
	 * Register theme image sizes
	 */
	zeta_add_image_sizes();
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
	add_image_size( 'zeta-header-slider', 9999, 900 );
}

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function zeta_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'zeta' ),
		'id'            => 'sidebar-1',
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
	wp_enqueue_style( 'zeta-style', get_stylesheet_uri() );

	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'zeta-fonts', zeta_fonts_url(), array(), null );

	// Add Dashicons for small menu
	wp_enqueue_style( 'dashicons' );

	// BuddyPress
	if ( function_exists( 'buddypress' ) && is_buddypress() ) {
		wp_enqueue_style( 'zeta-buddypress', get_template_directory_uri() . '/css/buddypress.css', array( 'buddypress' ) );
	}

	// Navigation menu for small screens
	wp_enqueue_script( 'zeta-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_localize_script( 'zeta-navigation', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . __( 'Expand child menu',   'zeta' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . __( 'Collapse child menu', 'zeta' ) . '</span>',
	) );

	wp_enqueue_script( 'zeta-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Flexslider
	wp_register_script( 'flexslider', get_template_directory_uri() . '/js/jquery.flexslider.min.js', array( 'jquery' ), '2.3.0', true );
}
add_action( 'wp_enqueue_scripts', 'zeta_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Add additional inline styles.
 *
 * @since Zeta 1.0.0
 *
 * @see wp_add_inline_style()
 *
 * @uses vgsr_entity()
 * @uses is_singular()
 * @uses wp_get_sidebars_widgets()
 * @uses zeta_count_widgets_wit_setting()
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

	// Consider VGSR Entity plugin
	if ( function_exists( 'vgsr_entity' ) ) {
		$entity = vgsr_entity();

		// Besturen
		if ( isset( $entity->bestuur ) && is_singular( $entity->bestuur->type ) ) {

			// Define labels for single Bestuur
			$prev = $entity->bestuur->get_season( get_adjacent_post( false, '', true  ) );
			$next = $entity->bestuur->get_season( get_adjacent_post( false, '', false ) );
		}
	}

	// Post navigation description
	$css .= '
		.post-navigation .nav-previous a:before { content: "' . $prev . '"; }
		.post-navigation .nav-next a:before { content: "'     . $next . '"; }
	';

	/**
	 * Widgets
	 */

	// Define the max count of full-width widgets in any sidebar
	$widgets_count = 0;
	foreach ( wp_get_sidebars_widgets() as $sidebar => $widgets ) {
		$count = zeta_count_widgets_with_setting( $sidebar, 'zeta-full-width', true );
		if ( $count > $widgets_count )
			$widgets_count = $count;
	}

	// Define styles for widgets following multiple full-width widgets,
	// since there is no :nth-of-class type selector available in CSS.
	if ( $widgets_count > 2 ) {
		$left = $right = $left_before = $right_before = $rep = '';

		while ( $widgets_count > 1 ) {
			$rep = str_repeat( ' ~ aside.full-width', $widgets_count - 1 );

			// Widgets preceded by an odd number of full-width widgets
			if ( 1 === $widgets_count % 2 ) {
				$left         = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(even),\n" . $left;
				$right        = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(odd),\n" . $right;
				$left_before  = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(even):before,\n" . $left_before;
				$right_before = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(odd):before,\n" . $right_before;

			// Widgets preceded by an even number of full-width widgets
			} else {
				$left         = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(odd),\n" . $left;
				$right        = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(even),\n" . $right;
				$left_before  = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(odd):before,\n" . $left_before;
				$right_before = "\t\t\t.widget-area aside.full-width{$rep} + aside:not(.full-width) ~ aside:not(.full-width):nth-of-type(even):before,\n" . $right_before;
			}

			$widgets_count--;
		}

		// Define styles
		$left         = trim( $left,         ",\n" ) . " { padding: 35px 17.5px 35px 35px; clear: both; }\n";
		$right        = trim( $right,        ",\n" ) . " { padding: 35px 35px 35px 17.5px; clear: none; }\n";
		$left_before  = trim( $left_before,  ",\n" ) . " { content: ''; }\n";
		$right_before = trim( $right_before, ",\n" ) . " { content: none; }\n";

		// Append widget styles within media query. See style.css chapter 9.0
		$css .= "
		@media screen and (min-width: 587px) and (max-width: 740px), (min-width: 881px) {\n{$left}{$left_before}{$right}{$right_before}\t\t}
		";
	}

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
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
