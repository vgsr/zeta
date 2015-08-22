<?php
/**
 * zeta Theme Customizer
 *
 * @package Zeta
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function zeta_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	/* Background Image */

	// Include the control class
	require_once( get_template_directory() . '/inc/classes/class-zeta-customize-multi-image-control.php' );

	// Register the control class
	$wp_customize->register_control_type( 'Zeta_Customize_Multi_Image_Control' );

	// Add control section
	$wp_customize->add_section( 'background_image', array(
		'title'       => __( 'Default Background', 'zeta' ),
		'description' => __( 'Select images that serve as a background fallback when the current page has no images or slides to show. By default a <em>single random</em> image will be used from the selected images. When you chose Rotate All Images, all images will be shown in the slider.', 'zeta' ),
		'priority'    => 80
	) );

	// Add Images control setting
	$wp_customize->add_setting( 'background_image', array( 'capability' => 'manage_options' ) );
	$wp_customize->add_control( new Zeta_Customize_Multi_Image_Control( 
		$wp_customize,
		'background_image',
		array(
			'section'     => 'background_image',
			'label'       => __( 'Background Image', 'zeta' ),
			'min_width'   => 1200,
			'min_height'  => 900
		)
	) );

	// Add Rotate All checkbox control setting
	$wp_customize->add_setting( 'background_image_rotate', array( 'capability' => 'manage_options' ) );
	$wp_customize->add_control( 'background_image_rotate', array(
		'label'   => __( 'Rotate All Images', 'zeta' ),
		'section' => 'background_image',
		'type'    => 'checkbox'
	) );
}
add_action( 'customize_register', 'zeta_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function zeta_customize_preview_js() {
	wp_enqueue_script( 'zeta_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'zeta_customize_preview_js' );
