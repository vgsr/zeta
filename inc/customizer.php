<?php

/**
 * Zeta Theme Customizer
 *
 * @package Zeta
 * @subpackage Customizer
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function zeta_customize_register( $wp_customize ) {

	// Blog name and description
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	// Use partials, since WP 4.5+
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'            => '.site-title a',
			'container_inclusive' => false,
			'render_callback'     => 'zeta_customize_partial_blogname'
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'            => '.site-description a',
			'container_inclusive' => false,
			'render_callback'     => 'zeta_customize_partial_blogdescription'
		) );
	}

	/* Theme Settings */

	// Add control section
	$wp_customize->add_section( 'theme_settings', array(
		'title'    => __( 'Theme Settings', 'zeta' ),
		'priority' => 70
	) );

	// Include and register control class
	require_once( get_template_directory() . '/inc/classes/class-zeta-customize-image-radio-control.php' );
	$wp_customize->register_control_type( 'Zeta_Customize_Image_Radio_Control' );

	// Default layout
	$wp_customize->add_setting( 'default_layout', array( 
		'capability' => 'edit_theme_options',
		'default'    => 'single-column',
		'transport'  => 'postMessage'
	) );
	$wp_customize->add_control( new Zeta_Customize_Image_Radio_Control(
		$wp_customize,
		'default_layout',
		array(
			'label'       => __( 'Default Layout', 'zeta' ),
			'description' => __( 'This setting can be overridden on a per-page basis.', 'zeta' ),
			'section'     => 'theme_settings',
			'choices'     => array(
				'sidebar-content' => __( 'Sidebar - Content', 'zeta' ),
				'single-column'   => __( 'Single Column',     'zeta' ),
				'content-sidebar' => __( 'Content - Sidebar', 'zeta' ),
			),
		)
	) );

	/* Background Image using Featured Images plugin */
	if ( function_exists( 'featured_images' ) ) {

		// Add control section
		$wp_customize->add_section( 'background_image', array(
			'title'       => __( 'Default Background', 'zeta' ),
			'description' => __( 'Select images that serve as a background fallback when the current page has no images or slides to show. By default all selected images will be shown in the image slider in random order.', 'zeta' ),
			'priority'    => 80
		) );

		// Add Images control setting
		$wp_customize->add_setting( 'background_image', array( 'capability' => 'edit_theme_options' ) );
		$wp_customize->add_control( new Customize_Featured_Images_Control(
			$wp_customize,
			'background_image',
			array(
				'label'       => __( 'Background Image', 'zeta' ),
				'section'     => 'background_image',
				'min_width'   => 1200,
				'min_height'  => 900
			)
		) );

		// Add Rotate All checkbox control setting
		$wp_customize->add_setting( 'background_image_single', array( 'capability' => 'edit_theme_options' ) );
		$wp_customize->add_control( 'background_image_single', array(
			'label'   => __( 'Display only a single image', 'zeta' ),
			'section' => 'background_image',
			'type'    => 'checkbox'
		) );
	}
}
add_action( 'customize_register', 'zeta_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function zeta_customize_preview_js() {
	wp_enqueue_script( 'zeta_customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), time(), true );
}
add_action( 'customize_preview_init', 'zeta_customize_preview_js' );

/**
 * Render the site title for the selective refresh partial
 *
 * @since 1.0.0
 */
function zeta_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site description for the selective refresh partial
 *
 * @since 1.0.0
 */
function zeta_customize_partial_blogdescription() {
	bloginfo( 'description' );
}
